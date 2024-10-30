<?php
namespace MineCloudvod\LMS\Blocks;

class Course{
    
    public function __construct()
    {
        add_action( 'init',                     [ $this, 'mcv_register_course_blocks'] );
    }

    public function mcv_register_course_blocks(){
        if( !mcv_current_theme_is_fse_theme() ){
            wp_register_style( 'mcv-global-styles-inline', false );
            wp_enqueue_style( 'mcv-global-styles-inline' );
            wp_add_inline_style( 'mcv-global-styles-inline', 'body{ --wp--style--global--wide-size: '. (MINECLOUDVOD_SETTINGS['mcv_lms_general']['wide_size']??'1200px') .'; }' );
        }
        register_block_type( MINECLOUDVOD_PATH . '/build/lms/course-list/');
        register_block_type( MINECLOUDVOD_PATH . '/build/lms/course-single/');
        register_block_type( MINECLOUDVOD_PATH . '/build/lms/lesson-single/');
        register_block_type( MINECLOUDVOD_PATH . '/build/lms/course-checkout/');
        register_block_type( MINECLOUDVOD_PATH . '/build/lms/order-list/');
        register_block_type( MINECLOUDVOD_PATH . '/build/lms/favorites/');
        register_block_type( MINECLOUDVOD_PATH . '/build/lms/user/');
        register_block_type( MINECLOUDVOD_PATH . '/build/lms/user-courses/');
    }

}
