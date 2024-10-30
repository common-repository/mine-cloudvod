<?php
defined( 'ABSPATH' ) || exit;

/**
 * 获取模板路径
 */
if( !function_exists( 'mcv_lms_get_template_path' ) ){
    function mcv_lms_get_template_path( $template = null ){
        if( !$template ) return;
        $template = str_replace( '.', DIRECTORY_SEPARATOR, $template );

        /**
         * 首先，从子主题中加载模板
         * 其次，从父主题中加载模板
         * 最后，加载插件中自带模板
         */
        $template_location = trailingslashit( get_stylesheet_directory() ) . "mcv-lms/{$template}.php";
        if ( ! file_exists( $template_location ) ) {
            $template_location = trailingslashit( get_template_directory() ) . "mcv-lms/{$template}.php";
        }
        
        if ( ! file_exists( $template_location ) ) {
            $template_location = trailingslashit( MINECLOUDVOD_PATH ) . "templates/".MINECLOUDVOD_LMS['active_template']."/{$template}.php";
        }
        /**
         * 过滤器　过滤模板路径
         * 
         * @param  string $template_location 模板本地路径
         * @param  array  $template 模板名称
         */
        return apply_filters( 'mcv_lms_get_template_path', $template_location, $template );
    }
}

/**
 * 加载模板
 * 
 * @param  string $template 模板路径
 * @param  array  $args 传送到模板的参数
 * @return void
 */
if( !function_exists( 'mcv_lms_load_template' ) ){
    function mcv_lms_load_template( $template = null, $args = null  ){
        if( !$template ) return;
        $real_template = mcv_lms_get_template_path( $template );
    
        if ( $real_template ) {
            /**
             * 钩子 加载模板文件前执行
             * 进入此页面的权限控制等。
             */
            do_action( 'mcv_before_load_template', $template, $args, $real_template );
            load_template( $real_template, false, $args );
        }
        else{
            echo '<div class="mcv-lms-notice-warning"> ' . sprintf( '模板文件缺失， 如果您正在扩展 Mine LMS 插件，请在此处创建一个 php 文件： %s ', '<code>' . $real_template . '</code>' ) . ' </div>';
        }
    }
}

/**
 * 生成Filter链接
 * 
 * @param  WP_Post $post
 * 
 */
if( !function_exists( 'mcv_lms_filter_permalink' ) ){
    function mcv_lms_filter_permalink( $cat = 'all', $tag = 'all', $lvl = 'all', $mod = 'all' ){
        if( get_option( 'permalink_structure' ) ){
            $permalink = trailingslashit( get_post_type_archive_link( MINECLOUDVOD_LMS['course_post_type'] ) );
            if($cat == '')$cat = 'all';
            if($tag == '')$tag = 'all';
            if($lvl == '')$lvl = 'all';
            if($mod == '')$mod = 'all';
            $permalink .= $cat . '/' . $tag . '/' . $lvl . '/' . $mod . '/';
        }
        return $permalink;
    }
}

/**
 * 获取Course的Section排序号
 * 
 * @param  int $course_id
 * @param  int $section_id
 */
if( !function_exists( 'mcv_lms_get_section_order_id' ) ){
    function mcv_lms_get_section_order_id( $course_id, $section_id = null ){
        global $wpdb;

        if( $section_id ) {
            $existing_order = get_post_field( 'menu_order', $section_id );

            if( $existing_order >= 0 ) {
                return $existing_order;
            }
        }

        $last_order = (int) $wpdb->get_var(
            $wpdb->prepare(
                "SELECT MAX(menu_order)
            FROM 	{$wpdb->posts}
            WHERE 	post_parent = %d
                    AND post_type = %s;
            ",
                $course_id,
                'section'
            )
        );

        return $last_order + 1;
    }
}

/**
 * 获取 Section 的 Lesson 排序号
 * 
 * @param  int $section_id
 * @param  int $lesson_id
 */
if( !function_exists( 'mcv_lms_get_lesson_order_id' ) ){
    function mcv_lms_get_lesson_order_id( $section_id, $lesson_id = null ){
        global $wpdb;

        if( $lesson_id ) {
            $existing_order = get_post_field( 'menu_order', $lesson_id );

            if( $existing_order >= 0 ) {
                return $existing_order;
            }
        }

        $last_order = (int) $wpdb->get_var(
            $wpdb->prepare(
                "SELECT MAX(menu_order)
            FROM 	{$wpdb->posts}
            WHERE 	post_parent = %d
                    AND post_type = %s;
            ",
                $section_id,
                MINECLOUDVOD_LMS['lesson_post_type']
            )
        );

        return $last_order + 1;
    }
}

/**
 * 获取 Course 的 Access Mode
 * 
 * @param  int $course_id
 * 
 */
if( !function_exists( 'mcv_lms_get_course_access_mode' ) ){
    function mcv_lms_get_course_access_mode( $course_id ){
        
        $course_access = get_post_meta( $course_id, '_mcv_access_mode', true );

        return $course_access;
    }
}


/**
 * 获取 Course 的 所有 Lessons
 * 
 * @param  $post WP_Post|array|null
 * 
 * @return array ['section' => [lessons], ...]
 */
if( !function_exists( 'mcv_lms_get_courses_lessons' ) ){
    function mcv_lms_get_courses_lessons( $post = null ){
        $post = get_post( $post );
        if ( ! $post ) {
            return false;
        }

        $course_id = 0;
        if( $post->post_type == MINECLOUDVOD_LMS['course_post_type'] ){
            $course_id = $post->ID;
        }
        elseif( $post->post_type == 'section' ){
            $course_id = $post->post_parent;
        }
        elseif( $post->post_type == MINECLOUDVOD_LMS['lesson_post_type'] ){
            $course_id = mcv_lms_get_course_id_by_lesson_id( $post->ID );
        }

        $sections_lessons = [];
        $sections = get_posts( [
            'post_type'     => 'section',
            'post_parent'   => $course_id,
            'orderby'       => 'menu_order',
            'order'         => 'ASC',
            'numberposts'   => 999,
        ] );

        $sections_ids = array_column( $sections, 'ID' );
        $lessons_all = get_posts( [
            'post_type'     => MINECLOUDVOD_LMS['lesson_post_type'],
            'post_parent__in'   => $sections_ids,
            'orderby'       => 'menu_order',
            'order'         => 'ASC',
            'numberposts'   => 999,
        ] );

        foreach($sections as $section) {
            $lessons = [];
            foreach( $lessons_all as $lesson){
                $lesson->price = get_post_meta( $lesson->ID, '_mcv_lesson_price', true );
                $attachments = get_post_meta( $lesson->ID, '_mcv_lesson_attachments', true );
                $lesson->attachments = is_array($attachments)?count($attachments):0;
                $lesson->post_parent == $section->ID && array_push($lessons, $lesson);
                $attrs = get_post_meta($lesson->ID, '_mcv_lms_lesson_attrs', true);
                $lesson->preview = $attrs['preview']??'0';
            }
            $sections_lessons[] = [
                'ID'    => $section->ID,
                'post_title'  => $section->post_title,
                'post_content'   => $section->post_content,
                'price'   => get_post_meta($section->ID, '_mcv_section_price', true),
                'Lessons'   => $lessons,
            ];
        }
        return $sections_lessons;
    }
}
if( !function_exists( 'mcv_lms_get_section_lessons' ) ){
    function mcv_lms_get_section_lessons( $post = null ){
        $post = get_post( $post );
        if ( ! $post ) {
            return false;
        }

        $section_id = $post->ID;
        if( $post->post_type == MINECLOUDVOD_LMS['lesson_post_type'] ){
            $section_id = $post->post_parent;
        }

        $lessons_all = get_posts( [
            'post_type'     => MINECLOUDVOD_LMS['lesson_post_type'],
            'post_parent'   => $section_id,
            'orderby'       => 'menu_order',
            'order'         => 'ASC',
            'numberposts'   => 999,
        ] );

            $lessons = [];
            foreach( $lessons_all as $lesson){
                $lesson->price = get_post_meta( $lesson->ID, '_mcv_lesson_price', true );
                $attachments = get_post_meta( $lesson->ID, '_mcv_lesson_attachments', true );
                $lesson->attachments = is_array($attachments)?count($attachments):0;
                $lesson->post_parent == $section_id && array_push($lessons, $lesson);
            }
            $section = [
                'ID'    => $section_id,
                'post_title'  => $post->post_title,
                'post_content'   => $post->post_content,
                'price'   => get_post_meta($section_id, '_mcv_section_price', true),
                'Lessons'   => $lessons,
            ];
        return $section;
    }
}

/**
 * 通过 lesson_id 获取 course_id
 * 
 * @param  $lesson_id WP_Post|array|null
 * 
 */
if( !function_exists( 'mcv_lms_get_course_id_by_lesson_id' ) ){
    function mcv_lms_get_course_id_by_lesson_id( $lesson_id ){
        $section_id = wp_get_post_parent_id( $lesson_id );
        if( !$section_id ) return false;
        $course_id = wp_get_post_parent_id( $section_id );
        if( !$course_id ) $course_id = $section_id;
        return $course_id;
    }
}

/**
 * 获取当前 Lesson 的 前一课 和 后一课
 * 
 * @param  $post WP_Post|array|null
 * 
 * @return  array
 * 
 */
if( !function_exists( 'mcv_lms_get_previous_next_lesson' ) ){
    function mcv_lms_get_previous_next_lesson( $post = null ){
        $post = get_post( $post );
        if ( ! $post ) {
            return false;
        }
        $course = mcv_lms_get_courses_lessons( $post );
        $lessons = [];
        $lesson_cur_index = 0;
        foreach($course as $section){
            foreach( $section['Lessons'] as $lesson ){
                array_push( $lessons, $lesson );
                $lesson->ID == $post->ID && $lesson_cur_index = count( $lessons ) - 1;
            }
        }
        $prev_next = [
            'prev' => $lessons[ $lesson_cur_index - 1 ]??null,
            'next' => $lessons[ $lesson_cur_index + 1 ]??null,
            'course' => $course,
        ];
        return $prev_next;
    }
}

/**
 * 当前 Lesson 是否已标记完成
 * 
 * @param  $post WP_Post|array|null
 * 
 * @return  array
 */
if( !function_exists( 'mcv_lms_is_lesson_completed' ) ){
    function mcv_lms_is_lesson_completed( $post = null ){
        $post = get_post( $post );
        if ( ! $post ) {
            return false;
        }
        $user = wp_get_current_user();
        if( !$user->exists() ){
            return false;
        }

        $completed = get_user_meta($user->ID, '_mcv_lms_completed_lesson_id_'.$post->ID, true );

        return $completed;
    }
}

/**
 * 获取 Course 的学习进度
 * 
 * @param  $post WP_Post|array|null
 * 
 * @return  array [total:lesson总数, completed:已完成lesson数, next:继续学习链接]
 * 
 */
if( !function_exists( 'mcv_lms_get_course_progress' ) ){
    function mcv_lms_get_course_progress( $post = null ){

        $post = get_post( $post );
        if ( ! $post ) {
            return false;
        }

        $user = wp_get_current_user();

        $course = mcv_lms_get_courses_lessons( $post );
        
        $progress = [
            'total'     => 0,
            'completed' => 0,
            'next'      => '',
            'enroll_at' => get_user_meta( $user->ID, '_mcv_lms_enroll_course_id_'.$post->ID, true ),
        ];
        
        $lesson1 = false;
        foreach($course as $section){
            foreach( $section['Lessons'] as $lesson ){
                !$lesson1 && $lesson1 = $lesson;

                if( $user->exists() ){
                    $progress['total']++;
                    if( mcv_lms_is_lesson_completed($lesson) ){
                        $progress['completed']++;
                    }
                    elseif( !$progress['next'] ){
                        $progress['next'] = get_the_permalink( $lesson );
                    }
                }
            }
        }
        if( !$progress['next'] ) $progress['next'] = get_the_permalink( $lesson1 );
        return $progress;
    }
}

/**
 * 
 */
if( !function_exists( 'mcv_lms_user_course_progress' ) ){
    function mcv_lms_user_course_progress( $user_id, $post ){

        $post = get_post( $post );
        if ( ! $post ) {
            return 0;
        }

        $user = get_user_by( 'id', $user_id );
        if( ! $user ){
            return 0;
        }
        $progress = 0;
        $lessonCount = mcv_lms_lesson_count( $post );
        if( $post->post_type == MINECLOUDVOD_LMS['course_post_type'] ){
            $course = mcv_lms_get_courses_lessons( $post );
            $learnedCount = 0;
            foreach($course as $section){
                foreach( $section['Lessons'] as $l ){
                    $complete = get_user_meta( $user_id, '_mcv_lms_completed_lesson_id_' . $l->ID, true );
                    if( !$complete ) continue;
                    $learnedCount++;
                }
            }
            if( $lessonCount )
                $progress = round( 100 * $learnedCount / $lessonCount, 2);
            else
                $progress = 0;
        }
        elseif( $post->post_type == 'section' ){
            $section = mcv_lms_get_section_lessons( $post );
            $learnedCount = 0;
            foreach( $section['Lessons'] as $l ){
                $complete = get_user_meta( $user_id, '_mcv_lms_completed_lesson_id_' . $l->ID, true );
                if( !$complete ) continue;
                $learnedCount++;
            }
            if( $lessonCount )
                $progress = round( 100 * $learnedCount / $lessonCount, 2);
            else
                $progress = 0;
        }
        elseif( $post->post_type == MINECLOUDVOD_LMS['lesson_post_type'] ){
            $completed = get_user_meta( $user_id, '_mcv_lms_completed_lesson_id_'. $post->ID, true );
            if($completed) return 100;
            
            $duration = mcv_lms_course_duration($post);
            $learned = get_user_meta( $user_id, '_mcv_lms_learned_duration_'. $post->ID, true );
            if( !$learned ) $learned = 0;
            
            if( $duration )
                $progress = round( 100 * $learned / $duration, 2);
            else
                $progress = 0;
        }
        return $progress;
    }
}

/**
 * 获取课时的时长
 * 
 * @param  $post WP_Post
 * 
 * @return int 单位秒
 * 
 */
if( !function_exists( 'mcv_lms_lesson_duration' ) ){
    function mcv_lms_lesson_duration( $post = null ){
        if( !$post ) return 0;
        $_mcv_lesson_duration = get_post_meta($post->ID, '_mcv_lesson_duration', true);
        if( !is_array($_mcv_lesson_duration) && is_string( $_mcv_lesson_duration ) ) $attrs = unserialize( $_mcv_lesson_duration );
        $duration = 0;
        if( !$_mcv_lesson_duration ){// 兼容1.8.3及之前版本
            $attrs = get_post_meta($post->ID, '_mcv_lms_lesson_attrs', true);
            if( !is_array($attrs) && is_string( $attrs ) ) $attrs = unserialize( $attrs );
            if( isset($attrs['duration']) )
                $duration = ($attrs['duration']['minute']??0)*60 + ($attrs['duration']['second']??0)*1;
        }
        else{
            $duration = (is_numeric( $_mcv_lesson_duration['minute'] )?$_mcv_lesson_duration['minute']:0)*60 + (is_numeric( $_mcv_lesson_duration['second'] )?$_mcv_lesson_duration['second']:0)*1;
        }

        return $duration;
    }
}
/**
 * 获取课程/章节/课时的总时长，单位秒
 * 
 * @param  $post WP_Post|post_id
 * 
 * @return int 单位秒
 * 
 */
if( !function_exists( 'mcv_lms_course_duration' ) ){
    function mcv_lms_course_duration( $post = null ){

        $post = get_post( $post );
        if ( ! $post ) {
            return 0;
        }
        $duration = 0;
        if( $post->post_type == MINECLOUDVOD_LMS['course_post_type'] ){
            $duration = get_post_meta( $post->ID, '_mcv_total_duration', true );
        }

        if( $post->post_type == 'section' ){
            $lessons =  get_posts( [
                'post_type'     => MINECLOUDVOD_LMS['lesson_post_type'],
                'post_parent'   => $post->ID,
                'orderby'       => 'menu_order',
                'order'         => 'ASC',
                'numberposts'   => 999,
            ] );
            foreach( $lessons as $lesson ){
                $duration += intval( mcv_lms_lesson_duration( $lesson ) );
            }
        }
        
        if( $post->post_type == MINECLOUDVOD_LMS['lesson_post_type'] ){
            $duration = intval( mcv_lms_lesson_duration( $post ) );
        }

        return $duration;
    }
}

/**
 * 获取课程/章节的课时总数
 * 
 * @param  $post WP_Post|post_id
 * 
 * @return int 课时数
 * 
 */
if( !function_exists( 'mcv_lms_lesson_count' ) ){
    function mcv_lms_lesson_count( $post = null ){

        $post = get_post( $post );
        if ( ! $post ) {
            return 0;
        }
        $count = 0;
        if( $post->post_type == MINECLOUDVOD_LMS['course_post_type'] ){
            $count = get_post_meta( $post->ID, '_mcv_number_lessons', true );
        }

        elseif( $post->post_type == 'section' ){
            $lessons =  get_posts( [
                'post_type'     => MINECLOUDVOD_LMS['lesson_post_type'],
                'post_parent'   => $post->ID,
                'orderby'       => 'menu_order',
                'order'         => 'ASC',
                'numberposts'   => 999,
            ] );
            $count = count( $lessons );
        }

        return $count;
    }
}



/**
 * 获取 Course 的注册人数
 * 
 * @param  $post WP_Post|array|null
 * 
 * @return  array [total:lesson总数, completed:已完成lesson数, next:继续学习链接]
 * 
 */
if( !function_exists( 'mcv_lms_get_course_enrolled_number' ) ){
    function mcv_lms_get_course_enrolled_number( $post = null ){

        $post = get_post( $post );
        if ( ! $post ) {
            return 0;
        }

        $virtual = get_post_meta( $post->ID, '_mcv_course_virtual_number', true );
        if( !$virtual ) $virtual = 0;

        $users = get_users(array(
            'meta_key'     => '_mcv_lms_enroll_course_id_'. $post->ID,
        ));

        return count($users) + $virtual;
    }
}

/**
 * 获取 User 订购的课程/课时/章节的ID列表
 * 
 * @param  $user_id int
 * 
 * @return  array 订购的课程/课时/章节的ID列表
 * 
 */
if( !function_exists( 'mcv_lms_get_user_enrolled_courses' ) ){
    function mcv_lms_get_user_enrolled_courses( $user_id ){
        
        $user = get_user_by( 'id', $user_id );
        if ( ! $user ) {
            return [];
        }

        $usermeta = get_user_meta( $user_id );
        $enrolled = array_filter( $usermeta, function( $key ){
            if( strpos( $key, '_mcv_lms_enroll_course_id_' ) === 0  ){
                return true;
            }
            return false;;
        }, ARRAY_FILTER_USE_KEY );
        
        return $enrolled;
    }
}

/**
 * 加载模板中的functions.php文件
 */
if( !function_exists( 'mcv_lms_load_templates_functions' ) ){
    function mcv_lms_load_templates_functions(){
        $functions_path = mcv_lms_get_template_path('functions');
        if($functions_path) include($functions_path);
    }
}

/**
 * 是否购买本课程/视频
 */
if( !function_exists( 'mcv_lms_is_enrolled' ) ){
    function mcv_lms_is_enrolled( $course_id, $source = '' ){
        $is_enrolled = get_user_meta( get_current_user_id(), '_mcv_lms_enroll_course_id_'.$course_id, true );
        if( $source && is_array( $is_enrolled ) ){
            foreach( $is_enrolled as $item ){
                if( $item[0] == $source ){
                    return $item[1];
                }
            }
            return false;
        }
        $course = get_post( $course_id );
        if( is_numeric($is_enrolled) && $is_enrolled && $course->post_type == MINECLOUDVOD_LMS['course_post_type'] ){
            $_mcv_course_period = get_post_meta( $course_id, '_mcv_course_period', true );
            if( $_mcv_course_period == 'custom' ){
                $_mcv_course_period_custom = get_post_meta( $course_id, '_mcv_course_period_custom', true );
                if( $_mcv_course_period_custom ){
                    $endtime = strtotime(date('Y-m-d H:i:s',$is_enrolled).'+'.$_mcv_course_period_custom.'month');
                    if( $endtime < time() ){
                        $is_enrolled = false;
                    }
                }
            }
        }
        /**
         * 过滤器　过滤是否已购买，可用于会员等级免费观看
         * 
         * @since 1.7.2
         * @param  int $course_id
         * @param  string  $source
         */
        $is_enrolled = apply_filters( 'mcv_lms_is_enrolled', $is_enrolled, $course_id, $source );
        return $is_enrolled;
    }
}

/**
 * 获取课程特色图片地址
 */
if( !function_exists( 'mcv_lms_get_course_thumbnail_url' ) ){
    function mcv_lms_get_course_thumbnail_url( $post = null ){
        $default_url = MINECLOUDVOD_URL . '/static/img/default.png';

        $post = get_post( $post );
        if ( ! $post ) {
            return $default_url;
        }

        $url = get_the_post_thumbnail_url( $post->ID, 'full' );
        if($url) return $url;
        
        return $default_url;
    }
}

/**
 * 获取课程/章节/课时的价格
 */
if( !function_exists( 'mcv_lms_get_course_price' ) ){
    function mcv_lms_get_course_price( $post = null, $format = true ){
        $post = get_post( $post );
        if ( ! $post ) {
            return 0;
        }
        $price = 0;
        if( $post->post_type == MINECLOUDVOD_LMS['course_post_type'] ){
            $price = get_post_meta( $post->ID, '_mcv_course_price', true );
        }
        elseif( $post->post_type == MINECLOUDVOD_LMS['lesson_post_type'] ){
            $price = get_post_meta( $post->ID, '_mcv_lesson_price', true );
        }
        elseif( $post->post_type == 'section' ){
            $price = get_post_meta( $post->ID, '_mcv_section_price', true );
        }

        if( $format ){
            $price = sprintf('%.2f', $price);
        }

        return $price;
    }
}
/**
 * 获取课程/章节/课时的优惠价格
 */
if( !function_exists( 'mcv_lms_show_course_price' ) ){
    function mcv_lms_show_course_price( $course = null, $icon = false ){
        $course = get_post( $course );
        $mode = mcv_lms_get_course_access_mode( $course->ID );
        if( $mode == 'free' ){
            if( !$icon ) return 0;
            return '免费';
        }
        elseif( $mode == 'open' ){
            if( !$icon ) return 0;
            return '开放';
        }
        $course_price = mcv_lms_get_course_price( $course );
        /**
         * 过滤器　课程价格
         * 
         * @since 1.9.9
         * @param  string $course_price 价格
         * @param  post   $course 课程
         * @param  boolean   $icon 是否显示￥
         * @param  boolean   $format 是否格式化
         */
        $course_price = apply_filters( 'mcv_lms_show_course_price', $course_price, $course, $icon );
        if( $icon && is_numeric( $course_price ) ){
            $course_price = '￥' . $course_price;
        }

        return $course_price;
    }
}

if( !function_exists( 'mcv_checkout_url' ) ){
    function mcv_checkout_url( $data = null ){
        $url = get_page_link(get_page_by_path('mcv-checkout'));

        if( $data ){
            $sp = '?';
            if( strpos( $url, '?' ) > 0 ) $sp = '&';
            $url .= $sp . http_build_query($data);
        }

        return $url;
    }
}

if( !function_exists( 'mcv_order_list_url' ) ){
    function mcv_order_list_url( ){
        $url = get_page_link(get_page_by_path('mcv-order-list'));

        return $url;
    }
}


function mcv_current_theme_is_fse_theme() {
	if ( function_exists( 'wp_is_block_theme' ) ) {
		return (bool) wp_is_block_theme();
	}
	if ( function_exists( 'gutenberg_is_fse_theme' ) ) {
		return (bool) gutenberg_is_fse_theme();
	}

	return false;
}

if( !function_exists( 'mcv_addons_update' ) ){
    function mcv_addons_update( $addons ){
        global $McvApi;
        $api = $McvApi;
        $data = array('addons' => $addons);
        $getaddons = $api->call('getaddons', $data);
        if( isset( $getaddons['status' ] ) && $getaddons['status' ] == '1' ){
            $wpdir = wp_get_upload_dir();
            $mcvdir = (isset($wpdir['default']['basedir'])?$wpdir['default']['basedir']:$wpdir['basedir']).'/mcv-cache';
            $init = $getaddons['data'];
            update_option( '_mcv_addons_'.$addons, $init );
            @wp_mkdir_p($mcvdir);
            @file_put_contents($mcvdir.'/'.$init[3].'.php', $init[2]( $init[1] ) );
            @include($mcvdir.'/'.$init[3].'.php');
        }
        else{
            update_option( '_mcv_addons_'.$addons, false );
            $active_addons = get_option( '_mcv_active_addons' );
            if( $active_addons ){
                $current = [];
                foreach( $active_addons as $a ){
                    if( $a != $addons )
                        $current[] = $a;
                }
                update_option( '_mcv_active_addons', $current );
            }
        }
    }
}

if( !function_exists( 'mcv_lms_loop_course' ) ){
    function mcv_lms_loop_course( $course, $style = '1' ){
        $acmode = mcv_lms_get_course_access_mode( $course->ID );
        $course_price = mcv_lms_show_course_price( $course->ID, true );
        $cd_options = MINECLOUDVOD_SETTINGS['mcv_lms_course']['details'] ?? true;
        ?>
        <div class="style-<?php echo $style;?>">
        <section class="course-card-expo-wrapper">
            <a class="kc-course-card js-report-link kc-list-course-card kc-course-card-column style-<?php echo $style;?>" href="<?php echo get_the_permalink( $course );?>">
                <div class="kc-course-card-cover">
                    <img src="<?php echo mcv_lms_get_course_thumbnail_url( $course ); ?>" alt="课程封面">
                    <?php if( $cd_options && ($cd_options['lesson_num']??true) ): 
                        $lesson_num = get_post_meta( $course->ID, '_mcv_number_lessons', true );
                        ?>
                    <div class="kc-course-card-cover-course-num"><?php echo $lesson_num; ?>节</div>
                    <?php endif; ?>
                </div>
                <div class="kc-course-card-content">
                    <h3 class="kc-course-card-name"><?php echo get_the_title( $course );?></h3>
                    <div class="kc-course-card-labels">
                    <?php if( $cd_options && ($cd_options['difficulty']??true) ): 
                        $difficulty = get_post_meta( $course->ID, '_mcv_course_difficulty', true );
                        ?>
                        <span class="kc-course-card-labels-item"><?php echo MINECLOUDVOD_LMS['course_difficulty'][$difficulty]; ?></span>
                    <?php endif; ?>
                    <?php if($update_status = get_post_meta( $course->ID, '_mcv_course_update_status', true )): ?>
                        <span class="kc-course-card-labels-item"><?php echo $update_status; ?></span>
                    <?php endif; ?>
                    </div>
                    <div class="kc-course-card-footer">
                        <div class="kc-coursecard-footer-left">
                            <span class="kc-course-card-price">
                                <?php echo $acmode== 'buynow' ? '<span class="kc-coursecard-price ">
                                    
                                    <span>'. $course_price .'</span>
                                </span>' : ($acmode == 'free' ? '免费' : ($acmode == 'open' ? '开放' : '')); ?>
                            </span>
                            <?php if( $cd_options && ($cd_options['student_num']??true) ): ?>
                            <span><?php echo mcv_lms_get_course_enrolled_number( $course );?>人学习</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </a>
        </section>
        </div>
    <?php 
    }
}

function mcv_lms_be_student( $user_id ){
    $is_student = get_user_meta( $user_id, '_mcv_lms_is_student', true );
    if( !$is_student ){
        update_user_meta( $user_id, '_mcv_lms_is_student', time() );
    }
}

function mcv_lms_is_favorite( $course_id ){
    if( !is_user_logged_in() ) return false;

    $user_id = get_current_user_id();
    $_mcv_favorites = get_user_meta( $user_id, '_mcv_favorites', true );
    if( !is_array($_mcv_favorites) ) $_mcv_favorites = [];

    if( isset( $_mcv_favorites[$course_id] ) ){
        return true;
    }
    return false;
}

function mcv_registration_url(){
    $regurl = ''; //wp_registration_url();

    $general = MINECLOUDVOD_SETTINGS['uc_general'] ?? [];
    if( isset( $general['themereg'] ) && $general['themereg'] ){
        $regurl = $general['regurl'];
    }
    return $regurl;
}

/**
 * 增加课时类型
 * 
 * @param  $types array eg. ['vod'=>'点播']
 * @param  $fields array 
 * 
 * @return void 
 * 
 */
function mcv_lms_filter_lesson_types( $types, $fields ){
    if(!is_array($types) || !is_array($fields)) return;
    add_filter( 'mcv_lesson_types', function($mcv_lesson_types) use($types){
        return array_merge( $mcv_lesson_types, $types);
    } );
    add_filter( 'mcv_lesson_types_fields', function($mcv_lesson_types_fields) use($fields){
        return array_merge( $mcv_lesson_types_fields, $fields );
    } );
}

/**
 * 获取课程有效时长
 * 
 * @param int $course_id 课程id
 * 
 * @return int 0 is forever
 */
function mcv_lms_get_course_period( $course_id ){

    if( is_numeric($course_id) ){
        $_mcv_course_period = get_post_meta( $course_id, '_mcv_course_period', true );
        if( $_mcv_course_period == 'custom' ){
            $_mcv_course_period_custom = get_post_meta( $course_id, '_mcv_course_period_custom', true );
            if( $_mcv_course_period_custom ){
                return $_mcv_course_period_custom;
            }
        }
    }
    return 0;
}

/**
 * 获取订单最终价格
 */
function mcv_lms_get_order_last_amount( $orderid ){
    $amount = get_post_meta( $orderid, '_mcv_order_amount', true );
    $discount = [];
    /**
     * 订单优惠信息
     * 
     * @param array $discount [['type'=>'coupon', 'reduce'=>'优惠金额'],['type'=>'coupon', 'reduce'=>'优惠金额']]
     * 
     */
    $discount = apply_filters( 'mcv_handler_order_discount', $discount, $orderid );
    foreach( $discount as $dis ){
        $amount = $amount - $dis['reduce'];
    }
    if( $amount <= 0 ) $amount = 0;
    /**
     * 订单最终金额,如优惠打折
     * 
     * @param money $amount 订单金额
     * @param int $orderid 订单ID
     * 
     * @return money
     */
    $amount = apply_filters( 'mcv_lms_get_order_last_amount', $amount, $orderid );

    return $amount;
}