<?php
namespace MineCloudvod\Ability;

use MineCloudvod\MineCloudVod;

class Filters
{
    public function __construct(){
        add_filter( 'post_thumbnail_id',    [ $this, 'mcv_post_thumbnail_id'], 10, 2);
        add_filter( 'page_template',        [ $this, 'mcv_page_template'] );
        add_filter( 'display_post_states',  [ $this, 'mcv_add_post_state'], 10, 2 );
        add_action( 'init',                 [ $this, 'mcv_flush_permalinks'] );
        add_action( 'load-toplevel_page_mcv-options',       [$this, 'mcv_upgraded'] );
        add_action( 'csf_mcv_settings_save_after', 'mcv_activation_hook' );
        add_action( 'get_avatar_url', [ $this, 'mcv_avatar_url'], 10, 3 );
        add_action( 'init', [ $this, 'mcv_global_vars' ] );
        add_action( 'wp_enqueue_scripts', [ $this, 'remove_themes_assets' ], 99 );
        add_filter('mcv_lms_is_enrolled', array($this, 'mcv_admin_can_learn'));
    }
    public function mcv_admin_can_learn($canplay){
        if( current_user_can( 'manage_options' ) ){
            if( !isset( MINECLOUDVOD_SETTINGS['mcv_lms_course']['admin_enrolled'] )
                || MINECLOUDVOD_SETTINGS['mcv_lms_course']['admin_enrolled'] == '1'
            )
                return 1;
        }
        return $canplay;
    }

    public function remove_themes_assets(){
        global $post;
        if( $post && $post->post_type == MINECLOUDVOD_LMS['lesson_post_type'] ){
            global $wp_styles, $wp_scripts;
            foreach( $wp_styles->registered as $key => $value ){
                $src = $value->src;
                if( strpos($src, 'wp-content/themes/') > 0 ){
                    unset( $wp_styles->registered[$key] );
                }
            }
            foreach( $wp_scripts->registered as $key => $value ){
                $src = $value->src;
                if( $key != 'jquery' && strpos($src, 'wp-content/themes/') > 0 ){
                    unset( $wp_scripts->registered[$key] );
                }
            }
        }
    }

    public function mcv_global_vars(){
        //全局js变量
        wp_localize_script( 'mcv_localize_script', 'mcv_global', apply_filters( 'mcv_global_vars',  [
            'islogin' => is_user_logged_in(),
        ] ) );
        wp_enqueue_script( 'mcv_localize_script' );
        if( isset( MINECLOUDVOD_SETTINGS['hideAdminBar'] ) && MINECLOUDVOD_SETTINGS['hideAdminBar'] ){
            show_admin_bar( false );
		    remove_action('wp_head', '_admin_bar_bump_cb');
        }
    }

    public function mcv_avatar_url( $url, $id_or_email, $args ){
        if( is_user_logged_in() ){
            $uid = get_current_user_id();
            $mcv_avatar = get_user_meta( $uid, 'mcv_avatar', true );
            if( $mcv_avatar ){
                $url = $mcv_avatar;
            }
        }
        return $url;
    }

    public function mcv_upgraded( $upgrader ) {
        global $mcv_classes;
        $Addons = $mcv_classes->Addons;
        $is_new = false;
        if( isset(MINECLOUDVOD_SETTINGS['mcv_cloudvod']['aliyun']) && MINECLOUDVOD_SETTINGS['mcv_cloudvod']['aliyun'] ){
            $Addons->active_addons('aliyun');
            $is_new = true;
        }
        if( isset(MINECLOUDVOD_SETTINGS['mcv_cloudvod']['qcloud']) && MINECLOUDVOD_SETTINGS['mcv_cloudvod']['qcloud'] ){
            $Addons->active_addons('qcloud');
            $is_new = true;
        }
        if( isset(MINECLOUDVOD_SETTINGS['mcv_cloudvod']['dogecloud']) && MINECLOUDVOD_SETTINGS['mcv_cloudvod']['dogecloud'] ){
            $Addons->active_addons('doge');
            $is_new = true;
        }
        
        if( isset(MINECLOUDVOD_SETTINGS['mcv_integrations']['b2'])  && MINECLOUDVOD_SETTINGS['mcv_integrations']['b2'] ){
            $Addons->active_addons('i_b2');
            $is_new = true;
        }
        if( isset(MINECLOUDVOD_SETTINGS['mcv_integrations']['tutor']) && MINECLOUDVOD_SETTINGS['mcv_integrations']['tutor'] ){
            $Addons->active_addons('i_tutor');
            $is_new = true;
        }
        if( isset(MINECLOUDVOD_SETTINGS['mcv_integrations']['elementor']) && MINECLOUDVOD_SETTINGS['mcv_integrations']['elementor'] ){
            $Addons->active_addons('i_elementor');
            $is_new = true;
        }
        if( isset(MINECLOUDVOD_SETTINGS['mcv_integrations']['ri']) && MINECLOUDVOD_SETTINGS['mcv_integrations']['ri'] ){
            $Addons->active_addons('i_ripro');
            $is_new = true;
        }
        if( isset(MINECLOUDVOD_SETTINGS['mcv_integrations']['ceomax']) && MINECLOUDVOD_SETTINGS['mcv_integrations']['ceomax'] ){
            $Addons->active_addons('i_ceomax');
            $is_new = true;
        }
        if( isset(MINECLOUDVOD_SETTINGS['mcv_integrations']['masterstudy']) && MINECLOUDVOD_SETTINGS['mcv_integrations']['masterstudy'] ){
            $Addons->active_addons('i_masterstudy');
            $is_new = true;
        }
        if( $is_new ){
            $setting = MINECLOUDVOD_SETTINGS;
            unset($setting['mcv_integrations']);
            unset($setting['mcv_cloudvod']);
            update_option('mcv_settings', $setting);
        }
    }

    public function mcv_add_post_state( $post_states, $post ) {
        if ( mine_get_page_id_by_slug( 'mcv-aliplayer-note' ) === $post->ID ) {
            $post_states['mcv_aliplayer_note'] = __( 'Mine Aliplayer Note Page', 'mine-cloudvod' );
        }
        if ( mine_get_page_id_by_slug( 'mcv-checkout' ) === $post->ID ) {
            $post_states['mcv_checkout'] = __( 'Mine Checkout Page', 'mine-cloudvod' );
        }
        if ( mine_get_page_id_by_slug( 'mcv-order-list' ) === $post->ID ) {
            $post_states['mcv_order_list'] = __( 'Mine Order List Page', 'mine-cloudvod' );
        }
        if ( mine_get_page_id_by_slug( 'mcv-my-courses' ) === $post->ID ) {
            $post_states['mcv_my_courses'] = __( 'User\'s courses', 'mine-cloudvod' );
        }
        if ( mine_get_page_id_by_slug( 'mcv-favorites' ) === $post->ID ) {
            $post_states['mcv_favorites'] = __( 'Mine Favorite Courses', 'mine-cloudvod' );
        }
        if ( mine_get_page_id_by_slug( 'mcv-index' ) === $post->ID ) {
            $post_states['mcv_index'] = __( 'MCV Index', 'mine-cloudvod' );
        }
        return $post_states;
    }

    public function mcv_page_template( $page_template ){
        $is_fse = mcv_current_theme_is_fse_theme();
        if ( is_page( 'upload-aliyunvod' ) ) {
            $page_template = MINECLOUDVOD_PATH . '/templates/vod/upload-aliyunvod.php';
        }
        if ( is_page( 'mcv-aliplayer-note' ) ) {
            $page_template = MINECLOUDVOD_PATH . '/templates/vod/note.php';
        }
        if ( !$is_fse && is_page( 'mcv-checkout' ) ) {
            $page_template = mcv_lms_get_template_path('checkout');
        }
        if ( !$is_fse && is_page( 'mcv-order-list' ) ) {
            $page_template = mcv_lms_get_template_path('order-list');
        }
        if ( !$is_fse && is_page( 'mcv-my-courses' ) ) {
            $page_template = mcv_lms_get_template_path('user-courses');
        }
        if ( !$is_fse && is_page( 'mcv-favorites' ) ) {
            $page_template = mcv_lms_get_template_path('favorites');
        }
        if ( !$is_fse && is_page( 'mcv-index' ) ) {
            $page_template = mcv_lms_get_template_path('index');
        }
        return $page_template;
    }

    /**
     * 如果文章没有特色图像,则使用播放器的封面图像
     */
    public function mcv_post_thumbnail_id($thumbnail_id, $post){
        if( !$thumbnail_id && has_block('mine-cloudvod/aliyun-vod', $post) ){
            $post_id = is_numeric( $post ) ? $post : $post->ID;
            if( !$post_id ) return $thumbnail_id;
            $_mcv_alivod_snapshot = get_post_meta($post_id, '_mcv_alivod_snapshot', true);
           
            return $_mcv_alivod_snapshot;
        }
        return $thumbnail_id;
    }

    public function mcv_flush_permalinks(){
        if( !get_option('_mcv_permalinks_flushed') ) {
            flush_rewrite_rules(false);
            update_option('_mcv_permalinks_flushed', 1);
        }
    }
}
