<?php 
defined( 'ABSPATH' ) || exit;

/**
 * 删除顶部管理员导航条
 */
show_admin_bar( false );

$course_id = mcv_lms_get_course_id_by_lesson_id( $post->ID );
$access_mode = mcv_lms_get_course_access_mode( $course_id );

//非open未登录，跳转到course页面
if( $access_mode != "open" && !$current_user->exists() ) {
    wp_redirect( get_the_permalink( $course_id ) );
    exit;
}
$course_title = get_the_title( $course_id );

$progress = false;
if( $current_user->exists() ){
    if( !get_user_meta( $current_user->ID, '_mcv_lms_enroll_course_id_'.$course_id, true ) ){
        update_user_meta( $current_user->ID, '_mcv_lms_enroll_course_id_'.$course_id, time() );
    }
    $progress = mcv_lms_get_course_progress( $course_id );
}
    
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <?php wp_head(); ?>
</head>

<body <?php body_class('has-sidebar'); ?>>
    <div class="minelms-root">
        <div class="nk-wrap ">
            <div class="nk-header nk-header-fixed is-light">
                <div class="container-fluid">
                    <div class="nk-header-wrap">
                        <div class="nk-menu-trigger d-xl-none ms-n1">
                            <a href="#" class="nk-nav-toggle nk-quick-nav-icon" data-target="sidebarMenu"><em class="icon ni ni-menu"></em></a>
                        </div>
                        <div class="nk-header-app-name">
                            <div class="nk-header-app-info">
                                <a href="<?php echo get_the_permalink($course_id); ?>"><?php echo $course_title; ?></a>
                            </div>
                        </div>
                        <?php if( $progress ) : 
                                $percent = (int)( $progress['completed'] / $progress['total'] * 100 )
                        ?>
                        <div class="minelms-header-tools nk-header-tools">
                            <div class="progress progress-lg">
                                <div class="progress-bar" style="width: <?php echo $percent; ?>%;"><?php echo $percent; ?>%</div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="nk-sidebar" data-content="sidebarMenu">
                <?php mcv_lms_load_template('elements.accordion'); ?>
            </div>
            <div class="ml-content ">
                <div class="container-fluid wide-lg">
                    <?php 
                    the_content();
                    ?>
                </div>
                <div class="container-fluid wide-lg">
                    <?php $prev_next = mcv_lms_get_previous_next_lesson();?>
                    <ul class="row g-gs">
                        <li class="col-lg-4 ml-text-center text-start pdl-0px">
                            <?php if( $prev_next['prev'] ): ?>
                            <a href="<?php the_permalink( $prev_next['prev'] ); ?>" class="btn btn-white btn-dim btn-outline-primary"><em class="icon ni ni-chevron-left"></em><span><?php _e('Previous Lesson', 'mine-cloudvod') ?></span> </a>
                            <?php endif;?>
                        </li>
                        <li class="col-lg-4 text-center">
                            <?php if( mcv_lms_is_lesson_completed() ): ?>
                            <a href="javascript:;" class="btn btn-success"><em class="icon ni ni-check"></em><?php _e('Completed', 'mine-cloudvod');?></a>
                            <?php else: ?>
                            <a href="javascript:;" class="btn btn-white btn-dim btn-outline-success mcv-mark-as-complete" data-id="<?php the_ID() ?>"><?php _e('Mark As Complete', 'mine-cloudvod');?></a>
                            <?php endif;?>
                        </li>
                        <li class="col-lg-4 ml-text-center text-end pdr-0px">
                            <?php if( $prev_next['next'] ): ?>
                            <a href="<?php the_permalink( $prev_next['next'] ); ?>" class="btn btn-white btn-dim btn-outline-primary"><span><?php _e('Next Lesson', 'mine-cloudvod'); ?></span><em class="icon ni ni-chevron-right"></em></a>
                            <?php endif;?>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <?php wp_footer();?>
</body>
</html>