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

<div class="wrapper" id="pending-payment-wrapper">

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

				<div class="row">

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
								while (have_posts()):
									the_post();
									$user = get_user_by('id', carbon_get_the_post_meta('crb_pending_with_user')[0]['id']);
								?>

									<tr>
										<th scope="row"><?php echo get_the_ID(); ?></th>
										<td><?php echo $user->display_name; ?></td>
										<td>₹<?php echo number_format(esc_html(carbon_get_the_post_meta('crb_pending_amount')), 2); ?></td>
									</tr>

								<?php endwhile; ?>

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
