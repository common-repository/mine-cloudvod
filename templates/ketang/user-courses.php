<?php 
/**
 * template name: User's Courses Page
 */

defined( 'ABSPATH' ) || exit;

get_header( 'mcv-lms' );

echo do_blocks( '<!-- wp:mine-cloudvod/user-courses /-->' );

get_footer( 'mcv-lms' );
