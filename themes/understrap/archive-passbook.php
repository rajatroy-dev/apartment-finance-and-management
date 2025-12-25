<?php

/**
 * The template for displaying archive pages
 *
 * Learn more: https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Understrap
 */

// Exit if accessed directly.
defined('ABSPATH') || exit;

get_header();

$container = get_theme_mod('understrap_container_type');
?>

<div class="wrapper" id="passbook-wrapper">

	<div class="<?php echo esc_attr($container); ?> mb-5" id="content" tabindex="-1">

		<main class="site-main" id="main">

			<nav aria-label="breadcrumb">
				<ol class="breadcrumb">
					<li class="breadcrumb-item"><a href="<?php echo get_home_url(); ?>">Home</a></li>
					<li class="breadcrumb-item active" aria-current="page"><?php post_type_archive_title(); ?></li>
				</ol>
			</nav>

			<?php
			if (!is_user_logged_in()):
				get_template_part('loop-templates/content', 'none');
			else:
			?>

				<?php
				$latest_entry = new WP_Query(array(
					'posts_per_page' => 1,
					'post_type' => 'passbook',
				));

				while ($latest_entry->have_posts()):
					$latest_entry->the_post();
				?>

					<h4 id="#bank-balance" class="mb-3">
						<span class="fw-semibold">Total Bank Balance:</span> ₹<?php echo number_format(esc_html(carbon_get_the_post_meta('crb_passbook_amount')), 2); ?>
					</h4>

				<?php
				endwhile;
				?>

				<div class="row">

					<div class="table-responsive">
						<table class="table">
							<thead>
								<tr>
									<th scope="col">#</th>
									<th scope="col">Year</th>
									<th scope="col">Month</th>
									<th scope="col">Amount</th>
									<th scope="col">Entries</th>
								</tr>
							</thead>
							<tbody class="table-group-divider">

								<?php
								// Start the loop.
								while (have_posts()):
									the_post();

									$date = strtotime(esc_html(carbon_get_the_post_meta('crb_passbook_month')));
									$month = date("F", $date);
									$year = date("Y", $date);
								?>

									<tr>
										<th scope="row"><?php echo get_the_ID(); ?></th>
										<td><?php echo $year; ?></td>
										<td><?php echo $month; ?></td>
										<td>₹<?php echo number_format(esc_html(carbon_get_the_post_meta('crb_passbook_amount')), 2); ?></td>

										<td>

											<?php $entries = carbon_get_the_post_meta('crb_passbook_entry') ?>

											<?php if (!empty($entries)):
												$count = 1;
												foreach ($entries as $entry):
													$img = $entry['passbook_image'];
													$img_url = wp_get_attachment_image_url($img, '');
											?>

													<a class="ms-1" href="<?php echo $img_url ?>" target="_blank"
														rel="noopener noreferrer">
														<?php echo $count; ?>
													</a>,

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

							</tbody> <!-- table-group-divider -->
						</table> <!-- .table -->
					</div> <!-- .table-responsive -->


					<div class="d-flex justify-content-center my-3">
						<?php echo understrap_pagination(); ?>
					</div>

				</div><!-- .row -->

			<?php endif; ?>

		</main>

	</div><!-- #content -->

</div><!-- #archive-wrapper -->

<?php
get_footer();
