<html>
	<head>Display DTA-CH record for transaction</head>
<body>
	<h2 style="color:white;background:blue">&nbsp;Display DTA-CH record for transaction</h2>

	<h3>List of prepared transactions</h3>

	<?php

	// include dta-ch object class for payments
	require_once 'dta-ch.php';

	// include basic variables
	require_once 'dta-include.php';

	// validate action parameters
	$action = getAction();
	if ($action == "prepare"){
		$comingFrom = comingFrom();
		$dfId = getDataFormat();

		$adjust = getAdjust();
		$transactionList = getTransferredDtaList();
		$totalValue = 0.0;

		echo "Number of transactions to be processed: " . count($transactionList) . "<br>\n";
		echo "<br>\n";

		foreach ($transactionList as $dta) {

			// initialize variables
			$from = "";
			$bankIdSender = "";
			$accountSender = "";
			$to = "";
			$bankIdReceiver = "";
			$accountReceiver = "";
			$orderingInformation = "";
			$orderingValue = "";
			$orderingDate = "";
			$dataFileSenderIdentification = "";

			// transaction type
			$taId = $dta->getTransactionType();

			// get dataFileSenderIdentification -- needed for later
			$dataFileSenderIdentification = $dta->getDataFileSenderIdentification();

			// ordering party (sender)
			$from = $dta->getTextFieldValue("orderingPartyLine1") . ", " . $dta->getTextFieldValue("orderingPartyLine2") . ", " . $dta->getTextFieldValue("orderingPartyLine3");
			if (in_array($taId, array(826,827,830,832,837))) {
				$from .= ", " .  $dta->getTextFieldValue("orderingPartyLine4");
			}

			// bank (sender)
			$bankIdSender = $dta->getBankClearingNumberSender();

			// account (sender)
			$accountSender = $dta->getTextFieldValue("accountWithIban") . $dta->getTextFieldvalue("accountWithoutIban");

			// beneficiary (receiver)
			$to = $dta->getTextFieldValue("beneficiaryPartyLine1") . ", " . $dta->getTextFieldValue("beneficiaryPartyLine2") . ", " . $dta->getTextFieldValue("beneficiaryPartyLine3") ;
			if (in_array($taId, array(826,827,830,832,837))) {
				$to .= ", " .  $dta->getTextFieldValue("beneficiaryPartyLine4");
			}
			
			// bank (receiver)
			$bankIdReceiver = "";
			if ($taId == 827) {
				$bankIdReceiver = $dta->getBankClearingNumberReceiver();
			} 

			if ($taId == 830) {
				$bankIdReceiver = $dta->getTextFieldValue("beneficiaryInstituteLine1");
			}

			// account (receiver)
			if ($taId == 826) {
				$accountReceiver = $dta->getTextFieldValue("beneficiaryPartyIdentification");
			}

			if (in_array($taId, array(827,830,832,837))) {
				$accountReceiver = $dta->getTextFieldValue("beneficiaryPartyAccount");
			}

			if ($taId == 836) {
				$accountReceiver = $dta->getTextFieldValue("beneficiaryInstituteLine1") . ", " . $dta->getTextFieldValue("beneficiaryInstituteLine2") . ", " . $dta->getTextFieldValue("iban");
			}

			// ordering information
			$orderingInformation = $dta->getTextFieldValue("orderingPartyIdentification") . " - " . $dta->getTextFieldValue("orderingPartyTransactionNumber");

			// ordering value
			$orderingValue = $dta->getTextFieldValue("paymentCurrencyCode") . " " . $dta->getTextFieldValue("paymentAmount");

			// order date
			$orderingDate = $dta->getTextFieldValue("paymentValueDate");

			echo "<table>\n";
			echo "<tr>\n";
			echo "<td bgcolor=\"silver\">Sender</td>\n<td>$from</td>\n";
			echo "</tr>\n";
			echo "<tr>\n";
			echo "<td bgcolor=\"silver\">Sender's Bank ID</td>\n<td>$bankIdSender</td>\n";
			echo "</tr>\n";
			echo "<tr>\n";
			echo "<td bgcolor=\"silver\">Sender's Account</td>\n<td>$accountSender</td>\n";
			echo "</tr>\n";
			echo "<tr>\n";
			echo "<td bgcolor=\"silver\">Receiver</td>\n<td>$to</td>\n";
			echo "</tr>\n";
			echo "<tr>\n";
			echo "<td bgcolor=\"silver\">Receiver's Bank ID</td>\n<td>$bankIdReceiver</td>\n";
			echo "</tr>\n";
			echo "<tr>\n";
			echo "<td bgcolor=\"silver\">Receiver's Account</td>\n<td>$accountReceiver</td>\n";
			echo "</tr>\n";
			echo "<tr>\n";
			echo "<td bgcolor=\"silver\">Ordering information</td>\n<td>$orderingInformation</td>\n";
			echo "</tr>\n";
			echo "<tr>\n";
			echo "<td bgcolor=\"silver\">Ordering value (date)</td>\n<td>$orderingValue ($orderingDate)</td>\n";
			echo "</tr>\n";
			echo "</table>\n";
			echo "<br>\n";

			// payment amount
			$paymentAmount = $dta->getTextFieldValue("paymentAmount");
			$paymentAmount = preg_replace('/,/' , '.', $paymentAmount);
			$totalValue += $paymentAmount;
		}

		// sort transaction list
		// - by requested processing date, first
		$dtaList = $transactionList;
		$dateList = array();
		foreach ($transactionList as $dta) {
			$dateList[] = $dta->getRequestedProcessingDate();
		}
		array_multisort($dateList, $dtaList);
		//var_dump($dateList);
		$transactionList = $dtaList;
		
		// - by ordering party identification, second, per processing date
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
		$transactionList = $newTransactionList;

		//foreach ($transactionList as $dta) {
		//	echo $dta->getRequestedProcessingDate() . ": " . $dta->getTransactionType() . ": " . $dta->getTextFieldValue("orderingPartyIdentification") . ": " . $dta->getBankClearingNumberReceiver() . "<br>\n";
		//}

		// display total
		echo "<h3>Total</h3>\n";

		echo "total: $totalValue<br>\n";

		// creating total record TA 890 to complete the transaction
		// - create dtach object
		//   initialize a new dta-ch object
		$dta = new DTACH();

		// - fill object with data
		//   set data format
		$dta->setDataFormat($dfId);

		// - bank clearing number
		//   left empty

		// - data file sender identification
		//   remember value saved earlier
		$dta->setDataFileSenderIdentification($dataFileSenderIdentification);

		// - transaction type = 890
		$dta->setTransactionType(890);

		// total value
		$paymentAmount = "$totalValue";
		$paymentAmount = preg_replace('/\./', ',', $paymentAmount);
		$dta->addTextField("total", $paymentAmount);

		// adjust and validate new dta entry
		$dta->adjustHeader();
		$dta->adjustDataFields();
		$dta->validateHeader();
		$dta->validateDataFields();

		// add to transactionList
		$transactionList[] = $dta;

		// adjust numbering of the dta records
		$nr = 1;
		foreach ($transactionList as $key => $dta) {
			$number = strval($nr);
			$number = str_pad($number, 5, "0", STR_PAD_LEFT);
			$dta->setEntrySequenceNumber($number);
			$transactionList[$key] = $dta;
			$nr++;
		}

		// transform each dta object into transaction data
		// prepare output, simultaniously
		$fileContent = "";
		foreach ($transactionList as $dta) {
			$dta->outputFullRecord();
			$fileContent .= $dta->getFullRecord();
		}

		// create download link for the dta file
		echo "<h3>Download dta record as plain text file</h3>\n";

		// prepare 
		$stringLength = strlen($fileContent);
		$i = 2;
		$partLength = 3;
		$newFileContent = "";
		while ($i <= $stringLength) {
			$partialString = substr($fileContent,$i-2,$partLength);
			$asciiValue = base_convert(trim($partialString), 16, 10);
			$newFileContent .= chr($asciiValue);
			$i += $partLength;
		}

		// - create temporary file
		$dtaFileName = "dta-" . date("dmyhis");
		$fileHandle = fopen($dtaFileName, "w");
		if ($fileHandle) {
			$retVal = fputs($fileHandle, $newFileContent);
			$retVal = fclose($fileHandle);
			// - create link
			echo "<a href=\"$dtaFileName\">dta record as plain text file</a>\n";
		} else {
			echo "cannot open file: $dtaFileName<br>\n";
		}

	}
			
	?>
	<hr>
</body>
</html>
