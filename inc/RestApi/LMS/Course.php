<?php
namespace MineCloudvod\RestApi\LMS;

if ( ! defined( 'ABSPATH' ) )
    exit;

class Course extends Base{

    protected $base = 'lms/course';

    public function __construct(){
        $this->register();
    }

    public function register(){
        add_action('rest_api_init', [$this, 'register_routes']);
    }

    public function register_routes(){
        /**
         * 获取学员列表
         */
        register_rest_route("{$this->namespace}/{$this->version}", "/".$this->base."/student", [
            [
                'methods'             => \WP_REST_Server::EDITABLE,
                'callback'            => [$this, 'student_list'],
                'permission_callback' => [$this, 'read_files_permissions_check'],
                'args'                => [
                    'course_id' => [
                        'validate_callback' => function ($param) {
                            return is_numeric($param);
                        }
                    ]
                ],
            ],
        ]);
        /**
         * 删除Student
         */
        register_rest_route("{$this->namespace}/{$this->version}", "/".$this->base."/remove_student", [
            [
                'methods'             => \WP_REST_Server::EDITABLE,
                'callback'            => [$this, 'remove_student'],
                'permission_callback' => [$this, 'read_files_permissions_check'],
                'args'                => [
					'course_id' => [
						'validate_callback' => function ($param) {
							return is_numeric($param);
						}
					],
					'uid' => [
						'validate_callback' => function ($param) {
							return is_numeric($param);
						}
					]
                ],
            ],
        ]);
        /**
         * 添加Student
         */
        register_rest_route("{$this->namespace}/{$this->version}", "/".$this->base."/add_student", [
            [
                'methods'             => \WP_REST_Server::EDITABLE,
                'callback'            => [$this, 'add_student'],
                'permission_callback' => [$this, 'read_files_permissions_check'],
                'args'                => [
					'course_id' => [
						'validate_callback' => function ($param) {
							return is_numeric($param);
						}
					],
					'uid' => [
						'type' => 'string',
					]
                ],
            ],
        ]);
        /**
         * 收藏
         */
        register_rest_route("{$this->namespace}/{$this->version}", "/".$this->base."/favorite", [
            [
                'methods'             => \WP_REST_Server::EDITABLE,
                'callback'            => [$this, 'course_favorite'],
                'permission_callback' => '__return_true',
                'args'                => [
					'course_id' => [
						'validate_callback' => function ($param) {
							return is_numeric($param);
						}
					]
                ],
            ],
        ]);
    }
    public function course_favorite(\WP_REST_Request $request){
        $course_id   = $request['course_id'];

        $user_id = get_current_user_id();
        if( $user_id ){
            $_mcv_favorites = get_user_meta( $user_id, '_mcv_favorites', true );
            if( !is_array($_mcv_favorites) ) $_mcv_favorites = [];

            $data = [];
            if( isset( $_mcv_favorites[$course_id] ) ){
                unset( $_mcv_favorites[$course_id] );
                $data['fav'] = false;
            }
            else{
                $_mcv_favorites[$course_id] = time();
                $data['fav'] = true;
            }

            update_user_meta( $user_id, '_mcv_favorites', $_mcv_favorites );
            return wp_send_json_success($data);
        }
        return wp_send_json_error();
    }


    public function add_student(\WP_REST_Request $request){
        $course_id   = $request['course_id'];
        $uid   = $request['uid'];
        $user = null;
        if( is_numeric( $uid ) ){
            $user = get_user_by('ID', $uid);
        }
        elseif(is_email( $uid )){
            $user = get_user_by('email', $uid);
        }
        else{
            $user = get_user_by('login', $uid);
        }
        
        if($user){
            $ed = get_user_meta( $user->ID, '_mcv_lms_enroll_course_id_'.$course_id, true );
            if( !$ed ) update_user_meta( $user->ID, '_mcv_lms_enroll_course_id_'.$course_id, time() );
            $is = get_user_meta( $user->ID, '_mcv_lms_is_student', true );
            if( !$is ) update_user_meta( $user->ID, '_mcv_lms_is_student', time() );
            return wp_send_json_success();
        }
        return wp_send_json_error();
    }

    public function remove_student(\WP_REST_Request $request){
        $course_id   = $request['course_id'];
        $uid   = $request['uid'];

        $user = get_user_by('ID', $uid);
        if($user){
            delete_user_meta( $uid, '_mcv_lms_enroll_course_id_'.$course_id );
            return wp_send_json_success();
        }
        return wp_send_json_error();
    }

    public function student_list(\WP_REST_Request $request){
        $course_id   = $request['course_id'];
        $course = get_post( $course_id );
        if( $course ){
            $metaQuery[] = [
                'key'       => '_mcv_lms_enroll_course_id_'.$course_id,
                'value'     => '1',
                'compare'   => '>',
                'type'      => 'NUMERIC'
            ];
            // $course = get_post( $course_id );
            $args = [
                'meta_query' => $metaQuery,
                'meta_key'=>'_mcv_lms_enroll_course_id_'.$course_id,
                'orderby'   => 'meta_value',
                'order'     => 'DESC',
            ];
        }
        else{
            $metaQuery = [
                [
                    'key'       => '_mcv_lms_is_student',
                    'value'     => '1',
                    'compare'   => '>',
                    'type'      => 'NUMERIC'
                ]
            ];
            $args = [
                'meta_query' => $metaQuery,
                'orderby'   => 'ID',
                'order'     => 'DESC',
            ];
        }

        $users = get_users( $args );
        $list = [];
        if( is_array( $users ) ){
            $model_order = new \MineCloudvod\Models\Order();
            foreach( $users as $user ){
                $args = [
                    'author' => $user->ID,
                    'post_status' => 'publish',
                ];
                $orders = $model_order->all($args);
                $list[] = [
                    'id' => $user->ID,
                    'username' => $user->data->display_name,
                    'email' => $user->data->user_email,
                    'enrolled_num' => count( mcv_lms_get_user_enrolled_courses( $user->ID ) ),
                    'order_num' => count( $orders ),
                    'roles' => $user->roles,
                    'registered' => $user->data->user_registered,
                    'enroll_date' => get_user_meta( $user->ID, '_mcv_lms_enroll_course_id_' . $course_id, true ),
                ];
            }
        }

        return rest_ensure_response( [
            'list' => $list,
            'page' => 1,
            'course' => $course,
        ] );
    }
}
