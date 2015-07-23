<html>
	<head>Testing the DTA-CH class</head>
<body>
	<h2 style="color:white;background:blue">&nbsp;Testing the DTA-CH class</h2>

	<?php

	// include dta-ch object class for payments
	require_once 'dta-ch.php';

	// include basic variables
	require_once 'dta-include.php';

	// validate action parameters
	$action = getAction();
	if ($action == "validate"){
		$comingFrom = comingFrom();
		$dfId = getDataFormat();
		$taId = getTransactionType();

		$adjust = getAdjust();
		if ($adjust == False) {
			echo "Adjustment mismatch: set value to disabled<br>\n";
		} else {
			if ($adjust == "true") {
				$adjust = True;
			} elseif ($adjust == "false") {
				$adjust = False;
			}
		}
	} else {
		// something went wrong ... raise an error
		$action = "new";
		$comingFrom = "validate";
		echo "<b>Unexpected parameter received. Evaluation doubtful.</b>\n";
	}

	// initialize a new dta-ch object
	$dta = new DTACH();

	// set transaction type
	$dta->setTransactionType($taId);	

	// set data format
	$dta->setDataFormat($dfId);

	// set date of delivery
	$dateOfDelivery = date("ymd");
	$dta->setDateOfDelivery($dateOfDelivery);

	// collect both header and further data
	foreach($_POST as $key=>$value) {
		switch($key) {
			case "requestedProcessingDate":
				$dta->setRequestedProcessingDate($value);
				break;
			case "bankClearingNumberReceiver":
				$dta->setBankClearingNumberReceiver($value);
				break;
			case "outputSequenceNumber":
				$dta->setOutputSequenceNumber($value);
				break;
			case "creationDate":
				$dta->setCreationDate($value);
				break;
			case "bankClearingNumberSender":
				$dta->setBankClearingNumberSender($value);
				break;
			case "dataFileSenderIdentification":
				$dta->setDataFileSenderIdentification($value);
				break;
			case "entrySequenceNumber":
				$dta->setEntrySequenceNumber($value);
				break;
			case "paymentType":
				$dta->setPaymentType($value);
				break;
			case "processingFlag":
				$dta->setProcessingFlag($value);
				break;
			default: 
				$dta->addTextField($key, $value);
				break;
		}
	}

	// auto-adjust values coming from the input fields
	if ($adjust) {
		$dta->adjustHeader();
		$dta->adjustDataFields();
	}

	// validate header and data without automatic correction
	$dta->validateHeader();
	$dta->validateDataFields();

	// display evaluation result

	echo "<h3>Step 3: Validate the transaction data for " . $dtaTransactionList[$taId] . ", " . $dtaDataFormatList[$dfId] . "</h3>\n";

	# define some variables
	$opl = 0;
	$bpl = 0;

	echo "<table border=\"0\" col=\3\" width=\"100%\">\n";
	echo "<tr>\n";
		echo "<td width=\"40%\" bgcolor=\"silver\"><b>Key</b></td>\n";
		echo "<td width=\"30%\" bgcolor=\"silver\"><b>Value</b></td>\n";
		echo "<td width=\"30%\" bgcolor=\"silver\"><b>Evaluation</b></td>\n";
	echo "</tr>\n";

	$positive = "lime";
	$unknown = "yellow";
	$negative = "red";
	$basic = "white";
	$itemList = array_merge($dta->header, $dta->fields);
	$itemList["dateOfDelivery"] = $dateOfDelivery;

	foreach($itemList as $key=>$value)
	{
		// default value for background color
		$bgColor = $white;
		$message = "&nbsp;";
			
		echo "<tr>\n";
		echo "<td width=\"40%\" bgcolor=\"silver\">$key</td>\n";
		echo "<td width=\"30%\">$value</td>\n";

		if ($dta->validationResult[$key] == True) {
			$bgColor = $positive;
			$message = "OK";
		} else {
			$bgColor = $negative;
			switch($key) {
				case "dateOfDelivery":
					$message = "date out of range";
					break;
				case "dataFormat":
					$message = "date invalid";
					break;
				case "bankClearingNumberReceiver":
					$message = "bank number invalid";
					break;
				case "requestedProcessingDate":
					$message = "date invalid";
					break;
				case "outputSequenceNumber":
					$message = "output sequence number invalid";
					break;
				case "creationDate":
					$message = "creation date invalid";
					break;
				case "bankClearingNumberSender":
					$message = "bank clearing number invalid";
					break;
				case "dataFileSenderIdentification":
					$message = "identification number invalid";
					break;
				case "entrySequenceNumber":
					$message = "entry sequence number invalid";
					break;
				case "transactionType":
					$message = "transaction type invalid";
					break;
				case "paymentType":
					$message = "payment type invalid";
					break;
				case "processingFlag":
					$message = "processing flag invalid";
					break;
					
				// - reference number (20)
				// + orderingPartyIdentification
				case "orderingPartyIdentification":
					$message = "ordering party identifcation invalid";
					break;
				// + orderingPartyTransactionNumber
				case "orderingPartyTransactionNumber":
					$message = "ordering party transaction invalid";
					break;

				// - account to be debited (25)
				// + accountWithIban
				case "accountWithIban":
					$message = "account data invalid";
					break;
				// + accountWithoutIban
				case "accountWithoutIban":
					$message = "account data invalid";
					break;
			
				// - payment amount (32a)
				// + paymentValueDate
				case "paymentValueDate":
					$message = "value date invalid";
					break;
				// + paymentCurrencyCode
				case "paymentCurrencyCode":
					$message = "ISO currency code invalid";
					break;
				// + paymentAmount
				case "paymentAmount":
					$message = "payment amount value invalid";
					break;

				// - ordering party (50)
				// + orderingPartyLine1 to 4
				case "orderingPartyLine1":
				case "orderingPartyLine2":
				case "orderingPartyLine3":
				case "orderingPartyLine4":
					$message = "value invalid";
					break;

				// - beneficiary (isr party number) (59)
				// + beneficiaryPartyIdentification
				case "beneficiaryPartyIdentification":
					$message = "value invalid";
					break;

				// + beneficiaryTransferType
				case "beneficiaryTransferType":
					$message = "transfer type invalid";
					break;
				// + beneficiaryPartyAccount
				case "beneficiaryPartyAccount":
					// TA 827, 830 and 832 only
					$message = "account value invalid";
					break;
			
				// + beneficiaryPartyLine1 to 4
				case "beneficiaryPartyLine1":
				case "beneficiaryPartyLine2":
				case "beneficiaryPartyLine3":
				case "beneficiaryPartyLine4":
					$message = "value invalid";
					break;

				// - reason for payment (70)
				// + ISR reference number
				case "isrReferenceNumber":
					$message = "invalid ISR reference number";
					break;

				// + ISR check digit
				case "isrCheckDigit":
					$message = "check digit invalid";
					break;
				
				// + beneficiaryMessageLine1 to 4
				case "beneficiaryMessageLine1":
				case "beneficiaryMessageLine2":
				case "beneficiaryMessageLine3":
				case "beneficiaryMessageLine4":
					$message = "value invalid";
					break;
				
				// - endBeneficiary (55)
				// + endBeneficiaryPartyAccount
				case "endBeneficiaryPartyAccount":
					$message = "end beneficiary party account number invalid";
					break;

				// + endBeneficiaryPartyLine1 to 4
				case "endBeneficiaryPartyLine1":
				case "endBeneficiaryPartyLine2":
				case "endBeneficiaryPartyLine3":
				case "endBeneficiaryPartyLine4":
					$message = "value invalid";
					break;

				// - convertionRate (36)
				case "convertionRate":
					$message = "convertion rate invalid";
					break;

				// - beneficiaryInstitution (57a or 57d)
				// + beneficiaryInstitution
				case "beneficiaryInstituteLine1":
					$message = "beneficiary institution id invalid";
					break;

				// + beneficiaryInstituteLine2 to 5
				// TA 836, only
				case "beneficiaryInstituteLine2":
				case "beneficiaryInstituteLine3":
				case "beneficiaryInstituteLine4":
				case "beneficiaryInstituteLine5":
					$message = "information invalid";
					break;
				// + iban
				// TA 836 and 837, only
				case "iban":
					$message = "wrong iban format";
					break;

				case "beneficiaryInstituteLine1":
					// option 57a and 57d
					$message = "SWIFT address invalid";
					break;

				// - bankPaymentInstruction (72)
				case "bankPaymentInstruction":
					$message = "payment instruction invalid";
					break;

				// - purpose (70i and 70u)
				// + purpose structure
				case "purposeStructure":
					$message = "purpose structure invalid";
					break;

				// + purposeLine1 to 3
				case "purposeLine1":
				case "purposeLine2":
				case "purposeLine3":
					$message = "pupose instruction invalid";
					break;

				// - rulesForCharges (71ia)
				case "rulesForCharges":
					$message = "invalid value";
					break;

				// - bank structured informations (72s or 72u)
				// + informationStructure
				case "informationStructure":
					$message = "information structure invalid";
					break;

				// + instructionLine1 to 4
				case "instructionLine1":
				case "instructionLine2":
				case "instructionLine3":
				case "instructionLine4":
				case "instructionLine5":
				case "instructionLine6":
					$message = "instruction text invalid";
					break;
				// - total
				case "total":
					$message = "total value invalid";
					break;
			}
		}
		
		echo "<td width=\"30%\" bgcolor=\"$bgColor\">\n";
		echo "$message\n";
		echo "</td>\n";
		echo "</tr>\n";
	}

	// echo "<br>";
	// echo "<br>";
	// echo "<input type=\"submit\" value=\"Select transaction\">";
	// echo "&nbsp;";
	// echo "<input type=\"reset\" value=\"Reset\">";
	// echo "</form>";
		
	echo "</table><br>\n";

	// evaluate opl value
	if ($opl < 2) {
		echo "Warning: ordering's party address has less than two lines.<br>\n";
	}
	if ($bpl == 0) {
		echo "Warning: beneficiary address has no lines.<br>\n";
	}

	echo "<hr>\n";
	echo "<h3>Created DTA record:</h3>\n";

	$dtaRecord = $dta->outputFullRecord();
		
	// navigation buttons
	echo "<table border=\"0\" col=\"2\">\n";
	echo "<tr>\n";

	// - edit data
	echo "<td>\n";
	echo "<form action=\"dta-formular.php\" method=\"post\">\n";
	echo "<input type=\"submit\" value=\"Edit transaction data\">\n";
	// - header and data fields

	$itemList = array_merge($dta->header, $dta->fields);
	foreach($itemList as $key=>$value) {
		echo "<input type=\"hidden\" name=\"$key\" value=\"$value\">\n";
	}

	echo "<input type=\"hidden\" name=\"action\" value=\"edit\">\n";
	echo "<input type=\"hidden\" name=\"comingFrom\" value=\"validate\">\n";
	echo "<input type=\"hidden\" name=\"preSelect\" value=\"$taId\">\n";
	echo "<input type=\"hidden\" name=\"dataFormat\" value=\"$dfId\">\n";
	echo "</form>\n";
	echo "</td>\n";

	// - add similar transaction
	/*echo "<td>\n";
	echo "<form action=\"dta-formular.php\" method=\"post\">\n";
	echo "<input type=\"submit\" value=\"Add similar transaction\">\n";
	echo "<input type=\"hidden\" name=\"action\" value=\"selectsimilar\">\n";
	echo "<input type=\"hidden\" name=\"comingFrom\" value=\"validate\">\n";
	echo "<input type=\"hidden\" name=\"preSelect\" value=\"$taId\">\n";
	echo "<input type=\"hidden\" name=\"dataFormat\" value=\"$dfId\">\n";
	echo "</form>\n";
	echo "</td>\n";

	// - add new (other) transaction
	echo "<td>\n";
	echo "<form action=\"dta.php\" method=\"post\">\n";
	echo "<input type=\"submit\" value=\"Add other transaction\">\n";
	echo "<input type=\"hidden\" name=\"action\" value=\"selectnext\">\n";
	echo "<input type=\"hidden\" name=\"comingFrom\" value=\"validate\">\n";
	echo "<input type=\"hidden\" name=\"preSelect\" value=\"$taId\">\n";
	echo "<input type=\"hidden\" name=\"dataFormat\" value=\"$dfId\">\n";
	echo "</form>\n";
	echo "</td>\n";
*/
	// - create dta record
	echo "<td>\n";
	//echo "<form action=\"dta-validate.php\" method=\"post\">\n";
	//echo "<input type=\"submit\" value=\"Create dta transaction list\">\n";
	//echo "<input type=\"hidden\" name=\"action\" value=\"validate\">\n";
	//echo "<input type=\"hidden\" name=\"adjust\" value=\"false\">\n";
	//echo "<input type=\"hidden\" name=\"comingFrom\" value=\"validate\">\n";
	//echo "<input type=\"hidden\" name=\"preSelect\" value=\"$taId\">\n";
	//echo "<input type=\"hidden\" name=\"dataFormat\" value=\"$dfId\">\n";
	//echo "</form>\n";
	echo "<form action=\"dta-create-dta-record.php\" method=\"post\">\n";
	echo "Save the DTA record to file:\n";
	echo "<input type=\"text\" name=\"outputFilename\">\n";
	echo "<input type=\"hidden\" name=\"data\" value=\"$dtaRecord\">\n";
	echo "<input type=\"submit\" value=\"Save\">\n";
	echo "</form>\n";
	echo "</td>\n";

	echo "</tr>\n";
	echo "</table>\n";
	?>

	<hr>
</body>
</html>
