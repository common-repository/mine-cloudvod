<?php
defined( 'ABSPATH' ) || exit;

// 课程id 章节id 课时id 视频vid
$id = sanitize_text_field( $_REQUEST['id']??'' );
//类型 course/video/其他自定义类型
$type = sanitize_text_field( $_REQUEST['type']??'course' );
// 文章id
$post_id = sanitize_text_field( $_REQUEST['post_id']??'' );
//来源链接
$r = sanitize_url( $_REQUEST['r']??'' );

//加载样式
$viewDependencies = include( MINECLOUDVOD_PATH.'/build/lms/course-checkout/view.asset.php' );
wp_register_script(
    'mine-cloudvod-course-checkout-view-script',
    MINECLOUDVOD_URL.'/build/lms/course-checkout/view.js',
    array_merge(['jquery', 'mcv_layer'], $viewDependencies['dependencies']),
    MINECLOUDVOD_VERSION,
    true
);
foreach($viewDependencies['dependencies'] as $dpc){
    wp_enqueue_style( $dpc );
}
wp_register_style(
    'mine-cloudvod-course-checkout-view-style',
    MINECLOUDVOD_URL.'/build/lms/course-checkout/view.css',
    null,
    MINECLOUDVOD_VERSION
);
wp_enqueue_style( 'mine-cloudvod-course-checkout-view-style' );
wp_enqueue_script( 'mine-cloudvod-course-checkout-view-script' );

$mcv_payment = MINECLOUDVOD_SETTINGS['mcv_payment'];
$payments = [];
if(is_array($mcv_payment)){
    foreach( $mcv_payment as $key=>$payment ){
        if( $payment['status'] ){
            $payments[] = [
                'id'    => $key,
                'name'  => $payment['name']
            ];
        }
    }
}

wp_localize_script( 'mine-cloudvod-course-checkout-view-script', 'mcv_course_data', ['payments' => $payments] );

$user_id = get_current_user_id();
$orderid = 0;
$order_info = '';
$order_price = 0;
$thumbnail = '';
$order_data = [
    'payments' => $payments,
    'defaultPayment' => $_GET['payment']??false,
];
//支付现有订单
if( isset($_REQUEST['orderid']) ){
    $orderid = sanitize_text_field($_REQUEST['orderid']);
    $order = get_post( $orderid );
    if( $order && $order->post_author == $user_id ){
        $order_data['id'] = $order->ID;
        $order_data['title'] = $order->post_title;
        $order_price = get_post_meta( $order->ID, '_mcv_order_amount', true );
        $items = get_post_meta( $order->ID, '_mcv_order_items', true );
        $mime = $order->post_mime_type;
        if(is_numeric($items[0])){
            $id = $items[0];
        }
        elseif(is_array( $items[0] )){
            $id = $items[0][1];
            $post_id = $items[0][0];
        }
        if( $mime ){
            $type = str_replace( 'mcv/', '',  $mime );
        }
        else{
            $type = 'course';
        }
    }
    else{
        echo 'Order was gone';
        return;
    }
}
elseif( !$id ){
    return;
}
$item_id = $id;

    
    $post = null;
    $course = null;
    $section = null;
    $lesson = null;
    
    
    if( $type == 'course' && is_numeric( $id ) ){
        $post = get_post( $id );
        $item_id = $post->ID;
        $order_data['title'] = $post->post_title;
        //课程
        $course = null;
        //购买的章节
        $bsection = null;
        //购买的课时
        $blesson = null;
        if( $post->post_type == MINECLOUDVOD_LMS['course_post_type'] ){
            $course = $post;
        }
        elseif( $post->post_type == MINECLOUDVOD_LMS['lesson_post_type'] ){
            $blesson = $post;
            $course_id = mcv_lms_get_course_id_by_lesson_id( $id );
            $course = get_post( $course_id );
            $order_data['title'] = $course->post_title . ' - ' . $post->post_title;
            $order_info = '<p>您正在购买课程《<a href="'.get_the_permalink($course->ID).'" target="_blank">'.$course->post_title.'</a>》中的章节 「'.$post->post_title.'」</p>';
        }
        elseif( $post->post_type == 'section' ){
            $bsection = $post;
            $course_id = $bsection->post_parent;
            $course = get_post( $course_id );
            $order_data['title'] = $course->post_title . ' - ' . $post->post_title;
            $order_info = '<p>您正在购买课程《<a href="'.get_the_permalink($course->ID).'" target="_blank">'.$course->post_title.'</a>》中的课时 「'.$post->post_title.'」</p>';
        }
    
        $thumbnail = mcv_lms_get_course_thumbnail_url( $course );
        $course_id = $course->ID;
        $course_title = $course->post_title;
        $access_mode = mcv_lms_get_course_access_mode( $course_id );
        $price = $order_price?:mcv_lms_get_course_price( $id );
        
        if( $access_mode != "buynow"  ){
            wp_safe_redirect( get_the_permalink( $course ) );
        }
        
        $course_terms = get_the_terms($course_id, 'course-category');
        $terms = null;
        if( $course_terms ){
            foreach( $course_terms as $term ){
                $terms[] = [
                    'id'    => $term->term_id,
                    'name'  => $term->name,
                    'url'   => get_term_link( $term ),
                ];
            }
        }
        
        $courses_lessons = mcv_lms_get_courses_lessons($post);
        $lists = [];
        $lessonCount = 0;
        $duration = 0;
        foreach($courses_lessons as $section){
            $list = [
                'id'    => $section['ID'],
                'title' => $section['post_title']
            ];
            $lessonCount += count($section['Lessons']);
            foreach($section['Lessons'] as $lesson){
                $attrs = get_post_meta($lesson->ID, '_mcv_lms_lesson_attrs', true);
                if( !is_array($attrs) && is_string( $attrs ) ) $attrs = unserialize( $attrs );
                if(!isset($attrs['duration'])){
                    $_mcv_lesson_duration = get_post_meta($lesson->ID, '_mcv_lesson_duration', true);
                    if(isset($_mcv_lesson_duration['minute'])) $duration += intval($_mcv_lesson_duration['minute']) * 60;
                    if(isset($_mcv_lesson_duration['second'])) $duration += intval($_mcv_lesson_duration['second']);
                }
                else{
                    if(isset($attrs['duration']['minute'])) $duration += intval($attrs['duration']['minute']) * 60;
                    if(isset($attrs['duration']['second'])) $duration += intval($attrs['duration']['second']);
                }
                
                $list['lessons'][] = [
                    'id' => $lesson->ID,
                    'title' => $lesson->post_title,
                    'url'   => get_the_permalink($lesson->ID),
                    'attrs' => $attrs,
                ];
            }
            $lists[] = $list;
        }
        
        $order_data += [
            'course'    => [
                'id'    => $course_id,
                'title' => $course_title,
                'url'   => get_the_permalink($course_id),
                'thumb' => $thumbnail,
                'terms' => $terms,
            ],
            'section'   => $bsection ? [
                'id'    => $bsection->ID,
                'title' => $bsection->post_title,
            ] : false,
            'lesson'    => $blesson ? [
                'id'    => $blesson->ID,
                'title' => $blesson->post_title,
                'url'   => get_the_permalink($blesson->ID),
            ] : false,
            'lessonCount' => $lessonCount,
            'duration' => round($duration/60/60, 2),
            'payments' => $payments,
            'defaultPayment' => $_GET['payment']??false,
            'price' => $price,
        ];
        if(!$blesson && !$bsection){
            $order_info = '<p>'.$order_data['lessonCount'].' 课时  '.$order_data['duration'].' 小时</p>';
        }
    }
    elseif( $type == 'video' ){
        $item_id = $post_id;
        $price = mcv_get_video_price( $post_id, $id );
        $post = get_post( $post_id );
        $order_data['title'] = $post->post_title;
        if( !$post ){
            return;
        }
        $order_data += [
            'type'      => $type,
            'id'        => $id,
            'post_id'   => $post_id, 
            'r'         => $r,
            'title'     => $post->post_title,
            'price'     => $price,
            'payments' => $payments,
            'defaultPayment' => $_GET['payment']??false,
        ];
    }
    else{
        do_action( 'mcv_course_checkout_'.$type, $payments );
    }
    $order_data = apply_filters( 'mcv_order_data_'.$type, $order_data, $item_id );
    
    
    if( $user_id && !$orderid ){
        $post_order = array(
            'post_title'   => $order_data['title'],
            'post_status'  => 'publish',
            'post_author'  => $user_id,
            'post_date'    => date('Y-m-d H:i:s'),
            'meta_input'   => [
                '_mcv_order_create_time'    => date('Y-m-d H:i:s'),
                '_mcv_order_status'         => 'pending',
                '_mcv_order_payment'        => '',
                '_mcv_order_amount'         => $order_data['price'],
                '_mcv_order_items' => [ $item_id ],
            ],
        );
        if( $type == 'video' ){
            $post_order['post_mime_type'] = 'mcv/video';
            $post_order['meta_input']['_mcv_order_items'] = [ [$item_id, $id] ];
        }
        /**
         * filter 过滤创建订单数据
         * 
         * @since v1.8.7
         */
        $post_order = apply_filters( 'mcv_create_order', $post_order );
        if( $type == 'package' ){
            $post_order['post_mime_type'] = 'mcv/package';
        }
    
        $model_order = new \MineCloudvod\Models\Order();
        $orderid = $model_order->create( $post_order );
        $order_data['id'] = $orderid;
    }


do_action( 'mcv_lms_before_checkout_body', $order_data );
?>
<div id="mcv-purchase-body">
    <section class="purchase">
        <div class="purchase-detail purchase-item">
            <p class="purchase-detail-title">订单信息确认</p>
            
            <?php if( $type == 'course' ): ?>
            <div class="purchase-detail-wrap">
                <img class="purchase-detail-cover" src="<?php echo $thumbnail; ?>" alt="<?php echo $order_data['title']; ?>">
                <div class="purchase-detail-content">
                    <h1 class="purchase-detail-name" title="<?php echo $order_data['title']; ?>"><?php echo $order_data['title']; ?></h1>
                    <?php if( isset( $order_data['course']['terms'] ) ) : foreach( $order_data['course']['terms'] as $term ): ?>
                    <a class="purchase-detail-agency" target="_blank" title="<?php echo $term['name']; ?>" href="<?php echo $term['url']; ?>"><?php echo $term['name']; ?></a>
                    <?php endforeach; endif; ?>
                    <p class="purchase-detail-price"><?php echo mcv_lms_show_course_price($course_id, true); ?></p>
                    <div class="purchase-detail-term">
                        <h2 class="purchase-detail-term--title">课程信息</h2>
                        <?php echo $order_info; ?>
                    </div>
                </div>
            </div>
            <?php elseif( $type == 'video' ): ?>
            <div class="purchase-detail-wrap">
                <div>
                    <h1 class="purchase-detail-name" title="<?php echo $order_data['title']; ?>"><?php echo $order_data['title']; ?></h1>
                    <p class="purchase-detail-price">¥<?php echo $order_data['price']; ?></p>
                </div>
            </div>
            <?php else: ?>
                <?php 
                /**
                 * 输出不同订单类型的展示信息
                 */
                do_action( 'mcv_checkout_info_'.$type, $order_data, $item_id );
                ?>
            <?php endif; ?>
        </div>
        <div class="purchase-price purchase-item">
            <div class="purchase-price--rows">
                <div class="purchase-price--label">支付方式</div>
                <div class="purchase-price--right">
                <?php if( !$payments ): ?>
                    <div className="purchase-price--right">请启用支付方式</div>
                <?php else: for($i = 0; $i < count( $payments ); $i++ ): $pm = $payments[$i]; ?>
                    <div class="purchase-price--channel<?php if( $i == 0 ) echo ' selected'; ?>" data-id="<?php echo $pm['id']; ?>">
                        <i class="<?php echo $pm['id']; ?>-icon"></i><?php echo $pm['name']; ?>
                    </div>
                <?php endfor; endif; ?>
                </div>
            </div>
        </div>
        <section class="purchase-bottom purchase-item">
            <div class="purchase-bottom-content">
                <?php
                do_action( 'mcv_checkout_purchase_bottom', $order_data );
                ?>
                <div class="purchase-bottom-rows">
                    <p class="purchase-bottom-label">需支付金额</p>
                    <p class="purchase-bottom-right">￥<span id="mcv-need-pay-amount"><?php echo $price; ?></span></p>
                </div>
            </div>
            <p class="f-checkbox" id="mcv-purchase-btn"><button class="im-btn purchase-bottom-btn btn-default btn-m">确认支付</button></p>
        </section>
    </section>
    <input type="hidden" value="<?php echo $orderid;?>" id="mcv_orderid" />
</div>
<?php
do_action( 'mcv_lms_after_checkout_body', $orderid );