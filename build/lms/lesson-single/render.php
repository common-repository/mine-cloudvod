<?php 
defined( 'ABSPATH' ) || exit;
date_default_timezone_set(wp_timezone_string());
global $post;
$course_id = mcv_lms_get_course_id_by_lesson_id( $post->ID );
$is_enrolled = mcv_lms_is_enrolled( $course_id );

// 私密课程 且 未订购
if( !is_post_publicly_viewable($course_id) && !$is_enrolled ){
    return 'Private course';
}
$course_title =  get_the_title( $course_id );
$course_link = get_the_permalink($course_id);

$access_mode = mcv_lms_get_course_access_mode( $course_id );
$course_price = mcv_lms_show_course_price( $course_id, true );
$user_id = get_current_user_id();

$is_open = $access_mode == 'open';
$is_free = $access_mode == 'free';

$course_terms = get_the_terms($course_id, 'course-category');

$is_user_logged_in = is_user_logged_in();
// 登录后显示目录是否开启
$catelog_show = get_post_meta( $course_id, '_mcv_course_catelog', true );
if( $catelog_show === "" ) $catelog_show = false;
else $catelog_show = !!$catelog_show;
// 购买后显示目录是否开启
$catelog_enroll = get_post_meta( $course_id, '_mcv_course_catelog_enroll', true );
if( $catelog_enroll === "" ) $catelog_enroll = false;
else $catelog_enroll = !!$catelog_enroll;

$next = [];
$nextflag = false;
$courses_lessons = mcv_lms_get_courses_lessons();
$lists = [];
foreach($courses_lessons as $section){
    $section_enrolled = mcv_lms_is_enrolled( $section['ID'] );
    $list = [
        'id'    => $section['ID'],
        'enrolled' => $section_enrolled,
        'title' => $section['post_title']
    ];
    $sst = false;
    foreach($section['Lessons'] as $lesson){
        if( $nextflag ){
            $next['title'] = $lesson->post_title;
            $next['link'] = get_the_permalink($lesson->ID);
            $nextflag = false;
        }
		$lesson_type = get_post_meta($lesson->ID, '_lesson_type', true);
        $attrs = get_post_meta($lesson->ID, '_mcv_lms_lesson_attrs', true);
        if( !is_array($attrs) && is_string( $attrs ) ) $attrs = unserialize( $attrs );
        $lesson_enrolled = mcv_lms_is_enrolled( $lesson->ID );
        if(!$attrs) $attrs = [];
        if(!isset($attrs['duration']) && $lesson_type=='vod') $attrs['duration'] = get_post_meta($lesson->ID, '_mcv_lesson_duration', true);
        $attachments = get_post_meta( $lesson->ID, '_mcv_lesson_attachments', true );

        $lid = $lesson->ID;
        $ltitle = $lesson->post_title;
        $url = get_the_permalink($lesson->ID);
        if( 
            ( $catelog_show && !$is_user_logged_in && (!isset($attrs['preview']) || !$attrs['preview']) ) // 开启登录后显示目录，但没有登录
            || ( $catelog_show && $is_user_logged_in && $catelog_enroll && !$lesson_enrolled && !$section_enrolled && !$is_enrolled && (!isset($attrs['preview']) || !$attrs['preview']) ) // 开启登录后显示目录，已登录，开启购买后显示目录，但未购买，且未开启试看
        ){
            $ltitle = __('Check the catalog after enrolled.', 'mine-cloudvod');
        }
        else{
            $sst = true;
        }

        $list['lessons'][] = [
            'id' => $lid,
            'title' => $ltitle,
            'url'   => $url,
            'attrs' => $attrs,
            'attachments' => is_array($attachments)?count($attachments):0,
            'lesson_type' => $lesson_type,
            'aliyun_livetime' => get_post_meta($lesson->ID, 'aliyun_livetime', true),
            'progress' => mcv_lms_user_course_progress( get_current_user_id(), $lesson ),
            'enrolled' => $lesson_enrolled,
        ];
        if( $lesson->ID == $post->ID ){
            $nextflag = true;
        }
    }
    if( !$sst ){
        $list['title'] = __('Check the catalog after enrolled.', 'mine-cloudvod');
    }
    $lists[] = $list;
}

$viewDependencies = include( MINECLOUDVOD_PATH.'/build/lms/course-single/view.asset.php' );
foreach($viewDependencies['dependencies'] as $dpc){
    wp_enqueue_style( $dpc );
}
wp_set_script_translations( 'mine-cloudvod-lesson-single-editor-script', 'mine-cloudvod' );
wp_enqueue_style( 'wp-block-library' );
wp_enqueue_global_styles();
wp_enqueue_style( 'mine-cloudvod-lesson-single-editor-style' );
wp_enqueue_script( 'jquery' );
wp_enqueue_script( 'mcv_layer' );
wp_enqueue_script( 'mine-cloudvod-lesson-single-editor-script' );
wp_enqueue_script( 'mine-cloudvod-user-script' );

// $_mcv_course_attachments = get_post_meta( $course_id, '_mcv_course_attachments', true );
// $course_attachments = [];
// if( is_array( $_mcv_course_attachments ) ):
//     foreach( $_mcv_course_attachments as $attachment ){
//         $ret = [];
//         if( !isset($attachment['type']) || $attachment['type'] == '1' ){
//             $attachment = $attachment['attachment'];
//             $ret['id'] = $attachment['id'];
//             $ret['title'] = $attachment['title'];
//             $ret['down_url'] = $is_enrolled ? get_permalink( $attachment['id'] ). '?attachment_id='. $attachment['id'] . '&download_file=1' : '';
//         }
//         elseif( $attachment['type'] == '2' ){
//             $attachment = $attachment['share'];
//             if( $attachment ){
//                 $attachment = explode( "\n", $attachment );
//                 $ret['id'] = 0;
//                 $ret['title'] = $attachment[1]??__( 'Lesson Attachments', 'mine-cloudvod' );
//                 $ret['down_url'] = $is_enrolled ? $attachment[0].( isset($attachment[2])? '?pwd='.$attachment[2]:'' ) : '';
//             }
//         }
//         $course_attachments[] = $ret;
//     }
// endif;
$catelog_no = get_post_meta( $course_id, '_mcv_course_no_type', true );
if( $catelog_no === "" ) $catelog_no = true;
else $catelog_no = !!$catelog_no;
$watermark = ['status'=>1,'text'=>get_bloginfo('name')];
if( isset( MINECLOUDVOD_SETTINGS['mcv_lms_course']['watermark'] ) ){
    $watermark = MINECLOUDVOD_SETTINGS['mcv_lms_course']['watermark'];
    $current_user = wp_get_current_user();
    if( $current_user ){
        $watermark['text'] = str_replace(['{userid}', '{username}', '{userip}', '{useremail}', '{usernickname}'], [$current_user->ID, $current_user->user_login, $_SERVER['REMOTE_ADDR'], $current_user->user_email, $current_user->display_name], $watermark['text']);
    }
    else{
        $watermark['text'] = str_replace(['{userid}', '{username}', '{userip}', '{useremail}', '{usernickname}'], '', $watermark['text']);
    }
}
if( empty( $watermark['text'] ) ) $watermark['text'] = get_bloginfo('name');
wp_localize_script( 'mine-cloudvod-lesson-single-editor-script', 'mcv_lesson_data', [
    'course'    => [
        'id'    => $course_id,
        'title' => $course_title,
        'url'   => $course_link,
        // 'attachments' => $course_attachments,
        'access_mode' => $access_mode,
        'catelog_no' => $catelog_no,
        'enrolled' => $is_enrolled,
    ],
    'sections'   => $lists, 
    'current'    => $post->ID,
    'watermark'  => $watermark
]);
$lesson_attrs = get_post_meta($post->ID, '_mcv_lms_lesson_attrs', true);
$lesson_type = get_post_meta($post->ID, '_lesson_type', true)?:'vod';

$is_live = false;
$stime = 0;
$etime = 0;
if( $lesson_type && $lesson_type == 'live' ){
    $is_live = true;
    $livetime = get_post_meta($post->ID, 'aliyun_livetime', true);
    $stime = strtotime($livetime['from']);
    $etime = strtotime($livetime['to']);
}

$sprice = mcv_lms_show_course_price($post->post_parent);
$section_enrolled = false;
if( $sprice ){
    $section_enrolled = mcv_lms_is_enrolled( $post->post_parent );
}
$lprice = get_post_meta( $post->ID, '_mcv_lesson_price', true);
$lesson_enrolled = false;
if( $lprice ){
    $lesson_enrolled = mcv_lms_is_enrolled( $post->ID );
}

$is_iframe = false;
if( isset( $_GET['iframe'] ) ){
    $is_iframe = true;
}
    show_admin_bar( false );
    remove_action('wp_head', '_admin_bar_bump_cb');

if( $user_id ){
    if( $access_mode != "buynow" ){
        //不需要购买的课程，直接添加订购时间。    
        if( !get_user_meta( $user_id, '_mcv_lms_enroll_course_id_'.$course_id, true ) ){
            mcv_order_update_items( [$course_id], $user_id );
        }
    }
}
$canplay = false;
$title = __('Check the catalog after enrolled.', 'mine-cloudvod');
if( (isset($lesson_attrs['preview']) && $lesson_attrs['preview']) || $is_enrolled || ($is_free && $user_id) || $is_open || $section_enrolled || $lesson_enrolled ){
    $canplay = true;
    $title = get_the_title();
}
?>
<div id="mcv_ketang_container" class="web index-placeholder-body<?php echo $is_iframe ? ' is-iframe' : ''; ?>">
    <div class="study-header">
        <div class="mcv-breadcrumb">
            <a href="<?php echo get_post_type_archive_link(MINECLOUDVOD_LMS['course_post_type']); ?>">全部课程</a>
            <?php if( $course_terms ): ?>
            <span class="ke-icon" style="color: rgb(161, 169, 178); font-size: 14px;">
                <svg width="1em" height="1em" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M9.636 20.435a1 1 0 0 0 1.414 0l4.95-4.95a4 4 0 0 0 0-5.656l-4.95-4.95a1 1 0 0 0-1.414 1.414l4.95 4.95a2 2 0 0 1 0 2.828l-4.95 4.95a1 1 0 0 0 0 1.414Z"
                        fill="currentColor"></path>
                </svg>
            </span>
            <?php foreach( $course_terms as $term ): ?>
            <a href="<?php echo get_term_link( $term ); ?>"><?php echo $term->name; ?></a> &nbsp;
            <?php endforeach; ?>
            <?php endif; ?>
            <span class="ke-icon" style="color: rgb(161, 169, 178); font-size: 14px;">
                <svg width="1em" height="1em" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M9.636 20.435a1 1 0 0 0 1.414 0l4.95-4.95a4 4 0 0 0 0-5.656l-4.95-4.95a1 1 0 0 0-1.414 1.414l4.95 4.95a2 2 0 0 1 0 2.828l-4.95 4.95a1 1 0 0 0 0 1.414Z"
                        fill="currentColor"></path>
                </svg>
            </span>
            <a href="<?php echo $course_link;?>" title="<?php echo $course_title;?>"><?php echo $course_title;?></a>
            <span class="ke-icon" style="color: rgb(161, 169, 178); font-size: 14px;">
                <svg width="1em" height="1em" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M9.636 20.435a1 1 0 0 0 1.414 0l4.95-4.95a4 4 0 0 0 0-5.656l-4.95-4.95a1 1 0 0 0-1.414 1.414l4.95 4.95a2 2 0 0 1 0 2.828l-4.95 4.95a1 1 0 0 0 0 1.414Z"
                        fill="currentColor"></path>
                </svg>
            </span>
            <a href="<?php the_permalink();?>" title="<?php echo $title; ?>"
                class="mcv-curlesson"><?php echo $title; ?></a>
        </div>
        <div class="operations">
            <div class="nav-comment-wrapper">
                <div class="comment header-item"><span id="mcv-course-review"></span></div>
            </div>
            <div class="header-item profile">
                <div class="s-avatar">
                    <img class="avatar-img img--full-radius"
                        src="<?php echo MINECLOUDVOD_URL.'/static/img/user.png'; ?>" alt="" width="32" height="32">
                </div>
                <ul class="profile-menu hidden">
                    <li class="li-item"><a href="<?php echo home_url(); ?>" target="_blank"
                            rel="noopener noreferrer">首页</a></li>
                    <li class="li-item"><a href="<?php echo get_page_link(get_page_by_path('mcv-my-courses')); ?>"
                            target="_blank" rel="noopener noreferrer">课程表</a></li>
                    <li class="li-item"><a href="<?php echo mcv_order_list_url(); ?>" target="_blank"
                            rel="noopener noreferrer">订单管理</a></li>
                    <li class="li-item"><a href="<?php echo get_page_link(get_page_by_path('mcv-favorites')); ?>"
                            target="_blank" rel="noopener noreferrer">我的收藏</a></li>
                </ul>
            </div>
        </div>
        <div class="mcv-progress"></div>
    </div>

    <?php if($canplay): ?>
        <?php if( $is_live ): $ntime = time(); // 直播 ?>
        <div class="ke_overlay study-body" id="mcv-ketang-main-body">
            <?php if( $ntime >= $stime && $ntime <= $etime ): // 直播中 ?>
            <div class="video-wrap mcv-video" style="height: 100%;">
                <?php the_content();?>
            </div>
            <?php elseif( $ntime < $stime ): // 未开始 ?>
            <div class="ke_overlay_content live">
                <p class="title"><?php echo $post->post_title; ?> 未开始</p>
                <p class="next">直播时间：<?php echo date( 'm月d日 H:i', $stime ) . '-' . date( 'H:i', $etime ); ?></p>

            </div>
            <?php else: // 已结束 ?>
            <div class="ke_overlay_content live">
                <p class="title">该直播任务已结束！</p>
                <?php if( count( $next ) > 0 ): ?>
                <p class="next">下节任务：<?php echo $next['title']; ?></p>
                <div class="btn-wrap"><a href="<?php echo $next['link']; ?>" target="_parent"><span
                            class="btn next-btn">下一任务</span></a></div>
                <?php endif; ?>
                <!-- <p class="download-tips">本节课有回放，点击<a target="_blank" href="#">观看回放</a></p> -->
            </div>
            <?php endif; ?>
        </div>
        <?php else: // 点播 ?>
        <div class="ke_overlay study-body <?php echo $lesson_type; ?>" id="mcv-ketang-main-body">
            <div class="mcv-anchor"></div>
            <div class="video-wrap mcv-video <?php echo $lesson_type; ?>" style="height: 100%;">
                <?php the_content();?>
            </div>
        </div>
        <?php endif; ?>
    <?php elseif($is_free): 
            $login_url = wp_login_url( get_the_permalink() );
            $register_url = mcv_registration_url();
        ?>
    <div class="ke_overlay study-body" id="mcv-ketang-main-body">
        <div class="ke_overlay_content">
            <p class="title">请登录后学习完整内容</p>
            <div class="pay-info">
                <a target="_blank" href="<?php echo get_the_permalink($course_id); ?>" class="link"
                    rel="noopener noreferrer"><?php echo $course_title; ?></a>
                <p class="pay">免费</p>
                <div class="btn-wrap">
                    <a class="btn pay-btn mcv-login" href="javascript:;">立即登录</a>
                    <a class="btn consult-btn" href="<?php echo $register_url;?>" target="_parent">注册会员</a>
                </div>
            </div>
        </div>
    </div>
    <?php else: ?>
    <div class="ke_overlay" id="mcv-ketang-main-body">
        <div class="ke_overlay_content">
            <p class="title">请付费后学习完整内容</p>
            <?php if( $lprice ): ?>
            <div class="pay-info">
                <p>
                    <span class="link" style="width:170px;">本节课时</span>
                    <a target="_blank" href="<?php echo get_the_permalink($course_id); ?>" class="link"
                        style="width:170px;" rel="noopener noreferrer">完整课程</a>
                </p>
                <p>
                    <b class="pay" style="width:170px;display: inline-block;"><?php echo $lprice; ?></b>
                    <b class="pay" style="width:170px;display: inline-block;"><?php echo $course_price; ?></b>
                </p>
                <div class="btn-wrap">
                    <a class="btn pay-btn" href="<?php echo mcv_checkout_url(['id' => $post->ID]); ?>"
                        <?php echo $is_iframe ? ' target="_blank"' : '';?>>课时购买</a>
                    <a class="btn consult-btn" href="<?php echo mcv_checkout_url(['id' => $course_id]); ?>"
                        <?php echo $is_iframe ? ' target="_blank"' : '';?>>完整购买</a>
                </div>
            </div>
            <?php else: ?>
            <div class="pay-info">
                <a target="_blank" href="<?php echo get_the_permalink($course_id); ?>" class="link"
                    rel="noopener noreferrer"><?php echo $course_title; ?></a>
                <p class="pay"><?php echo $course_price; ?></p>
                <div class="btn-wrap">
                    <a class="btn pay-btn" href="<?php echo apply_filters( 'mcv_lms_buy_link', mcv_checkout_url(['id' => $course_id]), $course_id ); ?>"
                        <?php echo $is_iframe ? ' target="_blank"' : '';?>><?php echo apply_filters( 'mcv_lms_buy_btn', __('Buy now', 'mine-cloudvod'), $course_id ); ?></a>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <div class="right-side-bar normal-task" id="right-side-bar" style="right: 0px;"></div>
</div>
<?php
do_action('mcv_lms_lesson_after', $post);
?>