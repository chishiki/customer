<?php

final class CustomerTestView {

	private $loc;
	private $input;
	private $modules;
	private $errors;
	private $messages;

	public function __construct($loc = array(), $input = array(), $modules = array(), $errors = array(), $messages = array()) {

		$this->loc = $loc;
		$this->input = $input;
		$this->modules = $modules;
		$this->errors = $errors;
		$this->messages = $messages;

	}

	public function customerTestView() {

		$test = 'CUSTOMER TEST';
		$card = new CardView('customer_test_id', array('container-fluid'),'', array('col-12'), 'TEST', $test, false);
		return $card->card();

	}

}

?>