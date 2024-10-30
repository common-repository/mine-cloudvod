<?php
if(!defined('ABSPATH'))exit;

define('MINECLOUDVOD_SETTINGS', get_option('mcv_settings'));
if(!isset(MINECLOUDVOD_SETTINGS['cdntype']) || MINECLOUDVOD_SETTINGS['cdntype'] == 'self' || empty(MINECLOUDVOD_SETTINGS['cdnprefix'])){
    define('MINECLOUDVOD_URL', plugins_url('', dirname(__FILE__)));
}
elseif(MINECLOUDVOD_SETTINGS['cdntype'] == 'jsdelivr'){
    define('MINECLOUDVOD_URL', 'https://cdn.jsdelivr.net/wp/plugins/mine-cloudvod/tags/'.MINECLOUDVOD_VERSION);
}
elseif(MINECLOUDVOD_SETTINGS['cdntype'] == 'customize'){
    define('MINECLOUDVOD_URL', str_replace('{version}', MINECLOUDVOD_VERSION,MINECLOUDVOD_SETTINGS['cdnprefix']));
}
define('MINECLOUDVOD_ALIYUNVOD_ENDPOINT', array(
    'cn-beijing'        => __('China(Beijing)', 'mine-cloudvod'),//'华北2（北京）',
    'cn-zhangjiakou'    => __('China(Zhangjiakou)', 'mine-cloudvod'),//'华北3（张家口）',
    'cn-hangzhou'       => __('China(Hangzhou)', 'mine-cloudvod'),//'华东1（杭州）',
    'cn-shanghai'       => __('China(Shanghai)', 'mine-cloudvod'),//'华东2（上海）',
    'cn-shenzhen'       => __('China(Shenzhen)', 'mine-cloudvod'),//'华南1（深圳）',
    'cn-hongkong'       => __('China(Hongkong)', 'mine-cloudvod'),//'香港',
    'ap-northeast'      => __('Janpan(Tokyo)', 'mine-cloudvod'),//'日本（东京）',
    'ap-southeast-1'    => __('Singapore', 'mine-cloudvod'),//'新加坡',
    'ap-southeast-5'    => __('Indonesia(Jakarta)', 'mine-cloudvod'),//'印度尼西亚（雅加达）',
    'us-west-1'         => __('USA (Silicon Valley)', 'mine-cloudvod'),//'美国（硅谷）',
    'eu-west-1'         => __('U.K (London)', 'mine-cloudvod'),//'英国（伦敦）',
    'eu-central-1'      => __('Germany(Frankfurt)', 'mine-cloudvod'),//'德国（法兰克福）',
    'ap-south-1'        => __('India(Mumbai)', 'mine-cloudvod'),//'印度（孟买）'
));
define('MINECLOUDVOD_ALIPLAYER', array(
    'css' => 'https://g.alicdn.com/apsara-media-box/imp-web-player/2.19.0/skins/default/aliplayer-min.css',
    'js'  => 'https://g.alicdn.com/apsara-media-box/imp-web-player/2.19.0/aliplayer-min.js',
    'anti'  => 'https://g.alicdn.com/apsara-media-box/imp-web-player/2.19.0/hls/aliplayer-vod-anti-min.js'//防调试代码
));

define('MINECLOUDVOD_TCVOD_ENDPOINT', array(
    'ap-beijing' => '华北地区(北京)',
    'ap-chengdu' => '西南地区(成都)',
    'ap-chongqing' => '西南地区(重庆)',
    'ap-guangzhou' => '华南地区(广州)',
    'ap-hongkong' => '港澳台地区(中国香港)',
    'ap-shanghai' => '华东地区(上海)',
    'ap-shanghai-fsi' => '华东地区(上海金融)',
    'ap-shenzhen-fsi' => '华南地区(深圳金融)',
    'ap-bangkok' => '亚太东南(曼谷)',
    'ap-mumbai' => '亚太南部(孟买)',
    'ap-seoul' => '亚太东北(首尔)',
    'ap-singapore' => '亚太东南(新加坡)',
    'ap-tokyo' => '亚太东北(东京)',
    'eu-frankfurt' => '欧洲地区(法兰克福)',
    'eu-moscow' => '欧洲地区(莫斯科)',
    'na-ashburn' => '美国东部(弗吉尼亚)',
    'na-siliconvalley' => '美国西部(硅谷)',
    'na-toronto' => '北美地区(多伦多)'
));

define('MINECLOUDVOD_LMS', [
    'course_post_type'      => 'mcv_course',
    'lesson_post_type'      => 'mcv_lesson',
    'order_post_type'       => 'mcv_order',
    'active_template'       => MINECLOUDVOD_SETTINGS['mcv_lms_general']['template'] ?? 'ketang',
    'course_difficulty'     => apply_filters( 'mcv_course_difficulty', [
        '1'      => __("Beginner", "mine-cloudvod"),
        '2'      => __("Intermediate", "mine-cloudvod"),
        '3'      => __("Expert", "mine-cloudvod"),
    ] ),
    'access_mode'     => [
        'open'      => __("Open", "mine-cloudvod"),
        'free'      => __("Free", "mine-cloudvod"),
        'buynow'    => __("Buy Now", "mine-cloudvod"),
        // 'closed'    => __("Closed", "mine-cloudvod"),
    ]
]);
