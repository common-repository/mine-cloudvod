<?php
defined( 'ABSPATH' ) || exit;

$doge_attrs = $attributes;
$doge_attrs['minecloudvod']['doge'] = [
    'vcode' => $attributes['vcode'],
    'userId' => $attributes['userId'],
];
unset( $doge_attrs['vcode'] );
unset( $doge_attrs['userId'] );

$video = do_blocks('<!-- wp:mine-cloudvod/dplayer '.json_encode($doge_attrs).' /-->');

echo $video;