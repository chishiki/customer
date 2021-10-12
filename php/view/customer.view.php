<?php 

final class CustomerView {

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
	    $this->messages = $messages;
	    
	}

	public function customerList() {

		// set up CustomerList parameters
		$searchString = null;
		if (isset($_SESSION['customerList']['priceLevel'])) { $priceLevel = $_SESSION['customerList']['priceLevel']; } else { $priceLevel = null; }
		if (isset($_SESSION['customerList']['customerID'])) { $customerID = $_SESSION['customerList']['customerID']; } else { $customerID = null; }
		$limitOffset = 0;
		$limitCount = 25;
		$currentPage = 1;
		if (is_numeric($this->loc[2])) { $currentPage = $this->loc[2]; }
		if ($currentPage > 1) { $limitOffset = ($currentPage - 1) * $limitCount; };

		// get total number of customers for this query
		$totalCustomerList = new CustomerList($searchString, $customerID, $priceLevel);
		$customerCount = count($totalCustomerList->customers());

		// get this page's customers
		$customerList = new CustomerList($searchString, $customerID, $priceLevel, $limitOffset, $limitCount);
		$customers = $customerList->customers();
		
		// calculate pagination parameters
		$totalPages = ceil($customerCount / 25);
		$currentPage = 1;
		if (is_numeric($this->loc[2])) { $currentPage = $this->loc[2]; }
		if ($currentPage > $totalPages) { $currentPage = 1; }

		// set up pagination
		$baseURL = '/' . Lang::prefix() . 'customer/admin/';
		$pagination = PaginationView::paginate($totalPages, $currentPage, $baseURL);

		$dmp = new CustomerModalParameters();
		$dmp->fieldName = 'customerList[customerID]';
		if (isset($_SESSION['customerList']['customerID'])) { $dmp->customerID = $_SESSION['customerList']['customerID']; }
		$dmp->includeModal = true;

	    $body = '

			<div class="d-flex flex-wrap mb-2">
				<div class="flex-grow-1" data-customer-count="' . $customerCount . '" data-base-url="' . $baseURL . '" data-total-pages="' . $totalPages . '" data-current-page="' . $currentPage . '">' . ($totalPages>1?$pagination:'') . '</div>
				<div class="mb-2">
					<a href="/' . Lang::prefix() . 'csv/customer/customers/" class="btn btn-outline-secondary mr-1" download><span class="fas fa-file-download"></span> ' . Lang::getLang('export') . '</a>
					<a href="/' . Lang::prefix() . 'customer/admin/create/" class="btn btn-outline-success"><span class="fas fa-plus"></span> ' . Lang::getLang('create') . '</a>
				</div>
			</div>

			<form id="customer_list_filter" method="post" class="mb-2" action="/' . Lang::prefix() . 'customer/admin/">
				<div class="form-row">
					<div class="form-group col-12 col-md-6 col-lg-4">' . $this->customerListAutocomplete($dmp) . '</div>
					<div class="form-group col-12 col-sm-6 col-md-2">' . $this->priceLevelDropdown('customerList[priceLevel]', $priceLevel, true) . '</div>
					<div class="form-group col-12 col-sm-6 col-md-2"><button type="submit" class="btn btn-block btn-outline-primary">' . Lang::getLang('filter'). '</button></div>
					<div class="form-group col-12 col-sm-6 col-md-2"><button type="submit" class="btn btn-block btn-outline-secondary btn-reset">' . Lang::getLang('reset'). '</button></div>
				</div>
			</form>

			<div class="table-container">
				
				<div class="table-responsive">
					<table class="table table-bordered table-striped table-sm">
					  <thead class="thead-light">
					    <tr>
					      <th scope="col" class="text-center">' . Lang::getLang('customerName') . '</th>
						  <th scope="col" class="text-center">' . Lang::getLang('customerTelephone') . '</th>
						  <th scope="col" class="text-center">' . Lang::getLang('customerFax') . '</th>
						  <th scope="col" class="text-center">' . Lang::getLang('customerEmail') . '</th>
						  <th scope="col" class="text-center">' . Lang::getLang('priceLevel') . '</th>
					      <th scope="col" class="text-center">' . Lang::getLang('customerNotes') . '</th>
					      <th scope="col" class="text-center">' . Lang::getLang('customers') . '</th>
					      <th scope="col" class="text-center table-action-column">' . Lang::getLang('action') . '</th>
					    </tr>
					  </thead>
					  <tbody>' . $this->customerListRows($customers) . '</tbody>
					</table>
				</div>
			</div>

	   ';
		
		$card = new CardView('customer_list',array('container-fluid'),'',array('col-12'),Lang::getLang('customers'),$body);
	    return $card->card();
	    
	}

	public function customerForm($type, $customerID = null) {

		$customer = new Customer($customerID);
		if (!empty($this->input)) {
			foreach($this->input AS $key => $value) { if(isset($customer->$key)) { $customer->$key = $value; } }
		}
		
		$form = $this->customerFormNavTabs();
		
		if ($type == 'update' && is_numeric($this->loc[3]) && $this->loc[4] == 'files') {
			
			$fv = new FileView($this->loc, $this->input, $this->errors);
			$form .= $fv->fileManager('Customer', $this->loc[3], '/' . Lang::prefix() . 'customer/admin/update/' . $this->loc[3] . '/files/');
				
		} elseif ($type == 'update' && is_numeric($this->loc[3]) && $this->loc[4] == 'address') {
			
			$av = new AddressView($this->loc, $this->input, $this->errors);
			$form .= $av->addressManager('Customer', $this->loc[3], '/' . Lang::prefix() . 'customer/admin/update/' . $this->loc[3] . '/address/');
				
		} else {

			$form .= '<div class="row">';
			
				$form .= '<div class="col-12 col-lg-8">';
					$form .= '
						<form id="customerForm' . ucfirst($type) . '" method="post" action="/' . Lang::prefix() . 'customer/admin/' . $type . '/' . ($customerID?$customerID.'/':'') . '">
							' . ($customerID?'<input type="hidden" name="customerID" value="' . $customerID . '">':'') . '
							<div class="form-row">
								<div class="form-group col-12 col-md-4">
									<label for="customerNameEnglish">' . Lang::getLang('customerNameEnglish') . '</label>
									<input type="text" class="form-control" name="customerNameEnglish" value="' . $customer->customerNameEnglish . '" required>
								</div>
								<div class="form-group col-9 col-md-4">
									<label for="customerNameJapanese">' . Lang::getLang('customerNameJapanese') . '</label>
									<input type="text" class="form-control" name="customerNameJapanese" value="' . $customer->customerNameJapanese . '">
								</div>
								<div class="form-group col-3 col-md-2">
									<label for="customerHonorarySuffix">' . Lang::getLang('customerHonorarySuffix') . '</label>
									' . $this->honorarySuffixDropdown('customerHonorarySuffix', $customer->customerHonorarySuffix) . '
								</div>
								<div class="form-group col-12 col-md-2">
									<label for="priceLevel">' . Lang::getLang('priceLevel') . '</label>
									' . $this->priceLevelDropdown('priceLevel', $customer->priceLevel) . '
								</div>
							</div>
							<div class="form-row">
								<div class="form-group col-12 col-sm-4">
									<label for="customerRepresentativeDepartment">' . Lang::getLang('customerRepresentativeDepartment') . '</label>
									<input type="text" class="form-control" name="customerRepresentativeDepartment" value="' . $customer->customerRepresentativeDepartment . '">
								</div>
								<div class="form-group col-12 col-sm-4">
									<label for="customerPersonInCharge">' . Lang::getLang('customerPersonInCharge') . '</label>
									<input type="text" class="form-control" name="customerPersonInCharge" value="' . $customer->customerPersonInCharge . '">
								</div>
                                <div class="form-group col-12 col-sm-4">
									<label for="customerRepresentativeTitle">' . Lang::getLang('customerRepresentativeTitle') . '</label>
									<input type="text" class="form-control" name="customerRepresentativeTitle" value="' . $customer->customerRepresentativeTitle . '">
								</div>
							</div>
                            <div class="form-row">
								<div class="form-group col-12 col-sm-4">
									<label for="customerTelephone">' . Lang::getLang('customerTelephone') . '</label>
									<input type="text" class="form-control" name="customerTelephone" value="' . $customer->customerTelephone . '">
								</div>
								<div class="form-group col-12 col-sm-4">
									<label for="customerFax">' . Lang::getLang('customerFax') . '</label>
									<input type="text" class="form-control" name="customerFax" value="' . $customer->customerFax . '">
								</div>
                                <div class="form-group col-12 col-sm-4">
									<label for="customerEmail">' . Lang::getLang('customerEmail') . '</label>
									<input type="email" class="form-control" name="customerEmail" value="' . $customer->customerEmail . '">
								</div>
							</div>
							<div class="form-row">
								<div class="form-group col-12">
									<label for="customerNotes">' . Lang::getLang('customerNotes') . '</label>
									<textarea class="form-control" name="customerNotes">' . $customer->customerNotes . '</textarea>
								</div>
							</div>
							<hr />
							<div class="text-right">
								<button type="submit" class="btn btn-outline-'. ($type=='create'?'success':'primary') . '">' . Lang::getLang($type) . '</button>
								<a href="/' . Lang::prefix() . 'customer/admin/" class="btn btn-outline-secondary" role="button">' . Lang::getLang('cancel') . '</a>
							</div>
						</form>
					';
				$form .= '</div>';
				
				if ($type == 'update') {
				
					$da = new AddressDefault('Customer', $customerID);
					$addy = $da->address();
					$oneline = array();
					
					$form .= '<div class="col-12 col-lg-4">';
						if (!empty($addy)) {
							$form .= '<ul>';
								if (!empty($addy['streetAddress1'])) { $form .= '<li>' . $addy['streetAddress1'] . '</li>'; }
								if (!empty($addy['streetAddress2'])) { $form .= '<li>' . $addy['streetAddress2'] . '</li>'; }
								if (!empty($addy['city'])) { $oneline[] = $addy['city']; }
								if (!empty($addy['state'])) { $oneline[]= $addy['state']; }
								if (!empty($addy['country'])) { $oneline[]= $addy['country']; }
								if (!empty($oneline)) { $form .= '<li>' . implode(', ', $oneline) . '</li>'; }
								if (!empty($addy['postalCode'])) { $form .= '<li>' . $addy['postalCode'] . '</li>'; }
							$form .= '</ul>';
						}
						$form .= '<div class="text-right">';
							$form .= '<a href="/' . Lang::prefix() . 'customer/admin/update/' . $customerID . '/address/" class="btn btn-outline-primary" role="button">' . Lang::getLang('changeAddress') . '</a>';
						$form .= '</div>';
						$form .= '<hr />';
					$form .= '</div>';

				}
				
			$form .= '</div>';
		
		}
		
		$card = new CardView('customer_form',array('container-fluid'),'',array('col-12'),Lang::getLang('customer'.ucfirst($type)),$form);
	    return $card->card();
		
	}
	
	public function customerConfirmDelete($customerID) {
		
		$customer = new Customer($customerID);

		$form = '
			<form id="customerConfirmDelete"">
				<div class="form-group">
					<label for="customerName">' . Lang::getLang('customerName') . '</label>
					<input type="text" class="form-control" name="customerName" value="' . $customer->name() . '" readonly>
				</div>
				<div class="form-group">
					<label for="customerNotes">' . Lang::getLang('customerNotes') . '</label>
					<input type="text" class="form-control" name="customerNotes" value="' . $customer->customerNotes . '" readonly>
				</div>
				<div class="text-right">
					<a href="/' . Lang::prefix() . 'customer/admin/delete/' . $customerID . '/" class="btn btn-danger" role="button">' . Lang::getLang('delete') . '</a>
					<a href="/' . Lang::prefix() . 'customer/admin/" class="btn btn-outline-secondary" role="button">' . Lang::getLang('cancel') . '</a>
				</div>
			</form>
		';
		
		$card = new CardView('customer_confirm_delete',array('container-fluid'),'',array('col-12'),Lang::getLang('customerConfirmDelete'),$form);
	    return $card->card();
		
	}

	public function customerListAutocomplete(CustomerModalParameters $dmp) {

		$customerName = '';
		if ($dmp->customerID) { $d = new Customer($dmp->customerID); $customerName = $d->name(); }
		$dac = '<input id="' . $dmp->modalKey . '_hidden_input_id" type="hidden" name="' . $dmp->fieldName . '" value="' . $dmp->customerID . '">';

		$dac .= '

			<div class="input-group">
				<input id="' . $dmp->modalKey . '_text_input_id" type="text" class="customer-list-autocomplete form-control' . ($dmp->size?' form-control-'.$dmp->size:'') . '" value="' . $customerName . '">
				<div class="input-group-append">
					<button id="' . $dmp->modalKey. '_btn_id" class="btn-customer-modal-trigger btn btn-outline-secondary' . ($dmp->size?' btn-'.$dmp->size:'') . '" type="button" tabindex="-1" data-toggle="modal" data-target="#customerReferenceFormModal">' . lang::getLang($dmp->customerModalButtonAnchor) . '</button>
				</div>
			</div>

	    ';

		if ($dmp->includeModal) { $dac .= $this->customerReferenceModal('customerReferenceFormModal'); }

		return $dac;

	}
	
	public function customerReferenceModal($modalID = 'customerReferenceModal') {
		
		$dl = new CustomerList();
		$customers = $dl->customers();
		
		$modal = '

			<div class="modal fade" id="' . $modalID . '" tabindex="-1" role="dialog" aria-hidden="true">
				<div class="modal-dialog modal-dialog-scrollable" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title">Customer Reference</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>
						<div class="modal-body">
							<ul class="list-group">
								';
								
								foreach ($customers AS $customerID) {
									$d = new Customer($customerID);
									$modal .= '<a class="list-group-item" data-customerid="' . $customerID . '">' . $d->name() . '</a>';
								}
								
								$modal .= '
							</ul>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</div>
			</div>

		';
		
		return $modal;
		
	}
	
	private function priceLevelDropdown($name = 'priceLevel', $priceLevel = null, $isFilter = false, $selectClasses = null) {
		
		$priceLevels = array(1,2,3,4);
		$d = '<select class="form-control' . ($selectClasses?' '.implode(' ',$selectClasses):'') . '" name="' . $name . '">';
			if ($isFilter) { $d .= '<option value="0">' . Lang::getLang('priceLevel') . '</option>'; }
			foreach ($priceLevels AS $thisPriceLevel) {
				$d .= '<option value="' . $thisPriceLevel . '"' . ($priceLevel==$thisPriceLevel?' selected':'') . '>' . Lang::getLang('priceLevel') . ' ' . $thisPriceLevel . '</option>';
			}
		$d .= '</select>';
		return $d;
		
	}

	private function honorarySuffixDropdown($name = 'customerHonorarySuffix', $selectedHonorarySuffix = null, $size = null) {

		$suffixes = array('onchu','sama');

		$d = '<select class="form-control' . ($size?' form-control-'.$size:'') . '" name="' . $name . '">';
		$d .= '<option value=""></option>';
			foreach ($suffixes AS $suffix) {
				$d .= '<option value="' . $suffix . '"' . ($suffix==$selectedHonorarySuffix?' selected':'') . '>' . Lang::getLang($suffix) . '</option>';
			}
		$d .= '</select>';
		return $d;

	}
	
	private function customerListRows($customers) {
		
		$rows = '';
		
		foreach ($customers AS $customerID) {
			
			$customer = new Customer($customerID);

			// any sales orders?
			/*
			$sosf = new CustomerSearchFields();
			$sosf->customerDateFrom = null;
			$sosf->customerDateTo = null;
			$sosf->customerID = $customerID;
			$sol = new CustomerList($sosf);
			$numberOfCustomers = count($sol->customers());
			*/

			$deleteButton = '';
			/*
			if ($numberOfCustomers == 0) {
				$deleteButton = '<a href="/' . Lang::prefix() . 'customer/admin/confirm-delete/' . $customerID . '/" class="btn btn-outline-danger btn-sm mb-2 mb-xl-0"><span class="far fa-trash-alt"></span> ' . Lang::getLang('delete') . '</a>';
			}
			*/

			$rows .= '
				<tr id="customer_list_item_' . $customerID . '" class="customerListRow">
					<th scope="row">' . $customer->name() . '</th>
					<td>' . $customer->customerTelephone . '</td>
					<td>' . $customer->customerFax . '</td>
					<td>' . $customer->customerEmail . '</td>
					<td class="text-center">' . $customer->priceLevel() . '</td>
					<td>' . $customer->customerNotes . '</td>
					<td class="text-center">' . /* ($numberOfCustomers?$numberOfCustomers:'') . */ '</td>
					<td class="text-center table-action-column">
						<a href="/' . Lang::prefix() . 'customer/admin/update/' . $customerID . '/" class="btn btn-outline-primary btn-sm mb-2 mb-xl-0"><span class="fas fa-pencil-alt"></span> ' . Lang::getLang('update') . '</a>
						' . $deleteButton . '
					</td>
			    </tr>
			';
			
		}
		
		return $rows;
		
	}
	
	private function customerFormNavTabs() {
		
		$type = $this->loc[2];
		$customerID = $this->loc[3];
		$subForm = $this->loc[4];

		$addressDisabled = true;
		$filesDisabled = true;
		$customerURL = '#';
		$addressURL = '#';
		$filesURL = '#';
		$activeTab = 'customer';

		if ($type == 'update') {
			
			$addressDisabled = false;
			$filesDisabled = false;
			$customerURL = '/' . Lang::prefix() . 'customer/admin/update/' . $customerID . '/';
			$addressURL = $customerURL . 'address/';
			$filesURL = $customerURL . 'files/';
			if ($subForm == 'address') { $activeTab = 'address'; }
			if ($subForm == 'files') { $activeTab = 'files'; }
			
		}
		
		$t = '<ul id="customer_form_nav_tabs" class="nav nav-tabs">';
			$t .= '<li class="nav-item">';
				$t .= '<a class="nav-link' . ($activeTab=='customer'?' active':'') . '" href="' . $customerURL . '">' . Lang::getLang('customer') . '</a>';
			$t .= '</li>';
			$t .= '<li class="nav-item">';
				$t .= '<a class="nav-link' . ($addressDisabled?' disabled':'') . ($activeTab=='address'?' active':'') . '" href="' . $addressURL . '"' . ($addressDisabled?' tabindex="-1" aria-disabled="true"':'') . '>';
					$t .= Lang::getLang('address');
				$t .= '</a>';
			$t .= '</li>';
			$t .= '<li class="nav-item">';
				$t .= '<a class="nav-link' . ($filesDisabled?' disabled':'') . ($activeTab=='files'?' active':'') . '" href="' . $filesURL . '"' . ($filesDisabled?' tabindex="-1" aria-disabled="true"':'') . '>';
					$t .= Lang::getLang('files');
				$t .= '</a>';
			$t .= '</li>';
		$t .= '</ul>';
		
		return $t;
		
	}
	
}

?>