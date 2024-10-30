<?php
namespace MineCloudvod\Payment;

class Hupijiao extends Base {
    public function __construct( ) {

        add_action( 'mcv_add_admin_options_before_purchase', [ $this, 'hpj_admin_options' ] );

        add_action('rest_api_init', [$this, 'register_routes']);

        add_filter( 'mcv_order_payment_methods', [ $this, 'add_payment_methods' ] );
    }

    public function add_payment_methods( $methods ){
        if( MINECLOUDVOD_SETTINGS['mcv_payment']['hupijiao_alipay']['status']??false ){
            $methods['hupijiao_alipay'] = __('Hupijiao Alipay', 'mine-cloudvod');
        }
        if( MINECLOUDVOD_SETTINGS['mcv_payment']['hupijiao_wechat']['status']??false ){
            $methods['hupijiao_wechat'] = __('Hupijiao Wechat', 'mine-cloudvod');
        }
        return $methods;
    }

    public function register_routes(){
        /**
         * Alipay支付回调
         */
        register_rest_route('mine-cloudvod/v1', '/hupijiao_alipay_notify', [
            [
                'methods'             => \WP_REST_Server::EDITABLE,
                'callback'            => [$this, 'mcv_hupijiao_alipay_notify'],
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
        /**
         * Alipay支付回调
         */
        register_rest_route('mine-cloudvod/v1', '/hupijiao_wechat_notify', [
            [
                'methods'             => \WP_REST_Server::EDITABLE,
                'callback'            => [$this, 'mcv_hupijiao_wechat_notify'],
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
     * 支付回调
     */
    public function mcv_hupijiao_wechat_notify( \WP_REST_Request $request ){
        $appid      = MINECLOUDVOD_SETTINGS['mcv_payment']['hupijiao_wechat']['appId'];
        $appsecret  = MINECLOUDVOD_SETTINGS['mcv_payment']['hupijiao_wechat']['appSecret'];
        $my_plugin_id       = 'mcv_order';

        $data = $_POST;
        foreach ($data as $k=>$v){
            $data[$k] = stripslashes($v);
        }
        if(!isset($data['hash'])||!isset($data['trade_order_id'])){
            echo 'failed';exit;
        }

        //自定义插件ID,请与支付请求时一致
        if(isset( $data['plugins'] ) && $data['plugins']!=$my_plugin_id){
            echo 'failed';exit;
        }

        $hupijiaoService = new HupijiaoService( $appid, $appsecret );
        $hash = $hupijiaoService->generateHash( $data, $appsecret );
        
        if($data['hash']!=$hash){
            //签名验证失败
            echo 'failed';exit;
        }

        //商户订单ID
        $tradeno =$data['trade_order_id'];

        if($data['status']=='OD'){
            if( is_numeric( $tradeno ) ){
                $order = get_post( $tradeno );
                if( $order ){
                    //收款额度
                    $total_amount = sanitize_text_field( $_POST['total_fee'] );
                    $order_amount = mcv_lms_get_order_last_amount( $order->ID );
                    $order_items = get_post_meta( $order->ID, '_mcv_order_items', true );
                    if( $total_amount == $order_amount && $order_items ){
                        // 支付平台内部交易号
                        $trade_no = sanitize_text_field( $_POST['transaction_id'] );
                        //虎皮椒内部订单号
                        $open_order_id = sanitize_text_field( $_POST['open_order_id'] );
                        //付款时间
                        $gmt_payment = date( 'Y-m-d H:i:s' );
                        
                        update_post_meta( $order->ID, '_mcv_order_status', 'payed' );
                        update_post_meta( $order->ID, '_mcv_order_transaction_id', $trade_no) ;
                        update_post_meta( $order->ID, '_mcv_order_hpj_order_id', $open_order_id );
                        update_post_meta( $order->ID, '_mcv_order_gmt_payment', $gmt_payment );
                        
                        mcv_order_update_items( $order_items, $order->post_author, $order, $total_amount );

                        update_post_meta( $order->ID, '_mcv_order_payment', 'hupijiao_wechat' );
                        echo 'success';
                    }
                    exit();
                }
            }
        }else{
            if( is_numeric( $tradeno ) ){
                $order = get_post( $tradeno );
                if( $order ){
                    update_post_meta( $order->ID, '_mcv_order_status', 'paying' );
                    // 支付平台内部交易号
                    $trade_no = sanitize_text_field( $_POST['transaction_id'] );
                    update_post_meta( $order->ID, '_mcv_order_transaction_id', $trade_no) ;
                }
            }
        }
        exit;
    }
    public function mcv_hupijiao_alipay_notify( \WP_REST_Request $request ){
        $appid      = MINECLOUDVOD_SETTINGS['mcv_payment']['hupijiao_alipay']['appId'];
        $appsecret  = MINECLOUDVOD_SETTINGS['mcv_payment']['hupijiao_alipay']['appSecret'];
        $my_plugin_id       = 'mcv_order';

        $data = $_POST;
        foreach ($data as $k=>$v){
            $data[$k] = stripslashes($v);
        }
        if(!isset($data['hash'])||!isset($data['trade_order_id'])){
            echo 'failed';exit;
        }

        //自定义插件ID,请与支付请求时一致
        if(isset( $data['plugins'] ) && $data['plugins']!=$my_plugin_id){
            echo 'failed';exit;
        }

        $hupijiaoService = new HupijiaoService( $appid, $appsecret );
        $hash = $hupijiaoService->generateHash( $data, $appsecret );
        
        if($data['hash']!=$hash){
            //签名验证失败
            echo 'failed';exit;
        }

        //商户订单ID
        $tradeno =$data['trade_order_id'];

        if($data['status']=='OD'){
            if( is_numeric( $tradeno ) ){
                $order = get_post( $tradeno );
                if( $order ){
                    //收款额度
                    $total_amount = sanitize_text_field( $_POST['total_fee'] );
                    $order_amount = mcv_lms_get_order_last_amount( $order->ID );
                    $order_items = get_post_meta( $order->ID, '_mcv_order_items', true );
                    if( $total_amount == $order_amount && $order_items ){
                        // 支付平台内部交易号
                        $trade_no = sanitize_text_field( $_POST['transaction_id'] );
                        //虎皮椒内部订单号
                        $open_order_id = sanitize_text_field( $_POST['open_order_id'] );
                        //付款时间
                        $gmt_payment = date( 'Y-m-d H:i:s' );
                        
                        update_post_meta( $order->ID, '_mcv_order_status', 'payed' );
                        update_post_meta( $order->ID, '_mcv_order_transaction_id', $trade_no) ;
                        update_post_meta( $order->ID, '_mcv_order_hpj_order_id', $open_order_id );
                        update_post_meta( $order->ID, '_mcv_order_gmt_payment', $gmt_payment );
                        
                        mcv_order_update_items( $order_items, $order->post_author, $order, $total_amount );
                        
                        update_post_meta( $order->ID, '_mcv_order_payment', 'hupijiao_alipay' );
                        echo 'success';
                    }
                    exit;
                }
            }
        }else{
            if( is_numeric( $tradeno ) ){
                $order = get_post( $tradeno );
                if( $order ){
                    update_post_meta( $order->ID, '_mcv_order_status', 'paying' );
                    // 支付平台内部交易号
                    $trade_no = sanitize_text_field( $_POST['transaction_id'] );
                    update_post_meta( $order->ID, '_mcv_order_transaction_id', $trade_no) ;
                }
            }
        }
        exit;
    }

    public function hpj_admin_options(){
        $prefix = 'mcv_settings';
        \MCSF::createSection($prefix, array(
            'parent'     => 'mcv_payment',
            'title'  => __('Hupijiao', 'mine-cloudvod'),
            'icon'   => 'fas fa-hand-holding-usd',
            'fields' => array(
                array(
                    'id'        => 'mcv_payment',
                    'type'      => 'fieldset',
                    'title'     => '',
                    'fields'    => array(
                        array(
                            'type'    => 'submessage',
                            'style'   => 'warning',
                            'content' => __('虎皮椒支付, 请自行考查接口的安全性和稳定性.', 'mine-cloudvod'), 
                        ),
                        array(
                            'id'        => 'hupijiao_alipay',
                            'type'      => 'fieldset',
                            'title'     => __('Hupijiao Alipay', 'mine-cloudvod'),
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
                                    'id'    => 'name',
                                    'type'  => 'text',
                                    'title' => __('Alipay Name' , 'mine-cloudvod'),
                                    'dependency' => array('status', '==', true),
                                    'default' => '支付宝',
                                ),
                                array(
                                    'id'    => 'class',
                                    'type'  => 'text',
                                    'title' => '',
                                    'dependency' => array('status', '==', 'none'),
                                    'default' => 'MineCloudvod\Payment\Hupijiao',
                                ),
                                array(
                                    'id'    => 'appId',
                                    'type'  => 'text',
                                    'title' => __('AppId' , 'mine-cloudvod'),
                                    'dependency' => array('status', '==', true),
                                ),
                                array(
                                    'id'    => 'appSecret',
                                    'type'  => 'text',
                                    'title' => 'AppSecret',
                                    'dependency' => array('status', '==', true),
                                ),
                            ),
                        ),
                        array(
                            'id'        => 'hupijiao_wechat',
                            'type'      => 'fieldset',
                            'title'     => __('Hupijiao Wechat', 'mine-cloudvod'),
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
                                    'id'    => 'name',
                                    'type'  => 'text',
                                    'title' => __('Wechat Name' , 'mine-cloudvod'),
                                    'dependency' => array('status', '==', true),
                                    'default' => '微信支付',
                                ),
                                array(
                                    'id'    => 'class',
                                    'type'  => 'text',
                                    'title' => '',
                                    'dependency' => array('status', '==', 'none'),
                                    'default' => 'MineCloudvod\Payment\Hupijiao',
                                ),
                                array(
                                    'id'    => 'appId',
                                    'type'  => 'text',
                                    'title' => __('AppId' , 'mine-cloudvod'),
                                    'dependency' => array('status', '==', true),
                                ),
                                array(
                                    'id'    => 'appSecret',
                                    'type'  => 'text',
                                    'title' => 'AppSecret',
                                    'dependency' => array('status', '==', true),
                                ),
                            ),
                        ),
                    ),
                ),
                array(
                    'id'        => 'hupijiao_gate',
                    'type'      => 'text',
                    'title'     => __('网关地址', 'mine-cloudvod'),
                    'default'   => 'https://pay.xunhunet.com/payment/do.html'
                ),
            )
        ));
    }

    public function handlePayment( $payAmount, $outTradeNo, $orderName, $post_id, $request ){
        $method = $request['pay'];
        $appid = null;
        $appsecret = null;
        $notifyUrl = '';
        $return_url = urldecode( get_the_permalink( $post_id ) );
        if( $method == 'hupijiao_alipay' ){
            $appid = MINECLOUDVOD_SETTINGS['mcv_payment']['hupijiao_alipay']['appId'];
            $appsecret = MINECLOUDVOD_SETTINGS['mcv_payment']['hupijiao_alipay']['appSecret'];
            $method = 'alipay';
            $notifyUrl = get_rest_url() . 'mine-cloudvod/v1/hupijiao_alipay_notify';
        }
        if( $method == 'hupijiao_wechat' ){
            $appid = MINECLOUDVOD_SETTINGS['mcv_payment']['hupijiao_wechat']['appId'];
            $appsecret = MINECLOUDVOD_SETTINGS['mcv_payment']['hupijiao_wechat']['appSecret'];
            $method = 'wechat';
            $notifyUrl = get_rest_url() . 'mine-cloudvod/v1/hupijiao_wechat_notify';
        }

        $data = [
            'trade_order_id'=> $outTradeNo,
            'payment' => $method,
            'total_fee' => $payAmount,
            'title' => $orderName,
            'notify_url'=> $notifyUrl,
            'return_url'=> $return_url,
        ];
        if ( \wp_is_mobile() && !mcv_is_wechat() && $method == 'wechat' ) {
            $home_url = home_url();
            $data['type']     = "WAP";
            $data['wap_url']  = $home_url;
            $data['wap_name'] = $home_url;
        }
        
        $hupijiaoService = new HupijiaoService( $appid, $appsecret );
        $response = $hupijiaoService->request( $data );
        return $response;
    }
    /**
     * 返回需要执行的js代码
     */
    public function handleScripts( $request ){
        $payment = $request['pay'];
        $name = '';
        $pic = '';
        $color = '';
        if( $payment == 'hupijiao_wechat' ){
            $name = '微信';
            $pic = 'wxzf';
            $color = '#00b54b';
        } 
        if( $payment == 'hupijiao_alipay' ){
            $name = '支付宝';
            $pic = 'alipay';
            $color = '#00a7ef';
        }
        $script = '';
        //wap
        if( \wp_is_mobile() ){
            $script = 'location.href=res.url';
        }
        else{
            $script = 'openQRBox("'.MINECLOUDVOD_URL.'/static/img/'.$pic.'.jpg",res.url_qrcode,,"'.$name.'扫码支付 "+res.amount+" 元","'.$name.'", "'.$color.'");';
        }
        return $script;
    }
}
class HupijiaoService{
    private $app_id;
    private $app_secret;
    private $api_url;

    public function __construct( $app_id, $app_secret ) {
        $this->app_id = $app_id;
        $this->app_secret = $app_secret;
        // 生产网关
        $this->api_url = MINECLOUDVOD_SETTINGS['hupijiao_gate']??'https://pay.xunhunet.com/payment/do.html';
    }
    //请求支付
    public function request( $data=[] ){

        $data=$this->formatData($data);

        $response=$this->httpRequest($this->api_url,$data);
        $response=json_decode($response,true);
        return $response;
    }

    //整合请求数据并返回
    public function formatData($data=[]){
        $data['version']    = '1.1';
        $data['appid']      = $this->app_id;
        $data['time']       = time();
        $data['nonce_str']  = md5(time());
        $data['plugins']    = 'mcv_order';
        $data['hash']=$this->generateHash($data);
        return $data;
    }

    //生成hash
    public function generateHash($data){
        if(array_key_exists('hash',$data)){
            unset($data['hash']);
        }
        ksort($data);

        $buff = "";
        foreach ($data as $k => $v) {
            if ($k != "hash" && $v !== "" && !is_array($v)) {
                $buff .= $k . "=" . $v . "&";
            }
        }
        $buff = trim($buff, "&");
        $string=$buff.$this->app_secret;

        return md5($string);
    }

    //验证返回参数
    public function checkResponse($data){
        if($data['status']!='OD'){
            exit($data['status']);
        }
        //校验签名
        $hish=$this->generateHash($data);
        if($hish!=$data['hash']){
            exit('签名校验失败');
        }
        return true;
    }

    //http请求
    public function httpRequest($url, $data = [],$headers = []){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
        curl_setopt($ch, CURLOPT_POSTFIELDS , $data);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error=curl_error($ch);
        curl_close($ch);
        if($httpStatusCode!=200){
            return new \Exception("invalid httpstatus:{$httpStatusCode} ,response:$response,detail_error:".$error,$httpStatusCode);
        }
         
        return $response;
    }
}