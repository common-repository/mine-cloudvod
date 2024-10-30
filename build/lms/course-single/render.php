<?php
defined( 'ABSPATH' ) || exit;

$cid = $attributes['cid']??false;

global $post;
if( $cid ){
    $post = get_post( $cid );
}

$thumbnail = mcv_lms_get_course_thumbnail_url($post);

$course_id = $post->ID;
$course_title = $post->post_title;
$access_mode = mcv_lms_get_course_access_mode( $course_id );

$course_price = mcv_lms_show_course_price( $course_id, true );
$is_enrolled = mcv_lms_is_enrolled( $course_id );

$progress = mcv_lms_get_course_progress();
$is_user_logged_in = is_user_logged_in();
// $cuser = wp_get_current_user();
$login_url = wp_login_url( $progress['next'] );
$start_url = '';
if( $access_mode == 'open' ){
    $start_url = $progress['next'];
}
elseif( $access_mode == 'free' ){
    if( $is_user_logged_in ){
        $start_url = $progress['next'];
    }
    else $start_url = '';
}
elseif( $access_mode == 'buynow' ){
    if( $is_enrolled )
        $start_url = $progress['next'];
    else
        $start_url = mcv_checkout_url( ['id' => $course_id] );
}
$start_url = apply_filters( 'mcv_lms_buy_link', $start_url, $course_id );

$str_price = '';
$btn_txt = '';
if($access_mode == 'buynow'){
    $str_price = $is_enrolled ? __('Buyed', 'mine-cloudvod') : $course_price;
    $btn_txt = $is_enrolled ? __('Start learning', 'mine-cloudvod') : apply_filters( 'mcv_lms_buy_btn', __('Buy now', 'mine-cloudvod'), $course_id );
}
else{
    $str_price = $access_mode == 'free' ? __('Free', 'mine-cloudvod') : ($access_mode == 'open' ? __('Open', 'mine-cloudvod') : '');
    $btn_txt = __('Start learning', 'mine-cloudvod');
}

$course_terms = get_the_terms($course_id, 'course-category');
$update_status = get_post_meta( $course_id, '_mcv_course_update_status', true );

// 登录后显示目录是否开启
$catelog_show = get_post_meta( $course_id, '_mcv_course_catelog', true );
if( $catelog_show === "" ) $catelog_show = false;
else $catelog_show = !!$catelog_show;
// 购买后显示目录是否开启
$catelog_enroll = get_post_meta( $course_id, '_mcv_course_catelog_enroll', true );
if( $catelog_enroll === "" ) $catelog_enroll = false;
else $catelog_enroll = !!$catelog_enroll;

$courses_lessons = mcv_lms_get_courses_lessons();
$lists = [];
$lessonCount = 0;
$duration = 0;
$lesson_list = '';
$shixue = '';
if( is_array( $courses_lessons ) ){
    foreach($courses_lessons as $section){
        $section_enrolled = mcv_lms_is_enrolled( $section['ID'] );
        $list = [
            'id'    => $section['ID'],
            'title' => $section['post_title'],
            'enrolled' => $section_enrolled,
            'price' => get_post_meta($section['ID'], '_mcv_section_price', true),
        ];
        $lessonCount += count($section['Lessons']);
        foreach($section['Lessons'] as $lesson){
            $attrs = get_post_meta($lesson->ID, '_mcv_lms_lesson_attrs', true);
            if( !is_array($attrs) && is_string( $attrs ) ) $attrs = unserialize( $attrs );
            if(!$attrs) $attrs = [];
            $lprice = get_post_meta($lesson->ID, '_mcv_lesson_price', true);
            $lesson_enrolled = mcv_lms_is_enrolled( $lesson->ID );
            if(!isset($attrs['duration'])){
                $_mcv_lesson_duration = get_post_meta($lesson->ID, '_mcv_lesson_duration', true);
                $attrs['duration'] = $_mcv_lesson_duration;
                if(isset($_mcv_lesson_duration['minute'])) $duration += intval($_mcv_lesson_duration['minute']) * 60;
                if(isset($_mcv_lesson_duration['second'])) $duration += intval($_mcv_lesson_duration['second']);
            }
            else{
                if(isset($attrs['duration']['minute'])) $duration += intval($attrs['duration']['minute']) * 60;
                if(isset($attrs['duration']['second'])) $duration += intval($attrs['duration']['second']);
            }
            
            $attachments = get_post_meta( $lesson->ID, '_mcv_lesson_attachments', true );

            $url = get_the_permalink($lesson->ID);
            if( isset($attrs['preview']) && $attrs['preview'] && !$shixue ){
                $shixue = $url;
            }
            $lid = $lesson->ID;
            $ltitle = $lesson->post_title;
            if( 
                ( $catelog_show && !$is_user_logged_in ) // 开启登录后显示目录，但没有登录
                || ( $catelog_show && $is_user_logged_in && $catelog_enroll && !$lesson_enrolled && !$section_enrolled && !$is_enrolled ) // 开启登录后显示目录，已登录，开启购买后显示目录，但未购买
            ){
                $lid = 0;
                $ltitle = __('Check the catalog after enrolled.');
                $url = get_the_permalink($course_id);
            }
            $list['lessons'][] = [
                'id' => $lid,
                'title' => $ltitle,
                'url'   => $url,
                'attrs' => $attrs,
                'lesson_type' => get_post_meta($lesson->ID, '_lesson_type', true),
                'aliyun_livetime' => get_post_meta($lesson->ID, 'aliyun_livetime', true),
                'price' => $lprice,
                'enrolled' => $lesson_enrolled,
                'attachments' => is_array($attachments)?count($attachments):0,
            ];
            $lesson_list .= '<li><a href="'.$url.'" title="'.$lesson->post_title.'">'.$lesson->post_title.'</a></li>';
        }
        $lists[] = $list;
    }
}

$viewDependencies = include( MINECLOUDVOD_PATH.'/build/lms/course-single/view.asset.php' );
wp_register_script(
    'mine-cloudvod-course-single-view-script',
    MINECLOUDVOD_URL.'/build/lms/course-single/view.js',
    array_merge($viewDependencies['dependencies'],['mcv_layer']),
    MINECLOUDVOD_VERSION,
    true
);

wp_set_script_translations( 'mine-cloudvod-course-single-view-script', 'mine-cloudvod' );
foreach($viewDependencies['dependencies'] as $dpc){
    wp_enqueue_style( $dpc );
}
wp_enqueue_style( 'wp-block-library' );
wp_enqueue_global_styles();
$colors = MINECLOUDVOD_SETTINGS['mcv_lms_general']['ketang'][0]??[
    'bgColor' => '#ffffff',
    'fontColor' => '#14171a',
    'mainColor1' => '#ff7a38',
    'mainColor2' => '#2080f7',
    'secondColor1' => '#c9d0d6',
    'secondColor2' => '#586470',
    'secondColor3' => '#666c80',
    'secondColor4' => '#3e454d',
    'secondColor5' => '#f5f8fa',
];
$styles = mcv_trim( '.mcv-global-main{
    --wp--mcv--color--background: '.$colors['bgColor'].';
    --wp--mcv--color--font: '.$colors['fontColor'].';
    --wp--mcv--color--main1: '.$colors['mainColor1'].';
    --wp--mcv--color--main2: '.$colors['mainColor2'].';
    --wp--mcv--color--second1: '.$colors['secondColor1'].';
    --wp--mcv--color--second2: '.$colors['secondColor2'].';
    --wp--mcv--color--second3: '.$colors['secondColor3'].';
    --wp--mcv--color--second4: '.$colors['secondColor4'].';
    --wp--mcv--color--second5: '.$colors['secondColor5'].';
}' );
wp_enqueue_style( 'mine-cloudvod-course-single-editor-style' );
wp_add_inline_style( 'mine-cloudvod-course-single-editor-style', $styles );
wp_enqueue_script( 'mine-cloudvod-course-single-view-script' );
wp_enqueue_script( 'mine-cloudvod-user-script' );

$_mcv_course_attachments = get_post_meta( $course_id, '_mcv_course_attachments', true );
$course_attachments = [];
if(is_array($_mcv_course_attachments)):
    foreach( $_mcv_course_attachments as $attachment ){
        $ret = [];
        if( !isset($attachment['type']) || $attachment['type'] == '1' ){
            $attachment_id = $attachment['attachment']['id'];
            $attachment_title = isset( $attachment['title'] ) ? $attachment['title'] : $attachment['attachment']['title'];
            $ret['id'] = $attachment_id;
            $ret['title'] = $attachment_title;
            $ret['down_url'] = $is_enrolled ? get_permalink( $attachment_id ). '?attachment_id='. $attachment_id . '&download_file=1' : '';
        }
        elseif( $attachment['type'] == '2' ){
            $attachment = $attachment['share'];
            if( $attachment ){
                $attachment = explode( "\n", $attachment );
                $ret['id'] = 0;
                $ret['title'] = trim($attachment[1]??__( 'Lesson Attachments', 'mine-cloudvod' ));
                $ret['down_url'] = trim(trim($attachment[0])?($is_enrolled || $access_mode == 'free' ? trim($attachment[0]).( isset($attachment[2])? '?pwd='.$attachment[2]:'' ) : ''):'');
            }
        }
        $course_attachments[] = $ret;
    }
endif;
$num1 = get_comments( [
    'post_id' => $course_id,
    'count' => true,
    'meta_key' => 'mcv_stars',
    'meta_value' => [1, 2]
] );
$num3 = get_comments( [
    'post_id' => $course_id,
    'count' => true,
    'meta_key' => 'mcv_stars',
    'meta_value' => 3
] );
$num5 = get_comments( [
    'post_id' => $course_id,
    'count' => true,
    'meta_key' => 'mcv_stars',
    'meta_value' => [4, 5]
] );
$num0 = $num1 + $num3 + $num5;
$catelog_no = get_post_meta( $course_id, '_mcv_course_no_type', true );
if( $catelog_no === "" ) $catelog_no = true;
else $catelog_no = !!$catelog_no;
wp_localize_script( 'mine-cloudvod-course-single-view-script', 'mcv_lesson_data', [
    'course'    => [
        'id'    => $course_id,
        'title' => $course_title,
        'thumb' => $thumbnail,
        'url'   => get_the_permalink($course_id),
        'content' => do_blocks($post->post_content),
        'access_mode' => $access_mode,
        'enrolled' => $is_enrolled,
        'attachments' => $course_attachments,
        'fav' => mcv_lms_is_favorite( $course_id ),
        'num' => [$num0, $num1, $num3, $num5, 10],
        'catelog_no' => $catelog_no,
        'catelog_show' => $catelog_show,
        'catelog_enroll' => $catelog_enroll,
        'price' => '￥'.mcv_lms_get_course_price($course_id),
        'lessonCount' => $lessonCount,
    ],
    'sections'  => $lists, 
    'current'   => $post->ID,
    'ckurl'     => mcv_checkout_url(),
    'showcate'  => isset(MINECLOUDVOD_SETTINGS['mcv_lms_course']['showCatelogInDetail']) && MINECLOUDVOD_SETTINGS['mcv_lms_course']['showCatelogInDetail'] == '1' ? true : false,
]);

$cd_options = MINECLOUDVOD_SETTINGS['mcv_lms_course']['details'] ?? true;
// 有效期类型
$_mcv_course_period = get_post_meta( $course_id, '_mcv_course_period', true );
if( !$_mcv_course_period || $_mcv_course_period == 'forever' ){
    $period = __('Forever', 'mine-cloudvod');
}
elseif( $_mcv_course_period == 'custom' ){
    $_mcv_course_period_custom = get_post_meta( $course_id, '_mcv_course_period_custom', true );
    $arr = [
        '1'     => __('One month', 'mine-cloudvod'),
        '2'     => __('Two months', 'mine-cloudvod'),
        '3'     => __('Three months', 'mine-cloudvod'),
        '6'     => __('Half a year', 'mine-cloudvod'),
        '12'    => __('One year', 'mine-cloudvod'),
        '24'    => __('Two years', 'mine-cloudvod'),
        '36'    => __('Three years', 'mine-cloudvod'),
        '48'    => __('Four years', 'mine-cloudvod'),
        '60'    => __('Five years', 'mine-cloudvod'),
    ];
    if( !isset( $arr[$_mcv_course_period_custom] ) ){
        $period = __('Forever', 'mine-cloudvod');
    }
    else{
        $period = $arr[$_mcv_course_period_custom];
    }
}
if($cid):
?>
    <div class="banner-wrap">
        <div class="course-banner--pay">
            <div class="course-banner-bg" style="background-image: url(&quot;<?php echo $thumbnail;?>&quot;);"></div>
            <div class="course-banner-hd">
            </div>
            <div class="course-banner-inner">
                <div class="course-banner-row course-single">
                    <div class="section-left">
                        <div class="cover-pay-wrapper" style="background-image: url(&quot;<?php echo $thumbnail;?>&quot;);width:100%;height: auto;padding-top: 56.25%;">
                            <a class="cover-mask cover-mask--trial <?php echo !$shixue && !$is_user_logged_in && $access_mode!='open' ? 'mcv-login' : '' ;?>" href="<?php echo $shixue ? $shixue : (!$is_user_logged_in && $access_mode!='open' ? 'javascript:;' : $start_url) ; ?>">
                                <div class="cover-mask-icon js-expr-btn">
                                    <span class="ke-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24" fill="none">
                                            <path d="M18.76 9.856c1.619.971 1.619 3.317 0 4.288l-9.307 5.584c-1.666 1-3.786-.2-3.786-2.144V6.415c0-1.943 2.12-3.143 3.786-2.143l9.308 5.584Z" fill="currentColor"></path>
                                        </svg>
                                    </span>
                                </div>
                                <p class="cover-mask-txt"><?php echo __('Start learning', 'mine-cloudvod'); ?></p>
                            </a>
                        </div>
                    </div>
                    <div class="section-right">
                        <a href="<?php the_permalink(); ?>"><h1 class="course-title"><?php the_title(); ?></h1></a>
                        <p style="color:#ff7a38;margin:0;padding:0;"><?php echo $str_price; ?></p>
                        <p class="course-hints" style="flex-wrap: wrap;line-height:1.6;">
                            <?php if( $cd_options && ($cd_options['lesson_num']??true) ): ?>
                            <span>
                                <span class="num"><?php echo $lessonCount; ?></span>节课时
                            </span>
                            <?php endif; ?>
                            <?php if( $cd_options && ($cd_options['hours']??true) ): ?>
                            <span>
                                <span class="num"><?php echo round($duration/60/60, 2);?></span>小时
                            </span>
                            <?php endif; ?>
                            <?php if( $cd_options && ($cd_options['student_num']??true) ): ?>
                            <span>
                                <span class="num"><?php echo mcv_lms_get_course_enrolled_number(); ?></span>人学习
                            </span>
                            <?php endif; ?>
                            <?php if( $cd_options && ($cd_options['update']??true) ): ?>
                            <span>
                                <span class="num"><?php echo get_the_modified_date('Y-m-d'); ?></span>更新
                            </span>
                            <?php endif; ?>
                            <?php if( $cd_options && ($cd_options['difficulty']??true) ): 
                                $darr = MINECLOUDVOD_LMS['course_difficulty'];
                                $difficulty = get_post_meta( $course_id, '_mcv_course_difficulty', true );
                                ?>
                            <span>
                                难度 <span class="num"><?php echo $darr[$difficulty]; ?></span>
                            </span>
                            <?php endif; ?>
                            <span>
                                <?php echo __('Validity period', 'mine-cloudvod'); ?> <span class="num"><?php echo $period; ?></span>
                            </span>
                        </p>
                        <?php do_action( 'mcv_course_attrs_after' ); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php
else:
?>
    <main class="mcv-global-main">
        <div class="banner-wrap">
            <div class="course-banner--pay">
                <div class="course-banner-bg" style="background-image: url(&quot;<?php echo $thumbnail;?>&quot;);"></div>
                <div class="course-banner-hd">
                    <div class="mcv-breadcrumb">
                        <a href="<?php echo get_post_type_archive_link($post->post_type); ?>">全部课程</a>
                        <?php if( $course_terms ): ?>
                        <span class="ke-icon" style="color: rgb(161, 169, 178); font-size: 14px;">
                            <svg width="1em" height="1em" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M9.636 20.435a1 1 0 0 0 1.414 0l4.95-4.95a4 4 0 0 0 0-5.656l-4.95-4.95a1 1 0 0 0-1.414 1.414l4.95 4.95a2 2 0 0 1 0 2.828l-4.95 4.95a1 1 0 0 0 0 1.414Z" fill="currentColor"></path>
                            </svg>
                        </span>
                        <?php foreach( $course_terms as $term ): ?>
                        <a href="<?php echo mcv_lms_filter_permalink( $term->slug ); ?>"><?php echo $term->name; ?></a> &nbsp;
                        <?php endforeach; ?>
                        <?php endif; ?>
                        <span class="ke-icon" style="color: rgb(161, 169, 178); font-size: 14px;">
                            <svg width="1em" height="1em" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M9.636 20.435a1 1 0 0 0 1.414 0l4.95-4.95a4 4 0 0 0 0-5.656l-4.95-4.95a1 1 0 0 0-1.414 1.414l4.95 4.95a2 2 0 0 1 0 2.828l-4.95 4.95a1 1 0 0 0 0 1.414Z" fill="currentColor"></path>
                            </svg>
                        </span>
                        <a href="<?php the_permalink();?>"><?php the_title(); ?></a>
                    </div>
                    <div class="section-corner">
                        <div id="mcv-course-fav"></div>
                        <div id="mcv-course-share"></div>
                    </div>
                </div>
                <div class="course-banner-inner">
                    <div class="course-banner-row">
                        <div class="section-left">
                            <div class="cover-pay-wrapper" style="background-image: url(&quot;<?php echo $thumbnail;?>&quot;);">
                                <a class="cover-mask cover-mask--trial <?php echo !$shixue && !$is_user_logged_in && $access_mode!='open' ? 'mcv-login' : '' ;?>" href="<?php echo $shixue ? $shixue : (!$is_user_logged_in && $access_mode!='open' ? 'javascript:;' : $start_url) ; ?>">
                                    <div class="cover-mask-icon js-expr-btn">
                                        <span class="ke-icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24" fill="none">
                                                <path d="M18.76 9.856c1.619.971 1.619 3.317 0 4.288l-9.307 5.584c-1.666 1-3.786-.2-3.786-2.144V6.415c0-1.943 2.12-3.143 3.786-2.143l9.308 5.584Z" fill="currentColor"></path>
                                            </svg>
                                        </span>
                                    </div>
                                    <p class="cover-mask-txt"><?php echo $shixue ? '试看' : __('Start learning', 'mine-cloudvod'); ?></p>
                                </a>
                            </div>
                        </div>
                        <div class="section-right">
                            <h1 class="course-title"><?php the_title(); ?></h1>
                            <?php if( current_user_can( 'edit_post', $course_id ) ): ?>
                            <div class="course-highlights">
                                <div class="highlight-item">
                                    <span><a href="<?php echo admin_url('post.php?post='.$course_id.'&action=edit');?>">编辑</a></span>
                                </div>
                            </div>
                            <?php endif; ?>
                            <p class="course-hints">
                                <?php if( $cd_options && ($cd_options['lesson_num']??true) ): ?>
                                <span>
                                    <span class="num"><?php echo $lessonCount; ?></span>节课时
                                </span>
                                <?php endif; ?>
                                <?php if( $cd_options && ($cd_options['hours']??true) ): ?>
                                <span>
                                    <span class="num"><?php echo round($duration/60/60, 2);?></span>小时
                                </span>
                                <?php endif; ?>
                                <?php if( $cd_options && ($cd_options['student_num']??true) ): ?>
                                <span>
                                    <span class="num"><?php echo mcv_lms_get_course_enrolled_number(); ?></span>人学习
                                </span>
                                <?php endif; ?>
                                <?php if( $cd_options && ($cd_options['update']??true) ): ?>
                                <span>
                                    <span class="num"><?php echo get_the_modified_date('Y-m-d'); ?></span>更新
                                </span>
                                <?php endif; ?>
                                <?php if( $cd_options && ($cd_options['difficulty']??true) ): 
                                    $darr = MINECLOUDVOD_LMS['course_difficulty'];
                                    $difficulty = get_post_meta( $course_id, '_mcv_course_difficulty', true );
                                    ?>
                                <span>
                                    难度 <span class="num"><?php echo $darr[$difficulty]; ?></span>
                                </span>
                                <?php endif; ?>
                                <span>
                                    <?php echo __('Validity period', 'mine-cloudvod'); ?> <span class="num"><?php echo $period; ?></span>
                                </span>
                            </p>
                            <?php do_action( 'mcv_course_attrs_after' ); ?>
                        </div>
                    </div>
                    <div class="section-apply">
                        <div class="apply-bar-workspace apply-bar" style="--bgColor:#f23f4e;">
                            <div class="apply-bar-main">
                                <div class="main-left">
                                    <div class="js-course-price normal-course course-price">
                                        <div class="course-price-info">
                                            <p id="course-price" class="price-container"><?php echo $str_price; ?></p>
                                        </div>
                                    </div>
                                    <?php if( $update_status ): ?>
                                    <p class="dividing">|</p>
                                    <div class="apply-promise"><?php echo $update_status; ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="main-right">
                                    <div class="apply-btn-box">
                                        <div>
                                            <div class="">
                                                <div class="general-btn institution-btn">
                                                    <a type="button" class="general-btn-main institution-btn-main <?php echo !$is_user_logged_in && $access_mode!='open' ? 'mcv-login' : '' ;?>" href="<?php echo !$is_user_logged_in && $access_mode!='open' ? 'javascript:;' : $start_url ; ?>">
                                                        <span class="general-btn-txt institution-btn-txt"><?php echo $btn_txt; ?></span>
                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="apply-bar-suffix" id="apply-bar-suffix">
                                <div class="basic-bar-layout-mores"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="section-main">
            <main id="mcv-course-main">
            <div class="mcv-tab-panel">
                <div role="tablist" aria-orientation="horizontal" class="components-tab-panel__tabs">
                    <button type="button" role="tab" aria-selected="true" id="tab-panel-0-tab-content" aria-controls="tab-panel-0-tab-content-view" class="components-button components-tab-panel__tabs-item tab-content is-active">课程详情</button>
                    <button type="button" role="tab" tabindex="-1" aria-selected="false" id="tab-panel-0-tab-mulu" aria-controls="tab-panel-0-tab-mulu-view" class="components-button components-tab-panel__tabs-item tab-mulu">课程目录</button>
                </div>
                <div aria-labelledby="tab-panel-0-tab-content" role="tabpanel" id="tab-panel-0-tab-content-view" class="components-tab-panel__tab-content">
                    <div class="course-catalog-ctn">
                        <section class="section detail-content">
                            <?php the_content(); ?>
                        </section>
                </div>
            </div>
            </main>
            <aside class="aside">
                <div class="recommend ">
                    <div class="recommend-box">
                        <h2 class="recommend-tt">课程推荐</h2>
                        <ul class="course-list agency">
                            <?php 
                            $recommArgs = [
                                'post_type'     => MINECLOUDVOD_LMS['course_post_type'],
                                'post_status'   => 'publish',
                                'order'         => 'DESC',
                                'numberposts'   => 3,
                            ];
                            if( is_array( $course_terms ) ){
                                $ts = [];
                                foreach( $course_terms as $t ){
                                    $ts[] = $t->term_id;
                                }
                                $recommArgs['tax_query']=[
                                    [
                                        'taxonomy'=>'course-category',
                                        'field'=>'term_id',
                                        'terms'=>$ts,
                                    ]
                                ];
                            }
                            $recomm = get_posts( $recommArgs );
                            foreach($recomm as $rc){
                                $acmode = mcv_lms_get_course_access_mode( $rc->ID );
                                $cprice = mcv_lms_show_course_price( $rc->ID, true );
                            ?>
                            <li>
                                <section class="course-card-expo-wrapper">
                                    <a class="kc-course-card js-report-link kc-course-card-row" href="<?php echo get_the_permalink($rc->ID);?>">
                                        <div class="kc-course-card-cover">
                                            <img src="<?php echo mcv_lms_get_course_thumbnail_url($rc->ID);?>" alt="课程封面" class="">
                                        </div>
                                        <div class="kc-course-card-content">
                                            <h3 class="kc-course-card-name" title="<?php echo $rc->post_title;?>"><?php echo $rc->post_title;?></h3>
                                            <div class="kc-course-card-footer">
                                                <div class="kc-coursecard-footer-left">
                                                    <span class="kc-course-card-price">
                                                        <?php echo $acmode == 'buynow' ? '<span class="kc-coursecard-price ">
                                                            <span>'. $cprice .'</span>
                                                        </span>' : ($acmode == 'free' ? __('Free', 'mine-cloudvod') : ($acmode == 'open' ? __('Open', 'mine-cloudvod') : '')); ?>
                                                    </span>
                                                    <?php if( $cd_options && ($cd_options['student_num']??true) ): ?>
                                                    <span class="kc-course-card-student"><?php echo mcv_lms_get_course_enrolled_number($rc);?>人学习</span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </section>
                            </li>
                            <?php
                            }
                            ?>
                        </ul>
                    </div>
                </div>
            </aside>
        </div>
    </main>
    <?php do_action( 'mcv_after_course_rendered' ); ?>
<?php endif; ?>