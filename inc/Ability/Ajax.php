<?php
namespace MineCloudvod\Ability;
use MineCloudvod\Aliyun\Vod;

class Ajax
{
    public function __construct()
    {
        add_action('wp_ajax_mcv_mark_as_complete', array($this, 'mcv_mark_as_complete'));
    }
    
    public function mcv_mark_as_complete(){
        header('Content-type:application/json; Charset=utf-8');
        $nonce   = !empty($_POST['nonce']) ? $_POST['nonce'] : null;
        if ($nonce && !wp_verify_nonce($nonce, 'mcv_lms_nonce')) {
            wp_send_json_error(['msg' => __('Illegal request', 'mine-cloudvod')]);
        }
        $user = wp_get_current_user();
        if( !$user->exists() ){
            wp_send_json_error(['msg' => __('Illegal request', 'mine-cloudvod')]);
        }
        $lesson_id = is_numeric($_POST['id']) ? $_POST['id'] : 0;
        if( !$lesson_id ){
            wp_send_json_error(['msg' => __('Illegal request', 'mine-cloudvod')]);
        }
        update_user_meta($user->ID, '_mcv_lms_completed_lesson_id_'.$lesson_id, time() );

        $prev_next = mcv_lms_get_previous_next_lesson($lesson_id);

        $data = [];

        if( $prev_next['next'] ) 
            $data['location'] = get_the_permalink($prev_next['next']);
        else 
            $data['location'] = get_the_permalink($prev_next['course']);
        
        wp_send_json_success($data);
    }
}
