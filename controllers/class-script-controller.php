<?php

class Script_Controller {
	
	public function enque_scripts(){
		add_action('admin_enqueue_scripts', array($this, 'include_admin_scripts_styles'));
	}
	
	
	
	public function include_admin_scripts_styles () {
		wp_enqueue_script('jquery');
		if (function_exists('wp_enqueue_media')) {
			wp_enqueue_media();
		} else {
			wp_enqueue_style('thickbox');
			wp_enqueue_script('media-upload');
			wp_enqueue_script('thickbox');
		}
		
		wp_register_script('krc_filr_upload', plugins_url('js/krc-file-uploader.js', dirname(__FILE__)), array("jquery"));
		wp_enqueue_script('krc_filr_upload');
		wp_register_style('krc_cast_css', plugins_url('css/krc-cast.css', dirname(__FILE__)));
		wp_enqueue_style('krc_cast_css');
		
		wp_register_script('krc_cast_post', plugins_url('js/krc-cast-post.js', dirname(__FILE__)), array("jquery"));
		wp_enqueue_script('krc_cast_post');
		wp_register_style('krc_cast_ranking_css', plugins_url('css/krc-cast-ranking.css', dirname(__FILE__)));
		wp_enqueue_style('krc_cast_ranking_css');
		
		
		
		wp_enqueue_script('jquery-ui-sortable');
		wp_register_style('CPTStyleSheets', plugins_url('css/cpt.css', dirname(__FILE__)));
		wp_enqueue_style( 'CPTStyleSheets');
		
		wp_enqueue_script('jquery-touch-punch');
		
		wp_register_script('krc_cast_order', plugins_url('js/krc-cast-order.js', dirname(__FILE__)), array("jquery"));
		wp_enqueue_script('krc_cast_order');
		
		wp_register_script('krc_cast_ranking', plugins_url('js/krc-cast-ranking.js', dirname(__FILE__)), array("jquery"));
		wp_enqueue_script('krc_cast_ranking');
		wp_register_style('krc_cast_ranking_css', plugins_url('css/krc-cast-ranking.css', dirname(__FILE__)));
		wp_enqueue_style('krc_cast_ranking_css');
		
		wp_enqueue_script('jquery-ui-datepicker');
		wp_register_script('krc-cast-schedule', plugins_url('js/krc-cast-schedule.js', dirname(__FILE__)), array("jquery", "jquery-ui-datepicker"));
		wp_enqueue_script('krc-cast-schedule');
		wp_register_style('krc-cast-schedule_css', plugins_url('css/krc-cast-schedule.css', dirname(__FILE__)));
		wp_enqueue_style('krc-cast-schedule_css');
		wp_register_style('jquery-ui_css', plugins_url('css/jquery-ui.min.css', dirname(__FILE__)));
		wp_enqueue_style('jquery-ui_css');
		
		
		
		
		
		
	}
	
	
	
}

?>
