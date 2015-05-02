<?php

class Krc_Model_Casts {
	
	private $post_type;
	private $template_parser;
	private $type_taxonomy;
	private $grade_taxonomy;
	private $new_taxonomy;
	private $age_taxonomy;
	private $tall_taxonomy;
	private $cups_taxonomy;
	private $error_message;
	
	public function __construct ($template_parser) {
		$this->post_type = 'krc_cast';
		$this->type_taxonomy = "krc_type";
		$this->grade_taxonomy = "krc_grade";
		$this->new_taxonomy = "krc_new";
		$this->age_taxonomy = "krc_age";
		$this->tall_taxonomy = "krc_tall";
		$this->cups_taxonomy = "krc_cups";
		
		$this->error_message = "";
		$this->template_parser = $template_parser;
	}
	
	
	public function initialize () {
		//フック登録
		add_action( 'init', array( $this, 'create_cast_post_type' ) );
		add_action( 'init', array( $this, 'create_cast_custom_taxonomies' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_cast_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'save_cast_meta_data' ) );
		add_filter( 'post_updated_messages', array( $this, 'generate_cast_messages' ) );
		add_filter( 'manage_edit-' . $this->post_type . '_columns', array( $this, 'manage_posts_columns' ) );
		add_action( 'manage_' . $this->post_type . '_posts_custom_column', array( $this, 'add_cpt_column' ) );
		add_filter( 'wp_terms_checklist_args', array( $this, 'krc_switch' ) );
		
		
	}
	
	
	//キャストカスタム投稿作成
	public function create_cast_post_type () {
		$labels = array(
			'name'                  => __( 'キャスト管理', 'krc' ),
			'singular_name'         => __( 'キャスト管理', 'krc' ),
			'add_new'               => __( 'キャスト新規登録', 'krc' ),
			'add_new_item'          => __( 'キャスト新規登録', 'krc' ),
			'edit_item'             => __( 'キャスト登録内容編集', 'krc' ),
			'new_item'              => __( 'キャスト新規登録', 'krc' ),
			'all_items'             => __( 'キャスト管理', 'krc' ),
			'view_item'             => __( '表示確認', 'krc' ),
			'search_items'          => __( '検索', 'krc' ),
			'not_found'             => __( '見つかりません', 'krc' ),
			'not_found_in_trash'    => __( 'ゴミ箱は空です', 'krc' ),
			'parent_item_colon'     => '',
			'menu_name'             => __( 'キャストマネージャー', 'krc' ),
		);
		$args = array(
			'labels'                => $labels,
			'hierarchical'          => true,
			'description'           => '在席キャスト管理',
			'supports'              => array('title', 'editor', 'custom-fields'),
			'public'                => true,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'show_in_nav_menus'     => true,
			'show_in_admin_bar'     => false,
			'publicly_queryable'    => true,
			'exclude_from_search'   => false,
			'menu_icon'             => 'dashicons-groups',
			'has_archive'           => true,
			'query_var'             => true,
			'can_export'            => true,
			'rewrite'               => true,
			'capability_type'       => 'post',
		);
		register_post_type( $this->post_type, $args );
	}
	
	
	
	
	//キャストカスタム投稿に当てはめるタクソノミー作成
	public function create_cast_custom_taxonomies () {
		register_taxonomy(
			$this->type_taxonomy,
			$this->post_type,
			array(
				'labels' => array(
					'name'              => __( 'タイプ管理', 'krc' ),
					'singular_name'     => __( 'タイプ管理', 'krc' ),
					'search_items'      => __( 'キャストタイプ検索', 'krc' ),
					'all_items'         => __( 'キャストタイプ一覧', 'krc' ),
					'parent_item'       => __( '親', 'krc' ),
					'parent_item_colon' => __( '親:', 'krc' ),
					'edit_item'         => __( 'キャストタイプ編集', 'krc' ),
					'update_item'       => __( 'キャストタイプ更新', 'krc' ),
					'add_new_item'      => __( 'キャストタイプ追加', 'krc' ),
					'new_item_name'     => __( 'キャストタイプ名', 'krc' ),
					'menu_name'         => __( 'タイプ管理', 'krc' ),
				),
				'hierarchical' => true
			)
		);
		register_taxonomy(
			$this->grade_taxonomy,
			$this->post_type,
			array(
				'labels' => array(
					'name'              => __( 'グレード管理', 'krc' ),
					'singular_name'     => __( 'グレード管理', 'krc' ),
					'search_items'      => __( 'キャストグレード検索', 'krc' ),
					'all_items'         => __( 'キャストグレード一覧', 'krc' ),
					'parent_item'       => __( '親', 'krc' ),
					'parent_item_colon' => __( '親:', 'krc' ),
					'edit_item'         => __( 'キャストグレード編集', 'krc' ),
					'update_item'       => __( 'キャストグレード更新', 'krc' ),
					'add_new_item'      => __( 'キャストグレード追加', 'krc' ),
					'new_item_name'     => __( 'キャストグレード名', 'krc' ),
					'menu_name'         => __( 'グレード管理', 'wpwa' ),
				),
				'hierarchical' => true
			)
		);
		register_taxonomy(
			$this->new_taxonomy,
			$this->post_type,
			array(
				'labels' => array(
					'name'              => __( 'キャスト新人区分', 'krc' ),
					'singular_name'     => __( 'キャスト新人区分', 'krc' ),
					'search_items'      => __( 'キャスト新人区分検索', 'krc' ),
					'all_items'         => __( 'キャスト新人区分一覧', 'krc' ),
					'parent_item'       => __( '親', 'krc' ),
					'parent_item_colon' => __( '親:', 'krc' ),
					'edit_item'         => __( 'キャスト新人区分編集', 'krc' ),
					'update_item'       => __( 'キャスト新人区分更新', 'krc' ),
					'add_new_item'      => __( 'キャスト新人区分追加', 'krc' ),
					'new_item_name'     => __( 'キャスト新人区分名', 'krc' ),
					'menu_name'         => __( '新人区分', 'wpwa' ),
				),
				'hierarchical' => true,
				'capabilities' => array(
					'manage_terms'      => 'manage_krc_type',
					'edit_terms'        => 'edit_krc_type',
					'delete_terms'      => 'delete_krc_type',
				),
			)
		);
		wp_set_object_terms(
			$this->post_type,
			array(
				'new' => __( '新人', 'krc' ),
			),
			$this->new_taxonomy
		);
		register_taxonomy(
			$this->age_taxonomy,
			$this->post_type,
			array(
				'labels' => array(
					'name'              => __( 'キャスト年代', 'krc' ),
					'singular_name'     => __( 'キャスト年代', 'krc' ),
					'search_items'      => __( 'キャスト年代検索', 'krc' ),
					'all_items'         => __( 'キャスト年代一覧', 'krc' ),
					'parent_item'       => __( '親', 'krc' ),
					'parent_item_colon' => __( '親:', 'krc' ),
					'edit_item'         => __( 'キャスト年代編集', 'krc' ),
					'update_item'       => __( 'キャスト年代更新', 'krc' ),
					'add_new_item'      => __( 'キャスト年代追加', 'krc' ),
					'new_item_name'     => __( 'キャスト年代名', 'krc' ),
					'menu_name'         => __( '年代', 'wpwa' ),
				),
				'hierarchical' => true,
				'capabilities' => array(
					'manage_terms'      => 'manage_krc_type',
					'edit_terms'        => 'edit_krc_type',
					'delete_terms'      => 'delete_krc_type',
				),
			)
		);
		wp_set_object_terms(
			$this->post_type,
			array(
				'over18' => __( '18歳～20歳', 'krc' ),
				'over21' => __( '21歳～23歳', 'krc' ),
				'over24' => __( '24歳～26歳', 'krc' ),
				'over27' => __( '27歳～29歳', 'krc' ),
				'over30' => __( '30歳～32歳', 'krc' ),
				'over33' => __( '33歳～35歳', 'krc' ),
				'over36' => __( '36歳～38歳', 'krc' ),
				'over39' => __( '39歳～41歳', 'krc' ),
				'over42' => __( '42歳以上', 'krc' ),
				
			),
			$this->age_taxonomy
		);
		register_taxonomy(
			$this->tall_taxonomy,
			$this->post_type,
			array(
				'labels' => array(
					'name'              => __( 'キャスト身長', 'krc' ),
					'singular_name'     => __( 'キャスト身長', 'krc' ),
					'search_items'      => __( 'キャスト身長検索', 'krc' ),
					'all_items'         => __( 'キャスト身長一覧', 'krc' ),
					'parent_item'       => __( '親', 'krc' ),
					'parent_item_colon' => __( '親:', 'krc' ),
					'edit_item'         => __( 'キャスト身長編集', 'krc' ),
					'update_item'       => __( 'キャスト身長更新', 'krc' ),
					'add_new_item'      => __( 'キャスト身長追加', 'krc' ),
					'new_item_name'     => __( 'キャスト身長名', 'krc' ),
					'menu_name'         => __( '身長', 'wpwa' ),
				),
				'hierarchical' => true,
				'capabilities' => array(
					'manage_terms'      => 'manage_krc_type',
					'edit_terms'        => 'edit_krc_type',
					'delete_terms'      => 'delete_krc_type',
				),
			)
		);
		wp_set_object_terms(
			$this->post_type,
			array(
				'under150' => __( '150cm以下', 'krc' ),
				'over151' => __( '151cm～155cm', 'krc' ),
				'over156' => __( '156cm～160cm', 'krc' ),
				'over161' => __( '161cm～165cm', 'krc' ),
				'over166' => __( '166cm～170cm', 'krc' ),
				'over171' => __( '171cm～175cm', 'krc' ),
				'over176' => __( '176cm以上', 'krc' ),
			),
			$this->tall_taxonomy
		);
		register_taxonomy(
			$this->cups_taxonomy,
			$this->post_type,
			array(
				'labels' => array(
					'name'              => __( 'キャストカップ', 'krc' ),
					'singular_name'     => __( 'キャストカップ', 'krc' ),
					'search_items'      => __( 'キャストカップ検索', 'krc' ),
					'all_items'         => __( 'キャストカップ一覧', 'krc' ),
					'parent_item'       => __( '親', 'krc' ),
					'parent_item_colon' => __( '親:', 'krc' ),
					'edit_item'         => __( 'キャストカップ編集', 'krc' ),
					'update_item'       => __( 'キャストカップ更新', 'krc' ),
					'add_new_item'      => __( 'キャストカップ追加', 'krc' ),
					'new_item_name'     => __( 'キャストカップ名', 'krc' ),
					'menu_name'         => __( 'カップ', 'wpwa' ),
				),
				'hierarchical' => true,
				'capabilities' => array(
					'manage_terms'      => 'manage_krc_type',
					'edit_terms'        => 'edit_krc_type',
					'delete_terms'      => 'delete_krc_type',
				),
			)
		);
		wp_set_object_terms(
			$this->post_type,
			array(
				'a' => __( 'A', 'krc' ),
				'b' => __( 'B', 'krc' ),
				'c' => __( 'C', 'krc' ),
				'd' => __( 'D', 'krc' ),
				'e' => __( 'E', 'krc' ),
				'f' => __( 'F', 'krc' ),
				'g' => __( 'G', 'krc' ),
				'h' => __( 'H', 'krc' ),
				'i' => __( 'I', 'krc' ),
				'j' => __( 'J以上', 'krc' ),
			),
			$this->cups_taxonomy
		);
		
		
		
	}
	
	//メタボックス登録呼び出し
	public function add_cast_meta_boxes () {
		add_meta_box( 'krc-cast-meta', 'キャスト基本情報', array( $this, 'display_cast_meta_boxes' ), $this->post_type, 'normal','high');
		add_meta_box( 'krc-photo-meta', 'キャスト画像', array( $this, 'display_photo_meta_boxes' ), $this->post_type, 'normal', 'default' );
	}
	
	//基本情報メタボックス作成
	public function display_cast_meta_boxes() {
		global $post;
		//テンプレートにデータを渡す
		$data = array();
		$data['cast_meta_nonce'] = wp_create_nonce('cast_meta_nonce');
		$data['krc_name'] = esc_attr(get_post_meta( $post->ID, "_krc_name", true ));
		$data['krc_age'] = esc_attr(get_post_meta( $post->ID, "_krc_age", true ));
		$data['krc_tall'] = esc_attr(get_post_meta( $post->ID, "_krc_tall", true ));
		$data['krc_bust'] = esc_attr(get_post_meta( $post->ID, "_krc_bust", true ));
		$data['krc_waist'] = esc_attr(get_post_meta( $post->ID, "_krc_waist", true ));
		$data['krc_hips'] = esc_attr(get_post_meta( $post->ID, "_krc_hips", true ));
		$data['krc_cups'] = esc_attr(get_post_meta( $post->ID, "_krc_cups", true ));
		echo $this->template_parser->render( 'cast_meta.html', $data );
	}
	
	//キャスト画像メタボックス作成
	public function display_photo_meta_boxes () {
		global $post;
		//テンプレートに画像データを渡す
		$data = array();
		$data['photo_meta_nonce'] = wp_create_nonce('photo_meta_nonce');
		
		$data['krc_cast_screens'] = json_decode(get_post_meta($post->ID, "_krc_cast_screens", true));
		echo $this->template_parser->render( 'photo_meta.html', $data );
	}
	
	
	//メタキャスト基本情報保存
	public function save_cast_meta_data ($post_id) {
		global $post;
		//nonce値比較　権限確認
		if (!$post_id || !isset($_POST['cast_meta_nonce'])) { return; }
		if (!wp_verify_nonce($_POST['cast_meta_nonce'], 'cast_meta_nonce')) { return $post->ID; }
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) { return $post->ID; }
		if ($this->post_type == $_POST['post_type'] && current_user_can('publish_posts', $post->ID)) {
			//バリデート
			$krc_name = (isset( $_POST['krc_name'] ) ? (string) esc_attr( trim($_POST['krc_name']) ) : '');
			$krc_age = (isset( $_POST['krc_age'] ) ? (int) esc_attr( trim($_POST['krc_age']) ) : '');
			$krc_tall = (isset( $_POST['krc_tall'] ) ? (int) esc_attr( trim($_POST['krc_tall']) ) : '');
			$krc_bust = (isset( $_POST['krc_bust'] ) ? (int) esc_attr( trim($_POST['krc_bust']) ) : '');
			$krc_waist = (isset( $_POST['krc_waist'] ) ? (int) esc_attr( trim($_POST['krc_waist']) ) : '');
			$krc_hips = (isset( $_POST['krc_hips'] ) ? (int) esc_attr( trim($_POST['krc_hips']) ) : '');
			$krc_cups = (isset( $_POST['krc_cups'] ) ? (string) esc_attr( trim($_POST['krc_cups']) ) : '');
			
			if ( empty( $krc_name ) ) {
				$this->error_message .= __('【基本情報】 名前を入力して下さい。<br/>', 'krc' );
			}
			//エラーの場合下書きにしてエラー文作成
			if ( !empty( $this->error_message ) ) {
				remove_action( 'save_post', array( $this, 'save_cast_meta_data' ) );
				$post->post_status = "draft";
				wp_update_post( $post );
				add_action( 'save_post', array( $this, 'save_cast_meta_data' ) );
				$this->error_message = __('<strong>入力エラー</strong><br/>', 'krc' ) . $this->error_message;
				set_transient( "cast_error_message_$post->ID", $this->error_message, 60 * 10 );
			}
			//メタボックス情報保存
			update_post_meta( $post->ID, "_krc_name", $krc_name );
			update_post_meta( $post->ID, "_krc_age", $krc_age );
			update_post_meta( $post->ID, "_krc_tall", $krc_tall );
			update_post_meta( $post->ID, "_krc_bust", $krc_bust );
			update_post_meta( $post->ID, "_krc_waist", $krc_waist );
			update_post_meta( $post->ID, "_krc_hips", $krc_hips );
			update_post_meta( $post->ID, "_krc_cups", $krc_cups );
			$krc_cast_screens = isset($_POST['h_krc_cast_screens']) ? $_POST['h_krc_cast_screens'] : "";
			$krc_cast_screens = json_encode($krc_cast_screens);
			update_post_meta($post->ID, "_krc_cast_screens", $krc_cast_screens);
			
		} else {
			return $post->ID;
		}
		
	}
	
	
	//オリジナルエラーメッセージに変更
	public function generate_cast_messages( $messages ) {
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
				1 => sprintf(__('キャストを更新しました。<a href="%s">確認</a>', 'krc' ), esc_url(get_permalink($post_ID))),
				2 => __('カスタムフィールド更新', 'krc' ),
				3 => __('カスタムフィールド削除', 'krc' ),
				4 => __('更新しました', 'krc' ),
				5 => isset($_GET['revision']) ? sprintf(__('リビジョンは %s', 'krc' ), wp_post_revision_title((int) $_GET['revision'], false)) : false,
				6 => sprintf(__('キャストを登録しました。<a href="%s">確認</a>', 'krc' ), esc_url(get_permalink($post_ID))),
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
			'_krc_name' => __( '名前', 'krc' ),
			'_krc_cast_screens' => __( '写真', 'krc' ),
			'_krc_age' => __( '年齢', 'krc' ),
			'size' => __( 'サイズ', 'krc' ),
			'krc_type' => __( 'タイプ', 'krc' ),
			'krc_grade' => __( 'グレード', 'krc' ),
			'krc_new' => __( '区分', 'krc' ),
			'date' => __( '日時', 'krc' ),
		);
		return $columns;
	}
	
	//キャスト一覧表示内容作成
	//綺麗にした方がいいかも
	public function add_cpt_column ($column_name) {
		global $post;
		
		if ($column_name == "_krc_cast_screens") {
			$screens = json_decode(get_post_meta($post->ID, "_krc_cast_screens", true));
			echo '<a href="post.php?post=' . $post->ID . '&action=edit">' . '<img src="' . esc_url($screens[0]) . '" class="list_cast_photo cast_photo" />' . '</a>';
		} else if ($column_name == "_krc_name") {
			echo '<a href="post.php?post=' . $post->ID . '&action=edit">' . esc_html(get_post_meta( $post->ID, $column_name, true )) . '</a>';
		} else if ($column_name == "size") {
			echo esc_html(get_post_meta( $post->ID, '_krc_tall', true )) . ' (' .  esc_html(get_post_meta( $post->ID, '_krc_cups', true )) . ') ' . esc_html(get_post_meta( $post->ID, '_krc_bust', true )) . '.' . esc_html(get_post_meta( $post->ID, '_krc_waist', true )) . '.' . esc_html(get_post_meta( $post->ID, '_krc_hips', true ));
			
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
	
	
	//キャストの投稿画面だったらカテゴリの自動並べ替えを止める
	public function krc_switch ($args, $post_id = null) {
		global $post;
		if ($post->post_type == $this->post_type){
			if ($args['checked_ontop'] !== false ){ 
				$args['checked_ontop'] = false;
			}
			return $args;
		}
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}







?>