<?php

/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Understrap
 */

// Exit if accessed directly.
defined('ABSPATH') || exit;

get_header();

$container = get_theme_mod('understrap_container_type');
?>

<?php if (is_front_page() && is_home()) : ?>
	<?php get_template_part('global-templates/hero'); ?>
<?php endif; ?>

<?php if (is_user_logged_in()): ?>
	<div class="wrapper pb-0" id="index-wrapper">

		<div class="<?php echo esc_attr($container); ?> mb-5 " id="content" tabindex="-1">

			<main class="site-main" id="main">

				<section class="mb-5">
					<?php
					global $total_balance_post_id;
					$total_cash = (float) esc_html(get_post_field('post_content', $total_balance_post_id));

					$homepage_cash_in_account_query = new WP_Query(array(
						'posts_per_page' => 1,
						'post_type' => 'passbook',
					));
					$cash_in_account = 0;

					while ($homepage_cash_in_account_query->have_posts()):
						$homepage_cash_in_account_query->the_post();

						$cash_in_account = esc_html(carbon_get_the_post_meta('crb_passbook_amount'));
						break;
					endwhile;

					$homepage_due_payments_query = new WP_Query(array(
						'posts_per_page'   => -1,
						'post_type' => 'pending-payment',
					));
					$due_payments = 0;

					while ($homepage_due_payments_query->have_posts()):
						$homepage_due_payments_query->the_post();

						$due_payments += esc_html(carbon_get_the_post_meta('crb_pending_amount'));
					endwhile;
					?>
					<div class="table-responsive">
						<table class="table">
							<thead>
								<tr>
									<th scope="col">Total Cash</th>
									<th scope="col">Cash In Account</th>
									<th scope="col">Due Payments</th>
								</tr>
							</thead>
							<tbody class="table-group-divider">
								<tr>
									<td>₹<?php echo number_format($total_cash, 2) ?></td>
									<td>₹<?php echo number_format($cash_in_account, 2) ?></td>
									<td>₹<?php echo number_format($due_payments, 2) ?></td>
								</tr>
							</tbody>
						</table>
					</div>
				</section> <!-- .mb-5 -->

				<section class="mb-5">
					<h4 id="#urgent-tasks" class="d-flex justify-content-between">
						Notices
						<a
							class="btn btn-primary"
							href="<?php echo get_post_type_archive_link('bulletin'); ?>">
							View All
						</a>
					</h4>
					<div class="row">

						<?php
						$homepage_bulletins = new WP_Query(array(
							'posts_per_page' => 3,
							'post_type' => 'bulletin',
						));

						while ($homepage_bulletins->have_posts()):
							$homepage_bulletins->the_post();
						?>

							<div class="col-12 col-sm-6 col-lg-4 mb-3">
								<div class="card text-center">
									<div class="card-header">
										NOTICE
									</div>
									<div class="card-body">
										<h5 class="card-title mb-3">DATED: <?php echo get_the_date('d/m/Y'); ?></h5>
										<p class="card-text text-start mb-3"><?php echo wp_trim_words(get_the_content(), 20); ?></p>
										<a href="<?php the_permalink(); ?>" class="btn btn-primary w-100">View Details</a>
									</div>
									<div class="card-footer text-body-secondary">
										<span class="fw-bold">Commissioned By:</span> <?php echo get_the_author(); ?>
									</div>
								</div>
							</div>

						<?php
						endwhile;
						?>

					</div> <!-- .row -->
				</section> <!-- .mb-5 -->

				<section class="mb-5">
					<h4 id="#urgent-tasks" class="d-flex justify-content-between">
						Urgent Tasks
						<a
							class="btn btn-primary"
							href="<?php echo get_post_type_archive_link('actionitem'); ?>">
							View All
						</a>
					</h4>
					<div class="table-responsive">
						<table class="table">
							<thead>
								<tr>
									<th scope="col">#</th>
									<th scope="col">Priority</th>
									<th scope="col">Status</th>
									<th style="min-width: 8em;" scope="col">Title</th>
									<th style="min-width: 26em;" scope="col">Description</th>
								</tr>
							</thead>
							<tbody class="table-group-divider">

								<?php
								$homepage_action_items = new WP_Query(array(
									'posts_per_page' => 4,
									'post_type' => 'actionitem',
								));

								while ($homepage_action_items->have_posts()):
									$homepage_action_items->the_post();
								?>

									<tr>
										<th scope="row"><?php echo get_the_ID(); ?></th>
										<td class="text-center">

											<?php $priority = esc_html(carbon_get_the_post_meta('crb_actionitem_priority')); ?>
											<i class="bi <?php echo ActionItem::priorityIcon($priority); ?>"></i>

										</td> <!-- .text-center -->
										<td>
											<?php $status = esc_html(carbon_get_the_post_meta('crb_actionitem_status')); ?>
											<span class="badge <?php echo ActionItem::statusIcon($status); ?>"><?php echo $status; ?></span>
										</td>
										<!-- TODO: WP > Settings > Permalink > Save Changes -->
										<td><a href="<?php the_permalink(); ?>" target="_blank"><?php the_title(); ?></a></td>
										<td><?php echo wp_trim_words(get_the_content(), 10); ?></td>
									</tr>

								<?php
								endwhile;
								?>

							</tbody> <!-- .table-group-divider -->
						</table> <!-- .table -->
					</div> <!-- .table-responsive -->
				</section> <!-- .mb-5 -->

				<section class="mb-5">
					<h4 id="#pending-payments" class="d-flex justify-content-between">
						Pending Payments
						<a
							class="btn btn-primary"
							href="<?php echo get_post_type_archive_link('pending-payment'); ?>">
							View All
						</a>
					</h4>
					<div class="table-responsive">
						<table class="table">
							<thead>
								<tr>
									<th scope="col">#</th>
									<th scope="col">Name</th>
									<th scope="col">Amount</th>
								</tr>
							</thead>
							<tbody class="table-group-divider">

								<?php
								$homepage_due_payments_query = new WP_Query(array(
									'posts_per_page' => 3,
									'post_type' => 'pending-payment',
								));

								while ($homepage_due_payments_query->have_posts()):
									$homepage_due_payments_query->the_post();
									$user = get_user_by('id', carbon_get_the_post_meta('crb_pending_with_user')[0]['id']);
								?>

									<tr>
										<th scope="row"><?php echo get_the_ID(); ?></th>
										<td><?php echo $user->display_name; ?></td>
										<td>₹<?php echo number_format(esc_html(carbon_get_the_post_meta('crb_pending_amount')), 2); ?></td>
									</tr>

								<?php endwhile; ?>

							</tbody>
					</div>
					</table>
				</section> <!-- .mb-5 -->

				<section class="mb-5">
					<h4 id="#latest-transactions" class="d-flex justify-content-between">
						Latest Transactions
						<a
							class="btn btn-primary"
							href="<?php echo get_post_type_archive_link('transaction'); ?>">
							View All
						</a>
					</h4>
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
								$homepage_txns = new WP_Query(array(
									'posts_per_page' => 4,
									'post_type' => 'transaction',
								));

								while ($homepage_txns->have_posts()):
									$homepage_txns->the_post();

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
												$count = 1;
												foreach ($receipts as $receipt):
													$img = $receipt['receipt_image'];
													$img_url = wp_get_attachment_image_url($img, '');
											?>

													<a class="ms-1" href="<?php echo $img_url ?>" target="_blank"
														rel="noopener noreferrer"><?php echo $count; ?></a>,

											<?php
													$count++;
												endforeach;
											endif;
											?>

										</td>
									</tr>

								<?php
								endwhile;
								?>

							</tbody> <!-- .table-group-divider -->
						</table> <!-- .table -->
					</div> <!-- .table-responsive -->
				</section> <!-- .mb-5 -->

			</main>

		</div><!-- #content -->

	</div><!-- #index-wrapper -->

<?php else: ?>

	<div class="mt-5 d-flex justify-content-center align-items-center">
		<div class="mt-5 card shadow-sm">
			<div class="card-body text-center p-4 p-sm-5">
				<h3>Welcome to</h3>
				<h1 class="card-title mb-5"><?php echo getenv('APARTMENT_NAME'); ?></h1>
				<a href="<?php echo esc_url(wp_login_url()); ?>" type="button" class="btn btn-primary w-100 mb-3">Login</a>
				<a href="<?php echo esc_url(wp_registration_url()); ?>" type="button" class="btn btn-outline-primary w-100">Signup</a>
			</div>
		</div>
	</div>

<?php endif; ?>

<?php
get_footer();
