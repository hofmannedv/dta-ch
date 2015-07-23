<html>
	<head>Testing the DTA-CH class</head>
<body>
	<h2 style="color:white;background:blue">&nbsp;Testing the DTA-CH class</h2>
	<?php

	require_once('dta-include.php');

	// validate action parameters
	$action = getAction();
	switch($action){
	case "new":
		$comingFrom = comingFrom();
		$dfId = getDataFormat();
		$taId = getTransactionType();
		break;
	case "edit":
		$comingFrom = comingFrom();
		$dfId = getDataFormat();
		$taId = getTransactionType();

		$requestedProcessingDate = trim($_POST["requestedProcessingDate"]);
		$bankClearingNumberReceiver = trim($_POST["bankClearingNumberReceiver"]);
		$outputSequenceNumber = trim($_POST["outputSequenceNumber"]);
		$creationDate = trim($_POST["creationDate"]);
		$bankClearingNumberSender = trim($_POST["bankClearingNumberSender"]);
		$bankPaymentInstruction = trim($_POST["bankPaymentInstruction"]);
		$dataFileSenderIdentification = trim($_POST["dataFileSenderIdentification"]);
		$entrySequenceNumber = trim($_POST["entrySequenceNumber"]);
		$paymentType = trim($_POST["paymentType"]);
		$processingFlag = trim($_POST["processingFlag"]);
		$beneficiaryTransferType = trim($_POST["beneficiaryTransferType"]);
		$beneficiaryPartyAccount = trim($_POST["beneficiaryPartyAccount"]);
		$beneficiaryPartyIdentification = trim($_POST["beneficiaryPartyIdentification"]);
		$beneficiaryPartyLine1 = trim($_POST["beneficiaryPartyLine1"]);
		$beneficiaryPartyLine2 = trim($_POST["beneficiaryPartyLine2"]);
		$beneficiaryPartyLine3 = trim($_POST["beneficiaryPartyLine3"]);
		$beneficiaryPartyLine4 = trim($_POST["beneficiaryPartyLine4"]);
		$identificationBankAddress = trim($_POST["identificationBankAddress"]);
		$beneficiaryInstituteLine1 = trim($_POST["beneficiaryInstituteLine1"]);
		$beneficiaryInstituteLine2 = trim($_POST["beneficiaryInstituteLine2"]);
		$beneficiaryInstituteLine3 = trim($_POST["beneficiaryInstituteLine3"]);
		$beneficiaryInstituteLine4 = trim($_POST["beneficiaryInstituteLine4"]);
		$beneficiaryInstituteLine5 = trim($_POST["beneficiaryInstituteLine5"]);
		$beneficiaryMessageLine1 = trim($_POST["beneficiaryMessageLine1"]);
		$beneficiaryMessageLine2 = trim($_POST["beneficiaryMessageLine2"]);
		$beneficiaryMessageLine3 = trim($_POST["beneficiaryMessageLine3"]);
		$beneficiaryMessageLine4 = trim($_POST["beneficiaryMessageLine4"]);
		$endBeneficiaryPartyAccount = trim($_POST["endBeneficiaryPartyAccount"]);
		$endBeneficiaryPartyLine1 = trim($_POST["endBeneficiaryPartyLine1"]);
		$endBeneficiaryPartyLine2 = trim($_POST["endBeneficiaryPartyLine2"]);
		$endBeneficiaryPartyLine3 = trim($_POST["endBeneficiaryPartyLine3"]);
		$endBeneficiaryPartyLine4 = trim($_POST["endBeneficiaryPartyLine4"]);
		$isrReferenceNumber = trim($_POST["isrReferenceNumber"]);
		$isrCheckDigit = trim($_POST["isrCheckDigit"]);
		$orderingPartyIdentification = trim($_POST["orderingPartyIdentification"]);
		$orderingPartyTransactionNumber = trim($_POST["orderingPartyTransactionNumber"]);
		$accountWithoutIban = trim($_POST["accountWithoutIban"]);
		$accountWithIban = trim($_POST["accountWithIban"]);
		$paymentValueDate = trim($_POST["paymentValueDate"]);
		$paymentCurrencyCode = trim($_POST["paymentCurrencyCode"]);
		$paymentAmount = trim($_POST["paymentAmount"]);
		$orderingPartyLine1 = trim($_POST["orderingPartyLine1"]);
		$orderingPartyLine2 = trim($_POST["orderingPartyLine2"]);
		$orderingPartyLine3 = trim($_POST["orderingPartyLine3"]);
		$orderingPartyLine4 = trim($_POST["orderingPartyLine4"]);
		$convertionRate = trim($_POST["convertionRate"]);
		$iban = trim($_POST["iban"]);
		$purposeStructure = trim($_POST["purposeStructure"]);
		$purposeLine1 = trim($_POST["purposeLine1"]);
		$purposeLine2 = trim($_POST["purposeLine2"]);
		$purposeLine3 = trim($_POST["purposeLine3"]);
		$informationStructure = trim($_POST["informationStucture"]);
		$instructionLine1 = trim($_POST["instructionLine1"]);
		$instructionLine2 = trim($_POST["instructionLine2"]);
		$instructionLine3 = trim($_POST["instructionLine3"]);
		$instructionLine4 = trim($_POST["instructionLine4"]);
		break;
	default:
		// default values
		$action = "new";
		$comingFrom = "formular";
		$taId = "826";
		$dfId = "fixed";
		break;
	}

	// input field length table
	$inputFieldLength = Array(
		"20"  => Array("fixed" => 16, "variable" => 16),
		"25"  => Array("fixed" => 24, "variable" => 24),
		"58"  => Array("fixed" => 34, "variable" => 34),
		"72s" => Array("fixed" => 35, "variable" => 35),
		"72u" => Array("fixed" => 30, "variable" => 35)
	);

	// input field number table
	$inputFieldNumber = Array(
		"20"  => Array("fixed" => 1, "variable" => 1),
		"24"  => Array("fixed" => 1, "variable" => 1),
		"58"  => Array("fixed" => 1, "variable" => 1),
		"72s" => Array("fixed" => 3, "variable" => 6),
		"72u" => Array("fixed" => 4, "variable" => 6)
	);

	echo "<h3>Step 2: transaction type: " . $dtaTransactionList[$taId] . ", " . $dtaDataFormatList[$dfId] . "</h3>\n";

	echo "<form action=\"dta-validate.php\" method=\"post\">\n";
	echo "<input type=\"hidden\" name=\"dataFormat\" value=\"$dfId\">\n";

	echo "<table border=\"0\" col=\"2\" width=\"100%\">\n";

	// dta header
	echo "<tr>\n";
	echo "<td colspan=\"2\" bgcolor=\"silver\"><b>Dta header</b>\n";
	echo "</td>\n";
	echo "</tr>\n";

	// requested processing date (826, 827)
	echo "<tr>\n";
	echo "<td width=\"40%\" bgcolor=\"silver\">Requested processing date *</td>\n";
	echo "<td width=\"60%\">\n";
		if (in_array($taId, Array(826,827))){
			echo "<input type=\"text\" name=\"requestedProcessingDate\" size=\"6\" maxlength=\"6\" value=\"$requestedProcessingDate\">&nbsp;\n";
			echo "(six digits YYMMDD)\n";
		} else {
			echo "<input type=\"hidden\" name=\"requestedProcessingDate\" value=\"000000\">\n";
			echo "000000\n";
		}
	echo "</td>\n";
	echo "</tr>\n"; 

	// bank clearing number - receiver (827)
 	if ($taId == 827){
		echo "<tr>\n";
		echo "<td width=\"40%\" bgcolor=\"silver\">Bank clearing no. of the beneficiary's bank * </td>\n";
		echo "<td>\n";
 		echo "<input type=\"text\" name=\"bankClearingNumberReceiver\" size=\"12\" maxlength=\"12\" value=\"$bankClearingNumberReceiver\">&nbsp;\n";
 		echo "(twelve digits flush left without punctuation)\n";
 		echo "</td>\n";
 		echo "</tr>\n";
	} else {
 		echo "<input type=\"hidden\" name=\"bankClearingNumberReceiver\" value=\"            \">\n";
	}

	// output sequence number (all TAs)
	// filled with zeros
 	echo "<tr>\n";
 	echo "<td width=\"40%\" bgcolor=\"silver\">Output sequence number * </td>\n";
 	echo "<td>\n";
 		$outputSequenceNumber = "00000";
 		echo "<input type=\"hidden\" name=\"outputSequenceNumber\" value=\"$outputSequenceNumber\">\n";
 		echo "$outputSequenceNumber (five zeros)\n";
 	echo "</td>\n";
 	echo "</tr>\n";

	// creation date (all TAs)
 	echo "<tr>\n";
 	echo "<td width=\"40%\" bgcolor=\"silver\">Creation date * </td>\n";
 	echo "<td>\n";
 		$creationDate = date("ymd");
 		echo "<input type=\"hidden\" name=\"creationDate\" value=\"$creationDate\">\n";
 		echo "$creationDate (YYMMDD)\n";
 	echo "</td>\n";
 	echo "</tr>\n";

	// bank clearing number - sender (all TAs)
	// TA 890: blanks, only
 	echo "<tr>\n";
 	echo "<td width=\"40%\" bgcolor=\"silver\">Bank clearing no. of the ordering party's bank * </td>\n";
 	echo "<td>\n";
 		if ($taId == 890){
 			echo "field left blank\n";
 		} else {
 			echo "<input type=\"text\" name=\"bankClearingNumberSender\" size=\"7\" maxlength=\"7\" value=\"$bankClearingNumberSender\">&nbsp;\n";
 			echo "(seven digits flush left without punctuation)\n";
 		}
 	echo "</td>\n";
 	echo "</tr>\n";

	// data file sender identification (all TAs)
 	echo "<tr>\n";
 	echo "<td width=\"40%\" bgcolor=\"silver\">Data file sender identification * </td>\n";
 	echo "<td>\n";
 		echo "<input type=\"text\" name=\"dataFileSenderIdentification\" size=\"5\" maxlength=\"5\" value=\"$dataFileSenderIdentification\">&nbsp;\n";
 		echo "(five alphanumerical characters flush left)\n";
 	echo "</td>\n";
 	echo "</tr>\n";

	// entry sequence number (all TAs)
 	echo "<tr>\n";
 	echo "<td width=\"40%\" bgcolor=\"silver\">Entry sequence number * </td>\n";
 	echo "<td>\n";
 		echo "<input type=\"text\" name=\"entrySequenceNumber\" size=\"5\" maxlength=\"5\" value=\"$entrySequenceNumber\">&nbsp;\n";
 		echo "(five numerical characters flush left)\n";
 	echo "</td>\n";
 	echo "</tr>\n";

	// transaction type (all TAs)
 	echo "<tr>\n";
 	echo "<td width=\"40%\" bgcolor=\"silver\">Transaction type * </td>\n";
 	echo "<td>\n";
 		echo "$taId&nbsp;";
 		echo "<input type=\"hidden\" name=\"transactionType\" value=\"$taId\">\n";
 		echo "(three numerical characters flush left)\n";
 	echo "</td>\n";
 	echo "</tr>\n";

	// payment type indicating salary and pension payments
	// for TA 826, 836 and 837: 1, otherwise: 0
	echo "<tr>\n";
 	echo "<td width=\"40%\" bgcolor=\"silver\">Payment type * </td>\n";
 	echo "<td width=\"60%\">\n";
		$checked = "";
		if ($paymentType == 1) {$checked = "checked";}
 		if (in_array($taId, Array(827,836,837))){
			echo "<input type=\"checkbox\" name=\"paymentType\" value=\"1\" $checked> salary and pension payment\n";
		} else {
			echo "<input type=\"hidden\" name=\"paymentType\" value=\"0\">\n";
			echo "field with fixed value: 0\n";
		}
 	echo "</td>\n";
 	echo "</tr>\n";

	// processing flag (all TAs)
	// bank internal, set to 0
 	echo "<tr>\n";
 	echo "<td width=\"40%\" bgcolor=\"silver\">Processing flag * </td>\n";
 	echo "<td>\n";
 		$processingFlag = 0;
 		echo "<input type=\"hidden\" name=\"processingFlag\" value=\"$processingFlag\">\n";
 		echo "$processingFlag (0)\n";
 	echo "</td>\n";
 	echo "</tr>\n";

	// dta fields
 	echo "<tr\n>";
 	echo "<td colspan=\"2\" bgcolor=\"silver\"><b>Dta fields</b>\n";
 	echo "</td>\n";
 	echo "</tr>\n";

	// specific dta fields for TA 826
	// - field id 59: beneficiary (isr party number)
 	if ($taId == 826) {
 		echo "<tr>\n";
 		echo "<td width=\"40%\" bgcolor=\"silver\">Beneficiary (59) * </td>\n";
 		echo "<td>\n";
 			echo "Beneficiary's ISR Party number: <input type=\"text\" name=\"beneficiaryPartyIdentification\" size=\"12\" maxlength=\"12\" value=\"$beneficiaryPartyIdentification\">&nbsp;(twelve characters)<br>\n";
			$vList = Array("", $beneficiaryPartyLine1, $beneficiaryPartyLine2, $beneficiaryPartyLine3, $beneficiaryPartyLine4);
 			$i=1;
 			while($i<5) {
				$v = "";
				$v = $vList[$i];
 				echo "#$i <input type=\"text\" name=\"beneficiaryPartyLine$i\" size=\"20\" maxlength=\"20\" value=\"$v\"><br>\n";
 				$i++;
 			}
 		echo "</td>\n";
 		echo "</tr>\n";
	
		// - field id 70: reason for payment
 		echo "<tr>\n";
 		echo "<td width=\"40%\" bgcolor=\"silver\">Reason for payment (70) * </td>\n";
 		echo "<td>\n";
 			echo "ISR reference number: <input type=\"text\" name=\"isrReferenceNumber\" size=\"27\" maxlength=\"27\" value=\"$isrReferenceNumber\">\n<br>\n";
			echo "ISR check digit: <input type=\"text\" name=\"isrCheckDigit\" size=\"2\" maxlength=\"2\" value=\"$isrCheckDigit\">\n";
 		echo "</td>\n";
 		echo "</tr>\n";

		
 	}

	// specific dta fields for TA 827
 	if ($taId == 827) {
		// - field id 59: beneficiary for bank payment or postal payment
 		echo "<tr>\n";
 		echo "<td width=\"40%\" bgcolor=\"silver\">Beneficiary (59) * </td>\n";
 		echo "<td>\n";
			$beneficiaryTransferTypeList = Array(
				"bankPayment" => "Bank payments",
				"postalPayment" => "Postal payments",
				"postalOrder" => "Postal order"
			);
 			echo "<select size=\"1\" name=\"beneficiaryTransferType\">\n";
			foreach ($beneficiaryTransferTypeList as $transferKey => $transferItem) {
				$selected = "";
				if ($transferKey == $beneficiaryTransferType) {
					$selected = "selected";
				}
				echo "<option $selected value=\"$transferKey\">$transferItem</option>\n";
			}
 			echo "</select>\n";
 			echo " <input type=\"text\" name=\"beneficiaryPartyAccount\" size=\"30\" maxlength=\"30\" value=\"$beneficiaryPartyAccount\">&nbsp;(thirty characters)<br>\n";
			$vList = Array("", $beneficiaryPartyLine1, $beneficiaryPartyLine2, $beneficiaryPartyLine3, $beneficiaryPartyLine4);
 			for ($i=1;$i<5;$i++) {
 				$v = $vList[$i];
		echo "#$i <input type=\"text\" name=\"beneficiaryPartyLine$i\" size=\"24\" maxlength=\"24\" value=\"$v\"><br>\n";
 			}
 		echo "</td>\n";
 		echo "</tr>\n";

		// - field id 70: reason for payment
 		echo "<tr>\n";
 		echo "<td width=\"40%\" bgcolor=\"silver\">Reason for payment (70)</td>\n";
 		echo "<td>\n";
		$vList = Array("", $beneficiaryMessageLine1, $beneficiaryMessageLine2, $beneficiaryMessageLine3, $beneficiaryMessageLine4);
 		for ($i=1;$i<5;$i++) {
 			$v = $vList[$i];
 			echo "#$i <input type=\"text\" name=\"beneficiaryMessageLine$i\" size=\"28\" maxlength=\"28\" value=\"$v\"><br>\n";
 		}
 		echo "</td>\n";
 		echo "</tr>\n";

		// - field id 55: end beneficiary
 		echo "<tr>\n";
 		echo "<td width=\"40%\" bgcolor=\"silver\">End beneficiary (55)</td>\n";
 		echo "<td>\n";
 			echo "Account number: <input type=\"text\" name=\"endBeneficiaryPartyAccount\" size=\"30\" maxlength=\"30\" value=\"$endBeneficiaryPartyAccount\">&nbsp;(thirty characters)<br>\n";
			$vList = Array("", $endBeneficiaryPartyLine1, $endBeneficiaryPartyLine2, $endBeneficiaryPartyLine3, $endBeneficiaryPartyLine4);
 			for ($i=1;$i<5;$i++) {
 				$v = $vList[$i];
 				echo "#$i <input type=\"text\" name=\"endBeneficiaryPartyLine$i\" size=\"24\" maxlength=\"24\" value=\"$v\"><br>\n";
 			}
 		echo "</td>\n";
 		echo "</tr>\n";
 	}

	// specific dta fields for TA 830
 	if ($taId == 830) {
		// - field id 36: convertion rate
 		echo "<tr>\n";
 		echo "<td width=\"40%\" bgcolor=\"silver\">Convertion rate (36)</td>\n";
 		echo "<td>\n";
 			echo "Value: <input type=\"text\" name=\"convertionRate\" size=\"12\" maxlength=\"12\" value=\"$convertionRate\">&nbsp;(twelve digits)<br>\n";
 		echo "</td>\n";
 		echo "</tr>\n";

		echo "<tr>\n";
		echo "<td width=\"40%\" bgcolor=\"silver\">Identification Bank Address (57)</td>\n";
		echo "<td>\n";
			echo "Value: <input type=\"text\" name=\"identificationBankAddress\" size=\"1\" maxlength=\"1\" value=\"$identificationBankAddress\">&nbsp;(A or D)\n";
		echo "</td>\n";
		echo "</tr>\n";

		// - field id 57a or 57d: beneficiary's institution (bank/post office)
 		echo "<tr>\n";
 		echo "<td width=\"40%\" bgcolor=\"silver\">Beneficiary's institution (57a or 57d) * </td>\n";
 		echo "<td>\n";
 			echo "Bank sorting code, BC number: <input type=\"text\" name=\"beneficiaryInstituteLine1\" size=\"24\" maxlength=\"24\" value=\"$beneficiaryInstitutionLine1\">&nbsp;(twenty-four characters)<br>\n";
			$vList = Array("", $beneficiaryInstituteLine2, $beneficiaryInstituteLine3, $beneficiaryInstituteLine4, $beneficiaryInstituteLine5);
 			echo "or<br>\n";
 			for ($i=2;$i<6;$i++) {
				$v = $vList[$i];
				echo "#$i: <input type=\"text\" name=\"beneficiaryInstituteLine$i\" size=\"24\" maxlength=\"24\" value=\"$v\"><br>\n";
 			}
 		echo "</td>\n";
 		echo "</tr>\n";

		// - field id 59: beneficiary
 		echo "<tr>\n";
 		echo "<td width=\"40%\" bgcolor=\"silver\">Beneficiary (59) * </td>\n";
 		echo "<td>\n";
 			echo "Beneficiary's account number: <input type=\"text\" name=\"beneficiaryPartyAccount\" size=\"24\" maxlength=\"24\" value=\"$beneficiaryPartyAccount\">&nbsp;(twenty-four characters)<br>\n";
			$vList = Array("", $beneficiaryPartyLine1, $beneficiaryPartyLine2, $beneficiaryPartyLine3, $beneficiaryPartyLine4);
 			for ($i=1;$i<5;$i++) {
				$v = $vList[$i];
				echo "#$i: <input type=\"text\" name=\"beneficiaryPartyLine$i\" size=\"24\" maxlength=\"24\" value=\"$v\"><br>\n";
 			}
 		echo "</td>\n";
 		echo "</tr>\n";

		// - field id 70: reason for payment
 		echo "<tr>\n";
 		echo "<td width=\"40%\" bgcolor=\"silver\">Reason for payment (70)</td>\n";
 		echo "<td>\n";
			$vList = Array("", $beneficiaryMessageLine1, $beneficiaryMessageLine2, $beneficiaryMessageLine3, $beneficiaryMessageLine4);
 			for ($i=1;$i<5;$i++) {
				$v = $vList[$i];
 				echo "#$i: <input type=\"text\" name=\"beneficiaryMessageLine$i\" size=\"30\" maxlength=\"30\" value=\"$v\"><br>\n";
 			}
 		echo "</td>\n";
 		echo "</tr>\n";

		// - field id 72: bank payment instructions
 		echo "<tr>\n";
 		echo "<td width=\"40%\" bgcolor=\"silver\">Bank payment instructions (72)</td>\n";
 		echo "<td>\n";
 			$bankPaymentInstructionList = Array(
				"CHG/OUR" => "to our charge (CHG/OUR)",
				"CHG/BEN" => "charge beneficiary (CHG/BEN)",
				" " => "charges split (default value)"
			);
 			echo "<select size=\"1\" name=\"bankPaymentInstruction\">\n";
			foreach ($bankPaymentInstructionList as $instructionKey => $instructionItem) {
				$selected = "";
				if ($instructionKey == $bankPaymentInstruction) {
					$selected = "selected";
				}
				echo "<option $selected value=\"$instructionKey\">$instructionItem</option>\n";
			}
 			echo "</select>\n";
 		echo "</td>\n";
 		echo "</tr>\n";
	}

	// specific dta fields for TA 832
 	if ($taId == 832) {
		// - field id 36: convertion rate
 		echo "<tr>\n";
 		echo "<td width=\"40%\" bgcolor=\"silver\">Convertion rate (36)</td>";
 		echo "<td>\n";
 			echo "Value: <input type=\"text\" name=\"convertionRate\" size=\"12\" maxlength=\"12\" value=\"$convertionRate\">&nbsp;(twelve digits)<br>\n";
 		echo "</td>\n";
 		echo "</tr>\n";

		// - field id 59: beneficiary
 		echo "<tr>\n";
 		echo "<td width=\"40%\" bgcolor=\"silver\">Beneficiary (59) * </td>\n";
 		echo "<td>\n";
			echo "Account number: <input type=\"text\" name=\"beneficiaryPartyAccount\" size=\"24\" maxlength=\"24\" value=\"$beneficiaryPartyAccount\"><br>\n";
			$vList = Array("", $beneficiaryPartyLine1, $beneficiaryPartyLine2, $beneficiaryPartyLine3, $beneficiaryPartyLine4);
 			for ($i=1;$i<5;$i++) {
				$v = $vList[$i];
 				echo "#$i: <input type=\"text\" name=\"beneficiaryPartyLine$i\" size=\"24\" maxlength=\"24\" value=\"$v\"><br>\n";
 			}
 		echo "</td>\n";
 		echo "</tr>\n";

		// - field id 70: reason for payment
 		echo "<tr>\n";
 		echo "<td width=\"40%\" bgcolor=\"silver\">Reason for payment (70)</td>\n";
 		echo "<td>\n";
			$vList = Array("", $beneficiaryMessageLine1, $beneficiaryMessageLine2, $beneficiaryMessageLine3, $beneficiaryMessageLine4);
 			for ($i=1;$i<5;$i++) {
				$v = $vList[$i];
 				echo "#$i: <input type=\"text\" name=\"beneficiaryMessageLine$i\" size=\"30\" maxlength=\"30\" value=\"$v\"><br>\n";
 			}
 		echo "</td>\n";
 		echo "</tr>\n";

		// - field id 72: bank payment instructions
 		echo "<tr>\n";
 		echo "<td width=\"40%\" bgcolor=\"silver\">Bank payment instructions (72)</td>\n";
 		echo "<td>\n";
 		 	$bankPaymentInstructionList = Array(
				"CHG/OUR" => "to our charge (CHG/OUR)",
				"CHG/BEN" => "charge beneficiary (CHG/BEN)",
				" " => "charges split (default value)"
			);
 			echo "<select size=\"1\" name=\"bankPaymentInstruction\">\n";
			foreach ($bankPaymentInstructionList as $instructionKey => $instructionItem) {
				$selected = "";
				if ($instructionKey == $bankPaymentInstruction) {
					$selected = "selected";
				}
				echo "<option $selected value=\"$instructionKey\">$instructionItem</option>\n";
			}
 			echo "</select>\n";
 		echo "</td>\n";
 		echo "</tr>\n";
	}

	// specific dta fields for TA 836
 	if ($taId == 836) {
		// - field id 36: convertion rate
 		echo "<tr>\n";
 		echo "<td width=\"40%\" bgcolor=\"silver\">Convertion rate (36)</td>\n";
 		echo "<td>\n";
 			echo "Value: <input type=\"text\" name=\"convertionRate\" size=\"12\" maxlength=\"12\" value=\"$convertionRate\">&nbsp;(twelve digits)<br>\n";
 		echo "</td>\n";
 		echo "</tr>\n";

		echo "<tr>\n";
		echo "<td width=\"40%\" bgcolor=\"silver\">Identification Bank Address (57)</td>\n";
		echo "<td>\n";
			echo "Value: <input type=\"text\" name=\"identificationBankAddress\" size=\"1\" maxlength=\"1\" value=\"$identificationBankAddress\">&nbsp;(A or D)\n";
		echo "</td>\n";
		echo "</tr>\n";

	// - field id 57a or 57d: beneficiary's institution (bank/post office)
		echo "<tr>\n";
		echo "<td width=\"40%\" bgcolor=\"silver\">Beneficiary's institution (57a or 57d) * </td>\n";
		echo "<td>\n";
 			echo "SWIFT address of the beneficiary's institute or name and address of the beneficiary's institution:<br>\n";
 			echo "#1: <input type=\"text\" name=\"beneficiaryInstituteLine1\" size=\"35\" maxlength=\"35\" value=\"$beneficiaryInstituteLine1\"><br>\n";
 			echo "#2: <input type=\"text\" name=\"beneficiaryInstituteLine2\" size=\"35\" maxlength=\"35\" value=\"$beneficiaryInstituteLine2\"><br>\n";
		echo "</td>\n";
		echo "</tr>\n";

		// - field id 58: IBAN (beneficiary's account number)
		echo "<tr>\n";
		echo "<td width=\"40%\" bgcolor=\"silver\">IBAN (58) * </td>\n";
		echo "<td>\n";
			$lMax = $inputFieldLength["58"][$dfId];
 			echo "Value: <input type=\"text\" name=\"iban\" size=\"$lMax\" maxlength=\"$lMax\" value=\"$iban\">&nbsp;(thirty-four digits)<br>\n";
		echo "</td>\n";
		echo "</tr>\n";

		// - field id 59: beneficiary
		echo "<tr>\n";
		echo "<td width=\"40%\" bgcolor=\"silver\">Beneficiary (59) * </td>\n";
		echo "<td>\n";
			$vList = Array("", $beneficiaryPartyLine1, $beneficiaryPartyLine2, $beneficiaryPartyLine3, $beneficiaryPartyLine4);
 			for ($i=1;$i<4;$i++) {
				$v = $vList[$i];
 				echo "#$i: <input type=\"text\" name=\"beneficiaryPartyLine$i\" size=\"35\" maxlength=\"35\" value=\"$v\"><br>\n";
 			}
		echo "</td>\n";
		echo "</tr>\n";

		// - field id 70i or 70u: purpose
		echo "<tr>\n";
		echo "<td width=\"40%\" bgcolor=\"silver\">Purpose (70i or 70u) * </td>\n";
		echo "<td>\n";
			$purposeStructureList = Array(
				"I" => "structured with IPI reference number",
				"U" => "unstructured with free text"
			);
 			echo "<select size=\"1\" name=\"purposeStructure\">\n";
			foreach($purposeStructureList as $structureKey => $structureValue) {
				$selected = "";
				if($purposeStructure == $structureKey) {
					$selected = "selected";
				}
				echo "<option $selected value=\"$structureKey\">$structureValue</option>\n";
			}
 			echo "</select><br>\n";
			$vList = Array("", $purposeLine1, $purposeLine2, $purposeLine3);
 			for ($i=1;$i<4;$i++) {
				$v = $vList[$i];
 				echo "#$i: <input type=\"text\" name=\"purposeLine$i\" size=\"35\" maxlength=\"35\" value=\"$v\"><br>\n";
 			}
		echo "</td>\n";
		echo "</tr>\n";

		// - field id 71ia: rules for charges
		echo "<tr>\n";
		echo "<td width=\"40%\" bgcolor=\"silver\">Rules for charges (71ia) * </td>\n";
		echo "<td>\n";
 		 	$rulesForChargesList = Array(
				"OUR (all charges debited to the ordering party)", 
				"BEN (all charges debited to the beneficiary)", 
				"SHA (charges split)"
			);
 			echo "<select size=\"1\" name=\"rulesForCharges\">\n";
			foreach($rulesForChargesList as $chargesKey => $chargesValue) {
				$selected = "";
				if($chargesKey == $rulesForCharges) {
					$selected = "selected";
				}
				echo "<option $selected value=\"$chargesKey\">$chargesValue</option>\n";
			}
 			echo "</select>\n";
		echo "</td>\n";
		echo "</tr>\n";
	 }
	
	// specific dta fields for TA 837
	if ($taId == 837) {
		// - field id 36: convertion rate
		echo "<tr>\n";
		echo "<td width=\"40%\" bgcolor=\"silver\">Convertion rate (36)</td>\n";
		echo "<td>\n";
 			echo "Value: <input type=\"text\" name=\"convertionRate\" size=\"12\" maxlength=\"12\" value=\"$convertionRate\">&nbsp;(twelve digits)<br>\n";
		echo "</td>\n";
		echo "</tr>\n";

		// - field id 57a or 57d: beneficiary's institution (bank/post office)
		echo "<tr>\n";
		echo "<td width=\"40%\" bgcolor=\"silver\">Identification Bank Address (57)</td>\n";
		echo "<td>\n";
			echo "Value: <input type=\"text\" name=\"identificationBankAddress\" size=\"1\" maxlength=\"1\" value=\"$identificationBankAddress\">&nbsp;(A or D)\n";
		echo "</td>\n";
		echo "</tr>\n";

		echo "<tr>\n";
		echo "<td width=\"40%\" bgcolor=\"silver\">Beneficiary's institution (57a or 57d) * </td>\n";
		echo "<td>\n";
 			echo "Bank sorting code, BC number: <input type=\"text\" name=\"beneficiaryInstituteLine1\" size=\"24\" maxlength=\"24\" value=\"$beneficiaryInstituteLine1\">&nbsp;(twenty-four characters)<br>\n";
			$vList = Array("", $beneficiaryInstituteLine2, $beneficiaryInstituteLine3, $beneficiaryInstituteLine4, $beneficiaryInstituteLine5);
 			echo "<br>\n";
 			for ($i=2;$i<6;$i++) {
				$v = $vList[$i];
 				echo "Line #$i: <input type=\"text\" name=\"beneficiaryInstituteLine$i\" size=\"24\" maxlength=\"24\" value=\"$v\"><br>\n";
 			}
		echo "</td>\n";
		echo "</tr>\n";

		// - field id 58: IBAN (beneficiary's account number)
		echo "<tr>\n";
		echo "<td width=\"40%\" bgcolor=\"silver\">IBAN (beneficiary's account number) (58)</td>\n";
		echo "<td>\n";
 			echo "Value: <input type=\"text\" name=\"iban\" size=\"34\" maxlength=\"34\" value=\"$iban\">&nbsp;(thirty-four digits)<br>\n";
		echo "</td>\n";
		echo "</tr>\n";

		// - field id 59: beneficiary
		echo "<tr>\n";
		echo "<td width=\"40%\" bgcolor=\"silver\">Beneficiary (59) * </td>\n";
		echo "<td>\n";
 			echo "Beneficiary's account number: <input type=\"text\" name=\"beneficiaryPartyAccount\" size=\"24\" maxlength=\"24\" value=\"$beneficiaryPartyAccount\">&nbsp;(twenty-four characters)<br>\n";
			$vList = Array("", $beneficiaryPartyLine1, $beneficiaryPartyLine2, $beneficiaryPartyLine3, $beneficiaryPartyLine4);
 			for ($i=1;$i<5;$i++) {
				$v = $vList[$i];
 				echo "#$i: <input type=\"text\" name=\"beneficiaryPartyLine$i\" size=\"24\" maxlength=\"24\" value=\"$v\"><br>\n";
 			}
		echo "</td>\n";
		echo "</tr>\n";

		// - field id 70i or 70u: purpose
		echo "<tr>\n";
		echo "<td width=\"40%\" bgcolor=\"silver\">Purpose (70i or 70u) * </td>\n";
		echo "<td>\n";
			$purposeStructureList = Array(
				"I" => "structured with IPI reference number",
				"U" => "unstructured with IPI reference number",
				"F" => "unstructured without IPI and with free text"
			);
 			echo "<select size=\"1\" name=\"purposeStructure\">\n";
			foreach($purposeStructureList as $structureKey => $structureValue) {
				$selected = "";
				if($purposeStructure == $structureKey) {
					$selected = "selected";
				}
				echo "<option $selected value=\"$structureKey\">$structureValue</option>\n";
			}
 			echo "</select><br>\n";
			$vList = Array("", $purposeLine1, $purposeLine2, $purposeLine3);
 			for ($i=1;$i<4;$i++) {
				$v = $vList[$i];
 				echo "#$i <input type=\"text\" name=\"purposeLine$i\" size=\"35\" maxlength=\"35\" value=\"$v\"><br>\n";
 			}
		echo "</td>\n";
		echo "</tr>\n";

		// - field id 71a: rules for charges
		echo "<tr>\n";
		echo "<td width=\"40%\" bgcolor=\"silver\">Rules for charges (71a) * </td>\n";
		echo "<td>\n";
 		 	$rulesForChargesList = Array(
				"OUR (all charges debited to the ordering party)", 
				"BEN (all charges debited to the beneficiary)", 
				"SHA (charges split)"
			);
 			echo "<select size=\"1\" name=\"rulesForCharges\">\n";
			foreach($rulesForChargesList as $chargesKey => $chargesValue) {
				$selected = "";
				if($chargesKey == $rulesForCharges) {
					$selected = "selected";
				}
				echo "<option $selected value=\"$chargesKey\">$chargesValue</option>\n";
			}
 			echo "</select>\n";
		echo "</td>\n";
		echo "</tr>\n";

		// - field id 72s or 72u: bank payment instructions
		echo "<tr>\n";
		echo "<td width=\"40%\" bgcolor=\"silver\">Bank payment instructions (72s or 72u)</td>\n";
		echo "<td>\n";
			if ($dfId == "fixed") {
				$informationStructureList = Array(
					"S" => "Structured information",
					"U" => "Unstructured information"
				);

				echo "<select size=\"1\" name=\"informationStructure\">\n";
				foreach($informationStructureList as $structureKey => $structureValue) {
					$selected = "";
					if($informationStructure == $structureKey) {
						$selected = "selected";
					}
					echo "<option i$selected value=\"$structureKey\">$structureValue</option>\n";
				}
 				echo "</select><br>\n";
			}
	
			$iMax = $inputFieldNumber["72u"][$dfId];
			$lMax = $inputFieldLength["72u"][$dfId];
			$vList = Array("", $instructionLine1, $instructionLine2, $instructionLine3, $instructionLine4, $instructionLine5, $instructionLine6);
 			for ($i=1;$i<=$iMax;$i++) {
				$v = $vList[$i];
 				echo "#$i: <input type=\"text\" name=\"instructionLine$i\" size=\"$lMax\" maxlength=\"$lMax\" value=\"$v\"><br>\n";
 			}
		echo "</td>\n";
		echo "</tr>\n";
	}
	
	// specific dta fields for TA 890
	if ($taId == 890) {
		// - field id 90: total amount
		echo "<tr>\n";
		echo "<td width=\"40%\" bgcolor=\"silver\">Total amount (90) * </td>\n";
		echo "<td>\n";
 			echo "all payment record amounts will be added together taking account of the comma, regardless of the currency.\n";
 			echo "<input type=\"hidden\" name=\"total\" value=\"0,00\">\n";
		echo "</td>\n";
		echo "</tr>\n";
		
	}

	// dta fields for all TAs except TA 890
	if ($taId != 890) {
		// - field id 20: reference number
		echo "<tr>\n";
		echo "<td width=\"40%\" bgcolor=\"silver\">Reference Number (20) * </td>\n";
		echo "<td>\n";
			echo "Ordering Party Information: <input type=\"text\" name=\"orderingPartyIdentification\" size=\"5\" maxlength=\"5\" value=\"$orderingPartyIdentification\">&nbsp;(five characters)<br>\n";
			echo "Ordering Party Transaction Number: <input type=\"text\" name=\"orderingPartyTransactionNumber\" size=\"11\" maxlength=\"11\" value=\"$orderingPartyTransactionNumber\">&nbsp;(eleven characters)\n";
		echo "</td>\n";
		echo "</tr>\n";

		// - field id 25: account to be debited
		echo "<tr>\n";
		echo "<td width=\"40%\" bgcolor=\"silver\">Account to be debited (25) * </td>\n";
		echo "<td>\n";
			echo "Without IBAN: <input type=\"text\" name=\"accountWithoutIban\" size=\"24\" maxlength=\"24\" value=\"$accountWithoutIban\">&nbsp;(sixteen digits plus nine spaces)<br>\n";
			echo "or<br>\n";
			echo "With IBAN: <input type=\"text\" name=\"accountWithIban\" size=\"24\" maxlength=\"24\" value=\"$accountWithIban\">&nbsp;(twenty-one characters plus three spaces)\n";
		echo "</td>\n";
		echo "</tr>\n";

		// - field id 32a: payment amount
		echo "<tr>\n";
		echo "<td width=\"40%\" bgcolor=\"silver\">Payment amount (32a) * </td>\n";
		echo "<td>\n";
			if (in_array($taId, Array(826,827))) {
				echo "Value date:  <input type=\"hidden\" name=\"paymentValueDate\" value=\"      \"><br>\n";
				echo "ISO Currency Code: CHF <input type=\"hidden\" name=\"paymentCurrencyCode\" value=\"CHF\"><br>\n";
				echo "Amount: <input type=\"text\" name=\"paymentAmount\" size=\"12\" maxlength=\"12\" value=\"$paymentAmount\">&nbsp;(twelve digits with decimal point)<br>\n";
			} elseif (in_array($taId, Array(830,832,836,837))) {
				echo "Value date: <input type=\"text\" name=\"paymentValueDate\" size=\"6\" maxlength=\"6\" value=\"$paymentValueDate\"><br>\n";
				echo "ISO Currency Code: <input type=\"text\" name=\"paymentCurrencyCode\" size=\"3\" maxlength=\"3\" value=\"$paymentCurrencyCode\"><br>\n";
				echo "Amount: <input type=\"text\" name=\"paymentAmount\" size=\"15\" maxlength=\"15\" value=\"$paymentAmount\">&nbsp;(fifteen digits with decimal point)<br>\n";
			}
		echo "</td>\n";
		echo "</tr>\n"; 

		// - field id 50: ordering party
		$orderingPartyLineSize = 20; // for TA 826
		$iMax = 4;
		if (in_array($taId, Array(827,830,832,837))) 
		{
			$orderingPartyLineSize = 24;
		} elseif ($taId == 836) {
			$iMax = 3;
			$orderingPartyLineSize = 35;
		}
		echo "<tr>\n";
		echo "<td width=\"40%\" bgcolor=\"silver\">Ordering party (50) * </td>\n";
		echo "<td>\n";
			$orderingPartyLineList = Array("", $orderingPartyLine1, $orderingPartyLine2, $orderingPartyLine3, $orderingPartyLine4);
			$i=1;
			while($i<=$iMax) {
				$v = $orderingPartyLineList[$i];
				echo "#$i: <input type=\"text\" name=\"orderingPartyLine$i\" size=\"$orderingPartyLineSize\" maxlength=\"$orderingPartyLineSize\" value=\"$v\"><br>\n";
				$i++;
			}
		echo "</td>\n";
		echo "</tr>\n"; 
	}
	echo "</table>\n";

	echo "<br>\n";
	echo "<br>\n";

	echo "<table border=\"0\" col=\3\">\n";
	echo "<tr>\n";

	echo "<td>\n";
	echo "<input type=\"submit\" value=\"Validate\">\n";
	echo "</td>\n";

	echo "<td>\n";
	echo "<input type=\"reset\" value=\"Reset\">\n";
	echo "<input type=\"hidden\" name=\"action\" value=\"validate\">\n";
	echo "<input type=\"hidden\" name=\"comingFrom\" value=\"formular\">\n";
	echo "<input type=\"hidden\" name=\"adjust\" value=\"true\">\n";
	echo "</form>\n";
	echo "</td>\n";

	echo "<td>\n";
	echo "<form action=\"dta.php\" method=\"post\">\n";
	echo "<input type=\"submit\" value=\"Choose a different transaction type\">\n";
	echo "<input type=\"hidden\" name=\"action\" value=\"selectagain\">\n";
	echo "<input type=\"hidden\" name=\"comingFrom\" value=\"formular\">\n";
	echo "<input type=\"hidden\" name=\"preSelect\" value=\"$taId\">\n";
	echo "<input type=\"hidden\" name=\"dataFormat\" value=\"$dfId\">\n";
	echo "</form>\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	?>

	<hr>
</body>
</html>
