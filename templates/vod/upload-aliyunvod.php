<?php 
/**
 * template name: 前台上传到阿里云视频点播
 */
global $current_user;

$args = array(
    'tinymce'       => array(
        'toolbar1'      => 'formatselect,align,bold,italic,underline,strikethrough,|,bullist,numlist,forecolor,|',
        'toolbar2'      => '',
        'toolbar3'      => '',
    ),
    'media_buttons' => false,
    'quicktags' => false,
);
$content = '';
$editor_id = 'testeditor';
wp_editor( $content, $editor_id, $args );

wp_footer();