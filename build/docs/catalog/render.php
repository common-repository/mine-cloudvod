<?php
defined( 'ABSPATH' ) || exit;

$catid = $attributes['catid']??0;

global $post;

if( !$catid && $post ){
    $cats = get_the_terms( $post, 'docs_category' );
    if( is_array( $cats ) ){
        foreach( $cats as $cat ){
            if( $cat->parent == 0 ){
                $catid = $cat->term_id;
            }
        }
    }
}
if( !$catid ) return;

$term = get_term( $catid );
// var_dump($term);

$terms = get_terms( [
    'taxonomy' => 'docs_category',
    'parent' => $catid
] );

// var_dump($terms);

wp_enqueue_style( 'wp-block-library' );
wp_enqueue_style( 'mine-cloudvod-docs-catalog-editor-style' );
wp_enqueue_script( 'mine-cloudvod-docs-catalog-editor-script-2' );
?>
<div class="mcv-doc-sider">
    <div class="mcv-doc-title">
        <?php echo $term->name; ?>
    </div>
    <div class="mcv-doc-menu">
        <div class="mcv-doc-menu-inner">
            <?php foreach( $terms as $t ): 
                 $docs = get_posts( [
                    'post_type' => 'mcv_docs',
                    'post_status' => 'publish',
                    'numberposts' => 999,
                    'tax_query' => [
                        [
                            'taxonomy' => 'docs_category',
                            'field'    => 'term_id',
                            'terms'    => $t->term_id
                        ]
                    ],
                    'order' => 'ASC',
                ] );
                $meun_content = '';
                $has_selected = '';
                foreach( $docs as $doc ): 
                    $title = $doc->post_title;
                    $selected = '';
                    if( $post->ID == $doc->ID ){
                        $selected = ' mcv-menu-selected';
                        $has_selected = ' mcv-menu-selected';
                    }
                    $meun_content .= mcv_trim( '<div class="mcv-menu-item'. $selected .'">
                        <span class="mcv-menu-indent"></span>
                        <span class="mcv-menu-item-inner">
                            <a href="'. get_permalink($doc->ID) .'" title="'. $title .'">'. $title .'</a>
                        </span>
                    </div>' );
                endforeach;
            ?>
            <div class="mcv-doc-menu-item<?php echo $has_selected; ?>">
                <div class="mcv-menu-header">
                    <span><?php echo $t->name; ?></span>
                    <span class="mcv-menu-icon-suffix is-open">
                        <svg fill="none" stroke="currentColor" stroke-width="4" width="14" height="14" viewBox="0 0 48 48" aria-hidden="true" focusable="false" class="arco-icon arco-icon-down"><path d="M39.6 17.443 24.043 33 8.487 17.443"></path></svg>
                    </span>
                </div>
                <div class="mcv-menu-content"<?php echo $has_selected?' style="display:block;"':''; ?>>
                    <?php echo $meun_content; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>