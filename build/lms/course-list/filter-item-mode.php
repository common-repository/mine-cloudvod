<?php
$modes = MINECLOUDVOD_LMS['access_mode'];
$args = $wp_query->query_vars;
do_action( 'mcv_before_filter_item_mode', $modes );
?>

<div class="selector-line">
    <div class="selector-title"><?php echo __('Access Mode', 'mine-cloudvod'); ?> : </div>
    <div class="selector-main" style="height:auto">
        <div class="kc-tag-group">
            <a href="<?php echo mcv_lms_filter_permalink( $args['course-category']??'all', $args['course-tag']??'all', $args['mcv-lvl']??'all', 'all' ); ?>" class="kc-tag <?php echo ( !isset($args['mcv-mod']) || $args['mcv-mod'] == 'all' ?' is-active' : '' );?>">全部</a>
            <?php 
            if( is_array( $modes ) ): 
                foreach( $modes as $key => $value ):
                    $is_active = '';
                    if( $key == ( $args['mcv-mod']??'' ) ) $is_active = ' is-active';
            ?>
                <a href="<?php echo mcv_lms_filter_permalink( $args['course-category']??'all', $args['course-tag']??'all', $args['mcv-lvl']??'all', $key ); ?>" class="kc-tag<?php echo $is_active; ?>"><?php echo $value; ?></a>
            <?php
                endforeach;
            endif;
            ?>
        </div>
        <div class="selector-aside"></div>
    </div>
</div>

<?php
do_action( 'mcv_after_filter_item_mode', $modes );
?>