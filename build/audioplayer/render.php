<?php
defined( 'ABSPATH' ) || exit;

$divId = sprintf('mcv_%s', md5(serialize($attributes)));
$is_rendered = mcv_is_block_rendered( $divId );
if( $is_rendered ) return ;

$private = $attributes['privt'] ?? false;
if($private && !is_user_logged_in()){
    return include(MINECLOUDVOD_PATH . "/templates/vod/private.php");
}
$audio      = $attributes['audio'] ?? '';
$aliyunAid  = $attributes['aliyunAid'] ?? '';
$autoplay   = filter_var( ( $attributes['autoplay'] ?? false ), FILTER_VALIDATE_BOOLEAN );
$thumbnail  = $attributes['thumbnail'] ?? false;
$title      = $attributes['title'] ?? '';
$lrc        = $attributes['lrc'] ?? '';
$mode       = $attributes['mode'] ?? 'normal';
$color      = $attributes['color'] ?? '#600060';
$bkcolor    = $attributes['bkcolor'] ?? '#800080';
$size       = $attributes['size'] ?? '66';

$modeStr = '';
$inlineStyle = '';
switch($mode){
    case 'fixed':
        $modeStr = 'fixed:true,';
        break;
    case 'mini':
        $modeStr = 'mini:true,';
        $inlineStyle .= '
            #'.$divId.'.mcv-aplayer.mcv-aplayer-narrow,#'.$divId.'.mcv-aplayer.mcv-aplayer-narrow .mcv-aplayer-body,#'.$divId.'.mcv-aplayer.mcv-aplayer-narrow .mcv-aplayer-pic{
                width:'.$size.'px;
                height:'.$size.'px;
            }
        ';
        break;
}


global $mcv_classes;
if ( mcv_is_wechat_miniprogram() ) {
    $mini_src = '';
    if( $audio ){
        $mini_src = $audio;
    }
    elseif( $aliyunAid ){
        $vod = $mcv_classes->Alivod;
        $endpoint = MINECLOUDVOD_SETTINGS['alivod']['endpoint'];
        $result = $vod->get_playurl($aliyunAid, $endpoint);
        $mini_src = $result['data']['mp4'];
    }
    $video = '<audio poster="'.$thumbnail.'" name="'.$title.'" author="" src="'.$mini_src.'" id="'.$divId.'" controls loop></audio>';
    echo $video;
    return;
}

$video = '<div id="'.$divId.'" class="aplayer"><div class="aplayer-body"><center style="line-height:66px;">'.__('Audio is loading...', 'mine-cloudvod').'</center></div></div>';

$inlineScript = '
    jQuery(function(){
        const ap = new McvAPlayer({
            container: document.getElementById("'.$divId.'"),
            audio: {
                name: "'.$title.'",
                url: "'.$audio.'",
                artist:"",
                cover: "'.$thumbnail.'",
                lrc: "'.$lrc.'"
            },
            '.( $lrc ? 'lrcType: 3,' : '' ).'
            autoplay:'.($autoplay?'true':'false').',
            theme: "'.$color.'",
            loop: "one",
            mutex: true,
            '.$modeStr.'
        });
    });
';
$inlineStyle .= '
    #'.$divId.' .mcv-aplayer-body{
        background-color:'.$bkcolor.';
    }
';
if(!$audio && $aliyunAid){
    $inlineScript = '
        jQuery(function(){
            jQuery.get("'.get_rest_url().'mine-cloudvod/v1/aliyun/vod/playurl",{vid: "'.$aliyunAid.'"}, function(data){
                
                const ap = new McvAPlayer({
                    container: document.getElementById("'.$divId.'"),
                    audio: [{
                        name: "'.$title.'",
                        url: data.data.mp4,
                        artist:"",
                        cover: "'.$thumbnail.'",
                        lrc: "'.$lrc.'"
                    }],
                    autoplay:'.($autoplay?'true':'false').',
                    theme: "'.$color.'",
                    loop: "one",
                    mutex: true,
                    '.$modeStr.'
                });
                ap.on("noticeshow", function(text){
                    console.log(text);
                    return;
                });
            }, "json");
        });
    ';
}
$inlineScript = mcv_trim($inlineScript);
$inlineStyle = mcv_trim($inlineStyle);

wp_register_style( 'mcv-aplayer-inline-style', false );
wp_enqueue_style( 'mcv-aplayer-inline-style' );
wp_add_inline_style( 'mcv-aplayer-inline-style', $inlineStyle );

$mcv_classes->Audioplayer::style_script();
wp_add_inline_script('mcv_aplayer', $inlineScript);

$video = apply_filters('mcv_filter_audioplayer', $video, $attributes, $inlineScript);

if (isset($enqueue) && !$enqueue) {
    echo '<style>' . $inlineStyle . '</style>';
}
echo $video;
if (isset($enqueue) && !$enqueue) {
    echo '<script>' . $inlineScript . '</script>';
}