<?php
namespace MineCloudvod\Aliyun;

class Aliplayer
{
    public function __construct(){
        // add_action('wp_enqueue_scripts',	array($this, 'mcv_aliplayer_scripts'));

        // add_filter('render_block_data', array($this, 'mcv_render_aliplayer'), 10, 2);
        add_action( 'mcv_add_admin_options_after_aliplayer', array( $this, 'mcv_admin_options' ) );

        add_action( 'init',     [ $this, 'mcv_register_block'] );
    }

    public function mcv_register_block(){
        wp_register_script(
            'mcv_alivod_sdk',
            MINECLOUDVOD_URL.'/static/aliyun/upload-js-sdk/aliyun-upload-sdk-1.5.0.min.js',
            null,
            MINECLOUDVOD_VERSION,
            true
        );
        wp_register_script(
            'mcv_alivod_es6-promise',
            MINECLOUDVOD_URL.'/static/aliyun/upload-js-sdk/lib/es6-promise.min.js',
            null,
            MINECLOUDVOD_VERSION,
            true
        );
        wp_register_script(
            'mcv_alivod_oss',
            MINECLOUDVOD_URL.'/static/aliyun/upload-js-sdk/lib/aliyun-oss-sdk-5.3.1.min.js',
            null,
            MINECLOUDVOD_VERSION,
            true
        );


        register_block_type( MINECLOUDVOD_PATH . '/build/aliplayer/');

        $uid = get_current_user_id();
        wp_add_inline_script('mcv_alivod_sdk','var mcv_alivod_config={endpoint:"'.(MINECLOUDVOD_SETTINGS['alivod']['endpoint']??'').'",userId:"'.(MINECLOUDVOD_SETTINGS['alivod']['userId']??'').'",nonce:"'.wp_create_nonce('mcv-aliyunvod-'.$uid).'",down_snapshot:'.(isset(MINECLOUDVOD_SETTINGS['alivod']['down_snapshot'])&&MINECLOUDVOD_SETTINGS['alivod']['down_snapshot']?MINECLOUDVOD_SETTINGS['alivod']['down_snapshot']:'false').',sdk:'.(MINECLOUDVOD_SETTINGS['alivod']['accessKeyID']??MINECLOUDVOD_SETTINGS['alivod']['accessKeyID']??false ? 'true' : 'false').',aliyun_config_url:"'.admin_url('/admin.php?page=mcv-options#tab='.str_replace(' ', '-', strtolower(urlencode(__('Alibaba Cloud', 'mine-cloudvod'))))).'pro"};var mcv_aliplayer_config={slide:'.(!empty(MINECLOUDVOD_SETTINGS['aliplayer_slide']['status'])?'true':'false').'};var mcv_nonce={ajaxUrl:"'.admin_url("admin-ajax.php").'",et:"'.wp_create_nonce('mcv_sync_endtime').'",endtime:'.strtotime(MINECLOUDVOD_SETTINGS['endtime']).', buynow:"'.admin_url('/admin.php?page=mcv-options#tab='.str_replace(' ', '-', strtolower(urlencode(__('Purchase time', 'mine-cloudvod'))))).'", restRootUrl:"'.get_rest_url().'"};');
    }

    public function mcv_aliplayer_scripts(){
        global $post;
        $er = false;
        if( has_block('mine-cloudvod/aliyun-vod', $post) || has_block('mine-cloudvod/aliplayer', $post)){
            $blocks = parse_blocks($post->post_content);
            foreach($blocks as $block){
                if($this->mcv_check_block_refer($block)){
                    $er = true;
                    break;
                }
            }
        }
        if(!$er) echo '<meta name="referrer" content="unsafe-url">';
    }
    public function mcv_check_block_refer($block){
        if($block['blockName'] == 'mine-cloudvod/aliyun-vod' || $block['blockName'] == 'mine-cloudvod/aliplayer'){
            if( isset($block['attrs']['referrer']) && $block['attrs']['referrer'] ){
                echo '<meta name="referrer" content="'.$block['attrs']['referrer'].'">';
                return true;
            }
        }
    }

    public function mcv_render_aliplayer($parsed_block, $source_block){
        if($parsed_block['blockName'] == "mine-cloudvod/aliplayer"){
            $video = $this->mcv_block_aliplayer($parsed_block);
            if($video){
                $parsed_block['innerContent'][0] = $video;
            }
        }
        return $parsed_block;
    }

    public function mcv_block_aliplayer($parsed_block, $enqueue = true){
        $attributes = $parsed_block['attrs'];

        ob_start();
        include(MINECLOUDVOD_PATH.'/build/aliplayer/render.php');
        $video = ob_get_clean();
        
        return $video;
    }
    public static function style_script(){
        wp_register_script(
            'mcv_aliplayer_before',
            MINECLOUDVOD_ALIPLAYER['js'],
            array( 'jquery','mcv_layer' ),
            MINECLOUDVOD_VERSION,
            true
        );
        wp_register_script(
            'mcv_aliplayer',
            MINECLOUDVOD_URL.'/static/aliyun/aliplayercomponents-1.0.6.min.js',
            array( 'mcv_aliplayer_before' ),
            MINECLOUDVOD_VERSION,
            true
        );
        wp_register_style(
            'mcv_aliplayer_css',
            MINECLOUDVOD_ALIPLAYER['css'], 
            null,
            MINECLOUDVOD_VERSION
        );
        wp_register_style(
            'mcv_aliplayer_view_css',
            MINECLOUDVOD_URL.'/build/aliplayer/view.css',
            [ 'mcv_aliplayer_css' ],
            MINECLOUDVOD_VERSION
        );
        $slideStyle = '';
        if (isset(MINECLOUDVOD_SETTINGS['aliplayer_slide']['status']) && MINECLOUDVOD_SETTINGS['aliplayer_slide']['status']) {
            if(isset(MINECLOUDVOD_SETTINGS['aliplayer_slide']['duration']) && MINECLOUDVOD_SETTINGS['aliplayer_slide']['duration']){
                $slideStyle .= '.bullet-screen{' . MINECLOUDVOD_SETTINGS['aliplayer_slide']['style'] . 'animation-duration: '.MINECLOUDVOD_SETTINGS['aliplayer_slide']['duration'].'s !important;' . '}';
            }
            else{
                $slideStyle .= '.bullet-screen{' . MINECLOUDVOD_SETTINGS['aliplayer_slide']['style'] . '}';
            }
        }
        $sticky = '';
        if (isset(MINECLOUDVOD_SETTINGS['aliplayer_sticky']['status']) && MINECLOUDVOD_SETTINGS['aliplayer_sticky']['status']) {
            $sticky_position = 'right:5px;bottom:5px;';
            switch (MINECLOUDVOD_SETTINGS['aliplayer_sticky']['position']) {
                case 'rt':
                    $sticky_position = 'right:5px;top:5px;';
                    break;
                case 'lb':
                    $sticky_position = 'left:5px;bottom:5px;';
                    break;
                case 'lt':
                    $sticky_position = 'left:5px;top:5px;';
                    break;
            }
            $width_pc       = 35;
            $width_tablet   = 50;
            $width_mobile   = 90;
            if (isset(MINECLOUDVOD_SETTINGS['aliplayer_sticky']['width'])) {
                $width_pc       = MINECLOUDVOD_SETTINGS['aliplayer_sticky']['width']['pc'];
                $width_tablet   = MINECLOUDVOD_SETTINGS['aliplayer_sticky']['width']['tablet'];
                $width_mobile   = MINECLOUDVOD_SETTINGS['aliplayer_sticky']['width']['mobile'];
            }
            $height_pc      = $width_pc     * 0.5625;
            $height_tablet  = $width_tablet * 0.5625;
            $height_mobile  = $width_mobile * 0.5625;

            $sticky = '.mcv-fixed{position:fixed;z-index:99999;width:' . $width_pc . '% !important;height:auto !important;padding-top:' . $height_pc . '%;' . $sticky_position . '-webkit-animation: fadeInDown .5s .2s ease both; -moz-animation: fadeInDown .5s .2s ease both;}@keyframes fade-in {0% {opacity: 0;}40% {opacity: 0;}100% {opacity: 1;}}@-webkit-keyframes fade-in { 0% {opacity: 0;}  40% {opacity: 0;}100% {opacity: 1;}}@-webkit-keyframes fadeInDown{0%{opacity: 0; -webkit-transform: translateY(-10px);} 100%{opacity: 1; -webkit-transform: translateY(0);}}@-moz-keyframes fadeInDown{0%{opacity: 0; -moz-transform: translateY(-10px);} 100%{opacity: 1; -moz-transform: translateY(0);}}@media (max-width: 1024px) {.mcv-fixed{width:' . $width_tablet . '% !important;padding-top:' . $height_tablet . '%;}}@media (max-width: 450px) {.mcv-fixed{width:' . $width_mobile . '% !important;padding-top:' . $height_mobile . '%;}}
            .mcv-fixed .prism-controlbar,.mcv-fixed .preview-component-tip,.mcv-fixed .memory-play-wrap{display:none !important;}
            ';
        }
        $inlineStyle = '';
        if (isset(MINECLOUDVOD_SETTINGS['aliplayerconfig']['controlColor']) && MINECLOUDVOD_SETTINGS['aliplayerconfig']['controlColor']) {
            $inlineStyle .= '.prism-player .prism-controlbar .prism-controlbar-bg{background:'.MINECLOUDVOD_SETTINGS['aliplayerconfig']['controlColor'].'}';
        }
        $inlineStyle .= html_entity_decode(MINECLOUDVOD_SETTINGS['aliplayercss'] ?? '.prism-player{width: 100%;height: auto;padding-top: 56.25%;}');
        $inlineStyle .=  $slideStyle ;
        $inlineStyle .= $sticky;
        $inlineStyle = mcv_trim($inlineStyle);
        wp_enqueue_script( 'mcv_aliplayer' );
        wp_enqueue_style( 'mcv_aliplayer_view_css' );
        wp_add_inline_style('mcv_aliplayer_view_css', $inlineStyle);
    }
    public function mcv_admin_options(){
        $prefix = 'mcv_settings';
        include MINECLOUDVOD_PATH . '/inc/options/aliplayer.php';
        include MINECLOUDVOD_PATH . '/inc/options/aliplayer_components.php';
    }
}
