<?php
namespace MineCloudvod\RestApi\LMS;

if ( ! defined( 'ABSPATH' ) )
    exit;

class Addons extends Base{

    protected $base = 'addons';

    private $_wpcvApi;
    public function __construct(){
        global $McvApi;
        $this->_wpcvApi     = $McvApi;
        $this->register();
    }

    public function register(){
        add_action('rest_api_init', [$this, 'register_routes']);
    }

    public function register_routes(){

        register_rest_route("{$this->namespace}/{$this->version}", "/".$this->base."/active", [
            [
                'methods'             => \WP_REST_Server::EDITABLE,
                'callback'            => [$this, 'addons_active'],
                'permission_callback' => [$this, 'read_files_permissions_check'],
                'args'                => [
					'addons' => [
                        'type' => 'string',
                    ],
                    'status' => [
                        'type' => 'boolean',
                    ]
                ],
            ],
        ]);
        register_rest_route("{$this->namespace}/{$this->version}", "/".$this->base."/infos", [
            [
                'methods'             => \WP_REST_Server::EDITABLE,
                'callback'            => [$this, 'addons_infos'],
                'permission_callback' => [$this, 'read_files_permissions_check'],
                'args'                => []
            ],
        ]);
        register_rest_route("{$this->namespace}/{$this->version}", "/".$this->base."/buy", [
            [
                'methods'             => \WP_REST_Server::EDITABLE,
                'callback'            => [$this, 'addons_buy'],
                'permission_callback' => [$this, 'read_files_permissions_check'],
                'args'                => [
					'addons' => [
                        'type' => 'string',
                    ],
                    'price' => [
                        'type' => 'string',
                    ]
                ]
            ],
        ]);
        register_rest_route("{$this->namespace}/{$this->version}", "/".$this->base."/get", [
            [
                'methods'             => \WP_REST_Server::EDITABLE,
                'callback'            => [$this, 'addons_get'],
                'permission_callback' => [$this, 'read_files_permissions_check'],
                'args'                => [
					'addons' => [
                        'type' => 'string',
                    ],
                ]
            ],
        ]);
    }

    public function addons_active(\WP_REST_Request $request){
        $addons_id   = sanitize_text_field( $request['addons'] );
        $status = $request['status'];
        $result = false;
        global $mcv_classes;
        if( $status ){
            $result = $mcv_classes->Addons->active_addons( $addons_id );
            if($result) mcv_addons_update( $addons_id );
            $result = true;
        }
        else{
            $result = $mcv_classes->Addons->deactive_addons( $addons_id );
        }
        return rest_ensure_response( [ 'result'=>$result ] );
    }

    public function addons_infos(\WP_REST_Request $request){
        $data = array();
        $addons = $this->_wpcvApi->call('addons', $data);
        $infos = ['addons'=>[]];
        if(isset($addons['data']['et'])){
            $setting = MINECLOUDVOD_SETTINGS;
            $setting['endtime'] = $addons['data']['et'];
            update_option('mcv_settings', $setting);
            $infos = $addons['data'];
        }
        return rest_ensure_response( $infos );
    }

    public function addons_buy(\WP_REST_Request $request){
        $addons_id   = sanitize_text_field( $request['addons'] );
        $price       = sanitize_text_field( $request['price'] );
        $met         = sanitize_text_field( $request['met'] );

        $data = array('addons' => $addons_id, 'price' => $price, 'met' => $met);
        $buyaddons = $this->_wpcvApi->call('buyaddons', $data);

        return rest_ensure_response( $buyaddons );
    }

    public function addons_get(\WP_REST_Request $request){
        $addons_id   = sanitize_text_field( $request['addons'] );

        $data = array('addons' => $addons_id);
        $buyaddons = $this->_wpcvApi->call('getaddons', $data);

        return rest_ensure_response( $buyaddons );
    }

    public function course_section_del(\WP_REST_Request $request){
        $section_id  = $request['section_id'];
        
        $delete = wp_delete_post( $section_id );
        $lessons = get_posts( [
            'post_type'     => MINECLOUDVOD_LMS['lesson_post_type'],
            'post_parent'   => $section_id,
            'orderby'       => 'menu_order',
            'order'         => 'ASC',
            'numberposts'   => 500,
        ] );
        if( is_array( $lessons ) && count( $lessons ) > 0){
            foreach( $lessons as $lesson ){
                wp_delete_post( $lesson->ID );
            }
        }
        if (is_wp_error($delete)) {
            return $delete;
        }
        
        return rest_ensure_response([
            'status'   => 1,
        ]);
    }

    public function course_section_order(\WP_REST_Request $request){
        global $wpdb;
        $section_ids  = $request['ids'];
        
        $menu_order = 1;
        foreach( $section_ids as $section_id ){
            $wpdb->update(
                $wpdb->posts,
                [ 'menu_order' => $menu_order ],
                ['ID' => $section_id ]
            );
            $menu_order++;
        }
        
        wp_send_json_success();
    }
}
