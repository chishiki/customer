<?php

final class CustomerAdminViewController {

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
		$input = $this->input;
		$modules = $this->modules;
		$errors = $this->errors;
		$messages = $this->messages;

		if ($loc[0] == 'customer' && $loc[1] == 'admin') {

			$view = new CustomerView($loc, $input, $modules, $errors, $messages);

			// /customer/admin/create/
			if ($loc[2] == 'create') { return $view->customerForm('create'); }

			// /customer/admin/update/<customerID>/
			if ($loc[2] == 'update' && is_numeric($loc[3])) { return $view->customerForm('update', $loc[3]); }

			// /customer/admin/confirm-delete/<customerID>/
			if ($loc[2] == 'confirm-delete' && is_numeric($loc[3])) { return $view->customerConfirmDelete($loc[3]); }

			// /customer/admin/
			return $view->customerList();

		}
		
	}

}

?>