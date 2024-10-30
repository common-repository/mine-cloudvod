<?php
namespace MineCloudvod\Integrations\Ceomax;
/**
 * 问题：集成此主题使用了不规范的代码
 * 原因：此主题没有使用规范的加载js的方法 wp_enqueue_script
 */
class Ceomax
{
    public function __construct()
    {
        $theme = wp_get_theme();
        if (strtolower($theme->get_template()) == 'ceomax'
            || strtolower($theme->get_stylesheet()) == 'ceomax'
            || strtolower($theme->get('TextDomain')) == 'ceomax'
            || strtolower($theme->get('Template')) == 'ceomax'
            ) {
            add_action('template_redirect', array($this, 'shortcode_head'));
            add_action('wp_ajax_mcv_aliplayer_ajax_ceomax', array($this, 'mcv_aliplayer_ajax_ceomax'));
            add_action('wp_ajax_nopriv_mcv_aliplayer_ajax_ceomax', array($this, 'mcv_aliplayer_ajax_ceomax'));

            add_action('wp_head', array($this, 'ceomax_head_script'), 10);
            add_filter('mcv_filter_aliplayer', array($this, 'ceomax_mcv_filter_aliplayer'), 10, 5);
            add_filter('mcv_filter_tcplayer', array($this, 'ceomax_mcv_filter_tcplayer'), 10, 4);
            add_filter('mcv_filter_embedvideo', array($this, 'ceomax_mcv_filter_embedvideo'), 10, 4);
            add_filter('mcv_filter_audioplayer', array($this, 'ceomax_mcv_filter_audioplayer'), 10, 3);
        }
    }

    public function shortcode_head()
    {
        global $post;
        if (is_singular()) {
            $my_post_video_options = get_post_meta(get_the_ID(), 'my_post_video_options', 1);

            if ($my_post_video_options && isset($my_post_video_options['video_url']) && strpos($my_post_video_options['video_url'], '[mine_cloudvod') !== false) {
                global $mcv_classes;
                $mcv_classes->Aliplayer && $mcv_classes->Aliplayer::style_script();
                $mcv_classes->Tcplayer && $mcv_classes->Tcplayer::style_script();
                $mcv_classes->Dplayer && $mcv_classes->Dplayer::style_script();
                $mcv_classes->Audioplayer && $mcv_classes->Audioplayer::style_script();
            }
        }
    }

    public function mcv_aliplayer_ajax_ceomax()
    {
        global $current_user;
        $uid = $current_user->ID;

        $nonce   = !empty($_POST['nonce']) ? sanitize_text_field($_POST['nonce']) : null;

        if ($nonce && !wp_verify_nonce($nonce, 'mcv-aliyunvod-aliplayer-' . $uid)) {
            echo json_encode(array('status' => '0', 'msg' => __('Illegal request', 'mine-cloudvod')));
            exit;
        }

        $url = !empty($_POST['url']) ? sanitize_text_field($_POST['url']) : null;
        $url = urldecode($url);
        preg_match('/\[mine_cloudvod id\=(\d*)\]/is', $url, $mcv_postid);
        $aliplayer = do_shortcode('[mine_cloudvod id=' . $mcv_postid[1] . ' from="ceomax"]');
        $aliplayer = explode('{{mcvsplit}}', $aliplayer);
        if (count($aliplayer) == 3) {
            if ($aliplayer[2] == 'aliyunvod') {
                echo json_encode(array('status' => '1', 'aliplayer' => $aliplayer));
            }
            if ($aliplayer[2] == 'qcloudvod') {
                echo json_encode(array('status' => '2', 'aliplayer' => $aliplayer));
            }
            if ($aliplayer[2] == 'embedvideo') {
                echo json_encode(array('status' => '3', 'aliplayer' => $aliplayer));
            }
            if ($aliplayer[2] == 'audioplayer') {
                echo json_encode(array('status' => '4', 'aliplayer' => $aliplayer));
            }
        }
        else{
            echo json_encode(array('status' => '0', 'aliplayer' => $aliplayer));
        }

        exit;
    }
    public function ceomax_mcv_filter_aliplayer($video, $pconfig, $components, $events, $r)
    {
        global $mcv_block_ajax_from;
        if ($mcv_block_ajax_from == 'ceomax') {
            $conn   = !empty($_POST['conn']) ? sanitize_text_field($_POST['conn']) : null;
            $video = '<style>.prism-player{padding-top:unset;}</style>';
            $video .= '{{mcvsplit}}var aliplayerconfig_' . $r . '=' . json_encode($pconfig) . ';aliplayerconfig_' . $r . '.height="100%"; ' . $components . 'window.mcv_ceomax_player=new Aliplayer(aliplayerconfig_' . $r . ', function (player) {' . $events . '});{{mcvsplit}}aliyunvod';
            $video = str_replace($pconfig['id'], 'ckplayer-video-'.$conn, $video);
        }
        return $video;
    }
    public function ceomax_mcv_filter_audioplayer($video, $parsed_block, $inlineScript)
    {
        global $mcv_block_ajax_from;
        if ($mcv_block_ajax_from == 'ceomax') {
            $video .= '{{mcvsplit}}'.$inlineScript.'{{mcvsplit}}audioplayer';
        }
        return $video;
    }
    public function ceomax_mcv_filter_tcplayer($video, $pconfig, $post_id, $parsed_block)
    {
        global $mcv_block_ajax_from;
        if ($mcv_block_ajax_from == 'ceomax') {
            $videoId = sprintf('mcv-%s', md5(serialize($parsed_block)));
            $instance = 0;
            $video .= '{{mcvsplit}}';
            $video .= 'if(jQuery("#' . $videoId . '")){var tcplayerconfig_' . $post_id . $instance . '=' . json_encode($pconfig) . ';window.mcv_ceomax_player = TCPlayer(\'' . $videoId . '\', tcplayerconfig_' . $post_id . $instance . ');}';
            $video .= '{{mcvsplit}}qcloudvod';
        }
        return $video;
    }
    public function ceomax_mcv_filter_embedvideo($video, $src, $width, $height)
    {
        global $mcv_block_ajax_from;
        if ($mcv_block_ajax_from == 'ceomax') {
            $video .= '{{mcvsplit}}';
            $video .= 'function mcv_onresize(){document.querySelector("#mcv_embed_iframe").style.height = document.querySelector(".post-style-5-video-box").clientHeight+"px";}window.onresize=mcv_onresize;mcv_onresize();';
            $video .= '{{mcvsplit}}embedvideo';
        }
        return $video;
    }

    public function ceomax_head_script()
    {
        global $current_user;
        $nonce                = wp_create_nonce('mcv-aliyunvod-aliplayer-' . $current_user->ID);
        $ajaxUrl = admin_url("admin-ajax.php");
        echo mcv_trim("<script>
            jQuery(function(){
                var conv = jQuery('.ckplayer-video').data('video');
                var conn = jQuery('.ckplayer-video').data('nonce');
                if(conv && conv.indexOf('mine-cloudvod')){
                    jQuery('.ckplayer-video').removeClass('ckplayer-video-real');
                    jQuery.post('".$ajaxUrl."', {action:'mcv_aliplayer_ajax_ceomax', nonce:'".$nonce."', url:conv, conn:conn}, function(res){
                        if(window.mcv_ceomax_player){window.mcv_ceomax_player.dispose();window.mcv_ceomax_player=null;}
                        jQuery('.ckplayer-video').empty();
                        if(res.status == '1'){jQuery('#ckplayer-video-'+conn).before(res.aliplayer[0]);ev"."al(res.aliplayer[1]);}
                        else{jQuery('#ckplayer-video-'+conn).html(res.aliplayer[0]);ev"."al(res.aliplayer[1]);}
                    }, 'json');
                }
            });
        </script>");
    }
}
