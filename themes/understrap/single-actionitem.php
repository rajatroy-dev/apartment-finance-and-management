<?php

/**
 * The template for displaying all single posts
 *
 * @package Understrap
 */

// Exit if accessed directly.
defined('ABSPATH') || exit;

get_header();

$container = get_theme_mod('understrap_container_type');
?>

<div class="wrapper" id="single-actionitem-wrapper">

	<div class="<?php echo esc_attr($container); ?> mb-5" id="content" tabindex="-1">

		<main class="site-main" id="single-actionitem-main">

			<nav aria-label="breadcrumb">
				<ol class="breadcrumb">
					<li class="breadcrumb-item"><a href="<?php echo get_home_url(); ?>">Home</a></li>
					<li class="breadcrumb-item"><a href="<?php echo get_post_type_archive_link('actionitem'); ?>">Action Items</a></li>
					<li class="breadcrumb-item active" aria-current="page"><?php the_title(); ?></li>
				</ol>
			</nav>

			<?php
			if (!is_user_logged_in()):
				get_template_part('loop-templates/content-none');
			else:
			?>

				<h1 class="mb-3"><?php the_title(); ?></h1>

				<section class="mb-5">

					<?php
					$image_size = 'blog_post_image';
					$crb_image = carbon_get_post_meta(get_the_ID(), 'crb_actionitem_featured_image');
					$images_count = count($crb_image);
					?>

					<div id="action_item_images" class="carousel slide" data-bs-ride="carousel">
						<div class="carousel-indicators">
							<?php
							for ($i = 0; $i < $images_count; $i++) {
								if ($i == 0) {
							?>
									<button type="button" data-bs-target="#action_item_images" data-bs-slide-to="<?php echo $i ?>" class="active" aria-current="true" aria-label="Action Item Image <?php echo $i ?>"></button>
								<?php
								} else {
								?>
									<button type="button" data-bs-target="#action_item_images" data-bs-slide-to="<?php echo $i ?>" aria-label="Action Item Image <?php echo $i ?>"></button>
							<?php
								}
							}
							?>
						</div>
						<div class="carousel-inner rounded">
							<?php
							for ($i = 0; $i < $images_count; $i++) {
								$img_attrs = ['class' => 'img-fluid rounded mx-auto d-block mb-3', 'alt' => 'Action Item Image' . $i];
								$image_url = wp_get_attachment_image_url($crb_image[$i]['actionitem_image'], '');
								$image = wp_get_attachment_image($crb_image[$i]['actionitem_image'], $image_size, false, $img_attrs);
								if (strlen($image) == 0) {
									$image_url = wp_get_attachment_image_url("71", '');
									$image = wp_get_attachment_image("71", $image_size, false, $img_attrs);
								}
							?>
								<div class="carousel-item <?php echo ($i == 0) ? 'active' : '' ?>">
									<!-- https://www.advancedcustomfields.com/resources/image/ -->
									<a href="<?php echo $image_url; ?>" target="_blank" rel="noopener noreferrer">
										<?php echo $image; ?>
									</a>
									<!-- <img src="..." class="d-block w-100" alt="..."> -->
								</div>
							<?php
							}
							?>
						</div>
						<button class="carousel-control-prev" type="button" data-bs-target="#action_item_images" data-bs-slide="prev">
							<span class="carousel-control-prev-icon" aria-hidden="true"></span>
							<span class="visually-hidden">Previous</span>
						</button>
						<button class="carousel-control-next" type="button" data-bs-target="#action_item_images" data-bs-slide="next">
							<span class="carousel-control-next-icon" aria-hidden="true"></span>
							<span class="visually-hidden">Next</span>
						</button>
					</div>

					<p class="lead mb-3">
						Reported By:
						<small class="text-body-secondary"><?php echo get_the_author_meta('display_name', get_post_field('post_author')); ?></small>
					</p>
					<p class="mb-3">

						<?php $priority = esc_html(carbon_get_post_meta(get_the_ID(), 'crb_actionitem_priority')); ?>

						<span class="fw-bold">Priority:</span>
						<span class="ms-1">
							<i class="me-1 bi <?php echo ActionItem::priorityIcon($priority); ?>"></i>
							<?php echo $priority; ?>
						</span>
					</p> <!-- .mb-3 -->
					<p class="mb-3">

						<?php $status = esc_html(carbon_get_post_meta(get_the_ID(), 'crb_actionitem_status')); ?>

						<span class="fw-bold">Status:</span>
						<span class="ms-1 badge <?php echo ActionItem::statusIcon($status); ?>"><?php echo $status; ?></span>
					</p> <!-- .mb-3 -->
					<p class="mb-3">
						<span class="fw-bold">Least Quotation:</span>
						₹<?php echo number_format(esc_html(carbon_get_post_meta(get_the_ID(), 'crb_actionitem_least_quotation')), 2); ?>
					</p>
					<p class="mb-3">
						<span class="fw-bold">Highest Quotation:</span>
						₹<?php echo number_format(esc_html(carbon_get_post_meta(get_the_ID(), 'crb_actionitem_highest_quotation')), 2); ?>
					</p>
				</section> <!-- .mb-5 -->

				<section class="mb-5">
					<h4 class="mb-3">Description</h4>
					<p class="mb-3"><?php echo get_the_content(); ?></p>
				</section>

				<section class="mb-5">

					<?php $quotations = carbon_get_post_meta(get_the_ID(), 'crb_actionitem_quotation'); ?>

					<h4 class="mb-3">Quotations Submitted</h4>
					<div class="row">

						<?php
						if (!empty($quotations)) {

							foreach ($quotations as $quotation) {
								$quotationImageSize = 'thumbnail_image';
								$quotationImgAttrs = ['class' => 'card-img-top', 'alt' => 'Quotation By ' . $quotation['quotation_proposer']];
								$cbrQuotation = carbon_get_post_meta(get_the_ID(), 'crb_actionitem_quotation');
								$cbrQuotationImg = $quotation['quotation_image'];
								$cbrQuotationImageUrl = wp_get_attachment_image_url($cbrQuotationImg, '');
								$cbrQuotationImage = wp_get_attachment_image($cbrQuotationImg, $quotationImageSize, false, $quotationImgAttrs);

								if (strlen($cbrQuotationImage) == 0) {
									$cbrQuotationImageUrl = wp_get_attachment_image_url("71", $quotationImageSize);
									$cbrQuotationImage = wp_get_attachment_image("71", $quotationImageSize, false, $quotationImgAttrs);
								}
						?>

								<div class="col-12 col-sm-6 col-lg-4 mb-3 d-flex justify-content-center">
									<div class="card" style="width: 18rem;">
										<a href="<?php echo $cbrQuotationImageUrl; ?>" target="_blank" rel="noopener noreferrer">
											<?php echo $cbrQuotationImage; ?>
										</a>
										<div class="card-body">
											<h5 class="card-title">Quotation: ₹<?php echo number_format(esc_html($quotation['quotation_amount']), 2); ?></h5>
											<p class="card-text">Proposed By: <?php echo esc_html($quotation['quotation_proposer']); ?></p>
											<?php
											if ($quotation['quotation_approval_status'] === 'Pending') {
											?>
												<!-- https://wordpress.stackexchange.com/questions/319546/how-to-call-wordpress-functions-from-a-form-processing-script -->
												<div class="d-flex justify-content-evenly">
													<div class="flex-grow-1 pe-2">
														<form action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" method="post">
															<input type="hidden" name="action" value="action_item_approve_payment" />
															<input type="hidden" value="₹<?php echo $quotation['quotation_amount']; ?>" name="action_item_approve_payment_id" id="action_item_approve_payment_id" />
															<input type="hidden" value="<?php echo get_the_ID(); ?>" name="action_item_approve_id" id="action_item_approve_id" />
															<?php wp_nonce_field('action_item_approve_payment_action', 'action_item_approve_payment_field'); ?>
															<?php wp_nonce_field('action_item_approve_payment_action', 'action_item_approve_id_field'); ?>
															<input class="btn btn-success w-100" type="submit" value="APPROVE">
														</form>
													</div>
													<div class="flex-grow-1 ps-2">
														<form action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" method="post">
															<input type="hidden" name="action" value="action_item_reject_payment" />
															<input type="hidden" value="₹<?php echo $quotation['quotation_amount']; ?>" name="action_item_reject_payment_id" id="action_item_reject_payment_id">
															<input type="hidden" value="<?php echo get_the_ID(); ?>" name="action_item_reject_id" id="action_item_reject_id">
															<?php wp_nonce_field('action_item_reject_payment_action', 'action_item_reject_payment_field'); ?>
															<?php wp_nonce_field('action_item_reject_payment_action', 'action_item_reject_id_field'); ?>
															<input class="btn btn-danger w-100" type="submit" value="REJECT">
														</form>
													</div>
												</div>
											<?php } else if ($quotation['quotation_approval_status'] === 'Approved') { ?>
												<div>
													<button class="btn btn-success w-100" disabled>APPROVED</button>
												</div>
											<?php } else if ($quotation['quotation_approval_status'] === 'Rejected') { ?>
												<div>
													<button class="btn btn-secondary w-100" disabled>REJECTED</button>
												</div>
											<?php } ?>
										</div>
									</div>
								</div>

							<?php
							}
						} else {
							?>

							<h5 class="text-center m-5">Till now, no quotations have been submitted!</h5>

						<?php
						}
						?>

					</div> <!-- .row -->

				</section> <!-- .mb-5 -->

			<?php endif; ?>

		</main>

	</div><!-- #content -->

</div><!-- #index-wrapper -->

<?php
get_footer();
