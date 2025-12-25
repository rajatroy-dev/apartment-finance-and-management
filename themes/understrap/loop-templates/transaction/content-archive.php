<?php

// Exit if accessed directly.
defined('ABSPATH') || exit;

global $total_balance_post_id;
$total_cash = (float) esc_html(get_post_field('post_content', $total_balance_post_id));
?>

<h4 id="#total-balance" class="mb-3">
	<span class="fw-semibold">Total Balance:</span> ₹<?php echo number_format($total_cash, 2); ?>
</h4>

<div class="row">

	<div class="table-responsive">
		<table class="table">
			<thead>
				<tr>
					<th scope="col">#</th>
					<th scope="col">Date</th>
					<th scope="col">Type</th>
					<th style="min-width: 12em;" scope="col">Category</th>
					<th style="min-width: 26em;" scope="col">Description</th>
					<th scope="col">Amount</th>
					<th scope="col">From</th>
					<th scope="col">To</th>
					<th scope="col">Receipts</th>
				</tr>
			</thead>
			<tbody class="table-group-divider">

				<?php
				// Start the loop.
				while (have_posts()):
					the_post();

					$fromUserId = carbon_get_the_post_meta('crb_transaction_from_user')[0]['id'];
					$fromUser = get_user_by('id', esc_html($fromUserId));
					$toUserId = carbon_get_the_post_meta('crb_transaction_to_user')[0]['id'];
					$toUser = get_user_by('id', esc_html($toUserId));
				?>

					<tr>
						<th scope="row"><?php echo get_the_ID(); ?></th>
						<td><?php echo esc_html(carbon_get_the_post_meta('crb_transaction_date')); ?></td>
						<td><?php echo esc_html(carbon_get_the_post_meta('crb_transaction_type')); ?></td>
						<td><?php echo esc_html(carbon_get_the_post_meta('crb_transaction_category')); ?></td>
						<td><?php the_title(); ?></td>
						<td>₹<?php echo number_format(esc_html(carbon_get_the_post_meta('crb_transaction_amount')), 2); ?></td>
						<td><?php echo $fromUser->display_name; ?></td>
						<td><?php echo $toUser->display_name; ?></td>

						<td>

							<?php $receipts = carbon_get_the_post_meta('crb_transaction_receipt') ?>

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

				<?php
				endwhile;
				?>

			</tbody> <!-- table-group-divider -->
		</table> <!-- .table -->
	</div> <!-- .table-responsive -->


	<div class="d-flex justify-content-center my-3">
		<?php echo understrap_pagination(); ?>
	</div>

</div><!-- .row -->