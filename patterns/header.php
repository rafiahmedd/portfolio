<?php
/**
 * Title: Header
 * Slug: rafi/header
 * Inserter: no
 */
?>
<!-- wp:html -->
<a class="logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">RAFI AHMED.</a>
<nav class="nav" aria-label="Primary">
  <a href="<?php echo esc_url( home_url( '/#work' ) ); ?>">work</a><a href="<?php echo esc_url( home_url( '/#experience' ) ); ?>">experience</a><a href="<?php echo esc_url( get_post_type_archive_link( 'post' ) ); ?>">blog</a><a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">contact</a>
</nav>
<!-- /wp:html -->
