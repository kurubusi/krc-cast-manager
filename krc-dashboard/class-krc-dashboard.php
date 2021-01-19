<?php

class Krc_Dashboard {
	
	
	public function initialize () {
		
		add_action( 'wp_before_admin_bar_render', array($this, 'customize_admin_toolbar') );
		add_action( 'wp_before_admin_bar_render', array($this, 'customize_admin_new_toolbar') );
		
		
	}
	
	
	public function customize_admin_new_toolbar () {
		global $wp_admin_bar;

	}
	
	public function customize_admin_toolbar () {
		global $wp_admin_bar;
		
		if (current_user_can('publish_posts')) {
			$wp_admin_bar->add_menu(array(
				'id' => 'krc_toolbar_cast',
				'title' => '在席キャスト管理',
				'href' => admin_url() . 'edit.php?post_type=cast'
			));
			$wp_admin_bar->add_menu(array(
				'id' => 'krc_toolbar_cast_list',
				'title' => 'キャスト管理',
				'href' => admin_url() . 'edit.php?post_type=cast',
				'parent' => 'krc_toolbar_cast'
			));
			$wp_admin_bar->add_menu(array(
				'id' => 'krc_toolbar_cast_new',
				'title' => 'キャスト新規登録',
				'href' => admin_url() . 'post-new.php?post_type=cast',
				'parent' => 'krc_toolbar_cast'
			));
			$wp_admin_bar->add_menu(array(
				'id' => 'krc_toolbar_schedule',
				'title' => 'スケジュール管理',
				'href' => admin_url() . 'edit.php?post_type=cast&page=cast-schedule-krc_cast',
				'parent' => 'krc_toolbar_cast'
			));
			$wp_admin_bar->add_menu(array(
				'id' => 'krc_toolbar_order',
				'title' => '表示順管理',
				'href' => admin_url() . 'edit.php?post_type=cast&page=order-post-types-krc_cast',
				'parent' => 'krc_toolbar_cast'
			));
			$wp_admin_bar->add_menu(array(
				'id' => 'krc_toolbar_ranking_list',
				'title' => 'ランキング管理',
				'href' => admin_url() . 'edit.php?post_type=krc_ranking',
				'parent' => 'krc_toolbar_cast'
			));
			
			
			
			
		}
		
	}
	
	
}

?>