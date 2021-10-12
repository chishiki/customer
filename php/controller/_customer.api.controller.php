<?php

final class CustomerAPI {
		
	    private $loc;
	    private $input;
	    
	    public function __construct($loc, $input) {
			
	        $this->loc = $loc;
	        $this->input = $input;
			
		}
		
		public function response() {

	    	// /api/customer/search/
			if ($this->loc[1] == 'customer' && $this->loc[2] == 'search') {


				$customerSearchString = null;
				if (
					isset($this->input['customerSearchString'])
					&& !empty($this->input['customerSearchString'])
				) {
					$customerSearchString = $this->input['customerSearchString'];
				}

				$list = new CustomerList($customerSearchString);
				$cl = $list->customers();

				$customers = array();
				foreach ($cl AS $customerID) {
					$c = new Customer($customerID);
					$customers[] = array('value' => $customerID, 'label' => $c->name());
				}

				return json_encode($customers);

			}

			// /api/customer/1001/
			if ($this->loc[1] == 'customer' && is_numeric($this->loc[3])) {

				$customer = new Customer($this->loc[3]);
				return json_encode($customer);

			}

			$response = '{"api":"customer"}';
			return $response;

		}

		
	}

?>