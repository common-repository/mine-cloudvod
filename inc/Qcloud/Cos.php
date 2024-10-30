<?php
namespace MineCloudvod\Qcloud;

class Cos
{
    private $_wpcvApi;

    public function __construct(){
        global $McvApi;
        $this->_wpcvApi     = $McvApi;
        add_action('wp_ajax_mcv_asyc_tccos_buckets', array($this, 'mcv_asyc_tccos_buckets'));

        add_action( 'mcv_add_admin_options_before_purchase', array( $this, 'qcloud_admin_options' ) );
    }

    public function qcloud_admin_options(){
        $prefix = 'mcv_settings';
        $mcv_alioss_bucketsList = array('' => __('Please sync Bukcets List first', 'mine-cloudvod'));//'请先同步转码模板');
        if($tctc = get_option('mcv_tccos_bucketsList')){
            $mcv_alioss_bucketsList = array();
            foreach($tctc as $tc){
                $mcv_alioss_bucketsList[$tc[0]] =  $tc[0];
            }
        }

        \MCSF::createSection( $prefix, array(
            'parent'     => 'tencentvod',
            'title'  => __('Tencent COS', 'mine-cloudvod'),//'腾讯云COS',
            'icon'   => 'far fa-file-video',
            'fields' => array(
                array(
                'type'    => 'submessage',
                'style'   => 'warning',
                'content' => __('By default, Tencent Cloud VOD is charged after the end of the day, and it can also be found on <a href="https://curl.qcloud.com/F8Ad6KaX" target="_blank">Tencent Cloud VOD Platform</a> Purchase the corresponding resource pack consumption.', 'mine-cloudvod'),//'<p>腾讯云点播默认是日结后收费模式，也可以在 <a href="https://curl.qcloud.com/F8Ad6KaX" target="_blank">腾讯云点播平台</a> 购买相应的资源包消费</p>',
                ),
                array(
                'id'        => 'tcvod',
                'type'      => 'fieldset',
                'title'     => '',
                'fields'    => array(
                    array(
                    'id'          => 'buckets',
                    'type'        => 'select',
                    'title'       => __('Bucket', 'mine-cloudvod'),//'转码模板',
                    'after'       => '<p><a href="javascript:mcv_sync_tccos_buckets();">'.__('Sync Buckets List', 'mine-cloudvod').'</a></p>',//同步Bucket列表,
                    'options'     => $mcv_alioss_bucketsList,
                    'default'     => ''
                    ),
                ),
                ),
            
            )
            ) );
    }

    public function get_mediaUrl($objcet, $bucket){
        $data = array(
            'bucket'  => $bucket,
            'object' => $objcet,
            'mode' => 'tccos'
        );
        $playinfo = $this->_wpcvApi->call('geturl', $data);
        return $playinfo;
    }
    
    public function mcv_asyc_tccos_buckets(){
        if(!current_user_can('manage_options')){
            echo json_encode(array('status' => '0', 'msg' => __('Illegal request', 'mine-cloudvod')));exit;
        }
        header('Content-type:application/json; Charset=utf-8');
        $nonce   = !empty($_POST['nonce']) ? $_POST['nonce'] : null;
        if ($nonce && !wp_verify_nonce($nonce, 'mcv_asyc_tccos_buckets')) {
            echo json_encode(array('status' => '0', 'msg' => __('Illegal request', 'mine-cloudvod')));exit;
        }
        $data = array('bucket'=>'mcv','mode' => 'tccos');
        $buckets = $this->_wpcvApi->call('buckets', $data); 
        update_option('mcv_tccos_bucketsList', $buckets['data']);
        echo json_encode($buckets);
        exit;
    }
}
