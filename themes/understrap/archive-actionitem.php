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

<div class="wrapper" id="actionitem-wrapper">

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

					<?php
					while (have_posts()):
						the_post();

						$image_size = 'thumbnail_image';
						$img_attrs = ['class' => 'card-img-top', 'alt' => 'Action Item Image'];
						$crb_image = carbon_get_the_post_meta('crb_actionitem_featured_image');
						$image = wp_get_attachment_image($crb_image[0]['actionitem_image'], $image_size, false, $img_attrs);
						if (strlen($image) == 0) {
							$image = wp_get_attachment_image("71", $image_size, false, $img_attrs);
						}
					?>

						<div class="col-12 col-sm-6 col-lg-4 mb-3 d-flex justify-content-center">
							<div class="card" style="max-width: 18rem;">

								<?php echo $image; ?>

								<div class="card-body">
									<h5 class="card-title"><?php the_title(); ?></h5>
									<p class="card-text">
										<?php echo wp_trim_words(get_the_content(), 10); ?>
									</p>
									<a href="<?php the_permalink(); ?>" target="_blank" class="btn btn-primary w-100">View Details</a>
								</div>
							</div>
						</div>

					<?php
					endwhile;
					?>

				</div><!-- .row -->

			<?php endif; ?>

		</main>

		<div class="d-flex justify-content-center my-3">
			<?php echo understrap_pagination(); ?>
		</div>

	</div><!-- #content -->

</div><!-- #index-wrapper -->

<?php
get_footer();
