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

<div class="wrapper" id="single-bulletin-wrapper">

	<div class="<?php echo esc_attr($container); ?> mb-5" id="content" tabindex="-1">

		<main class="site-main" id="single-bulletin-main">

			<nav aria-label="breadcrumb">
				<ol class="breadcrumb">
					<li class="breadcrumb-item"><a href="<?php echo get_home_url(); ?>">Home</a></li>
					<li class="breadcrumb-item"><a href="<?php echo get_post_type_archive_link('bulletin'); ?>">Bulletins</a></li>
					<li class="breadcrumb-item active" aria-current="page"><?php the_title(); ?></li>
				</ol>
			</nav>

			<?php
			if (!is_user_logged_in()):
				get_template_part('loop-templates/content-none');
			else:
			?>

				<h1 class="mb-3 text-center">NOTICE</h1>

				<section class="mb-5">

					<p class="lead mb-3">
						Commissioned By:
						<small class="text-body-secondary"><?php echo get_the_author_meta('display_name', get_post_field('post_author')); ?></small>
					</p>
					<p class="mb-3">
						<span class="fw-bold">Dated:</span>
						<span class="ms-1"><?php echo get_the_date('d/m/Y'); ?></span>
					</p> <!-- .mb-3 -->
					<p class="mb-3"><?php the_content(); ?></p> <!-- .mb-3 -->
				</section> <!-- .mb-5 -->
			<?php endif; ?>

		</main>

	</div><!-- #content -->

</div><!-- #index-wrapper -->

<?php
get_footer();
