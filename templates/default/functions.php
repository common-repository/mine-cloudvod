<?php
defined( 'ABSPATH' ) || exit;

add_action('mcv_course_frontend_scripts', 'frontend_script');
function frontend_script($post_type){
    $ajaxUrl = admin_url("admin-ajax.php");
    wp_enqueue_style( 'minelms-css', MINECLOUDVOD_URL . '/templates/'.MINECLOUDVOD_LMS['active_template'].'/assets/css/minelms.css', array(), MINECLOUDVOD_VERSION );
    wp_enqueue_script( 'jquery' );
    wp_add_inline_script( 'jquery', 'jQuery(function(){
        let nonce = "'.wp_create_nonce('mcv_lms_nonce').'";
        jQuery(".minelms-accordion .accordion-head").on("click",function() {
            jQuery(this).toggleClass("collapsed");
            jQuery(this).next().slideToggle();
        });
        jQuery(".nk-nav-toggle").on("click",function(){
            jQuery(".nk-sidebar").toggleClass("nk-sidebar-active");
            jQuery(".ml-content").one("click", function(){jQuery(".nk-sidebar").toggleClass("nk-sidebar-active");});
        });
        jQuery(".mcv-mark-as-complete").on("click", function(){
            jQuery.post("'.$ajaxUrl.'", {action: "mcv_mark_as_complete", nonce: nonce, id: jQuery(this).data("id")}, function(data){
                if(data.success){
                    if(data.data.location)location.href = data.data.location;
                }
            }, "json");
        });
    });' );
    wp_enqueue_script( 'jquery-ui-tabs' );
    wp_add_inline_script( 'jquery-ui-tabs', 'jQuery(function(){
        jQuery(".minelms-tabs").tabs({
            activate: function(event, ui) { 
                ui.oldTab.children().removeClass("active");
                ui.newTab.children().addClass("active");
            }
        });
    });' );
}