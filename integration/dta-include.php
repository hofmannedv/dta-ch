<?php

// list of transactions
$dtaTransactionList = Array(
	"826"=>"TA 826 (ISR payments)",
	"827"=>"TA 827 (domestic CHF payments - bank and postal account payments and postal orders)",
	"830"=>"TA 830 (payments to financial institutions abroad in CHF and FX, domestic FX payments)",
	"832"=>"TA 832 (bank cheques in CHF and FX)",
	"836"=>"TA 836 (payments with an IBAN in Switzerland and abroad, in all currencies)",
	"837"=>"TA 837 (payments to financial institutions in Switzerland and abroad, in all currencies)",
	"890"=>"TA 890 (total record)"
);

// data format

$dtaDataFormatList = Array(
	"fixed"=>"fixed format (diskettes)",
	"variable"=>"variable format (magnetic tape)"
);

// data list format
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

	$action = $_POST["action"];
	$actionList = Array("new", "selectagain", "selectnext", "validate", "selectsimilar", "edit", "createdta", "export", "prepare");
	if (in_array($action, $actionList)) {
		return $action;
	} else {
		return False;
	}
}

function comingFrom() {
	// evaluate action parameter -- the page we come from
	$comeFrom = $_POST["comeFrom"];
	$comeFromList = Array("dta", "formular", "validate", "export", "prepare");
	if (in_array($comeFrom, $comeFromList)) {
		return $comeFrom;
	} else {
		return False;
	}
}

function preSelect() {
	// evaluate preselect variable -- the value to be pre-selected
	$preSelect = $_POST["preSelect"];
	return $preSelect;
}

function getDataFormat() {
	// evaluate dataformat variable
	$dataFormat = $_POST["dataFormat"];
	if (in_array($dataFormat, Array("fixed", "variable"))) {
		return $dataFormat;
	} else {
		return False;
	}
}

function getDataListFormat() {
	// evaluate data list format variable
	$dataListFormat = $_POST["dataListFormat"];
	if (in_array($dataListFormat, Array("csv", "array"))) {
		return $dataListFormat;
	} else {
		return False;
	}
}

function getTransactions() {
	// evaluate transactions file variable
	$transactions = $_POST["transactions"];
	if (count($transactions) > 0){
		$textArray = explode("\n", $transactions);
		$processedArray = array();
		foreach ($textArray as $key => $entry) {
			$entry2 = trim($entry);
			if (strlen($entry2)) {
				$processedArray[] = $entry2;
			}
		}
		if (count($processedArray) > 0) {
			return $processedArray;
		} else {
			// transmitted data field does not contain data, but blanks
			return False;
		}
		return $textArray;
	}

	// no input data, or variable does not exist
	return False;
}

function getTransactionType() {
	// evaluate transaction type variable
	$transactionType = $_POST["transactionType"];
	if (in_array($transactionType, Array(826, 827, 830, 832, 836, 837, 890))) {
		return $transactionType;
	} else {
		return False;
	}
}

function getAdjust() {
	// evaluate adjustment value to validate a transmitted data

	$adjust = $_POST["adjust"];
	if (in_array($adjust, Array("true", "false"))) {
		return $adjust;
	} else {
		return False;
	}
}

function getTransferredDtaList() {
	// evaluate transmitted dta variables
	$dtaList = $_POST["dta"];
	$uList = array();
	foreach ($dtaList as $dtaKey => $dtaValue) {
		$uList[$dtaKey] = unserialize($dtaValue);
	}

	return $uList;
}
?>

