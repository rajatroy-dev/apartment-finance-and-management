<?php

/**
 * Custom 404 Page
 */

// Exit if accessed directly.
defined('ABSPATH') || exit;

get_header();

$container = get_theme_mod('understrap_container_type');
?>

<div class="wrapper" id="transaction-audit-wrapper">

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
				get_template_part(
					'loop-templates/transaction-audit/content',
					'archive'
				);
			endif;
			?>

		</main>

	</div><!-- #content -->

</div><!-- #archive-wrapper -->

<?php
get_footer();
