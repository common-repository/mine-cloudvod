<?php
namespace MineCloudvod\RestApi\Member;

if ( ! defined( 'ABSPATH' ) )
    exit;

class Login extends Base{

    protected $base = 'member';

    public function __construct(){
        $this->register();
    }

    public function register(){
        add_action('rest_api_init', [$this, 'register_routes']);
        add_filter('sanitize_user', [$this, 'mcv_sanitize_user'], 10, 3);
    }

    public function register_routes(){
        /**
         * 账号密码登录
         */
        register_rest_route("{$this->namespace}/{$this->version}", "/".$this->base."/login", [
            [
                'methods'             => \WP_REST_Server::EDITABLE,
                'callback'            => [$this, 'user_login'],
                'permission_callback' => '__return_true',
                'args'                => [
                    'log' => [
                        'type' => 'string',
                    ],
                    'pwd' => [
                        'type' => 'string',
                    ],
                ]
            ],
        ]);
        /**
         * 账号密码注册
         */
        register_rest_route("{$this->namespace}/{$this->version}", "/".$this->base."/reg", [
            [
                'methods'             => \WP_REST_Server::EDITABLE,
                'callback'            => [$this, 'user_reg'],
                'permission_callback' => '__return_true',
                'args'                => [
                    'log' => [
                        'type' => 'string',
                    ],
                    'pwd' => [
                        'type' => 'string',
                    ],
                    'email' => [
                        'type' => 'string',
                    ],
                ]
            ],
        ]);
    }
    public function mcv_sanitize_user ($username, $raw_username, $strict) {
        $username = wp_strip_all_tags( $raw_username );
        $username = remove_accents( $username );
        // Kill octets
        $username = preg_replace( '|%([a-fA-F0-9][a-fA-F0-9])|', '', $username );
        $username = preg_replace( '/&.+?;/', '', $username ); // entities
        if ($strict) {
            $username = preg_replace ('|[^a-z\p{Han}0-9 _.\-@]|iu', '', $username);
        }
        
        $username = trim( $username );
        // Consolidate contiguous whitespace
        $username = preg_replace( '|\s+|', ' ', $username );
        
        return $username;
    }


    public function user_reg(\WP_REST_Request $request){
        $can = get_option( 'users_can_register' );
        if( !$can ){
            return new \WP_Error('cant-trash', '当前站点未开放注册', ['status' => 500]);
        }
        $log  = sanitize_user( $request['log'] );
        $pwd  = sanitize_text_field( $request['pwd'] );
        $email  = sanitize_email( $request['email'] );
        
        if( !$log || !$pwd ){
            return new \WP_Error('cant-trash', '用户名或密码不能为空', ['status' => 500]);
        }
        if( !$email ){
            return new \WP_Error('cant-trash', '请填写正确的邮箱地址', ['status' => 500]);
        }

        $userdata=array(
            'user_login' => $log,
            'display_name' => $log,
            'nickname' => $log,
            'user_pass' => $pwd,
            'first_name' => $log,
            'user_email' => $email
        );
        $user_id = wp_insert_user( $userdata );

        if ( is_wp_error( $user_id ) ) {
            $errors = $user_id->errors;
            $emsg = '';
            foreach($errors as $error){
                $emsg = $error[0];
            }
            return new \WP_Error('cant-trash', $emsg, ['status' => 500]);
        }
        return rest_ensure_response(['success'=>true]);
    }

    public function user_login(\WP_REST_Request $request){
        
        $log  = sanitize_user( $request['log'] );
        $pwd  = sanitize_text_field( $request['pwd'] );
        
        if( !$log || !$pwd ){
            return new \WP_Error('cant-trash', '用户名或密码不能为空', ['status' => 500]);
        }

        $user = wp_signon(['user_login'=>$log,'user_password'=>$pwd,'remember'=>true]);
        if ( is_wp_error( $user ) ) {
            $errors = $user->errors;
            $emsg = '';
            foreach($errors as $error){
                $emsg = $error[0];
            }
            return new \WP_Error('cant-trash', $emsg, ['status' => 500]);
        }
        return rest_ensure_response(['success'=>true]);
    }
}
