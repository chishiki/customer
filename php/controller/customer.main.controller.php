<?php

final class CustomerMainController {

	private $loc;
	private $input;
	private $modules;
	private $errors;
	private $messages;
	
	public function __construct($loc, $input, $modules) {

		$this->loc = $loc;
		$this->input = $input;
		$this->modules = $modules;
		$this->errors = array();
		$this->messages =  array();
		
	}
	
	public function setState() {

	}
	

	public function getErrors() {
		return $this->errors;
	}

	public function getMessages() {
	    return $this->messages;
	}
	
}

?>