<?php 
get_header();
if( $wp_query->is_tax($taxonomy) ){
    $post = array_pop($wp_query->posts);
}

echo do_blocks( '
<!-- wp:columns {"isStackedOnMobile":false} -->
<div class="wp-block-columns is-not-stacked-on-mobile"><!-- wp:column {"width":"240px"} -->
<div class="wp-block-column" style="flex-basis:240px;flex-shrink:0;flex-grow:0;"><!-- wp:mine-cloudvod/docs-catalog {"catid":777} /--></div>
<!-- /wp:column -->

<!-- wp:column {"width":"","style":{"spacing":{"padding":{"top":"0","right":"0","bottom":"0","left":"0"}}},"className":"mcv-docs-content","layout":{"type":"constrained"}} -->
<div class="wp-block-column mcv-docs-content" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><!-- wp:group {"style":{"border":{"radius":"6px"}},"backgroundColor":"white","layout":{"type":"default"}} -->
<div class="wp-block-group has-white-background-color has-background" style="border-radius:6px"><!-- wp:post-title {"style":{"typography":{"fontStyle":"normal","fontWeight":"600","textTransform":"capitalize"},"spacing":{"padding":{"top":"0.67rem","right":"0","bottom":"0","left":"0"}}}} /-->

<!-- wp:group {"style":{"spacing":{"blockGap":"0","padding":{"top":"var:preset|spacing|50"}}},"layout":{"type":"default"}} -->
<div class="wp-block-group" style="padding-top:var(--wp--preset--spacing--50, 1.5rem)"><!-- wp:post-content {"layout":{"type":"default"}} /--></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:column -->

<!-- wp:column {"width":"240px","className":"mcv-anchor","layout":{"type":"constrained","justifyContent":"right"}} -->
<div class="wp-block-column mcv-anchor" style="flex-basis:240px;flex-shrink:0;flex-grow:0;"><!-- wp:paragraph -->
<p></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->
' );
// echo '</div>';
get_footer();
wp_footer();
?>