<?php
/**
 * 顶级分类过滤
 */
$parent = 0;
$categories = get_terms( [
    'taxonomy' => 'course-category',
    'parent' => $parent,
] );
$args = $wp_query->query_vars;
do_action( 'mcv_before_filter_item_category', $categories );
?>

<div class="selector-line">
    <div class="selector-title">分类：</div>
    <div class="selector-main" style="height:auto">
        <div class="kc-tag-group">
                <a href="<?php echo mcv_lms_filter_permalink( 'all', $args['course-tag']??'all', $args['mcv-lvl']??'all', $args['mcv-mod']??'all' ); ?>" class="kc-tag <?php echo ( !isset($args['course-category']) || (isset($args['course-category']) && $args['course-category'] == '')?'is-active':'' );?>">全部</a>
            <?php 
            if( is_array( $categories ) ): 
                foreach( $categories as $category ):
                    $is_active = '';
                    if( $category->slug == ( $args['course-category']??'' ) ) $is_active = ' is-active';
            ?>
                <a href="<?php echo mcv_lms_filter_permalink( $category->slug, $args['course-tag']??'all', $args['mcv-lvl']??'all', $args['mcv-mod']??'all' ); ?>" class="kc-tag<?php echo $is_active; ?>"><?php echo $category->name; ?></a>
            <?php
                endforeach;
            endif;
            ?>
        </div>
        <div class="selector-aside"></div>
    </div>
</div>

<?php
do_action( 'mcv_after_filter_item_category', $categories );
?>