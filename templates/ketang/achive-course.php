<?php 
/**
 * template name: Mine Course Page
 */

defined( 'ABSPATH' ) || exit;

get_header( 'mcv-lms' );

echo do_blocks( '<!-- wp:mine-cloudvod/course-list {"title":"全部课程","template":"archive-mcv_course"} /-->' );

get_footer( 'mcv-lms' );
