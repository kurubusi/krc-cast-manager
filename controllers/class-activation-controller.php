<?php

class Activation_Controller {

	public function initialize_activation_hooks() {
		register_activation_hook('krc-cast-manager/class-krc-cast-manager.php', array($this, 'execute_activation_hooks'));
		//削除した時の処理も必用か考える
		
	}
	
	public function execute_activation_hooks() {
		$database_manager = new Krc_Dashboard_Manager();
		$database_manager->create_custom_tables();
		
		
		
	}

}
?>
