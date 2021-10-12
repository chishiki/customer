<?php

final class CustomerAdminController {

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

		$loc = $this->loc;
    	$input = $this->input;

		if (isset($input['customerList'])) {
			foreach ($input['customerList'] AS $key => $value) { $_SESSION['customerList'][$key] = $value; }
		}

		if ($loc[2] == 'create' && !empty($input)) {

			// $this->errors = Customer::validate('create', $input);
			
			if (empty($this->errors)) {
				
				$customer = new Customer();
				foreach ($input AS $property => $value) { if (isset($customer->$property)) { $customer->$property = $value; } }
				$customerID = Customer::insert($customer, true, 'customer_');
				$successURL = '/' . Lang::prefix() . 'customer/admin/';
				header("Location: $successURL");

			}

		} 
		
		if ($loc[2] == 'update' && is_numeric($loc[3])) {
			
			$customerID = $loc[3];

			if ($loc[4] == 'address') {
				
				$baseURL = '/' . Lang::prefix() . 'customer/admin/update/' . $customerID . '/address/';
				$action = $loc[5]; // (create|update|delete|set-default)
				if (!empty($loc[6])) { $addressID = $loc[6]; } else { $addressID = null; }

				$ac = new AddressController($loc, $input, $this->modules);
				$ac->setState($baseURL, $action, 'Customer', $customerID, $addressID);

			} elseif ($loc[4] == 'files' && isset($_FILES['perihelionFiles'])) {
				
				$this->errors = File::uploadFiles($_FILES['perihelionFiles'],'Customer',$customerID);
				
			} elseif ($loc[4] == 'files' && $loc[5] == 'delete' && is_numeric($loc[6])) {
				
				$fileID = $loc[6];
				$successURL = "/" . Lang::languageUrlPrefix() . "customer/admin/update/" . $customerID . "/files/";
				// $fu = new CustomerFileUtilities();
				// $this->errorArray = $fu->delete($fileID, $successURL);
	
			} else {
				
				if (!empty($input)) {
					
					// $this->errors = Customer::validate('update', $input);
					
					if (empty($this->errors)) {
						
						$customer = new Customer($customerID);
						$customer->updated = date('Y-m-d H:i:s');
						foreach ($input AS $property => $value) { if (isset($customer->$property)) { $customer->$property = $value; } }
						$conditions = array('customerID' => $customerID);
						Customer::update($customer, $conditions, true, false, 'customer_');
						$this->messages[] = Lang::getLang('customerUpdateSuccessful');
	
					}
					
				}
				
			}

		}

		if ($loc[2] == 'delete' && is_numeric($loc[3])) {

			// any orders?
			$sosf = new OrderSearchFields();
			$sosf->customerDateFrom = null;
			$sosf->customerDateTo = null;
			$sosf->customerID = $loc[3];
			$sol = new CustomerList($sosf);
			$numberOfCustomers = count($sol->customers());

			if (($numberOfCustomers > 0)) {
				$this->errors[] = array('customerDelete' => Lang::getLang('thisCustomerCannotBeDeleted'));
			}
			
			if (empty($this->errors)) {
				
				$customer = new Customer($loc[3]);
				$customer->markAsDeleted();
				$this->messages[] = Lang::getLang('customerDeleteSuccessful');

			}
			
		}

	}
	

	public function getErrors() {
		return $this->errors;
	}

	public function getMessages() {
	    return $this->messages;
	}
	
}

?>