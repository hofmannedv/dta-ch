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

	// sender details
	// - name of sender incl. address
	// - bank clearing number (sender)
	// - iban account number (sender)
	// - sender identification
	// - transaction number

	// receiver details
	// - name of receiver incl. address
	// - bank clearing number (receiver)
	// - bank address (receiver)
	// - iban account number (receiver)
	// - reason for payment (purpose)

	// transaction details
	// - date of transaction (payment value date)
	// - value
	// - currency
	// - convertion rate
	// - data file sender identification
	// - rules for charges

	// define transaction data for TA 836
	$transaction = "";

	// transaction type: 836
	$transaction .= "836;";

	// requested processing date: empty
	$transaction .= ";";

	// bank clearing number beneficiary (receiver): empty
	$transaction .= ";";

	// output sequence number (processed by the bank): 00000
	$transaction .= "00000";

	// data file creation date: set to today
	$transaction .= date("ymd") . ";";

	// bank clearing number order (sender)
	$transaction .= $senderDetails["bankClearingNumber"] . ";";

	// data file sender identification
	$transaction .= $transactionDetails["dataFileSenderIdentification"] . ";";

	// entry sequence number: empty
	$transaction .= ";";

	// transaction payment type: regular payment = 0
	$transaction .= "0;";

	// transaction processing flag: 0
	$transaction .= "0;";

	// ordering party identification (sender id)
	$transaction .= $senderDetails["senderIdentification"] . ";";

	// ordering party transaction number
	$transaction .= $senderDetails["transactionNumber"] . ";";

	// account to be debited (iban of sender)
	$transaction .= $senderDetails["iban"] . ";";

	// payment value date: has to be empty
	$transaction .= ";";

	// ISO currency code
	$transaction .= $transactionDetails["currency"] . ";";

	// payment amount
	$transaction .= $transactionDetails["paymentAmount"] . ";";

	// convertion rate
	$transaction .= $transactionDetails["convertionRate"] . ";";

	// ordering party: 3 lines
	$transaction .= $senderDetails["orderingPartyLine1"] . ";";
	$transaction .= $senderDetails["orderingPartyLine2"] . ";";
	$transaction .= $senderDetails["orderingPartyLine3"] . ";";

	// identification bank address (receiver): D
	$transaction .= "D;";

	// bank clearing number (receiver)
	$transaction .= $receiverDetails["bankClearingNumber"] . ";";

	// bank address (receiver)
	$transaction .= $receiverDetails["bankAddress"] . ";";

	// iban (receiver)
	$transaction .= $receiverDetails["iban"] . ";";

	// name and address (receiver)
	$transaction .= $receiverDetails["nameAndAddressLine1"] . ";";
	$transaction .= $receiverDetails["nameAndAddressLine2"] . ";";
	$transaction .= $receiverDetails["nameAndAddressLine3"] . ";";

	// transaction purpose as unstructured content
	$transaction .= "U;";

	// transaction purpose
	$transaction .= $receiverDetails["reasonForPaymentLine1"] . ";";
	$transaction .= $receiverDetails["reasonForPaymentLine2"] . ";";
	$transaction .= $receiverDetails["reasonForPaymentLine3"] . ";";

	// rules for charges
	$transaction .= $transactionDetails["rulesForCharges"];

	// return transaction data
	return $transaction;
}

// define transaction data
$senderDetails = array(
	"orderingPartyLine1" 			=> "Holger Fischer",
	"orderingPartyLine2" 			=> "Haus 4",
	"orderingPartyLine3" 			=> "1000 Basel",
	"bankClearingNumber" 			=> "00762",
	"iban" 							=> "CH930076201162123456",
	"senderIdentification" 			=> "ABC01",
	"transactionNumber" 			=> "fh-1234"
);

$receiverDetails = array(
	"nameAndAddressLine1" 			=> "Tom Foster",
	"nameAndAddressLine2" 			=> "45 House Road",
	"nameAndAddressLine3" 			=> "12345 San Diego",
	"bankClearingNumber" 			=> "12345",
	"bankAddress" 					=> "BCGE Geneve",
	"iban" 							=> "CH1234567890",
	"reasonForPaymentLine1" 		=> "order 12345",
	"reasonForPaymentLine2" 		=> "contract 67890",
	"reasonForPaymentLine3" 		=> "part 2"
);

$transactionDetails = array(
	"dataFileSenderIdentification" 	=> "ABC12",
	"currency" 						=> "CHF",
	"paymentAmount" 				=> "123,45",
	"convertionRate" 				=> "",
	"rulesForCharges" 				=> "CHG/OUR"
);

// define transaction list
$transactionData = array();

// - entry one
//$transactionData[] = "836;131223;;00000;131222;Geldhaus 23;ABC12;;1;6;ABC01;12345678901;67890;;CHF;123,45;;Holger F;Haus 4;1000 Basel;D;;;CH1234567890;Tom Foster;45 House Road;12345 San Diego;U;pro;Monat;und Jahr;CHG/OUR";

$transactionData[] = ta836($senderDetails, $receiverDetails, $transactionDetails);

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
