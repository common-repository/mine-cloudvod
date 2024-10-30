<?php
/**
 * 顶级标签过滤
 */

$tags = get_terms( [
    'taxonomy' => 'course-tag',
    'parent' => 0,
] );
$args = $wp_query->query_vars;
do_action( 'mcv_before_filter_item_tag', $tags );
?>

<div class="selector-line">
    <div class="selector-title">标签：</div>
    <div class="selector-main" style="height:auto">
        <div class="kc-tag-group">
            <a href="<?php echo mcv_lms_filter_permalink( $args['course-category']??'all', 'all', $args['mcv-lvl']??'all', $args['course-mod']??'all' ); ?>" class="kc-tag <?php echo ( !isset($args['course-tag']) || (isset($args['course-tag']) && $args['course-tag'] == '')?'is-active':'' );?>">全部</a>
            <?php 
            if( is_array( $tags ) ): 
                foreach( $tags as $tag ):
                    $is_active = '';
                    if( $tag->slug == ( $args['course-tag']??'' ) ) $is_active = ' is-active';
            ?>
                <a href="<?php echo get_term_link( $tag ); ?>" class="kc-tag<?php echo $is_active; ?>"><?php echo $tag->name; ?></a>
            <?php
                endforeach;
            endif;
            ?>
        </div>
        <div class="selector-aside"></div>
    </div>
</div>

<?php
do_action( 'mcv_after_filter_item_tag', $tags );
?>