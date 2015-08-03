<?php

/*
-----------------------------------------------------------
DTA-CH include file that is useful for forms
contains static variables, transaction list descriptions

(C) 2013-15 Frank Hofmann, Berlin, Germany
Released under GNU Public License (GPL)
email frank.hofmann@efho.de
-----------------------------------------------------------
*/

// list of transactions according to the SIX standard
$dtaTransactionList = Array(
	"826"=>"TA 826 (ISR payments)",
	"827"=>"TA 827 (domestic CHF payments - bank and postal account payments and postal orders)",
	"830"=>"TA 830 (payments to financial institutions abroad in CHF and FX, domestic FX payments)",
	"832"=>"TA 832 (bank cheques in CHF and FX)",
	"836"=>"TA 836 (payments with an IBAN in Switzerland and abroad, in all currencies)",
	"837"=>"TA 837 (payments to financial institutions in Switzerland and abroad, in all currencies)",
	"890"=>"TA 890 (total record)"
);

// data format for the output file
$dtaDataFormatList = Array(
	"fixed"=>"fixed format (diskettes)",
	"variable"=>"variable format (magnetic tape)"
);

// data list format for the output file
// currently: csv, only
$dtaDataListFormatList = Array(
	//"array"=>"php array",
	"csv"=>"comma-separated list (csv)"
);

// input field length table
// $inputFieldLength = Array(
//	"20"  => Array("fixed" => 16, "variable" => 16),
//	"25"  => Array("fixed" => 24, "variable" => 24),
//	"58"  => Array("fixed" => 34, "variable" => 34),
//	"72s" => Array("fixed" => 35, "variable" => 35),
//	"72u" => Array("fixed" => 30, "variable" => 35)
//);

// input field number table
// $inputFieldNumber = Array(
//	"20"  => Array("fixed" => 1, "variable" => 1),
//	"24"  => Array("fixed" => 1, "variable" => 1),
//	"58"  => Array("fixed" => 1, "variable" => 1),
//	"72s" => Array("fixed" => 3, "variable" => 6),
//	"72u" => Array("fixed" => 4, "variable" => 6)
//);

function getAction() {
	// evaluate action parameters exchanged between different 
	// pages to display call state

	// read POST variables stored in variable $action
	$action = $_POST["action"];

	// define a list of valid action terms
	$actionList = Array("new", "selectagain", "selectnext", "validate", "selectsimilar", "edit", "createdta", "export", "prepare");

	// check whether the transmitted action is valid
	if (in_array($action, $actionList)) {
		return $action;
	} else {
		// return False as an error code
		return False;
	}
}

function comingFrom() {
	// evaluate action parameter -- the page we come from

	// read POST variables stored in variable $comeFrom
	$comeFrom = $_POST["comeFrom"];

	// define a list of valid action terms
	$comeFromList = Array("dta", "formular", "validate", "export", "prepare");

	// check whether the transmitted action is valid
	if (in_array($comeFrom, $comeFromList)) {
		return $comeFrom;
	} else {
		// return False as an error code
		return False;
	}
}

function preSelect() {
	// evaluate preselect variable -- the value to be pre-selected

	// read POST variables stored in variable $preSelect
	$preSelect = $_POST["preSelect"];
	return $preSelect;
}

function getDataFormat() {
	// evaluate dataformat variable

	// read POST variables stored in variable $dataFormat
	$dataFormat = $_POST["dataFormat"];

	// check whether the transmitted data format is valid
	if (in_array($dataFormat, Array("fixed", "variable"))) {
		return $dataFormat;
	} else {
		// return False as an error code
		return False;
	}
}

function getDataListFormat() {
	// evaluate data list format variable

	// read POST variables stored in variable $dataListFormat
	$dataListFormat = $_POST["dataListFormat"];

	// check whether the transmitted data list format format is valid
	if (in_array($dataListFormat, Array("csv", "array"))) {
		return $dataListFormat;
	} else {
		// return False as an error code
		return False;
	}
}

function getTransactions() {
	// evaluate transactions file variable

	// read POST variables stored in variable $transactions
	$transactions = $_POST["transactions"];

	// see how many transactions are planned to be evaluated
	if (count($transactions) > 0){
		// split the entries by linebreak
		$textArray = explode("\n", $transactions);

		// define an array of processed data
		$processedArray = array();

		// go through it item by item
		foreach ($textArray as $key => $entry) {

			// remove both the leading and trailing spaces
			$entry2 = trim($entry);

			if (strlen($entry2)) {
				// add the task to the list if non-empty
				$processedArray[] = $entry2;
			}
		}

		// count the number of prepared data
		if (count($processedArray) > 0) {
			return $processedArray;
		} else {
			// transmitted data field does not contain data, but blanks
			// return False as an error code
			return False;
		}
		return $textArray;
	}

	// no input data, or variable does not exist
	// return False as an error code
	return False;
}

function getTransactionType() {
	// evaluate the type of transaction

	// read POST variables stored in variable $transactionType
	$transactionType = $_POST["transactionType"];

	// if the type of transaction is in the list of possible transactions
	if (in_array($transactionType, Array(826, 827, 830, 832, 836, 837, 890))) {
		return $transactionType;
	} else {
		// return False as an error code
		return False;
	}
}

function getAdjust() {
	// evaluate adjustment value to validate a transmitted data

	// read POST variables stored in variable $adjust
	$adjust = $_POST["adjust"];

	// evaluate the adjustment type
	if (in_array($adjust, Array("true", "false"))) {
		return $adjust;
	} else {
		// return False as an error code
		return False;
	}
}

function getTransferredDtaList() {
	// evaluate transmitted dta variables

	// read POST variables stored in variable $dta
	$dtaList = $_POST["dta"];

	// define list for unserialized data
	$uList = array();

	// go through the list item by item
	foreach ($dtaList as $dtaKey => $dtaValue) {
		$uList[$dtaKey] = unserialize($dtaValue);
	}

	return $uList;
}
?>

