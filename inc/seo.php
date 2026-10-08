<?php
/**
 * SEO / AEO / GEO: meta description, canonical, Open Graph, JSON-LD and /llms.txt.
 * Only loaded when no SEO plugin is active (see functions.php).
 */

function rafi_plain( $html ) {
	return trim( preg_replace( '/\s+/', ' ', html_entity_decode( wp_strip_all_tags( $html ), ENT_QUOTES, 'UTF-8' ) ) );
}

// ponytail: single-author site, so every post is attributed to this one Person. Read from the post author if that changes.
function rafi_person() {
	return array(
		'@type'      => 'Person',
		'@id'        => home_url( '/#person' ),
		'name'       => 'Rafi Ahmed',
		'url'        => home_url( '/' ),
		'image'      => get_theme_file_uri( 'rafi.jpg' ),
		'jobTitle'   => 'Software Engineer',
		'knowsAbout' => array( 'WordPress', 'PHP', 'JavaScript', 'React', 'Laravel' ),
		'sameAs'     => array(
			'https://github.com/rafiahmedd',
			'https://www.linkedin.com/in/rafiahmedd',
			'https://x.com/rafiahmedd',
		),
	);
}

function rafi_description() {
	$text = '';
	$id   = get_queried_object_id();
	if ( $id && ( is_singular() || is_home() ) ) {
		$text = get_the_excerpt( $id );
	} elseif ( is_category() || is_tag() || is_tax() ) {
		$text = term_description();
	}
	$text = rafi_plain( $text );
	if ( ! $text && is_home() && ! is_front_page() ) {
		$text = 'Notes on WordPress, PHP, JavaScript and shipping products, by Rafi Ahmed.';
	}
	return $text ?: get_bloginfo( 'description' );
}

// Details blocks whose summary is a question become FAQPage entries.
// ponytail: top-level blocks only; walk innerBlocks if FAQs ever get nested in groups/columns.
function rafi_faq( $post ) {
	$faq = array();
	foreach ( parse_blocks( $post->post_content ) as $block ) {
		if ( 'core/details' !== $block['blockName'] || ! preg_match( '#<summary>(.*?)</summary>#s', $block['innerHTML'], $m ) ) {
			continue;
		}
		$question = rafi_plain( $m[1] );
		if ( '?' !== substr( $question, -1 ) ) {
			continue;
		}
		$faq[] = array(
			'@type'          => 'Question',
			'name'           => $question,
			'acceptedAnswer' => array(
				'@type' => 'Answer',
				'text'  => rafi_plain( implode( ' ', array_map( 'render_block', $block['innerBlocks'] ) ) ),
			),
		);
	}
	return $faq;
}

add_filter( 'wp_robots', function ( $robots ) {
	return $robots + array( 'max-snippet' => '-1', 'max-video-preview' => '-1' );
} );

add_action( 'wp_head', function () {
	if ( is_404() || is_search() ) {
		return;
	}

	$post    = is_singular() ? get_queried_object() : null;
	$is_post = is_singular( 'post' );
	$url     = $post ? get_permalink( $post ) : user_trailingslashit( home_url( $GLOBALS['wp']->request ) );
	$title   = rafi_plain( wp_get_document_title() );
	$desc    = rafi_description();
	$image   = $post && has_post_thumbnail( $post ) ? get_the_post_thumbnail_url( $post, 'full' ) : get_theme_file_uri( 'rafi.jpg' );
	$lang    = get_bloginfo( 'language' );

	$og = array(
		'og:type'        => $is_post ? 'article' : 'website',
		'og:title'       => $title,
		'og:description' => $desc,
		'og:url'         => $url,
		'og:site_name'   => get_bloginfo( 'name' ),
		'og:image'       => $image,
		'og:locale'      => get_locale(),
	);
	if ( $is_post ) {
		$og['article:published_time'] = get_the_date( 'c', $post );
		$og['article:modified_time']  = get_the_modified_date( 'c', $post );
	}

	printf( "<meta name=\"description\" content=\"%s\">\n", esc_attr( $desc ) );
	if ( ! $post ) { // Core already prints the canonical for singular views.
		printf( "<link rel=\"canonical\" href=\"%s\">\n", esc_url( $url ) );
	}
	foreach ( $og as $property => $content ) {
		printf( "<meta property=\"%s\" content=\"%s\">\n", esc_attr( $property ), esc_attr( $content ) );
	}
	echo "<meta name=\"twitter:card\" content=\"summary_large_image\">\n<meta name=\"twitter:creator\" content=\"@rafiahmedd\">\n";

	$me    = array( '@id' => home_url( '/#person' ) );
	$site  = array( '@id' => home_url( '/#website' ) );
	$graph = array(
		rafi_person(),
		$site + array(
			'@type'       => 'WebSite',
			'url'         => home_url( '/' ),
			'name'        => get_bloginfo( 'name' ),
			'description' => get_bloginfo( 'description' ),
			'publisher'   => $me,
			'inLanguage'  => $lang,
		),
	);
	$page  = array(
		'@type'       => 'WebPage',
		'@id'         => $url,
		'url'         => $url,
		'name'        => $title,
		'description' => $desc,
		'isPartOf'    => $site,
		'inLanguage'  => $lang,
	);

	if ( is_front_page() ) {
		$page['@type']      = 'ProfilePage';
		$page['mainEntity'] = $me;
	} elseif ( is_home() ) {
		$page['@type']    = 'Blog';
		$page['author']   = $me;
		$page['blogPost'] = array_map(
			fn( $p ) => array(
				'@type'         => 'BlogPosting',
				'headline'      => rafi_plain( get_the_title( $p ) ),
				'url'           => get_permalink( $p ),
				'datePublished' => get_the_date( 'c', $p ),
				'author'        => $me,
			),
			$GLOBALS['wp_query']->posts
		);
	} elseif ( $is_post ) {
		$headline = rafi_plain( get_the_title( $post ) );
		$page     = array(
			'@type'            => 'BlogPosting',
			'@id'              => $url . '#article',
			'headline'         => $headline,
			'description'      => $desc,
			'url'              => $url,
			'mainEntityOfPage' => $url,
			'datePublished'    => get_the_date( 'c', $post ),
			'dateModified'     => get_the_modified_date( 'c', $post ),
			'author'           => $me,
			'publisher'        => $me,
			'image'            => $image,
			'articleSection'   => wp_list_pluck( get_the_category( $post->ID ), 'name' ),
			'keywords'         => wp_list_pluck( get_the_tags( $post->ID ) ?: array(), 'name' ),
			'wordCount'        => str_word_count( wp_strip_all_tags( $post->post_content ) ),
			'isPartOf'         => $site,
			'inLanguage'       => $lang,
		);
		$crumbs   = array( 'Home' => home_url( '/' ), 'Blog' => get_post_type_archive_link( 'post' ), $headline => $url );
		$graph[]  = array(
			'@type'           => 'BreadcrumbList',
			'itemListElement' => array_map(
				fn( $name, $i ) => array( '@type' => 'ListItem', 'position' => $i + 1, 'name' => $name, 'item' => $crumbs[ $name ] ),
				array_keys( $crumbs ),
				array_keys( array_keys( $crumbs ) )
			),
		);
		$faq      = rafi_faq( $post );
		if ( $faq ) {
			$graph[] = array( '@type' => 'FAQPage', '@id' => $url . '#faq', 'mainEntity' => $faq );
		}
	} elseif ( is_page( 'contact' ) ) {
		$page['@type'] = 'ContactPage';
	} elseif ( is_archive() ) {
		$page['@type'] = 'CollectionPage';
	}
	$graph[] = $page;

	// Default flags escape "/" so post content can never close the script tag.
	echo '<script type="application/ld+json">' . wp_json_encode( array( '@context' => 'https://schema.org', '@graph' => $graph ) ) . "</script>\n";
}, 1 );

// /llms.txt: a plain-text map of the site for AI crawlers and answer engines.
// ponytail: latest 100 posts, built per request. Paginate or cache if the blog outgrows that.
add_action( 'init', function () {
	$path = fn( $url ) => trim( (string) wp_parse_url( $url, PHP_URL_PATH ), '/' );
	if ( $path( home_url( '/llms.txt' ) ) !== $path( $_SERVER['REQUEST_URI'] ?? '' ) ) {
		return;
	}
	header( 'Content-Type: text/plain; charset=utf-8' );
	printf( "# %s\n\n> %s\n\n", get_bloginfo( 'name' ), get_bloginfo( 'description' ) );
	printf( "## Pages\n\n- [Home](%s)\n- [Blog](%s)\n- [Contact](%s)\n\n## Posts\n\n", home_url( '/' ), get_post_type_archive_link( 'post' ), home_url( '/contact/' ) );
	foreach ( get_posts( array( 'numberposts' => 100 ) ) as $p ) {
		printf( "- [%s](%s): %s\n", rafi_plain( get_the_title( $p ) ), get_permalink( $p ), rafi_plain( get_the_excerpt( $p ) ) );
	}
	exit;
} );
