<?php 
namespace MineCloudvod;

if(!defined('ABSPATH'))exit;

class McvOptions{
    public $prefix = 'mcv_settings';
    public function __construct() {

        add_action( 'mcv_add_admin_options_before_purchase', array( $this, 'function_switch_options' ) );

        $this->init();
    }

    public function function_switch_options(){
        
        \MCSF::createSection(  $this->prefix, array(
            'parent'      => 'mcv_general',
            'title'       => __('Function Switch', 'mine-cloudvod'),
            'icon'        => 'fas fa-power-off',
            'description' => '',
            'fields'      => array(
                array(
                    'type'    => 'submessage',
                    'style'   => 'success',
                    'content' => __('Turning off unneeded functions can properly save server resources.', 'mine-cloudvod'),
                ),
                array(
                    'id'        => 'mcv_lms',
                    'title'     => __('Mine LMS', 'mine-cloudvod'),
                    'type'      => 'fieldset',
                    'fields'    => array(
                        array(
                            'id'    => 'status',
                            'type'  => 'switcher',
                            'title' => ' ' . __('State', 'mine-cloudvod'),
                            'text_on'    => __('Enable', 'mine-cloudvod'),
                            'text_off'   => __('Disable', 'mine-cloudvod'),
                            'default' => true,
                        ),
                        array(
                            'type'    => 'submessage',
                            'style'   => 'warning',
                            'content' => ' <a href="javascript:mcv_init_lms();">' . __('For the first time, please click here to initialize', 'mine-cloudvod') . '</a>',
                            'dependency' => array('status', '==', true),
                        ),
                    ),
                ),
                array(
                    'id'        => 'players',
                    'title'     => __('Player', 'mine-cloudvod'),
                    'type'      => 'fieldset',
                    'fields'    => array(
                        array(
                            'id'    => 'dplayer',
                            'type'  => 'switcher',
                            'title' => __('DPlayer', 'mine-cloudvod'),
                            'text_on'    => __('Enable', 'mine-cloudvod'),
                            'text_off'   => __('Disable', 'mine-cloudvod'),
                            'default' => true,
                        ),
                        array(
                            'id'    => 'aplayer',
                            'type'  => 'switcher',
                            'title' => __('Audio Player', 'mine-cloudvod'),
                            'text_on'    => __('Enable', 'mine-cloudvod'),
                            'text_off'   => __('Disable', 'mine-cloudvod'),
                            'default' => true,
                        ),
                        array(
                            'id'    => 'aliplayer',
                            'type'  => 'switcher',
                            'title' => __('Aliplayer', 'mine-cloudvod'),
                            'text_on'    => __('Enable', 'mine-cloudvod'),
                            'text_off'   => __('Disable', 'mine-cloudvod'),
                            'default' => true,
                        ),
                        array(
                            'id'    => 'embed',
                            'type'  => 'switcher',
                            'title' => __('Embed Video', 'mine-cloudvod'),
                            'text_on'    => __('Enable', 'mine-cloudvod'),
                            'text_off'   => __('Disable', 'mine-cloudvod'),
                            'default' => true,
                        ),
                        array(
                            'id'    => 'playlist',
                            'type'  => 'switcher',
                            'title' => __('Video Playlist', 'mine-cloudvod'),
                            'text_on'    => __('Enable', 'mine-cloudvod'),
                            'text_off'   => __('Disable', 'mine-cloudvod'),
                            'default' => true,
                        ),
                    )
                ),
            ),
        ));
    }
    public function init() {
        $prefix = $this->prefix;
        
        \MCSF::createOptions( $this->prefix, array(
            'menu_title' => __('Mine CloudVod', 'mine-cloudvod'),
            'menu_slug'  => 'mcv-options',
            'admin_bar_menu_icon'=>'fas fa-cloud',
            'framework_title' => __('Mine CloudVod', 'mine-cloudvod') . ' <small>by mine27</small>',
            'menu_icon'=>MINECLOUDVOD_URL.'/static/img/aliplayer_20.png',
            'show_bar_menu' => false,
            'show_sub_menu'=>false,
            'menu_position' => 2,
            'menu_hidden' => true,
            'show_search' => false,
            'show_reset_all' => false,
            'footer_text'             => __('Welcome to use my plugin.', 'mine-cloudvod'),
            'footer_credit' => sprintf(
				__( 'If you like %1$s please leave us a %2$s rating. A huge thanks in advance!', 'mine-cloudvod' ),
				sprintf( '<strong>%s</strong>', esc_html__( 'Mine CloudVod', 'mine-cloudvod' ) ),
				'<a href="https://wordpress.org/support/plugin/mine-cloudvod/reviews?rate=5#new-post" target="_blank" class="mcv-rating-link">&#9733;&#9733;&#9733;&#9733;&#9733;</a>'
			)
        ));
        
        
        \MCSF::createSection(  $this->prefix, array(
            'id'          => 'mcv_general',
            'title'       => __('General settings', 'mine-cloudvod'),
            'icon'        => 'fas fa-home',
            'description' => ''
        ));
        \MCSF::createSection( $this->prefix, array(
            'parent'      => 'mcv_general',
            'title'  => __('General settings', 'mine-cloudvod'),
            'icon'   => 'fas fa-home',
            'fields' => array(
                array(
                    'type'    => 'submessage',
                    'style'   => 'success',
                    'content' => __('Welcome to use Mine CloudVod, after installing this plugin, there is a 14-day trial period for pro feature.', 'mine-cloudvod'),
                ),
                array(
                    'id'         => 'siteid',
                    'type'       => 'text',
                    'title'      => __('Site ID', 'mine-cloudvod'), //'站点ID',
                    'attributes' => array(
                        'readonly' => 'readonly'
                    ),
                    'default'    => ''
                ),
                array(
                    'id'         => 'secret',
                    'type'       => 'text',
                    
        
                    'attributes' => array(
                        'hidden' => ''
                    ),
                    'default'    => ''
                ),
                array(
                    'id'         => 'endtime',
                    'type'       => 'text',
                    'title'      => 'Pro '.__('Valid until', 'mine-cloudvod'),
                    'before'     => __('Only show the expiration time, do not tamper with.', 'mine-cloudvod') . ' <a href="javascript:mcv_sync_endtime();">' . __('Sync expiration time', 'mine-cloudvod') . '</a>',
                    'after'      => '<p><a href="' . admin_url('/admin.php?page=mcv-options#tab=' . str_replace(' ', '-', strtolower(urlencode(__('Purchase time', 'mine-cloudvod'))))) . '" data-tab-id="' . str_replace(' ', '-', strtolower(urlencode(__('Purchase time', 'mine-cloudvod')))) . '">' . __('Purchase time', 'mine-cloudvod') . '</a></p>',
                    'attributes' => array(
                        'readonly' => 'readonly'
                    ),
                    'default'    => ''
                ),
                array(
                    'id'      => 'cdntype',
                    'type'    => 'radio',
                    'title'   => __('CDN Type', 'mine-cloudvod'),
                    'inline'  => true,
                    'options' => array(
                        'self'    => __('Self Hosted', 'mine-cloudvod'),
                        'jsdelivr'   => __('Jsdelivr', 'mine-cloudvod'),
                        'customize'   => __('Customize', 'mine-cloudvod'),
                    ),
                    'default' => 'self',
                ),
                array(
                    'id'         => 'cdnprefix',
                    'type'       => 'text',
                    'title'      => __('CDN Prefix', 'mine-cloudvod'), //'CDN前缀',
                    'after'      => __('You can use {version} to replace the version of the plugin.', 'mine-cloudvod'),
                    'default'    => 'https://cdn.jsdelivr.net/wp/plugins/mine-cloudvod/tags/{version}',
                    'dependency' => array('cdntype', '==', 'customize'),
                ),
                array(
                    'id'        => 'rolePermission',
                    'type'      => 'fieldset',
                    'title' => __('Role Permission', 'mine-cloudvod'),//'角色权限',
                    'subtitle'     => '',
                    'fields'    => array(
                        array(
                            'id'    => 'status',
                            'type'  => 'switcher',
                            'title' => __('State', 'mine-cloudvod'), //'状态',
                            'text_on'    => __('Enable', 'mine-cloudvod'), //'启用',
                            'text_off'   => __('Disable', 'mine-cloudvod'), //'禁用',
                            'default' => false
                        ),
                        array(
                            'id'    => 'roles',
                            'type'  => 'select',
                            'options'     => 'roles',
                            'multiple'      => true,
                            'attributes' => array(
                              'style'    => 'min-width: 150px;min-height:200px;'
                            ),
                            'dependency' => array('status', '==', true),
                            'default'    => ['administrator','author','editor']
                        ),
                    )
                ),
                array(
                    'type'    => 'submessage',
                    'style'   => 'success',
                    'content' => __('Welcome to use my plugin.', 'mine-cloudvod'),
                ),
            )
        ));

        // include 'options/aliplayer.php';include 'options/aliplayer_components.php';
        /**
         * 在aliplayer之后添加配置选项
         */
        do_action( 'mcv_add_admin_options_after_aliplayer' );
        
        
        
        // include 'options/tcvod.php';
        // include 'options/tccos.php';
        // include 'options/tcplayer.php';
        //include 'options/tcvod_touwei.php';
        
        
        
        // include 'options/aliyunvod.php';
        //include 'options/aliyunvod_touwei.php';
        // include 'options/aliyunoss.php';
        
        
        
        
        do_action( 'mcv_add_admin_options_before_purchase' );
        
        \MCSF::createSection( $this->prefix, array(
            'id'     => 'buytime',
            'title'  => __('Purchase time', 'mine-cloudvod'),
            'icon'   => 'fas fa-shopping-cart',
            'fields' => array(
                array(
                    'type'    => 'submessage',
                    'style'   => 'warning',
                    'content' => __('<font color="red">Reminder</font>: The time package purchased on this page is the service time of using the Mine CloudVod plugin.', 'mine-cloudvod'), //'<p><font color="red">温馨提示</font>：本页面购买的时长包，是使用 Mine云点播 插件的服务时长</p>',
                ),
                array(
                    'id'      => 'timebug',
                    'type'    => 'radio',
                    'title'   => __('Time package', 'mine-cloudvod'), //'时长包',
                    'inline'  => false,
                    'options' => array(
                        '6'     => __('Half a year', 'mine-cloudvod'), //'半年',
                        '12'    => __('One year (15% off)', 'mine-cloudvod'), //'1 年 （85折）',
                        '24'    => __('Two years (25% off)', 'mine-cloudvod'), //'2 年 （75折）',
                        '36'    => __('Three years (35% off)', 'mine-cloudvod'), //'3 年 （65折）',
                        '48'    => __('Four years (45% off)', 'mine-cloudvod'), //'4 年 （55折）',
                        '60'    => __('Five years (50% off)', 'mine-cloudvod'), //'5 年 （5折）',
                    ),
                    'default' => '12'
                ),
                array(
                    'type'    => 'content',
                    'content' => '<p><input type="button" id="buytimebug" class="button button-primary csf-save" value="' . __('Click to buy', 'mine-cloudvod') . '"></p>', //'<p><input type="button" id="buytimebug" class="button button-primary csf-save" value="点击购买"></p>',
                ),
                array(
                    'type'    => 'submessage',
                    'style'   => 'warning',
                    'content' => '
            <p>咨询QQ: 995525477, 微信号: MineCloudVod</p>
            <p>折扣以实际显示价格为准</p>
            <p><font color="red">支付成功后请进入<a href="#tab=' . str_replace([' ', '+'], '-', strtolower(urlencode(__('General settings', 'mine-cloudvod')))) . '" data-tab-id="' . str_replace([' ', '+'], '-', strtolower(urlencode(__('General settings', 'mine-cloudvod')))) . '">' . __('General settings', 'mine-cloudvod') . '</a>页面，点击同步到期时间<br></font></p>
            ',
                ),
            )
        ));
    }
}


