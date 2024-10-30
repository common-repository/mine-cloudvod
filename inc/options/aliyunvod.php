<?php
\MCSF::createSection( $prefix, array(
    'parent'     => 'aliyunvod',
    'title'  => __('ApsaraVideo VOD', 'mine-cloudvod'),
    'icon'   => 'fas fa-video',
    'fields' => array(
        array(
        'type'    => 'submessage',
        'style'   => 'warning',
        'content' => __('By default, Alibaba Cloud VOD is charged after the end of the day, and it can also be found on <a href="https://www.aliyun.com/minisite/goods?userCode=49das3ha" target="_blank">Alibaba Cloud VOD Platform</a> Purchase the corresponding resource pack consumption.', 'mine-cloudvod'),//'<p>阿里云视频点播默认是日结后收费模式，也可以在 <a href="https://www.aliyun.com/minisite/goods?userCode=49das3ha" target="_blank">阿里云视频点播平台</a> 购买相应的资源包消费</p>',
        ),
        array(
        'id'        => 'alivod',
        'type'      => 'fieldset',
        'title'     => __('ApsaraVideo VOD', 'mine-cloudvod'),//'阿里云视频点播',
        'fields'    => array(
            array(
            'id'    => 'userId',
            'type'  => 'text',
            'title' => 'UserId',
            ),
            array(
            'id'          => 'endpoint',
            'type'        => 'select',
            'title'       => __('Storage area', 'mine-cloudvod'),//'存储区域',
            'placeholder' => __('Select storage area', 'mine-cloudvod'),//'选择区域',
            'options'     => MINECLOUDVOD_ALIYUNVOD_ENDPOINT,
            'default'     => 'cn-shanghai'
            ),
            array(
            'id'          => 'transcode',
            'type'        => 'select',
            'title'       => __('Transcoding template', 'mine-cloudvod'),//'转码模板',
            'after'       => '<p><a href="javascript:mcv_sync_ali_transcode();">'.__('Sync transcoding template', 'mine-cloudvod').'</a></p>',//同步转码模板组,
            'placeholder' => __('Select transcoding template', 'mine-cloudvod'),//'选择转码模板',
            'options'     => $mcv_ali_transcode,
            'default'     => 'VOD_NO_TRANSCODE'
            ),
            array(
                'id'    => 'down_snapshot',
                'type'  => 'switcher',
                'title' => __('Download Snapshot', 'mine-cloudvod'),//'HLS标准加密',
                'after' => __('<br /><p>After enabling, the snapshot of video will download to Media Libary, and it will be the feature image of the post when there has no feature image.</p>', 'mine-cloudvod'),
                'text_on'    => __('Enable', 'mine-cloudvod'),//'启用',
                'text_off'   => __('Disable', 'mine-cloudvod'),//'禁用',
                'default' => false
            ),
            array(
                'id'    => 'encrypt',
                'type'  => 'switcher',
                'title' => __('Standard HLS encryption', 'mine-cloudvod'),//'HLS标准加密',
                'after' => __('<br /><p style="color:red">After enabling, the video uploaded through the plugin will be automatically encrypted with Standard HLS encryption</p>', 'mine-cloudvod'),//'<br /><p style="color:red">启用后，通过插件上传的视频会自动进行标准加密</p>',
                'text_on'    => __('Enable', 'mine-cloudvod'),//'启用',
                'text_off'   => __('Disable', 'mine-cloudvod'),//'禁用',
                'default' => false
            ),
            array(
            'type'    => 'submessage',
            'style'   => 'success',
            'dependency' => array( 'encrypt', '==', true ),
            'content' => '
            <p>使用流程：</p>
            <p>1. <a href="https://help.aliyun.com/zh/vod/getting-started/activate-apsaravideo-vod" target="_blank">开通视频点播</a>.</p>
            <p>2. <a href="https://ram.console.aliyun.com/role/authorization?spm=a2c4g.11186623.0.0.4bc62453ncagLb&request=%7B%22Services%22%3A%5B%7B%22Service%22%3A%22VOD%22%2C%22Roles%22%3A%5B%7B%22RoleName%22%3A%22AliyunVODDefaultRole%22%2C%22TemplateId%22%3A%22DefaultRole%22%7D%5D%7D%5D%2C%22ReturnUrl%22%3A%22https%3A%2F%2Fvod.console.aliyun.com%2F%22%7D" target="_blank">云资源访问授权</a>.</p>
            <p>3. <a href="https://vod.console.aliyun.com/#/settings/encryption" target="_blank">创建service key</a>.</p>
            ',
            ),
            array(
            'type'    => 'submessage',
            'style'   => 'warning',
            'dependency' => array( 'encrypt', '==', true ),
            'content' => '
            <p>注意事项：</p>
            <p>1. 转码模板必须选择封装格式为hls、视频加密开启的模板.</p>
            <p>2. 必须开启HLS标准加密参数透传。<p>
            ',
            ),
            array(
            'id'    => 'keyId',
            'type'  => 'text',
            'title' => 'KeyId',
            'dependency' => array( 'encrypt', '==', true ),
            'subtitle'=>__('KMS KeyID', 'mine-cloudvod'),//秘钥管理服务id
            'before' => __('Used for <a href="https://help.aliyun.com/document_detail/68612.htm?spm=a2c4g.11186623.0.0.5ceb2074txQis7#title-rvd-6ql-49n" target="_blank">Standard HLS encryption</a>, need to submit a ticket to apply for activation', 'mine-cloudvod'),//'用于<a href="https://help.aliyun.com/document_detail/68612.htm?spm=a2c4g.11186623.0.0.5ceb2074txQis7#title-rvd-6ql-49n" target="_blank">hls标准加密</a>，需要提交工单申请开通',
            'after' => '<a href="javascript:mcv_sync_ali_keyid();">获取KeyId</a>',
            ),
            array(
            'id'    => 'token',
            'type'  => 'text',
            'title' => __('Security key', 'mine-cloudvod'),//'安全密钥',
            'dependency' => array( 'encrypt', '==', true ),
            'after' => __('Security verification for playing standard hls encrypted video', 'mine-cloudvod'),//'用于播放HLS标准加密视频的安全验证',
            'default' => time()
            ),
            array(
            'id'    => 'tokenTime',
            'type'  => 'number',
            'title' => __('Valid Duration', 'mine-cloudvod'),//'有效时长',
            'subtitle' => __('Valid duration of transparent transmission parameters', 'mine-cloudvod'),//'透传参数的有效时长',
            'dependency' => array( 'encrypt', '==', true ),
            'after' => __('Hour', 'mine-cloudvod'),//'小时',
            'default' => 10
            ),
        ),
        ),
    
    )
    ) );