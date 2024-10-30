<?php 
/**
 * template name: Mine Course Page
 */

defined( 'ABSPATH' ) || exit;

get_header( 'minelms-course' );

?>
<div class="minelms-root">
		<div class="ml-main">
			<div class="wide-lg m-auto">
				<div class="ml-content">
					<div class="container-fluid">
						<div class="ml-content-inner">
							<div class="ml-content-body">
								<div class="ml-block-head ml-block-head-sm">
									<div class="ml-block-between">
										<div class="ml-block-head-content">
											<h3 class="ml-block-title page-title"><?php echo __('Courses', 'mine-cloudvod'); ?></h3>
										</div>
									</div>
								</div>
								<div class="nk-block">
									<div class="row g-gs">
										<?php 
										while(have_posts()){
											the_post();
											mcv_lms_load_template( 'loop.course' );
										}
										mcv_lms_load_template( 'elements.pagination' );
										?>
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

do_action( 'mcv_lms_after_course_achive' );


get_footer( 'mcv-lms-course' );
