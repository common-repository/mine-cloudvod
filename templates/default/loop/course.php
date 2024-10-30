<?php
defined( 'ABSPATH' ) || exit;

$thumbnail = get_the_post_thumbnail_url();
if( !$thumbnail ) $thumbnail = MINECLOUDVOD_URL . '/templates/'.MINECLOUDVOD_LMS['active_template'].'/assets/images/default.png';
?><div class="col-xxl-3 col-lg-4 col-sm-6">
	<div class="card card-bordered product-card">
		<div class="product-thumb">
			<a href="<?php echo esc_url(get_the_permalink()); ?>">
				<img class="card-img-top" src="<?php echo $thumbnail; ?>" alt="<?php the_title(); ?>">
			</a>
		</div>
		<div class="card-inner">
			<h5 class="product-title" title="<?php the_title(); ?>">
				<a href="<?php echo esc_url(get_the_permalink()); ?>"><?php the_title(); ?></a>
			</h5>
			<!-- <div class="product-price text-primary h5"><small class="text-muted del fs-13px">$350</small> $324</div> -->
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
			<div class="team-view">
				<a href="<?php echo esc_url(get_the_permalink()); ?>" class="btn btn-block btn-dim btn-primary"><span><?php _e('Start Learning', 'mine-cloudvod'); ?></span></a>
			</div>
		</div>
	</div>
</div><!-- .col -->