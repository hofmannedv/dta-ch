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

function processMultipleTransactions ($transactionData) {
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

function calculateTotal ($dtaList) {
	// investigate the total of the transactions

	// assume a total value of 0.0
	$totalValue = 0.0;

	// go through the list of transactions one by one
	foreach ($dtaList as $dta) {
		// retrieve the payment amount of the according dta
		$paymentAmount = $dta->getTextFieldValue("paymentAmount");

		// substitute "," by "."
		$paymentAmount = preg_replace('/,/' , '.', $paymentAmount);

		// calculate the total
		$totalValue += $paymentAmount;
	}

	// return the total value
	return $totalValue;
}

function sortTransactions ($dtaList) {
	// sort transaction list

	// step 1: sort by requested processing date
	
	// define an array of dates based on the processing date of a transaction
	$dateList = array();
	// go through the list of transactions one after the other
	foreach ($dtaList as $dta) {
		$dateList[] = $dta->getRequestedProcessingDate();
	}

	// sort the dateList first, and dtaList according to the order 
	// of dateList, afterwards
	array_multisort($dateList, $dtaList);

	//var_dump($dateList);


	$transactionList = $dtaList;
		
	// step 2: sort by ordering party identification per processing date
	$dateSegments = array_unique($dateList);
	// var_dump($dateSegments);
	$piList = array();
	foreach ($transactionList as $dta) {
		foreach ($dateSegments as $dtaDate) {
			if ($dta->getRequestedProcessingDate() == $dtaDate) {
				$piList[$dtaDate][] = $dta;
			}
		}
	}

	// ... now we have a list piList(date) = dta_1...dta_n
	$newTransactionList = array();
	foreach ($piList as $dtaList) {
		$piSpecific = array();
		foreach ($dtaList as $dta) {
			$piSpecific[] = $dta->getTextFieldValue("orderingPartyIdentification");
		}
		array_multisort($piSpecific, $dtaList);

		// ... sort by bank clearing number of the beneficiary bank, third
		$piSegments = array_unique($piSpecific);
		$clearingList = array();
		foreach ($dtaList as $dta) {
			foreach ($piSegments as $pi){
				if ($dta->getTextFieldValue("orderingPartyIdentification") == $pi) {
					$clearingList[$pi][] = $dta;
				}
			}
		}
	
		// ... now we have a list clearingList(pi) = dta_1...dta_n
		$clearingSpecific = array();
		foreach ($dtaList as $dta) {
			$clearingSpecific[] = $dta->getBankClearingNumberReceiver();
		}
		array_multisort($clearingSpecific, $dtaList);

		$newTransactionList = array_merge($newTransactionList, $dtaList);
	}

	// ... now we have a sorted list by processing date, and by ordering party identification
	return $newTransactionList;
}

function createTA890 ($transactionList) {
	return;
}

// define transaction list
$transactionData = array();

// - entry one
$transactionData[] = "826;131220;;12345;131220;Bank 44;ABC12;;1;6;ABC01;12345678901;56789;;CHF;123,45;Felix Z;Markt 1;1234 Zürich;;/C/123456789;Hans Wurst;Taufgraben 1;1234 Bern;;1234567890123456;34";

// - entry two
$transactionData[] = "827;131220;1234;12345;131220;Bank 44;ABC12;;1;6;ABC01;12345678901;56789;;CHF;123,45;Felix Z;Markt 1;1234 Zürich;;/C/123456789          ;Hans Wurst;Taufgraben 1;1234 Bern;;bankPayment;Das;ist;ein;Test;/C/456789;Holger Klein;Vor dem Tor 1;4132 Muttenz;";

// process the transaction list
$dtaList = processMultipleTransactions ($transactionData);

// calculate total for all transactions
$dtaTotal = calculateTotal ($dtaList);

// sort transaction list according to the SIX specification
$sortedDtaList = sortTransactions ($dtaList);

// create TA 890 record holding the total
$ta890 = createTA890 ($sortedDtaList);

// --------------------------------------------

?>
