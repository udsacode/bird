<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/*-----------------------------------------------------------------------------------

TABLE OF CONTENTS

- Register WP Menus
- Breadcrumb display
- Page navigation
- Post Meta
- Subscribe & Connect
- Comment Form Fields
- Comment Form Settings
- Archive Description
- WooPagination markup
- Google maps (for contact template)
- Featured Slider: Post Type
- Featured Slider: Hook Into Content
- Featured Slider: Get Slides
- Is IE
- Check if WooCommerce is activated
- Get Menu Name
- Social Icons
- Homepage posts per page

-----------------------------------------------------------------------------------*/


/*-----------------------------------------------------------------------------------*/
/* Register WP Menus */
/*-----------------------------------------------------------------------------------*/
if ( function_exists( 'wp_nav_menu') ) {
	add_theme_support( 'nav-menus' );
	register_nav_menus( array( 'primary-menu' => __( 'Primary Menu', 'woothemes' ) ) );
	register_nav_menus( array( 'top-menu' => __( 'Top Menu', 'woothemes' ) ) );
}

/*-----------------------------------------------------------------------------------*/
/* Breadcrumb display */
/*-----------------------------------------------------------------------------------*/

add_action('woo_main_before','woo_display_breadcrumbs',10);
if (!function_exists( 'woo_display_breadcrumbs')) {
	function woo_display_breadcrumbs() {
		global $woo_options;
		if ( isset( $woo_options['woo_breadcrumbs_show'] ) && $woo_options['woo_breadcrumbs_show'] == 'true' && ! is_home() ) {
		echo '<section id="breadcrumbs" class="content-container-fullwidth">';
			woo_breadcrumbs();
		echo '</section><!--/#breadcrumbs -->';
		}
	} // End woo_display_breadcrumbs()
} // End IF Statement


/*-----------------------------------------------------------------------------------*/
/* Page navigation */
/*-----------------------------------------------------------------------------------*/
if (!function_exists( 'woo_pagenav')) {
	function woo_pagenav() {

		global $woo_options;

		// If the user has set the option to use simple paging links, display those. By default, display the pagination.
		if ( array_key_exists( 'woo_pagination_type', $woo_options ) && $woo_options[ 'woo_pagination_type' ] == 'simple' ) {
			if ( get_next_posts_link() || get_previous_posts_link() ) {
		?>
            <nav class="nav-entries fix">
                <?php next_posts_link( '<span class="nav-prev fl">'. __( '<span class="meta-nav">&larr;</span> Older posts', 'woothemes' ) . '</span>' ); ?>
                <?php previous_posts_link( '<span class="nav-next fr">'. __( 'Newer posts <span class="meta-nav">&rarr;</span>', 'woothemes' ) . '</span>' ); ?>
            </nav>
		<?php
			}
		} else {
			woo_pagination();

		} // End IF Statement

	} // End woo_pagenav()
} // End IF Statement


/*-----------------------------------------------------------------------------------*/
/* Post Meta */
/*-----------------------------------------------------------------------------------*/

if (!function_exists( 'woo_post_meta')) {
	function woo_post_meta( ) {
?>
<aside class="post-meta">
	<ul>
		<li class="post-date">
			<span><?php the_time( get_option( 'date_format' ) ); ?></span>
		</li>
		<li class="post-category">
			<?php the_category( ', ') ?>
		</li>
		<?php edit_post_link( __( 'Edit', 'woothemes' ), '<li class="edit">', '</li>' ); ?>
	</ul>
</aside>
<?php
	}
}


/*-----------------------------------------------------------------------------------*/
/* Subscribe / Connect */
/*-----------------------------------------------------------------------------------*/

if (!function_exists( 'woo_subscribe_connect')) {
	function woo_subscribe_connect($widget = 'false', $title = '', $form = '', $social = '', $contact_template = 'false') {

		//Setup default variables, overriding them if the "Theme Options" have been saved.
		$settings = array(
						'connect' => 'false',
						'connect_title' => __('Subscribe' , 'woothemes'),
						'connect_related' => 'true',
						'connect_content' => __( 'Subscribe to our e-mail newsletter to receive updates.', 'woothemes' ),
						'connect_newsletter_id' => '',
						'connect_mailchimp_list_url' => '',
						'feed_url' => '',
						'connect_rss' => '',
						'connect_twitter' => '',
						'connect_facebook' => '',
						'connect_youtube' => '',
						'connect_flickr' => '',
						'connect_linkedin' => '',
						'connect_delicious' => '',
						'connect_rss' => '',
						'connect_googleplus' => '',
						'connect_dribbble' => '',
						'connect_instagram' => '',
						'connect_vimeo' => '',
						'connect_pinterest' => ''
						);
		$settings = woo_get_dynamic_values( $settings );

		// Setup title
		if ( $widget != 'true' )
			$title = $settings[ 'connect_title' ];

		// Setup related post (not in widget)
		$related_posts = '';
		if ( $settings[ 'connect_related' ] == "true" AND $widget != "true" )
			$related_posts = do_shortcode( '[related_posts limit="5"]' );

?>
	<?php if ( $settings[ 'connect' ] == "true" OR $widget == 'true' ) : ?>
	<aside id="connect" class="fix">
		<h3><?php if ( $title ) echo apply_filters( 'widget_title', $title ); else _e('Subscribe','woothemes'); ?></h3>

		<div>
			<?php if ($settings[ 'connect_content' ] != '' AND $contact_template == 'false') echo '<p>' . stripslashes($settings[ 'connect_content' ]) . '</p>'; ?>

			<?php if ( $settings[ 'connect_newsletter_id' ] != "" AND $form != 'on' ) : ?>
			<form class="newsletter-form" action="http://feedburner.google.com/fb/a/mailverify" method="post" target="popupwindow" onsubmit="window.open( 'http://feedburner.google.com/fb/a/mailverify?uri=<?php echo $settings[ 'connect_newsletter_id' ]; ?>', 'popupwindow', 'scrollbars=yes,width=550,height=520' );return true">
				<input class="email" type="text" name="email" value="<?php esc_attr_e( 'E-mail', 'woothemes' ); ?>" onfocus="if (this.value == '<?php _e( 'E-mail', 'woothemes' ); ?>') {this.value = '';}" onblur="if (this.value == '') {this.value = '<?php _e( 'E-mail', 'woothemes' ); ?>';}" />
				<input type="hidden" value="<?php echo $settings[ 'connect_newsletter_id' ]; ?>" name="uri"/>
				<input type="hidden" value="<?php bloginfo( 'name' ); ?>" name="title"/>
				<input type="hidden" name="loc" value="en_US"/>
				<input class="submit" type="submit" name="submit" value="<?php _e( 'Submit', 'woothemes' ); ?>" />
			</form>
			<?php endif; ?>

			<?php if ( $settings['connect_mailchimp_list_url'] != "" AND $form != 'on' AND $settings['connect_newsletter_id'] == "" ) : ?>
			<!-- Begin MailChimp Signup Form -->
			<div id="mc_embed_signup">
				<form class="newsletter-form<?php if ( $related_posts == '' ) echo ' fl'; ?>" action="<?php echo $settings['connect_mailchimp_list_url']; ?>" method="post" target="popupwindow" onsubmit="window.open('<?php echo $settings['connect_mailchimp_list_url']; ?>', 'popupwindow', 'scrollbars=yes,width=650,height=520');return true">
					<input type="text" name="EMAIL" class="required email" value="<?php _e('E-mail','woothemes'); ?>"  id="mce-EMAIL" onfocus="if (this.value == '<?php _e('E-mail','woothemes'); ?>') {this.value = '';}" onblur="if (this.value == '') {this.value = '<?php _e('E-mail','woothemes'); ?>';}">
					<input type="submit" value="<?php _e('Submit', 'woothemes'); ?>" name="subscribe" id="mc-embedded-subscribe" class="btn submit button">
				</form>
			</div>
			<!--End mc_embed_signup-->
			<?php endif; ?>

			<?php if ( $social != 'on' ) : ?>
			<div class="social">
		   		<?php if ( $settings['connect_rss' ] == "true" ) { ?>
		   		<a target="_blank" href="<?php if ( $settings['feed_url'] ) { echo esc_url( $settings['feed_url'] ); } else { echo get_bloginfo_rss('rss2_url'); } ?>" class="subscribe" title="RSS"></a>

		   		<?php } if ( $settings['connect_twitter' ] != "" ) { ?>
		   		<a target="_blank" href="<?php echo esc_url( $settings['connect_twitter'] ); ?>" class="twitter" title="Twitter"></a>

		   		<?php } if ( $settings['connect_facebook' ] != "" ) { ?>
		   		<a target="_blank" href="<?php echo esc_url( $settings['connect_facebook'] ); ?>" class="facebook" title="Facebook"></a>

		   		<?php } if ( $settings['connect_youtube' ] != "" ) { ?>
		   		<a target="_blank" href="<?php echo esc_url( $settings['connect_youtube'] ); ?>" class="youtube" title="YouTube"></a>

		   		<?php } if ( $settings['connect_flickr' ] != "" ) { ?>
		   		<a target="_blank" href="<?php echo esc_url( $settings['connect_flickr'] ); ?>" class="flickr" title="Flickr"></a>

		   		<?php } if ( $settings['connect_linkedin' ] != "" ) { ?>
		   		<a target="_blank" href="<?php echo esc_url( $settings['connect_linkedin'] ); ?>" class="linkedin" title="LinkedIn"></a>

		   		<?php } if ( $settings['connect_delicious' ] != "" ) { ?>
		   		<a target="_blank" href="<?php echo esc_url( $settings['connect_delicious'] ); ?>" class="delicious" title="Delicious"></a>

		   		<?php } if ( $settings['connect_googleplus' ] != "" ) { ?>
		   		<a target="_blank" href="<?php echo esc_url( $settings['connect_googleplus'] ); ?>" class="googleplus" title="Google+"></a>

				<?php } if ( $settings['connect_dribbble' ] != "" ) { ?>
		   		<a target="_blank" href="<?php echo esc_url( $settings['connect_dribbble'] ); ?>" class="dribbble" title="Dribbble"></a>

				<?php } if ( $settings['connect_instagram' ] != "" ) { ?>
		   		<a target="_blank" href="<?php echo esc_url( $settings['connect_instagram'] ); ?>" class="instagram" title="Instagram"></a>

				<?php } if ( $settings['connect_vimeo' ] != "" ) { ?>
		   		<a target="_blank" href="<?php echo esc_url( $settings['connect_vimeo'] ); ?>" class="vimeo" title="Vimeo"></a>

				<?php } if ( $settings['connect_pinterest' ] != "" ) { ?>
		   		<a target="_blank" href="<?php echo esc_url( $settings['connect_pinterest'] ); ?>" class="pinterest" title="Pinterest"></a>

				<?php } ?>
			</div>
			<?php endif; ?>

		</div>

	</aside>

	<?php if ( $settings['connect_related' ] == "true" AND $related_posts != '' ) : ?>
	<div id="related-posts">
	    <h4><?php _e( 'Related Posts:', 'woothemes' ); ?></h4>
	    <?php echo $related_posts; ?>
	</div><!-- col-right -->
	<?php wp_reset_query(); endif; ?>

	<?php endif; ?>
<?php
	}
}

/*-----------------------------------------------------------------------------------*/
/* Comment Form Fields */
/*-----------------------------------------------------------------------------------*/

	add_filter( 'comment_form_default_fields', 'woo_comment_form_fields' );

	if ( ! function_exists( 'woo_comment_form_fields' ) ) {
		function woo_comment_form_fields ( $fields ) {

			$commenter = wp_get_current_commenter();

			$required_text = ' <span class="required">(' . __( 'Required', 'woothemes' ) . ')</span>';

			$req = get_option( 'require_name_email' );
			$aria_req = ( $req ? " aria-required='true'" : '' );
			$fields =  array(
				'author' => '<p class="comment-form-author">' .
							'<input id="author" class="txt" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30"' . $aria_req . ' />' .
							'<label for="author">' . __( 'Name' ) . ( $req ? $required_text : '' ) . '</label> ' .
							'</p>',
				'email'  => '<p class="comment-form-email">' .
				            '<input id="email" class="txt" name="email" type="text" value="' . esc_attr(  $commenter['comment_author_email'] ) . '" size="30"' . $aria_req . ' />' .
				            '<label for="email">' . __( 'Email' ) . ( $req ? $required_text : '' ) . '</label> ' .
				            '</p>',
				'url'    => '<p class="comment-form-url">' .
				            '<input id="url" class="txt" name="url" type="text" value="' . esc_attr( $commenter['comment_author_url'] ) . '" size="30" />' .
				            '<label for="url">' . __( 'Website' ) . '</label>' .
				            '</p>',
			);

			return $fields;

		} // End woo_comment_form_fields()
	}

/*-----------------------------------------------------------------------------------*/
/* Comment Form Settings */
/*-----------------------------------------------------------------------------------*/

	add_filter( 'comment_form_defaults', 'woo_comment_form_settings' );

	if ( ! function_exists( 'woo_comment_form_settings' ) ) {
		function woo_comment_form_settings ( $settings ) {

			$settings['comment_notes_before'] = '';
			$settings['comment_notes_after'] = '';
			$settings['label_submit'] = __( 'Submit Comment', 'woothemes' );
			$settings['cancel_reply_link'] = __( 'Click here to cancel reply.', 'woothemes' );

			return $settings;

		} // End woo_comment_form_settings()
	}

	/*-----------------------------------------------------------------------------------*/
	/* Misc back compat */
	/*-----------------------------------------------------------------------------------*/

	// array_fill_keys doesn't exist in PHP < 5.2
	// Can remove this after PHP <  5.2 support is dropped
	if ( !function_exists( 'array_fill_keys' ) ) {
		function array_fill_keys( $keys, $value ) {
			return array_combine( $keys, array_fill( 0, count( $keys ), $value ) );
		}
	}

/*-----------------------------------------------------------------------------------*/
/**
 * woo_archive_description()
 *
 * Display a description, if available, for the archive being viewed (category, tag, other taxonomy).
 *
 * @since V1.0.0
 * @uses do_atomic(), get_queried_object(), term_description()
 * @echo string
 * @filter woo_archive_description
 */

if ( ! function_exists( 'woo_archive_description' ) ) {
	function woo_archive_description ( $echo = true ) {
		do_action( 'woo_archive_description' );

		// Archive Description, if one is available.
		$description = '';

		if ( ! is_post_type_archive() ) {

			if ( get_queried_object() != null ) {
				$term_obj 		= get_queried_object();
				$description 	= term_description( $term_obj->term_id, $term_obj->taxonomy );
			} else {
				$description 	= '';
			}

		}

		if ( $description != '' ) {
			// Allow child themes/plugins to filter here ( 1: text in DIV and paragraph, 2: term object )
			$description = apply_filters( 'woo_archive_description', '<div class="archive-description content-container-fullwidth">' . $description . '</div><!--/.archive-description-->', $term_obj );
		}

		if ( $echo != true ) { return $description; }

		echo $description;
	} // End woo_archive_description()
}

/*-----------------------------------------------------------------------------------*/
/* WooPagination Markup */
/*-----------------------------------------------------------------------------------*/

add_filter( 'woo_pagination_args', 'woo_pagination_html5_markup', 2 );

function woo_pagination_html5_markup ( $args ) {
	$args['before'] = '<nav class="pagination woo-pagination">';
	$args['after'] = '</nav>';

	return $args;
} // End woo_pagination_html5_markup()


/*-----------------------------------------------------------------------------------*/
/* Google Maps */
/*-----------------------------------------------------------------------------------*/

function woo_maps_contact_output($args){

	$key = get_option('woo_maps_apikey');

	// No More API Key needed

	if ( !is_array($args) )
		parse_str( $args, $args );

	extract($args);
	$mode = '';
	$streetview = 'off';
	$map_height = get_option('woo_maps_single_height');
	$featured_w = get_option('woo_home_featured_w');
	$featured_h = get_option('woo_home_featured_h');
	$zoom = get_option('woo_maps_default_mapzoom');
	$type = get_option('woo_maps_default_maptype');
	$marker_title = get_option('woo_contact_title');
	if ( $zoom == '' ) { $zoom = 6; }
	$lang = get_option('woo_maps_directions_locale');
	$locale = '';
	if(!empty($lang)){
		$locale = ',locale :"'.$lang.'"';
	}
	$extra_params = ',{travelMode:G_TRAVEL_MODE_WALKING,avoidHighways:true '.$locale.'}';

	if(empty($map_height)) { $map_height = 250;}

	if(is_home() && !empty($featured_h) && !empty($featured_w)){
	?>
    <div id="single_map_canvas" style="width:<?php echo $featured_w; ?>px; height: <?php echo $featured_h; ?>px"></div>
    <?php } else { ?>
    <div id="single_map_canvas" style="width:100%; height: <?php echo $map_height; ?>px"></div>
    <?php } ?>
    <script type="text/javascript">
		jQuery(document).ready(function(){
			function initialize() {


			<?php if($streetview == 'on'){ ?>


			<?php } else { ?>

			  	<?php switch ($type) {
			  			case 'G_NORMAL_MAP':
			  				$type = 'ROADMAP';
			  				break;
			  			case 'G_SATELLITE_MAP':
			  				$type = 'SATELLITE';
			  				break;
			  			case 'G_HYBRID_MAP':
			  				$type = 'HYBRID';
			  				break;
			  			case 'G_PHYSICAL_MAP':
			  				$type = 'TERRAIN';
			  				break;
			  			default:
			  				$type = 'ROADMAP';
			  				break;
			  	} ?>

			  	var myLatlng = new google.maps.LatLng(<?php echo $geocoords; ?>);
				var myOptions = {
				  zoom: <?php echo $zoom; ?>,
				  center: myLatlng,
				  mapTypeId: google.maps.MapTypeId.<?php echo $type; ?>
				};
				<?php if(get_option('woo_maps_scroll') == 'true'){ ?>
			  	myOptions.scrollwheel = false;
			  	<?php } ?>
			  	var map = new google.maps.Map(document.getElementById("single_map_canvas"),  myOptions);

				<?php if($mode == 'directions'){ ?>
			  	directionsPanel = document.getElementById("featured-route");
 				directions = new GDirections(map, directionsPanel);
  				directions.load("from: <?php echo $from; ?> to: <?php echo $to; ?>" <?php if($walking == 'on'){ echo $extra_params;} ?>);
			  	<?php
			 	} else { ?>

			  		var point = new google.maps.LatLng(<?php echo $geocoords; ?>);
	  				var root = "<?php echo esc_url( get_template_directory_uri() ); ?>";
	  				var callout = '<?php echo preg_replace("/[\n\r]/","<br/>",get_option('woo_maps_callout_text')); ?>';
	  				var the_link = '<?php echo get_permalink(get_the_id()); ?>';
	  				<?php $title = str_replace(array('&#8220;','&#8221;'),'"', $marker_title); ?>
	  				<?php $title = str_replace('&#8211;','-',$title); ?>
	  				<?php $title = str_replace('&#8217;',"`",$title); ?>
	  				<?php $title = str_replace('&#038;','&',$title); ?>
	  				var the_title = '<?php echo html_entity_decode($title) ?>';

	  			<?php
			 	if(is_page()){
			 		$custom = get_option('woo_cat_custom_marker_pages');
					if(!empty($custom)){
						$color = $custom;
					}
					else {
						$color = get_option('woo_cat_colors_pages');
						if (empty($color)) {
							$color = 'red';
						}
					}
			 	?>
			 		var color = '<?php echo $color; ?>';
			 		createMarker(map,point,root,the_link,the_title,color,callout);
			 	<?php } else { ?>
			 		var color = '<?php echo get_option('woo_cat_colors_pages'); ?>';
	  				createMarker(map,point,root,the_link,the_title,color,callout);
				<?php
				}
					if(isset($_POST['woo_maps_directions_search'])){ ?>

					directionsPanel = document.getElementById("featured-route");
 					directions = new GDirections(map, directionsPanel);
  					directions.load("from: <?php echo htmlspecialchars($_POST['woo_maps_directions_search']); ?> to: <?php echo $address; ?>" <?php if($walking == 'on'){ echo $extra_params;} ?>);



					directionsDisplay = new google.maps.DirectionsRenderer();
					directionsDisplay.setMap(map);
    				directionsDisplay.setPanel(document.getElementById("featured-route"));

					<?php if($walking == 'on'){ ?>
					var travelmodesetting = google.maps.DirectionsTravelMode.WALKING;
					<?php } else { ?>
					var travelmodesetting = google.maps.DirectionsTravelMode.DRIVING;
					<?php } ?>
					var start = '<?php echo htmlspecialchars($_POST['woo_maps_directions_search']); ?>';
					var end = '<?php echo $address; ?>';
					var request = {
       					origin:start,
        				destination:end,
        				travelMode: travelmodesetting
    				};
    				directionsService.route(request, function(response, status) {
      					if (status == google.maps.DirectionsStatus.OK) {
        					directionsDisplay.setDirections(response);
      					}
      				});

  					<?php } ?>
				<?php } ?>
			<?php } ?>


			  }
			  function handleNoFlash(errorCode) {
				  if (errorCode == FLASH_UNAVAILABLE) {
					alert("Error: Flash doesn't appear to be supported by your browser");
					return;
				  }
				 }



		initialize();

		});
	jQuery(window).load(function(){

		var newHeight = jQuery('#featured-content').height();
		newHeight = newHeight - 5;
		if(newHeight > 300){
			jQuery('#single_map_canvas').height(newHeight);
		}

	});

	</script>

<?php
}

/*-----------------------------------------------------------------------------------*/
/* Featured Slider: Post Type */
/*-----------------------------------------------------------------------------------*/

if ( ! function_exists( 'woo_featured_slider_post_type' ) ) {
function woo_featured_slider_post_type () {
	$labels = array(
		'name' => _x( 'Slides', 'post type general name', 'woothemes' ),
		'singular_name' => _x( 'Slide', 'post type singular name', 'woothemes' ),
		'add_new' => _x( 'Add New', 'slide', 'woothemes' ),
		'add_new_item' => __( 'Add New Slide', 'woothemes' ),
		'edit_item' => __( 'Edit Slide', 'woothemes' ),
		'new_item' => __( 'New Slide', 'woothemes' ),
		'view_item' => __( 'View Slide', 'woothemes' ),
		'search_items' => __( 'Search Slides', 'woothemes' ),
		'not_found' =>  __( 'No slides found', 'woothemes' ),
		'not_found_in_trash' => __( 'No slides found in Trash', 'woothemes' ),
		'parent_item_colon' => __( 'Parent slide:', 'woothemes' )
	);
	$args = array(
		'labels' => $labels,
		'public' => false,
		'publicly_queryable' => false,
		'show_ui' => true,
		'query_var' => true,
		'rewrite' => true,
		'capability_type' => 'post',
		'hierarchical' => false,
		'taxonomies' => array( 'slide-page' ),
		'menu_icon' => esc_url( get_template_directory_uri() . '/includes/images/slides.png' ),
		'menu_position' => 5,
		'supports' => array('title','editor','thumbnail', /*'author','thumbnail','excerpt','comments'*/)
	);

	register_post_type( 'slide', $args );

	// "Slide Pages" Custom Taxonomy
	$labels = array(
		'name' => _x( 'Slide Groups', 'taxonomy general name', 'woothemes' ),
		'singular_name' => _x( 'Slide Groups', 'taxonomy singular name', 'woothemes' ),
		'search_items' =>  __( 'Search Slide Groups', 'woothemes' ),
		'all_items' => __( 'All Slide Groups', 'woothemes' ),
		'parent_item' => __( 'Parent Slide Group', 'woothemes' ),
		'parent_item_colon' => __( 'Parent Slide Group:', 'woothemes' ),
		'edit_item' => __( 'Edit Slide Group', 'woothemes' ),
		'update_item' => __( 'Update Slide Group', 'woothemes' ),
		'add_new_item' => __( 'Add New Slide Group', 'woothemes' ),
		'new_item_name' => __( 'New Slide Group Name', 'woothemes' ),
		'menu_name' => __( 'Slide Groups', 'woothemes' )
	);

	$args = array(
		'hierarchical' => true,
		'labels' => $labels,
		'show_ui' => true,
		'query_var' => true,
		'rewrite' => array( 'slug' => 'slide-page' )
	);

	register_taxonomy( 'slide-page', array( 'slide' ), $args );
} // End woo_featured_slider_post_type()
}

add_action( 'init', 'woo_featured_slider_post_type' );

/*-----------------------------------------------------------------------------------*/
/* Featured Slider: Hook Into Content */
/*-----------------------------------------------------------------------------------*/

if ( ! function_exists( 'woo_featured_slider_loader' ) ) {
function woo_featured_slider_loader () {
	$settings = woo_get_dynamic_values( array( 'featured' => 'true' ) );

	if ( is_home() && ( $settings['featured'] == 'true' ) ) {
		get_template_part( 'includes/featured', 'slider' );
	}
} // End woo_featured_slider_loader()
}

add_action( 'woo_main_before', 'woo_featured_slider_loader' );

/*-----------------------------------------------------------------------------------*/
/* Featured Slider: Get Slides */
/*-----------------------------------------------------------------------------------*/

if ( ! function_exists( 'woo_featured_slider_get_slides' ) ) {
function woo_featured_slider_get_slides ( $args ) {
	$defaults = array( 'limit' => '5', 'order' => 'DESC', 'term' => '0' );
	$args = wp_parse_args( (array)$args, $defaults );
	$query_args = array( 'post_type' => 'slide' );
	if ( in_array( strtoupper( $args['order'] ), array( 'ASC', 'DESC' ) ) ) {
		$query_args['order'] = strtoupper( $args['order'] );
	}
	if ( 0 < intval( $args['limit'] ) ) {
		$query_args['numberposts'] = intval( $args['limit'] );
	}
	if ( 0 < intval( $args['term'] ) ) {
		$query_args['tax_query'] = array(
										array( 'taxonomy' => 'slide-page', 'field' => 'id', 'terms' => intval( $args['term']) )
									);
	}

	$slides = false;

	$query = get_posts( $query_args );

	if ( ! is_wp_error( $query ) && ( 0 < count( $query ) ) ) {
		$slides = $query;
	}

	return $slides;
} // End woo_featured_slider_get_slides()
}

/*-----------------------------------------------------------------------------------*/
/* Is IE */
/*-----------------------------------------------------------------------------------*/

if ( ! function_exists( 'is_ie' ) ) {
	function is_ie ( $version = '6.0' ) {
		$supported_versions = array( '6.0', '7.0', '8.0', '9.0' );
		$agent = substr( $_SERVER['HTTP_USER_AGENT'], 25, 4 );
		$current_version = substr( $_SERVER['HTTP_USER_AGENT'], 30, 3 );
		$response = false;
		if ( in_array( $version, $supported_versions ) && 'MSIE' == $agent && ( $version == $current_version ) ) {
			$response = true;
		}

		return $response;
	} // End is_ie()
}

/*-----------------------------------------------------------------------------------*/
/* Display social icons via "Subscribe & Connect" */
/*-----------------------------------------------------------------------------------*/
if ( ! function_exists( 'woo_display_social_icons' ) ) {
function woo_display_social_icons () {
	$html = '';
	$name = get_bloginfo( 'name' );
	$social_icons = array(
						'rss' => __( 'Subscribe to our RSS feed', 'woothemes' ),
						'twitter' => sprintf( __( 'Follow %s on Twitter', 'woothemes' ), $name ),
						'facebook' => sprintf( __( 'Like %s on Facebook', 'woothemes' ), $name ),
						'youtube' => sprintf( __( 'Watch %s on YouTube', 'woothemes' ), $name ),
						'flickr' => sprintf( __( 'Follow %s on Flickr', 'woothemes' ), $name ),
						'linkedin' => sprintf( __( 'Connect with %s on LinkedIn', 'woothemes' ), $name ),
						'delicious' => sprintf( __( 'Follow %s on Delicious', 'woothemes' ), $name ),
						'googleplus' => sprintf( __( 'Friend %s on Google+', 'woothemes' ), $name )
						);

	$social_icons = (array)apply_filters( 'woo_contact_social_icons', $social_icons );

	$settings_keys = array();
	foreach ( array_keys( $social_icons ) as $k => $v ) {
		$settings_keys['connect_' . $v] = '';
	}

	$settings = woo_get_dynamic_values( $settings_keys );

	if ( 'true' == $settings['connect_rss'] ) {
		$settings['connect_rss'] = get_feed_link();
	} else {
		$settings['connect_rss'] = '';
	}

	$html .= '<div id="connect">' . "\n";
	$html .= '<div class="social">' . "\n";
	foreach ( $social_icons as $k => $v ) {
		$class = $k;
		if ( 'rss' == $k ) { $class = 'subscribe'; }
		if ( '' != $settings['connect_' . $k] ) {
			$html .= '<a href="' . esc_url( $settings['connect_' . $k] ) . '" title="' . esc_attr( $v ) . '" class="' . $class . '"><span>' . $v . '</span></a>' . "\n";
		}
	}
	$html .= '</div>' . "\n";
	$html .= '</div>' . "\n";

	echo $html;
} // End woo_display_social_icons()
}

/*-----------------------------------------------------------------------------------*/
/* Check if WooCommerce is activated */
/*-----------------------------------------------------------------------------------*/

if ( ! function_exists( 'is_woocommerce_activated' ) ) {
	function is_woocommerce_activated() {
		if ( class_exists( 'woocommerce' ) ) { return true; } else { return false; }
	}
}


/*-----------------------------------------------------------------------------------*/
/* Get Menu Name */
/*-----------------------------------------------------------------------------------*/

function woo_get_menu_name($location){
    if(!has_nav_menu($location)) return false;
    $menus = get_nav_menu_locations();
    $menu_title = wp_get_nav_menu_object($menus[$location])->name;
    return $menu_title;
}

/*-----------------------------------------------------------------------------------*/
/* Woo Social Icons */
/*-----------------------------------------------------------------------------------*/

function woo_social_icons() {
    	$settings = array(
			'feed_url' => '',
			'connect_rss' => '',
			'connect_twitter' => '',
			'connect_facebook' => '',
			'connect_youtube' => '',
			'connect_flickr' => '',
			'connect_linkedin' => '',
			'connect_delicious' => '',
			'connect_rss' => '',
			'connect_googleplus' => '',
			'connect_dribbble' => '',
			'connect_instagram' => '',
			'connect_vimeo' => '',
			'connect_pinterest' => ''
			);
		$settings = woo_get_dynamic_values( $settings );
    ?>
    <div class="social">

    	<?php if ( apply_filters( 'upstart_navigation_connect_header', true ) ) : ?>
	    	<h3><?php _e( 'Connect', 'woothemes' ); ?></h3>
	    <?php endif; ?>

	    <div class="social-icons">

	   		<?php if ( $settings['connect_rss' ] == "true" ) { ?>
	   		<a target="_blank" href="<?php if ( $settings['feed_url'] ) { echo esc_url( $settings['feed_url'] ); } else { echo get_bloginfo_rss('rss2_url'); } ?>" class="subscribe" title="RSS"></a>

	   		<?php } if ( $settings['connect_twitter' ] != "" ) { ?>
	   		<a target="_blank" href="<?php echo esc_url( $settings['connect_twitter'] ); ?>" class="twitter" title="Twitter"></a>

	   		<?php } if ( $settings['connect_facebook' ] != "" ) { ?>
	   		<a target="_blank" href="<?php echo esc_url( $settings['connect_facebook'] ); ?>" class="facebook" title="Facebook"></a>

	   		<?php } if ( $settings['connect_youtube' ] != "" ) { ?>
	   		<a target="_blank" href="<?php echo esc_url( $settings['connect_youtube'] ); ?>" class="youtube" title="YouTube"></a>

	   		<?php } if ( $settings['connect_flickr' ] != "" ) { ?>
	   		<a target="_blank" href="<?php echo esc_url( $settings['connect_flickr'] ); ?>" class="flickr" title="Flickr"></a>

	   		<?php } if ( $settings['connect_linkedin' ] != "" ) { ?>
	   		<a target="_blank" href="<?php echo esc_url( $settings['connect_linkedin'] ); ?>" class="linkedin" title="LinkedIn"></a>

	   		<?php } if ( $settings['connect_delicious' ] != "" ) { ?>
	   		<a target="_blank" href="<?php echo esc_url( $settings['connect_delicious'] ); ?>" class="delicious" title="Delicious"></a>

	   		<?php } if ( $settings['connect_googleplus' ] != "" ) { ?>
	   		<a target="_blank" href="<?php echo esc_url( $settings['connect_googleplus'] ); ?>" class="googleplus" title="Google+"></a>

			<?php } if ( $settings['connect_dribbble' ] != "" ) { ?>
	   		<a target="_blank" href="<?php echo esc_url( $settings['connect_dribbble'] ); ?>" class="dribbble" title="Dribbble"></a>

			<?php } if ( $settings['connect_instagram' ] != "" ) { ?>
	   		<a target="_blank" href="<?php echo esc_url( $settings['connect_instagram'] ); ?>" class="instagram" title="Instagram"></a>

			<?php } if ( $settings['connect_vimeo' ] != "" ) { ?>
	   		<a target="_blank" href="<?php echo esc_url( $settings['connect_vimeo'] ); ?>" class="vimeo" title="Vimeo"></a>

			<?php } if ( $settings['connect_pinterest' ] != "" ) { ?>
	   		<a target="_blank" href="<?php echo esc_url( $settings['connect_pinterest'] ); ?>" class="pinterest" title="Pinterest"></a>

			<?php } ?>

		</div>

	</div> <?php
}


/*-----------------------------------------------------------------------------------*/
/* homepage posts per page */
/*-----------------------------------------------------------------------------------*/

add_action('pre_get_posts', 'upstart_homepage_posts');
function upstart_homepage_posts( $query ){
	if ( is_home() ):
	    $query->set( 'posts_per_page', 12 );
	    return;
	endif;
}

/*-----------------------------------------------------------------------------------*/
/* END */
/*-----------------------------------------------------------------------------------*/
?>