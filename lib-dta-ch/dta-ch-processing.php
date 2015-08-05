<?php
/*
-----------------------------------------------------------
DTA-CH support class to process single transactions
Requires lib-dta-ch.

(C) 2015 Frank Hofmann, Berlin, Germany
Released under GNU Public License (GPL)
email frank.hofmann@efho.de
-----------------------------------------------------------
*/

// include dta-ch object class for payments
require_once 'dta-ch.php';

class DTACHProcessing {
	function __construct($transactions, $outputDataFormat, $transactionDeliveryDate) {

		// define data format
		// - assume fixed (default value)
		$this->dataFormat = "fixed";

		// - check for either fixed, or variable
		if(in_array($outputDataFormat, Array("fixed", "variable"))) {
			// set data format, accordingly
			$this->dataFormat = $outputDataFormat;
		};

		// define date of delivery
		// - set the default date of delivery: today
		$this->dateOfDelivery = date("ymd");

		if($transactionDeliveryDate) {
			// validate the given date
			// define regex date pattern
			$datePattern = '/^\d{2}((0[1-9])|(1[0-2]))((0[1-9])|([1,2]\d)|(3[0,1]))$/';

			if (preg_match($datePattern, $transactiondeliveryDate) == True) {
				$this->dateOfDelivery = $transactionDeliveryDate;
			};
		}

		// store the transactions to be processed
		// we expect an array of strings
		if(is_array($transactions)) {
			$this->$transactionList = $transactions;
		} else {
			// handle the given variable as an array
			$this->$transactionList = Array("$transactions");
		}

		return;
	}

	function processSingleTransaction ($transactionData) {
		// process a single transaction

		// create dta-ch object
		// initialize a new dta-ch object
		$dta = new DTACH();

		// fill object with data format
		$dataFormat = $this->$dataFormat;
		$dta->setDataFormat($dataFormat);

		// set date of delivery
		$dateOfDelivery = $this->dateOfDelivery;
		$dta->setDateOfDelivery($dateOfDelivery);

		// import transaction data as csv data
		$importValue = $dta->importCsv($transactionData);

		// check for import error, and return with error code
		if ($importValue == False) {
			return False;
		};

		// auto-adjust data: header, and data fields
		$dta->adjustHeader();
		$dta->adjustDataFields();

		// validate data: header, and data fields
		$dta->validateHeader();
		$dta->validateDataFields();

		return $dta;
	}

}
?>
