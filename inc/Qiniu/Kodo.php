<?php
namespace MineCloudvod\Qiniu;

defined( 'ABSPATH' ) || exit;

class Kodo{

    private $id = 'qiniukodo';
    private $_wpcvApi;

    public function __construct() {
        global $McvApi;
        $this->_wpcvApi     = $McvApi;
        $this->init();
    }

    public function init(){
        add_action( 'mcv_add_admin_options_before_purchase', array( $this, 'admin_options' ) );

        add_action( 'init',     [ $this, 'mcv_register_block'] );
        add_action('wp_ajax_mcv_asyc_qiniu_buckets', array($this, 'mcv_asyc_qiniu_buckets'));
        add_action('rest_api_init', [$this, 'register_routes']);
    }

    public function register_routes(){
        /**
         * search videos
         */
        register_rest_route("mine-cloudvod/v1", '/qiniu/kodo/videos', [
            [
                'methods'             => \WP_REST_Server::READABLE,
                'callback'            => [$this, 'fetch_videos'],
                'permission_callback' => [$this, 'read_files_permissions_check'],
                'args'                => [
                    'page' => [
                        'type' => 'integer',
                    ],
                    'search' => [
                        'type' => 'string'
                    ],
                    'items_per_page'  => [
                        'type' => 'integer',
                    ]
                ]
            ],
        ]);
        /**
         * delete video
         */
        register_rest_route("mine-cloudvod/v1", '/qiniu/kodo/delvideo', [
            [
                'methods'             => \WP_REST_Server::DELETABLE,
                'callback'            => [$this, 'del_video'],
                'permission_callback' => [$this, 'is_admin_editor'],
                'args'                => [
                    'videoId' => [
                        'type' => 'string',
                    ]
                ]
            ],
        ]);
        /**
         * upload sign
         */
        register_rest_route("mine-cloudvod/v1", '/qiniu/kodo/usign', [
            [
                'methods'             => \WP_REST_Server::READABLE,
                'callback'            => [$this, 'get_usign'],
                'permission_callback' => '__return_true',
                'args'                => []
            ],
        ]);
        /**
         * play url
         */
        register_rest_route("mine-cloudvod/v1", '/qiniu/kodo/url', [
            [
                'methods'             => \WP_REST_Server::EDITABLE,
                'callback'            => [$this, 'get_play_url'],
                'permission_callback' => '__return_true',
                'args'                => [
                    'key' => [
                        'type' => 'string',
                    ],
                ]
            ],
        ]);
    }

    public function get_play_url(\WP_REST_Request $request){

        $result = $this->call_url(sanitize_text_field( $request['key'] ));

        if (is_wp_error($result)) {
            return $result;
        }
        if($result['status'] == 0){
            return new \WP_Error('cant-trash', $result['msg'], ['status' => 500]);
        }

        return rest_ensure_response($result);
    }
    public function call_url( $key ){
        $req = [
            'mode' => 'qiniu',
            'sdk' => $this->_wpcvApi->encrypt( MINECLOUDVOD_SETTINGS['qiniu'] ),
            'bucket'    => MINECLOUDVOD_SETTINGS['qiniu']['bucket'],
            'scheme' => is_ssl() ? 'https' : 'http',
            'key' => $key,
        ];
        $result = $this->_wpcvApi->call('geturl', $req);
        return $result;
    }

    public function del_video(\WP_REST_Request $request){
        $req = array(
            'mode' => 'qiniu',
            'sdk' => $this->_wpcvApi->encrypt( MINECLOUDVOD_SETTINGS['qiniu'] ),
            'bucket'    => MINECLOUDVOD_SETTINGS['qiniu']['bucket'],
            'key'    => sanitize_text_field( $request['videoId'] ),
        );
        $result = $this->_wpcvApi->call('delete', $req);

        if (is_wp_error($result)) {
            return $result;
        }
        if($result['status'] == 0){
            return new \WP_Error('cant-trash', $result['msg'], ['status' => 500]);
        }

        return rest_ensure_response($result);
    }

    public function get_usign(\WP_REST_Request $request){
        $req = array(
            'mode' => 'qiniu',
            'sdk' => $this->_wpcvApi->encrypt( MINECLOUDVOD_SETTINGS['qiniu'] ),
            'bucket'    => MINECLOUDVOD_SETTINGS['qiniu']['bucket'],
        );
        $result = $this->_wpcvApi->call('usign', $req);

        if (is_wp_error($result)) {
            return $result;
        }
        if($result['status'] == 0){
            return new \WP_Error('cant-trash', $result['msg'], ['status' => 500]);
        }
        
        $result['path'] = sprintf('http%s://upload%s.qiniup.com',
            is_ssl()?'s':'',
            MINECLOUDVOD_SETTINGS['qiniu']['region'] == 'z0' ? '' : '-' . MINECLOUDVOD_SETTINGS['qiniu']['region']
        );

        return rest_ensure_response($result);
    }

    public function fetch_videos(\WP_REST_Request $request){
        $req = array(
            'pageNo' => (int) $request['page'],
            'pageSize' => (int)$request['items_per_page'],
            'mode' => 'qiniu',
            'sdk' => $this->_wpcvApi->encrypt( MINECLOUDVOD_SETTINGS['qiniu'] ),
            'bucket'    => MINECLOUDVOD_SETTINGS['qiniu']['bucket'],
            'scrollToken'    => $request['st'],
            'keyword'    => $request['search'],
        );
        $result = $this->_wpcvApi->call('search', $req);

        if (is_wp_error($result)) {
            return $result;
        }
        if($result['status'] == 0){
            return new \WP_Error('cant-trash', $result['msg'], ['status' => 500]);
        }
        $videos = $result['data'];
        
        // var_dump($videos);
        if(is_array($videos['items']) && !empty($videos['items'])){
            $items = [];
            foreach ($videos['items'] as $key => $item) {
                if($item['fsize'] == 0) continue;
                $temp = $item;
                $temp['thumbnail'] = $item['coverURL']??'';
                $temp['updated_at'] = $item['putTime']??0;
                $temp['created_at'] = $item['putTime']??0;
                $temp['title'] = $item['x-qn-meta']['name']?:$item['key'];
                $temp['videoId'] = $item['key'];
                $temp['size'] = $item['fsize'];
                $temp['status'] = 'Normal';
                $temp['mediaType'] = $request['searchType'];
                if($request['searchType'] == 'audio'){
                    $temp['videoId'] = $item['audioId'];
                }
                $items[] = $temp;
            }
            $videos['items'] = $items;
        }

        return rest_ensure_response($videos);
    }
    
    public function is_admin_editor(){
        $user = wp_get_current_user();
        $allowed_roles = array( 'editor', 'administrator' );
        if ( array_intersect( $allowed_roles, $user->roles ) ) {
            return true;
        }
        return false;
    }
    public function read_files_permissions_check(){
        return current_user_can('edit_posts');
    }

    public function mcv_register_block(){
        wp_register_script(//mcv_dplayer_hls
            'mcv_dplayer_hls',
            MINECLOUDVOD_URL.'/static/dplayer/hls.min.js',
            null,
            MINECLOUDVOD_VERSION,
            true
        );

        wp_register_style(
            'mcv_dplayer_css',
            MINECLOUDVOD_URL.'/static/dplayer/style.css', 
            is_admin() ? array( 'wp-editor' ) : null,
            MINECLOUDVOD_VERSION
        );
        
        
        register_block_type( MINECLOUDVOD_PATH . '/build/qiniu/');
        
        wp_add_inline_script('mine-cloudvod-qiniu-editor-script','var mcv_qiniu_config={qiniu_config_url:"'.admin_url('/admin.php?page=mcv-options#tab='.str_replace(' ', '-', strtolower(urlencode(__('Qiniu', 'mine-cloudvod'))))).'pro",sdk:'.(MINECLOUDVOD_SETTINGS['qiniu']['sid']??MINECLOUDVOD_SETTINGS['qiniu']['kid']??false ? 'true' : 'false').'};');
    }

    public function mcv_asyc_qiniu_buckets(){
        if(!current_user_can('manage_options')){
            echo json_encode(array('status' => '0', 'msg' => __('Illegal request', 'mine-cloudvod')));exit;
        }
        header('Content-type:application/json; Charset=utf-8');
        $nonce   = !empty($_POST['nonce']) ? $_POST['nonce'] : null;
        if ($nonce && !wp_verify_nonce($nonce, 'mcv_asyc_qiniu_buckets')) {
            echo json_encode(array('status' => '0', 'msg' => __('Illegal request', 'mine-cloudvod')));exit;
        }
        $data = array(
            'mode' => 'qiniu',
            'sdk' => $this->_wpcvApi->encrypt( MINECLOUDVOD_SETTINGS['qiniu'] ),
            'region' => MINECLOUDVOD_SETTINGS['qiniu']['region'],
        );
        $buckets = $this->_wpcvApi->call('bucketsv2', $data);
        
        update_option('mcv_qiniu_bucketsList', $buckets['data'][0]);
        echo json_encode($buckets);
        exit;
    }

    public function admin_options(){
        $prefix = 'mcv_settings';
        $mcv_qiniu_bucketsList = array('' => __('Please sync Bukcets List first', 'mine-cloudvod'));
        if($tctc = get_option('mcv_qiniu_bucketsList')){
            $mcv_qiniu_bucketsList = array();
            foreach($tctc as $tc){
                $mcv_qiniu_bucketsList[$tc] =  $tc;
            }
        }
        \MCSF::createSection( $prefix, array(
            'id'    => 'mcv_qiniu',
            'title' => __('Qiniu', 'mine-cloudvod'). '<span class="mcv-pro-feature"><span class="plugin-count">Pro</span></span>',
            'icon'  => 'fas fa-cloud',
            'fields' => array(
                array(
                    'type'    => 'submessage',
                    'style'   => 'warning',
                    'content' => sprintf('<a href="https://s.qiniu.com/nMzeQb" target="_blank">%s</a>', __('Click here to register Qiniu Kodo and enjoy the gift: forever free.', 'mine-cloudvod')), 
                ),
                array(
                    'id'        => 'qiniu',
                    'type'      => 'fieldset',
                    'title'     => '',
                    'fields'    => array(
                        array(
                            'id'    => 'sid',
                            'type'  => 'text',
                            'title' => 'AccessKey',
                            'attributes'  => array(
                                'autocomplete' => 'off'
                            ),
                        ),
                        array(
                            'id'    => 'skey',
                            'type'  => 'text',
                            'attributes'  => array(
                                'type'      => 'password',
                                'autocomplete' => 'off'
                            ),
                            'title' => 'SecretKey',
                            'after' => '<a href="https://portal.qiniu.com/user/key?cps_key=1h7kzmxg6f2oi" target="_blank">点此获取 AccessKey 和 SecretKey </a>',
                        ),
                        array(
                            'id'          => 'region',
                            'type'        => 'select',
                            'title'       => __('Storage area', 'mine-cloudvod'),//'存储区域',
                            'placeholder' => __('Select storage area', 'mine-cloudvod'),//'选择区域',
                            'options'     => [
                                'z0'                => '华东-浙江',
                                'cn-east-2'         => '华东-浙江2',
                                'z1'                => '华北-河北',
                                'z2'                => '华南-广东',
                                'na0'               => '北美-洛杉矶',
                                'as0'               => '亚太-新加坡',
                                'ap-northeast-1'    => '亚太-首尔',
                            ],
                        ),
                        array(
                            'id'          => 'bucket',
                            'type'        => 'select',
                            'title'       => __('Bucket', 'mine-cloudvod'),//'存储桶',
                            'after'       => '<p><a href="javascript:mcv_sync_qiniu_buckets();">'.__('Sync Buckets List', 'mine-cloudvod').'</a></p>',//同步Bucket列表,
                            'options'     => $mcv_qiniu_bucketsList,
                            'default'     => ''
                        ),
                        [
                            'id'            => 'transcode',
                            'title'         => '转码',
                            'type'          => 'fieldset',
                            'fields'        => [
                                [
                                    'id'            => 'status',
                                    'title'         => __('Status', 'mine-cloudvod'),
                                    'type'          => 'switcher',
                                    'text_on'       => __('Enable', 'mine-cloudvod'),
                                    'text_off'      => __('Disable', 'mine-cloudvod'),
                                    'default'       => false,
                                ],
                                array(
                                    'id'    => 'style',
                                    'type'  => 'text',
                                    'title' => '样式名称',
                                    'attributes'  => array(
                                        'autocomplete' => 'off'
                                    ),
                                    'dependency'    => [ 'status', '==', true ]
                                ),
                            ],
                        ],
                    ),
                ),
            )
          ) );
    }

    public function mcv_block_render($parsed_block, $enqueue = true){
        global $mcv_classes;
        $video = $mcv_classes->Dplayer->mcv_block_dplayer($parsed_block, $enqueue);

        return $video;
    }
}
