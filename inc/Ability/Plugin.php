<?php
namespace MineCloudvod\Ability;

class Plugin
{
    protected $plugin_basename = "mine-cloudvod/mine-cloudvod.php";
    public function __construct()
    {
        add_filter( 'plugin_action_links_' . $this->plugin_basename, array( $this, 'plugin_action_links' ) );
        add_filter( 'plugin_row_meta', array($this, 'plugin_row_meta'), 10, 2 );
        add_action( 'admin_notices', array($this, 'notice_mcv_endtime') );
        add_action( 'admin_notices', array($this, 'init_pages') );
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
        add_action( 'wp_ajax_mcv_dismiss_notice', [ $this, 'dismiss_notice' ] );
    }

    public function plugin_action_links($actions){
		$actions['addons'] = '<a href="admin.php?page=mcv-addons">' . __('Add-ons', 'mine-cloudvod') . '</a>';
		$actions['settings'] = '<a href="admin.php?page=mcv-options">' . __('Settings') . '</a>';
		return $actions;
	}

    public function plugin_row_meta($plugin_meta, $plugin_file){

        if ($plugin_file === $this->plugin_basename) {
            $plugin_meta[] = sprintf( '<a href="%s">%s</a>',
                esc_url( 'https://www.zwtt8.com/docs-category/mine-cloudvod/?utm_source=mine_cloudvod&utm_medium=plugins_installation_list&utm_campaign=plugin_docs_link' ),
                __( '<strong style="color: #03bd24">Documentation</strong>', 'mine-cloudvod' )
            );
        }

        return $plugin_meta;
    }

    public function notice_mcv_endtime(){
        global $mcv_classes;
        $endtime = MINECLOUDVOD_SETTINGS['endtime'];
        if( $mcv_classes->Addons->is_addons_actived('aliyun')
         || $mcv_classes->Addons->is_addons_actived('qcloud')
         || $mcv_classes->Addons->is_addons_actived('qiniukodo')
         || $mcv_classes->Addons->is_addons_actived('attachment')
         || $mcv_classes->Addons->is_addons_actived('nextlesson')
         || $mcv_classes->Addons->is_addons_actived('coursereport')
        ){
            if($endtime){
                $endtime = strtotime($endtime);
                if($endtime < time()){
                    $class = 'notice notice-error';
                    $message = sprintf(__( 'Mine CloudVod is expired, please <a href="%s">renew</a> in time.', 'mine-cloudvod'), admin_url('/admin.php?page=mcv-options#tab='.str_replace(' ', '-', strtolower(urlencode(__('Purchase time', 'mine-cloudvod'))))));
                    printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), $message );
                }
                elseif($endtime-time() < 3600*24*10){
                    $class = 'notice notice-warning is-dismissible';
                    $message = sprintf(__( 'Mine CloudVod will expire in %d days, please <a href="%s">renew</a> in time.', 'mine-cloudvod' ), ($endtime-time())/3600/24, admin_url('/admin.php?page=mcv-options#tab='.str_replace(' ', '-', strtolower(urlencode(__('Purchase time', 'mine-cloudvod'))))));
                
                    printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), $message );
                }
            }
        }
    }

    public function enqueue_scripts(){
        wp_enqueue_script( 'mcv_layer' );
        wp_enqueue_script( 'mcv-admin-init' );
        wp_localize_script( 'mcv-admin-init', 'mcv_admin_init', [
            'nonce' => wp_create_nonce( 'mcv-admin-nonce' )
        ] );
    }

    public function dismiss_notice(){
        if ( ! empty( $_REQUEST['key'] ) && ! empty( $_REQUEST['mcv_nonce'] ) ) {
            if ( wp_verify_nonce($_REQUEST['mcv_nonce'], 'mcv-admin-nonce' ) ) {
                $hidden_notices = get_option( '_mcv_hidden_notices', array() );
                if ( ! is_array( $hidden_notices ) ) {
                    $hidden_notices = array();
                }

                $hidden_notices[] = sanitize_key( $_REQUEST['key'] );

                update_option( '_mcv_hidden_notices', $hidden_notices );
                wp_send_json_success();
            } else {
                wp_send_json_error( __( 'Illegal request', 'mine-cloudvod' ) );
            }
        }
    }

    public function init_pages(){
        $key = 'initpages';
        $hidden = get_option( '_mcv_hidden_notices', array() );
        if ( empty( $hidden ) || ! in_array( $key, $hidden ) ) {
            $class = 'mcv-notice notice notice-warning is-dismissible';
            $message = sprintf( __( '%s needs to create several pages (Checkout, Order List, Favorites) to function correctly.', 'mine-cloudvod' ), __('Mine CloudVod', 'mine-cloudvod'));
            $actions = '<a href="javascript:;" class="button button-primary mcv-'. $key .'">'. __('Create', 'mine-cloudvod') .'</a> <a href="javascript:void(0);" class="button-secondary mcv-dismiss">'. __('Cancle', 'mine-cloudvod') .'</a>';
        
            printf( '<div class="%1$s" data-id="'. $key .'"><p>%2$s</p><p>%3$s</p></div>', esc_attr( $class ), $message, $actions );
        }
    }
}
