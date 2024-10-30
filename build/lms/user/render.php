<?php
defined( 'ABSPATH' ) || exit;

$submenus = $attributes['submenus'] ?? [];

$user_id = get_current_user_id();
$avatar = MINECLOUDVOD_URL.'/static/img/user.png';
if( $user_id ){
    $avatar = get_user_meta( $user_id, 'mcv_avatar', true )?:$avatar;
    
    $ucStr = '<div class="mcv-usercenter">
        <p><img class="mcv-avatar" src="'.$avatar.'" /></p>
        <div class="submenu">';
    foreach( $submenus as $sm ){
        $ucStr .= '<p><a href="'.$sm['url'].'" rel="nofollow">'.$sm['title'].'</a></p>';
    }
    $ucStr .= '<p style="border-top: 1px solid #ccc;margin-top: 5px;padding-top: 5px;"><a href="'.wp_logout_url($_SERVER['REQUEST_URI']).'" rel="nofollow">'.__('Log out').'</a></p>
        </div>
    </div>';
    echo $ucStr;
}
else{
    $viewDependencies = include( MINECLOUDVOD_PATH.'/build/lms/user/view.asset.php' );
    foreach($viewDependencies['dependencies'] as $dpc){
        wp_enqueue_style( $dpc );
    }
    echo '<div class="mcv-usercenter mcv-login"><p><img class="mcv-avatar" src="'.$avatar.'" title="'.__('Click to login', 'mine-cloudvod').'" /></p></div>';
}
?>