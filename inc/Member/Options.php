<?php
namespace MineCloudvod\Member;

if ( ! defined( 'ABSPATH' ) ) exit;

class Options{
    public $prefix = 'mcv_settings';
    public function __construct() {
        add_action( 'mcv_add_admin_options_before_purchase', [ $this, 'logins_admin_options' ] );

        $this->init_logins();

        add_action( 'init', [ $this, 'localize_script' ] );
    }

    public function localize_script(){
        $general = MINECLOUDVOD_SETTINGS['uc_general'] ?? [];
        $mcv_uc_general = [];
        if( isset( $general['themelogin'] ) && $general['themelogin'] ){
            $mcv_uc_general['loginselector'] = $general['selector'];
        }
        $mcv_uc_general['regurl'] = mcv_registration_url();

        $uc_login3 = MINECLOUDVOD_SETTINGS['uc_login3'] ?? [];
        $logins = [];
        foreach( $uc_login3 as $key => $loginInfo ){
            if( !$loginInfo['status'] ) continue;
            $script = apply_filters( 'mcv_login_script', '', $key );
            $logins[] = [
                'name'      =>$key,
                'title'     =>$loginInfo['title'],
                'script'    => $script,
            ];
        }
        $mcv_uc_general['uc_login3'] = $logins;
        wp_localize_script( 'mcv_localize_script', 'mcv_uc_general', $mcv_uc_general );
        wp_enqueue_script( 'mcv_localize_script' );
    }

    public function logins_admin_options(){
        \MCSF::createSection( $this->prefix, [
            'id'    => 'mcv_member',
            'title' => __('User Center', 'mine-cloudvod'),
            'icon'  => 'fas fa-user',
        ]);
        $login3 = [
            [
                'id'        => 'default',
                'type'      => 'fieldset',
                'title'     => __('Default user login', 'mine-cloudvod'),
                'fields'    => [
                    [
                        'id'    => 'status',
                        'type'  => 'switcher',
                        'title' => __('State', 'mine-cloudvod'),
                        'text_on'    => __('Enable', 'mine-cloudvod'),
                        'text_off'   => __('Disable', 'mine-cloudvod'),
                        'default' => true,
                    ],
                    array(
                        'id'    => 'title',
                        'type'  => 'text',
                        'title' => __('Title', 'mine-cloudvod'),
                        'dependency' => array('status', '==', true),
                        'default' => __('User Login', 'mine-cloudvod'),
                    ),
                ],
            ]
        ];
        $login3 = apply_filters( 'mcv_user_login_options', $login3 );
        \MCSF::createSection($this->prefix, [
            'parent'     => 'mcv_member',
            'title'  => __('General settings', 'mine-cloudvod'),
            'icon'   => 'fas fa-home',
            'fields' => [
                [
                    'id'        => 'uc_general',
                    'type'      => 'fieldset',
                    'title'     => __('Login', 'mine-cloudvod'),
                    'fields'    => [
                        [
                            'id'    => 'themelogin',
                            'type'  => 'switcher',
                            'title' => '调用主题登录',
                            'text_on'    => __('Enable', 'mine-cloudvod'),
                            'text_off'   => __('Disable', 'mine-cloudvod'),
                            'default' => false,
                        ],
                        [
                            'id'    => 'selector',
                            'type'  => 'text',
                            'title' => 'CSS选择器',
                            'dependency' => ['themelogin', '==', true],
                            'desc' => '',
                        ],
                    ],
                ],
                [
                    'id'        => 'uc_general',
                    'type'      => 'fieldset',
                    'title'     => __('Register', 'mine-cloudvod'),
                    'fields'    => [
                        [
                            'id'    => 'themereg',
                            'type'  => 'switcher',
                            'title' => '自定义注册链接',
                            'text_on'    => __('Enable', 'mine-cloudvod'),
                            'text_off'   => __('Disable', 'mine-cloudvod'),
                            'default' => false,
                        ],
                        [
                            'id'    => 'regurl',
                            'type'  => 'text',
                            'title' => '注册链接',
                            'dependency' => ['themereg', '==', true],
                            'desc' => '',
                        ],
                    ],
                ],
                [
                    'id'    => 'hideAdminBar',
                    'type'  => 'switcher',
                    'title' => __('Hide Admin Bar', 'mine-cloudvod'),
                    'text_on'    => __('Enable', 'mine-cloudvod'),
                    'text_off'   => __('Disable', 'mine-cloudvod'),
                    'default' => false,
                ],
            ]
        ]); 
        \MCSF::createSection($this->prefix, [
            'parent'     => 'mcv_member',
            'title'  => __('Login', 'mine-cloudvod'),
            'icon'   => 'fas fa-share-alt',
            'fields' => [
                [
                    'type'      => 'submessage',
                    'style'     => 'success',
                    'content'   => __( 'Drag and drop login methods can be sorted.', 'mine-cloudvod' ),
                ],
                [
                    'id'        => 'uc_login3',
                    'class'     => 'uc_login3',
                    'type'      => 'sortable',
                    // 'title'     => __('Login', 'mine-cloudvod'),
                    'fields'    => $login3,
                ],
            ]
        ]); 
    }

    public function init_logins(){
        $logins = [
            'weixinopen' => 'MineCloudvod\Member\WechatOpen',
        ];
        /**
         * 登录class过滤器，注册登录的class，在class中处理登录的逻辑
         */
        $logins = apply_filters( 'mcv_user_login_classes', $logins );
        foreach( $logins as $login ){
            if( is_string( $login ) && class_exists( $login ) ){
                $login = new $login();
            }
        }

    }
}