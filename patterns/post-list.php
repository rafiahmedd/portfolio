<?php
/**
 * Title: Post list
 * Slug: rafi/post-list
 * Inserter: no
 */
?>
<!-- wp:query {"queryId":1,"query":{"inherit":true},"tagName":"section","className":"section"} -->
<section class="wp-block-query section">
<!-- wp:post-template {"className":"timeline"} -->
<!-- wp:group {"className":"job"} -->
<div class="wp-block-group job">
<!-- wp:post-date {"className":"year"} /-->

<!-- wp:group -->
<div class="wp-block-group">
<!-- wp:post-title {"isLink":true} /-->

<!-- wp:post-excerpt {"excerptLength":30} /-->
</div>
<!-- /wp:group -->

<!-- wp:post-terms {"term":"category","className":"company"} /-->
</div>
<!-- /wp:group -->
<!-- /wp:post-template -->

<!-- wp:query-pagination {"className":"pager"} -->
<!-- wp:query-pagination-previous {"label":"← Newer"} /-->

<!-- wp:query-pagination-numbers /-->

<!-- wp:query-pagination-next {"label":"Older →"} /-->
<!-- /wp:query-pagination -->

<!-- wp:query-no-results -->
<!-- wp:paragraph {"className":"big-copy"} -->
<p class="big-copy">Nothing published yet. <em>Soon.</em></p>
<!-- /wp:paragraph -->
<!-- /wp:query-no-results -->
</section>
<!-- /wp:query -->
