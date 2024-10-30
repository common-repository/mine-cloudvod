<?php
namespace MineCloudvod;
if ( ! defined( 'ABSPATH' ) ) exit;

class Addons {

    public function __construct() {
        $this->init();
    }

    public function init(){
        $this->load_addons_actived();
    }

    public function mcv_addons_lists_to_show() {
        $addons = apply_filters( 'mcv_addons_lists_to_show', [
            [ // aliyun
                'id'            => 'aliyun',
                'name'          => __( 'Alibaba Cloud', 'mine-cloudvod' ),
                'description'   => __( 'Insert videos from Alibaba Cloud to Wordpress.', 'mine-cloudvod' ),
                'logo'          => MINECLOUDVOD_URL . '/static/img/addons/mcv-aliyun.png',
                'type'          => 'pro',
                'require'       => false,
                'class'         => 'null',
                'tag'           => 'plyr',
                'doc'           => 'https://www.zwtt8.com/docs/aliyunvod-setting/',
                'setting'       => admin_url('/admin.php?page=mcv-options#tab='.str_replace([' ','+'], '-', strtolower(urlencode(__('Alibaba Cloud', 'mine-cloudvod'))))).'pro',
            ],
            [ // qcloud
                'id'            => 'qcloud',
                'name'          => __( 'Tencent Cloud', 'mine-cloudvod' ),
                'description'   => __( 'Insert videos from Tencent Cloud to Wordpress.', 'mine-cloudvod' ),
                'logo'          => MINECLOUDVOD_URL . '/static/img/addons/mcv-qcloud.png',
                'type'          => 'pro',
                'require'       => false,
                'class'         => 'null',
                'tag'           => 'plyr',
                'doc'           => 'https://www.zwtt8.com/docs/tcvod-setting/',
                'setting'       => admin_url('/admin.php?page=mcv-options#tab='.str_replace(' ', '-', strtolower(urlencode(__('Tencent Cloud', 'mine-cloudvod'))))).'pro',
            ],
            [ // doge
                'id'            => 'doge',
                'name'          => __( 'Dogecloud', 'mine-cloudvod' ),
                'description'   => __( 'Insert videos from Dogecloud to Wordpress.', 'mine-cloudvod' ),
                'logo'          => MINECLOUDVOD_URL . '/static/img/addons/mcv-doge.png',
                'type'          => 'pro',
                'require'       => false,
                'class'         => 'null',
                'tag'           => 'plyr',
                'doc'           => 'https://www.zwtt8.com/docs/dogecloud/',
                'setting'       => admin_url('/admin.php?page=mcv-options#tab='.str_replace(' ', '-', strtolower(urlencode(__('Dogecloud', 'mine-cloudvod'))))).'pro',
            ],
            [ // qiniukodo
                'id'            => 'qiniukodo',
                'name'          => __( 'Qiniu Kodo', 'mine-cloudvod' ),
                'description'   => __( 'Insert videos from Qiniu Kodo to Wordpress.', 'mine-cloudvod' ),
                'logo'          => MINECLOUDVOD_URL . '/static/img/addons/mcv-qiniu.png',
                'type'          => 'pro',
                'require'       => false,
                'class'         => '\MineCloudvod\Qiniu\Kodo',
                'tag'           => 'plyr',
                'doc'           => 'https://www.zwtt8.com/docs/%e4%b8%83%e7%89%9b%e4%ba%91%e5%ad%98%e5%82%a8/',
                'setting'       => admin_url('/admin.php?page=mcv-options#tab='.str_replace(' ', '-', strtolower(urlencode(__('Qiniu', 'mine-cloudvod'))))).'pro',
            ],
            [ // bunnynet
                'id'            => 'bunnynet',
                'name'          => __( 'BunnyNet', 'mine-cloudvod' ),
                'description'   => __( 'Insert videos from Bunny.net to Wordpress.', 'mine-cloudvod' ),
                'logo'          => MINECLOUDVOD_URL . '/static/img/addons/mcv-bunnynet.png',
                'type'          => 'pro',
                'require'       => false,
                'class'         => '\MineCloudvod\BunnyNet\Init',
                'tag'           => 'plyr',
                'doc'           => 'https://www.zwtt8.com/docs/bunnynet/',
                'setting'       => admin_url('/admin.php?page=mcv-options#tab='.str_replace(' ', '-', strtolower(urlencode(__('BunnyNet', 'mine-cloudvod'))))).'pro',
            ],
            [ // attachment
                'id'            => 'attachment',
                'name'          => __( 'Lesson Attachments', 'mine-cloudvod' ),
                'description'   => __( 'Add unlimited attachments to MCV lessons.', 'mine-cloudvod' ),
                'logo'          => MINECLOUDVOD_URL . '/static/img/addons/mcv-attachment.png',
                'type'          => 'pro',
                'require'       => false,
                'class'         => '\MineCloudvod\LMS\Addons\Attachment',
                'tag'           => 'lms',
                'doc'           => 'https://www.zwtt8.com/docs/%e9%9a%8f%e8%af%be%e8%b5%84%e6%96%99/',
                'setting'       => '',
            ],
            [ // nextlesson
                'id'            => 'nextlesson',
                'name'          => __( 'Auto Next Lesson', 'mine-cloudvod' ),
                'description'   => __( 'After current lesson is completed, automatically redirect to the next lesson.', 'mine-cloudvod' ),
                'logo'          => MINECLOUDVOD_URL . '/static/img/addons/mcv-nextlesson.png',
                'type'          => 'pro',
                'require'       => false,
                'class'         => '\MineCloudvod\LMS\Addons\NextLesson',
                'tag'           => 'lms',
                'doc'           => 'https://www.zwtt8.com/docs/%e8%87%aa%e5%8a%a8%e4%b8%8b%e4%b8%80%e8%af%be/',
                'setting'       => '',
            ],
            [ // coursereport
                'id'            => 'coursereport',
                'name'          => __( 'MCV Report of Courses', 'mine-cloudvod' ),
                'description'   => __( 'Statistical analysis of courses.', 'mine-cloudvod' ),
                'logo'          => MINECLOUDVOD_URL . '/static/img/addons/mcv-report.png',
                'type'          => 'pro',
                'require'       => false,
                'class'         => '\MineCloudvod\LMS\Addons\Report',
                'tag'           => 'lms',
                'doc'           => 'https://www.zwtt8.com/docs/%e8%af%be%e7%a8%8b%e6%8a%a5%e5%91%8a/',
                'setting'       => '',
            ],
            [ // partialpurchase
                'id'            => 'partialpurchase',
                'name'          => __( 'MCV Partial Purchase', 'mine-cloudvod' ),
                'description'   => __( 'Chapter purchase or Lesson purchase, without buying the entire course.', 'mine-cloudvod' ),
                'logo'          => MINECLOUDVOD_URL . '/static/img/addons/mcv-partialpurchase.png',
                'type'          => 'buy',
                'price'         => '219',
                'oprice'        => '699',
                'require'       => false,
                'class'         => '\MineCloudvod\LMS\Addons\Purchase',
                'tag'           => 'lms',
                'doc'           => 'https://www.zwtt8.com/docs/%e9%83%a8%e5%88%86%e8%b4%ad%e4%b9%b0/',
                'setting'       => '',
            ],
            [ // progress
                'id'            => 'progress',
                'name'          => __( 'Course Progress', 'mine-cloudvod' ),
                'description'   => __( 'Record the learning progress of the course/section/lesson.', 'mine-cloudvod' ),
                'logo'          => MINECLOUDVOD_URL . '/static/img/addons/mcv-progress.png',
                'type'          => 'buy',
                'price'         => '99',
                'oprice'        => '299',
                'require'       => false,
                'class'         => '\MineCloudvod\LMS\Addons\Progress',
                'tag'           => 'lms',
                'doc'           => 'https://www.zwtt8.com/docs/%e8%af%be%e7%a8%8b%e8%bf%9b%e5%ba%a6/',
                'setting'       => '',
            ],
            [ // coursesort
                'id'            => 'coursesort',
                'name'          => __( 'Courses Sorting', 'mine-cloudvod' ),
                'description'   => __( 'Sort the courses by sequence number.', 'mine-cloudvod' ),
                'logo'          => MINECLOUDVOD_URL . '/static/img/addons/mcv-sort.png',
                'type'          => 'pro',
                'require'       => false,
                'class'         => '\MineCloudvod\LMS\Addons\CourseSort',
                'tag'           => 'lms',
                'doc'           => '',
                'setting'       => '',
            ],
            [ // coursereview
                'id'            => 'coursereview',
                'name'          => __( 'User Review', 'mine-cloudvod' ),
                'description'   => __( 'User can submit reviews on courses.', 'mine-cloudvod' ),
                'logo'          => MINECLOUDVOD_URL . '/static/img/addons/mcv-review.png',
                'type'          => 'pro',
                'require'       => false,
                'class'         => '\MineCloudvod\LMS\Addons\Review',
                'tag'           => 'lms',
                'doc'           => '',
                'setting'       => '',
            ],
            [ // promotion
                'id'            => 'promotion',
                'name'          => __( 'Promotion Rebate', 'mine-cloudvod' ),
                'description'   => '推广新用户，获取高额佣金',
                'logo'          => MINECLOUDVOD_URL . '/static/img/addons/mcv-promotion.svg',
                'type'          => 'buy',
                'price'         => '199',
                'oprice'        => '699',
                'require'       => false,
                'class'         => '\MineCloudvod\LMS\Addons\Promotion',
                'tag'           => 'lms',
                'doc'           => 'https://www.zwtt8.com/docs/promotion-rebate/',
                'setting'       => admin_url('/admin.php?page=mcv-options#tab='.str_replace([' ','+'], '-', strtolower(urlencode(__('User Center', 'mine-cloudvod')))).'/'.str_replace([' ','+'], '-', strtolower(urlencode(__('Promotion Rebate', 'mine-cloudvod'))))),
            ],
            [ // previewpurchase
                'id'            => 'previewpurchase',
                'name'          => __( 'Purchase Video', 'mine-cloudvod' ),
                'description'   => __( 'Purchase for the video directly or after previewing.', 'mine-cloudvod' ),
                'logo'          => MINECLOUDVOD_URL . '/static/img/addons/mcv-preview.png',
                'type'          => 'buy',
                'price'         => '149',
                'oprice'        => '599',
                'require'       => false,
                'class'         => '\MineCloudvod\LMS\Addons\PreviewPurchase',
                'tag'           => 'plyr',
                'doc'           => 'https://www.zwtt8.com/docs/%e8%a7%86%e9%a2%91%e4%bb%98%e8%b4%b9/',
                'setting'       => '',
            ],
            [ // package
                'id'            => 'package',
                'name'          => __( 'Course Package', 'mine-cloudvod' ),
                'description'   => __( 'Some courses are sold together into a package.', 'mine-cloudvod' ),
                'logo'          => MINECLOUDVOD_URL . '/static/img/addons/mcv-package.png',
                'type'          => 'buy',
                'price'         => '199',
                'oprice'        => '699',
                'require'       => false,
                'class'         => '\MineCloudvod\LMS\Addons\Package',
                'tag'           => 'lms',
                'doc'           => 'https://www.zwtt8.com/docs/course-package/',
                'setting'       => '',
            ],
            [ // duplicate
                'id'            => 'duplicate',
                'name'          => __( 'Duplicate Course', 'mine-cloudvod' ),
                'description'   => __( 'Duplicate a course and includes its lessons.', 'mine-cloudvod' ),
                'logo'          => MINECLOUDVOD_URL . '/static/img/addons/mcv-duplicate.png',
                'type'          => 'buy',
                'price'         => '99',
                'oprice'        => '299',
                'require'       => false,
                'class'         => '\MineCloudvod\LMS\Addons\Duplicate',
                'tag'           => 'lms',
                'doc'           => 'https://www.zwtt8.com/docs/duplicate-course/',
                'setting'       => '',
            ],
            [ // exchange
                'id'            => 'exchange',
                'name'          => __( 'Exchange Code', 'mine-cloudvod' ),
                'description'   => __( 'Use a code to exchange courses.', 'mine-cloudvod' ),
                'logo'          => MINECLOUDVOD_URL . '/static/img/addons/mcv-exchange.png',
                'type'          => 'buy',
                'price'         => '299',
                'oprice'        => '899',
                'require'       => false,
                'class'         => '\MineCloudvod\LMS\Addons\Exchange',
                'tag'           => 'lms',
                'doc'           => 'https://www.zwtt8.com/docs/exchange-course/',
                'setting'       => '',
            ],
            [ // couponcode
                'id'            => 'couponcode',
                'name'          => __( 'Coupon Code', 'mine-cloudvod' ),
                'description'   => __( 'Use coupon code to enhance course purchase rates.', 'mine-cloudvod' ),
                'logo'          => MINECLOUDVOD_URL . '/static/img/addons/mcv-coupon.png',
                'type'          => 'buy',
                'price'         => '299',
                'oprice'        => '999',
                'require'       => false,
                'class'         => '\MineCloudvod\LMS\Addons\CouponCode',
                'tag'           => 'lms',
                'doc'           => 'https://www.zwtt8.com/docs/coupon-code/',
                'setting'       => '',
            ],
            [ // coupon
                'id'            => 'coupon',
                'name'          => __( 'Coupon', 'mine-cloudvod' ),
                'description'   => __( 'Use coupon to enhance course purchase rates.', 'mine-cloudvod' ),
                'logo'          => MINECLOUDVOD_URL . '/static/img/addons/mcv-coupon.png',
                'type'          => 'buy',
                'price'         => '499',
                'oprice'        => '999',
                'require'       => false,
                'class'         => '\MineCloudvod\LMS\Addons\Coupon',
                'tag'           => 'lms',
                'doc'           => 'https://www.zwtt8.com/docs/coupon/',
                'setting'       => '',
            ],
            [ // b2
                'id'            => 'i_b2',
                'name'          => __('B2', 'mine-cloudvod'),
                'description'   => __( 'Make Shortcode working in B2\'s Video Url.', 'mine-cloudvod' ),
                'logo'          => MINECLOUDVOD_URL . '/static/img/addons/mcv-integration.png',
                'type'          => 'free',
                'require'       => false,
                'class'         => '\MineCloudvod\Integrations\B_2\B_2',
                'tag'           => 'integration',
                'doc'           => 'https://www.zwtt8.com/docs/b2/',
                'setting'       => admin_url('/admin.php?page=mcv-options#tab=b2'),
            ],
            [ // ceomax
                'id'            => 'i_ceomax',
                'name'          => __('Ceomax', 'mine-cloudvod'),
                'description'   => __( 'Make Shortcode working in Ceomax\'s Video Url.', 'mine-cloudvod' ),
                'logo'          => MINECLOUDVOD_URL . '/static/img/addons/mcv-integration.png',
                'type'          => 'free',
                'require'       => false,
                'class'         => '\MineCloudvod\Integrations\Ceomax\Ceomax',
                'tag'           => 'integration',
                'doc'           => 'https://www.zwtt8.com/docs/ceomax/',
                'setting'       => '',
            ],
            [ // elementor
                'id'            => 'i_elementor',
                'name'          => __('Elementor', 'mine-cloudvod'),
                'description'   => __( 'Working with Elementor.', 'mine-cloudvod' ),
                'logo'          => MINECLOUDVOD_URL . '/static/img/addons/mcv-integration.png',
                'type'          => 'free',
                'require'       => false,
                'class'         => '\MineCloudvod\Integrations\Elementor\Elementor',
                'tag'           => 'integration',
                'doc'           => 'https://www.zwtt8.com/docs/elementor/',
                'setting'       => '',
            ],
            [ // ripro
                'id'            => 'i_ripro',
                'name'          => __('Ripro', 'mine-cloudvod'),
                'description'   => '集成Ripro v5主题',
                'logo'          => MINECLOUDVOD_URL . '/static/img/addons/mcv-integration.png',
                'type'          => 'free',
                'require'       => false,
                'class'         => '\MineCloudvod\Integrations\Ri\RiProV2',
                'tag'           => 'integration',
                'doc'           => 'https://www.zwtt8.com/docs/ripro-v2/',
                'setting'       => admin_url('/admin.php?page=mcv-options#tab=ripro'),
            ],
            [ // zibll
                'id'            => 'i_zibll',
                'name'          => __('Zibll', 'mine-cloudvod'),
                'description'   => __( 'Working with Zibll theme.', 'mine-cloudvod' ),
                'logo'          => MINECLOUDVOD_URL . '/static/img/addons/mcv-integration.png',
                'type'          => 'free',
                'require'       => false,
                'class'         => '\MineCloudvod\Integrations\Zibll\Zibll',
                'tag'           => 'integration',
                'doc'           => 'https://www.zwtt8.com/docs/zibll/',
                'setting'       => admin_url('/admin.php?page=mcv-options#tab=zibll'),
            ],
            [
                'id'            => 'i_tutor',
                'name'          => __('Tutor', 'mine-cloudvod'),
                'description'   => __( 'Make Shortcode working in Tutor\'s Video Url.', 'mine-cloudvod' ),
                'logo'          => MINECLOUDVOD_URL . '/static/img/addons/mcv-integration.png',
                'type'          => 'free',
                'require'       => false,
                'class'         => '\MineCloudvod\Integrations\Tutor\Tutor',
                'tag'           => 'integration',
                'doc'           => 'https://www.zwtt8.com/docs/tutorlms/',
                'setting'       => '',
            ],
            [
                'id'            => 'i_masterstudy',
                'name'          => __('MasterStudy', 'mine-cloudvod'),
                'description'   => __( 'Working with MasterStudy LMS.', 'mine-cloudvod' ),
                'logo'          => MINECLOUDVOD_URL . '/static/img/addons/mcv-integration.png',
                'type'          => 'free',
                'require'       => false,
                'class'         => '\MineCloudvod\Integrations\MasterStudy\MasterStudy',
                'tag'           => 'integration',
                'doc'           => 'https://www.zwtt8.com/docs/masterstudy-lms/',
                'setting'       => '',
            ],
            [ // Docs
                'id'            => 'docs',
                'name'          => __('Documentation', 'mine-cloudvod'),
                'description'   => __( 'Documentation', 'mine-cloudvod' ),
                'logo'          => MINECLOUDVOD_URL . '/static/img/addons/mcv-docs.png',
                'type'          => 'buy',
                'price'         => '999',
                'oprice'        => '1999',
                'require'       => false,
                'class'         => '\MineCloudvod\LMS\Addons\Docs',
                'tag'           => 'lms',
                'doc'           => 'https://www.zwtt8.com/docs/mcv-docs/',
                'setting'       => '',
            ],
            [ // epay
                'id'            => 'epay',
                'name'          => __('Easy Pay', 'mine-cloudvod'),
                'description'   => __( 'Easy Pay', 'mine-cloudvod' ),
                'logo'          => MINECLOUDVOD_URL . '/static/img/addons/mcv-epay.png',
                'type'          => 'pro',
                'require'       => false,
                'class'         => '\MineCloudvod\LMS\Addons\Epay',
                'tag'           => 'lms',
                'doc'           => 'https://www.zwtt8.com/docs/mcv-epay/',
                'setting'       => admin_url('/admin.php?page=mcv-options#tab='.str_replace([' ','+'], '-', strtolower(urlencode(__('Payment Gateway', 'mine-cloudvod')))).'/'.str_replace([' ','+'], '-', strtolower(urlencode(__('Easy Pay', 'mine-cloudvod'))))),
            ],
            [ // haipay
                'id'            => 'haipay',
                'name'          => __( 'HaiPay', 'mine-cloudvod' ),
                'description'   => __( 'Malaysia payment interface.', 'mine-cloudvod' ),
                'logo'          => MINECLOUDVOD_URL . '/static/img/addons/mcv-haipay.png',
                'type'          => 'buy',
                'price'         => '299',
                'oprice'        => '699',
                'require'       => false,
                'class'         => '\MineCloudvod\LMS\Addons\Haipay',
                'tag'           => 'lms',
                'doc'           => 'https://www.zwtt8.com/docs/haipay/',
                'setting'       => admin_url('/admin.php?page=mcv-options#tab='.str_replace([' ','+'], '-', strtolower(urlencode(__('Payment Gateway', 'mine-cloudvod')))).'/'.str_replace([' ','+'], '-', strtolower(urlencode(__('HaiPay', 'mine-cloudvod'))))),
            ],
            [ // alphapay
                'id'            => 'alphapay',
                'name'          => __( 'AlphaPay', 'mine-cloudvod' ),
                'description'   => __( 'Canadian payment interface.', 'mine-cloudvod' ),
                'logo'          => MINECLOUDVOD_URL . '/static/img/addons/mcv-alphapay.png',
                'type'          => 'buy',
                'price'         => '299',
                'oprice'        => '699',
                'require'       => false,
                'class'         => '\MineCloudvod\LMS\Addons\AlphaPay',
                'tag'           => 'lms',
                'doc'           => 'https://www.zwtt8.com/docs/alphapay/',
                'setting'       => admin_url('/admin.php?page=mcv-options#tab='.str_replace([' ','+'], '-', strtolower(urlencode(__('Payment Gateway', 'mine-cloudvod')))).'/'.str_replace([' ','+'], '-', strtolower(urlencode(__('AlphaPay', 'mine-cloudvod'))))),
            ],
            [ // wpcom
                'id'            => 'wpcom',
                'name'          => 'Wpcom会员对接',
                'description'   => 'Wpcom会员可以免费学习课程。',
                'logo'          => MINECLOUDVOD_URL . '/static/img/addons/mcv-wpcom.png',
                'type'          => 'buy',
                'price'         => '199',
                'oprice'        => '699',
                'require'       => '<a href="plugin-install.php?tab=plugin-information&amp;plugin=wpcom-member&amp;TB_iframe=true&amp;width=772&amp;height=844" class="thickbox open-plugin-details-modal" aria-label="关于 WPCOM Member 用户中心 的更多信息" data-title="WPCOM Member 用户中心">WPCOM Member</a>',
                'class'         => '\MineCloudvod\LMS\Addons\Wpcom',
                'tag'           => 'lms',
                'doc'           => 'https://www.zwtt8.com/docs/wpcom/',
                'setting'       => '',
            ],
        ] );

        return $addons;
    }

    public function load_addons_actived(){
        $active_addons = $this->get_actived_addons();
        global $ActiveAddons;
        if( $active_addons ){
            foreach( $active_addons as $aa ){
                if($aa['class'] !== 'null'){
                    $ActiveAddons[$aa['id']] = new $aa['class']();
                }
            }
        }
    }
    /**
     * 获取启用的扩展
     */
    public function get_actived_addons(){
        $active_addons = get_option( '_mcv_active_addons' );
        $current = [];
        if( $active_addons ){
            foreach( $active_addons as $aa ){
                $addons = $this->get_addons_by_id( $aa );
                if( $addons && (class_exists( $addons['class'] ) || $addons['class'] === 'null') ){
                    $current[] = $addons;
                }
            }
        }

        return apply_filters( 'get_actived_addons', $current );
    }

    /**
     * 扩展启用状态
     */
    public function is_addons_actived( $id ){
        $addons = $this->get_actived_addons();
        foreach( $addons as $a ){
            if( $a['id'] == $id ) return true;
        }
        return false;
    }

    public function get_addons_by_id( $id ){
        $addons = $this->mcv_addons_lists_to_show();
        foreach( $addons as $a ){
            if( $a['id'] == $id ) return $a;
        }
        return false;
    }

    /**
     * 启用扩展
     */
    public function active_addons( $plugin ){
        $addons = $this->mcv_addons_lists_to_show();
        foreach( $addons as $a ){
            if( $plugin == $a['id'] ){
                if( class_exists( $a['class'] ) || $a['class'] === 'null' ){
                    $active_addons = get_option( '_mcv_active_addons' );
                    $active_addons = $active_addons ? $active_addons : [];
                    if( !in_array( $plugin, $active_addons ) ){
                        $active_addons[] = $plugin;
                        update_option( '_mcv_active_addons', $active_addons );
                        if( method_exists( $a['class'], 'preInit' ) ){
                            call_user_func( [ $a['class'], 'preInit' ] );
                        }
                        return $a['type'] != 'free' && $a['class'] != 'null';
                    }
                }
            }
        }
        return false;
    }
    /**
     * 禁用扩展
     */
    public function deactive_addons( $plugin ){
        $addons = $this->mcv_addons_lists_to_show();
        $active_addons = get_option( '_mcv_active_addons' );
        if( $active_addons ){
            $current = [];
            foreach( $active_addons as $a ){
                if( $a != $plugin )
                    $current[] = $a;
            }
            update_option( '_mcv_active_addons', $current );
            return true;
        }
        return false;
    }
}
