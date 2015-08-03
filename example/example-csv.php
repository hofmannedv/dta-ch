<?php

/*
-----------------------------------------------------------
DTA-CH example reading CSV data

(C) 2015 Frank Hofmann, Berlin, Germany
Released under GNU Public License (GPL)
email frank.hofmann@efho.de
-----------------------------------------------------------
*/

// include dta-ch object class for payments
require_once 'dta-ch.php';

function processSingleTransaction ($transactionData) {
	// process a single transaction

	// create dta-ch object
	// initialize a new dta-ch object
	$dta = new DTACH();

	// fill object with data
	// - set data format: fixed
	$dtaDataFormat = "fixed";
	$dta->setDataFormat($dtaDataFormat);

	// - set date of delivery: today
	$dateOfDelivery = date("ymd");
	$dta->setDateOfDelivery($dateOfDelivery);

	// - import transaction data as csv data
	$importValue = $dta->importCsv($transactionData);

	// - auto-adjust data: header, and data fields
	$dta->adjustHeader();
	$dta->adjustDataFields();

	// - validate data: header, and data fields
	$dta->validateHeader();
	$dta->validateDataFields();

	return $dta;
}

function processMultipleTransaction ($transactionData) {
	// process multiple transactions

	// define result list
	$dtaList = array();

	// go through the list of transactions one by one
	foreach ($transactionData as $singleTransaction) {
		$dta = processSingleTransaction ($singleTransaction);

		// add new dta to dta list
		$dtaList[] = $dta;

	// return list of transactions
	return $dtaList;
}

?>
