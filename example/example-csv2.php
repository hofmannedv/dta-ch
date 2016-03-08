<?php

/*
-----------------------------------------------------------
DTA-CH example reading CSV data

(C) 2015 Frank Hofmann, Berlin, Germany
Released under GNU Public License (GPL)
email frank.hofmann@efho.de
-----------------------------------------------------------
*/

// include dta-ch object processing class for payments
require_once 'dta-ch-processing.php';

// define transaction list
$transactionData = array();

// - entry one
$transactionData[] = "826;131220;;12345;131220;Bank 44;ABC12;;1;6;ABC01;12345678901;56789;;CHF;123,45;Felix Z;Markt 1;1234 Zürich;;/C/123456789;Hans Wurst;Taufgraben 1;1234 Bern;;1234567890123456;34";

// - entry two
$transactionData[] = "827;131220;1234;12345;131220;Bank 44;ABC12;;1;6;ABC01;12345678901;56789;;CHF;123,45;Felix Z;Markt 1;1234 Zürich;;/C/123456789          ;Hans Wurst;Taufgraben 1;1234 Bern;;bankPayment;Das;ist;ein;Test;/C/456789;Holger Klein;Vor dem Tor 1;4132 Muttenz;";

// define output data format: set to fixed
$outputDataFormat = "fixed";

// define delivery date of the transactions: set to today
$transactionDeliveryDate = date("ymd");

// enable adjustments and corrections of the transmitted data
$skipAdjustments = "no";

// create dta-ch-processing object
// initialize a new dta-ch-processing object
$dtaProcessing = new DTACHProcessing($transactionData, $outputDataFormat, $transactionDeliveryDate, $skipAdjustments);

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
