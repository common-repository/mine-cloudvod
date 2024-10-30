<?php
defined( 'ABSPATH' ) || exit;

function wpPrepareAttachmentForJs($response, $attachment, $meta){
    if(isset($meta['mode'])){
        $response['mode'] = $meta['mode'];
        $response['filename'] = $meta['mode'].': '.$response['filename'];
    }
    return $response;
}
function mcv_tcvod_upload(){
    include MINECLOUDVOD_PATH.'/inc/MineTcVodClientUploader.php';
}
function mcv_alivod_upload(){
    include MINECLOUDVOD_PATH.'/inc/MineAliVodClientUploader.php';
}

function mcv_tcvod_url(){
    if(!is_user_logged_in())exit;
    global $current_user;
    if(!empty($current_user->roles) && in_array('administrator', $current_user->roles)){
        $postId = sanitize_text_field($_GET['fid']);
        if(!is_numeric($postId)){
            echo json_encode(array('status' => '0', 'msg' => __('Illegal request', 'mine-cloudvod').'004'));exit;
        }
        $meta = wp_get_attachment_metadata($postId);
        $mediaUrl = $meta['mediaUrl'];
        $murl = mcv_gen_tcvod_mediaUrl($mediaUrl);
        header('location:'.$murl);
    }
}

function mcv_gen_tcvod_mediaUrl($mediaUrl){
    $key = isset(MINECLOUDVOD_SETTINGS['tcvod']['fdlkey'])?MINECLOUDVOD_SETTINGS['tcvod']['fdlkey']:'';
    $dir = explode('/', $mediaUrl);
    unset($dir[count($dir)-1],$dir[0], $dir[1], $dir[2]);
    $dir = '/'.implode('/', $dir).'/';
    $time = dechex(time() + 600);
    $murl = $mediaUrl.'?t='.$time.'&rlimit=1&sign='.md5($key.$dir.$time.'1');
    return $murl;
}

function get_tcvod_piantouwei(){
    $tw = false;
    if(MINECLOUDVOD_SETTINGS['tcvodpiantou']['status'] && MINECLOUDVOD_SETTINGS['tcvodpiantou']['fileid']){
        $tw['tou'] = MINECLOUDVOD_SETTINGS['tcvodpiantou']['fileid'];
    }
    if(MINECLOUDVOD_SETTINGS['tcvodpianwei']['status'] && MINECLOUDVOD_SETTINGS['tcvodpianwei']['fileid']){
        $tw['wei'] = MINECLOUDVOD_SETTINGS['tcvodpianwei']['fileid'];
    }
    return $tw;
}

function mine_cloudvod($id)
{
    echo do_shortcode('[mine_cloudvod id=' . $id . ']');
}

if( ! function_exists( 'remove_class_filter' ) ){

    /**
     * Remove Class Filter Without Access to Class Object
     *
     * In order to use the core WordPress remove_filter() on a filter added with the callback
     * to a class, you either have to have access to that class object, or it has to be a call
     * to a static method.  This method allows you to remove filters with a callback to a class
     * you don't have access to.
     *
     * Works with WordPress 1.2+ (4.7+ support added 9-19-2016)
     * Updated 2-27-2017 to use internal WordPress removal for 4.7+ (to prevent PHP warnings output)
     *
     * @param string $tag         Filter to remove
     * @param string $class_name  Class name for the filter's callback
     * @param string $method_name Method name for the filter's callback
     * @param int    $priority    Priority of the filter (default 10)
     *
     * @return bool Whether the function is removed.
     */
    function remove_class_filter( $tag, $class_name = '', $method_name = '', $priority = 10 ) {

        global $wp_filter;

        // Check that filter actually exists first
        if ( ! isset( $wp_filter[ $tag ] ) ) {
            return FALSE;
        }

        /**
         * If filter config is an object, means we're using WordPress 4.7+ and the config is no longer
         * a simple array, rather it is an object that implements the ArrayAccess interface.
         *
         * To be backwards compatible, we set $callbacks equal to the correct array as a reference (so $wp_filter is updated)
         *
         * @see https://make.wordpress.org/core/2016/09/08/wp_hook-next-generation-actions-and-filters/
         */
        if ( is_object( $wp_filter[ $tag ] ) && isset( $wp_filter[ $tag ]->callbacks ) ) {
            // Create $fob object from filter tag, to use below
            $fob       = $wp_filter[ $tag ];
            $callbacks = &$wp_filter[ $tag ]->callbacks;
        } else {
            $callbacks = &$wp_filter[ $tag ];
        }

        // Exit if there aren't any callbacks for specified priority
        if ( ! isset( $callbacks[ $priority ] ) || empty( $callbacks[ $priority ] ) ) {
            return FALSE;
        }

        // Loop through each filter for the specified priority, looking for our class & method
        foreach ( (array) $callbacks[ $priority ] as $filter_id => $filter ) {

            // Filter should always be an array - array( $this, 'method' ), if not goto next
            if ( ! isset( $filter['function'] ) || ! is_array( $filter['function'] ) ) {
                continue;
            }

            // If first value in array is not an object, it can't be a class
            if ( ! is_object( $filter['function'][0] ) ) {
                continue;
            }

            // Method doesn't match the one we're looking for, goto next
            if ( $filter['function'][1] !== $method_name ) {
                continue;
            }

            // Method matched, now let's check the Class
            if ( get_class( $filter['function'][0] ) === $class_name ) {

                // WordPress 4.7+ use core remove_filter() since we found the class object
                if ( isset( $fob ) ) {
                    // Handles removing filter, reseting callback priority keys mid-iteration, etc.
                    $fob->remove_filter( $tag, $filter['function'], $priority );

                } else {
                    // Use legacy removal process (pre 4.7)
                    unset( $callbacks[ $priority ][ $filter_id ] );
                    // and if it was the only filter in that priority, unset that priority
                    if ( empty( $callbacks[ $priority ] ) ) {
                        unset( $callbacks[ $priority ] );
                    }
                    // and if the only filter for that tag, set the tag to an empty array
                    if ( empty( $callbacks ) ) {
                        $callbacks = array();
                    }
                    // Remove this filter from merged_filters, which specifies if filters have been sorted
                    unset( $GLOBALS['merged_filters'][ $tag ] );
                }

                return TRUE;
            }
        }

        return FALSE;
    }
}

/**
 * Make sure the function does not exist before defining it
 */
if( ! function_exists( 'remove_class_action') ){

    /**
     * Remove Class Action Without Access to Class Object
     *
     * In order to use the core WordPress remove_action() on an action added with the callback
     * to a class, you either have to have access to that class object, or it has to be a call
     * to a static method.  This method allows you to remove actions with a callback to a class
     * you don't have access to.
     *
     * Works with WordPress 1.2+ (4.7+ support added 9-19-2016)
     *
     * @param string $tag         Action to remove
     * @param string $class_name  Class name for the action's callback
     * @param string $method_name Method name for the action's callback
     * @param int    $priority    Priority of the action (default 10)
     *
     * @return bool               Whether the function is removed.
     */
    function remove_class_action( $tag, $class_name = '', $method_name = '', $priority = 10 ) {
        return remove_class_filter( $tag, $class_name, $method_name, $priority );
    }
}

/**
 * remove the space chars
 */
function mcv_trim($string){
    return str_replace(["    ", "\n", "\r", "\t"], '', $string);
}

if(!function_exists('mine_get_page_id_by_slug')){
    function mine_get_page_id_by_slug($slug){
        $page = get_page_by_path( $slug );
        if ( $page ) {
            return $page->ID;
        } else {
            return -1;
        }
    }
}

if( !function_exists( 'mcv_check_role_permission' ) ){
    function mcv_check_role_permission(){
        $hasRole = false;
        if( isset(MINECLOUDVOD_SETTINGS['rolePermission']['status']) && MINECLOUDVOD_SETTINGS['rolePermission']['status'] && isset(MINECLOUDVOD_SETTINGS['rolePermission']['roles']) ){
            global $current_user;
            if(!$current_user) return false;
            $roles = $current_user->roles;
            foreach($roles as $role){
                if(in_array($role, MINECLOUDVOD_SETTINGS['rolePermission']['roles'])){
                    $hasRole = true;
                    break;
                }
            }
        }
        else{
            $hasRole = true;
        }
        return $hasRole;
    }
}

if( !function_exists( 'mcv_is_wechat' ) ){
    function mcv_is_wechat(){
        if ( isset( $_SERVER['HTTP_USER_AGENT'] ) && stripos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false ) {
            return true;
        }  
        return false;
    }
}
if( !function_exists( 'mcv_is_wechat_miniprogram' ) ){
    function mcv_is_wechat_miniprogram(){
        if (stripos($_SERVER['HTTP_USER_AGENT'], 'miniprogram') !== false // android的微信小程序
        || (stripos($_SERVER['HTTP_USER_AGENT'], 'mac os') !== false && stripos($_SERVER['HTTP_USER_AGENT'], 'micromessenger') !== false && isset($_SERVER['HTTP_REFERER']) && stripos($_SERVER['HTTP_REFERER'], '://servicewechat.com') !== false)//ios的小程序
        || stripos($_SERVER['HTTP_USER_AGENT'], 'swan') !== false // 百度小程序
        ) {
            return true;
        }  
        return false;
    }
}

if( !function_exists( 'mcv_is_block_rendered' ) ){
    function mcv_is_block_rendered( $divId ){
        global $isilmd5;
        if (is_array($isilmd5) && in_array(md5($divId), $isilmd5)) return true;
        $isilmd5[] = md5($divId);
        
        return false;
    }
}

function mcv_activation_hook(){
    update_option('_mcv_permalinks_flushed', 0);
}

function mcv_get_video_price( $post_id, $source ){
    $post = get_post( $post_id );
    if( !$post ) return 0;

    $blocks = parse_blocks( $post->post_content );
    foreach( $blocks as $block ){
        if( $block['blockName'] == 'mine-cloudvod/block-container' && count( $block['innerBlocks'] ) == 1 ){
            $block = $block['innerBlocks'][0];
        }
        $attrs = $block['attrs'];
        $vodId = mcv_get_video_id( $attrs );
        if( base64_encode($vodId) == $source ){
            return $attrs['price'];
            break;
        }
    }
    preg_match_all( 
        '/' . get_shortcode_regex() . '/', 
        $post->post_content, 
        $mcv_shortcodes, 
        PREG_SET_ORDER
    );
    foreach( $mcv_shortcodes as $sc ){
        //云点播中心的简码
        if( $sc[2] == 'mine_cloudvod' ){
            $pid = explode( '=', $sc[3] );
            $pid = $pid[1];
            $sc_post = get_post( $pid );
            $price = mcv_get_vodhub_price( $sc_post->post_content, $source );
            return $price;
        }
        //经典编辑器简码
        elseif( 
            $sc[2] == 'mcv_dplayer'
            || $sc[2] == 'mcv_aliplayer'
            || $sc[2] == 'mcv_alioss'
            || $sc[2] == 'mcv_alivod'
            || $sc[2] == 'mcv_tcvod'
            || $sc[2] == 'mcv_tccos'
            || $sc[2] == 'mcv_dogevcloud'
        ){
            preg_match_all('/(\w+)\=\"(.*?)\"/', $sc[3], $key_value_pairs);
            if( count($key_value_pairs) == 3 ){
                $key = $key_value_pairs[1];
                $value = $key_value_pairs[2];
                $attrs = [];
                for( $i = 0; $i <= count( $key ); $i++ ){
                    $attrs[$key[$i]] = $value[$i];
                }
                $vodId = mcv_get_video_id( $attrs );
                if( base64_encode($vodId) == $source ){
                    return $attrs['price'];
                    break;
                }
            }
        }
    }

    /**
     * 过滤视频价格，用于自定义视频价格获取逻辑
     * 
     * @since 1.7.6
     */
    $price = apply_filters('mcv_get_video_price', 0, $post, $source);

    return $price;
}
function mcv_get_vodhub_price( $content, $source ){
    $blocks = parse_blocks( $content );
    foreach( $blocks as $block ){
        if( $block['blockName'] == 'mine-cloudvod/block-container' && count( $block['innerBlocks'] ) == 1 ){
            $block = $block['innerBlocks'][0];
        }
        $attrs = $block['attrs'];
        $vodId = mcv_get_video_id( $attrs );
        if( base64_encode($vodId) == $source ){
            return $attrs['price'];
            break;
        }
    }
    return 0;
}
function mcv_get_video_id( $attributes ){
    if( isset( $attributes['source'] ) && $attributes['source'] ){
        return $attributes['source'];
    }
    elseif( isset( $attributes['videoId'] ) && $attributes['videoId'] ){
        return $attributes['videoId'];
    }
    elseif( isset( $attributes['oss']['key'] ) && isset( $attributes['oss']['bucket'] ) && $attributes['oss']['key'] && $attributes['oss']['bucket'] ){
        return $attributes['oss']['bucket'] . '/' . $attributes['oss']['key'];
    }
    elseif( isset( $attributes['cos']['key'] ) && isset( $attributes['cos']['bucket'] ) && $attributes['cos']['key'] && $attributes['cos']['bucket'] ){
        return $attributes['cos']['bucket'] . '/' . $attributes['cos']['key'];
    }
    elseif( isset( $attributes['vcode'] ) && $attributes['vcode'] ){
        return $attributes['vcode'];
    }
    elseif( isset( $attributes['minecloudvod'] ) ){
        $tmp = $attributes['minecloudvod'];
        if( isset( $tmp['doge']['vcode'] ) && $tmp['doge']['vcode'] ){
            return $tmp['doge']['vcode'];
        }
        elseif( isset( $tmp['tcvod']['fileID'] ) && $tmp['tcvod']['fileID'] ){
            return $tmp['tcvod']['fileID'];
        }
    }
    return false;
}

function mcv_order_update_items( $order_items, $author_id, $order = null, $total_amount = 0 ){
    $isLms = false;
    foreach( $order_items as $item ){
        // 视频购买成功
        if( is_array( $item ) ){
            $eitems = get_user_meta( $author_id, '_mcv_lms_enroll_course_id_'.$item[0], true );
            if( !$eitems ){
                $eitems = [ [$item[1], time()] ];
            }
            else{
                $is_exisit = false;
                foreach( $eitems as $ei ){
                    if( $ei[0] == $item[1] ){
                        $is_exisit = true;
                    }
                }
                if( !$is_exisit ) $eitems = array_merge($eitems, [ [$item[1], time()] ]);
            }
            update_user_meta( $author_id, '_mcv_lms_enroll_course_id_'.$item[0], $eitems );
        }
        // 课程购买成功
        else{
            $isLms = true;
            update_user_meta( $author_id, '_mcv_lms_enroll_course_id_'.$item, time() );
            if( $order ){
                /**
                 * 课程购买成功，添加学员记录后执行的后续处理
                 * 
                 * @param int $item 课程/课时/章节 id
                 * @param int $author_id 下单用户id
                 * @param int $order 订单 post
                 * @param money $total_amount 实付金额
                 */
                do_action( 'mcv_course_order_handler', $item, $author_id, $order, $total_amount );
            }
        }
    }
    if( $isLms && function_exists( 'mcv_lms_be_student' ) ) mcv_lms_be_student( $author_id );
}

/**
 * 设置文件缓存
 * 
 * @param  $dir string 文件路经
 * @param  $filename string 文件名称
 * @param  $content string 文件内容
 * 
 * @return  null
 * 
 */
function mcv_set_file_cache($dir, $filename, $content){
    $wpdir = wp_get_upload_dir();
    $mcvdir =  (isset($wpdir['default']['basedir'])?$wpdir['default']['basedir']:$wpdir['basedir']).'/mcv-cache/'.$dir;
    $mcvfile = $mcvdir.'/'.$filename;

    @wp_mkdir_p($mcvdir);
    @file_put_contents($mcvfile, $content );
}
/**
 * 获取文件缓存
 * 
 * @param  $dir string 文件路经
 * @param  $filename string 文件名称
 * @param  $expire int 过期时间 单位秒
 * 
 * @return  string|bool
 * 
 */
function mcv_get_file_cache($dir, $filename, $expire = 3600){
    $wpdir = wp_get_upload_dir();
    $mcvdir =  (isset($wpdir['default']['basedir'])?$wpdir['default']['basedir']:$wpdir['basedir']).'/mcv-cache/'.$dir;
    $mcvfile = $mcvdir.'/'.$filename;

    if( file_exists($mcvfile) && filemtime($mcvfile) + $expire - 10 > time() ){
        return file_get_contents($mcvfile);
    }
    return false;
}