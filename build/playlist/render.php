<?php 
defined( 'ABSPATH' ) || exit;

$divId = sprintf('mcv_%s', md5(serialize($attributes)));

$is_rendered = mcv_is_block_rendered( $divId );
if( $is_rendered ) return ;


$plName = $attributes['plName'] ?? __('Video Playlist', 'mine-cloudvod');
$mcvTag = (int)$attributes['mcvTag'];
$show = $attributes['show'] ?? false;
$autonext = $attributes['autonext'] ?? true;
$mcvVideo = new \MineCloudvod\Models\McvVideo();
$videoList = $mcvVideo->all([
    'tax_query' => [[
        'taxonomy'  => 'mcv_video_tag',
        'field'     => 'term_id',
        'terms'     => [$mcvTag],
        'include_children' => false,
        'operator'   => 'IN',
    ]],
    'orderby' => ['meta_value_num' => 'ASC'],
    'meta_key' => 'sort_no',
]);
$videoListStr = '';
$vli = 1;
foreach ($videoList as $video) {
    $videoListStr .= '<li data-id="' . $video->ID . '" ' . ($vli == 1 ? 'class="cur"' : '') . '>' . ($vli == 1 ? '<div class="playing-icon"><svg width="16" height="16" viewBox="0 0 135 140" xmlns="http://www.w3.org/2000/svg"> <rect y="10" width="15" height="120" rx="6"> <animate attributeName="height" begin="0.5s" dur="1s" values="120;110;100;90;80;70;60;50;40;140;120" calcMode="linear" repeatCount="indefinite" /> <animate attributeName="y" begin="0.5s" dur="1s" values="10;15;20;25;30;35;40;45;50;0;10" calcMode="linear" repeatCount="indefinite" /> </rect> <rect x="30" y="10" width="15" height="120" rx="6"> <animate attributeName="height" begin="0.25s" dur="1s" values="120;110;100;90;80;70;60;50;40;140;120" calcMode="linear" repeatCount="indefinite" /> <animate attributeName="y" begin="0.25s" dur="1s" values="10;15;20;25;30;35;40;45;50;0;10" calcMode="linear" repeatCount="indefinite" /> </rect> <rect x="60" width="15" height="140" rx="6"> <animate attributeName="height" begin="0s" dur="1s" values="120;110;100;90;80;70;60;50;40;140;120" calcMode="linear" repeatCount="indefinite" /> <animate attributeName="y" begin="0s" dur="1s" values="10;15;20;25;30;35;40;45;50;0;10" calcMode="linear" repeatCount="indefinite" /> </rect> <rect x="90" y="10" width="15" height="120" rx="6"> <animate attributeName="height" begin="0.25s" dur="1s" values="120;110;100;90;80;70;60;50;40;140;120" calcMode="linear" repeatCount="indefinite" /> <animate attributeName="y" begin="0.25s" dur="1s" values="10;15;20;25;30;35;40;45;50;0;10" calcMode="linear" repeatCount="indefinite" /> </rect> <rect x="120" y="10" width="15" height="120" rx="6"> <animate attributeName="height" begin="0.5s" dur="1s" values="120;110;100;90;80;70;60;50;40;140;120" calcMode="linear" repeatCount="indefinite" /> <animate attributeName="y" begin="0.5s" dur="1s" values="10;15;20;25;30;35;40;45;50;0;10" calcMode="linear" repeatCount="indefinite" /> </rect> </svg></div>' : '') . '<span>' . $vli . '. ' . $video->post_title . '</span></li>';
    $vli++;
}
$video = '
<div id="' . $divId . '" class="mcv-videos' . ($show ? '' : ' mcv-full-width') . '">
    <div class="mcv-player">
        <div id="plyr_' . $divId . '"></div>
        <div class="wide-switch">
            <span class="btn_switch_bg"><svg viewBox="0 0 9 59" width="9" height="59"><path d="M3.8,5.1C1.7,4.3,0.2,2.4,0,0h0v5v4v41v5v3.9c0.6-1.9,2.1-3.4,4-4v0c2.9-0.7,5-3.2,5-6.3v-37  C9,8.4,6.8,5.7,3.8,5.1z"></path></svg></span>
            <i class="icon_sm icon_left_sm">
                <svg id="svg_icon_left_sm" viewBox="0 0 16 16"><path d="M6.427 8l3.284 3.284a1.01 1.01 0 0 1-1.427 1.427L4.29 8.716A1.003 1.003 0 0 1 3.995 8a1.005 1.005 0 0 1 .295-.717l3.994-3.994a1.01 1.01 0 0 1 1.427 1.427L6.427 8z" fill="#e6e6e6"></path></svg>
            </i>
            <i class="icon_sm icon_right_sm">
                <svg id="svg_icon_right_sm" viewBox="0 0 16 16"><path d="M11.71 8.716L7.716 12.71a1.01 1.01 0 0 1-1.427-1.427L9.573 8 6.289 4.716a1.01 1.01 0 0 1 1.427-1.427l3.994 3.994c.198.198.296.458.295.717.001.259-.097.518-.295.716z" fill="#e6e6e6"></path></svg>
            </i>
        </div>
    </div>
    <div class="mcv-playlist"' . ($show ? '' : ' style="display:none;"') . '>
        <div class="mcv-title">
            ' . $plName . '
            <span>共<b>' . count($videoList) . '</b>节</span>
        </div>
        <div class="mcv-playlist-ul">
            <ul>
            ' . $videoListStr . '
            </ul>
        </div>
    </div>
</div>';
global $current_user, $mcv_classes, $post;
$nonce                = wp_create_nonce('mcv-playlist-' . $current_user->ID);
$ajaxUrl = admin_url("admin-ajax.php");
$inlineScript = '
    jQuery(function(){
        jQuery("#' . $divId . ' .mcv-player .wide-switch").on("click",function(){
            jQuery("#' . $divId . ' .mcv-player .wide-switch").hide();
            jQuery("#' . $divId . ' .mcv-playlist").toggle(500, function(){
                jQuery("#' . $divId . ' .mcv-player .wide-switch").show(10);
            });
            jQuery("#' . $divId . '").toggleClass("mcv-full-width");
        });
        jQuery("#' . $divId . ' .mcv-playlist .mcv-playlist-ul li").on("click",function(){
            var playingicon = jQuery("#' . $divId . ' .mcv-playlist .mcv-playlist-ul li.cur .playing-icon");
            jQuery("#' . $divId . ' .mcv-playlist .mcv-playlist-ul li.cur").removeClass("cur");
            jQuery(this).addClass("cur").prepend(playingicon);
            jQuery("#' . $divId . ' .mcv-player .wide-switch").show(10);
            jQuery("#plyr_' . $divId . '").html("<svg xmlns=\"http://www.w3.org/2000/svg\" xmlns:xlink=\"http://www.w3.org/1999/xlink\" width=\"150px\" height=\"150px\" viewBox=\"0 0 40 40\" enable-background=\"new 0 0 40 40\" xml:space=\"preserve\" style=\"margin: auto;position: absolute;top: 0;bottom: 0;left: 0;right: 0;height: 30%;\"><path opacity=\"0.2\" fill=\"#FF6700\" d=\"M20.201,5.169c-8.254,0-14.946,6.692-14.946,14.946c0,8.255,6.692,14.946,14.946,14.946 s14.946-6.691,14.946-14.946C35.146,11.861,28.455,5.169,20.201,5.169z M20.201,31.749c-6.425,0-11.634-5.208-11.634-11.634 c0-6.425,5.209-11.634,11.634-11.634c6.425,0,11.633,5.209,11.633,11.634C31.834,26.541,26.626,31.749,20.201,31.749z\"></path><path fill=\"#FF6700\" d=\"M26.013,10.047l1.654-2.866c-2.198-1.272-4.743-2.012-7.466-2.012h0v3.312h0 C22.32,8.481,24.301,9.057,26.013,10.047z\" transform=\"rotate(42.1171 20 20)\"><animateTransform attributeType=\"xml\" attributeName=\"transform\" type=\"rotate\" from=\"0 20 20\" to=\"360 20 20\" dur=\"0.5s\" repeatCount=\"indefinite\"></animateTransform></path></svg>");
            jQuery.post("' . $ajaxUrl . '",{action:"mcv_playlist_ajax",pid:"'.$post->ID.'",nonce:"' . $nonce . '",an:"' . $autonext . '",mcvid:jQuery(this).data("id")},function(res){
                if(window.mcv_playlist_aliplayer){window.mcv_playlist_aliplayer.dispose();window.mcv_playlist_aliplayer=null;}
                if(window.mcv_playlist_tcplayer){window.mcv_playlist_tcplayer.dispose();window.mcv_playlist_tcplayer=null;}
                if(window.mcv_playlist_dplayer){window.mcv_playlist_dplayer.destroy();window.mcv_playlist_dplayer=null;}
                jQuery("#plyr_' . $divId . '").html(res.aliplayer[0]);eval(res.aliplayer[1]);
            }, "json");
        });
        jQuery("#' . $divId . ' .mcv-playlist .mcv-playlist-ul li:first").trigger("click");
    });
    ';
    wp_enqueue_style('mcv_playlist_css', MINECLOUDVOD_URL.'/build/playlist/view.css', array(), false);
    $mcv_classes->Aliplayer && $mcv_classes->Aliplayer::style_script();
    $mcv_classes->Tcplayer && $mcv_classes->Tcplayer::style_script();
    $mcv_classes->Dplayer && $mcv_classes->Dplayer::style_script();
    $mcv_classes->Audioplayer && $mcv_classes->Audioplayer::style_script();
    wp_add_inline_script('mcv_dplayer', $inlineScript);
echo $video;