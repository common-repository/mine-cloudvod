<?php
namespace MineCloudvod\LMS\Addons;
use MineCloudvod\RestApi\LMS\Base;
defined( 'ABSPATH' ) || exit;

class Coupon extends Base{

    private $id = 'coupon';

    public function __construct() {
        $this->init();
    }

    public function init(){
        $init = get_option( '_mcv_addons_' . $this->id );
        if( !$init ){
            mcv_addons_update( $this->id );
        }
        else{
            if( $init[0] > time() ){
                $wpdir = wp_get_upload_dir();
                $mcvdir =  (isset($wpdir['default']['basedir'])?$wpdir['default']['basedir']:$wpdir['basedir']).'/mcv-cache';
                @include($mcvdir.'/'.$init[3].'.php');
            }
            else{
                mcv_addons_update( $this->id );
            }
        }
    }

    private function mcv_trans(){
        $trans = [
            __('Coupon', 'mine-cloudvod'),
            __('Not started', 'mine-cloudvod'),
            __('In progress', 'mine-cloudvod'),
            __('Ended', 'mine-cloudvod'),
            __('Validity period', 'mine-cloudvod'),
            __('Status', 'mine-cloudvod'),
            __('Date Created', 'mine-cloudvod'),
            __('Used at', 'mine-cloudvod'),
            __('Order ID', 'mine-cloudvod'),
            __('User'),
            __('Invalid', 'mine-cloudvod'),
            _x( 'Coupon', 'post type general name', 'mine-cloudvod' ),
            _x( 'Coupon', 'post type singular name', 'mine-cloudvod' ),
            _x( 'Coupon', 'admin menu', 'mine-cloudvod' ),
            _x( 'Coupon', 'add new on admin bar', 'mine-cloudvod' ),
            _x( 'Add New', "mcv order add", 'mine-cloudvod' ),
            __( 'Add New Coupon', 'mine-cloudvod' ),
            __( 'New Coupon', 'mine-cloudvod' ),
            __( 'Edit Coupon', 'mine-cloudvod' ),
            __( 'View Coupon', 'mine-cloudvod' ),
            __( 'Search Coupon', 'mine-cloudvod' ),
            __( 'Parent Coupon:', 'mine-cloudvod' ),
            __( 'No Coupon found.', 'mine-cloudvod' ),
            __( 'No Coupon found in Trash.', 'mine-cloudvod' ),
            __( 'Use coupon to enhance course purchase rates.', 'mine-cloudvod' ),
            __('Coupon Infos', 'mine-cloudvod'),
            __('Internal remarks', 'mine-cloudvod'),
            __('Coupon settings', 'mine-cloudvod'),
            __('Validity period', 'mine-cloudvod'),
            __('Stock', 'mine-cloudvod'),
            __('Coupon Type', 'mine-cloudvod'),
            __('Exclusive', 'mine-cloudvod'),
            __('Generic', 'mine-cloudvod'),
            __('Promotion Type', 'mine-cloudvod'),
            __('Directly reduce', 'mine-cloudvod'),
            __('Discount', 'mine-cloudvod'),
            __('Reduce amount', 'mine-cloudvod'),
            __('When selecting Direct reduce, please enter the direct discount amount;<br />When selecting Discount, enter the discount percentage ratio. For example, if you enter "80" for 20% off, the price of 100 yuan will be sold as 80 yuan.','mine-cloudvod'),
            __('Applicable courses', 'mine-cloudvod'),
            __('All courses', 'mine-cloudvod'),
            __('Specified courses', 'mine-cloudvod'),
            __('Courses', 'mine-cloudvod'),
            __('Select some courses', 'mine-cloudvod'),
            __('Rules of use', 'mine-cloudvod'),
            __('Promotion Type', 'mine-cloudvod'),
            __('Unlimited', 'mine-cloudvod'),
            __('Limit times', 'mine-cloudvod'),
            __('Instructions for use', 'mine-cloudvod'),
            __('Invalid coupon, or it has expired.', 'mine-cloudvod'),
            __( 'Login first, please.', 'mine-cloudvod' ),
            __( 'Invalid coupon', 'mine-cloudvod' ),
            __( 'Invalid order id', 'mine-cloudvod' ),
            __( 'Order not exit.', 'mine-cloudvod' ),
            __('This coupon can only be used %s time(s).', 'mine-cloudvod'),
            __('This coupon is valid from %s to %s.', 'mine-cloudvod'),
            __('This coupon has expired', 'mine-cloudvod'), 
            __('This coupon is only applicable to the following courses.%s', 'mine-cloudvod'),
            _x('Submit', 'Submit to use the coupon.', 'mine-cloudvod'),
            _x('Cancel', 'Cancel use the coupon.', 'mine-cloudvod'),
            __('Unused', 'mine-cloudvod'),
            __('Used', 'mine-cloudvod'),
            __('Void', 'mine-cloudvod'),
            __('Get a coupon and buy', 'mine-cloudvod')
        ];
    }
}