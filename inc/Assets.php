<?php
namespace MineCloudvod;

class Assets{
    public function __construct() {
        add_action( 'init',                     [ $this, 'mcv_assets_register'] );
        add_action( 'admin_enqueue_scripts',    [ $this, 'mcv_course_admin_assets' ] );
        add_action( 'admin_enqueue_scripts',	[ $this, 'mcv_blocks_assets'] );
        add_action( 'admin_enqueue_scripts',	[ $this, 'mcv_options_scripts'] );
        load_textdomain( 'mine-cloudvod', MINECLOUDVOD_PATH .'/languages/mine-cloudvod-'. get_locale() .'.mo' );
    }

    public function mcv_assets_register(){
        
        global $wp_version;
        //将php变量本地化到页面js中
        wp_register_script( 'mcv_localize_script', false );
        wp_register_script(
            'mcv_layer',
            MINECLOUDVOD_URL.'/static/layer/layer.js',
            array( 'jquery' ),
            MINECLOUDVOD_VERSION,
            true
        );
        

        wp_register_style(
            'mine_cloudvod-aliyunvod-style-css',
            MINECLOUDVOD_URL.'/build/block/style-mcv.blocks.css', 
            is_admin() ? array( 'wp-editor' ) : null,
            MINECLOUDVOD_VERSION
        );
        wp_register_style(
            'mine-cloudvod-admin-css',
            MINECLOUDVOD_URL.'/build/admin/index.css', 
            null,
            MINECLOUDVOD_VERSION
        );

        $adminDependencies = include( MINECLOUDVOD_PATH.'/build/admin/index.asset.php' );
        wp_register_script(
            'mine-cloudvod-admin-js',
            MINECLOUDVOD_URL.'/build/admin/index.js',
            $adminDependencies['dependencies'],
            MINECLOUDVOD_VERSION,
            true
        );

        $adminDependencies = include( MINECLOUDVOD_PATH.'/build/admin/init/index.asset.php' );
        wp_register_script(
            'mcv-admin-init',
            MINECLOUDVOD_URL.'/build/admin/init/index.js',
            $adminDependencies['dependencies'],
            MINECLOUDVOD_VERSION,
            true
        );

        wp_register_script(
            'mine_cloudvod-aliyunvod-block-js',
            MINECLOUDVOD_URL.'/build/block/mcv.blocks.js',
            array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor' ),
            MINECLOUDVOD_VERSION,
            true
        );

        wp_register_script(
            'mine_cloudvod-classic-js',
            MINECLOUDVOD_URL.'/build/classic/index.js',
            array( 'wp-i18n', 'wp-block-editor', 'wp-api-fetch' ),
            MINECLOUDVOD_VERSION,
            true
        );

        wp_register_script(//import
            'mine_cloudvod-import-js',
            MINECLOUDVOD_URL.'/build/import/index.js',
            array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor' ),
            MINECLOUDVOD_VERSION,
            true
        );

        wp_register_script(//lms
            'mine_cloudvod-lms-js',
            MINECLOUDVOD_URL.'/build/lms/course-metabox/index.js',
            array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor', 'wp-data' ),
            MINECLOUDVOD_VERSION,
            true
        );

        wp_register_script(//elementor
            'mine_cloudvod-integrations-elementor-js',
            MINECLOUDVOD_URL.'/build/integrations/elementor/editor.js',
            array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor' ),
            MINECLOUDVOD_VERSION,
            true
        );

        wp_register_style(
            'mine_cloudvod-lms-css',
            MINECLOUDVOD_URL.'/build/lms/course-metabox/index.css',
            array(  ),
            MINECLOUDVOD_VERSION
        );

        wp_register_style(
            'mine_cloudvod-aliyunvod-block-editor-css',
            MINECLOUDVOD_URL.'/build/block/mcv.blocks.css',
            array( ),
            MINECLOUDVOD_VERSION
        );
        wp_set_script_translations( 'mine_cloudvod-aliyunvod-block-js', 'mine-cloudvod' );
        wp_set_script_translations( 'mine_cloudvod-import-js', 'mine-cloudvod' );
        wp_set_script_translations( 'mine_cloudvod-integrations-elementor-js', 'mine-cloudvod' );
        wp_set_script_translations( 'mine_cloudvod-classic-js', 'mine-cloudvod' );
        wp_set_script_translations( 'mine_cloudvod-lms-js', 'mine-cloudvod' );
        wp_set_script_translations( 'mine-cloudvod-admin-js', 'mine-cloudvod' );

        if( !mcv_check_role_permission() ) return;

        register_block_type(
            'mine-cloudvod/block-container', array(
                'editor_script' => 'mine_cloudvod-aliyunvod-block-js',
                'editor_style'  => 'mine_cloudvod-aliyunvod-block-editor-css',
            )
        );

        // global $mcv_classes;
        // wp_localize_script( 'mine_cloudvod-aliyunvod-block-js', 'mcv_switch', [
        //     'vod' => MINECLOUDVOD_SETTINGS['mcv_cloudvod']??[
        //         'aliyun' => $mcv_classes->Addons->is_addons_actived('aliyun'),
        //         'qcloud' => $mcv_classes->Addons->is_addons_actived('qcloud'),
        //         'dogecloud' => $mcv_classes->Addons->is_addons_actived('doge'),
        //         'qiniukodo' => $mcv_classes->Addons->is_addons_actived('qiniukodo'),
        //     ],
        //     'players' => MINECLOUDVOD_SETTINGS['players']??[
        //         'aliplayer'=>'1',
        //         'dplayer'=>'1',
        //         'playlist'=>'1',
        //         'aplayer'=>'1',
        //         'embed'=>'1',
        //     ],
        //     'lms' => MINECLOUDVOD_SETTINGS['mcv_lms']['status'] ?? true,
        // ]);

        $filter = 'block_categories';
        if (version_compare($wp_version, '5.8', ">=")) {
            $filter = 'block_categories_all';
        }
        add_filter( $filter, function( $categories ) {
            $categories = array_merge(
                array(
                    array(
                        'slug'  => 'mine',
                        'title' => __('Mine', 'mine-cloudvod'),
                    ),
                ),
                $categories
            );
            return $categories;
        } );
    }

    /**
     * 区块
     */
    public function mcv_blocks_assets($hook){
        if($hook != "post.php" && $hook != "post-new.php" && $hook != "edit.php" && $hook != "site-editor.php") return;
        global $current_user;
        $uid = $current_user->ID;
        wp_enqueue_script('jquery');
        wp_enqueue_script('mcv_aliplayer', MINECLOUDVOD_ALIPLAYER['js'],  array(), MINECLOUDVOD_VERSION , true);
        wp_enqueue_script('mcv_aplayer', MINECLOUDVOD_URL.'/static/aplayer/McvAPlayer.min.js',  array(), MINECLOUDVOD_VERSION , true);
        wp_enqueue_script('mcv_aliplayer_components', MINECLOUDVOD_URL.'/static/aliyun/aliplayercomponents-1.0.6.min.js',  array('mcv_aliplayer'), MINECLOUDVOD_VERSION , false );
        wp_enqueue_script('mcv_alivod_sdk', MINECLOUDVOD_URL.'/static/aliyun/upload-js-sdk/aliyun-upload-sdk-1.5.0.min.js',  array(), MINECLOUDVOD_VERSION , true);
        wp_enqueue_script('mcv_alivod_es6-promise', MINECLOUDVOD_URL.'/static/aliyun/upload-js-sdk/lib/es6-promise.min.js',  array(), MINECLOUDVOD_VERSION , true);
        wp_enqueue_script('mcv_alivod_oss', MINECLOUDVOD_URL.'/static/aliyun/upload-js-sdk/lib/aliyun-oss-sdk-5.3.1.min.js',  array(), MINECLOUDVOD_VERSION , true);
        wp_add_inline_script('jquery','var mcv_nonce={ajaxUrl:"'.admin_url("admin-ajax.php").'",et:"'.wp_create_nonce('mcv_sync_endtime').'",endtime:'.strtotime(MINECLOUDVOD_SETTINGS['endtime']).', buynow:"'.admin_url('/admin.php?page=mcv-options#tab='.str_replace(' ', '-', strtolower(urlencode(__('Purchase time', 'mine-cloudvod'))))).'", restRootUrl:"'.get_rest_url().'"};');
        
        wp_enqueue_style('mcv_aplayer_css', MINECLOUDVOD_URL.'/static/aplayer/APlayer.min.css', array(), false);
        wp_enqueue_style('mcv_tcplayer_css', 'https://web.sdk.qcloud.com/player/tcplayer/release/v4.7.2/tcplayer.min.css', array(), false);
        // wp_enqueue_script('mcv_tcplayerhls', 'https://web.sdk.qcloud.com/player/tcplayer/release/v4.6.0/libs/hls.min.1.1.5.js',  array(), MINECLOUDVOD_VERSION , true );
        wp_enqueue_script('mcv_tcplayer', 'https://web.sdk.qcloud.com/player/tcplayer/release/v4.7.2/tcplayer.v4.7.2.min.js',  array(), MINECLOUDVOD_VERSION , true );
        wp_add_inline_script('mcv_tcplayer','var mcv_tcvod_config={appID:"'.(MINECLOUDVOD_SETTINGS['tcvod']['appid']??'').'",key:"'.(MINECLOUDVOD_SETTINGS['tcvod']['fdlkey']??'').'",pcfg:"'.(MINECLOUDVOD_SETTINGS['tcvod']['plyrconfig']??'').'",nonce:"'.wp_create_nonce('mcv-aliyunvod-'.$uid).'",sdk:'.( MINECLOUDVOD_SETTINGS['tcvod']['sid']??MINECLOUDVOD_SETTINGS['tcvod']['skey']??false ? 'true' : 'false' ).',tc_config_url:"'.admin_url('/admin.php?page=mcv-options#tab='.str_replace(' ', '-', strtolower(urlencode(__('Tencent Cloud', 'mine-cloudvod'))))).'pro"};');

        global $mcv_classes;
        wp_localize_script( 'jquery-core', 'mcv_addons', [
            'actived' => $mcv_classes->Addons->get_actived_addons()
        ] );
    }

    /**
     * 课程
     */
    public function mcv_course_admin_assets($hook) {
        global $post_type, $post_id;
        if( $post_type && $post_type == MINECLOUDVOD_LMS['course_post_type'] && ( $hook == 'post.php' || $hook == 'post-new.php' ) ){
            wp_enqueue_style( 'mine_cloudvod-lms-css' );
            wp_enqueue_editor();

            $lms_config = [
                'new_lesson' => admin_url('post-new.php?post_type=mcv_lesson'),
                'edit_lesson' => admin_url('post.php?action=edit&post='),
            ];
            $catelog_no = true;
            if( $post_id ){
                $catelog_no = get_post_meta( $post_id, '_mcv_course_no_type', true );
                if( $catelog_no === "" ) $catelog_no = true;
                else $catelog_no = !!$catelog_no;
            }
            $lms_config['catelog_no'] = $catelog_no;
            wp_enqueue_script( 'mine_cloudvod-lms-js' );
            wp_localize_script('mine_cloudvod-lms-js', 'mcv_lms_config', $lms_config);
        }
    }

    /**
     * 设置
     */
    public function mcv_options_scripts($hook){
        wp_enqueue_style('mine_cloudvod-aliyunvod-style-css');
        $ajaxUrl = admin_url("admin-ajax.php");
        if($hook == 'toplevel_page_mcv-options'){
            wp_enqueue_script('mcv_layer', MINECLOUDVOD_URL.'/static/layer/layer.js',  array(), MINECLOUDVOD_VERSION , true );
            wp_add_inline_script('mcv_layer', mcv_trim( '
            function mcv_sync_tccos_buckets(){
                var index = layer.load(1, {
                    shade: [0.3,"#fff"] 
                });
                jQuery(function(){
                    jQuery.post("'.$ajaxUrl.'", {action: "mcv_asyc_tccos_buckets", nonce: "'. wp_create_nonce('mcv_asyc_tccos_buckets') .'"}, function(data){
                        layer.close(index);
                        if(data.status=="0")layer.msg("'.__('Get failed', 'mine-cloudvod').'"+data.msg);
                        if(data.status=="1"){
                            layer.msg("'.__('Successfully synchronized buckets', 'mine-cloudvod').'");
                            var tcoptions = "", tdata = data.data;
                            for(var i=0; i<tdata.length; i++){
                            tcoptions += "<option value=\""+tdata[i][0]+"\">"+tdata[i][0]+"</option>";
                            }
                            jQuery("select[name=\'mcv_settings[tcvod][buckets]\']").html(tcoptions);
                        }
                    }, "json");
                });
            }
            function mcv_sync_qiniu_buckets(){
                var index = layer.load(1, {
                    shade: [0.3,"#fff"] 
                });
                jQuery(function(){
                    jQuery.post("'.$ajaxUrl.'", {action: "mcv_asyc_qiniu_buckets", nonce: "'. wp_create_nonce('mcv_asyc_qiniu_buckets') .'"}, function(data){
                        layer.close(index);
                        if(data.status=="0")layer.msg("'.__('Get failed', 'mine-cloudvod').'"+data.msg);
                        if(data.status=="1"){
                            layer.msg("'.__('Successfully synchronized buckets', 'mine-cloudvod').'");
                            var tcoptions = "", tdata = data.data[0];
                            for(var i=0; i<tdata.length; i++){
                            tcoptions += "<option value=\""+tdata[i]+"\">"+tdata[i]+"</option>";
                            }
                            jQuery("select[name=\'mcv_settings[qiniu][bucket]\']").html(tcoptions);
                        }
                    }, "json");
                });
            }
            function mcv_sync_alioss_buckets(){
                var index = layer.load(1, {
                    shade: [0.3,"#fff"] 
                });
                jQuery(function(){
                    jQuery.post("'.$ajaxUrl.'", {action: "mcv_asyc_alioss_buckets", nonce: "'. wp_create_nonce('mcv_asyc_alioss_buckets') .'"}, function(data){
                        layer.close(index);
                        if(data.status=="0")layer.msg("'.__('Get failed', 'mine-cloudvod').'"+data.msg);
                        if(data.status=="1"){
                            layer.msg("'.__('Successfully synchronized buckets', 'mine-cloudvod').'");
                            var tcoptions = "", tdata = data.data;
                            for(var i=0; i<tdata.length; i++){
                            tcoptions += "<option value=\""+tdata[i][0]+"\">"+tdata[i][0]+"</option>";
                            }
                            jQuery("select[name=\'mcv_settings[alivod][buckets]\']").html(tcoptions);
                        }
                    }, "json");
                });
            }
            function mcv_sync_alilive_domains(){
                var index = layer.load(1, {
                    shade: [0.3,"#fff"] 
                });
                jQuery(function(){
                    jQuery.post("'.$ajaxUrl.'", {action: "mcv_sync_alilive_domains", nonce: "'. wp_create_nonce('mcv_sync_alilive_domains') .'"}, function(data){
                        layer.close(index);
                        if(data.status=="0")layer.msg("'.__('Get failed', 'mine-cloudvod').'"+data.msg);
                        if(data.status=="1"){
                            layer.msg("'.__('Successfully synchronized pull domains', 'mine-cloudvod').'");
                            var tcoptions = "", tdata = data.data;
                            for(var i=0; i<tdata.length; i++){
                            tcoptions += "<option value=\""+tdata[i]["DomainName"]+"\">"+tdata[i]["DomainName"]+"</option>";
                            }
                            jQuery("select[name=\'mcv_settings[alivod][PullDomain]\']").html(tcoptions);
                        }
                    }, "json");
                });
            }
            function mcv_sync_alilive_pushdomain(){
                let pd = jQuery("select[name=\'mcv_settings[alivod][PullDomain]\']").val();
                if(!pd){
                    layer.msg("'.__('Please sync Pull Domains first', 'mine-cloudvod').'");
                    return;
                }
                var index = layer.load(1, {
                    shade: [0.3,"#fff"] 
                });
                jQuery(function(){
                    jQuery.post("'.$ajaxUrl.'", {action: "mcv_sync_alilive_domains", domain: pd, nonce: "'. wp_create_nonce('mcv_sync_alilive_domains') .'"}, function(data){
                        layer.close(index);
                        if(data.status=="0")layer.msg("'.__('Get failed', 'mine-cloudvod').'"+data.msg);
                        if(data.status=="1"){
                            layer.msg("'.__('Successfully synchronized pull domains', 'mine-cloudvod').'");
                            var tcoptions = "", tdata = data.data;
                            for(var i=0; i<tdata.length; i++){
                            if(tdata[i]["Type"] == "publish")tcoptions += "<option value=\""+tdata[i]["DomainName"]+"\">"+tdata[i]["DomainName"]+"</option>";
                            }
                            jQuery("select[name=\'mcv_settings[alivod][PushDomain]\']").html(tcoptions);
                        }
                    }, "json");
                });
            }
            function mcv_modify_valid(type){
                if(type="pull"){
                    let pulldomain = jQuery("select[name=\'mcv_settings[alivod][PullDomain]\']").val();
                    if(!pulldomain){
                        layer.msg("'.__('Please sync Pull Domains first', 'mine-cloudvod').'");
                        return;
                    }
                    window.open("https://live.console.aliyun.com/#/domain/"+pulldomain+"/liveVideo/access");
                }
                else if(type="push"){
                    let pulldomain = jQuery("select[name=\'mcv_settings[alivod][PushDomain]\']").val();
                    if(!pulldomain){
                        layer.msg("'.__('Please sync Push Domains first', 'mine-cloudvod').'");
                        return;
                    }
                    window.open("https://live.console.aliyun.com/#/domain/"+pulldomain+"/liveEdge/access");
                }
            }
            function mcv_sync_ali_keyid(){
                var index = layer.load(1, {
                    shade: [0.3,"#fff"] 
                });
                jQuery(function(){
                    jQuery.post("'.$ajaxUrl.'", {action: "mcv_sync_ali_keyid", nonce: "'. wp_create_nonce('mcv_sync_ali_keyid') .'"}, function(data){
                        layer.close(index);
                        if(data.status=="0")layer.msg("'.__('Get failed', 'mine-cloudvod').'"+data.msg);
                        if(data.status=="1"){
                            layer.msg("'.__('Successfully get the KeyId', 'mine-cloudvod').'");
                            jQuery("input[data-depend-id=keyId]").val(data.data.keyId);
                        }
                    }, "json");
                });
            }
            function mcv_sync_endtime(){
                var index = layer.load(1, {
                    shade: [0.3,"#fff"] 
                });
                jQuery(function(){
                jQuery.post("'.$ajaxUrl.'", {action: "mcv_sync_endtime", nonce: "'. wp_create_nonce('mcv_sync_endtime') .'"}, function(data){
                    layer.close(index);
                    if(data.status=="0")layer.msg("'.__('Get failed', 'mine-cloudvod').'"+data.msg);
                    if(data.status=="1"){
                        layer.msg("'.__('Synchronize time successfully', 'mine-cloudvod').'");
                        jQuery("input[data-depend-id=endtime]").val(data.data.endtime);
                    }
                }, "json");
                });
            }
            function mcv_init_note(){
                var index = layer.load(1, {
                    shade: [0.3,"#fff"] 
                });
                jQuery(function(){
                jQuery.post("'.$ajaxUrl.'", {action: "mcv_note_init", nonce: "'. wp_create_nonce('mcv_note_init') .'"}, function(data){
                    layer.close(index);
                    layer.msg(data.msg);
                }, "json");
                });
            }
            function mcv_init_lms(){
                var index = layer.load(1, {
                    shade: [0.3,"#fff"] 
                });
                jQuery(function(){
                jQuery.post("'.$ajaxUrl.'", {action: "mcv_order_init", nonce: "'. wp_create_nonce('mcv-admin-nonce') .'"}, function(data){
                    layer.close(index);
                    layer.msg(data.data.msg);
                }, "json");
                });
            }
            function mcv_sync_transcode(){
                var index = layer.load(1, {
                    shade: [0.3,"#fff"] 
                });
                jQuery(function(){
                    jQuery.post("'.$ajaxUrl.'", {action: "mcv_asyc_transcode", nonce: "'. wp_create_nonce('mcv_asyc_transcode') .'"}, function(data){
                        layer.close(index);
                        if(data.status=="0")layer.msg("'.__('Get failed', 'mine-cloudvod').'"+data.msg);
                        if(data.status=="1"){
                            layer.msg("'.__('Successfully synchronized task flow list', 'mine-cloudvod').'");
                            var tcoptions = "", tdata = data.data;
                            for(var i=0; i<tdata.length; i++){
                            tcoptions += "<option value=\""+tdata[i][0]+"\">"+tdata[i][1]+" - "+tdata[i][0]+"</option>";
                            }
                            jQuery("select[data-depend-id=transcode]").html(tcoptions);
                        }
                    }, "json");
                });
            }
            function mcv_sync_ali_transcode(){
                var index = layer.load(1, {
                    shade: [0.3,"#fff"] 
                });
                jQuery(function(){
                    jQuery.post("'.$ajaxUrl.'", {action: "mcv_asyc_ali_transcode", nonce: "'. wp_create_nonce('mcv_asyc_ali_transcode') .'"}, function(data){
                        layer.close(index);
                        if(data.status=="0")layer.msg("'.__('Get failed', 'mine-cloudvod').'"+data.msg);
                        if(data.status=="1"){
                            layer.msg("'.__('Successfully synchronized transcoding template', 'mine-cloudvod').'");
                            var tcoptions = "", tdata = data.data;
                            for(var i=0; i<tdata.length; i++){
                            tcoptions += "<option value=\""+tdata[i][1]+"\">"+tdata[i][0]+"</option>";
                            }
                            jQuery("select[data-depend-id=transcode]").html(tcoptions);
                        }
                    }, "json");
                });
            }
            function mcv_sync_bunny_libs(){
                var index = layer.load(1, {
                    shade: [0.3,"#fff"] 
                });
                jQuery(function(){
                    jQuery.post("'.$ajaxUrl.'", {action: "mcv_sync_bunny_libs", nonce: "'. wp_create_nonce('mcv_sync_bunny_libs') .'"}, function(data){
                        layer.close(index);
                        if(data.status=="0")layer.msg("'.__('Get failed', 'mine-cloudvod').'"+data.msg);
                        if(data.status=="1"){
                            layer.msg("'.__('Successfully synchronized', 'mine-cloudvod').'");
                            var tcoptions = "", tdata = data.data;
                            for(var i=0; i<tdata.length; i++){
                            tcoptions += "<option value=\""+tdata[i]["Id"]+"\">"+tdata[i]["Name"]+"</option>";
                            }
                            jQuery("select[data-depend-id=transcode]").html(tcoptions);
                        }
                    }, "json");
                });
            }
            function mcv_asyc_plyrconfig(){
                var index = layer.load(1, {
                    shade: [0.3,"#fff"] 
                });
                jQuery(function(){
                jQuery.post("'.$ajaxUrl.'", {action: "mcv_asyc_plyrconfig", nonce: "'. wp_create_nonce('mcv_asyc_plyrconfig') .'"}, function(data){
                    layer.close(index);
                    if(data.status=="0")layer.msg("'.__('Get failed', 'mine-cloudvod').'"+data.msg);
                    if(data.status=="1"){
                        layer.msg("'.__('Successfully synchronized the player configuration list', 'mine-cloudvod').'");
                        var tcoptions = "", tdata = data.data;
                        for(var i=0; i<tdata.length; i++){
                            tcoptions += "<option value=\""+tdata[i][0]+"\">"+tdata[i][1]+" - "+tdata[i][0]+"</option>";
                        }
                        jQuery("select[data-depend-id=plyrconfig]").html(tcoptions);
                    }
                }, "json");
                });
            }
            jQuery(function(){
                function getQrCode(met){
                    var index = layer.load(1, {
                        shade: [0.3,"#fff"] 
                    });
                    jQuery.post("'.$ajaxUrl.'", {action: "mcv_buytimebug",met:met, timebug:jQuery(":radio[data-depend-id=timebug]:checked").val(), nonce: "'. wp_create_nonce('mcv_buytimebug') .'"}, function(data){
                        layer.closeAll();
                        if(data.status=="0")alert("'.__('Get failed', 'mine-cloudvod').'");
                        if(data.status=="1"){
                        var tradeno = data.data.tradeno;
                        var color="#00a7ef";
                        var plogo="'.MINECLOUDVOD_URL.'/static/img/alipay.jpg";
                        var txt1 = "'.__('Alipay scan code payment', 'mine-cloudvod').'";
                        var txt2 = "'.__('Please use Alipay <br>to scan the QR code to pay', 'mine-cloudvod').'";
                        var h = "435.4px";
                        if(met=="wxpay"){
                            color="#00b54b";
                            plogo="'.MINECLOUDVOD_URL.'/static/img/wxzf.jpg";
                            txt1 = "'.__('Wechat scan code payment', 'mine-cloudvod').'";
                            txt2 = "'.__('Please use Wechat <br>to scan the QR code to pay', 'mine-cloudvod').'";
                            h="422.4px";
                        }
                        layer.open({
                            type: 1,
                            title: false,
                            area: ["300px", h],
                            content: \'<style>.layui-layer-content{overflow:hidden !important;}#btb_alipay,#btb_wxpay{width:50%;color: #fff;display:inline-block;margin:0;padding:0;border:none;cursor:pointer;padding:7px 0;background:#ddd;}#btb_alipay{background:#00a7ef;border-color:#00a7ef;}#btb_wxpay{background:#00b54b;border-color:#00b54b;}</style><div id="swal2-content" style="display: block;width:300px;text-align: center;"><div style="border-bottom: 2px solid \'+color+\';"><input type="button" id="btb_alipay" class="" value="Alipay"><input type="button" id="btb_wxpay" class="cur" value="Wechat"></div><div style=""> <h5 style="padding: 0;margin-top: 1.8em;"> <img src="\'+plogo+\'" style="display: inline-block;margin: 0;padding: 0;width: 120px;text-align: center;"> </h5> <div style="font-size: 16px;margin: 10px auto;">\'+txt1+\' \'+data.data.payamount+\' '.__('Yuan', 'mine-cloudvod').'</div> <div align="center" class="qrcode"> <img style="width: 200px;height: 200px;" src="\'+data.data.paycode+\'" id="buytimebug_qrcode"> </div> <div style="width: 100%;color: #f2f2f2;padding: 16px 0px;text-align: center;font-size: 14px;margin-top: 20px;background: \'+color+\';"> \'+txt2+\'<br> </div> </div></div>\',
                            success: function(layero, index){
                                jQuery("#btb_alipay", layero).on("click", function(){
                                    getQrCode("alipay");
                                });
                                jQuery("#btb_wxpay", layero).on("click", function(){
                                    getQrCode("wxpay");
                                });
                            }
                        });
                        }
                    }, "json");
                }
                jQuery("#buytimebug").click(function(){
                    getQrCode("alipay");
                });
            });'));
        }
        // global $post_type;
        // if($hook == 'edit.php' && $post_type == 'mcv_video'){
        //     global $mcv_classes;
        //     wp_enqueue_script('jquery');
        //     wp_localize_script('jquery','mcv_switch', [
        //         'vod' => [
        //             'aliyun' => $mcv_classes->Addons->is_addons_actived('aliyun'),
        //             'qcloud' => $mcv_classes->Addons->is_addons_actived('qcloud'),
        //             'dogecloud' => $mcv_classes->Addons->is_addons_actived('doge'),
        //             'qiniukodo' => $mcv_classes->Addons->is_addons_actived('qiniukodo'),
        //         ],
        //     ]);
        // }
    }
}