<?php

// Exit if accessed directly.
defined('ABSPATH') || exit;
?>

<div class="row">

	<table class="table">
		<tbody class="table-group-divider">

			<?php
			$fromUserId = carbon_get_post_meta(get_the_ID(), 'crb_transaction_from_user')[0]['id'];
			$fromUser = get_user_by('id', esc_html($fromUserId));
			$toUserId = carbon_get_post_meta(get_the_ID(), 'crb_transaction_to_user')[0]['id'];
			$toUser = get_user_by('id', esc_html($toUserId));
			?>

			<tr>
				<th scope="row">Txn ID</th>
				<td><?php echo get_the_ID(); ?></td>
			</tr>
			<tr>
				<th scope="row">Txn Date</th>
				<td><?php echo esc_html(carbon_get_post_meta(get_the_ID(), 'crb_transaction_date')); ?></td>
			</tr>
			<tr>
				<th scope="row">Txn Type</th>
				<td><?php echo esc_html(carbon_get_post_meta(get_the_ID(), 'crb_transaction_type')); ?></td>
			</tr>
			<tr>
				<th scope="row">Txn Category</th>
				<td><?php echo esc_html(carbon_get_post_meta(get_the_ID(), 'crb_transaction_category')); ?></td>
			</tr>
			<tr>
				<th scope="row">Txn Amount</th>
				<td>₹<?php echo number_format(esc_html(carbon_get_post_meta(get_the_ID(), 'crb_transaction_amount')), 2); ?></td>
			</tr>
			<tr>
				<th scope="row">From</th>
				<td><?php echo $fromUser->display_name; ?></td>
			</tr>
			<tr>
				<th scope="row">To</th>
				<td><?php echo $toUser->display_name; ?></td>
			</tr>
			<tr>
				<th scope="row">Txn Description</th>
				<td><?php the_title(); ?></td>
			</tr>
			<tr>
				<th scope="col">Txn Receipts</th>
				<td>

					<?php $receipts = carbon_get_post_meta(get_the_ID(), 'crb_transaction_receipt') ?>

					<?php if (!empty($receipts)):
						$count = 0;
						foreach ($receipts as $receipt):
							$img = $receipt['receipt_image'];
							$img_url = wp_get_attachment_image_url($img, '');
					?>

							<a class="ms-1" href="<?php echo $img_url ?>" target="_blank"
								rel="noopener noreferrer"><?php echo ++$count; ?></a>,

					<?php
						endforeach;
					endif;
					?>

				</td>
			</tr>

		</tbody> <!-- table-group-divider -->
	</table> <!-- .table -->

</div><!-- .row -->