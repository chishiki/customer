<?php

final class CustomerViewController implements ViewControllerInterface {

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

	public function getView() {

		$loc = $this->loc;
		$input = $this->input;
		$modules = $this->modules;
		$errors = $this->errors;
		$messages = $this->messages;

		switch($loc[1]) {

			case 'admin':

				$v = new CustomerAdminViewController($loc, $input, $modules, $errors, $messages);
				break;

			case 'test':

				$v = new CustomerTestViewController($loc, $input, $modules, $errors, $messages);
				break;

			default:

				$v = new CustomerMainViewController($loc, $input, $modules, $errors, $messages);


		}

		if (isset($v)) {
			return $v->getView();
		} else {
			$url = '/' . Lang::prefix();
			header("Location: $url" );
		}

	}

}

?>