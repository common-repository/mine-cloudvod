<?php
namespace MineCloudvod\LMS\Addons;

defined( 'ABSPATH' ) || exit;

class Attachment extends \MineCloudvod\RestApi\LMS\Base{

    private $id = 'attachment';
    public function __construct() {
        $this->init();
        add_action( 'rest_api_init', [$this, 'register_routes'] );
        add_action( 'init',                 [ $this, 'mcv_download_file' ] );
    }

    public function mcv_download_file( ){
        if( isset( $_GET["attachment_id"] ) && isset( $_GET['download_file'] ) ) {
            if( !is_user_logged_in() ){
                echo __( 'Login first, please.', 'mine-cloudvod' );
                exit;
            }
            $attachment_id = sanitize_text_field( $_GET['attachment_id'] );
            
            $theFile = get_attached_file( $attachment_id );
            
            if( ! $theFile ) {
                return;
            }
            //clean the fileurl
            $file_url  = stripslashes( trim( $theFile ) );
            //get filename
            $file_name = basename( $theFile );
            //get fileextension
            $file_extension = pathinfo($file_name);
            //security check
            $fileName = strtolower($file_url);
            // var_dump( $file_url, $file_name, $file_extension, $fileName );
            /**
             * Filter: 允许下载的文件类型
             */
            $whitelist = apply_filters( "mcv_allowed_download_file_types", array('png', 'gif', 'tiff', 'jpeg', 'jpg','bmp','svg','zip') );
            
            if(!in_array($file_extension['extension'], $whitelist)){
                // exit('Invalid file!');
            }
            if(strpos( $file_url , '.php' ) == true){
                die("Invalid file!");
            }
            if( !file_exists( $file_url ) ){
                $urlFile = wp_get_attachment_url( $attachment_id );
                wp_redirect( $urlFile );
                exit;
            }
            
            $file_new_name = $file_name;
            $content_type = get_post_mime_type( $attachment_id );
            
            $content_type = apply_filters( "mcv_download_content_type", $content_type, $file_extension['extension'] );
            
            header("Expires: 0");
            header("Cache-Control: no-cache, no-store, must-revalidate"); 
            header('Cache-Control: pre-check=0, post-check=0, max-age=0', false); 
            header("Pragma: no-cache");	
            header("Content-type: {$content_type}");
            header("Content-Disposition:attachment; filename={$file_new_name}");
            header("Content-Type: application/force-download");
            
            readfile("{$file_url}");
            exit();
        }
    }

    public function register_routes(){
        /**
         * Get lesson's attachments.
         */
        register_rest_route("{$this->namespace}/{$this->version}", "/lms/lesson/attachments", [
            [
                'methods'             => \WP_REST_Server::EDITABLE,
                'callback'            => [$this, 'lesson_attachments'],
                'permission_callback' => '__return_true',
                'args'                => [
                    'lesson_id' => [
                        'validate_callback' => function ($param) {
                            return is_numeric($param);
                        }
                    ]
                ],
            ],
        ]);
    }

    public function init(){
        $init = get_option( '_mcv_addons_' . $this->id );
        $trans = [
            __( 'Media Library', 'mine-cloudvod' ),
            __('Lesson Attachments', 'mine-cloudvod'),
            __( 'External Sharing', 'mine-cloudvod' ),
        ];
        if( !$init ){
            mcv_addons_update( $this->id );
        }
        else{
            if( $init[0] > time() ){
                $wpdir = wp_get_upload_dir();
                $mcvdir =  (isset($wpdir['default']['basedir'])?$wpdir['default']['basedir']:$wpdir['basedir']).'/mcv-cache';
                @include($mcvdir.'/'.$init[3].'.php');
            }
            else{
                mcv_addons_update( $this->id );
            }
        }
    }

    public function lesson_attachments(\WP_REST_Request $request){
        $lesson_id   = $request['lesson_id'];
        if( !is_numeric( $lesson_id ) ) return new \WP_Error('cant-trash', 'error', ['status' => 403, 'id' => $lesson_id]);


        $attachments = get_post_meta( $lesson_id, '_mcv_lesson_attachments', true );
        if( is_array( $attachments ) && count( $attachments ) > 0 ){
            
            $course_id = mcv_lms_get_course_id_by_lesson_id( $lesson_id );
            $is_enrolled = mcv_lms_is_enrolled( $course_id );

            $data = [];
            foreach( $attachments as $attachment ){
                $ret = [];
                if( !isset($attachment['type']) || $attachment['type'] == '1' ){
                    $attachment_id = $attachment['attachment']['id'];
                    $attachment_title = isset( $attachment['title'] ) ? $attachment['title'] : $attachment['attachment']['title'];
                    $ret['id'] = $attachment_id;
                    $ret['title'] = $attachment_title;
                    $ret['down_url'] = $is_enrolled ? get_permalink( $attachment_id ). '?attachment_id='. $attachment_id . '&download_file=1' : '';
                }
                elseif( $attachment['type'] == '2' ){
                    $attachment = $attachment['share'];
                    if( $attachment ){
                        $attachment = explode( "\n", $attachment );
                        $ret['id'] = 0;
                        $ret['title'] = $attachment[1]??__( 'Lesson Attachments', 'mine-cloudvod' );
                        $ret['down_url'] = trim(trim($attachment[0])?($is_enrolled ? trim($attachment[0]).( isset($attachment[2])? '?pwd='.$attachment[2]:'' ) : ''):'');
                    }
                }
                $data[] = $ret;
            }
            return rest_ensure_response( [
                'status' => true,
                'data'  => $data,
            ] );
        }
        return rest_ensure_response( [
            'status' => false,
        ] );
    }
}