<?php

class Krc_Opt {
	
	private $current_post_type;
	
	public function __construct () {
		$this->current_post_type = null;
		
		//管理画面各ページの最初、ページがレンダリングされる前に実行する。
		//add_action( 'admin_init', array(&$this, 'registerFiles'), 11 );
		add_action( 'admin_init', array(&$this, 'checkPost'), 10 );
		//管理画面メニューの基本構造が配置された後に実行する。
		add_action( 'admin_menu', array(&$this, 'addMenu') );
		//update-custom-type-orderがキーノajaxが発生したら
		add_action( 'wp_ajax_update-custom-type-order', array(&$this, 'saveAjaxOrder') );
	}
	
	//各スクリプト・CSS読み込み
	public function registerFiles () {
	}
	
	
	public function checkPost () {
		if ( isset($_GET['page']) && substr($_GET['page'], 0, 17) == 'order-post-types-' ) {
			$this->current_post_type = get_post_type_object(str_replace( 'order-post-types-', '', $_GET['page'] ));
			if ( $this->current_post_type == null) {
				wp_die('Invalid post type');
			}
		}
	}
	
	
	public function saveAjaxOrder () {
		global $wpdb;//DB情報
		
		parse_str($_POST['order'], $data);// 文字列を処理し、変数に代入する
		//print_r($data);
		if (is_array($data)) {
			foreach ($data as $key => $values ) {
				if ( $key == 'item' ) {
					foreach ( $values as $position => $id ) {
						$data = array('menu_order' => $position, 'post_parent' => 0);
						$data = apply_filters('post-types-order_save-ajax-order', $data, $key, $id);
						$wpdb->update( $wpdb->posts, $data, array('ID' => $id) );
					}
				}
			}
		} else {
			foreach( $values as $position => $id ) {
				$data = array('menu_order' => $position, 'post_parent' => str_replace('item_', '', $key));
 				$data = apply_filters('post-types-order_save-ajax-order', $data, $key, $id);
				$wpdb->update( $wpdb->posts, $data, array('ID' => $id) );
			}
		}
		
	}
	
	
	public function addMenu () {
		global $userdata;
		$post_types = get_post_types();
		
		foreach( $post_types as $post_type_name ) {
			if ($post_type_name == 'page') { continue; }
			if ($post_type_name == 'reply' || $post_type_name == 'topic') { continue; }
			if ($post_type_name == 'cast') {
				add_submenu_page('edit.php?post_type=cast', __('表示順管理', 'krc'), __('表示順管理', 'krc'), 'publish_posts', 'order-post-types-'.$post_type_name, array(&$this, 'SortPage') );
			}
		}
	}
	
	
	public function SortPage () {
		
		$data = array();
		$tmp = new Krc_Template_Loader();
		$tmp->render("cast_order", array("list_pege" => $this->listPages('hide_empty=0&title_li=&post_type='.$this->current_post_type->name)));
	}
	
	
	public function listPages ($args = '') {
		
		$defaults = array(
			'depth' => 0, 'show_date' => '',
			'date_format' => get_option('date_format'),
			'child_of' => 0, 'exclude' => '',
			'title_li' => __('Pages'), 'echo' => 1,
			'authors' => '', 'sort_column' => 'menu_order',
			'link_before' => '', 'link_after' => '', 'walker' => ''
		);
		$r = wp_parse_args( $args, $defaults );//引数の配列とデフォルト値の配列を結合する
		extract( $r, EXTR_SKIP ); //配列からシンボルテーブルに変数をインポートする
		
		$output = '';
		
		$r['exclude'] = preg_replace('/[^0-9,]/', '', $r['exclude']);
		$exclude_array = ( $r['exclude'] ) ? explode(',', $r['exclude']) : array();
		$r['exclude'] = implode( ',', apply_filters('wp_list_pages_excludes', $exclude_array) );
		
		// Query pages.
		$r['hierarchical'] = 0;
		$args = array(
			'sort_column'   =>  'menu_order',
			'post_type'     =>  $post_type,
			'posts_per_page' => -1,
			'orderby'        => array(
				'menu_order'    => 'ASC',
				'post_date'     =>  'DESC'
			)
		);
		$the_query = new WP_Query($args);
		$pages = $the_query->posts;
		
		if ( !empty($pages) ) {
			if ( $r['title_li'] ) {
				$output .= '<li class="pagenav intersect">' . $r['title_li'] . '<ul>';
			}
			$output .= $this->walkTree($pages, $r['depth'], $r);
			
			if ( $r['title_li'] ) {
				$output .= '</ul></li>';
			}
		}
		
		$output = apply_filters('wp_list_pages', $output, $r);
		
		if ( $r['echo'] ) {
			return $output;
		} else {
			return $output;
		}
	}
	
	
	public function walkTree ($pages, $depth, $r) {
		if ( empty($r['walker']) ) {
			$walker = new Krc_Post_Types_Order_Walker;
		} else {
			$walker = $r['walker'];
		}
		$args = array($pages, $depth, $r);
		return call_user_func_array(array(&$walker, 'walk'), $args);
	}
	
	
	
	
}

?>