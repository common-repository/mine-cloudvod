<?php
defined( 'ABSPATH' ) || exit;

$attrs = $attributes;
global $ActiveAddons;

if( isset( $ActiveAddons['qiniukodo'] ) && is_object( $ActiveAddons['qiniukodo'] ) ){
    if( !isset($attrs['key']) ) return;
    $key = $attrs['key'];
    if( isset( MINECLOUDVOD_SETTINGS['qiniu']['transcode']['status'] ) && MINECLOUDVOD_SETTINGS['qiniu']['transcode']['status'] == '1' && !empty( MINECLOUDVOD_SETTINGS['qiniu']['transcode']['style'] ) ){
        $key .= '-' . MINECLOUDVOD_SETTINGS['qiniu']['transcode']['style'];
    }
    $geturl = $ActiveAddons['qiniukodo']->call_url( $key );
    
    if( $geturl['status'] == '1' ){
        $attrs['source'] = $geturl['data'];
        unset( $attrs['key'] );
        $video = do_blocks('<!-- wp:mine-cloudvod/dplayer '.json_encode($attrs).' /-->');
        
        echo $video;
    }
}