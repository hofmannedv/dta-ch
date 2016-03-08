<?php
/*
-----------------------------------------------------------
DTA-CH support class to process single transactions
Requires lib-dta-ch.

(C) 2015-16 Frank Hofmann, Berlin, Germany
Released under GNU Public License (GPL)
email frank.hofmann@efho.de
-----------------------------------------------------------
*/

// include dta-ch object class for payments
require_once 'dta-ch.php';

class DTACHProcessing {
	function __construct($transactions, $outputDataFormat, $transactionDeliveryDate, $skipAdjustments) {

		// define data format
		// - assume fixed (default value)
		$this->dataFormat = "fixed";

		// - check for either fixed, or variable
		if(in_array($outputDataFormat, Array("fixed", "variable"))) {
			// set data format, accordingly
			$this->dataFormat = $outputDataFormat;
		}

		// define date of delivery
		// - set the default date of delivery: today
		$this->dateOfDelivery = date("ymd");

		if($transactionDeliveryDate) {
			// validate the given date
			// define regex date pattern
			$datePattern = '/^\d{2}((0[1-9])|(1[0-2]))((0[1-9])|([1,2]\d)|(3[0,1]))$/';

			if (preg_match($datePattern, $transactionDeliveryDate) == True) {
				$this->dateOfDelivery = $transactionDeliveryDate;
			}
		}

		// store the transactions to be processed
		// we expect an array of strings
		if(is_array($transactions)) {
			$this->transactionList = $transactions;
		} else {
			// handle the given variable as an array
			$this->transactionList = Array("$transactions");
		}

		// auto-correct the values (do not skip)
		// - set the default value to: yes
		$this->adjustData = "yes";

		// - check for either yes, or no
		if(in_array($skipAdjustments, Array("no", "yes"))) {
			// set adjust data, accordingly
			$this->adjustData = $skipAdjustments;
		}

		return;
	}

	function processSingleTransaction ($transactionData) {
		// process a single transaction

		// create dta-ch object
		// initialize a new dta-ch object
		$dta = new DTACH();

		// fill object with data format
		$dataFormat = $this->dataFormat;
		$dta->setDataFormat($dataFormat);

		// set date of delivery
		$dateOfDelivery = $this->dateOfDelivery;
		$dta->setDateOfDelivery($dateOfDelivery);

		// import transaction data as csv data
		$importValue = $dta->importCsv($transactionData);

		// check for import error, and return with error code
		if ($importValue == False) {
			return False;
		}

		// auto-adjust data: header, and data fields
		// skip if disabled
		if ($this->adjustData == "yes") {
			$dta->adjustHeader();
			$dta->adjustDataFields();
		}

		// validate data: header, and data fields
		$dta->validateHeader();
		$dta->validateDataFields();

		// return the prepared transaction object
		return $dta;
	}

	function processMultipleTransactions () {
		// process multiple transactions

		// define result list
		$dtaList = array();

		// retrieve transaction data
		$transactionData = $this->transactionList;

		// go through the list of transactions one by one
		foreach ($transactionData as $singleTransaction) {
			$dta = $this->processSingleTransaction ($singleTransaction);

			// add new dta to dta list
			$dtaList[] = $dta;
		}

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

	function sortTransactionsByDate ($dtaList) {
		// the transactions by the requested processing date

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

		// return the sorted list
		return $dateList;

	}

	function identifyUniqueDateSegments ($dateList) {
		// identify unique date segments in the transaction list
		$dateSegments = array_unique($dateList);

		// output interim result if needed
		// var_dump($dateSegments);

		return $dateSegments;
	}

	function sortTransactions ($dtaList) {
		// sort transaction list

		// - step 1: sort by requested processing date -----------------------
	
		$dateList = sortTransactionsByDate ($dtaList);

		// - step 2: identify unique date segments ---------------------------

		$dateSegments = identifyUniqueDateSegments ($dateList);

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
					break;
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
						break;
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

	function createTA890 ($dataFileSenderIdentification, $totalValue) {
		// creating total record TA 890 to complete the transaction

		// create dtach object, and initialize it
		$dta = new DTACH();

		// fill object with data
		// - set data format
		$dataFormat = $this->dataFormat;
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

		// auto-adjust data: header, and data fields
		// skip if disabled
		if ($this->adjustData == "yes") {
			$dta->adjustHeader();
			$dta->adjustDataFields();
		}

		// validate new dta entry
		$dta->validateHeader();
		$dta->validateDataFields();

		// return TA 890
		return $dta;
	}

	function numberTransactions ($sortedDtaList) {
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

		return $sortedDtaList;

	}

	function exportDtaToPlaintext ($sortedDtaList) {
		// export dta list to plain text file
		// transform each dta object into transaction data
		// prepare output, simultaniously
	
		// start with an empty file content
		$fileContent = "";

		// go through the list of sorted dta entries one by one
		foreach ($sortedDtaList as $dta) {
			// prepare the transaction string
			$dta->outputFullRecord();

			// extend the file content by the new ascii data
			$fileContent .= $dta->getFullAsciiRecord();
		}

		// return transaction data as plain text
		return $fileContent;
	}
}
?>
