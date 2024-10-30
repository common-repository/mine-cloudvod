<?php
namespace MineCloudvod\Aliyun;

class Oss{
    private $_wpcvApi;

    public function __construct(){
        global $McvApi;
        $this->_wpcvApi     = $McvApi;
        add_action('wp_ajax_mcv_asyc_alioss_buckets', array($this, 'mcv_asyc_alioss_buckets'));
        add_action( 'mcv_add_admin_options_before_purchase', array( $this, 'aliyun_admin_options' ) );
    }

    public function get_mediaUrl($objcet, $bucket){
        $data = array(
            'bucket'  => $bucket,
            'object' => $objcet,
            'mode' => 'alioss'
        );
        $playinfo = $this->_wpcvApi->call('geturl', $data);
        return $playinfo;
    }

    public function aliyun_admin_options(){
        $prefix = 'mcv_settings';
        $ajaxUrl = admin_url("admin-ajax.php");
        $mcv_alioss_bucketsList = array('' => __('Please sync Bukcets List first', 'mine-cloudvod'));//'请先同步转码模板');
        if($tctc = get_option('mcv_alioss_bucketsList')){
            $mcv_alioss_bucketsList = array();
            foreach($tctc as $tc){
                $mcv_alioss_bucketsList[$tc[0]] =  $tc[0];
            }
        }

        \MCSF::createSection( $prefix, array(
        'parent'     => 'aliyunvod',
        'title' => __('Aliyun OSS', 'mine-cloudvod'),//'阿里云OSS',
        'icon'   => 'far fa-file-video',
        'fields' => array(
            array(
            'type'    => 'submessage',
            'style'   => 'warning',
            'content' => __('By default, Alibaba Cloud OSS is charged after the end of the hour, and it can also be found on <a href="https://www.aliyun.com/minisite/goods?userCode=49das3ha" target="_blank">Alibaba Cloud OSS Platform</a> Purchase the corresponding resource pack consumption.', 'mine-cloudvod'),//'<p>阿里云视频点播默认是时结后收费模式，也可以在 <a href="https://www.aliyun.com/minisite/goods?userCode=49das3ha" target="_blank">阿里云平台</a> 购买相应的资源包消费</p>',
            ),
            array(
            'id'        => 'alivod',
            'type'      => 'fieldset',
            'title'     => __('Aliyun OSS', 'mine-cloudvod'),//'阿里云对象存储 OSS',
            'fields'    => array(
                array(
                'id'          => 'buckets',
                'type'        => 'select',
                'title'       => __('Bucket', 'mine-cloudvod'),//'存储桶',
                'after'       => '<p><a href="javascript:mcv_sync_alioss_buckets();">'.__('Sync Buckets List', 'mine-cloudvod').'</a></p>',//同步Bucket列表,
                'options'     => $mcv_alioss_bucketsList,
                'default'     => ''
                ),
            ),
            ),

        )
        ) );
    }
    
    public function mcv_asyc_alioss_buckets(){
        if(!current_user_can('manage_options')){
            echo json_encode(array('status' => '0', 'msg' => __('Illegal request', 'mine-cloudvod')));exit;
        }
        header('Content-type:application/json; Charset=utf-8');
        $nonce   = !empty($_POST['nonce']) ? $_POST['nonce'] : null;
        if ($nonce && !wp_verify_nonce($nonce, 'mcv_asyc_alioss_buckets')) {
            echo json_encode(array('status' => '0', 'msg' => __('Illegal request', 'mine-cloudvod')));exit;
        }
        $data = array('bucket'=>'mcv','mode' => 'alioss');
        $buckets = $this->_wpcvApi->call('buckets', $data);
        update_option('mcv_alioss_bucketsList', $buckets['data']);
        echo json_encode($buckets);
        exit;
    }

}
