<?php
namespace MineCloudvod\Payment;
use MineCloudvod\Libs\QRcode;

if ( ! defined( 'ABSPATH' ) ) exit;
class Alipay extends Base {
    public function __construct(){
        add_action( 'mcv_add_admin_options_before_purchase', [ $this, 'alipay_admin_options' ] );

        add_action('rest_api_init', [$this, 'register_routes']);
    }

    public function register_routes(){
        /**
         * Alipay支付回调
         */
        register_rest_route('mine-cloudvod/v1', '/alipay_notify', [
            [
                'methods'             => \WP_REST_Server::EDITABLE,
                'callback'            => [$this, 'mcv_alipay_notify'],
                'permission_callback' => '__return_true',
                'args'                => [
					'out_trade_no' => [
						'validate_callback' => function ($param) {
							return is_numeric($param);
						}
					],
                    'trade_status' => [
                        'type' => 'string',
                    ],
                    'sign' => [
                        'type' => 'string',
                    ],
                ]
            ],
        ]);
    }
    /**
     * 支付成功回调
     */
    public function mcv_alipay_notify( \WP_REST_Request $request ){
        $aliPay = new AlipayService();
        $aliPay->setAlipayPublicKey( MINECLOUDVOD_SETTINGS['mcv_payment']['alipay']['public_key'] );
        //验证签名
        $result = $aliPay->rsaCheck($_POST,$_POST['sign_type']);
        
        if($result===true && $_POST['trade_status'] == 'WAIT_BUYER_PAY'){
            $tradeno = $_POST['out_trade_no'];
            if( is_numeric( $tradeno ) ){
                $order = get_post( $tradeno );
                if( $order ){
                    update_post_meta( $order->ID, '_mcv_order_status', 'paying' );
                    // 交易创建时间
                    update_post_meta( $order->ID, '_mcv_order_gmt_create', $_POST['gmt_create'] );
                    //支付宝账号
                    $buyer_logon_id = sanitize_text_field( $_POST['buyer_logon_id'] );
                    update_post_meta( $order->ID, '_mcv_order_buyer_logon_id', $buyer_logon_id );
                }
            }
        }
        elseif($result===true && $_POST['trade_status'] == 'TRADE_SUCCESS'){
            $tradeno = $_POST['out_trade_no'];
            if( is_numeric( $tradeno ) ){
                $order = get_post( $tradeno );
                if( $order ){
                    //收款额度
                    $total_amount = sanitize_text_field( $_POST['total_amount'] );
                    $order_amount = mcv_lms_get_order_last_amount( $order->ID );
                    $order_items = get_post_meta( $order->ID, '_mcv_order_items', true );
                    if( $total_amount == $order_amount && $order_items ){
                        // 支付宝订单号
                        $trade_no = sanitize_text_field( $_POST['trade_no'] );
                        //支付宝账号
                        $buyer_logon_id = sanitize_text_field( $_POST['buyer_logon_id'] );
                        //付款时间
                        $gmt_payment = sanitize_text_field( $_POST['gmt_payment'] );
                        
                        update_post_meta( $order->ID, '_mcv_order_status', 'payed' );
                        update_post_meta( $order->ID, '_mcv_order_transaction_id', $trade_no) ;
                        update_post_meta( $order->ID, '_mcv_order_buyer_logon_id', $buyer_logon_id );
                        update_post_meta( $order->ID, '_mcv_order_gmt_payment', $gmt_payment );
                        
                        mcv_order_update_items( $order_items, $order->post_author, $order, $total_amount );

                        update_post_meta( $order->ID, '_mcv_order_payment', 'alipay' );
                        echo 'success';
                    }
                    exit();
                }
            }
        }
    }

    public function alipay_admin_options(){
        $prefix = 'mcv_settings';
        \MCSF::createSection($prefix, array(
            'parent'     => 'mcv_payment',
            'title'  => __('Alipay', 'mine-cloudvod'),
            'icon'   => 'fab fa-alipay',
            'fields' => array(
                array(
                    'id'        => 'mcv_payment',
                    'type'      => 'fieldset',
                    'title'     => '',
                    'fields'    => array(
                        array(
                            'type'    => 'submessage',
                            'style'   => 'warning',
                            'content' => __('支付宝官方支付', 'mine-cloudvod'), 
                        ),
                        array(
                            'id'        => 'alipay',
                            'type'      => 'fieldset',
                            'title'     => '',
                            'fields'    => array(
                                array(
                                    'id'    => 'status',
                                    'type'  => 'switcher',
                                    'title' => __('State', 'mine-cloudvod'),
                                    'text_on'    => __('Enable', 'mine-cloudvod'),
                                    'text_off'   => __('Disable', 'mine-cloudvod'),
                                    'default' => false,
                                ),
                                array(
                                    'id'    => 'class',
                                    'type'  => 'text',
                                    'title' => '',
                                    'dependency' => array('status', '==', 'none'),
                                    'default' => 'MineCloudvod\Payment\Alipay',
                                ),
                                array(
                                    'type'    => 'submessage',
                                    'style'   => 'danger',
                                    'content' => __('签名方法仅支持: RSA2', 'mine-cloudvod'),
                                    'dependency' => array('status', '==', 'true'),
                                ),
                                array(
                                    'id'      => 'product',
                                    'type'    => 'checkbox',
                                    'title'   => __('Product', 'mine-cloudvod'),
                                    'inline'  => true,
                                    'options' => array(
                                        '1'     => __('PC Website Payment', 'mine-cloudvod'),
                                        '2'     => __('Mobile Website Payment', 'mine-cloudvod'),
                                        '3'     => __('Pay Face to Face', 'mine-cloudvod'),
                                    ),
                                    'dependency' => array('status', '==', true),
                                ),
                                array(
                                    'type'    => 'submessage',
                                    'style'   => 'success',
                                    'content' => __('启用当面付时，会优先使用当面付，直接扫码支付，无需跳转。', 'mine-cloudvod'),
                                    'dependency' => array('status|product', '==|any', 'true|3'),
                                ),
                                array(
                                    'id'    => 'name',
                                    'type'  => 'text',
                                    'title' => __('Name' , 'mine-cloudvod'),
                                    'dependency' => array('status', '==', true),
                                    'default' => '支付宝',
                                ),
                                array(
                                    'id'    => 'appId',
                                    'type'  => 'text',
                                    'title' => __('AppId' , 'mine-cloudvod'),
                                    'dependency' => array('status', '==', true),
                                ),
                                array(
                                    'id'    => 'public_key',
                                    'type'  => 'textarea',
                                    'title' => __('Alipay', 'mine-cloudvod').__('Public Key' , 'mine-cloudvod'),
                                    'dependency' => array('status', '==', true),
                                ),
                                array(
                                    'id'    => 'private_key',
                                    'type'  => 'textarea',
                                    'title' => __('Application', 'mine-cloudvod').__('Private Key' , 'mine-cloudvod'),
                                    'dependency' => array('status', '==', true),
                                ),
                            ),
                        ),
                    ),
                ),
            )
        ));
    }

    public function handlePayment( $payAmount, $outTradeNo, $orderName, $course_id, $request ){
        $product = MINECLOUDVOD_SETTINGS['mcv_payment']['alipay']['product'] ?? false;
        if( $product && is_array( $product ) ){

            $appid = MINECLOUDVOD_SETTINGS['mcv_payment']['alipay']['appId'];
            $notifyUrl = get_rest_url() . 'mine-cloudvod/v1/alipay_notify';
            $signType = 'RSA2';
            $rsaPrivateKey= MINECLOUDVOD_SETTINGS['mcv_payment']['alipay']['private_key'];

            $aliPay = new AlipayService();
            $aliPay->setAppid($appid);
            $aliPay->setNotifyUrl($notifyUrl);
            $aliPay->setRsaPrivateKey($rsaPrivateKey);
            $aliPay->setTotalFee($payAmount);
            $aliPay->setOutTradeNo($outTradeNo);
            $aliPay->setOrderName($orderName);
            $result = [];
            $is_mobile = wp_is_mobile();
            if( in_array('3', $product ) && !$is_mobile ){
                $result = $aliPay->doPay_Face2Face();
                $result = $result['alipay_trade_precreate_response'];
                if($result['code'] && $result['code']=='10000'){
                    //生成二维码
                    $qrimg = $this->getQrcode( $result['qr_code'] );
                    $result['qrimg'] = $qrimg;
                }
                //保存qr_code, 2h内再利用
                update_post_meta( $outTradeNo, '_mcv_order_qr_code', $result['qr_code'] );
                $result['mcv_method'] = 'face2face';
                $result['alipay'] = MINECLOUDVOD_URL.'/static/img/alipay.jpg';
            }
            else{
                $course_url = ( get_the_permalink( $course_id ) );
                $aliPay->setReturnUrl( $course_url );
                if( $is_mobile && in_array('2', $product ) ){
                    $result = $aliPay->doPay_Mobile();
                    $result['mcv_method'] = 'mobile';
                }
                elseif( in_array('1', $product ) ){
                    $result = $aliPay->doPay_PC();
                    $result['mcv_method'] = 'pc';
                }
            }
            return $result;
        }
    }
    /**
     * 返回需要执行的js代码
     */
    public function handleScripts( $request ){
        $script = '';
        return $script;
    }

    public function getQrcode($url){
        $errorCorrectionLevel = 'L'; //容错级别
        $matrixPointSize      = 6; //生成图片大小
        ob_start();
        QRcode::png($url, false, $errorCorrectionLevel, $matrixPointSize, 2);
        $data = ob_get_contents();
        ob_end_clean();

        $imageString = base64_encode($data);
        return 'data:image/jpeg;base64,'.$imageString;
    }
}

class AlipayService{
    protected $appId;
    protected $notifyUrl;
    protected $charset;
    // 私钥值
    protected $rsaPrivateKey;
    // 公钥
    protected $alipayPublicKey;
    protected $totalFee;
    protected $outTradeNo;
    protected $orderName;
    protected $returnUrl;

    public function __construct()
    {
        $this->charset = 'utf-8';
    }

    public function setAppid($appid)
    {
        $this->appId = $appid;
    }

    public function setNotifyUrl($notifyUrl)
    {
        $this->notifyUrl = $notifyUrl;
    }

    public function setRsaPrivateKey($saPrivateKey)
    {
        $this->rsaPrivateKey = $saPrivateKey;
    }

    public function setAlipayPublicKey($alipayPublicKey)
    {
        $this->alipayPublicKey = $alipayPublicKey;
    }

    public function setTotalFee($payAmount)
    {
        $this->totalFee = $payAmount;
    }

    public function setOutTradeNo($outTradeNo)
    {
        $this->outTradeNo = $outTradeNo;
    }

    public function setOrderName($orderName)
    {
        $this->orderName = $orderName;
    }
    public function setReturnUrl($returnUrl)
    {
        $this->returnUrl = $returnUrl;
    }

    /**
     *  验证签名
     **/
    public function rsaCheck($params) {
        $sign = $params['sign'];
        $signType = $params['sign_type'];
        unset($params['sign_type']);
        unset($params['sign']);
        return $this->verify($this->getSignContent($params), $sign, $signType);
    }

    function verify($data, $sign, $signType = 'RSA') {
        $data = str_replace('\"', '"', $data);
        $pubKey= $this->alipayPublicKey;
        $res = "-----BEGIN PUBLIC KEY-----\n" .
            wordwrap($pubKey, 64, "\n", true) .
            "\n-----END PUBLIC KEY-----";
        ($res) or die('支付宝RSA公钥错误。请检查公钥文件格式是否正确');

        //调用openssl内置方法验签，返回bool值
        if ("RSA2" == $signType) {
            $result = (bool)openssl_verify($data, base64_decode($sign), $res, version_compare(PHP_VERSION,'5.4.0', '<') ? SHA256 : OPENSSL_ALGO_SHA256);
        } else {
            $result = (bool)openssl_verify($data, base64_decode($sign), $res);
        }
        return $result;
    }


    /**
     * 当面付
     * @return array
     */
    public function doPay_Face2Face(){
        //请求参数
        $requestConfigs = array(
            'out_trade_no'=>$this->outTradeNo,
            'total_amount'=>$this->totalFee, //单位 元
            'subject'=>$this->orderName,
            'timeout_express'=>'2h'
        );
        $commonConfigs = array(
            //公共参数
            'app_id' => $this->appId,
            'method' => 'alipay.trade.precreate',//接口名称
            'format' => 'JSON',
            'charset'=>$this->charset,
            'sign_type'=>'RSA2',
            'timestamp'=>date('Y-m-d H:i:s'),
            'version'=>'1.0',
            'notify_url' => $this->notifyUrl,
            'biz_content'=>json_encode($requestConfigs),
        );
        $commonConfigs["sign"] = $this->generateSign($commonConfigs, $commonConfigs['sign_type']);
        $result = $this->curlPost('https://openapi.alipay.com/gateway.do?charset='.$this->charset,$commonConfigs);
        return json_decode($result,true);
    }
    /**
     * 电脑网站支付
     * @return array
     */
    public function doPay_PC(){
        //请求参数
        $requestConfigs = array(
            'out_trade_no'=>$this->outTradeNo,
            'product_code'=>'FAST_INSTANT_TRADE_PAY',
            'total_amount'=>$this->totalFee, //单位 元
            'subject'=>$this->orderName,  //订单标题
        );
        $commonConfigs = array(
            //公共参数
            'app_id' => $this->appId,
            'method' => 'alipay.trade.page.pay',             //接口名称
            'format' => 'JSON',
            'return_url' => $this->returnUrl,
            'charset'=>$this->charset,
            'sign_type'=>'RSA2',
            'timestamp'=>date('Y-m-d H:i:s'),
            'version'=>'1.0',
            'notify_url' => $this->notifyUrl,
            'biz_content'=>json_encode($requestConfigs),
        );
        $commonConfigs["sign"] = $this->generateSign($commonConfigs, $commonConfigs['sign_type']);
        return $commonConfigs;
    }
    /**
     * 手机网站支付
     * @return array
     */
    public function doPay_Mobile(){
        //请求参数
        $requestConfigs = array(
            'out_trade_no'=>$this->outTradeNo,
            'product_code'=>'QUICK_WAP_WAY',
            'total_amount'=>$this->totalFee, //单位 元
            'subject'=>$this->orderName,  //订单标题
        );
        $commonConfigs = array(
            //公共参数
            'app_id' => $this->appId,
            'method' => 'alipay.trade.wap.pay',             //接口名称
            'format' => 'JSON',
            'return_url' => $this->returnUrl,
            'charset'=>$this->charset,
            'sign_type'=>'RSA2',
            'timestamp'=>date('Y-m-d H:i:s'),
            'version'=>'1.0',
            'notify_url' => $this->notifyUrl,
            'biz_content'=>json_encode($requestConfigs),
        );
        $commonConfigs["sign"] = $this->generateSign($commonConfigs, $commonConfigs['sign_type']);
        return $commonConfigs;
    }

    public function generateSign($params, $signType = "RSA") {
        return $this->sign($this->getSignContent($params), $signType);
    }
    protected function sign($data, $signType = "RSA") {
        $priKey=$this->rsaPrivateKey;
        $res = "-----BEGIN RSA PRIVATE KEY-----\n" .
            wordwrap($priKey, 64, "\n", true) .
            "\n-----END RSA PRIVATE KEY-----";
        ($res) or die('您使用的私钥格式错误，请检查RSA私钥配置');
        if ("RSA2" == $signType) {
            openssl_sign($data, $sign, $res, version_compare(PHP_VERSION,'5.4.0', '<') ? SHA256 : OPENSSL_ALGO_SHA256); //OPENSSL_ALGO_SHA256是php5.4.8以上版本才支持
        } else {
            openssl_sign($data, $sign, $res);
        }
        $sign = base64_encode($sign);
        return $sign;
    }
    /**
     * 校验$value是否非空
     *  if not set ,return true;
     *    if is null , return true;
     **/
    protected function checkEmpty($value) {
        if (!isset($value))
            return true;
        if ($value === null)
            return true;
        if (trim($value) === "")
            return true;
        return false;
    }
    public function getSignContent($params) {
        ksort($params);
        $stringToBeSigned = "";
        $i = 0;
        foreach ($params as $k => $v) {
            if (false === $this->checkEmpty($v) && "@" != substr($v, 0, 1)) {
                // 转换成目标字符集
                $v = $this->characet($v, $this->charset);
                if ($i == 0) {
                    $stringToBeSigned .= "$k" . "=" . "$v";
                } else {
                    $stringToBeSigned .= "&" . "$k" . "=" . "$v";
                }
                $i++;
            }
        }
        unset ($k, $v);
        return $stringToBeSigned;
    }
    /**
     * 转换字符集编码
     * @param $data
     * @param $targetCharset
     * @return string
     */
    function characet($data, $targetCharset) {
        if (!empty($data)) {
            $fileType = $this->charset;
            if (strcasecmp($fileType, $targetCharset) != 0) {
                $data = mb_convert_encoding($data, $targetCharset, $fileType);
                //$data = iconv($fileType, $targetCharset.'//IGNORE', $data);
            }
        }
        return $data;
    }
    public function curlPost($url = '', $postData = '', $options = array())
    {
        if (is_array($postData)) {
            $postData = http_build_query($postData);
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30); //设置cURL允许执行的最长秒数
        if (!empty($options)) {
            curl_setopt_array($ch, $options);
        }
        //https请求 不验证证书和host
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }
}
