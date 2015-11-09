<?php

class Krc_Model_Ranking {
	
	private $post_type;
	private $template_parser;
	private $error_message;
	private $cast_post_type;
	private $rankings;
	
	public function __construct ($template_parser) {
		$this->post_type = 'krc_ranking';
		$this->cast_post_type = 'krc_cast';
		$this->error_message = "";
		$this->template_parser = $template_parser;
		
	}
	
	
	public function initialize () {
		//フック登録
		add_action( 'init', array( $this, 'create_ranking_post_type' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_ranking_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'save_ranking_meta_data' ) );
		add_action( 'save_post', array( $this, 'save_ranking_id_data' ) );
		add_filter( 'post_updated_messages', array( $this, 'generate_ranking_messages' ) );
		add_filter( 'manage_edit-' . $this->post_type . '_columns', array( $this, 'manage_posts_columns' ) );
		add_action( 'manage_' . $this->post_type . '_posts_custom_column', array( $this, 'add_cpt_column' ) );
		
		
	}
	
	
	//キャストカスタム投稿作成
	public function create_ranking_post_type () {
		$labels = array(
			'name'                  => __( 'ランキング管理', 'krc' ),
			'singular_name'         => __( 'ランキング管理', 'krc' ),
			'add_new'               => __( 'ランキング新規作成', 'krc' ),
			'add_new_item'          => __( 'ランキング新規作成', 'krc' ),
			'edit_item'             => __( 'ランキング内容編集', 'krc' ),
			'new_item'              => __( 'ランキング新規作成', 'krc' ),
			'all_items'             => __( 'ランキング管理', 'krc' ),
			'view_item'             => __( '表示確認', 'krc' ),
			'search_items'          => __( '検索', 'krc' ),
			'not_found'             => __( '見つかりません', 'krc' ),
			'not_found_in_trash'    => __( 'ゴミ箱は空です', 'krc' ),
			'parent_item_colon'     => '',
			'menu_name'             => __( 'ランキング管理', 'krc' ),
		);
		$args = array(
			'labels'                => $labels,
			'hierarchical'          => true,
			'description'           => 'ランキング管理',
			'supports'              => array('title', 'editor'),
			'public'                => true,
			'show_ui'               => true,
			'show_in_menu'          => 'edit.php?post_type=krc_cast',
			'show_in_nav_menus'     => true,
			'show_in_admin_bar'     => false,
			'publicly_queryable'    => true,
			'exclude_from_search'   => false,
			'has_archive'           => true,
			'query_var'             => true,
			'can_export'            => true,
			'rewrite'               => true,
			'capability_type'       => 'post',
		);
		register_post_type( $this->post_type, $args );
		
	}
	
	
	//メタボックス登録呼び出し
	public function add_ranking_meta_boxes () {
		add_meta_box( 'krc-ranking-meta', '基本情報', array( $this, 'display_ranking_meta' ), $this->post_type, 'normal', 'high' );
		add_meta_box( 'krc-ranking-in', 'ランキングBOX', array( $this, 'display_ranking_in_boxes' ), $this->post_type, 'side', 'default' );
		add_meta_box( 'krc-ranking-out', 'キャストBOX', array( $this, 'display_ranking_out_boxes' ), $this->post_type, 'normal','high');
	}
	
	
	public function display_ranking_meta() {
		global $post, $post_id;
		//テンプレートにデータを渡す
		$data = array();
		$data['ranking_meta_nonce'] = wp_create_nonce('ranking_meta_nonce');
		$data['krc_ranking_title'] = esc_attr(get_post_meta( $post_id, "_krc_ranking_title", true ));
		echo $this->template_parser->render( 'ranking_meta.html', $data );
	}
	
	
	public function display_ranking_in_boxes() {
		global $post, $post_id;
		
		$data = array();
		$data['ranking_in_meta_nonce'] = wp_create_nonce('ranking_in_meta_nonce');
		$this->rankings = json_decode(get_post_meta($post_id, "_krc_cast_rankings", true));
		$casts = array();
		if (is_array($this->rankings)) {
			foreach ($this->rankings as $key => $id) {
				$photo = json_decode(get_post_meta($id, '_krc_cast_screens', true));
				$casts[$id] = array(
					'krc_name' => (string) esc_html(get_post_meta($id, '_krc_name', true)),
					'krc_cast_screens' => (string) esc_url($photo[0])
				);
			}
		}
		$data['krc_cast_in_arr'] = $casts;
		echo $this->template_parser->render( 'ranking_in.html', $data );
	}
	
	
	
	public function display_ranking_out_boxes() {
		$data = array();
		$data['ranking_out_meta_nonce'] = wp_create_nonce('ranking_out_meta_nonce');
		
		$args = array(
			'sort_column'   =>  'menu_order',
			'post_type'     =>  $this->cast_post_type,
			'posts_per_page' => -1,
			'post__not_in' => $this->rankings,
			'orderby'        => array(
				'menu_order'    => 'ASC',
				'post_date'     =>  'DESC'
			)
		);
		$the_query = new WP_Query($args);
		$casts = array();
		while ( $the_query->have_posts() ) : $the_query->the_post();
			$photo = json_decode(post_custom('_krc_cast_screens'));
			$casts[get_the_ID()] = array(
				'krc_name' => (string) esc_html(post_custom('_krc_name')),
				'krc_cast_screens' => (string) esc_url($photo[0])
			);
		endwhile;
		wp_reset_postdata();
		$data['krc_cast_out_arr'] = $casts;
		
		echo $this->template_parser->render( 'ranking_out.html', $data );
	}
	
	
	//ランキング基本情報保存
	public function save_ranking_meta_data ($post_id) {
		global $post;
		//nonce値比較　権限確認
		if (!$post_id || !isset($_POST['ranking_meta_nonce'])) { return; }
		if (!wp_verify_nonce($_POST['ranking_meta_nonce'], 'ranking_meta_nonce')) { return $post->ID; }
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) { return $post->ID; }
		if ($this->post_type == $_POST['post_type'] && current_user_can('publish_posts', $post->ID)) {
			$krc_ranking = (isset( $_POST['krc_ranking_title'] ) ? (string) esc_attr( trim($_POST['krc_ranking_title']) ) : '');
			//メタボックス情報保存
			update_post_meta( $post->ID, "_krc_ranking_title", $krc_ranking );
		} else {
			return $post->ID;
		}
		
	}
	
	public function save_ranking_id_data ($post_id) {
		global $post;
		if (!$post_id || !isset($_POST['ranking_in_meta_nonce'])) { return; }
		if (!wp_verify_nonce($_POST['ranking_in_meta_nonce'], 'ranking_in_meta_nonce')) { return $post->ID; }
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) { return $post->ID; }
		if ($this->post_type == $_POST['post_type'] && current_user_can('publish_posts', $post->ID)) {
			
			$krc_cast_rankings = isset($_POST['h_krc_cast_rankings']) ? $_POST['h_krc_cast_rankings'] : "";
			$krc_cast_rankings = json_encode($krc_cast_rankings);
			
			update_post_meta($post->ID, "_krc_cast_rankings", $krc_cast_rankings);
			
		} else {
			return $post->ID;
		}
		
	}
	
	
	//オリジナルエラーメッセージに変更
	public function generate_ranking_messages( $messages ) {
		global $post, $post_ID;
		
		$this->error_message = get_transient( "cast_error_message_$post->ID" );
		$message_no = isset($_GET['message']) ? (int) $_GET['message'] : '0';
		//echo $this->error_message;
		//exit;
		delete_transient( "cast_error_message_$post->ID" );
		
		if ( !empty( $this->error_message ) ) {
			$messages[$this->post_type] = array( "$message_no" => $this->error_message );
		} else {
			$messages[$this->post_type] = array(
				0 => '', // Unused. Messages start at index 1.
				1 => sprintf(__('ランキングを更新しました。<a href="%s">確認</a>', 'krc' ), esc_url(get_permalink($post_ID))),
				2 => __('カスタムフィールド更新', 'krc' ),
				3 => __('カスタムフィールド削除', 'krc' ),
				4 => __('更新しました', 'krc' ),
				5 => isset($_GET['revision']) ? sprintf(__('リビジョンは %s', 'krc' ), wp_post_revision_title((int) $_GET['revision'], false)) : false,
				6 => sprintf(__('ランキングを作成しました。<a href="%s">確認</a>', 'krc' ), esc_url(get_permalink($post_ID))),
				7 => __('セーブ', 'wpwa' ),
				8 => sprintf(__('送信 <a target="_blank" href="%s">確認</a>', 'krc' ), esc_url(add_query_arg('preview', 'true', get_permalink($post_ID)))),
				9 => sprintf(__('スケジュールはr: <strong>%1$s</strong>. <a target="_blank" href="%2$s">確認</a>', 'krc' ),
				date_i18n(__('M j, Y @ G:i'), strtotime($post->post_date)), esc_url(get_permalink($post_ID))),
				10 => sprintf(__('下書きを更新しました。 <a target="_blank" href="%s">確認</a>', 'krc' ), esc_url(add_query_arg('preview', 'true', get_permalink($post_ID)))),
			);
		}
		return $messages;
	}
	
	
	//キャスト一覧画面表示項目変更
	//綺麗にした方がいいかも
	public function manage_posts_columns ($columns) {
		$columns = array(
			'cb' => '<input type="checkbox" />',
			'title' => __( 'TITLE', 'krc' ),
			//'_krc_title' => __( 'ランキングタイトル', 'krc' ),
			'_krc_cast_rankings' => __( '対象者', 'krc' ),
			'date' => __( '日時', 'krc' ),
		);
		return $columns;
	}
	
	//キャスト一覧表示内容作成
	//綺麗にした方がいいかも
	public function add_cpt_column ($column_name) {
		global $post;
		
		if ($column_name == "_krc_cast_rankings") {
			$ids = json_decode(get_post_meta($post->ID, "_krc_cast_rankings", true));
			if (is_array($ids)) {
				foreach ($ids as $id) {
					$screens = json_decode(get_post_meta($id, "_krc_cast_screens", true));
					echo '<dl class="cast_ranking_photo"><dt>' . esc_attr(get_post_meta( $id, "_krc_name", true )) . '</dt>';
					echo '<dd><a href="post.php?post=' . $id . '&action=edit" style="display:block;"><img src="' . esc_url($screens[0]) . '" class="" /></a></dd></dl>';
				}
			}
		} else if (substr($column_name, 0, 1) != '_') {
			//echo  esc_attr(get_the_terms( $post->ID, $column_name, true ));
			$terms_arr = get_the_terms( $post->ID, $column_name, true );
			if ( !empty($terms_arr) ) { 
				foreach ( $terms_arr as $ts ) {
					echo '<a href="edit.php?post_type=' . $this->post_type . '&' . $column_name . '=' . $ts->slug . '">' . esc_html(sanitize_term_field('name', $ts->name, $ts->term_id, $column_name, 'display')) . '</a><br />';
				}
			}
		} else {
			echo  esc_html(get_post_meta( $post->ID, $column_name, true ));
		}
		
		
		
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}







?>