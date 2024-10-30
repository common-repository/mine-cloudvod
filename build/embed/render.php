<?php

$src = $attributes['src']??'';
$width = $attributes['width']??'100%';
$height = $attributes['height']??'500px';
$danmaku = $attributes['danmaku']??false;
$type = $attributes['type']??'unknown';
if(!$danmaku && $type == 'bilibili'){
    $src .= '&danmaku=0';
}
// sandbox="allow-top-navigation allow-same-origin allow-forms allow-scripts allow-popups"
$video = '<iframe src="'.$src.'" width="'.$width.'" height="'.$height.'" scrolling="no" border="0" frameborder="no" framespacing="0" allowfullscreen="true"  id="mcv_embed_iframe" style="max-width:100% !important;" class="is-'.$type.'" sandbox="allow-top-navigation allow-same-origin allow-scripts "></iframe>';

$video = apply_filters('mcv_filter_embedvideo', $video, $src, $width, $height);

echo $video;