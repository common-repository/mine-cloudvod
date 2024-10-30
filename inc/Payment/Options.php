<?php
namespace MineCloudvod\Payment;

if ( ! defined( 'ABSPATH' ) ) exit;

class Options{
    public $prefix = 'mcv_settings';
    public function __construct() {
        add_action( 'mcv_add_admin_options_before_purchase', array( $this, 'payment_admin_options' ) );

        $this->init_payments();
    }

    public function payment_admin_options(){
        \MCSF::createSection( $this->prefix, array(
            'id'    => 'mcv_payment',
            'title' => __('Payment Gateway', 'mine-cloudvod'),
            'icon'  => 'fas fa-dollar-sign',
        ));
    }

    public function init_payments(){
        $payments = [
            'alipay' => 'MineCloudvod\Payment\Alipay',
            'wechat' => 'MineCloudvod\Payment\Wechat',
            'hupijiao' => 'MineCloudvod\Payment\Hupijiao',
        ];
        /**
         * 支付class过滤器，注册支付的class，在class中处理支付的逻辑
         */
        $payments = apply_filters( 'mcv_order_payment_classes', $payments );
        foreach( $payments as $payment ){
            if( is_string( $payment ) && class_exists( $payment ) ){
                $payment = new $payment();
            }
        }
    }
}