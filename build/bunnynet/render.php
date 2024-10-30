<?php
defined( 'ABSPATH' ) || exit;
global $ActiveAddons;

$config = $attributes;

$playinfo = $ActiveAddons['bunnynet']->vod->get_playinfo($attributes['libid'], $attributes['vid']);

$config['source'] = $playinfo['videoPlaylistUrl'];
if( !isset( $config['cover'] ) || !$config['cover'] ){
    $config['cover'] = $playinfo['thumbnailUrl'];
}

unset( $config['oss'] );
unset( $config['libid'] );
unset( $config['vid'] );

$video = do_blocks('<!-- wp:mine-cloudvod/dplayer '.json_encode($config).' /-->');

echo $video;