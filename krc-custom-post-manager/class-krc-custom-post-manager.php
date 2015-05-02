<?php


class Krc_Custom_Post_Manager {
	
	private $base_path;
	private $template_parser;
	private $cast;
	private $ranking;
	
	public function __construct() {
	}
	
	public function initialize(){
		$this->template_parser = Twig_Initializer::initialize_templates();
		$this->cast = new Krc_Model_Casts($this->template_parser);
		$this->cast->initialize();
		
		$this->ranking = new Krc_Model_Ranking($this->template_parser);
		$this->ranking->initialize();
		
		$this->schedule = new Krc_Model_Schedule($this->template_parser);
		$this->schedule->initialize();
		
		
		
		
	}

}


?>
