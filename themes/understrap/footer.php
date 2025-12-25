<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after
 *
 * @package Understrap
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

$container = get_theme_mod( 'understrap_container_type' );
?>

<?php get_template_part( 'sidebar-templates/sidebar', 'footerfull' ); ?>

<div class="wrapper bg-light fixed-bottom py-2" id="wrapper-footer">

	<div class="<?php echo esc_attr( $container ); ?>">

		<div class="row">

			<div class="col">

				<footer class="site-footer" id="colophon">

					<p class="text-end m-0">
						Made with <i class="bi bi-heart-fill text-danger"></i> by <a href="https://www.linkedin.com/in/rajat-roy-574686201" target="_blank" rel="noopener noreferrer">Rajat Roy</a> for <?php echo getenv('APARTMENT_NAME'); ?>
					</p>

				</footer><!-- #colophon -->

			</div><!-- col -->

		</div><!-- .row -->

	</div><!-- .container(-fluid) -->

</div><!-- #wrapper-footer -->

<?php // Closing div#page from header.php. ?>
</div><!-- #page -->

<?php wp_footer(); ?>

</body>

</html>

