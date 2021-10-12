<?php

final class CustomerPDF {

	private $doc;
	private $fileObject;
	private $fileObjectID;

	public function __construct($loc, $input) {

		$this->doc = 'Customer PDF';
		$this->fileObject = 'Customer';
		$this->fileObjectID = null;

	}

	public function doc() {

		return $this->doc;

	}

	public function getFileObject() {

		return $this->fileObject;

	}

	public function getFileObjectID() {

		return $this->fileObjectID;

	}

}

?>