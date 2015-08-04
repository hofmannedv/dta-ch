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

	// - step 1: sort by requested processing date -----------------------
	
	// define an array of dates based on the processing date of a transaction
	$dateList = array();
	// go through the list of transactions one after the other
	foreach ($dtaList as $dta) {
		$dateList[] = $dta->getRequestedProcessingDate();
	}

	// sort the dateList first, and dtaList according to the order 
	// of dateList, afterwards
	array_multisort($dateList, $dtaList);

	// output interim result if needed
	//var_dump($dateList);

	// - step 2: identify unique date segments ---------------------------
	$dateSegments = array_unique($dateList);

	// output interim result if needed
	// var_dump($dateSegments);

	// - step 3: sort by ordering party id per processing date -----------
	$piList = array();
	foreach ($dtaList as $dta) {

		// retrieve the date of transaction
		$currentDate = $dta->getRequestedProcessingDate();

		// find the according date segment
		foreach ($dateSegments as $dtaDate) {
			// if both dates match ...
			if ($currentDate == $dtaDate) {

				// ... extend the list by the current transaction
				$piList[$dtaDate][] = $dta;

				// quit searching, and end the inner foreach loop
				break
			}
		}
	}

	// the result is a list piList[dtaDate] = [dta_1...dta_n]
	// create a list of sorted transactions
	$sortedTransactionList = array();

	// go through the list of transactions per date segment
	foreach ($piList as $dateSegment) {

		// define a list of date-specific identifications of 
		// the ordering party
		$piSpecific = array();

		// go through the entries in the current date segment
		foreach ($dateSegment as $dta) {
			// retrieve the identification of the ordering party
			$dateSpecificIdentifications[] = $dta->getTextFieldValue("orderingPartyIdentification");
		}

		// sort both the identifications of the ordering party, 
		// and the date segment
		array_multisort($dateSpecificIdentifications, $dateSegment);

		// next, sort the entries in the current date segment by
		// the bank clearing number of the beneficiary bank

		// retrieve the unique entries of date-specific 
		// identifications of the ordering party
		$uniqueEntries = array_unique($dateSpecificIdentifications);

		// define a clearing list
		$clearingList = array();

		// go through the entries in the current date segment
		foreach ($dateSegment as $dta) {
			
			// retrieve the identification of the ordering party
			$id = $dta->getTextFieldValue("orderingPartyIdentification");

			// go through the unique entries one by one
			foreach ($uniqueEntries as $entry){

				if ($id == $entry) {
					$clearingList[$entry][] = $dta;

					// quit searching, and end the inner foreach loop
					break
				}
			}
		}
	
		// ... now we have a list clearingList[entry] = [dta_1...dta_n]

		// define an unsorted list
		$clearingSpecific = array();

		// go through the current date segment entry by entry
		foreach ($dateSegment as $dta) {
			// retrieve the bank clearing number of the payment receiver
			$clearingSpecific[] = $dta->getBankClearingNumberReceiver();
		}

		// sort both the bank clearing number of the payment receiver
		// and the date segment
		array_multisort($clearingSpecific, $dateSegment);

		// update the list of sorted transactions
		$sortedTransactionList = array_merge($sortedTransactionList, $dateSegment);
	}

	// ... now we have a sorted list by processing date, and by ordering party identification
	return $sortedTransactionList;
}

function createTA890 ($dataFormat, $dataFileSenderIdentification, $totalValue) {
	// creating total record TA 890 to complete the transaction

	// create dtach object, and initialize it
	$dta = new DTACH();

	// fill object with data
	// - set data format
	$dta->setDataFormat($dataFormat);

	// - bank clearing number
	//   left empty

	// - data file sender identification
	$dta->setDataFileSenderIdentification($dataFileSenderIdentification);

	// - transaction type = 890
	$dta->setTransactionType(890);

	// - validate total value
	$paymentAmount = "$totalValue";
	$paymentAmount = preg_replace('/\./', ',', $paymentAmount);

	// add further text field holding the total value
	$dta->addTextField("total", $paymentAmount);

	// adjust and validate new dta entry
	$dta->adjustHeader();
	$dta->adjustDataFields();
	$dta->validateHeader();
	$dta->validateDataFields();

	return $dta;
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

// create TA 890 record holding the total for all transactions
// - set data format: fixed
$dataFormat = "fixed";
// - retrieve the data file sender identification from the first
//   transaction
$dataFileSenderIdentification = $dtaList[0]->setDataFileSenderIdentification();

// - create the TA 890 record
$ta890 = createTA890 ($dataFormat, $dataFileSenderIdentification, $totalValue);

// add the TA 890 record to the transactionList
$sortedDtaList[] = $ta890;

// adjust numbering of the single dta records
$nr = 1;
foreach ($sortedDtaList as $key => $dta) {
	// convert the integer value into string, and extend it by 
	// leading zeros
	$number = strval($nr);
	$number = str_pad($number, 5, "0", STR_PAD_LEFT);

	// set the sequence number of the current dta entry
	$dta->setEntrySequenceNumber($number);

	// update the transaction value
	$sortedDtaList[$key] = $dta;

	// increase the number by one
	$nr++;
}

// export dta list to plain text file
// transform each dta object into transaction data
// prepare output, simultaniously

// - start with an empty file
$fileContent = "";

// go through the list of sorted dta entries one by one
foreach ($sortedDtaList as $dta) {
	// prepare the transaction string
	$dta->outputFullRecord();

	// extend the file content by the new ascii data
	$fileContent .= $dta->getFullAsciiRecord();
}

// create temporary file
// - define file name
$dtaFileName = "dta-" . date("dmyhis");

// - open file for writing
$fileHandle = fopen($dtaFileName, "w");

// - if file handle: save data, and close the file
if ($fileHandle) {
	$retVal = fputs($fileHandle, $fileContent);
	$retVal = fclose($fileHandle);
}

// --------------------------------------------

?>
