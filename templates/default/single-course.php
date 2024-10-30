<?php 
defined( 'ABSPATH' ) || exit;

get_header( 'minelms-course' );

$thumbnail = get_the_post_thumbnail_url();
if( !$thumbnail ) $thumbnail = MINECLOUDVOD_URL . '/templates/'.MINECLOUDVOD_LMS['active_template'].'/assets/images/default.png';


?>
<div class="minelms-root">
    <div class="ml-main ">
        <div class="wide-lg m-auto">
            <div class="ml-content ">
                <div class="container-fluid">
                    <div class="ml-content-inner">
                        <div class="ml-content-body">
                            <div class="ml-block-head ml-block-head-sm">
                                <div class="ml-block-between">
                                    <div class="ml-block-head-content">
                                        <h3 class="ml-block-title page-title"><?php the_title(); ?></h3>
                                        <div class="ml-block-des text-soft">
                                            <p><?php the_category(); ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="ml-block">
                                <div class="row g-gs">
                                    <div class="col-lg-8">
                                        <div class="card card-bordered card-full">
                                            <div class="product-thumb-course">
                                                <img class="card-img-top" src="<?php echo $thumbnail; ?>" alt="<?php the_title(); ?>">
                                            </div>
                                            <div class="card-inner minelms-tabs">
                                                <ul class="nav nav-tabs mt-n3">
                                                    <li class="nav-item">
                                                        <a class="nav-link active" href="#tabItem1"><?php _e('Course Details', 'mine-cloudvod'); ?></a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link" href="#tabItem2"><?php _e('Course Catelog', 'mine-cloudvod'); ?></a>
                                                    </li>
                                                </ul>
                                                <div class="tab-content">
                                                    <div class="tab-pane active" id="tabItem1">
                                                        <?php the_content(); ?>
                                                    </div>
                                                    <div class="tab-pane" id="tabItem2">
                                                        <div class="card-inner">
                                                            <?php mcv_lms_load_template('elements.accordion'); ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="row g-gs">
                                            <?php mcv_lms_load_template('elements.course-single-progress') ?>
                                            
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php

get_footer( 'mcv-lms-course' );
