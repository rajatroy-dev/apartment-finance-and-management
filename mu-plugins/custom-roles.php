<?php

// Create the Accountant role and inherit Subscriber capabilities
add_role('accountant', 'Accountant', get_role('subscriber')->capabilities);

// Create the Superviser role and inherit Subscriber capabilities
add_role('superviser', 'Superviser', get_role('subscriber')->capabilities);

// Create the Tenant role and inherit Subscriber capabilities
add_role('tenant', 'Tenant', get_role('subscriber')->capabilities);

add_action('admin_init', 'add_cap_to_users');
function add_cap_to_users()
{
    /**
     * An accountant should be able to:
     * - Create, read, update, delete and publish passbook
     * - Create, read, update and publish transaction
     * - Read action items
     * - Create, read, update, delete and publish bulletin
     * - Read and update other pending payment
     */
    // Get the Accountant role object
    $accountant_role = get_role('accountant');

    $accountant_role->add_cap('edit_passbooks');
    $accountant_role->add_cap('publish_passbooks');

    $accountant_role->add_cap('edit_transactions');
    $accountant_role->add_cap('publish_transactions');

    $accountant_role->add_cap('edit_pending-payments');
    $accountant_role->add_cap('publish_pending-payments');

    $accountant_role->add_cap('edit_maintenance-records');
    $accountant_role->add_cap('publish_maintenance-records');

    $accountant_role->add_cap('edit_repair-records');
    $accountant_role->add_cap('publish_repair-records');

    $accountant_role->add_cap('edit_bulletins');
    $accountant_role->add_cap('publish_bulletins');
    $accountant_role->add_cap('delete_bulletins');

    $accountant_role->add_cap('read_private_actionitems');
    $accountant_role->add_cap('read_private_bulletins');
    $accountant_role->add_cap('read_private_passbooks');
    $accountant_role->add_cap('read_private_transactions');
    $accountant_role->add_cap('read_private_pending-payments');
    $accountant_role->add_cap('read_private_maintenance-records');
    $accountant_role->add_cap('read_private_repair-records');
    $accountant_role->add_cap('read_private_transaction-audits');

    $accountant_role->add_cap('upload_files');

    // Get the Superviser role object
    $superviser_role = get_role('superviser');

    $superviser_role->add_cap('edit_actionitems');
    $superviser_role->add_cap('publish_actionitems');
    $superviser_role->add_cap('delete_actionitems');

    $superviser_role->add_cap('edit_bulletins');
    $superviser_role->add_cap('publish_bulletins');
    $superviser_role->add_cap('delete_bulletins');

    $superviser_role->add_cap('read_private_actionitems');
    $superviser_role->add_cap('read_private_bulletins');
    $superviser_role->add_cap('read_private_passbooks');
    $superviser_role->add_cap('read_private_transactions');
    $superviser_role->add_cap('read_private_pending-payments');
    $superviser_role->add_cap('read_private_maintenance-records');
    $superviser_role->add_cap('read_private_repair-records');
    $superviser_role->add_cap('read_private_transaction-audits');

    $superviser_role->add_cap('upload_files');

    // Get the Tenant role object
    $tenant_role = get_role('tenant');

    $tenant_role->add_cap('read_private_actionitems');
    $tenant_role->add_cap('read_private_bulletins');
    $tenant_role->add_cap('read_private_passbooks');
    $tenant_role->add_cap('read_private_transactions');
    $tenant_role->add_cap('read_private_pending-payments');
    $tenant_role->add_cap('read_private_maintenance-records');
    $tenant_role->add_cap('read_private_repair-records');
    $tenant_role->add_cap('read_private_transaction-audits');
}
