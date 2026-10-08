<?php
/**
 * Title: Breadcrumbs
 * Slug: rafi/crumbs
 * Inserter: no
 */
?>
<!-- wp:html -->
<nav class="kicker" aria-label="Breadcrumb"><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a> / <a href="<?php echo esc_url( get_post_type_archive_link( 'post' ) ); ?>">Blog</a></nav>
<!-- /wp:html -->
