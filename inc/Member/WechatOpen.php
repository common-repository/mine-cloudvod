<?php
namespace MineCloudvod\Member;
use MineCloudvod\Libs\QRcode;

class WechatOpen {
    private $is_wechat, $is_mobile, $murl, $login_id = 'wechat_open';
    public function __construct( ) {

        add_action('rest_api_init', [$this, 'register_routes']);

        add_filter( 'mcv_user_login_options', [ $this, 'login_options' ] );

        add_filter( 'mcv_login_script', [ $this, 'wechat_script' ], 10, 2 );
        //处理微信客户端登录/注册
        add_action('init', [$this, 'handle_wx_client']);
    }

    public function handle_wx_client(){
        if( !is_user_logged_in() && isset( $_GET['mcv_wx_client'] ) && $_GET['mcv_wx_client'] == '1' && isset( $_GET['code'] ) ){
            $code = $_GET['code'];

            $wechat_open = MINECLOUDVOD_SETTINGS['uc_login3'][$this->login_id] ?? [];
            $appid = $wechat_open['MpAppID']??'';
            $secret = $wechat_open['MpAppSecret']??'';

            $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$appid.'&secret='.$secret.'&code='.$code.'&grant_type=authorization_code';
            $response = wp_remote_get( $url );
            if( is_wp_error( $response ) ){
                return __('No response, try later!', 'mine-cloudvod');
            }
            $data = json_decode($response['body'], true);
            global $wpdb;
            $uid = 0;
            if( isset( $data['unionid'] ) && $data['unionid'] ){
                $uid = $wpdb->get_var("SELECT ID FROM $wpdb->users LEFT JOIN $wpdb->usermeta ON $wpdb->users.ID=$wpdb->usermeta.user_id WHERE $wpdb->usermeta.meta_key='mcv_wechat_unionid' and $wpdb->usermeta.meta_value='".esc_sql($data['unionid'])."';");
            }
            elseif( isset( $data['openid'] ) && $data['openid'] ){
                $uid = $wpdb->get_var("SELECT ID FROM $wpdb->users LEFT JOIN $wpdb->usermeta ON $wpdb->users.ID=$wpdb->usermeta.user_id WHERE $wpdb->usermeta.meta_key='mcv_wechat_openid' and $wpdb->usermeta.meta_value='".esc_sql($data['openid'])."';");
            }
            else{
                return 'No response, try later!';
            }
            
            if( $uid ){
                $user = get_user_by('id', $uid);
                wp_set_current_user( $uid );
                wp_set_auth_cookie($uid,true,is_ssl());
                do_action( 'wp_login', $user->user_login, $user );
            }else{
                $info_url = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$data['access_token'].'&openid='.$data['openid'].'&lang=zh_CN';
                $info_result = wp_remote_get( $info_url );
                if( is_wp_error( $info_result ) ){
                    return __('No response 1, try later!', 'mine-cloudvod');
                    exit;
                }
                $uinfo = json_decode($info_result['body'], true);
                
                $pass = wp_create_nonce(rand(10,1000));
                $login_name = "mcv".time().mt_rand(1000,9999);
                $username = $uinfo['nickname'];
                $userdata=array(
                    'user_login' => $login_name,
                    'display_name' => $username,
                    'user_nicename' => $username,
                    'user_pass' => $pass,
                    'first_name' => $username
                );
                $user_id = wp_insert_user( $userdata );
                
                if ( is_wp_error( $user_id ) ) {
                    // return __('No response 3, try later!', 'mine-cloudvod');
                }else{
                    $user = get_user_by('id', $user_id);
                    update_user_meta($user_id, 'mcv_avatar', $uinfo['headimgurl']);
                    update_user_meta($user_id, 'mcv_wechat_unionid', $uinfo['unionid']??'');
                    update_user_meta($user_id, 'mcv_wechat_openid', $uinfo['openid']);
                    wp_set_current_user( $user_id );
                    wp_set_auth_cookie($user_id,true,is_ssl());
                    do_action( 'wp_login', $user->user_login, $user );
                }
            }
        }
    }

    public function wechat_script( $script, $key ){
        if( $key == $this->login_id ){
            $wechat_open = MINECLOUDVOD_SETTINGS['uc_login3'][$this->login_id] ?? [];
            $appid = $wechat_open['AppID']??'';
            $mpappid = $wechat_open['MpAppID']??'';
            $script = ( '
                var ua = window.navigator.userAgent.toLowerCase();
                if(ua.match(/MicroMessenger/i) == "micromessenger"){
                    let ruri = location.href;
                    ruri += (ruri.indexOf("?")>0?"&":"?") + "mcv_wx_client=1"
                    location.href="https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$mpappid.'&redirect_uri="+encodeURIComponent(ruri)+"&response_type=code&scope=snsapi_userinfo&state=200#wechat_redirect";
                }
                else{
                    const script = document.createElement("script");
                    script.src = "//res.wx.qq.com/connect/zh_CN/htmledition/js/wxLogin.js";
                    const head = document.getElementsByTagName("head")[0];
                    head.appendChild(script);
                    script.onload = function() {
                        var obj = new WxLogin({
                            self_redirect:false,
                            id:"login_container", 
                            appid: "'.($appid).'", 
                            scope: "snsapi_login", 
                            redirect_uri: encodeURIComponent("'.get_rest_url( null, 'mine-cloudvod/v1/login_wechat_open' ).'"),
                            state: "'.urlencode( base64_encode($_SERVER['REQUEST_URI']).'_'.wp_create_nonce( 'wechat_open' ) ).'",
                            style: "",
                            href: "",
                            fast_login:1
                        });
                    }
                }
            ' );
        }
        return $script;
    }

    public function login_options( $login3 ){
        $ops = false;
        if( isset( MINECLOUDVOD_SETTINGS[$this->login_id] ) ) $ops = MINECLOUDVOD_SETTINGS[$this->login_id];
        $login3[] = array(
            'id'        => $this->login_id,
            'type'      => 'fieldset',
            'title'     => __('Wechat Open Platform', 'mine-cloudvod'),
            'fields'    => array(
                array(
                    'id'    => 'status',
                    'type'  => 'switcher',
                    'title' => __('State', 'mine-cloudvod'),
                    'text_on'    => __('Enable', 'mine-cloudvod'),
                    'text_off'   => __('Disable', 'mine-cloudvod'),
                    'default' => $ops ? $ops['status'] : false,
                ),
                array(
                    'id'    => 'title',
                    'type'  => 'text',
                    'title' => __('Title', 'mine-cloudvod'),
                    'dependency' => array('status', '==', true),
                    'default' => '微信登录',
                ),
                array(
                    'id'    => 'AppID',
                    'type'  => 'text',
                    'title' => 'AppID',
                    'dependency' => array('status', '==', true),
                    'desc' => '微信开放平台的AppID',
                    'default' => $ops ? $ops['AppID'] : false,
                ),
                array(
                    'id'    => 'AppSecret',
                    'type'  => 'text',
                    'title' => 'AppSecret',
                    'dependency' => array('status', '==', true),
                    'desc' => '微信开放平台的AppSecret',
                    'default' => $ops ? $ops['AppSecret'] : false,
                ),
                array(
                    'type'  => 'submessage',
                    'style'     => 'success',
                    'content'   => '微信客户端打开网站自动登录，请配置公众号信息',
                ),
                array(
                    'id'    => 'MpAppID',
                    'type'  => 'text',
                    'title' => '公众号AppID',
                    'dependency' => array('status', '==', true),
                    'desc' => '微信公众号的AppID',
                    'default' => $ops ? $ops['AppID'] : false,
                ),
                array(
                    'id'    => 'MpAppSecret',
                    'type'  => 'text',
                    'title' => '公众号AppSecret',
                    'dependency' => array('status', '==', true),
                    'desc' => '微信公众号的AppSecret',
                    'default' => $ops ? $ops['AppSecret'] : false,
                ),
                array(
                    'id'    => 'class',
                    'type'  => 'text',
                    'title' => '',
                    'dependency' => array('status', '==', 'none'),
                    'default' => 'MineCloudvod\Payment\WechatOpen',
                ),
            ),
        );
        return $login3;
    }
    public function register_routes(){
        /**
         * 登录回调
         */
        register_rest_route('mine-cloudvod/v1', '/login_wechat_open', [
            [
                'methods'             => \WP_REST_Server::READABLE,
                'callback'            => [$this, 'mcv_login_wechat_open'],
                'permission_callback' => '__return_true',
                'args'                => [
                    'code' => [
                        'type' => 'string'
                    ],
                    'state' => [
                        'type' => 'string'
                    ],
                ]
            ],
        ]);
    }
    /**
     * 登录回调
     */
    public function mcv_login_wechat_open( \WP_REST_Request $request ){
        if(empty($request['code']) || empty($request['state'])){
            return __('Code or State is missed', 'mine-cloudvod');
            exit;
        }
        $state = $request['state'];
        $state = explode( '_', urldecode( $state ) );
        $redirect_to = base64_decode( $state[0] );
        if( !wp_verify_nonce($state[1], 'wechat_open') ){
            return __('Hello Mine', 'mine-cloudvod');
            exit;
        }

        $code = $request['code'];
        $wechat_open = MINECLOUDVOD_SETTINGS['uc_login3'][$this->login_id];
        $wechat_api = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='. $wechat_open['AppID'].'&secret='. $wechat_open['AppSecret'].'&code='.$code.'&grant_type=authorization_code';
        $response = wp_remote_get( $wechat_api );
        if( is_wp_error( $response ) ){
            return __('No response, try later!', 'mine-cloudvod');
        }
        $data = json_decode($response['body'], true);
    	global $wpdb;
        $uid = 0;
        if( isset( $data['unionid'] ) && $data['unionid'] ){
            $uid = $wpdb->get_var("SELECT ID FROM $wpdb->users LEFT JOIN $wpdb->usermeta ON $wpdb->users.ID=$wpdb->usermeta.user_id WHERE $wpdb->usermeta.meta_key='mcv_wechat_unionid' and $wpdb->usermeta.meta_value='".esc_sql($data['unionid'])."';");
        }
        elseif( isset( $data['openid'] ) && $data['openid'] ){
            $uid = $wpdb->get_var("SELECT ID FROM $wpdb->users LEFT JOIN $wpdb->usermeta ON $wpdb->users.ID=$wpdb->usermeta.user_id WHERE $wpdb->usermeta.meta_key='mcv_wechat_openid' and $wpdb->usermeta.meta_value='".esc_sql($data['openid'])."';");
        }
        else{
            return 'No response, try later!';
        }
        
        if( $uid ){
            $user = get_user_by('id', $uid);
            wp_set_current_user( $uid );
            wp_set_auth_cookie($uid,true,is_ssl());
            do_action( 'wp_login', $user->user_login, $user );
            wp_redirect( $redirect_to );
            return;
        }else{
            $info_url = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$data['access_token'].'&openid='.$data['openid'].'&lang=zh_CN';
            $info_result = wp_remote_get( $info_url );
            if( is_wp_error( $info_result ) ){
                return __('No response 1, try later!', 'mine-cloudvod');
                exit;
            }
            $uinfo = json_decode($info_result['body'], true);
            if( !isset( $uinfo['unionid'] ) ){
                return __('No response 2, try later!', 'mine-cloudvod');
                exit;
            }
            $pass = wp_create_nonce(rand(10,1000));
            $login_name = "mcv".time().mt_rand(1000,9999);
            $username = $uinfo['nickname'];
            $userdata=array(
                'user_login' => $login_name,
                'display_name' => $username,
                'user_nicename' => $username,
                'user_pass' => $pass,
                'first_name' => $username
            );
            $user_id = wp_insert_user( $userdata );
            
            if ( is_wp_error( $user_id ) ) {
                return __('No response 3, try later!', 'mine-cloudvod');
            }else{
                $user = get_user_by('id', $user_id);
                update_user_meta($user_id, 'mcv_avatar', $uinfo['headimgurl']);
                update_user_meta($user_id, 'mcv_wechat_unionid', $uinfo['unionid']);
                update_user_meta($user_id, 'mcv_wechat_openid', $uinfo['openid']);
                wp_set_current_user( $user_id );
                wp_set_auth_cookie($user_id,true,is_ssl());
                do_action( 'wp_login', $user->user_login, $user );
                wp_redirect( $redirect_to );
                return;
            }
        }
    }
}