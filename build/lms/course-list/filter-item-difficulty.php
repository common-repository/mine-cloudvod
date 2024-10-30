<?php
$difficulties = MINECLOUDVOD_LMS['course_difficulty'];
$args = $wp_query->query_vars;
do_action( 'mcv_before_filter_item_difficulty', $difficulties );
?>

<div class="selector-line">
    <div class="selector-title"><?php echo __( 'Difficulty', 'mine-cloudvod' ); ?> : </div>
    <div class="selector-main" style="height:auto">
        <div class="kc-tag-group">
                <a href="<?php echo mcv_lms_filter_permalink( $args['course-category']??'all', $args['course-tag']??'all', 'all', $args['mcv-mod']??'all' ); ?>" class="kc-tag<?php echo ( !isset($args['mcv-lvl']) || $args['mcv-lvl'] == 'all' ?' is-active' : '' );?>">全部</a>
            <?php 
            if( is_array( $difficulties ) ): 
                foreach( $difficulties as $key => $value ):
                    $is_active = '';
                    if( $key == ( $args['mcv-lvl']??'' ) ) $is_active = ' is-active';
            ?>
                <a href="<?php echo mcv_lms_filter_permalink( $args['course-category']??'all', $args['course-tag']??'all', $key, $args['mcv-mod']??'all' ); ?>" class="kc-tag<?php echo $is_active; ?>"><?php echo $value; ?></a>
            <?php
                endforeach;
            endif;
            ?>
        </div>
        <div class="selector-aside"></div>
    </div>
</div>

<?php
do_action( 'mcv_after_filter_item_difficulty', $difficulties );
?>