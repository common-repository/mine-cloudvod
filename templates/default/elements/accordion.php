<?php
defined( 'ABSPATH' ) || exit;

$courses_lessons = $args;

if( !$courses_lessons ){
    $courses_lessons = mcv_lms_get_courses_lessons(  );
}


if( !is_array( $courses_lessons ) || count( $courses_lessons ) == 0 ) return;
$accordion = '';
foreach($courses_lessons as $section){
    if( !is_array( $section['Lessons'] ) || count( $section['Lessons'] ) == 0 ) continue;
    
    $accordion .= '<a class="accordion-head collapsed"><h6 class="title">' . $section['post_title'] . '</h6><span class="accordion-icon"></span></a>';
    $accordion .= '<div class="accordion-body collapse show"><div class="accordion-inner">';
    $lessons = $section['Lessons'];
    foreach($lessons as $lesson){
        $accordion .= '<a href="' . get_post_permalink( $lesson ) . '" class="btn btn-dim btn-outline-light w-100 ni ni-play-fill' . ( $lesson->ID == $post->ID ? ' cur' : '' ) . '">' . $lesson->post_title . '</a>';
    }
    $accordion .= '</div></div>';

}
?><div class="accordion minelms-accordion">
    <?php echo $accordion; ?>
</div>