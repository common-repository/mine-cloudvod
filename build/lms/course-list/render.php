<?php
defined( 'ABSPATH' ) || exit;
$title          = $attributes['title']??'';
$morelink       = $attributes['morelink'] ?? '';
$moretext       = $attributes['moretext'] ?? '';
$categories     = $attributes['categories'] ?? false;
$tags           = $attributes['tags'] ?? false;
$rows           = (int)($attributes['rows'] ?? 1);
$columns        = (int)($attributes['columns'] ?? 4);
$template       = $attributes['template'] ?? false;

wp_enqueue_style( 'mine-cloudvod-course-list-editor-style' );

if( $template ){
    global $wp_query;
    global $paged;
    $args = $wp_query->query_vars;
    if( ($args['course-category']??'') == 'all' && ($args['course-tag']??'') == 'all' ){
        // wp_redirect( get_post_type_archive_link( MINECLOUDVOD_LMS['course_post_type'] ) );
    }
    $cd = MINECLOUDVOD_SETTINGS['mcv_lms_course']??[];
    ?>
    <?php if( $cd['filter']['status']??false ) : ?>
    <div class="mcv-block-selector">
        <div class="mcv-block">
            <div class="block-container">
                <div class="selector">
                    <?php 
                    if( is_array( $cd['filter']['items'] ) ): 
                        foreach( $cd['filter']['items'] as $item ):
                            include( MINECLOUDVOD_PATH . '/build/lms/course-list/filter-item-' . $item . '.php' );
                        endforeach;
                    endif; 
                    ?>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="mcv-block pb-32">
        <div class="block-container">
            <div class="result">
                <h2 class="result__title"><?php echo $title; ?></h1>
                <span class="result__desc">共找到 
                    <em class="result__count"><?php echo $wp_query->found_posts ?></em> 个结果
                </span>
            </div>
            <div class="course-list col<?php echo $columns;?> style-<?php echo $cd['mobile_style']??'1';?>">
            <?php 
            if( $wp_query->have_posts() ): 
            while($wp_query->have_posts()) :
                $wp_query->the_post();
                global $post;
                mcv_lms_loop_course( $post, $cd['mobile_style']??'1' );
            endwhile;
            endif; 
            ?>
            </div>
            <?php
            $max_num_pages = $wp_query->max_num_pages;
            if( $max_num_pages > 1 ) :
            $paged = $paged ?: 1;
            ?>
            <ul class="rc-pagination pager course-list-pagination" unselectable="unselectable">
            <?php 
            //上一页
            if( $paged <= 1 || $paged > $max_num_pages ):?>
                <li title="上一页" class="rc-pagination-prev rc-pagination-disabled" aria-disabled="true">
                    <span disabled="" class="ke-icon">
                        <svg width="1em" height="1em" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M14.364 20.435a1 1 0 0 1-1.414 0L8 15.485A4 4 0 0 1 8 9.83l4.95-4.95a1 1 0 1 1 1.414 1.414l-4.95 4.95a2 2 0 0 0 0 2.828l4.95 4.95a1 1 0 0 1 0 1.414Z" fill="currentColor"></path>
                        </svg>
                    </span>
                </li>
            <?php
            elseif( $paged <= $max_num_pages):?>
                <li title="上一页" class="rc-pagination-prev" onclick="location.href='<?php echo get_pagenum_link( $paged - 1 );?>'">
                    <span disabled="" class="ke-icon">
                        <svg width="1em" height="1em" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M14.364 20.435a1 1 0 0 1-1.414 0L8 15.485A4 4 0 0 1 8 9.83l4.95-4.95a1 1 0 1 1 1.414 1.414l-4.95 4.95a2 2 0 0 0 0 2.828l4.95 4.95a1 1 0 0 1 0 1.414Z" fill="currentColor"></path>
                        </svg>
                    </span>
                </li>
            <?php
            endif;
            //数字页码
            for( $pi = 1; $pi <= $max_num_pages; $pi++ ){
                $cbtn = '';
                if( $pi == $paged ) $cbtn = ' rc-pagination-item-active';
            ?>
                <li title="<?php echo $pi;?>" class="rc-pagination-item rc-pagination-item-1<?php echo $cbtn;?>" tabindex="0" onclick="location.href='<?php echo get_pagenum_link( $pi );?>'">
                    <a rel="nofollow"><?php echo $pi;?></a>
                </li>
            <?php
            }
            //下一页
            if( $paged >= $max_num_pages ):
            ?>
                <li title="下一页" tabindex="0" class="rc-pagination-next  rc-pagination-disabled">
                    <span class="ke-icon">
                        <svg width="1em" height="1em" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M9.636 20.435a1 1 0 0 0 1.414 0l4.95-4.95a4 4 0 0 0 0-5.656l-4.95-4.95a1 1 0 0 0-1.414 1.414l4.95 4.95a2 2 0 0 1 0 2.828l-4.95 4.95a1 1 0 0 0 0 1.414Z" fill="currentColor"></path>
                        </svg>
                    </span>
                </li>
            <?php
            elseif( $paged < $max_num_pages):
                ?>
                    <li title="下一页" tabindex="0" class="rc-pagination-next" onclick="location.href='<?php echo get_pagenum_link( $paged + 1 );?>'">
                        <span class="ke-icon">
                            <svg width="1em" height="1em" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M9.636 20.435a1 1 0 0 0 1.414 0l4.95-4.95a4 4 0 0 0 0-5.656l-4.95-4.95a1 1 0 0 0-1.414 1.414l4.95 4.95a2 2 0 0 1 0 2.828l-4.95 4.95a1 1 0 0 0 0 1.414Z" fill="currentColor"></path>
                            </svg>
                        </span>
                    </li>
                <?php
            endif;
            ?>
            </ul>
            <?php endif; ?>
        </div>
    </div>
    <?php
}
else{

    $qargs = [
        'post_type'     => MINECLOUDVOD_LMS['course_post_type'],
        'post_status'   => 'publish',
        'order'         => 'ASC',
        'orderby'       => 'menu_order',
        'showposts'     => $rows * $columns,
    ];
    if( $categories ){
        $qargs[ 'tax_query' ][] = [
            'taxonomy' => 'course-category',
            'field'    => 'term_id',
            'terms'    => $categories
        ];
    }
    if( $tags ){
        // $qargs['course-tag'] = $tags;
        $qargs[ 'tax_query' ][] = [
            'taxonomy' => 'course-tag',
            'field'    => 'term_id',
            'terms'    => $tags
        ];
    }
    $courses = get_posts( $qargs );
    ?>
    
    <div class="mcv-block">
        <div class="block-container">
            <?php if( $title ): ?>
            <div class="result">
                <h2 class="result__title"><?php echo $title; ?></h1>
                <?php if( !$template && $moretext && $morelink ): ?>
                <a href="<?php echo $morelink; ?>" class="result__desc"><?php echo $moretext; ?></a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            <div class="course-list col<?php echo $columns;?> style-<?php echo $cd['mobile_style']??'1';?>">
            <?php 
            if( $courses && count($courses) > 0 ): 
            foreach( $courses as $course ) :
                mcv_lms_loop_course( $course, $cd['mobile_style']??'1' );
            endforeach;
            endif; 
            ?>
        </div>
        </div>
    </div>
    <?php
}