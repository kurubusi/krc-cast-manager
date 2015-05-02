<?php


class Krc_Cast_Order {
	
	public function __construct () {
		$base_path = plugin_dir_path(__FILE__);
		include $base_path . 'class-walkers.php';
	}
	
	public function initialize () {
		
		add_filter( 'pre_get_posts', array( $this, 'Krc_pre_get_posts' ));    //Wordpressがクエリを実行する前に呼び出し
		add_filter( 'posts_orderby', array( $this, 'KrcrderPosts' ), 99, 2);  //post 配列を返すクエリの ORDER BY 節に適用される。 フィルター
		add_action( 'wp_loaded', array( $this, 'initKrc' ));   //すべてのプラグイン、テーマが完全に読み込まれインスタント化された時実行する。
		
	}
	
	//フィルター有効化
	public function Krc_pre_get_posts () {
		global $post;
		
		if (is_object($post) && isset($post->ID) && $post->ID < 1) { return $query; }
		if (is_admin()) { return false; }
		//if (isset($query->query['suppress_filters'])) { $query->query['suppress_filters'] = FALSE; }
		//if (isset($query->query_vars['suppress_filters'])) { $query->query_vars['suppress_filters'] = FALSE; }
		$query->query_vars['suppress_filters'] = FALSE;
		return $query;
	}
	
	
	public function KrcrderPosts ($orderBy, $query) {
		global $wpdb;
		
		if (isset($query->query_vars['post_type']) && ((is_array($query->query_vars['post_type']) && in_array("reply", $query->query_vars['post_type'])) || ($query->query_vars['post_type'] == "reply"))) {
			return $orderBy;
		}
		if (isset($query->query_vars['post_type']) && ((is_array($query->query_vars['post_type']) && in_array("topic", $query->query_vars['post_type'])) || ($query->query_vars['post_type'] == "topic"))) {
			return $orderBy;
		}
		
		if (is_admin()) {
			if (!(defined('DOING_AJAX') && isset($_REQUEST['action']) && $_REQUEST['action'] == 'query-attachments')) {
				//$orderBy = "{$wpdb->posts}.menu_order, {$wpdb->posts}.post_date DESC";
			}
		} else {
			if($query->is_search()) {
				return($orderBy);
			}
			if(trim($orderBy) == '') {
				$orderBy = "{$wpdb->posts}.menu_order ";
			} else {
				$orderBy = "{$wpdb->posts}.menu_order, " . $orderBy;
			}
		}
		return($orderBy);//$orderByを返す
		
	}
	
	
	public function Krc_admin_notices () {
	}
	
	
	public function initKrc () {
		global $custom_post_type_order, $userdata;
		if (is_admin()) {
			$base_path = plugin_dir_path(__FILE__);
			include $base_path . 'class-list.php';
			//register_widget('Krc');
			if (current_user_can('publish_posts')) {
				$custom_post_type_order = new Krc_Opt(); //管理画面にメニュー作って入れ替えのシステムページをつくる
			}
		}
	}
	
	
	
	
	
	
	
	
	
	
	
}

?>