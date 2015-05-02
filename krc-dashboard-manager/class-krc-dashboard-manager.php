<?php
class Krc_Dashboard_Manager {
	
	public function __construct(){ }
	
	//スケジュール用オリジナルテーブル作成
	public function create_custom_tables () {
		global $wpdb;
		$table_name = $wpdb->prefix . 'krc_schedules';
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			day date DEFAULT '0000-00-00' NOT NULL,
			work text DEFAULT '',
			status mediumint(9) DEFAULT '0' NOT NULL,
			UNIQUE KEY ID (id)
		);";
		dbDelta($sql);
	}
	
	
}
?>