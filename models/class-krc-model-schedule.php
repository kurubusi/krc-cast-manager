<?php


class Krc_Model_Schedule {
	
	private $template_parser;
	private $error_message;
	private $cast_post_type;
	private $table_name;
	
	public function __construct ($template_parser) {
		global $wpdb;
		$this->cast_post_type = 'krc_cast';
		$this->error_message = "";
		$this->template_parser = $template_parser;
		$this->table_name = $wpdb->prefix . 'krc_schedules';
	}
	
	public function initialize () {
		add_action( 'admin_menu', array(&$this, 'addMenu') );
		add_action( 'wp_enqueue_scripts', array($this, 'include_scripts'));
		add_action( 'wp_ajax_krc_schedule_target_day', array($this, 'preparation_calendar'));
		add_action( 'wp_ajax_krc_schedule_update', array($this, 'schedule_update'));
		
		
	}
	
	
	public function addMenu () {
		global $userdata;
		$post_types = get_post_types();
		
		foreach( $post_types as $post_type_name ) {
			if ($post_type_name == 'page') { continue; }
			if ($post_type_name == 'reply' || $post_type_name == 'topic') { continue; }
			if ($post_type_name == 'krc_cast') {
				add_submenu_page('edit.php?post_type=krc_cast', __('スケジュール管理', 'krc'), __('スケジュール管理', 'krc'), 'publish_posts', 'cast-schedule-'.$post_type_name, array(&$this, 'schedule_page') );
			}
		}
		
	}
	
	
	public function schedule_update () {
		global $wpdb;//DB情報
		
		if (isset($_POST['day'])) {
			$day = str_replace("/", "-", $_POST['day']);
			if (is_array($_POST['order'])) {
				$item = serialize($_POST['order']);
			} else if ($_POST['order'] == "rest") {
				$item = serialize('rest');
			} else {
				$item = '';
			}
			
			$get_id = $wpdb->get_var(
				$wpdb->prepare("SELECT id FROM $this->table_name WHERE day = %s AND status = %d", $day, 0)
			);
			
			$set_arr = array(
				'day' => $day,
				'work' => $item,
				'status' => 0
			);
			if ($get_id) {
				$wpdb->update( $this->table_name, $set_arr, array( 'day' => $day), array( '%s', '%s', '%d' ), array( '%s' ) );
			} else {
				$wpdb->insert( $this->table_name, $set_arr, array( '%s', '%s', '%d' ) );
			}
			
		}
		
		
		exit;
	}
	
	
	public function schedule_page () {
		global $post, $post_id;
		//テンプレートにデータを渡す
		$data = array();
		echo $this->template_parser->render( 'cast_schedule.html', $data );
	}
	
	
	public function include_scripts() {
		global $wp_query;
		$config_array = array(
			'ajaxUrl' => admin_url('admin-ajax.php'),
		);
		wp_localize_script('schedule_target_day', 'krcphpschedule', $config_array);
	}
	
	
	public function preparation_calendar () {
		global $post, $post_id, $wpdb;
		
		//後出勤時間
		//吐き出し側は後でいいか
		
		$day = str_replace("/", "-", $_POST['order']);
		
		$day_schedule = $wpdb->get_var(
			$wpdb->prepare("SELECT work FROM $this->table_name WHERE day = %s AND status = %d", $day, 0)
		);
		$work = unserialize($day_schedule);
		$krc_schedule_in_arr = array();
		if (is_array($work)) {
			$work_id_arr = array_keys($work);
			$args = array(
				'post_type' =>  $this->cast_post_type,
				'post__in' => $work_id_arr, 
				'posts_per_page' => -1,
				'orderby' => 'post__in'
			);
			$the_query = new WP_Query($args);
			while ( $the_query->have_posts() ) : $the_query->the_post();
				$photo = json_decode(post_custom('_krc_cast_screens'));
				$krc_schedule_in_arr[get_the_ID()] = array(
					'krc_name' => (string) esc_html(post_custom('_krc_name')),
					'krc_cast_screens' => (string) esc_url($photo[0]),
					'fastslow' => $work[get_the_ID()]['fastslow'],
					'starttime' => $work[get_the_ID()]['starttime'],
					'endtime' => $work[get_the_ID()]['endtime'],
				);
			endwhile;
			wp_reset_postdata();
		} else if ($work == "rest") {
			$krc_schedule_rest = "rest";
			$work_id_arr = array();
		} else {
			$work_id_arr = array();
		}
		
		$args = array(
			'sort_column'   =>  'menu_order',
			'post_type'     =>  $this->cast_post_type,
			'posts_per_page' => -1,
			'post__not_in' => $work_id_arr,
			'orderby'        => array(
				'menu_order'    => 'ASC',
				'post_date'     =>  'DESC'
			)
		);
		$the_query = new WP_Query($args);
		$krc_schedule_out_arr = array();
		while ( $the_query->have_posts() ) : $the_query->the_post();
			$photo = json_decode(post_custom('_krc_cast_screens'));
			$krc_schedule_out_arr[get_the_ID()] = array(
				'krc_name' => (string) esc_html(post_custom('_krc_name')),
				'krc_cast_screens' => (string) esc_url($photo[0])
			);
		endwhile;
		wp_reset_postdata();
		
		$return_arr = array(
			'post_in' => $krc_schedule_in_arr,
			'post_not_in' => $krc_schedule_out_arr,
			'rest' => $krc_schedule_rest
		);
		
		echo json_encode($return_arr);
		exit;
		
	}
	
	
	
	
	
	
	
	
	
	
}

?>