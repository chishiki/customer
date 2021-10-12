<?php

final class CustomerTestViewController implements ViewControllerInterface {

	private $loc;
	private $input;
	private $modules;
	private $errors;
	private $messages;

	public function __construct($loc, $input, $modules, $errors, $messages) {

		$this->loc = $loc;
		$this->input = $input;
		$this->modules = $modules;
		$this->errors = $errors;
		$this->messages =  $messages;

	}

	public function getView() {

		$loc = $this->loc;

		if ($loc[0] == 'customer' && $loc[1] == 'test') {

			$view = new CustomerTestView($this->loc, $this->input, $this->modules, $this->errors, $this->messages);
			return $view->customerTestView();

		}

	}

}

?>