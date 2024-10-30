<?php
global $post;
$access_mode = mcv_lms_get_course_access_mode( $post->ID );

$progress = mcv_lms_get_course_progress();
$is_user_logged_in = is_user_logged_in();
$login_url = wp_login_url( $progress['next'] );

function get_progress($progress){
    $percent = (int)($progress['completed'] / $progress['total'] * 100);
            ?>
    <h6><?php echo __('Course Progress', 'mine-cloudvod'); ?></h6>
    <div class="progress-wrap">
        <div class="progress-text">
            <div class="progress-label"><?php echo $progress['completed']; ?>/<?php echo $progress['total']; ?></div>
            <div class="progress-amount"><?php echo $percent; ?>%</div>
        </div>
        <div class="progress progress-md">
            <div class="progress-bar" style="width: <?php echo $percent; ?>%;"></div>
        </div>
    </div>
    <div class="py-3">
        <a href="<?php echo $progress['next']; ?>" class="btn btn-primary d-sm-inline-block w-100"><?php 
        if($percent == 0) _e('Start Learning', 'mine-cloudvod');
        elseif($percent < 100) _e('Continue Studying', 'mine-cloudvod');
        else _e('Completed', 'mine-cloudvod');
        ?></a>
    </div>
    <p class="card-text"><?php $progress['enroll_at'] && printf( __('You enrolled in this course on %s.', 'mine-cloudvod'), date('Y-m-d', $progress['enroll_at']) ); ?></p>
            <?php
}
?><div class="col-md-6 col-lg-12">
    <div class="card card-bordered">
        <div class="card-header border-bottom">
        <?php
            /**
             * case 1: open 可直接 开始学习
             * case 2: free 需要登录 注册后 可开始学习
             * case 3: buynow 需要登录 购买后 可开始学习
             */
            
            if( $access_mode == 'open' ){
                if( !$is_user_logged_in ){
                    ?>
                        <div class="py-3">
                            <a href="<?php echo $progress['next']; ?>" class="btn btn-primary d-sm-inline-block w-100"><?php _e('Start Learning', 'mine-cloudvod'); ?></a>
                        </div>
                    <?php
                }
                else{
                    get_progress($progress);
                }
            }
            if( $access_mode == 'free' ){
                if( !$is_user_logged_in ){
        ?>
            <div class="py-3">
                <a href="<?php echo $login_url; ?>" class="btn btn-primary d-sm-inline-block w-100"><?php _e('Login to start learning', 'mine-cloudvod'); ?></a>
            </div>
        <?php
                }else{
                    get_progress($progress);
                }
            }
            if( $access_mode == 'buynow' ){
        ?>
            <div class="py-3">
                <a href="<?php echo $login_url; ?>" class="btn btn-primary d-sm-inline-block w-100"><?php _e('Buy Now', 'mine-cloudvod'); ?></a>
            </div>
        <?php
            }
        ?>
        </div>
        <div class="card-inner">
            <ul class="">
                <li class="py-1">
                    <em class="icon ni ni-users fs-14px"></em>
                    <span class=""><?php _e('Number Enrolled', 'mine-cloudvod'); ?> <?php echo mcv_lms_get_course_enrolled_number(); ?></span>
                </li>
                <li class="py-1">
                    <em class="icon ni ni-update fs-14px"></em>
                    <span class=""><?php printf( __('Updated on %s.', 'mine-cloudvod'), get_the_date('Y-m-d') ); ?></span>
                </li>
            </ul>
        </div>
    </div>
</div>