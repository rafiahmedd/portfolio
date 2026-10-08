<?php
/**
 * Rafi Ahmed theme.
 */

const RAFI_FONTS = 'https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Space+Grotesk:wght@400;500;600;700&display=swap';

add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_style( 'rafi-fonts', RAFI_FONTS, array(), null );
	wp_enqueue_style( 'rafi', get_stylesheet_uri(), array(), filemtime( get_theme_file_path( 'style.css' ) ) );
	wp_enqueue_script( 'rafi', get_theme_file_uri( 'assets/theme.js' ), array(), filemtime( get_theme_file_path( 'assets/theme.js' ) ), array( 'strategy' => 'defer', 'in_footer' => true ) );
} );

// Custom cursor and scroll reveals only apply once JS is known to run.
add_action( 'wp_head', function () {
	echo "<script>document.documentElement.classList.add('js')</script>\n";
}, 0 );

add_action( 'after_setup_theme', function () {
	add_editor_style( array( RAFI_FONTS, 'style.css' ) );
} );

// First activation: create Home, Blog and Contact pages and point the reading settings at them.
add_action( 'after_switch_theme', function () {
	if ( get_option( 'page_for_posts' ) ) {
		return;
	}
	$content = array(
		'contact' => "<!-- wp:paragraph {\"className\":\"big-copy\"} -->\n<p class=\"big-copy\">Have a problem worth solving? <em>Tell me about it.</em></p>\n<!-- /wp:paragraph -->\n\n<!-- wp:paragraph -->\n<p>Email <a href=\"mailto:hello.rafiahmed@proton.me\">hello.rafiahmed@proton.me</a> or use the form below.</p>\n<!-- /wp:paragraph -->",
	);
	$ids     = array();
	foreach ( array( 'home' => 'Home', 'blog' => 'Blog', 'contact' => 'Contact' ) as $slug => $title ) {
		$page         = get_page_by_path( $slug );
		$ids[ $slug ] = $page ? $page->ID : wp_insert_post(
			array(
				'post_type'    => 'page',
				'post_status'  => 'publish',
				'post_title'   => $title,
				'post_name'    => $slug,
				'post_content' => $content[ $slug ] ?? '',
			)
		);
	}
	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $ids['home'] );
	update_option( 'page_for_posts', $ids['blog'] );
	if ( ! get_option( 'blogdescription' ) ) {
		update_option( 'blogdescription', 'Creator / Software Engineer' );
	}
} );

// An SEO plugin owns the head tags and schema when one is active.
if ( ! defined( 'WPSEO_VERSION' ) && ! defined( 'RANK_MATH_VERSION' ) && ! defined( 'AIOSEO_VERSION' ) && ! defined( 'SEOPRESS_VERSION' ) ) {
	require __DIR__ . '/inc/seo.php';
}
