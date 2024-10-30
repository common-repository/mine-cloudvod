<?php
defined( 'ABSPATH' ) || exit;
// if( !$attributes['videoId'] ) return;
global $mcv_classes;
// var_dump($mcv_classes->Tcvod->mcv_get_tcvod_mediaUrl('243791576053475886'));exit;
$player = MINECLOUDVOD_SETTINGS['tcvod']['player']??'default';
// if($player == 'dplayer'){
//     $pcfg = $attributes['pcfg'] ?? MINECLOUDVOD_SETTINGS['tcvod']['plyrconfig'] ?? 'default';
//     $taskid = $attributes['taskid'] ?? MINECLOUDVOD_SETTINGS['tcvod']['transcode'] ?? 'default';
//     $appID = $attributes['appId'] ?? MINECLOUDVOD_SETTINGS['tcvod']['appid'] ?? '0';
//     $psign = $mcv_classes->Tcvod->mcv_generate_psign(0, $appID, $attributes['videoId'], $pcfg, $taskid);
//     $dplayer_attrs = [
//         'cover'     => $attributes['cover'] ?? false,
//         'captions'  => $attributes['captions'] ?? false,
//         'markers'   => $attributes['markers'] ?? false,
//         'height'   => $attributes['height'] ?? false,
//         'minecloudvod' => [
//             'tcvod'     => [
//                 'fileID' => $attributes['videoId'],
//                 'appID' => $appID,
//                 'psign' => $psign['psign'],
//                 'type'  => 'tcvod',
//                 'defaultQuality' => 0
//             ],
//         ],
//     ];
//     if($psign['sprite']){
//         $dplayer_attrs['minecloudvod']['thumbnails_rowcol'] = [
//             'row'   => $psign['sprite']['RowCount'],
//             'col'   => $psign['sprite']['ColumnCount'],
//         ];
//     }
//     wp_register_style( 'mcv-inline-style', false );
//     wp_enqueue_style( 'mcv-inline-style' );
//     wp_add_inline_style( 'mcv-inline-style', 'img.tcp-vtt-thumbnail-img{max-width:unset !important;max-height:unset !important;}'.html_entity_decode(MINECLOUDVOD_SETTINGS['tcplayercss']) );

//     $video = do_blocks('<!-- wp:mine-cloudvod/dplayer '.json_encode($dplayer_attrs).' /-->');
    
//     echo $video;
// }
// else{
    $tcplayer = $mcv_classes->Tcplayer;
    
    $video = $mcv_classes->Tcplayer->mcv_block_tcplayer($block->parsed_block);
    echo $video;
// }