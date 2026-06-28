<?php


class panel_item {
	
	private $query;
	private $controller;
	private $label;
	
	function __construct( $query, $controller, $label){
		$this->query = $query;
		$this->controller = $controller;
		$this->label = $label;		
	}
	
	function get_HTML (){
		
		$data = query1($this->query);
		$item_value = reset($data);
		$item_url = "#";
		if ($this->controller != "")
			$item_url = build_URL_Controller_search($this->controller);
		$item_label = $this->label;
		ob_start();
		include 'templates/panel_item.php';
		return ob_get_clean();
	}
	
}
