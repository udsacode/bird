<?php
// File Security Check
if ( ! function_exists( 'wp' ) && ! empty( $_SERVER['SCRIPT_FILENAME'] ) && basename( __FILE__ ) == basename( $_SERVER['SCRIPT_FILENAME'] ) ) {
    die ( 'You do not have sufficient permissions to access this page!' );
}
?><?php
/**
 * Index Template
 *
 * Here we setup all logic and XHTML that is required for the index template, used as both the homepage
 * and as a fallback template, if a more appropriate template file doesn't exist for a specific context.
 *
 * @package WooFramework
 * @subpackage Template
 */
	get_header();
	global $woo_options;

?>

    <div id="content" class="content-container">

    	<?php woo_main_before(); ?>

		<section id="main">

		<?php if ( apply_filters( 'upstart_homepage_features', true ) && class_exists( 'Woothemes_Features' ) ) : ?>

		<section class="type-page">
			<?php do_action( 'woothemes_features', array( 'limit' => apply_filters( 'upstart_homepage_features_limit', 3 ), 'size' => 999 ) ); ?>
		</section><!-- /.type-page -->

		<?php endif; ?>

		<?php if ( apply_filters( 'upstart_homepage_testimonials', true ) && class_exists( 'Woothemes_Testimonials' ) ) : ?>

		<?php
			$count_testimonials 	= wp_count_posts( 'testimonial' );
			$published_testimonials = $count_testimonials->publish;
		?>
		<section class="testiominals content-container-fullwidth-nopadding">
			<h1 class="section-title"><?php _e( 'Отзывы', 'woothemes' ); ?></h1>
			<?php do_action( 'woothemes_testimonials', array( 'limit' => apply_filters( 'upstart_homepage_testimonials_limit', $testimonials_limit = 4 ) ) ); ?>
			<?php
				if ( $published_testimonials > $testimonials_limit ) {
					echo '<p class="more-testimonials"><a href="' . get_post_type_archive_link( 'testimonial' ) . '" class="button large">' . __( 'View all testimonials', 'woothemes' ) . '</a></p>';
				}
			?>
		</section><!-- /.testimonials -->

		<?php endif; ?>

		<?php if ( apply_filters( 'upstart_homepage_blog_posts', true ) ) : ?>

		<section class="blog-posts">
			<h1 class="section-title"><?php _e( 'Последние новости', 'woothemes' ); ?></h1>

			<?php woo_loop_before(); ?>

			<?php
				// To customise the query used on this template, please uncomment the code below.
				/*
				$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1; query_posts( array( 'post_type' => 'post', 'paged' => $paged ) );
				*/
	        	if ( have_posts() ) : $count = 0;
	        ?>

				<?php /* Start the Loop */ ?>
				<?php while ( have_posts() ) : the_post(); $count++; ?>

					<?php
						/* Include the Post-Format-specific template for the content.
						 * If you want to overload this in a child theme then include a file
						 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
						 */
						get_template_part( 'content', get_post_format() );
					?>

				<?php endwhile; ?>

			<?php else : ?>

	            <article <?php post_class(); ?>>
	                <p><?php _e( 'Sorry, no posts matched your criteria.', 'woothemes' ); ?></p>
	            </article><!-- /.post -->

	        <?php endif; ?>

	        <?php woo_loop_after(); ?>

			<?php woo_pagenav(); ?>

		</section><!--/.blog-posts -->

		<?php endif; ?>

		<?php if ( is_woocommerce_activated() && apply_filters( 'upstart_homepage_featured_products', true ) ) : ?>
			<section class="featured-products content-container-fullwidth-nopadding">
				<h1 class="section-title"><?php _e( 'Средства защиты от птиц', 'woothemes' ); ?></h1>
				<?php
					$featured_products_per_page = apply_filters( 'upstart_homepage_featured_products_per_page', $per_page = 12 );
					echo do_shortcode( '[featured_products per_page="' . $featured_products_per_page . '"]' );
				?>
			</section>
		<?php endif; ?>

		<?php if ( class_exists( 'Woothemes_Our_Team' ) && apply_filters( 'upstart_homepage_our_team', true ) ) : ?>
			<section class="meet-the-team content-container-fullwidth-nopadding">
				<h1 class="section-title"><?php _e( 'Meet the team', 'woothemes' ); ?></h1>
				<?php
					$our_team_per_page = apply_filters( 'upstart_homepage_our_team_per_page', $per_page = 8 );
					echo do_shortcode( '[woothemes_our_team per_page="' . $our_team_per_page . '"]' );
				?>
			</section>
		<?php endif; ?>

		</section><!-- /#main -->

		<?php woo_main_after(); ?>

    </div><!-- /#content -->

<?php get_footer(); ?>