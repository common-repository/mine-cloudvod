<?php
defined( 'ABSPATH' ) || exit;

$user_id = get_current_user_id();

if( !$user_id ) {
    return;
}
wp_enqueue_style( 'mine-cloudvod-order-list-editor-style' );
$model_order = new \MineCloudvod\Models\Order();
if( isset($_GET['del']) && is_numeric($_GET['del']) ){
    $order_id = sanitize_text_field( $_GET['del'] );
    $order = get_post( $order_id );
    if( $order && ($order->post_status == 'publish' || $order->post_status == 'draft') ){
        $order_status = get_post_meta( $order->ID, '_mcv_order_status', true );
        if( $order_status != 'payed' ){
            wp_update_post( [
                'ID' => $order->ID,
                'post_status' => 'trash'
            ] );
        }
    }
    unset($order);
}
$user_orders = get_posts( [
    'author' => $user_id,
    'post_type' => MINECLOUDVOD_LMS['order_post_type'],
    'post_status' => ['publish', 'draft'],
    'posts_per_page' => 10,
] );
?>
<div id="mcv-order-list-body">
    <section class="mcv-uc">
        <aside class="mcv-uc-left">
            <div class="mcv-nav-area">
                <ul class="mcv-nav">
                    <li class="mcv-nav-item"><a title="课程表" href="<?php echo get_page_link(get_page_by_path('mcv-my-courses')); ?>">课程表</a></li>
                    <li class="mcv-nav-item"><a title="订单管理" href="<?php echo mcv_order_list_url(); ?>" class="active">订单管理</a></li>
                    <li class="mcv-nav-item"><a title="我的收藏" href="<?php echo get_page_link(get_page_by_path('mcv-favorites')); ?>">我的收藏</a></li>
                </ul>
            </div>
        </aside>
        <main class="mcv-uc-main">
            <div class="order-component">
                <div class="tabs">
                    <div class="tab">
                        <a href="javascript:void(0);" class="active">全部订单</a>
                    </div>
                </div>
                <div class="flex-list">
                    <div class="flex-list-header">
                        <div class="flex-row">
                            <div class="flex-cell first">课程订单</div>
                            <div class="flex-cell">价格</div>
                            <div class="flex-cell">状态</div>
                            <div class="flex-cell">操作</div>
                        </div>
                    </div>
                    <?php foreach( $user_orders as $order ):
                        $price = get_post_meta( $order->ID, '_mcv_order_amount', true );
                        $_mcv_order_create_time = get_post_meta( $order->ID, '_mcv_order_create_time', true );
                        $order_status = get_post_meta( $order->ID, '_mcv_order_status', true );
                        $_mcv_order_items = get_post_meta( $order->ID, '_mcv_order_items', true );
                    ?>
                    <div class="flex-list-item">
                        <div class="flex-row head">
                            <div class="time"><?php echo $_mcv_order_create_time; ?></div>
                            <div class="order-id">订单号：<?php echo $order->ID; ?></div>
                            <?php if( $order_status != 'payed' ): ?>
                            <a href="<?php echo mcv_order_list_url().'?del='.$order->ID; ?>" class="link icon delete"</a>
                            <?php endif; ?>
                        </div>
                        <div class="flex-row content">
                            <div class="flex-cell first cover">
                                <?php if( $order->post_mime_type == '' ):
                                    $course_id = $_mcv_order_items[0];
                                    $course = get_post( $course_id );
                                    $course_link = get_the_permalink( $course_id );
                                    $course_terms = get_the_terms($course_id, 'course-category');
                                    $terms = '';
                                    if( $course_terms )foreach( $course_terms as $term ){
                                        $terms .= $term->name . '/';
                                    }
                                    $terms = trim($terms, '/');
                                ?>
                                <a href="<?php echo $course_link; ?>" class="link js-report-link" target="_blank">
                                    <img src="<?php echo mcv_lms_get_course_thumbnail_url( $course ); ?>" alt="课程封面" />
                                    <div class="title">
                                        <span
                                            title="<?php echo $course->post_title; ?>"><?php echo $course->post_title; ?></span>
                                        <div class="sub">
                                            <span><?php echo $terms; ?></span>
                                        </div>
                                    </div>
                                </a>
                                <?php elseif( $order->post_mime_type == 'mcv/package' ): 
                                    $pkg_id =  $_mcv_order_items[0];
                                    $courses = get_post_meta( $pkg_id, '_mcv_pkg_cid', true );
                                ?>
                                <a href="<?php echo get_the_permalink($courses[0]); ?>" class="link js-report-link">
                                    <div class="title">
                                        <span title="<?php echo $order->post_title; ?>"><?php echo $order->post_title; ?></span>
                                    </div>
                                </a>
                                <?php elseif( $order->post_mime_type == 'mcv/video' ): 
                                    $video_id =  $_mcv_order_items[0][0];
                                    $video = get_post( $video_id );
                                ?>
                                <a href="<?php echo get_the_permalink($video); ?>" class="link js-report-link">
                                    <div class="title">
                                        <span title="<?php echo $order->post_title; ?>"><?php echo $order->post_title; ?></span>
                                    </div>
                                </a>
                                <?php else: ?>
                                <a href="#" class="link js-report-link">
                                    <div class="title">
                                        <span title="<?php echo $order->post_title; ?>"><?php echo $order->post_title; ?></span>
                                    </div>
                                </a>
                                <?php endif; ?>
                            </div>
                            <div class="flex-cell price">¥<?php echo $price; ?></div>
                            <div class="flex-cell wording">
                                <?php if( $order_status == 'pending' || $order_status == 'paying' ): ?>
                                <div class="red">等待付款</div>
                                <?php elseif( $order_status == 'payed' ): ?>
                                <div class="black">报名成功</div>
                                <?php endif; ?>
                            </div>
                            <div class="flex-cell operating">
                                <?php if( $order_status == 'pending' || $order_status == 'paying' ): ?>
                                <a class="im-btn operating-btn btn-default btn-s"
                                    href="<?php echo mcv_checkout_url( ['orderid' => $order->ID] ) ?>"
                                    target="_blank">立即付款</a>
                                <?php elseif( $order_status == 'payed' ): ?>
                                <div class="black">报名成功</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </main>
    </section>
</div>