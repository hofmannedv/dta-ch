<?php

/*
-----------------------------------------------------------
DTA-CH example for processing TA 836

(C) 2015 Frank Hofmann, Berlin, Germany
Released under GNU Public License (GPL)
email frank.hofmann@efho.de
-----------------------------------------------------------
*/

// include dta-ch object processing class for payments
require_once 'dta-ch-processing.php';

function ta836($senderDetails, $receiverDetails, $transactionDetails) {
	// create valid record for TA 836
	// use specific default settings

	// define transaction data for TA 836
	$transactionData = "";

	// read/extract sender details
	// - name of sender incl. address
	// - bank clearing number (sender)
	// - iban account number (sender)
	// - sender identification
	// - transaction number

	// read/extract receiver details
	// - name of receiver incl. address
	// - bank clearing number (receiver)
	// - bank address (receiver)
	// - iban account number (receiver)
	// - reason for payment

	// read/extract transaction details
	// - date of transaction (payment value date)
	// - value
	// - currency
	// - exchange rate

	// return transaction data
	return $transactionData;
}

// define transaction list
$transactionData = array();

// - entry one
$transactionData[] = "836;131223;;00000;131222;Geldhaus 23;ABC12;;1;6;ABC01;12345678901;67890;;CHF;123,45;;Holger F;Haus 4;1000 Basel;D;;;CH1234567890;Tom Foster;45 House Road;12345 San Diego;U;pro;Monat;und Jahr;CHG/OUR";

// define output data format: set to fixed
$outputDataFormat = "fixed";

// define delivery date of the transactions: set to today
$transactionDeliveryDate = date("ymd");

// enable adjustments and corrections of the transmitted data
$skipAdjustments = "no";

// create dta-ch-processing object
// initialize a new dta-ch-processing object
$dtaProcessing = new DTACHProcessing($transactionData, $transactionDeliveryDate, $skipAdjustments);

// process the transaction list
$transactionList = $dtaProcessing->processMultipleTransactions ();

// calculate total for all transactions
$dtaTotal = $dtaProcessing->calculateTotal ($transactionList);

// sort transaction list according to the SIX specification
$sortedTransactionList = $dtaProcessing->sortTransactions ($transactionList);

// create TA 890 record holding the total for all transactions
// - retrieve the data file sender identification from the first transaction
$dataFileSenderIdentification = $transactionList[0]->getDataFileSenderIdentification();

// - create the TA 890 record
$ta890 = $dtaProcessing->createTA890 ($dataFileSenderIdentification, $totalValue);

// add the TA 890 record to the transactionList
$sortedTransactionList[] = $ta890;

// adjust numbering of the single dta records
$sortedTransactionList = $dtaProcessing->numberTransactions();

// export dta list to plain text file
$fileContent = $dtaProcessing->exportDtaToPlaintext ($sortedTransactionList);

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
