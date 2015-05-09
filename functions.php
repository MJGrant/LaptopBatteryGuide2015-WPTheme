<?php
//* Start the engine
include_once( get_template_directory() . '/lib/init.php' );

//* Child theme (do not remove)
define( 'CHILD_THEME_NAME', 'Laptop Battery Guide Theme' );
define( 'CHILD_THEME_URL', 'http://www.laptopbatteryguide.com' );
define( 'CHILD_THEME_VERSION', '1.0.0' );

//* Enqueue Google Fonts
add_action( 'wp_enqueue_scripts', 'genesis_sample_google_fonts' );
function genesis_sample_google_fonts() {

	wp_enqueue_style( 'google-fonts', '//fonts.googleapis.com/css?family=Lato:300,400,700', array(), CHILD_THEME_VERSION );

}

//* Add HTML5 markup structure
add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list' ) );

//* Add viewport meta tag for mobile browsers
add_theme_support( 'genesis-responsive-viewport' );

//* Add support for custom background
add_theme_support( 'custom-background' );

// Add support for custom header
add_theme_support( 'genesis-custom-header', array(
	'width' => 688, //1152 original
	'height' => 166 //120 original
) );


function clickable_header() {
    ?>
    <!--<div id="titleAndDescription"><h1><a href="<?php echo get_bloginfo ( 'url' ); ?>"><?php echo get_bloginfo ( 'name' ); ?></a></h1>-->
    <!--<p><?php echo get_bloginfo ( 'description' ); ?></p></div>-->
    <a href="http://laptopbatteryguide.com/"><img src="http://laptopbatteryguide.com/laptop_battery_guide_banner.png"></a>
    <?php
    }

add_action('genesis_site_title','clickable_header' );
remove_action( 'genesis_site_title', 'genesis_seo_site_title' );
remove_action( 'genesis_site_description', 'genesis_seo_site_description' );

//* Add support for 3-column footer widgets
add_theme_support( 'genesis-footer-widgets', 3 );


/**********************************
 *
 * Replace Header Site Title with Inline Logo
 *
 * Fixes Genesis bug - when using static front page and blog page (admin reading settings) Home page is <p> tag and Blog page is <h1> tag
 *
 * Replaces "is_home" with "is_front_page" to correctly display Home page wit <h1> tag and Blog page with <p> tag
 *
 * @author AlphaBlossom / Tony Eppright
 * @link http://www.alphablossom.com/a-better-wordpress-genesis-responsive-logo-header/
 *
 * @edited by Sridhar Katakam
 * @link http://www.sridharkatakam.com/use-inline-logo-instead-background-image-genesis/
 *
************************************/

add_filter( 'genesis_seo_title', 'custom_header_inline_logo', 10, 3 );
function custom_header_inline_logo( $title, $inside, $wrap ) {
 
	$logo = '<img src="' . get_stylesheet_directory_uri() . '/images/logo.png" alt="' . esc_attr( get_bloginfo( 'name' ) ) . '" title="' . esc_attr( get_bloginfo( 'name' ) ) . '" width="300" height="60" />';
 
	$inside = sprintf( '<a href="%s" title="%s">%s</a>', trailingslashit( home_url() ), esc_attr( get_bloginfo( 'name' ) ), $logo );
 
	// Determine which wrapping tags to use - changed is_home to is_front_page to fix Genesis bug
	$wrap = is_front_page() && 'title' === genesis_get_seo_option( 'home_h1_on' ) ? 'h1' : 'p';
 
	// A little fallback, in case an SEO plugin is active - changed is_home to is_front_page to fix Genesis bug
	$wrap = is_front_page() && ! genesis_get_seo_option( 'home_h1_on' ) ? 'h1' : $wrap;
 
	// And finally, $wrap in h1 if HTML5 & semantic headings enabled
	$wrap = genesis_html5() && genesis_get_seo_option( 'semantic_headings' ) ? 'h1' : $wrap;
 
	return sprintf( '<%1$s %2$s>%3$s</%1$s>', $wrap, genesis_attr( 'site-title' ), $inside );
 
}

// Remove the site description
//remove_action( 'genesis_site_description', 'genesis_seo_site_description' );

// Enqueue scripts and styles
add_action( 'wp_enqueue_scripts', 'custom_scripts_styles_mobile_responsive' );
function custom_scripts_styles_mobile_responsive() {

	wp_enqueue_script( 'responsive-menu', get_stylesheet_directory_uri() . '/js/responsive-menu.js', array( 'jquery' ), '1.0.0', true );
	wp_enqueue_style( 'dashicons' );
}

add_action('genesis_site_title','clickable_header' );
remove_action( 'genesis_site_title', 'genesis_seo_site_title' );
remove_action( 'genesis_site_description', 'genesis_seo_site_description' );

// Customize the previous page link
add_filter ( 'genesis_prev_link_text' , 'sp_previous_page_link' );
function sp_previous_page_link ( $text ) {
	return g_ent( '&laquo; ' ) . __( 'Previous Page', CHILD_DOMAIN );
}

// Customize the next page link
add_filter ( 'genesis_next_link_text' , 'sp_next_page_link' );
function sp_next_page_link ( $text ) {
	return __( 'Next Page', CHILD_DOMAIN ) . g_ent( ' &raquo; ' );
}

/**
 * Remove Genesis Page Templates
 *
 * @author Bill Erickson
 * @link http://www.billerickson.net/remove-genesis-page-templates
 *
 * @param array $page_templates
 * @return array
 */
function be_remove_genesis_page_templates( $page_templates ) {
	unset( $page_templates['page_archive.php'] );
	unset( $page_templates['page_blog.php'] );
	return $page_templates;
}
add_filter( 'theme_page_templates', 'be_remove_genesis_page_templates' );

/*Use excerpts in category view */
add_action( 'genesis_before', 'child_conditional_actions' );
function child_conditional_actions() {
    if( is_category() ) {
        //put your actions here
        remove_action( 'genesis_post_content', 'genesis_do_post_content' );
		remove_action( 'genesis_before_post_content', 'genesis_post_info' );
        /*remove_action( 'genesis_post_content', 'genesis_do_post_image' );*/
        add_action( 'genesis_post_content', 'the_excerpt' );
 
    }
}

//Add this to the bottom of your functions.php file
//Must be using Genesis framework for WordPress

//This code will change your category pages to contain a simple list of your post titles as links

add_action( 'pre_get_posts', 'mjg_show_titles_only_category_pages' );
function mjg_show_titles_only_category_pages( $query ) {
 
	if( $query->is_main_query() && $query->is_category() ) {
		$query->set( 'orderby', 'title' );
		$query->set( 'order', 'ASC' );
		
		remove_action( 'genesis_loop', 'genesis_do_loop' );
		add_action( 'genesis_loop', 'mjg_custom_loop' );
	}
}
 
function mjg_custom_loop() {
 
 	echo '<ul class="category-post-title-list">';
	while (have_posts()) : the_post();
		?>
		<li><a href="<?php the_permalink() ?>"><?php the_title() ?></a></li>
	<?php
	endwhile;
}