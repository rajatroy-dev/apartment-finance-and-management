<?php

$total_balance_post_id = (int) getenv('TOTAL_BALANCE_POST_ID');

function write_log($data)
{
    if (true === WP_DEBUG) {
        if (is_array($data) || is_object($data)) {
            error_log(print_r($data, true));
        } else {
            error_log($data);
        }
    }
}

add_action('carbon_fields_post_meta_container_saved', 'calculate_total_balance');
add_action('carbon_fields_post_meta_container_saved', 'calculate_highest_least_quotation');

add_action('save_post', 'handle_visibility', 10, 2);

add_action('add_maintenance_amount_cron', 'maintenance_cron');

add_action('admin_post_action_item_approve_payment', 'action_item_approve_payment');
add_action('admin_post_action_item_reject_payment', 'action_item_reject_payment');


/**
 * Logic for transaction.
 *
 * When a transaction is added, it can be of 2 types:
 * - Credit
 * - Debit
 *
 * Adding a transaction will have an impact on total balance and pending payments.
 *
 * If a transaction is new, total balance will increase if it's a credit. It will decrease if it's a debit.
 * While doing the above, we need to take care that total balance cannot be negative.
 *
 * If a transaction is edited, then we need to adjust the total balance first with
 * the previous transaction amount and then with current transaction amount.
 * For this, there should be a separate record of transactions for auditing purposes.
 *
 * When we add a transaction, it will also be added to audit table.
 * When a transaction is edited, it will find the previous transaction reference from audit table,
 * adjust the total balance, add a new entry to audit table and then save the correct total balance.
 * While editing, if the total balance is less than 0 then reverse the transaction.
 */
function calculate_total_balance($post_id)
{
    // Check it's not an auto save routine
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

    // Perform permission checks! For example:
    if (!current_user_can('edit_post', $post_id)) return;

    // Check the post type
    if ('transaction' !== get_post_type($post_id)) return;

    // Get the total balance. Getting it from env otherwise wp cli cron doesn't get the post.
    $total_balance_post_id = (int) getenv('TOTAL_BALANCE_POST_ID');
    $total_balance = get_post($total_balance_post_id);
    $latest_balance = (float) get_post_field('post_content', $total_balance_post_id);

    // Check if it's a new transaction
    $is_new = false;
    $args = array(
        'posts_per_page' => 1,
        'post_type' => 'transaction-audit',
        'meta_query' => array(
            array(
                'key'   => 'crb_transaction_audit_ref',
                'value' => $post_id
            ),
        ),
    );
    $existing_record = new WP_Query($args);

    if (!$existing_record->have_posts()) $is_new = true;

    // Update total balance as per transaction
    $new_total_balance = 0;
    $post_txn_amount = (float) carbon_get_post_meta($post_id, 'crb_transaction_amount');
    $post_txn_type = carbon_get_post_meta($post_id, 'crb_transaction_type');

    if ($is_new) {
        if ('Credit' === $post_txn_type) {
            $new_total_balance = $latest_balance + $post_txn_amount;
        } else if ('Debit' === $post_txn_type) {
            $new_total_balance = $latest_balance - $post_txn_amount;
        }

        if ($new_total_balance < 0) {
            // Do not accept post that debits more money than possible
            wp_delete_post($post_id, true);
        } else {
            $total_balance->post_content = $new_total_balance;
            wp_update_post($total_balance);
            record_transaction_audit($post_id);
            // If this method is hooked to 'carbon_fields_post_meta_container_saved',
            // it creates a bug that credits to pending amount more than once.
            // This results in offsetting the pending amount incorrectly.
            // As such, adding it here so that pending amount credits for new 
            // transactions only.
            offset_pending_amount($post_id);
        }
    } else {
        $prev_total_balance = 0;
        $audit_post_id = 0;

        while ($existing_record->have_posts()) {
            $existing_record->the_post();
            $audit_post_id = get_the_ID();

            $prev_txn_amount = carbon_get_the_post_meta('crb_transaction_audit_amount');
            $prev_txn_type = carbon_get_the_post_meta('crb_transaction_audit_type');

            if ('Credit' === $prev_txn_type) {
                $prev_total_balance = $latest_balance - $prev_txn_amount;
            } else if ('Debit' === $prev_txn_type) {
                $prev_total_balance = $latest_balance + $prev_txn_amount;
            }

            if ('Credit' === $post_txn_type) {
                $new_total_balance = $prev_total_balance + $post_txn_amount;
            } else if ('Debit' === $post_txn_type) {
                $new_total_balance = $prev_total_balance - $post_txn_amount;
            }

            break;
        }

        if ($new_total_balance < 0) {
            // Do not accept post that debits more money than possible
            reverse_transaction($post_id, $audit_post_id);
        } else {
            $total_balance->post_content = $new_total_balance;
            wp_update_post($total_balance);
            record_transaction_audit($post_id);
        }
    }
}

function calculate_highest_least_quotation($post_id)
{
    // Check it's not an auto save routine
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

    // Perform permission checks! For example:
    if (!current_user_can('edit_post', $post_id)) return;

    // Check the post type
    if ('actionitem' !== get_post_type($post_id)) return;

    $quotations = carbon_get_post_meta($post_id, 'crb_actionitem_quotation');

    $highest_quotation = (float) $quotations[0]['quotation_amount'];
    $lowest_quotation = (float) $quotations[0]['quotation_amount'];
    foreach ($quotations as $quotation) {
        $value = (float) $quotation['quotation_amount'];
        if ($value >= $highest_quotation) {
            $highest_quotation = $value;
        }

        if ($value <= $lowest_quotation) {
            $lowest_quotation = $value;
        }
    }

    carbon_set_post_meta($post_id, 'crb_actionitem_least_quotation', $lowest_quotation);
    carbon_set_post_meta($post_id, 'crb_actionitem_highest_quotation', $highest_quotation);
}

// On saving a transaction check if the user has pending amount and offset the amount
function offset_pending_amount($post_id)
{
    // Check it's not an auto save routine
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

    // Perform permission checks! For example:
    if (!current_user_can('edit_post', $post_id)) return;

    // Check the post type
    if ('transaction' !== get_post_type($post_id)) return;

    $user_id = carbon_get_post_meta($post_id, 'crb_transaction_from_user')[0]['id'];
    $args = array(
        'post_type' => 'pending-payment',
        'posts_per_page' => 1,
        'meta_query' => array(
            array(
                'key'       => 'crb_pending_with_user',
                'value'     => 'user:user:' . $user_id,
                'compare'   => '='
            )
        ),
    );
    $pending_payment = new WP_Query($args);
    if (!$pending_payment->have_posts()) return;

    $post_txn_type = carbon_get_post_meta($post_id, 'crb_transaction_type');
    $post_txn_amount = (float) carbon_get_post_meta($post_id, 'crb_transaction_amount');

    $pending_amount = 0;
    $pending_payment_id = 0;
    while ($pending_payment->have_posts()) {
        $pending_payment->the_post();

        $pending_amount = carbon_get_the_post_meta('crb_pending_amount');
        $pending_payment_id = get_the_ID();
        break;
    }

    if ('Credit' == $post_txn_type) {
        $new_pending_amount = $pending_amount - $post_txn_amount;
        carbon_set_post_meta($pending_payment_id, 'crb_pending_amount', $new_pending_amount);
    }
}

function handle_visibility($post_id, $post)
{
    // If we don't do this, somehow the types are not editable even though an user is the creator
    $allowed_post_types = ['actionitem', 'transaction', 'passbook', 'bulletin', 'pending-payment', 'maintenance-record', 'repair-record'];
    if (!in_array(get_post_type($post_id), $allowed_post_types)) {
        return;
    }

    if ($post->post_status === 'publish') {
        $post->post_status = 'private';
        wp_update_post($post);
    }
}

// Cron job to add Maintenance pending amount at the beginning of month
function maintenance_cron()
{
    $date = date('d');
    if ($date != 01) return;

    $args = array(
        'post_type' => 'pending-payment',
        'post_status' => ['private'],
        'posts_per_page' => -1
    );
    $pending_payment = new WP_Query($args);
    if (!$pending_payment->have_posts()) return;

    // Getting it from env otherwise wp cli cron doesn't get the post.
    $maintenance_post_id = (int) getenv('MAINTENANCE_ID');
    $maintenance_amount_query = get_post($maintenance_post_id);
    $maintenance_amount = (float) $maintenance_amount_query->post_content;

    while ($pending_payment->have_posts()) {
        $pending_payment->the_post();

        $pending_amount = carbon_get_the_post_meta('crb_pending_amount');
        $new_pending_amount = $pending_amount + $maintenance_amount;
        carbon_set_post_meta(get_the_ID(), 'crb_pending_amount', $new_pending_amount);
    }
}

function record_transaction_audit(int $post_id)
{
    $record = get_post($post_id);
    $postarr = array(
        'post_type'     => 'transaction-audit',
        'post_title'    => $record->post_title,
        'post_author'   => 1,
        'post_status'   =>  'publish'
    );
    $new_post_id = wp_insert_post($postarr);

    carbon_set_post_meta($new_post_id, 'crb_transaction_audit_date', carbon_get_post_meta($post_id, 'crb_transaction_date'));
    carbon_set_post_meta($new_post_id, 'crb_transaction_audit_type', carbon_get_post_meta($post_id, 'crb_transaction_type'));
    carbon_set_post_meta($new_post_id, 'crb_transaction_audit_category', carbon_get_post_meta($post_id, 'crb_transaction_category'));
    carbon_set_post_meta($new_post_id, 'crb_transaction_audit_amount', carbon_get_post_meta($post_id, 'crb_transaction_amount'));
    carbon_set_post_meta($new_post_id, 'crb_transaction_audit_ref', $post_id);
    carbon_set_post_meta($new_post_id, 'crb_transaction_audit_from_user', carbon_get_post_meta($post_id, 'crb_transaction_from_user'));
    carbon_set_post_meta($new_post_id, 'crb_transaction_audit_to_user', carbon_get_post_meta($post_id, 'crb_transaction_to_user'));
    carbon_set_post_meta($new_post_id, 'crb_transaction_audit_receipt', carbon_get_post_meta($post_id, 'crb_transaction_receipt'));
}

function reverse_transaction(int $post_id, int $audit_post_id)
{
    carbon_set_post_meta($post_id, 'crb_transaction_date', carbon_get_post_meta($audit_post_id, 'crb_transaction_audit_date'));
    carbon_set_post_meta($post_id, 'crb_transaction_type', carbon_get_post_meta($audit_post_id, 'crb_transaction_audit_type'));
    carbon_set_post_meta($post_id, 'crb_transaction_category', carbon_get_post_meta($audit_post_id, 'crb_transaction_audit_category'));
    carbon_set_post_meta($post_id, 'crb_transaction_amount', carbon_get_post_meta($audit_post_id, 'crb_transaction_audit_amount'));
    carbon_set_post_meta($post_id, 'crb_transaction_from_user', carbon_get_post_meta($audit_post_id, 'crb_transaction_audit_from_user'));
    carbon_set_post_meta($post_id, 'crb_transaction_to_user', carbon_get_post_meta($audit_post_id, 'crb_transaction_audit_to_user'));
    carbon_set_post_meta($post_id, 'crb_transaction_receipt', carbon_get_post_meta($audit_post_id, 'crb_transaction_audit_receipt'));
}

function action_item_approve_payment()
{
    // 1. Verify the nonce for security
    if (
        ! isset($_POST['action_item_approve_payment_field'])
        || ! isset($_POST['action_item_approve_id_field'])
        || ! wp_verify_nonce($_POST['action_item_approve_payment_field'], 'action_item_approve_payment_action')
        || ! wp_verify_nonce($_POST['action_item_approve_id_field'], 'action_item_approve_payment_action')
    ) {
        wp_die('Security check failed!');
    }

    // 2. Sanitize and process the form data
    $post_id = sanitize_text_field($_POST['action_item_approve_id']);
    $amount_id = sanitize_text_field($_POST['action_item_approve_payment_id']);
    $repair_amount = 0;

    $quotations = carbon_get_post_meta($post_id, 'crb_actionitem_quotation');

    foreach ($quotations as $key => $quotation) {
        if ($amount_id === '₹' . $quotation['quotation_amount']) {
            $repair_amount = $quotation['quotation_amount'];
            $quotations[$key]['quotation_approval_status'] = 'Approved';
        }
    }

    $pending_payment = new WP_Query(array(
        'posts_per_page'   => -1,
        'post_type' => 'pending-payment',
    ));
    $total_tenants = $pending_payment->found_posts;
    $repair_amount_per_tenant = $repair_amount / $total_tenants;

    while ($pending_payment->have_posts()) {
        $pending_payment->the_post();

        $pending_amount = carbon_get_the_post_meta('crb_pending_amount');
        $new_pending_amount = $pending_amount + $repair_amount_per_tenant;
        carbon_set_post_meta(get_the_ID(), 'crb_pending_amount', round($new_pending_amount, 2));
        write_log(round($new_pending_amount, 2));
    }

    write_log($quotations);

    carbon_set_post_meta($post_id, 'crb_actionitem_quotation', $quotations);

    // 3. (Optional) Store messages in transients to persist across the redirect
    // set_transient( 'my_form_message', 'Data processed successfully!', 60 );

    // 4. Redirect back to the original page or another admin page
    // Using wp_redirect and exit() is crucial to prevent a blank page
    wp_redirect(get_permalink($post_id));
    exit();
}

function action_item_reject_payment()
{
    // 1. Verify the nonce for security
    if (
        ! isset($_POST['action_item_reject_payment_field'])
        || ! isset($_POST['action_item_reject_id_field'])
        || ! wp_verify_nonce($_POST['action_item_reject_payment_field'], 'action_item_reject_payment_action')
        || ! wp_verify_nonce($_POST['action_item_reject_id_field'], 'action_item_reject_payment_action')
    ) {
        wp_die('Security check failed!');
    }

    // 2. Sanitize and process the form data
    $post_id = sanitize_text_field($_POST['action_item_reject_id']);
    $amount_id = sanitize_text_field($_POST['action_item_reject_payment_id']);

    $quotations = carbon_get_post_meta($post_id, 'crb_actionitem_quotation');

    foreach ($quotations as $key => $quotation) {
        if ($amount_id === '₹' . $quotation['quotation_amount']) {
            $quotations[$key]['quotation_approval_status'] = 'Rejected';
        }
    }

    write_log($quotations);

    carbon_set_post_meta($post_id, 'crb_actionitem_quotation', $quotations);

    // 3. (Optional) Store messages in transients to persist across the redirect
    // set_transient( 'my_form_message', 'Data processed successfully!', 60 );

    // 4. Redirect back to the original page or another admin page
    // Using wp_redirect and exit() is crucial to prevent a blank page
    wp_redirect(get_permalink($post_id));
    exit();
}
