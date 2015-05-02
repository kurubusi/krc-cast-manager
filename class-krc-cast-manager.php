<?php

/*
  Plugin Name: Krc Cast Manager
  Plugin URI:
  Description: 在席キャスト管理システム
  Version: 1.0
  Author: Masahiro Okubo
  Author URI: http://kurubusi.net/
  License: GPLv3
 */

if( !defined('PLUGIN_NAME') )
	define( 'PLUGIN_NAME', 'krc-cast-manager' );

require_once 'krc-dashboard-manager/class-krc-dashboard-manager.php';
require_once 'krc-dashboard/class-krc-dashboard.php';
require_once 'krc-custom-post-manager/class-krc-custom-post-manager.php';
require_once 'krc-cast-order/class-krc-cast-order.php';
require_once 'admin/index.php';


include_once 'class-krc-template-loader.php';

//オートローダー
spl_autoload_register('krc_autoloader');

//twig
$base_path = plugin_dir_path(__FILE__);
require_once $base_path.'/twig/lib/Twig/Autoloader.php';
Twig_Autoloader::register();

//オートローダー
function krc_autoloader( $class_name ) {
	$class_components = explode( "_", $class_name );
	if ( isset( $class_components[0] ) && $class_components[0] == "Krc" && isset( $class_components[1] )) {
		$class_directory = $class_components[1];
		unset( $class_components[0], $class_components[1] );
		$file_name = implode( "_", $class_components );
		$base_path = plugin_dir_path(__FILE__);
		switch ( $class_directory ) {
		case 'Model':
			$file_path = $base_path . "models/class-krc-model-".lcfirst( $file_name ) . '.php';
			if ( file_exists( $file_path ) && is_readable( $file_path ) ) {
				include $file_path;
			}
			break;
		}
	}
}





class Krc_Cast_Manager {
	
	public function initialize_controllers() {
		require_once 'controllers/class-activation-controller.php';
		$activation_controller = new Activation_Controller();
		$activation_controller->initialize_activation_hooks();
		
		require_once 'controllers/class-script-controller.php';
		$script_controller = new Script_Controller();
		$script_controller->enque_scripts();
		
		
		
		
	}
	
	public function initialize_app_controllers() {
		
		
		
		$cast_order = new Krc_Dashboard();
		$cast_order->initialize();
		
		$cast_order = new Krc_Cast_Order();
		$cast_order->initialize();
		
		
		$base_path = plugin_dir_path(__FILE__);
		require_once $base_path . 'class-twig-initializer.php';
		$custom_posts = new Krc_Custom_Post_Manager();
		$custom_posts->initialize();
		
	}
	
}
$krc_register_cast = new Krc_Cast_Manager();
$krc_register_cast->initialize_controllers();
$krc_register_cast->initialize_app_controllers();

?>
