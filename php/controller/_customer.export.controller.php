<?php

final class CustomerExportController {

	private $loc;
	private $input;
	private $modules;

	private $filename;
	private $columns;
	private $rows;

	public function __construct($loc, $input, $modules) {

		$this->loc = $loc;
		$this->input = $input;
		$this->modules = $modules;

		$this->filename = 'export';
		$this->columns = array();
		$this->rows = array();

		if ($loc[0] == 'csv' && $loc[1] == 'news') {

			$this->filename = 'customers_' . date('Ymd-His');

			$customer = new Customer();
			$cols = array_keys($customer->describe());
			foreach ($cols AS $colName) { $this->columns = Lang::getLang($colName); }

			$list = new CustomerList();
			$customers = $list->customers();

			foreach ($customers AS $customerID) {
				$customer = new News($customerID);
				$this->rows[] = $customer;
			}

		}

	}

	public function filename() {

		return $this->filename;
		
	}
	
	public function columns() {

		return $this->columns;
		
	}
	
	public function rows() {

		return $this->rows;
		
	}

}

?>