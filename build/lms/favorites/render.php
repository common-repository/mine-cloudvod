<?php
defined( 'ABSPATH' ) || exit;

$user_id = get_current_user_id();

if( !$user_id ) {
    return;
}

$_mcv_favorites = get_user_meta( $user_id, '_mcv_favorites', true );
if( !is_array($_mcv_favorites) ) $_mcv_favorites = [];

if( isset($_GET['del']) && is_numeric($_GET['del']) ){
    $course_id = $_GET['del'];
    if( isset( $_mcv_favorites[$course_id] ) ){
        unset( $_mcv_favorites[$course_id] );
        update_user_meta( $user_id, '_mcv_favorites', $_mcv_favorites );
    }
    unset($course_id);
}

?>
<div id="mcv-fav-body">
    <section class="mcv-uc">
        <aside class="mcv-uc-left">
            <div class="mcv-nav-area">
                <ul class="mcv-nav">
                    <li class="mcv-nav-item"><a title="课程表" href="<?php echo get_page_link(get_page_by_path('mcv-my-courses')); ?>">课程表</a></li>
                    <li class="mcv-nav-item"><a title="订单管理" href="<?php echo mcv_order_list_url(); ?>">订单管理</a></li>
                    <li class="mcv-nav-item"><a title="我的收藏" href="<?php echo get_page_link(get_page_by_path('mcv-favorites')); ?>" class="active">我的收藏</a></li>
                </ul>
            </div>
        </aside>
        <main class="mcv-uc-main">
            <div class="im-table-wrap">
                <table class="im-table">
                    <thead>
                        <tr>
                            <th style="width: 70%;">课程信息</th>
                            <th style="width: 16%;">金额</th>
                            <th style="width: 14%;">操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach( $_mcv_favorites as $course_id => $time ): 
                        $link = get_permalink( $course_id );
                        $title = get_the_title( $course_id );
                        $price = mcv_lms_show_course_price( $course_id );
                        ?>
                        <tr>
                            <td>
                                <div class="fav-info clearfix">
                                    <a class="fav-info-cover" href="<?php echo $link; ?>" target="_blank" title="<?php echo $title; ?>"><img src="<?php echo get_the_post_thumbnail_url($course_id); ?>"></a>
                                    <div class="fav-info-desc">
                                        <p class="fav-info-name">
                                            <a class="link-3" href="<?php echo $link; ?>" target="_blank" title="<?php echo $title; ?>"><?php echo $title; ?></a>
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td><div class="fav-price"><?php echo $price; ?></div></td>
                            <td><a class="link-3" href="<?php echo get_page_link(get_page_by_path('mcv-favorites')).'?del='.$course_id; ?>">取消收藏</a></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </section>
</div>