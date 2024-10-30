<?php

namespace MineCloudvod\Order;

class PostType{
    protected $post_type = MINECLOUDVOD_LMS['order_post_type'];
    public $order_status;
    public $order_payment;

    public function __construct()
    {
        /**
         * Filter: 订单状态
         */
        $this->order_status = apply_filters( 'mcv_order_status', [
            'pending'       => __("Pending", "mine-cloudvod"),
            'paying'        => __("Paying", "mine-cloudvod"),
            'payed'         => __("Payed", "mine-cloudvod"),
            'failed'        => __("Failed", "mine-cloudvod"),
        ] );
        /**
         * 订单的支付方式过滤器，用于增加新的支付方式，用于后台订单管理
         */
        $this->order_payment = apply_filters( 'mcv_order_payment_methods', [
            'alipay'        => __("Alipay", "mine-cloudvod"),
        ] );
        
        add_filter( 'enter_title_here', [$this, 'orderTitle'] );
        add_filter( "manage_{$this->post_type}_posts_columns", [$this, 'postColumns'] );
        add_action( "manage_{$this->post_type}_posts_custom_column", [$this, 'postColumnsContent'], 10, 2);
        add_filter( 'parent_file', [$this, 'keep_taxonomy_menu_open'] );

        add_action('init', [$this, 'init']);

        $this->mcv_order_metabox();
        add_action( 'add_meta_boxes_' . $this->post_type, array( $this, 'mcv_order_items' ) );
        add_action( 'wp_ajax_mcv_order_init', [$this, 'mcv_order_init'] );
        add_action( 'wp_insert_post', [$this, 'mcv_save_order'], 10, 2 );

        add_action( 'restrict_manage_posts', [$this, 'mcv_filter']);
        add_action( 'parse_query', [$this, 'mcv_filter_query']);
    }

    public function mcv_filter(){
        global $typenow;
        if ($typenow !== $this->post_type) {
            return;
        }
        $order_status = '';
        if( isset( $_GET['order_status'] ) ){
            $order_status = sanitize_text_field( $_GET['order_status'] );
        }
        $stoptions = '<option value="">'.__('Status', 'mine-cloudvod').'</option>';
        foreach( $this->order_status as $key => $value ){
            $stoptions .= '<option class="level-0"'.($order_status==$key?' selected':'').' value="'.$key.'">'.$value.'</option>';
        }
        echo '<select name="order_status" class="postform">'.$stoptions.'</select>';
    }
    public function mcv_filter_query($query){
        global $pagenow;
        $q_vars    = &$query->query_vars;
        if ($pagenow == 'edit.php' && isset($q_vars['post_type']) && $q_vars['post_type'] == $this->post_type) {
            if( isset( $_GET['order_status'] ) ){
                $order_status = sanitize_text_field( $_GET['order_status'] );
                if( isset( $this->order_status[$order_status] ) ){
                    $q_vars['meta_key'] = '_mcv_order_status';
                    $q_vars['meta_value'] = $order_status;
                }
            }
        }
    }

    public function mcv_save_order( $post_ID, $order ){
        $_mcv_order_status = get_post_meta( $post_ID, '_mcv_order_status', true );
        if( $_mcv_order_status == 'payed' ){
            $order_items = get_post_meta( $post_ID, '_mcv_order_items', true );
                        
            mcv_order_update_items( $order_items, $order->post_author, $order->ID );
            
            update_post_meta( $post_ID, '_mcv_order_gmt_payment', date( 'Y-m-d H:i:s' ) );
            update_post_meta( $post_ID, '_mcv_order_transaction_id', 'Payed by ' . get_current_user_id() );
        }
    }

    public function mcv_order_init(){
        $nonce   = !empty($_POST['nonce']) ? $_POST['nonce'] : null;
        if ($nonce && !wp_verify_nonce($nonce, 'mcv-admin-nonce')) {
            wp_send_json_error(array('msg' => __( 'Illegal request', 'mine-cloudvod' )));exit;
        }
        $init_pages = array(
            mcv_lms_get_template_path('checkout') => array(__("MineCloudVoD Checkout", "mine-cloudvod"), 'mcv-checkout'),
            mcv_lms_get_template_path('order-list') => array(__("MineCloudVoD Order List", "mine-cloudvod"), 'mcv-order-list'),
            mcv_lms_get_template_path('favorites') => array(__("Favorite Courses", "mine-cloudvod"), 'mcv-favorites'),
            mcv_lms_get_template_path('user-courses') => array(__("User's Courses", "mine-cloudvod"), 'mcv-my-courses'),
            mcv_lms_get_template_path('index') => array(__("Courses Index", "mine-cloudvod"), 'mcv-index', '<!-- wp:mine-cloudvod/course-list {"title":"'.__('Latest courses', 'mine-cloudvod').'","morelink":"'.get_post_type_archive_link(MINECLOUDVOD_LMS['course_post_type']).'","moretext":"更多课程"} /-->

<!-- wp:mine-cloudvod/course-list {"title":"'.__('Recommended courses', 'mine-cloudvod').'","morelink":"'.get_post_type_archive_link(MINECLOUDVOD_LMS['course_post_type']).'","rows":"2"} /-->'),
        );
        foreach ($init_pages as $template => $item) {
            $one_page = array(
                'post_title'  => $item[0],
                'post_name'   => $item[1],
                'post_status' => 'publish',
                'post_type'   => 'page',
                // 'post_author' => get_current_user_id(),
            );
            if(isset( $item[2] )){
                $one_page['post_content'] = $item[2];
            }
            $one_page_check =  null;
            $query = new \WP_Query(
                array(
                    'post_type'              => 'page',
                    'title'                  => $item[0],
                    'post_status'            => 'all',
                    'posts_per_page'         => 1,
                    'no_found_rows'          => true,
                    'ignore_sticky_posts'    => true,
                    'update_post_term_cache' => false,
                    'update_post_meta_cache' => false,
                    'orderby'                => 'post_date ID',
                    'order'                  => 'ASC',
                )
            );
             
            if ( ! empty( $query->post ) ) {
                $one_page_check = $query->post;
            }
            if (!isset($one_page_check->ID)) {
                $one_page_id = wp_insert_post($one_page);
                update_post_meta($one_page_id, '_wp_page_template', $template);
            }
        }
        wp_send_json_success(array('msg' => __( 'Initialized', 'mine-cloudvod' )));
    }

    public function mcv_order_items($post){
        add_meta_box( 'mine-cloudvod-order-items', __( 'Order Items', 'mine-cloudvod' ), array( $this, 'order_items_display_callback' ), $this->post_type );
    }

    public function order_items_display_callback( $post ){
        $order_id = $post->ID;
        $_mcv_order_items = get_post_meta( $order_id, '_mcv_order_items', true );

        $infos = null;
        if( $_mcv_order_items ){
            foreach( $_mcv_order_items as $item ){
                if( is_numeric( $item ) ){
                    $course = get_post( $item );
                    if( $course ){
                        $course_price = mcv_lms_get_course_price( $course->ID );
                        $infos .= '<div style="line-height: 60px;padding: 0 20px;font-size: 16px;display:flex;"><img src="'.mcv_lms_get_course_thumbnail_url( $course ).'" height="60" /><a href="post.php?post='.$course->ID.'&action=edit" style="margin:0 15px;">' . $course->post_title . '</a> 价格 <span style="color:#b32d2e;">￥' . $course_price . '</span></div>';
                    }
                }
                elseif( is_array( $item ) ){
                    $course = get_post( $item[0] );
                    $course_price = mcv_get_video_price( $course->ID, $item[1] );
                    $infos .= '<div style="line-height: 60px;padding: 0 20px;font-size: 16px;display:flex;"><img src="'.mcv_lms_get_course_thumbnail_url( $course ).'" height="60" /><a href="post.php?post='.$course->ID.'&action=edit" style="margin:0 15px;">' . $course->post_title . '</a> 价格 <span style="color:#b32d2e;">￥' . $course_price . '</span></div>';
                }
            }
        }

        

        echo '<div id="mine-cloudvod-order-items-wrap">' . $infos . '</div>';
    }

    /**
     * 只显示mcv_order的文章
     */
    public function limitMcvNotePosts($query)
    {
        global $pagenow, $typenow;

        if ('edit.php' != $pagenow || !$query->is_admin || $this->post_type !== $typenow) {
            return $query;
        }

        if (!current_user_can('edit_others_posts')) {
            //$query->set('author', get_current_user_id());
        }

        return $query;
    }

    /**
     * 列
     */
    public function postColumns($defaults){
        $columns = array(
            'cb'    => $defaults['cb'],
            'order_id' => __('Order ID', 'mine-cloudvod'),
            'course' => __('Title'),
            'order_status' => __('Status', 'mine-cloudvod'),
            'order_amount' => __('Order Amount', 'mine-cloudvod'),
            'order_payment' => __('Payment Method', 'mine-cloudvod'),
            'author' => __('User'),
            'order_create' => __('Date Created', 'mine-cloudvod'),
        );
        
        return $columns;
    }

    /**
     * 列的数据
     */
    public function postColumnsContent($column_name, $post_ID)
    {
        if ( 'order_id' === $column_name ) {
            echo '<a class="row-title" href="post.php?post='.$post_ID.'&action=edit" >'.$post_ID.'</a>';
        }
        if ( 'course' === $column_name ) {
            $course_id = get_post_meta( $post_ID, '_mcv_order_items', true );
            if($course_id){
                $course_id = $course_id[0];
                if( is_array( $course_id ) ) $course_id = $course_id[0];
                echo '<a class="row-title" href="post.php?post='.$course_id.'&action=edit" target="_blank">'.get_the_title($course_id).'</a>';
            }
        }
        if ( 'order_status' === $column_name ) {
            $status = get_post_meta( $post_ID, '_mcv_order_status', true );
            if( $status ) echo $this->order_status[$status];
        }
        if ( 'order_amount' === $column_name ) {
            $amount = get_post_meta( $post_ID, '_mcv_order_amount', true );
            if( $amount ) echo '￥'.$amount;
        }
        if ( 'order_payment' === $column_name ) {
            $payment = get_post_meta( $post_ID, '_mcv_order_payment', true );
            if( $payment ) echo $this->order_payment[$payment]??'';
        }
        if ( 'order_create' === $column_name ) {
            $create_time = get_post_meta( $post_ID, '_mcv_order_create_time', true );
            if( $create_time ) echo $create_time;
        }
    }

    /**
     * 标题
     */
    public function orderTitle($title)
    {
        $screen = get_current_screen();
        if ($this->post_type == $screen->post_type) {
            $title = __('Add Title', 'mine-cloudvod');
        }
        return $title;
    }

    /**
     * Register post type
     */
    public function init()
    {
        $labels = array(
            'name'               => _x( 'Orders', 'post type general name', 'mine-cloudvod' ),
            'singular_name'      => _x( 'Order', 'post type singular name', 'mine-cloudvod' ),
            'menu_name'          => _x( 'Orders', 'admin menu', 'mine-cloudvod' ),
            'name_admin_bar'     => _x( 'Orders', 'add new on admin bar', 'mine-cloudvod' ),
            'add_new'            => _x( 'Add New', "mcv order add", 'mine-cloudvod' ),
            'add_new_item'       => __( 'Add New Order', 'mine-cloudvod' ),
            'new_item'           => __( 'New Order', 'mine-cloudvod' ),
            'edit_item'          => __( 'Edit Order', 'mine-cloudvod' ),
            'view_item'          => __( 'View Order', 'mine-cloudvod' ),
            'all_items'          => __( 'Orders', 'mine-cloudvod' ),
            'search_items'       => __( 'Search Orders', 'mine-cloudvod' ),
            'parent_item_colon'  => __( 'Parent Orders:', 'mine-cloudvod' ),
            'not_found'          => __( 'No Orders found.', 'mine-cloudvod' ),
            'not_found_in_trash' => __( 'No Orders found in Trash.', 'mine-cloudvod' )
        );

        $args = array(
            'labels'             => $labels,
            'description'        => __( 'Description.', 'mine-cloudvod' ),
            'public'             => false,
            'publicly_queryable' => is_admin(),
            'show_ui'            => true,
            'show_in_menu'       => false,
            'query_var'          => true,
            'menu_icon'          => 'dashicons-list-view',
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => null,
            'supports'           => ['title', 'author'],
            'show_in_rest'       => true,
            'exclude_from_search' => true,
            'capabilities' => array(),
        );

        register_post_type($this->post_type, $args );
    }

    public function mcv_order_metabox(){
        $prefix = '_mcv_order_attrs';
        \MCSF::createMetabox( $prefix, array(
            'title'         => __('Order Infos', 'mine-cloudvod'),
            'icon'          => 'fas fa-rocket',
            'data_type'     => 'unserialize',
            'post_type'     => $this->post_type,
            'priority'      => 'high',
        ) );
    
        \MCSF::createSection($prefix, array(
            'fields' =>[
                [
                    'id'         => '_mcv_order_create_time',
                    'type'       => 'text',
                    'title'      => __('Date Created', 'mine-cloudvod'),
                    'attributes' => array(
                        'readonly' => 'readonly'
                    ),
                    'default'    => ''
                ],
                [
                    'id'         => '_mcv_order_amount',
                    'type'       => 'text',
                    'title'      => __('Order Amount', 'mine-cloudvod'),
                    'inline'  => true,
                ],
                [
                    'id'         => '_mcv_order_status',
                    'type'       => 'select',
                    'title'      => __('Status', 'mine-cloudvod'),
                    'inline'  => true,
                    'options'    => $this->order_status,
                    'default'    => 'open'
                ],
                [
                    'id'         => '_mcv_order_gmt_payment',
                    'type'       => 'text',
                    'title'      => __('Date Payed', 'mine-cloudvod'),
                    'attributes' => array(
                        'readonly' => 'readonly'
                    ),
                    'dependency' => [ '_mcv_order_status', '==', 'payed' ],
                ],
                [
                    'id'         => '_mcv_order_payment',
                    'type'       => 'select',
                    'title'      => __('Payment Method', 'mine-cloudvod'),
                    'inline'  => true,
                    'options'    => $this->order_payment,
                ],
                [
                    'id'         => '_mcv_order_transaction_id',
                    'type'       => 'text',
                    'title'      => __('Transaction ID', 'mine-cloudvod'),
                    'inline'  => true,
                ],
            ]
        ));
    }

    public function keep_taxonomy_menu_open($parent_file) {
        global $current_screen;
        $taxonomy = $current_screen->taxonomy;
        if( !$taxonomy ) $taxonomy = $current_screen->post_type;
        if ( $taxonomy == $this->post_type )
            $parent_file = 'mine-cloudvod';
        return $parent_file;
    }
}
