<?php

/*

CREATE TABLE `crestronDB`.`customer_Customer` (
  `customerID` INT(12) NOT NULL AUTO_INCREMENT,
  `siteID` INT(12) NOT NULL,
  `creator` INT(12) NOT NULL,
  `created` DATETIME NOT NULL,
  `updated` DATETIME NOT NULL,
  `deleted` INT(1) NOT NULL,
  `customerNameEnglish` VARCHAR(100) NOT NULL,
  `customerNameJapanese` VARCHAR(100) NOT NULL,
  `customerPersonInCharge` VARCHAR(100) NOT NULL,
  `customerRepresentativeDepartment` VARCHAR(100) NOT NULL,
  `customerRepresentativeTitle` VARCHAR(100) NOT NULL,
  `customerHonorarySuffix` VARCHAR(10) NOT NULL,
  `customerNotes` VARCHAR(255) NOT NULL,
  `customerTelephone` VARCHAR(50) NOT NULL,
  `customerFax` VARCHAR(50) NOT NULL,
  `customerEmail` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`customerID`)
);

*/

final class Customer extends ORM {

	public $customerID; // int(12)
	public $siteID; // int(12)
	public $creator; // int(12)
	public $created; // datetime
	public $updated; // datetime
	public $deleted; // int(1)
	public $customerNameEnglish; // varchar(100)
	public $customerNameJapanese; // varchar(100)
	public $customerPersonInCharge; // varchar(100)
	public $customerRepresentativeDepartment; // varchar(100)
	public $customerRepresentativeTitle; // varchar(100)
	public $customerHonorarySuffix; // varchar(10)
	public $customerNotes; // varchar(255)
	public $customerTelephone; // varchar(50)
	public $customerFax; // varchar(50)
	public $customerEmail; // varchar(50)
	public $priceLevel; // int(2)

	public function __construct($customerID = null) {

		$this->customerID = 0;
		$this->siteID = $_SESSION['siteID'];
		$this->creator = $_SESSION['userID'];
		$this->created = date('Y-m-d H:i:s');
		$this->updated = date('Y-m-d H:i:s');
		$this->deleted = 0;
		$this->customerNameEnglish = '';
		$this->customerNameJapanese = '';
		$this->customerPersonInCharge = '';
		$this->customerRepresentativeDepartment = '';
		$this->customerRepresentativeTitle = '';
		$this->customerHonorarySuffix = '';
		$this->customerNotes = '';
		$this->customerTelephone = '';
		$this->customerFax = '';
		$this->customerEmail = '';
		$this->priceLevel = 1;
		
		if ($customerID) {
		
			$nucleus = Nucleus::getInstance();
			$query = "SELECT * FROM customer_Customer WHERE customerID = :customerID LIMIT 1";
			$statement = $nucleus->database->prepare($query);
			$statement->execute(array(':customerID' => $customerID));
			if ($row = $statement->fetch()) {
				foreach ($row AS $key => $value) { if (isset($this->$key)) { $this->$key = $value; } }
			}

		}
		
	}
	
	public function name() {

	    if ($_SESSION['lang'] == 'ja' && $this->customerNameJapanese != '') { return $this->customerNameJapanese; }
	    else { return $this->customerNameEnglish; }

	}
	
	public function priceLevel() {
		return Lang::getLang('priceLevel') . ' ' . $this->priceLevel;
	}

	public function markAsDeleted() {

		$this->updated = date('Y-m-d H:i:s');
		$this->deleted = 1;
		$conditions = array('customerID' => $this->customerID);
		self::update($this, $conditions, true, false, 'customer_');

	}

	public function describe() {
		return get_object_vars($this);
	}

}

final class CustomerList {
	
	private $customers;
	
	public function __construct($searchString = null, $customerID = null, $priceLevel = null, $limitOffset = 0, $limitCount = 1000) {

		$limitOffset = intval($limitOffset);
		$limitCount = intval($limitCount);
		
		$whereClause = array();
		$whereClause[] = 'siteID = :siteID';
		$whereClause[] = 'deleted = 0';
		if ($priceLevel) { $whereClause[] = 'priceLevel = :priceLevel'; }
		if ($customerID) { $whereClause[] = 'customerID = :customerID'; }
		if ($searchString) {
			$whereClause[] = '(customerNameEnglish LIKE concat("%",:customerNameEnglish,"%") OR customerNameJapanese LIKE concat("%",:customerNameJapanese,"%"))';
		}
		$query = "SELECT customerID FROM customer_Customer WHERE " . implode(' AND ',$whereClause) . " ORDER BY customerNameEnglish ASC LIMIT " . $limitOffset . ", " . $limitCount;
	
		$nucleus = Nucleus::getInstance();
		$statement = $nucleus->database->prepare($query);
		$statement->bindParam(':siteID', $_SESSION['siteID'], PDO::PARAM_INT);
		if ($priceLevel) { $statement->bindParam(':priceLevel', $priceLevel, PDO::PARAM_INT); }
		if ($customerID) { $statement->bindParam(':customerID', $customerID, PDO::PARAM_INT); }
		if ($searchString) {
			$statement->bindParam(':customerNameEnglish', $searchString);
			$statement->bindParam(':customerNameJapanese', $searchString);
		}
		$statement->execute();
		
		$this->customers = array();
		while ($row = $statement->fetch()) { $this->customers[] = $row['customerID']; }
	
	}
	
	public function customers() {
		
		return $this->customers;
		
	}
	
}

final class CustomerCount {
	
	private $count;
	
	public function __construct($searchString = null, $priceLevel = null) {

		$whereClause = array();
		$whereClause[] = 'siteID = :siteID';
		$whereClause[] = 'deleted = 0';
		if ($priceLevel) { $whereClause[] = 'priceLevel = :priceLevel'; }
		if ($searchString) {
			$whereClause[] = '(customerNameEnglish LIKE concat("%",:customerNameEnglish,"%") OR customerNameJapanese LIKE concat("%",:customerNameJapanese,"%"))';
		}

		$query = "SELECT count(customerID) AS customerCount FROM customer_Customer WHERE " . implode(' AND ',$whereClause);
		$nucleus = Nucleus::getInstance();
		$statement = $nucleus->database->prepare($query);
		$statement->bindParam(':siteID', $_SESSION['siteID'], PDO::PARAM_INT);
		if ($priceLevel) { $statement->bindParam(':priceLevel', $priceLevel, PDO::PARAM_INT); }
		if ($searchString) {
			$statement->bindParam(':customerNameEnglish', $searchString);
			$statement->bindParam(':customerNameJapanese', $searchString);
		}
		$statement->execute();
		
		$this->count = 0;
		if ($row = $statement->fetch()) { $this->count = $row['customerCount']; }
	
	}
	
	public function count() {
		return $this->count;
	}
	
}

final class CustomerModalParameters {

	public $customerID;
	public $size;
	public $fieldName;
	public $includeModal;
	public $customerModalButtonAnchor;
	public $modalKey;

	public function __construct() {

		$this->customerID = 0;
		$this->size = null;
		$this->fieldName = 'customerID';
		$this->includeModal = true;
		$this->customerModalButtonAnchor = 'customerModalButtonAnchor';
		$this->modalKey = 'customer_modal';

	}

}

?>