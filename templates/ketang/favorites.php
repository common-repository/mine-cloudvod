<?php 
/**
 * template name: MCV Favorites
 */

defined( 'ABSPATH' ) || exit;

get_header( 'mcv-lms' );

echo do_blocks( '<!-- wp:mine-cloudvod/favorites /-->' );

get_footer( 'mcv-lms' );
