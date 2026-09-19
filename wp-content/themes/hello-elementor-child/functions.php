<?php
/**
 * Workfoster child theme bootstrap.
 *
 * @package Workfoster
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'WORKFOSTER_THEME_VERSION', '1.1.0' );

/**
 * Shared functionality.
 *
 * Job listings themselves are handled by WP Job Manager; these files cover the
 * icon set, the extra listing fields, the page shortcodes and the Contact Form
 * 7 integration.
 */
require_once get_stylesheet_directory() . '/inc/class-wf-icons.php';
require_once get_stylesheet_directory() . '/inc/job-fields.php';
require_once get_stylesheet_directory() . '/inc/shortcodes.php';

if ( defined( 'WPCF7_VERSION' ) ) {
	require_once get_stylesheet_directory() . '/inc/forms.php';
}

/**
 * Load parent + child stylesheets.
 *
 * Hello Elementor registers its handles on `wp_enqueue_scripts` at the default
 * priority, so we run later to guarantee the child CSS wins the cascade.
 */
function workfoster_enqueue_assets() {
	$theme_dir = get_stylesheet_directory();
	$theme_uri = get_stylesheet_directory_uri();

	wp_enqueue_style(
		'workfoster-theme',
		$theme_uri . '/assets/css/theme.css',
		array( 'hello-elementor' ),
		workfoster_asset_version( $theme_dir . '/assets/css/theme.css' )
	);

	wp_enqueue_style(
		'workfoster-sections',
		$theme_uri . '/assets/css/sections.css',
		array( 'workfoster-theme' ),
		workfoster_asset_version( $theme_dir . '/assets/css/sections.css' )
	);

	wp_enqueue_script(
		'workfoster-theme',
		$theme_uri . '/assets/js/theme.js',
		array(),
		workfoster_asset_version( $theme_dir . '/assets/js/theme.js' ),
		true
	);
}
add_action( 'wp_enqueue_scripts', 'workfoster_enqueue_assets', 20 );

/**
 * Cache-bust against the file mtime in local/dev, fall back to the theme version.
 *
 * @param string $path Absolute path to the asset.
 * @return string
 */
function workfoster_asset_version( $path ) {
	if ( file_exists( $path ) && ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ) {
		return (string) filemtime( $path );
	}

	return WORKFOSTER_THEME_VERSION;
}

/**
 * Theme supports that Hello Elementor leaves off.
 */
function workfoster_after_setup_theme() {
	add_theme_support( 'align-wide' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );

	register_nav_menus(
		array(
			'workfoster-primary' => esc_html__( 'Primary Menu', 'workfoster' ),
			'workfoster-footer'  => esc_html__( 'Footer Menu', 'workfoster' ),
			'workfoster-legal'   => esc_html__( 'Legal Menu', 'workfoster' ),
		)
	);

	add_image_size( 'workfoster-card', 720, 480, true );
}
add_action( 'after_setup_theme', 'workfoster_after_setup_theme', 20 );

/**
 * Elementor needs to know this theme supports its locations, otherwise the
 * Hello Elementor header/footer render alongside the Header Footer builder.
 */
function workfoster_register_elementor_locations( $manager ) {
	$manager->register_all_core_location();
}
add_action( 'elementor/theme/register_locations', 'workfoster_register_elementor_locations' );

/**
 * Trim the WP head of endpoints this site does not use.
 */
function workfoster_clean_head() {
	remove_action( 'wp_head', 'wp_generator' );
	remove_action( 'wp_head', 'wlwmanifest_link' );
	remove_action( 'wp_head', 'rsd_link' );
	remove_action( 'wp_head', 'wp_shortlink_wp_head' );
}
add_action( 'init', 'workfoster_clean_head' );

/**
 * Search results should never leak the career CPT twice — one clean list is enough.
 *
 * @param WP_Query $query Query instance.
 */
function workfoster_search_post_types( $query ) {
	if ( is_admin() || ! $query->is_main_query() || ! $query->is_search() ) {
		return;
	}

	$query->set( 'post_type', array( 'post', 'page', 'job_listing' ) );
}
add_action( 'pre_get_posts', 'workfoster_search_post_types' );

/**
 * Body classes used by the stylesheet to switch header treatments.
 *
 * @param array $classes Body classes.
 * @return array
 */
function workfoster_body_class( $classes ) {
	if ( is_singular( 'job_listing' ) ) {
		$classes[] = 'wf-career-single';
	}

	if ( is_404() ) {
		$classes[] = 'wf-error-page';
	}

	return $classes;
}
add_filter( 'body_class', 'workfoster_body_class' );

/**
 * Excerpt length tuned for the blog card grid.
 *
 * @return int
 */
function workfoster_excerpt_length() {
	return 24;
}
add_filter( 'excerpt_length', 'workfoster_excerpt_length' );

/**
 * @return string
 */
function workfoster_excerpt_more() {
	return '&hellip;';
}
add_filter( 'excerpt_more', 'workfoster_excerpt_more' );

/**
 * Estimated reading time for a post body.
 *
 * @param string $content Post content.
 * @return string
 */
function workfoster_reading_time( $content ) {
	$words   = str_word_count( wp_strip_all_tags( $content ) );
	$minutes = max( 1, (int) ceil( $words / 220 ) );

	/* translators: %d: reading time in minutes. */
	return sprintf( _n( '%d min read', '%d min read', $minutes, 'workfoster' ), $minutes );
}

/**
 * Two-letter monogram used where a post has no featured image.
 *
 * @param string $title Post title.
 * @return string
 */
function workfoster_post_monogram( $title ) {
	$words = preg_split( '/\s+/', trim( wp_strip_all_tags( $title ) ), -1, PREG_SPLIT_NO_EMPTY );

	if ( ! $words ) {
		return 'WF';
	}

	if ( 1 === count( $words ) ) {
		return strtoupper( mb_substr( $words[0], 0, 2 ) );
	}

	return strtoupper( mb_substr( $words[0], 0, 1 ) . mb_substr( $words[1], 0, 1 ) );
}

/**
 * Drop WP Job Manager's own listing stylesheet.
 *
 * Its card styling (the yellow wash on featured listings) and filter layout are
 * written for the plugin's default markup. This theme overrides those templates
 * and styles the result itself, so the plugin CSS only fights the design.
 * Select2's own stylesheet is left alone — the category dropdown needs it.
 */
function workfoster_dequeue_job_manager_styles() {
	foreach ( array( 'wp-job-manager-frontend', 'wp-job-manager-job-listings' ) as $handle ) {
		wp_dequeue_style( $handle );
		wp_deregister_style( $handle );
	}
}
add_action( 'wp_enqueue_scripts', 'workfoster_dequeue_job_manager_styles', 100 );
