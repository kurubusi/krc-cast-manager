<?php
class Krc_Template_Loader{
	private $template_dir;

	public function __construct(){
		$this->template_dir = "templates";
	}

	public function render($name,$data =  array() ){
		include plugin_dir_path(__FILE__).$this->template_dir."/".$name.".php";
	}
}


?>
