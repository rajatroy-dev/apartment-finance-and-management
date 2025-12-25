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

<div class="wrapper" id="repair-record-wrapper">

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

				<div class="my-5">
					<div class="row">

						<?php
						while (have_posts()):
							the_post();
						?>

							<div class="col">
								<a href="<?php the_permalink(); ?>" class="btn btn-primary w-100"><?php echo wp_trim_words(get_the_title(), 100); ?></a>
							</div>

						<?php
						endwhile;
						?>


					</div><!-- .row -->
				</div>

			<?php endif; ?>

		</main>

	</div><!-- #content -->

</div><!-- #archive-wrapper -->

<?php
get_footer();
