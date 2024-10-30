<?php
/**
 * 分页
 */
defined( 'ABSPATH' ) || exit;

$max_num_pages = $wp_query->max_num_pages;

if( $max_num_pages <= 1 ) return;

$paged = $paged ?: 1;

?><nav>
	<ul class="pagination justify-content-center">
		<?php 
		//上一页
		if( $paged <= 1 || $paged > $max_num_pages ){
			echo '<li class="page-item disabled"><a class="page-link" href="javascript:;" tabindex="-1" aria-disabled="true">' . __('Prev', 'mine-cloudvod') . '</a></li>';
		}
		elseif( $paged <= $max_num_pages){
			echo '<li class="page-item"><a class="page-link" href="' . get_pagenum_link( $paged - 1 ) . '">' . __('Prev', 'mine-cloudvod') . '</a></li>';
		}
		//数字页码
		for( $pi = 1; $pi <= $max_num_pages; $pi++ ){
			$cbtn = '';
			if( $pi == $paged ) $cbtn = ' active';
			echo '<li class="page-item' . $cbtn . '"><a class="page-link" href="' . get_pagenum_link($pi) . '">' . $pi . '</a></li>';
		}
		//下一页
		if( $paged >= $max_num_pages ){
			echo '<li class="page-item disabled"><a class="page-link" href="javascript:;" tabindex="-1" aria-disabled="true">' . __('Next', 'mine-cloudvod') . '</a></li>';
		}
		elseif( $paged < $max_num_pages){
			echo '<li class="page-item"><a class="page-link" href="' . get_pagenum_link( $paged + 1 ) . '">' . __('Next', 'mine-cloudvod') . '</a></li>';
		}
		?>
	</ul>
</nav>