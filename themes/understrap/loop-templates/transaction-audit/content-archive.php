<?php

// Exit if accessed directly.
defined('ABSPATH') || exit;

?>

<div class="row">

	<div class="table-responsive">
		<table class="table">
			<thead>
				<tr>
					<th scope="col">#</th>
					<th scope="col">Reference</th>
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

					$fromUserId = carbon_get_the_post_meta('crb_transaction_audit_from_user')[0]['id'];
					$fromUser = get_user_by('id', esc_html($fromUserId));
					$toUserId = carbon_get_the_post_meta('crb_transaction_audit_to_user')[0]['id'];
					$toUser = get_user_by('id', esc_html($toUserId));
					$referenceId = carbon_get_the_post_meta('crb_transaction_audit_ref');
				?>

					<tr>
						<th scope="row"><?php echo get_the_ID(); ?></th>
						<td><a href="<?php echo get_permalink($referenceId); ?>" target="_blank"><?php echo esc_html($referenceId); ?></a></td>
						<td><?php echo esc_html(carbon_get_the_post_meta('crb_transaction_audit_date')); ?></td>
						<td><?php echo esc_html(carbon_get_the_post_meta('crb_transaction_audit_type')); ?></td>
						<td><?php echo esc_html(carbon_get_the_post_meta('crb_transaction_audit_category')); ?></td>
						<td><?php the_title(); ?></td>
						<td>₹<?php echo number_format(esc_html(carbon_get_the_post_meta('crb_transaction_audit_amount')), 2); ?></td>
						<td><?php echo $fromUser->display_name; ?></td>
						<td><?php echo $toUser->display_name; ?></td>

						<td>

							<?php $receipts = carbon_get_the_post_meta('crb_transaction_audit_receipt') ?>

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