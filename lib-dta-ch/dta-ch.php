<?php
/*
-----------------------------------------------------------
DTA-CH class

(C) 2015 Frank Hofmann, Berlin, Germany
Released under GNU Public License (GPL)
email frank.hofmann@efho.de
-----------------------------------------------------------
*/

class DTACH {
	function __construct() {

		// define data format
		$this->dataFormat = "";

		// define date of delivery (transaction date)
		$this->dateOfDelivery = "";

		// initialize transaction header
		$this->header = Array(
			"requestedProcessingDate" => "",
			"bankClearingNumberReceiver" => "",
			"outputSequenceNumber" => "",
			"creationDate" => date("ymd"),
			"bankClearingNumberSender" => "",
			"dataFileSenderIdentification" => "",
			"entrySequenceNumber" => 0,
			"transactionType" => 0,
			"paymentType" => 0,
			"processingFlag" => 0
		);

		// initialize transaction fields
		$this->fields = Array();

		// initialize validation fields
		$this->validationResult = Array();
	}

	function getHeader() {
		// return the transaction header
		return $this->header;
	}

	function getFields() {
		// return the transaction fields
		return $this->fields;
	}

	// adjustment functions

	function adjustString($term){
		// the transaction content accepts a limited number of characters, 
		// only, and that's why special characters like accents have to be 
		// transformed

		// invalid characters as a regexp
		$invalidStringRegexp = '/[^A-Z0-9 \.,&\-\/\+\*\$%]/';

		// define a transition table of special characters like accents,
		// and umlauts
		$specialChars = array(
			'á' => 'a',
			'à' => 'a',
			'ä' => 'ae',
			'â' => 'a',
			'ã' => 'a',
			'å' => 'a',
			'æ' => 'ae',
			'ā' => 'a',
			'ă' => 'a',
			'ą' => 'a',
			'ȁ' => 'a',
			'ȃ' => 'a',
			'Á' => 'A',
			'À' => 'A',
			'Ä' => 'Ae',
			'Â' => 'A',
			'Ã' => 'A',
			'Å' => 'A',
			'Æ' => 'AE',
			'Ā' => 'A',
			'Ă' => 'A',
			'Ą' => 'A',
			'Ȁ' => 'A',
			'Ȃ' => 'A',
			'ç' => 'c',
			'ć' => 'c',
			'ĉ' => 'c',
			'ċ' => 'c',
			'č' => 'c',
			'Ç' => 'C',
			'Ć' => 'C',
			'Ĉ' => 'C',
			'Ċ' => 'C',
			'Č' => 'C',
			'ď' => 'd',
			'đ' => 'd',
			'Ď' => 'D',
			'Đ' => 'D',
			'é' => 'e',
			'è' => 'e',
			'ê' => 'e',
			'ë' => 'e',
			'ē' => 'e',
			'ĕ' => 'e',
			'ė' => 'e',
			'ę' => 'e',
			'ě' => 'e',
			'ȅ' => 'e',
			'ȇ' => 'e',
			'É' => 'E',
			'È' => 'E',
			'Ê' => 'E',
			'Ë' => 'E',
			'Ē' => 'E',
			'Ĕ' => 'E',
			'Ė' => 'E',
			'Ę' => 'E',
			'Ě' => 'E',
			'Ȅ' => 'E',
			'Ȇ' => 'E',
			'ĝ' => 'g',
			'ğ' => 'g',
			'ġ' => 'g',
			'ģ' => 'g',
			'Ĝ' => 'G',
			'Ğ' => 'G',
			'Ġ' => 'G',
			'Ģ' => 'G',
			'ĥ' => 'h',
			'ħ' => 'h',
			'Ĥ' => 'H',
			'Ħ' => 'H',
			'ì' => 'i',
			'ì' => 'i',
			'î' => 'i',
			'ï' => 'i',
			'ĩ' => 'i',
			'ī' => 'i',
			'ĭ' => 'i',
			'į' => 'i',
			'ı' => 'i',
			'ĳ' => 'ij',
			'ȉ' => 'i',
			'ȋ' => 'i',
			'Í' => 'I',
			'Ì' => 'I',
			'Î' => 'I',
			'Ï' => 'I',
			'Ĩ' => 'I',
			'Ī' => 'I',
			'Ĭ' => 'I',
			'Į' => 'I',
			'İ' => 'I',
			'Ĳ' => 'IJ',
			'Ȉ' => 'I',
			'Ȋ' => 'I',
			'ĵ' => 'j',
			'Ĵ' => 'J',
			'ķ' => 'k',
			'Ķ' => 'K',
			'ĺ' => 'l',
			'ļ' => 'l',
			'ľ' => 'l',
			'ŀ' => 'l',
			'ł' => 'l',
			'Ĺ' => 'L',
			'Ļ' => 'L',
			'Ľ' => 'L',
			'Ŀ' => 'L',
			'Ł' => 'L',
			'ñ' => 'n',
			'ń' => 'n',
			'ņ' => 'n',
			'ň' => 'n',
			'ŉ' => 'n',
			'Ñ' => 'N',
			'Ń' => 'N',
			'Ņ' => 'N',
			'Ň' => 'N',
			'ó' => 'o',
			'ò' => 'o',
			'ö' => 'oe',
			'ô' => 'o',
			'õ' => 'o',
			'ø' => 'o',
			'ō' => 'o',
			'ŏ' => 'o',
			'ő' => 'o',
			'œ' => 'oe',
			'ȍ' => 'o',
			'ȏ' => 'o',
			'Ó' => 'O',
			'Ò' => 'O',
			'Ö' => 'Oe',
			'Ô' => 'O',
			'Õ' => 'O',
			'Ø' => 'O',
			'Ō' => 'O',
			'Ŏ' => 'O',
			'Ő' => 'O',
			'Œ' => 'OE',
			'Ȍ' => 'O',
			'Ȏ' => 'O',
			'ŕ' => 'r',
			'ř' => 'r',
			'ȑ' => 'r',
			'ȓ' => 'r',
			'Ŕ' => 'R',
			'Ř' => 'R',
			'Ȑ' => 'R',
			'Ȓ' => 'R',
			'ß' => 'ss',
			'ś' => 's',
			'ŝ' => 's',
			'ş' => 's',
			'š' => 's',
			'ș' => 's',
			'Ś' => 'S',
			'Ŝ' => 'S',
			'Ş' => 'S',
			'Š' => 'S',
			'Ș' => 'S',
			'ţ' => 't',
			'ť' => 't',
			'ŧ' => 't',
			'ț' => 't',
			'Ţ' => 'T',
			'Ť' => 'T',
			'Ŧ' => 'T',
			'Ț' => 'T',
			'ú' => 'u',
			'ù' => 'u',
			'ü' => 'ue',
			'û' => 'u',
			'ũ' => 'u',
			'ū' => 'u',
			'ŭ' => 'u',
			'ů' => 'u',
			'ű' => 'u',
			'ų' => 'u',
			'ȕ' => 'u',
			'ȗ' => 'u',
			'Ú' => 'U',
			'Ù' => 'U',
			'Ü' => 'Ue',
			'Û' => 'U',
			'Ũ' => 'U',
			'Ū' => 'U',
			'Ŭ' => 'U',
			'Ů' => 'U',
			'Ű' => 'U',
			'Ų' => 'U',
			'Ȕ' => 'U',
			'Ȗ' => 'U',
			'ŵ' => 'w',
			'Ŵ' => 'W',
			'ý' => 'y',
			'ÿ' => 'y',
			'ŷ' => 'y',
			'Ý' => 'Y',
			'Ÿ' => 'Y',
			'Ŷ' => 'Y',
			'ź' => 'z',
			'ż' => 'z',
			'ž' => 'z',
			'Ź' => 'Z',
			'Ż' => 'Z',
			'Ž' => 'Z',
		);
		
		if (strlen($term) == 0) {
			return "";
		}

		// ensure UTF-8 encoding; for single-byte-encodings use 
		// either the internal encoding or assume ISO-8859-1
		$utf8String = mb_convert_encoding(
			$term,
			"UTF-8",
			array("UTF-8", mb_internal_encoding(), "ISO-8859-1")
		);

		// replace special characters as defined in the table before
		$result = strtr($utf8String, $specialChars);

		// transform to upper case
		$result = strtoupper($result);

		// make sure every special char is replaced by one space, 
		// not two or three
		$result = mb_convert_encoding($result, "ASCII", "UTF-8");
		$result = preg_replace($invalidStringRegexp, ' ', $result);

		// return the converted string
		return $result;
	}

	function adjustNumber($number){
		// adjust numbers 
		// function currently not in use
		return;
	}

	// Data format functions

	function setDataFormat($value) {
		// store the given data format
		$this->dataFormat = $value;
	}

	function getDataFormat() {
		// retrieve the saved data format
		return $this->dataFormat;
	}

	// date of delivery

	function setDateOfDelivery($value){
		// store the given date of delivery
		$this->dateOfDelivery = $value;
		return;
	}

	function getDateOfDelivery() {
		// retrieve the saved date of delivery
		return $this->dateOfDelivery;
	}

	// Header functions

	function setTransactionType($value) {
		// store the given transaction type
		$this->header["transactionType"] = $value;
	}

	function getTransactionType() {
		// retrieve the stored transaction type
		return($this->header["transactionType"]);
	}

	function setRequestedProcessingDate($value) {
		// store the requested processing date of the transaction
		$this->header["requestedProcessingDate"] = $value;
	}

	function getRequestedProcessingDate() {
		// retrieve the stored processing date of the transaction
		return($this->header["requestedProcessingDate"]);
	}

	function setBankClearingNumberReceiver($number) {
		// store the given bank clearing number
		$this->header["bankClearingNumberReceiver"] = $number;
	}

	function getBankClearingNumberReceiver() {
		// retrieve the stored bank clearing number
		return($this->header["bankClearingNumberReceiver"]);
	}

	function setOutputSequenceNumber($value) {
		// store the given transaction sequence number
		$this->header["outputSequenceNumber"] = $value;
	}

	function getOutputSequenceNumber() {
		// get the stored transaction sequence number
		return($this->header["outputSequenceNumber"]);
	}

	function setCreationDate($value) {
		// store the creation date of the transaction
		$this->header["creationDate"] = $value;
	}

	function getCreationDate() {
		// retrieve the stored creation date of the transaction
		return($this->header["creationDate"]);
	}

	function setBankClearingNumberSender($number) {
		// store the bank clearing number of the sender
		$this->header["bankClearingNumberSender"] = $number;
	}

	function getBankClearingNumberSender() {
		// retrieve the bank clearing number of the sender
		return($this->header["bankClearingNumberSender"]);
	}

	function setDataFileSenderIdentification($id) {
		// store the sender identification of the data file
		$this->header["dataFileSenderIdentification"] = $id;
	}

	function getDataFileSenderIdentification() {
		// retrieve the sender identification of the data file
		return($this->header["dataFileSenderIdentification"]);
	}

	function setEntrySequenceNumber($number) {
		// store given entry sequence number
		$this->header["entrySequenceNumber"] = $number;
	}

	function getEntrySequenceNumber() {
		// retrieve stored entry sequence number
		return($this->header["entrySequenceNumber"]);
	}

	function setPaymentType($value) {
		// set given transaction payment type
		$this->header["paymentType"] = $value;
	}

	function getPaymentType() {
		// retrieve stored transaction payment type
		return($this->header["paymentType"]);
	}

	function setProcessingFlag($flag) {
		// store transaction processing flag
		$this->header["processingFlag"] = $flag;
	}

	function getProcessingFlag() {
		// retrieve transaction processing flag
		return($this->header["processingFlag"]);
	}

	// text field functions

	function setBeneficiaryTransferType($value) {
		// store given value as beneficiary transfer type
		$this->fields["beneficiaryTransferType"] = $value;
	}

	function getBeneficiaryTransferType() {
		// retrieve stored beneficiary transfer type
		return $this->fields["beneficiaryTransferType"];
	}

	function hasTextField($fieldId) {
		// verify the existence of a field in the field list
		return array_key_exists($fieldId, $this->fields);
	}

	function getTextFieldValue($fieldId) {
		// return field value if the requested field exists
		if ($this->hasTextField($fieldId)) {
			return $this->fields[$fieldId];
		} else {
			// ... otherwise return False as an error code
			return False;
		}
	}

	function setTextFieldValue($fieldId, $fieldValue) {
		// store the given value in the desired field if this field exists
		if ($this->hasTextField($fieldId)) {
			$this->fields[$fieldId] = $fieldValue;
			// return True if successfull
			return True;
		} else {
			// ... otherwise return False as an error code
			echo "<i>field id $fieldId does not exist.</i>\n";
			return False;
		}
	}

	function addTextField($fieldId, $fieldValue) {
		// extend the field list by the new field, and assign the given
		// value to it
		$this->fields[$fieldId] = $fieldValue;
		return True;		
	}

	function getHeaderFields() {
		// return the list of all the transaction header fields with the
		// according values
		$taHeader = array(
			"transactionType" => $this->getTransactionType(),
			"requestedProcessingDate" => $this->getRequestedProcessingDate(),
			"bankClearingNumberReceiver" => $this->getBankClearingNumberReceiver(),
			"outputSequenceNumber" => $this->getOutputSequenceNumber(),
			"creationDate" => $this->getCreationDate(),
			"bankClearingNumberSender" => $this->getBankClearingNumberSender(),
			"dataFileSenderIdentification" => $this->getDataFileSenderIdentification(),
			"entrySequenceNumber" => $this->getEntrySequenceNumber(),
			"paymentType" => $this->getPaymentType(),
			"processingFlag" => $this->getProcessingFlag()
		);
		return $taHeader;
	}

	function getIndividualFields(){
		// return the list of all the individual fields with the
		// according values

		// retrieve the current type of transaction
		$taId = $this->getTransactionType();

		// define a basic list of fields that all the transactions have
		// in common
		$basicList = array(
			"orderingPartyIdentification",
			"orderingPartyTransactionNumber",
			"orderingPartyLine1",
			"orderingPartyLine2",
			"orderingPartyLine3",
			"paymentValueDate",
			"paymentCurrencyCode",
			"paymentAmount",
			"accountWithIban", 
			"accountWithoutIban",
			"beneficiaryPartyLine1",
			"beneficiaryPartyLine2",
			"beneficiaryPartyLine3"
		);

		// define an individual list per transaction type as described
		// in the SIX specification
		$taList = array();

		switch ($taId) {
			case 826:
				$taList = array(
					"orderingPartyLine4",
					"beneficiaryPartyIdentification",
					"beneficiaryPartyLine4",
					"isrReferenceNumber",
					"isrCheckDigit"
				);
				break;
			case 827:
				$taList = array(
					"orderingPartyLine4",
					"beneficiaryTransferType",
					"beneficiaryPartyAccount",
					"beneficiaryPartyLine4",
					"beneficiaryMessageLine1",
					"beneficiaryMessageLine2",
					"beneficiaryMessageLine3",
					"beneficiaryMessageLine4",
					"endBeneficiaryPartyAccount",
					"endBeneficiaryPartyLine1",
					"endBeneficiaryPartyLine2",
					"endBeneficiaryPartyLine3",
					"endBeneficiaryPartyLine4"
				);
				break;
			case 830:
				$taList = array(
					"convertionRate",
					"orderingPartyLine4",
					"identificationBankAddress",
					"beneficiaryInstituteLine1",
					"beneficiaryInstituteLine2",
					"beneficiaryInstituteLine3",
					"beneficiaryInstituteLine4",
					"beneficiaryInstituteLine5",
					"beneficiaryPartyAccount",
					"beneficiaryPartyLine4",
					"beneficiaryMessageLine1",
					"beneficiaryMessageLine2",
					"beneficiaryMessageLine3",
					"beneficiaryMessageLine4",
					"bankPaymentInstruction"
				);
				break;
			case 832:
				$taList = array(
					"convertionRate",
					"orderingPartyLine4",
					"beneficiaryPartyAccount",
					"beneficiaryPartyLine4",
					"beneficiaryMessageLine1",
					"beneficiaryMessageLine2",
					"beneficiaryMessageLine3",
					"beneficiaryMessageLine4",
					"bankPaymentInstruction"
				);
				break;
			case 836:
				$taList = array(
					"convertionRate",
					"identificationBankAddress",
					"beneficiaryInstituteLine1",
					"beneficiaryInstituteLine2",
					"iban",
					"purposeStructure",
					"purposeLine1",
					"purposeLine2",
					"purposeLine3",
					"rulesForCharges"
				);
				break;
			case 837:
				$taList = array(
					"convertionRate",
					"orderingPartyLine4",
					"identificationBankAddress",
					"beneficiaryInstituteLine1",
					"beneficiaryInstituteLine2",
					"beneficiaryInstituteLine3",
					"beneficiaryInstituteLine4",
					"beneficiaryInstituteLine5",
					"iban",
					"beneficiaryPartyAccount",
					"beneficiaryPartyLine4",
					"purposeStructure",
					"purposeLine1",
					"purposeLine2",
					"purposeLine3",
					"rulesForCharges",
					"informationStructure",
					"instructionLine1",
					"instructionLine2",
					"instructionLine3",
					"instructionLine4"
				);
				break;
			}

			// combine the two lists of fields
			$fullList = array_merge($basicList, $taList);

			// retrieve the according field values for this transaction
			$taFull = array();

			foreach ($fullList as $entry) {
				$v = $this->getTextFieldValue($entry);
				if ($v != False) {
					$taFull[$entry] = $v;
					// echo "$entry: $v<br>\n";
				} else {
					echo "<i>cannot associate $entry</i>\n";
				}
			}
		// return the full list
		return $taFull;
	}

	function getEntryDescriptions(){
		// define the descriptions for the entry fields
		// helpful to design input fields

		$descriptions = array(
			"accountWithIban" => "account with IBAN",
			"accountWithoutIban" => "account without IBAN",
			//"accountToBeDebited" => "account to be debited",
			"bankClearingNumberReceiver" => "bank clearing number receiver",
			"bankClearingNumberSender" => "bank clearing number sender",
			"bankPaymentInstruction" => "bank payment instruction",
			//"beneficiarySwiftAddress" => "beneficiary institution",
			"beneficiaryInstituteLine1" => "beneficiary institute line 1",
			"beneficiaryInstituteLine2" => "beneficiary institute line 2",
			"beneficiaryInstituteLine3" => "beneficiary institute line 3",
			"beneficiaryInstituteLine4" => "beneficiary institute line 4",
			"beneficiaryInstituteLine5" => "beneficiary institute line 5",
			"beneficiaryMessageLine1" => "beneficiary message line 1",
			"beneficiaryMessageLine2" => "beneficiary message line 2",
			"beneficiaryMessageLine3" => "beneficiary message line 3",
			"beneficiaryMessageLine4" => "beneficiary message line 4",
			"beneficiaryPartyIdentification" => "beneficiary party identification",
			"beneficiaryPartyAccount" => "beneficiary party account",
			"beneficiaryPartyLine1" => "beneficiary party line 1",
			"beneficiaryPartyLine2" => "beneficiary party line 2",
			"beneficiaryPartyLine3" => "beneficiary party line 3",
			"beneficiaryPartyLine4" => "beneficiary party line 4",
			"beneficiaryTransferType" => "beneficiary transfer type",
			"convertionRate" => "convertion rate",
			"creationDate" => "creation date",
			"dataFileSenderIdentification" => "data file sender identification",
			// "dateOfDelivery" => "date of delivery",
			"endBeneficiaryPartyAccount" => "end beneficiary party account",
			"endBeneficiaryPartyLine1" => "end beneficiary party line 1",
			"endBeneficiaryPartyLine2" => "end beneficiary party line 2",
			"endBeneficiaryPartyLine3" => "end beneficiary party line 3",
			"endBeneficiaryPartyLine4" => "end beneficiary party line 4",
			"endBeneficiaryPartyLine5" => "end beneficiary party line 5",
			"entrySequenceNumber" => "entry sequence number",
			"iban" => "iban",
			"identificationBankAddress" => "identification bank address",
			"informationStructure" => "information structure",
			"instructionLine1" => "instruction line 1",
			"instructionLine2" => "instruction line 2",
			"instructionLine3" => "instruction line 3",
			"instructionLine4" => "instruction line 4",
			"isrCheckDigit" => "isr check digit",
			"isrReferenceNumber" => "isr reference number",
			"orderingPartyIdentification" => "ordering party identification",
			"orderingPartyLine1" => "ordering party line 1",
			"orderingPartyLine2" => "ordering party line 2",
			"orderingPartyLine3" => "ordering party line 3",
			"orderingPartyLine4" => "ordering party line 4",
			"orderingPartyTransactionNumber" => "ordering party transaction number",
			"outputSequenceNumber" => "output sequence number",
			"paymentAmount" => "payment amount",
			"paymentCurrencyCode" => "payment currency code",
			"paymentType" => "payment type",
			"paymentValueDate" => "payment value date",
			"processingFlag" => "processing flag",
			// "referenceNumber" => "reference number",
			"purposeStructure" => "purpose structure",
			"purposeLine1" => "purpose line 1",
			"purposeLine2" => "purpose line 2",
			"purposeLine3" => "purpose line 3",
			"requestedProcessingDate" => "requested processing date",
			"rulesForCharges" => "rules for charges",
			"transactionType" => "transaction type"
		);
		return $descriptions;
	}

	//function removeTextField($fieldId)
	//{
	//}

	//function clearTextField($fieldId)
	//{
		//return setTextFieldValue($fieldId, "");
	//}

	//function countTextFields()
	//{
	//}

	// validation functions

	function validateRequestedProcessingDate(){
		// validate the requested transaction processing date 

		// retrieve the transaction type
		$transaction = $this->getTransactionType();

		// define a list of possible transaction types
		$transactionList = Array(826, 827);

		// retrieve the stored transaction processing date
		$requestedProcessingDate = $this->getRequestedProcessingDate();

		// retrieve the stored date of delivery
		$dateOfDelivery = $this->getDateOfDelivery();

		// define two regex patterns to validate the date
		$pattern1 = '/^\d{2}((0[1-9])|(1[0-2]))((0[1-9])|([1,2]\d)|(3[0,1]))$/';
		$pattern2 = '/^0{6}$/';

		// does the transaction type match?
		if(in_array($transaction, $transactionList)) {
			
			// does the requested transaction processing date matches
			// the regex pattern? (Is the format correct?)
			if (preg_match($pattern1, $requestedProcessingDate)) {

				// the numbers are valid, somehow
				// ... so check further
				// assume: we do not have calculations before the year 2000
				// extract year, month, and day from the given date
				$year = "20" . substr($requestedProcessingDate, 0, 2);
				$month = substr($requestedProcessingDate, 2, 2);
				$day = substr($requestedProcessingDate, 4, 2);

				// validate the date
				if (checkdate($month,$day,$year)) {

					// compare with date of delivery:
					// max. 10 calendar days prior to date of delivery
					// max. 60 calendar days following the date of delivery
					// assume: we do not have calculations before 2000
					$year2 = "20" . substr($dateOfDelivery, 0, 2);
					$month2 = substr($dateOfDelivery, 2, 2);
					$day2 = substr($dateOfDelivery, 4, 2);

					$datetime1 = new DateTime("$year-$month-$day");
					$datetime2 = new DateTime("$year2-$month2-$day2");

					// define processing range
					// - 10 days before
					$dateMin = new DateTime("$year2-$month2-$day2");
					$dateMin->sub(new DateInterval('P10D'));

					// - 60 days after
					$dateMax = new DateTime("$year2-$month2-$day2");
					$dateMax->add(new DateInterval('P60D'));

					// calculate minimal, and max. date as timestamp
					$min = $dateMin->format('Y-m-d');
					$max = $dateMax->format('Y-m-d');
					//echo "Bereich: $min bis $max";

					// check whether the date is within the possible range
					if($datetime1 > $dateMin) {
						if($datetime1 < $dateMax) {
							return True;
						}
					}
				}
			}
		} else {
			// check for an empty date field: 6x0
			if (preg_match($pattern2, $requestedProcessingDate)) {
				return True;
			}
		}
		// the validations failed, and so the return value is set to False
		return False;
	}

	function adjustRequestedProcessingDate(){
		// auto-correct the requested transaction processing date 

		// retrieve the transaction type
		$transaction = $this->getTransactionType();

		// define a list of possible transaction types
		$transactionList = Array(826, 827);

		// retrieve the stored transaction processing date
		$requestedProcessingDate = $this->getRequestedProcessingDate();
		
		// does the transaction type match?
		if(in_array($transaction, $transactionList)) {
			// remove all whitespaces
			$requestedProcessingDate = trim($requestedProcessingDate);

			// remove all non-digits 
			$requestedProcessingDate = preg_replace('/[^\d]/', '', $requestedProcessingDate);
		} else {
			// set to "000000"
			$requestedProcessingDate = str_repeat("0", 6);
		}

		// update value of requested transaction processing date
		$this->setRequestedProcessingDate($requestedProcessingDate);
		return;
	}

	function validateBankClearingNumberReceiver() {
		// validate the bank clearing number of the transaction receiver

		// retrieve the transaction type
		$transactionType = $this->getTransactionType();

		// read the according bank clearing number
		$bankClearingNumberReceiver = $this->getBankClearingNumberReceiver();

		// identify the beneficary transfer type
		$beneficiaryTransferType = $this->getBeneficiaryTransferType();

		// check the transaction type
		if($transactionType == 827) {
			if ($beneficiaryTransferType == "bankPayment") {
				// define a pattern for the bank payment
				$pattern = '/^\d+\s*$/';
			} else {
				// define a pattern for the postal payment
				$pattern = '/^\s{12}$/';
			}

			// validate both pattern, and the string length
			if (preg_match($pattern, $bankClearingNumberReceiver)) {
				if (strlen($bankClearingNumberReceiver) == 12) {
					return True;
				}
			}
		} else {
			// define a pattern for the postal payment
			$pattern = '/^\s{12}$/';

			// validate the pattern
			if (preg_match($pattern, $bankClearingNumberReceiver)) {
				return True;
			}
		}

		// if still unmet, return False
		return False;
	}

	function adjustBankClearingNumberReceiver() {
		// auto-correct the bank clearing number of the receiver

		// retrieve the bank clearing number of the receiver
		$bankClearingNumberReceiver = $this->getBankClearingNumberReceiver();

		// remove all non-digits 
		$bankClearingNumberReceiver = preg_replace('/[^\d]+/', '', $bankClearingNumberReceiver);

		// to meet the desired field length of 12 characters add as many
		// spaces as necessary on the right end
		$bankClearingNumberReceiver = str_pad($bankClearingNumberReceiver,12," ", STR_PAD_RIGHT);

		// update the value for the bank clearing number of the receiver
		$this->setBankClearingNumberReceiver($bankClearingNumberReceiver);
		return;
	}

	function validateOutputSequenceNumber(){
		// validate the output sequence number of the transaction
		// has to consist of five digits

		// retrieve the output sequence number of the transaction
		$outputSequenceNumber = $this->getOutputSequenceNumber();

		// define the validation pattern
		$pattern = '/^\d{5}$/';
		if (preg_match($pattern, $outputSequenceNumber)) {
			// in case the pattern fits the value return True
			return True;
		}

		// ... otherwise False
		return False;
	}

	function adjustOutputSequenceNumber(){
		// auto-correct the output sequence number of the transaction

		// retrieve the output sequence number of the transaction
		$outputSequenceNumber = $this->getOutputSequenceNumber();

		// define the validation pattern: "00000"
		$pattern = '/^0{5}$/';

		// verify the output sequence number, and adjust if necessary
		if (preg_match($pattern, $outputSequenceNumber) == False) {
			$outputSequenceNumber = "00000";

			// update the value, and store the new content
			$this->setOutputSequenceNumber($outputSequenceNumber);
		}
		return;
	}

	function validateCreationDate(){
		// validate the creation date of the transaction

		// retrieve the stored creation date of the transaction
		$creationDate = $this->getCreationDate();

		// define the validation pattern
		$datePattern = '/^\d{2}((0[1-9])|(1[0-2]))((0[1-9])|([1,2]\d)|(3[0,1]))$/';

		// verify the creation date
		if (preg_match($datePattern, $creationDate)) {
			// ... valid, so return True
			return True;
		}
		// ... invalid, so return False
		return False;
	}

	function adjustCreationDate(){
		// auto-adjust the creation date of the transaction

		// retrieve the stored creation date of the transaction
		$creationDate = $this->getCreationDate();

		// define the validation pattern
		$datePattern = '/^\d{2}((0[1-9])|(1[0-2]))((0[1-9])|([1,2]\d)|(3[0,1]))$/';

		// verify the creation date
		if (preg_match($datePattern, $creationDate)) {
			// creation date is invalid
			// substitute the date by the current date, instead
			$creationDate = date("ymd");

			// update the value of creation date of the transaction
			$this->setCreationDate($creationDate);
		}
		return;
	}

	function validateBankClearingNumberSender(){
		// validate the bank clearing number of the sender

		// retrieve the stored bank clearing number of the sender
		$bankClearingNumberSender = $this->getBankClearingNumberSender();

		// retrieve the current type of transaction
		$transactionType = $this->getTransactionType();
		if ($transactionType == 890) {
			// define pattern for empty spaces
			$bankPattern = '/^\s*$/';
		} else {
			// define pattern of digits and spaces
			$bankPattern = '/^\d+\s*$/';
		}

		// does the bank clearing number matches the pattern?
		if (preg_match($bankPattern, $bankClearingNumberSender)) {
			// does the bank clearing number has a length of seven characters?
			if (strlen($bankClearingNumberSender) == 7) {
				// ... yes: return True
				return True;
			}
		}
		// ... no: return False
		return False;
	}

	function adjustBankClearingNumberSender(){
		// auto-adjust the bank clearing number of the sender

		// retrieve the stored bank clearing number of the sender
		$bankClearingNumberSender = $this->getBankClearingNumberSender();

		// define a regex pattern to remove all non-digits from 
		// the current value
		$bankClearingNumberSender = preg_replace('/[^\d]+/', '', $bankClearingNumberSender);

		// extend the bank clearing number by spaces to achieve a 
		// length of seven characters
		$bankClearingNumberSender = str_pad($bankClearingNumberSender,7," ", STR_PAD_RIGHT);

		// update bank clearing number
		$this->setBankClearingNumberSender($bankClearingNumberSender);
		return;
	}

	function validateDataFileSenderidentification(){
		// validate the data file sender identification

		// retrieve the stored data file sender identification
		$dataFileSenderIdentification = $this->getDataFileSenderidentification();

		// define a regex pattern to match the identification id
		$pattern = '/^[\dA-Z]{5}$/';

		// does the pattern matches the identification id?
		if (preg_match($pattern, $dataFileSenderIdentification)) {
			// does the identification id has a length of five characters?
			if(strlen($dataFileSenderIdentification) == 5) {
				// ... yes: return True
				return True;
			}
		}

		// ... no: return False
		return False;
	}

	function adjustDataFileSenderidentification(){
		// auto-adjust the data file sender identification

		// retrieve the stored data file sender identification
		$dataFileSenderIdentification = $this->getDataFileSenderidentification();

		// define a regex pattern to remove all non-digits plus letters 
		// from the current value
		$dataFileSenderIdentification = preg_replace('/[^\dA-Z]+/', '', $dataFileSenderIdentification);

		// extend the data file sender identification by spaces to
		// achieve a length of seven characters
		$dataFileSenderIdentification = str_pad($dataFileSenderIdentification,5," ", STR_PAD_RIGHT);

		// update the value for the data file sender identification
		$this->setDataFileSenderIdentification($dataFileSenderIdentification);
		return;
	}

	function validateEntrySequenceNumber(){
		// validate the entry sequence number

		// retrieve the stored entry sequence number
		$entrySequenceNumber = $this->getEntrySequenceNumber();

		// define a regex pattern of five digits
		$pattern = '/^\d{5}$/';

		// does the pattern match the entry sequence number?
		if (preg_match($pattern, $entrySequenceNumber)) {
			// does the length fit?
			if(strlen($entrySequenceNumber) == 5) {
				// ... yes: return True
				return True;
			}
		}
		// ... no: return False
		return False;
	}

	function adjustEntrySequenceNumber(){
		// auto-adjust the entry sequence number

		// retrieve the stored entry sequence number
		$entrySequenceNumber = $this->getEntrySequenceNumber();

		// remove all non-digits from the entry sequence number
		$entrySequenceNumber = preg_replace('/[^\d]+/', '', $entrySequenceNumber);

		// verify the length of the entry sequence number
		if (strlen($entrySequenceNumber) == 0) {
			// if zero set it to "00001"
			$entrySequenceNumber = "00001";
		} else {
			// otherwise add as many zeros as needed starting from the left
			$entrySequenceNumber = str_pad($entrySequenceNumber,5,"0", STR_PAD_LEFT);		
		}

		// update the entry sequence number
		$this->setEntrySequenceNumber($entrySequenceNumber);
		return;
	}

	function validatePaymentType(){
		// validate the payment type

		// retrieve the stored payment type
		$paymentType = $this->getPaymentType();

		// retrieve the stored transaction type
		$transactionType = $this->getTransactionType();

		// paymentType 1 is allowed for TA 827, 836, and 837, only
		if ($paymentType == 1) {
			if (in_array($transactionType, Array(827,836,837))){
				return True;
			}
		} else {
			return True;
		}
		return False;
	}

	function adjustPaymentType(){
		// auto-adjust the payment type

		// retrieve the stored payment type
		$paymentType = $this->getPaymentType();

		// retrieve the stored transaction type
		$transactionType = $this->getTransactionType();

		// paymentType 1 is allowed for TA 827, 836, and 837, only
		if ($paymentType == 1) {
			if (in_array($transactionType, Array(827,836,837)) == False){
				// set it back to paymentType 0
				$this->setPaymentType(0);
			}
		}
		return;
	}

	function validateProcessingFlag() {
		// validate the processing flag

		// retrieve the stored processing flag
		$processingFlag = $this->getProcessingFlag();

		// it has to be zero
		if ($processingFlag == 0) {
			return True;
		}
		return False;
	}

	function adjustProcessingFlag(){
		// auto-adjust the processing flag

		// it has to be zero. Update the processing flag.
		$this->setProcessingFlag(0);
		return;
	}

	function validateHeader() {
		// validate the whole transaction header

		// requestedProcessingDate
		$v = $this->validateRequestedProcessingDate();
		$this->validationResult["requestedProcessingDate"] = $v;

		// bankClearingNumberReceiver
		$v = $this->validateBankClearingNumberReceiver();
		$this->validationResult["bankClearingNumberReceiver"] = $v;

		// outputSequenceNumber
		$v = $this->validateOutputSequenceNumber();
		$this->validationResult["outputSequenceNumber"] = $v;

		// creationDate
		$v = $this->validateCreationDate();
		$this->validationResult["creationDate"] = $v;

		// bankClearingNumberSender
		$v = $this->validateBankClearingNumberSender();
		$this->validationResult["bankClearingNumberSender"] = $v;
	
		// dataFileSenderIdentification
		$v = $this->validateDataFileSenderidentification();
		$this->validationResult["dataFileSenderIdentification"] = $v;

		// entrySequenceNumber
		$v = $this->validateEntrySequenceNumber();
		$this->validationResult["entrySequenceNumber"] = $v;

		// transactionType
		if (in_array($this->getTransactionType(), Array(826,827,830,832,836,837,890))) {
			$this->validationResult["transactionType"] = True;
		}

		// paymentType
		$v = $this->validatePaymentType();
		$this->validationResult["paymentType"] = $v;

		// processingFlag
		$v = $this->validateProcessingFlag();
		$this->validationResult["processingFlag"] = $v;

		return;
	}

	function adjustHeader() {
		// auto-adjust the transaction header

		// requestedProcessingDate
		$this->adjustRequestedProcessingDate();

		// bankClearingNumberReceiver
		$this->adjustBankClearingNumberReceiver();

		// outputSequenceNumber
		$this->adjustOutputSequenceNumber();

		// creationDate
		$this->adjustCreationDate();

		// bankClearingNumberSender
		$this->adjustBankClearingNumberSender();
	
		// dataFileSenderIdentification
		$this->adjustDataFileSenderidentification();

		// entrySequenceNumber
		$this->adjustEntrySequenceNumber();

		// transactionType
		$this->adjustBeneficiaryTransactionType();

		// paymentType
		$this->adjustPaymentType();

		// processingFlag
		$this->adjustProcessingFlag();

		return;
	}

	function validateDataFormat() {
		// validate the transmitted data format

		// retrieve the stored data format
		$dataFormat = $this->getDataFormat();

		// the data format has to be either fixed, or variable
		// if so return True otherwise False
		return in_array($dataFormat, Array("fixed", "variable"));
	}

	function validateReferenceNumber() {
		// validate the transaction reference number

		// retrieve the stored ordering party identification
		$orderingPartyIdentification = $this->validationResult["orderingPartyIdentification"];

		// retrieve the party transaction number
		$orderingPartyTransactionNumber = $this->validationResult["orderingPartyTransactionNumber"];

		if ($orderingPartyIdentification AND $orderingPartyTransactionNumber) {
			// return True if both exist
			return True;
		}

		// ... otherwise False
		return False;
	}

	function validateOrderingPartyIdentification() {
		// validate the identification of the ordering party

		// retrieve the stored identification of the ordering party
		$orderingPartyIdentification = $this->getTextFieldValue("orderingPartyIdentification");

		// define a regex pattern that consists of five letters or digits
		$pattern = '/^[\dA-Z]{5}$/';

		// if the pattern matches the identification ...
		if (preg_match($pattern, $orderingPartyIdentification)) {
			// ... and the length of the string is five ...
			if(strlen($orderingPartyIdentification) == 5) {
				// ... the identification of the ordering party is valid
				return True;
			}
		}
		// ... otherwise it is invalid
		return False;
	}

	function adjustOrderingPartyIdentification() {
		// auto-adjust the identification of the ordering party

		// retrieve the stored identification of the ordering party
		$orderingPartyIdentification = $this->getTextFieldValue("orderingPartyIdentification");

		// define a regex pattern that consists of non-letters and non-digits
		// remove all characters that match this pattern from the identification
		$orderingPartyIdentification = preg_replace('/[^\dA-Z]+/', '', $orderingPartyIdentification);

		// add as many spaces as needed starting from the right
		$orderingPartyIdentification = str_pad($orderingPartyIdentification,5," ", STR_PAD_RIGHT);

		// update the value of the identification of the ordering party
		$this->setTextFieldValue("orderingPartyIdentification",$orderingPartyIdentification);

		return;
	}

	function validateOrderingPartyTransactionNumber() {
		// validate the transaction number of the ordering party

		// retrieve the transaction number of the ordering party
		$orderingPartyTransactionNumber = $this->getTextFieldValue("orderingPartyTransactionNumber");

		// define a regex pattern of 11 digits
		$pattern = '/^\d{11}$/';

		// compare the pattern with the transaction number of the ordering party
		if (preg_match($pattern, $orderingPartyTransactionNumber)) {

			// make sure that the length of the transaction number has
			// the correct length
			if(strlen($orderingPartyTransactionNumber) == 11) {
				return True;
			}
		}

		// ... return False
		return False;
	}

	function adjustOrderingPartyTransactionNumber() {
		// auto-adjust the transaction number of the ordering party

		// retrieve the transaction number of the ordering party
		$orderingPartyTransactionNumber = $this->getTextFieldValue("orderingPartyTransactionNumber");

		// substitute/remove all non-digits
		$orderingPartyTransactionNumber = preg_replace('/[^\d]+/', '', $orderingPartyTransactionNumber);

		// pad the number with the according number of zeros to achieve
		// a string length of 11 characters
		$orderingPartyTransactionNumber = str_pad($orderingPartyTransactionNumber,11,"0", STR_PAD_LEFT);

		// update the value for the transaction number of the ordering party
		$this->setTextFieldValue("orderingPartyTransactionNumber",$orderingPartyTransactionNumber);
		return;
	}

	function validateAccountToBeDebited(){
		// validate the account information

		// retrieve the account information (without IBAN) flag
		$withoutIban = $this->validateAccountWithoutIban();

		// retrieve the account information (with IBAN) flag
		$withIban = $this->validateAccountWithIban();

		// retrieve the account information (without IBAN)
		$accountWithoutIban = $this->getTextFieldValue("accountWithoutIban");

		// retrieve the account information flag (with IBAN)
		$accountWithIban = $this->getTextFieldValue("accountWithIban");

		// validate the "without IBAN" field
		if($withoutIban) {
			if (strlen(trim($accountWithIban)) == 0) {
				// in case we have no entry enable both flags
				$this->validationResult["accountWithoutIban"] = True;
				$this->validationResult["accountWithIban"] = True;
				return True;
			}
		}

		// validate the "with IBAN" field
		if($withIban) {
			if (strlen(trim($accountWithoutIban)) == 0) {
				// in case we have no entry enable both flags
				$this->validationResult["accountWithoutIban"] = True;
				$this->validationResult["accountWithIban"] = True;
				return True;
			}
		}

		// ... return error flag
		return False;
	}

	function validateAccountWithoutIban(){
		// validate the account information without IBAN

		// retrieve the transaction type
		$transactionType = $this->getTransactionType();

		// set default length to 24
		$length = 24;
		if($transactionType == 837){
			// except for TA 837: 34
			$length = 34;
		}

		// retrieve the account information without IBAN
		$accountWithoutIban = $this->getTextFieldValue("accountWithoutIban");

		// define validation pattern of up to 16 digits, followed 
		// by at least 8 spaces
		$pattern = '/\d{1,16}\s{8,}/';

		// verify the string length
		if (strlen($accountWithoutIban) == $length) {
			if (preg_match($pattern, $accountWithoutIban)) {
				// return True in case the pattern matches
				return True;
			}

			// check for an empty string
			if (trim($accountWithoutIban) == "") {
				return True;
			}
		}
		// ... otherwise return False to indicate an error
		return False;
	}

	function adjustAccountWithoutIban(){
		// auto-update the account information without IBAN

		// retrieve the transaction type
		$transactionType = $this->getTransactionType();

		// set default length to 24
		$length = 24;
		if($transactionType == 837){
			// except for TA 837: 34
			$length = 34;
		}

		// retrieve the account information without IBAN
		$accountWithoutIban = $this->getTextFieldValue("accountWithoutIban");

		// replace/remove all non-digits from the string
		$accountWithoutIban = preg_replace('/[^\d]/', '', $accountWithoutIban );

		// extend the string by spaces to achieve the needed length
		$accountWithoutIban = str_pad($accountWithoutIban ,$length," ", STR_PAD_RIGHT);

		// update the account information without IBAN
		$this->setTextFieldValue("accountWithoutIban",$accountWithoutIban);
		return;
	}

	function validateAccountWithIban(){
		// validate the account information with IBAN

		// retrieve the transaction type
		$transactionType = $this->getTransactionType();

		// set default length to 24
		$length = 24;
		if($transactionType == 837){
			// except for TA 837: 34
			$length = 34;
		}

		// retrieve the account information with IBAN
		$accountWithIban = $this->getTextFieldValue("accountWithIban");

		// define a regex pattern for IBANs that are valid for either
		// Switzerland, or Liechtenstein
		$pattern = '/^((CH)|(LI))\d+\s{3,}$/';

		// validate the account information
		if (preg_match($pattern, $accountWithIban)) {
			// does the length fit?
			if (strlen($accountWithIban) == $length) {
				return True;
			}
		}
		return False;
	}

	function isIban($accountNumber) {
		// validate IBAN number

		// define regex validation pattern: 
		// two characters followed by digits
		$pattern = '/[A-Z][A-Z]\d+$/';

		// validate the account number
		if (preg_match($pattern, trim($accountNumber))) {
			// valid according to the pattern
			return True;
		}
		// invalid according to the pattern
		return False;
	}

	function adjustAccountWithIban(){
		// auto-update the account information with IBAN

		// retrieve the transaction type
		$transactionType = $this->getTransactionType();

		// retrieve the trimmed account information
		$accountWithIban = trim($this->getTextFieldValue("accountWithIban"));

		// define string length: 24
		$length = 24;
		if($transactionType == 837){
			// except for TA 837: 34
			$length = 34;
		}

		// extend the string by spaces to achieve the needed string length
		$accountWithIban = str_pad($accountWithIban ,$length," ", STR_PAD_RIGHT);

		// update value for the account information with IBAN
		$this->setTextFieldValue("accountWithIban",$accountWithIban);
		return;
	}

	function validateFullPaymentAmount() {
		// validate full payment
		return False;
	}

	function validatePaymentValueDate() {
		// validate the payment value date

		// retrieve the payment value date
		$paymentValueDate = $this->getTextFieldValue("paymentValueDate");

		// retrieve the type of transaction 
		$transactionType = $this->getTransactionType();

		// define the regex validation pattern: 6 spaces
		$pattern = '/^\s{6}$/';

		// define the regex date pattern
		$datePattern = '/^\d{2}((0[1-9])|(1[0-2]))((0[1-9])|([1,2]\d)|(3[0,1]))$/';

		// check for an empty value date (TA 826 and 827, only)
		if (in_array($transactionType, Array(826,827))) {
			if (preg_match($pattern, $paymentValueDate)) {
				return True;
			}
		} elseif (in_array($transactionType, Array(830,832,836,837))) {
			// check for a regular date (TA 830, 832, 836, and 837)
			if (preg_match($datePattern, $paymentValueDate)) {
				return True;
			}
		}
		return False;
	}

	function adjustPaymentValueDate() {
		// auto-adjust the payment value date

		// retrieve the payment value date
		$paymentValueDate = $this->getTextFieldValue("paymentValueDate");

		// retrieve type of transaction
		$transactionType = $this->getTransactionType();

		if (in_array($transactionType, Array(826,827))) {
			// for TA 826 and 826 the date is empty (6x space)
			$paymentValueDate = "      ";
		} elseif (in_array($transactionType, Array(830,832,836,837))) {
			// otherwise: validate the given date
			// define regex date pattern
			$datePattern = '/^\d{2}((0[1-9])|(1[0-2]))((0[1-9])|([1,2]\d)|(3[0,1]))$/';

			if (preg_match($datePattern, $paymentValueDate) == False) {
				// in case it is invalid use the today's date, instead
				$paymentValueDate = date("ymd");
			}
		}

		// update the payment value date
		$this->setTextFieldValue("paymentValueDate",$paymentValueDate);
		return;
	}

	function validatePaymentCurrencyCode() {
		// validate the payment currency code

		// retrieve the payment currency code
		$paymentCurrencyCode = $this->getTextFieldValue("paymentCurrencyCode");

		// retrieve the type of transaction
		$transactionType = $this->getTransactionType();

		if (in_array($transactionType, Array(826,827))) {
			// allowed currency for TA 826 and 827: CHF, only
			if ($paymentCurrencyCode == "CHF") {
				return True;
			}
		} elseif (in_array($transactionType, Array(830,832,836,837))) {
			// otherwise: all currencies

			// define regex currency pattern: 3 upper-case characters
			$pattern = '/^[A-Z]{3}$/';

			// validate the currency code
			if (preg_match($pattern, $paymentCurrencyCode)) {
				return True;
			}
		}
		// ... currency code is wrong, so return False
		return False;
	}

	function adjustPaymentCurrencyCode() {
		// auto-adjust the payment currency code

		// retrieve the payment currency code
		$paymentCurrencyCode = $this->getTextFieldValue("paymentCurrencyCode");

		// retrieve the type of transaction
		$transactionType = $this->getTransactionType();

		if (in_array($transactionType, Array(826,827))) {
			// allowed currency for TA 826 and 827: CHF, only
			$paymentCurrencyCode == "CHF";
		} elseif (in_array($transactionType, Array(830,832,836,837))) {
			
			// remove all non-upper-case letters
			$paymentCurrencyCode = preg_replace('/[^A-Z]/', '', $paymentCurrencyCode );

			// extend the currency code by space to ensure 
			// a three-character string
			$paymentCurrencyCode = str_pad($paymentCurrencyCode,3," ", STR_PAD_RIGHT);
		}

		// update the payment currency code
		$this->setTextFieldValue("paymentCurrencyCode",$paymentCurrencyCode);
		return;
	} 

	function validatePaymentAmount() {
		// validate the payment amount

		// define various regex patterns for payment amounts
		// - 7.2
		$pattern5 = '/^\d{1,7},\d{1,2}\s*$/';

		// - 8.2
		$pattern9 = '/^\d{1,8},\d{1,2}\s*$/';

		// - 10.2
		$pattern15 = '/^\d{1,10},\d{1,2}\s*$/';

		// - /C/xxxxxxxxx
		$patternISR9 = '/^\/C\/\d{9}$/';

		// - /C/0000xxxxxxxxx
		$patternISR5 = '/^\/C\/0000\d{5}$/';

		// - 9.2
		$patternAccount = '/^\d{1,9},\d{1,2}\s*$/';

		// - 6.2
		$patternPostalOrder = '/^\d{1,6},\d{1,2}\s*$/';

		// retrieve payment amount
		$paymentAmount = $this->getTextFieldValue("paymentAmount");

		// retrieve identification id for the beneficiary party
		$isr = $this->getTextFieldValue("beneficiaryPartyIdentification");

		// retrieve the type of transaction
		$transactionType = $this->getTransactionType();

		// - for TA 826
		if ($transactionType == 826) {
			// distinguish between ISR5 and ISR9 payments
			if (preg_match($patternISR5, $isr)) {
				if (preg_match($pattern5, $paymentAmount)) {
					return True;
				}
			} elseif (preg_match($patternISR9, $isr)) {
				if (preg_match($pattern9, $paymentAmount)) {
					return True;
				}
			}
		}

		// - for TA 827
		if ($transactionType == 827) {
			// retrieve transfer type
			$transferType = $this->getTextFieldValue("beneficiaryTransferType");

			// check for postal orders
			if ($transferType == "postalOrder") {
				if (preg_match($patternPostalOrder,$paymentAmount)) {
					return True;
				}
			} else {
				if (preg_match($patternAccount, $paymentAmount)) {
					return True;
				}
			}
		}

		// - for TA 830, 832, 836, and 837
		if (in_array($transactionType, Array(830,832,836,837))) {
			// check for ISR15 patterns
			if (preg_match($pattern15, $paymentAmount)) {
				return True;
			}
		}

		// ... invalid value: return False
		return False;
	}

	function adjustPaymentAmount(){
		// auto-adjust payment amount

		// retrieve the type of transaction
		$transactionType = $this->getTransactionType();

		// retrieve payment amount, and remove trailing white space
		$paymentAmount = trim($this->getTextFieldValue("paymentAmount"));

		// define valid string length per transaction type
		switch($transactionType) {
		case 826:
		case 827:
			$length = 12;
			break;
		case 830:
		case 832:
		case 836:
		case 837:
			$length = 15;
			break;
		}

		// extend the payment string to the length as defined for 
		// a transaction type above
		$paymentAmount = str_pad($paymentAmount,$length," ", STR_PAD_RIGHT);
		
		// update payment amount
		$this->setTextFieldValue("paymentAmount", $paymentAmount);
		return;
	}

	function validateOrderingParty(){
		// validate the ordering party

		// define the string length for the types of transactions
		$entryLength = Array(
			826 => 20,
			827 => 24,
			830 => 24,
			832 => 24,
			836 => 35,
			837 => 24,
			890 => 0
		);
		
		// retrieve the type of transaction
		$transactionType = $this->getTransactionType();

		// set the length according to the type of transaction
		$length = $entryLength[$transactionType];

		// define field indexes for the ordering party
		$orderingPartyLineList = Array(
			"orderingPartyLine1",
			"orderingPartyLine2",
			"orderingPartyLine3"
		);

		// all TAs except 836 have a fourth line
		if ($transactionType != 836) {
			$orderingPartyLineList[] = "orderingPartyLine4";
		};

		// verify each single entry for the defined entry length
		foreach($orderingPartyLineList as $entryKey) {
			// retrieve the entry value
			$value = $this->getTextFieldValue($entryKey);
			if (strlen($value) == $length) {
				$this->validationResult[$entryKey] = True;
			}
		}
		return True;
	}

	function adjustOrderingParty(){
		// adjust the ordering party

		// define the string length for the types of transactions
		$entryLength = Array(
			826 => 20,
			827 => 24,
			830 => 24,
			832 => 24,
			836 => 35,
			837 => 24,
			890 => 0
		);

		// retrieve the type of transaction
		$transactionType = $this->getTransactionType();

		// set the length according to the type of transaction
		$length = $entryLength[$transactionType];

		// define field indexes for the ordering party
		$orderingPartyLineList = Array(
			"orderingPartyLine1",
			"orderingPartyLine2",
			"orderingPartyLine3"
		);

		// all TAs except 836 have a fourth line
		if ($transactionType != 836) {
			$orderingPartyLineList[] = "orderingPartyLine4";
		};

		// adjust each single entry for the defined entry length
		foreach($orderingPartyLineList as $entryKey) {
			// retrieve the entry value
			$value = $this->getTextFieldValue($entryKey);

			// adjust the value
			$value = $this->adjustString($value);

			// extend the value by whitespace
			$value = str_pad($value,$length," ", STR_PAD_RIGHT);

			// update the entry value
			$this->setTextFieldValue($entryKey, $value);
		}
		return True;
	}

	function validateBeneficiaryPartyIdentification(){
		// validate the identification of the beneficiary party

		// retrieve the transaction type
		$transactionType = $this->getTransactionType();

		// TA 826, only
		if ($transactionType == 826) {
			// define a regex pattern: /C/123456789 or /C/000012345
			$pattern = '/^\/C\/((\d{9})|(0000\d{5}))$/';

			// retrieve the identification of the beneficiary party
			$beneficiaryPartyIdentification = $this->getTextFieldValue("beneficiaryPartyIdentification");

			// see if the pattern, and the identification of the
			// beneficiary party match
			if (preg_match($pattern, $beneficiaryPartyIdentification)) {
				return True;			
			} 
		}
		// no match: return False
		return False;
	}

	function validateBeneficiaryPartyAccount827(){
		// validate the party account of the beneficiary for TA 827, only

		// retrieve the party account of the beneficiary
		$beneficiaryPartyAccount = $this->getTextFieldValue("beneficiaryPartyAccount");

		// retrieve the transaction type
		$beneficiaryTransferType = $this->getTextFieldValue("beneficiaryTransferType");

		// validate different transfer types:
		// bank payment, postal payment, and postal order

		switch($beneficiaryTransferType) {
			case "bankPayment":
				// define different regex patterns
				// - like /C/CH1234567890
				$pattern1 = "/^\/C\/CH\d+\s*$/";
				// - like /C/1234567890
				$pattern2 = "/^\/C\/\d+\s*$/";
				// - like /C/00001234567890
				$pattern3 = "/^\/C\/0+\d+$/";

				// validate the string length: 30 characters are needed
				if (strlen($beneficiaryPartyAccount) == 30) {
					if (preg_match($pattern1, $beneficiaryPartyAccount)) {
						return True;
					} elseif (preg_match($pattern2, $beneficiaryPartyAccount)) {
						return True;
					} elseif (preg_match($pattern3, $beneficiaryPartyAccount)) {
						return True;
					}
				}
				break;
			case "postalPayment":
				// define regex pattern: /C/ + 9 digits + 18 spaces
				$pattern1 = "/^\/C\/\d{9}\s{18}$/";
				if (preg_match($pattern1, $beneficiaryPartyAccount)) {
					return True;
				} 
				break;
			case "postalOrder";
				// define regex pattern: /C/ + 27 spaces
				$pattern1 = "/^\/C\/\s{27}$/";
				if (preg_match($pattern1, $beneficiaryPartyAccount)) {
					return True;
				}
				break;
		}

		// no match, or invalid: return False
		return False;
	}

	function validateBeneficiaryPartyAccount830(){
		// validate the party account of the beneficiary for TA 830, only

		// retrieve the party account of the beneficiary
		$beneficiaryPartyAccount = $this->getTextFieldValue("beneficiaryPartyAccount");

		// define the regex pattern: /C/ + digits + spaces
		$pattern1 = "/^\/C\/\d+\s*$/";
		if (preg_match($pattern1, $beneficiaryPartyAccount)) {
			// if the length of the party account is 24 characters: return True
			if(strlen($beneficiaryPartyAccount) == 24) {
				return True;
			}
		}
		// ... otherwise: return False
		return False;
	}

	function validateBeneficiaryPartyAccount832(){
		// validate the party account of the beneficiary for TA 830, only

		// retrieve the party account of the beneficiary
		$beneficiaryPartyAccount = $this->getTextFieldValue("beneficiaryPartyAccount");

		// define the regex pattern: /C/ + 21 spaces
		$pattern1 = "/^\/C\/\s{21}$/";
		if (preg_match($pattern1, $beneficiaryPartyAccount)) {
			// if the party account of the beneficiary matches the
			// pattern: return True
			return True;
		}
		// .. return False
		return False;
	}

	function validateBeneficiaryPartyAccount837(){
		// validate the party account of the beneficiary for TA 837, only

		// retrieve the party account of the beneficiary
		$beneficiaryPartyAccount = $this->getTextFieldValue("beneficiaryPartyAccount");

		// verify the string length: 24 characters
		if(strlen($beneficiaryPartyAccount) == 24) {
			// define regex patterns
			// - /C/ + digits + spaces
			$pattern1 = "/^\/C\/\d+\s*$/";
			// - /C/ + spaces
			$pattern2 = "/^\/C\/\s*$/";

			// retrieve the stored IBAN
			$iban = trim($this->getTextFieldValue("iban"));

			// see if the party account of the beneficiary matches pattern1
			if (preg_match($pattern1, $beneficiaryPartyAccount)) {
				// check for an empty IBAN field
				if (strlen($iban) == 0) {
					$this->validationResult["iban"] = True;
					return True;
				}
			} elseif (preg_match($pattern2, $beneficiaryPartyAccount)) {
				// see if the party account of the beneficiary matches pattern2
				// check for an non-empty IBAN field
				if (strlen($iban)) {
					//$this->validationResult["iban"] = True;
					//if ($this->validationResult["iban"] == True) {
					return True;
				}
			}
		}
		// .. otherwise return False
		return False;
	}

	function validateBeneficiaryPartyAccount(){
		// validate the party account of the beneficiary

		// retrieve the type of transaction
		$transactionType = $this->getTransactionType();

		// set return value to False (default value)
		$v = False;

		// evaluate the party account of the beneficiary according
		// to the current type of transaction
		switch($transactionType) {
		case 827:
			$v = $this->validateBeneficiaryPartyAccount827();
			break;
		case 830:
			$v = $this->validateBeneficiaryPartyAccount830();
			break;
		case 837:
			$v = $this->validateBeneficiaryPartyAccount837();
			break;
		case 832:
			$v = $this->validateBeneficiaryPartyAccount832();
			break;
		}
		// return the value of the evaluation
		return $v;
	}

	function adjustBeneficiaryPartyAccount827(){
		$beneficiaryPartyAccount = $this->getTextFieldValue("beneficiaryPartyAccount");
		// TA 827, only
		$beneficiaryTransferType = $this->getTextFieldValue("beneficiaryTransferType");
		switch($beneficiaryTransferType) {
			case "bankPayment":
				$pattern1 = "/^\/C\/CH\d+\s*$/";
				$pattern2 = "/^\/C\/\d+\s*$/";
				$pattern3 = "/^\/C\/0+\d+$/";
				if (preg_match($pattern1, $beneficiaryPartyAccount)) {
					$beneficiaryPartyAccount = str_pad($beneficiaryPartyAccount,30," ", STR_PAD_RIGHT);
					$this->setTextFieldValue("beneficiaryPartyAccount", $beneficiaryPartyAccount);
						
				} elseif (preg_match($pattern2, $beneficiaryPartyAccount)) {
					$beneficiaryPartyAccount = str_pad($beneficiaryPartyAccount,30," ", STR_PAD_RIGHT);
					$this->setTextFieldValue("beneficiaryPartyAccount", $beneficiaryPartyAccount);
				} 
				break;
			case "postalPayment":
				$pattern1 = "/^\/C\/\d{9}\s*$/";
				if (preg_match($pattern1, $beneficiaryPartyAccount)) {
					$beneficiaryPartyAccount = str_pad($beneficiaryPartyAccount,30," ", STR_PAD_RIGHT);
					$this->setTextFieldValue("beneficiaryPartyAccount", $beneficiaryPartyAccount);
				} 
				break;
			case "postalOrder";
				$pattern1 = "/^\/C\/\s*$/";
				if (preg_match($pattern1, $beneficiaryPartyAccount)) {
					$beneficiaryPartyAccount = str_pad($beneficiaryPartyAccount,30," ", STR_PAD_RIGHT);
					$this->setTextFieldValue("beneficiaryPartyAccount", $beneficiaryPartyAccount);
				}
				break;
		}
		return;
	}

	function adjustBeneficiaryPartyAccount830(){
		$beneficiaryPartyAccount = $this->getTextFieldValue("beneficiaryPartyAccount");
		$pattern1 = "/^\/C\/\d+\s*$/";
		if (preg_match($pattern1, $beneficiaryPartyAccount)) {
			$beneficiaryPartyAccount = str_pad($beneficiaryPartyAccount,24," ", STR_PAD_RIGHT);
			$this->setTextFieldValue("beneficiaryPartyAccount", $beneficiaryPartyAccount);
		}
		return;
	}

	function adjustBeneficiaryPartyAccount837(){
		$beneficiaryPartyAccount = $this->getTextFieldValue("beneficiaryPartyAccount");
		$pattern1 = "/^\/C\/\d+\s*$/";
		$pattern2 = "/^\/C\/\s*$/";
		if (preg_match($pattern1, $beneficiaryPartyAccount)) {
			$beneficiaryPartyAccount = str_pad($beneficiaryPartyAccount,24," ", STR_PAD_RIGHT);
			$this->setTextFieldValue("beneficiaryPartyAccount", $beneficiaryPartyAccount);
		} elseif (preg_match($pattern2, $beneficiaryPartyAccount)) {
			// check iban, again
			// valid if empty
			$iban = trim($this->getTextFieldValue("iban"));
			if (strlen($iban)) {
				//$this->validationResult["iban"] = True;
				//if ($this->validationResult["iban"] == True) {
				$beneficiaryPartyAccount = str_pad($beneficiaryPartyAccount,24," ", STR_PAD_RIGHT);
				$this->setTextFieldValue("beneficiaryPartyAccount", $beneficiaryPartyAccount);
			}
		}
		return;
	}

	function adjustBeneficiaryPartyAccount832(){
		$beneficiaryPartyAccount = $this->getTextFieldValue("beneficiaryPartyAccount");
		$pattern1 = "/^\/C\/\s*$/";
		if (preg_match($pattern1, $beneficiaryPartyAccount)) {
			$beneficiaryPartyAccount = str_pad($beneficiaryPartyAccount,24," ", STR_PAD_RIGHT);
			$this->setTextFieldValue("beneficiaryPartyAccount", $beneficiaryPartyAccount);
		}
		return;
	}

	function adjustBeneficiaryPartyAccount(){
		$transactionType = $this->getTransactionType();
		switch($transactionType) {
		case 827:
			return $this->adjustBeneficiaryPartyAccount827();
		case 830:
			return $this->adjustBeneficiaryPartyAccount830();
		case 837:
			return $this->adjustBeneficiaryPartyAccount837();
		case 832:
			return $this->adjustBeneficiaryPartyAccount832();
		}
		return;
	}

	function validateIsrCheckDigit(){
		// validate the ISR check digit

		// define regex pattern: two digits
		$patternISRcheck = '/^\d\d$/';

		// retrieve ISR check digit
		$isrCheckDigit = $this->getTextFieldValue("isrCheckDigit");

		// validate ISR check digit
		if (preg_match($patternISRcheck, $isrCheckDigit)) {
			return True;
		}
		return False;
	}

	function validateReasonForPayment(){
		// validate reason for payment

		// define various regex patterns
		// - 27 digits
		$pattern27 = '/^\d{27}$/';

		// - 11 x zero followed by 16 digits
		$pattern16 = '/^0{11}\d{16}$/';

		// - 15 digits followed by 12 spaces
		$pattern5 = '/^\d{15}\s{12}$/';

		// - /C/ followed by 9 digits
		$patternISR9 = '/^\/C\/\d{9}$/';

		// - /C/ followed by four x zero, and 5 digits
		$patternISR5 = '/^\/C\/0000\d{5}$/';

		// retrieve type of transaction
		$transactionType = $this->getTransactionType();

		// TA 826, only
		if ($transactionType == 826) {

			// have a look at the identification of the beneficiary
			// - retrieve identification of the beneficiary
			$beneficiaryPartyIdentification = $this->getTextFieldValue("beneficiaryPartyIdentification");

			// - retrieve ISR reference number
			$isr = $this->getTextFieldValue("isrReferenceNumber");

			// compare with the ISR5 pattern
			if (preg_match($patternISR5, $beneficiaryPartyIdentification)) {
				if (preg_match($pattern5, $isr)) {
					return True;
				} 
			} elseif (preg_match($patternISR9, $beneficiaryPartyIdentification)) {
				// compare with the ISR9 pattern
				if (preg_match($pattern16, $isr)) {
					return True;
				} elseif (preg_match($pattern27, $isr)) {
					return True;
				}
			} 
		}
		// ... no match: return False
		return False;
	}

	function adjustReasonForPayment(){
		// adjust reason for payment

		// define various regex patterns
		// - 27 digits
		$pattern27 = '/^\d{27}$/';
		// - 16 digits
		$pattern16 = '/^\d{16}$/';
		// - 15 digits followed by 12 spaces
		$pattern5 = '/^\d{15}\s{12}$/';
		// - /C/ followed by 9 digits
		$patternISR9 = '/^\/C\/\d{9}$/';
		// - /C/ followed by four x zero, and 5 digits
		$patternISR5 = '/^\/C\/0000\d{5}$/';
		
		// have a look at the identification of the beneficiary
		// - retrieve identification of the beneficiary
		$beneficiaryPartyIdentification = $this->getTextFieldValue("beneficiaryPartyIdentification");

		// - retrieve ISR reference number
		$isr = $this->getTextFieldValue("isrReferenceNumber");

		// remove all non-digits from the string
		$isr = preg_replace('/[^\d]+/', '', $isr);

		// validate with ISR5 pattern
		if (preg_match($patternISR5, $beneficiaryPartyIdentification)) {
			// extend the string by additional spaces
			$isr = str_pad($isr ,27," ", STR_PAD_RIGHT);

			// update value for ISR reference number
			$this->setTextFieldValue("isrReferenceNumber", $isr);
			return;
		} 
		// ISR9 with 16 or 27 digits can be extended, partly
		if (preg_match($patternISR9, $beneficiaryPartyIdentification)) {
			if(preg_match($pattern16, $isr)) {
				// extend the string by additional spaces
				$isr = str_pad($isr ,27,"0", STR_PAD_LEFT);

				// update value for ISR reference number
				$this->setTextFieldValue("isrReferenceNumber", $isr);
			}
		}
		return;
	}

	function validateBeneficiaryPartyLine() {
		// validate the party description of the beneficiary

		// retrieve the type of transaction
		$transactionType = $this->getTransactionType();

		// assume three lines of description
		$partyLines = Array(
			"beneficiaryPartyLine1",
			"beneficiaryPartyLine2",
			"beneficiaryPartyLine3"
		);

		// this is true except for TA 836
		// let's add a fourth line of description
		if ($transactionType != 836) {
			$partyLines[] = "beneficiaryPartyLine4";
		};

		// define the field length
		// default value is 24 characters
		$length = 24;

		// set a different size (20) for TA 826
		if ($transactionType == 826) {$length = 20;}

		// set a different size (35) for TA 836
		if ($transactionType == 836) {$length = 35;}

		// validate each description line, and check for the correct length
		foreach($partyLines as $item) {
			$value = $this->getTextFieldValue($item);
			if (strlen($value) == $length) {
				$this->validationResult[$item] = True;
			}
		}
		return True;
	}

	function adjustBeneficiaryPartyLine() {
		// adjust the description of the beneficiary

		// retrieve the type of transaction
		$transactionType = $this->getTransactionType();

		// define the indexes
		$partyLines = Array(
			"beneficiaryPartyLine1",
			"beneficiaryPartyLine2",
			"beneficiaryPartyLine3"
		);

		// except for TA 836, there is a fourth index
		if ($transactionType != 836) {
			$partyLines[] = "beneficiaryPartyLine4";
		};

		// define default length: 24 characters
		$length = 24;

		// TA 826 and 836 have a different length
		if ($transactionType == 826) {$length = 20;}
		if ($transactionType == 836) {$length = 35;}

		// look at the data line by line
		foreach($partyLines as $item) {
			// remove all the spaces
			$value = trim($this->getTextFieldValue($item));
			$value = $this->adjustString($value);

			// extend the string by spaces to have the correct string length
			$value = str_pad($value ,$length," ", STR_PAD_RIGHT);

			// update the value
			$this->setTextFieldValue($item, $value);
		}
		return;
	}

	function adjustBeneficiaryTransactionType(){
		// auto-adjust the transaction type of the beneficiary

		// retrieve the type of transaction
		$transactionType = $this->getTransactionType();

		// continue in case of TA 827, only
		if ($transactionType == 827) {
			if ($this->validateBeneficiaryTransferType() == False) {
				// it has to be either a bank payment, a postal payment,
				// or a postal order
				// if it is none of those set the to value to bank payment
				$this->setTextFieldValue("beneficiaryTransferType", "bankPayment");
			}
		}
		return;
	}

	function validateBeneficiaryTransferType(){
		// validate the transfer type of the beneficiary

		// retrieve the value of the transfer type of the beneficiary
		$beneficiaryTransferType = $this->getTextFieldValue("beneficiaryTransferType");

		// retrieve the type of transaction
		$transactionType = $this->getTransactionType();

		// continue with TA 827, only
		if ($transactionType == 827) {
			// define a list of possibilites
			$v = Array("bankPayment", "postalPayment", "postalOrder");
			if (in_array($beneficiaryTransferType, $v)) {
				// if the transfer type is in the list return True
				return True;
			}
		}

		// ... otherwise return False
		return False;
	}

	function validateBeneficiaryMessageLine(){
		// validate the message line(s) for the beneficiary

		// retrieve the type of transaction
		$transactionType = $this->getTransactionType();

		// define a list of indexes for the message lines
		$messageLines = Array(
			"beneficiaryMessageLine1",
			"beneficiaryMessageLine2",
			"beneficiaryMessageLine3",
			"beneficiaryMessageLine4"
		);

		// continue if the type of transaction is either TA 827, 830, or 832
		if (in_array($transactionType, Array(827,830,832))) {

			// define default length: 28 (valid vor TA 827
			$length = 28; // for TA 827

			// for both TA 830 and 832 the length is 30 characters
			if (in_array($transactionType, Array(830,832))) {$length = 30;}

			// go through the message text line by line
			foreach($messageLines as $item) {
				$value = $this->getTextFieldValue($item);

				// if the length fits to the given length its OK
				if (strlen($value) == $length) {
					$this->validationResult[$item] = True;
				}
			}
		}
		return True;
	}

	function adjustBeneficiaryMessageLine(){
		// validate the message line(s) for the beneficiary

		// retrieve the type of transaction
		$transactionType = $this->getTransactionType();

		// define a list of indexes for the message lines
		$messageLines = Array(
			"beneficiaryMessageLine1",
			"beneficiaryMessageLine2",
			"beneficiaryMessageLine3",
			"beneficiaryMessageLine4"
		);

		// continue if the type of transaction is either TA 827, 830, or 832
		if (in_array($transactionType, Array(827,830,832))) {

			// define default length: 28 (valid vor TA 827
			$length = 28; // for TA 827

			// for both TA 830 and 832 the length is 30 characters
			if (in_array($transactionType, Array(830,832))) {$length = 30;}

			// go through the message text line by line
			foreach($messageLines as $item) {

				// retrieve the according value
				$value = $this->getTextFieldValue($item);

				// remove the spaces at the beginning, and at the end
				$value = trim($value);
				$value = $this->adjustString($value);

				// add as much spaces as needed to reach the specified length
				$value = str_pad($value ,$length," ", STR_PAD_RIGHT);

				// update the value
				$this->setTextFieldValue($item, $value);
			}
		}
		return True;
	}

	function validateEndBeneficiaryParty(){
		// 55 - this is optional, TA 827 only
		$transactionType = $this->getTransactionType();
		if ($transactionType == 827) {
			$endBeneficiaryPartyAccount = $this->getTextFieldValue("endBeneficiaryPartyAccount");
			$pattern = '/^\/C\/\d+\s*$/';
			if (preg_match($pattern, $endBeneficiaryPartyAccount)) {
				if (strlen($endBeneficiaryPartyAccount) == 30) {
					$this->validationResult["endBeneficiaryPartyAccount"] = True;
					$ba = 0;
				}
			} else {
				if (trim($endBeneficiaryPartyAccount) == "") {
					$this->validationResult["endBeneficiaryPartyAccount"] = True;
					$ba = 1;
				}
			}

			$partyLine = Array(
				"endBeneficiaryPartyLine1",
				"endBeneficiaryPartyLine2",
				"endBeneficiaryPartyLine3",
				"endBeneficiaryPartyLine4"
			);
			foreach($partyLine as $lineKey) {
				$value = $this->getTextFieldValue($lineKey);
				if (strlen($value) == 24) {
					if ($ba == 0) {
						$this->validationResult[$lineKey] = True;
					} elseif ($ba == 1) {
						if (trim($value) == "") {
							$this->validationResult[$lineKey] = True;
						}
					}
				}
			}
		}
		return True;
	}

	function adjustEndBeneficiaryParty(){
		// 55 - optional, TA 827 only
		$transactionType = $this->getTransactionType();
		if ($transactionType == 827) {
			$endBeneficiaryPartyAccount = $this->getTextFieldValue("endBeneficiaryPartyAccount");
			$pattern = '/^\/C\/\d+\s*$/';
			if (preg_match($pattern, $endBeneficiaryPartyAccount)) {
				$ba = 0;
			} elseif (trim($endBeneficiaryPartyAccount == "")) {
				$ba = 1;
			}

			$endBeneficiaryPartyAccount = trim($endBeneficiaryPartyAccount);
			$endBeneficiaryPartyAccount = str_pad($endBeneficiaryPartyAccount ,30," ", STR_PAD_RIGHT);
			
			$this->setTextFieldValue("endBeneficiaryPartyAccount", $endBeneficiaryPartyAccount);

			$partyLine = Array(
				"endBeneficiaryPartyLine1",
				"endBeneficiaryPartyLine2",
				"endBeneficiaryPartyLine3",
				"endBeneficiaryPartyLine4"
			);
			foreach($partyLine as $lineKey) {
				$value = trim($this->getTextFieldValue($lineKey));
				if ($ba == 1) {$value = "";}
				$value = $this->adjustString($value);
				$value = str_pad($value ,24," ", STR_PAD_RIGHT);
				$this->setTextFieldValue($lineKey, $value);
			}
		}
		return True;
	}

	function validateConvertionRate() {
		// this field is optional, and can be empty, too
		$transactionType = $this->getTransactionType();
		$convertionRate = $this->getTextFieldValue("convertionRate");
		if (in_array($transactionType, Array(830, 832,836,837))) {
			if (trim($convertionRate) == "") {
				return True;
			} else {
				$pattern = '/^\d+,\d{1,6}\s*$/';
				if (preg_match($pattern, $convertionRate)) {
					if (strlen($convertionRate) == 12) {
						return True;
					}
				}
			}
		}
		return False;
	}

	function adjustConvertionRate() {
		$transactionType = $this->getTransactionType();
		$convertionRate = $this->getTextFieldValue("convertionRate");
		if (in_array($transactionType, Array(830, 832,836,837))) {
			$pattern = '/[^\d,]/';
			$convertionRate = preg_replace($pattern, '', $convertionRate);
			// $convertionRate = trim($convertionRate);
			$convertionRate = str_pad($convertionRate ,12," ", STR_PAD_RIGHT);
			$this->setTextFieldValue("convertionRate", $convertionRate);
		}
		return;
	}

	function validateIdentificationBankAddress() {
		// validate the identification of the bank address

		// retrieve the type of the transaction
		$transactionType = $this->getTransactionType();

		// continue in case it is TA 830 or 837
		if (in_array($transactionType, Array(830,837))) {
			// retrieve the identification of the bank address
			$identificationBankAddress = $this->getTextFieldValue("identificationBankAddress");

			if (in_array($identificationBankAddress, Array ("A","D"))) {
				// if the value is either A or D return True
				return True;
			}
		}

		// ... return False
		return false;
	}

	function adjustIdentificationBankAddress() {
		// auto-adjust the identification of the bank address

		// retrieve the type of the transaction
		$transactionType = $this->getTransactionType();

		// continue in case it is TA 830 or 837
		if (in_array($transactionType, Array(830,837))) {

			// retrieve the identification of the bank address
			$identificationBankAddress = $this->getTextFieldValue("identificationBankAddress");

			// remove all the spaces both at the beginning and at the end
			$identificationBankAddress = trim($identificationBankAddress);

			// define a regex pattern: A or D
			$pattern = '/^[AD]$/';

			// compare the saved value with the pattern
			if (preg_match($pattern, $identificationBankAddress)) {
				// do nothing -- everything is OK
			} else {
				// incorrect value. Reset to A
				$identificationBankAddress = "A";
			}

			// update the identification of the bank address
			$this->setTextFieldValue("identificationBankAddress", $identificationBankAddress);
		}
		return;	
	}

	function validateBeneficiaryInstitution() {
		// validate the institution of the beneficiary

		// retrieve the type of transaction
		$transactionType = $this->getTransactionType();

		// continue if it is TA 830 or 837
		if (in_array($transactionType, Array(830,837))) {

			// retrieve the institution of the beneficiary
			$beneficiaryInstitution = $this->getTextFieldValue("beneficiaryInstitution");

			// remove all the spaces, and see if the remaining string is empty
			if (trim($beneficiaryInstitution) == "") {
				return True;
			}

			// if not, define a regex pattern: /C/ + digits
			$pattern = '/^\/C\/\d+\s*$/';

			// see if the pattern matches, and the string has a 
			// length of 24 characters
			if (preg_match($pattern, $beneficiaryInstitution)) {
				if (strlen($beneficiaryInstitution) == 24) {
					return True;
				}
			}
		}

		// ... return False
		return False;
	}

	function adjustBeneficiaryInstitution() {
		// auto-adjust the institution of the beneficiary

		// retrieve the type of transaction
		$transactionType = $this->getTransactionType();

		// continue if it is TA 830 or 837
		if (in_array($transactionType, Array(830,837))) {

			// retrieve the institution of the beneficiary
			$beneficiaryInstitution = $this->getTextFieldValue("beneficiaryInstitution");

			// remove all the spaces at the beginning, and at the end
			$beneficiaryInstitution = trim($beneficiaryInstitution);

			// extend the string by spaces at the end to have a 
			// length of 24 characters
			$beneficiaryInstitution = str_pad($beneficiaryInstitution ,24," ", STR_PAD_RIGHT);

			// update the value for the institution of the beneficiary
			$this->setTextFieldValue("beneficiaryInstitution", $beneficiaryInstitution);
		}
		return;
	}

	function validateBeneficiarySwiftAddress830(){
		// validate the SWIFT address of the payment beneficiary
		// option 57a and 57d, TA 830 only

		// retrieve the SWIFT id, and address lines
		$beneficiarySwiftAddress = $this->getTextFieldValue("beneficiaryInstituteLine1");
		$beneficiaryInstituteLine = Array(
			"beneficiaryInstituteLine2" => $this->getTextFieldValue("beneficiaryInstituteLine2"),
			"beneficiaryInstituteLine3" => $this->getTextFieldValue("beneficiaryInstituteLine3"),
			"beneficiaryInstituteLine4" => $this->getTextFieldValue("beneficiaryInstituteLine4"),
			"beneficiaryInstituteLine5" => $this->getTextFieldValue("beneficiaryInstituteLine5")
		);

		// set a marker to continue validation
		$stillTrue = True;

		// validate SWIFT address for the correct text length
		if (strlen($beneficiarySwiftAddress) != 24) {
			$this->validationResult["beneficiaryInstituteLine1"] = False;
			$stillTrue = False;
		}

		// validate the the address lines for the correct text length
		foreach($beneficiaryInstituteLine as $key => $value) {
			if (strlen($value) != 24) {
				$this->validationResult[$key] = False;
				$stillTrue = False;
			}
		}

		// does our marker is already False?
		if ($stillTrue == False) {
			return False;
		}

		// in case we did not encounter an error, continue

		// to be sure re-evaluate the bank address
		if ($this->validationResult["identificationBankAddress"] == True) {

			// define a regex pattern: /C/ + digits
			$pattern1 = '/^\/C\/\d+\s*$/';

			// see if the SWIFT address matches the pattern
			if (preg_match($pattern1, $beneficiarySwiftAddress)) {
				$this->validationResult["beneficiaryInstituteLine1"] = True;
			}

			// retrieve the bank address
			$bank = $this->getTextFieldValue("identificationBankAddress");

			// in case bank is set to A:
			if ($bank == "A") {

				// define a regex pattern: upper-case letters, followed
				// by digits and spaces
				$pattern2 = '/^[A-Z]+\d+\s*$/';

				// combine the single elements of the address into one string
				$content = trim(implode("", $beneficiaryInstituteLine));

				// see if the content matches the pattern2 
				if (preg_match($pattern2, $content)) {
					// check the length for 8, or 11 characters
					if (in_array(strlen($content), array(8,11))) {
						$this->validationResult["beneficiaryInstituteLine2"] = True;
						$this->validationResult["beneficiaryInstituteLine3"] = True;
						$this->validationResult["beneficiaryInstituteLine4"] = True;
						$this->validationResult["beneficiaryInstituteLine5"] = True;
						return True;
					}
				}
			}

			// in case bank is set to D: set these lines to True
			if ($bank == "D") {
				$this->validationResult["beneficiaryInstituteLine2"] = True;
				$this->validationResult["beneficiaryInstituteLine3"] = True;
				$this->validationResult["beneficiaryInstituteLine4"] = True;
				$this->validationResult["beneficiaryInstituteLine5"] = True;
				return True;
			}
		}

		// ... otherwise return False
		return False;
	}

	function validateBeneficiarySwiftAddress836(){
		// validate the SWIFT address of the payment beneficiary
		// option 57a and 57d, TA 836 only

		// retrieve the bank id, and address lines
		$bank  = trim($this->getTextFieldValue("identificationBankAddress"));
		$line1 = trim($this->getTextFieldValue("beneficiaryInstituteLine1"));
		$line2 = trim($this->getTextFieldValue("beneficiaryInstituteLine2"));

		// retrieve the IBAN
		$iban  = trim($this->getTextFieldValue("iban"));

		// continue in case we have an IBAN ...
		if($this->isIban($iban)) {
			if ($bank == "D"){
				// ... and bank is marked via D
				// set identification of bank address to True
				$this->validationResult["identificationBankAddress"] = True;

				// validate the content of the bank address
				if (strlen(trim($line1 . $line2)) == 0) {
					// there is no content: set to True
					$this->validationResult["beneficiaryInstituteLine1"] = True;
					$this->validationResult["beneficiaryInstituteLine2"] = True;
					return True;
				} else {
					// ... otherwise: False
					$this->validationResult["beneficiaryInstituteLine1"] = False;
					$this->validationResult["beneficiaryInstituteLine2"] = False;
					return False;
				}
			} else {
				// set identification of bank address to False
				$this->validationResult["identificationBankAddress"] = False;

				// validate the content of the bank address
				if (strlen(trim($line1 . $line2)) == 0) {
					// there is no content: set to True
					$this->validationResult["beneficiaryInstituteLine1"] = True;
					$this->validationResult["beneficiaryInstituteLine2"] = True;
					return False;
				} else {
					// ... otherwise: False
					$this->validationResult["beneficiaryInstituteLine1"] = False;
					$this->validationResult["beneficiaryInstituteLine2"] = False;
					return False;
				}
			}
		} else {
			// mark all the according entries as False
			$this->validationResult["iban"] = False;
			$this->validationResult["beneficiaryInstituteLine1"] = False;
			$this->validationResult["beneficiaryInstituteLine2"] = False;
			$this->validationResult["identificationBankAddress"] = False;
			return False;
		}

		return False;
	}

	function validateBeneficiarySwiftAddress() {
		// validate the SWIFT address of the payment beneficiary
		// option 57a and 57d, TA 830, 836 and 837 only

		// retrieve the type of transaction
		$transactionType = $this->getTransactionType();

		// continue for TA 830, 836, and 837, only
		if (in_array($transactionType, Array(830,836,837))) {
			switch ($transactionType) {
			case 830:
			case 837:
				return $this->validateBeneficiarySwiftAddress830();
				break;
			case 836:
				return $this->validateBeneficiarySwiftAddress836();
				break;
			}
		}
		return;
	}

	function adjustBeneficiarySwiftAddress() {
		// adjust the SWIFT address of the payment beneficiary

		// retrieve the type of transaction
		$transactionType = $this->getTransactionType();

		// continue for TA 830, 836, and 837, only
		if (in_array($transactionType, Array(830,836,837))) {
			// retrieve transaction-specific list of fields
			if ($transactionType == 836) {
				$itemList = Array(
				"beneficiaryInstituteLine1" => $this->getTextFieldValue("beneficiaryInstituteLine1"),
				"beneficiaryInstituteLine2" => $this->getTextFieldValue("beneficiaryInstituteLine2")
				);
			} else {
				$itemList = Array(
				"beneficiaryInstituteLine1" => $this->getTextFieldValue("beneficiaryInstituteLine1"),
				"beneficiaryInstituteLine2" => $this->getTextFieldValue("beneficiaryInstituteLine2"),
				"beneficiaryInstituteLine3" => $this->getTextFieldValue("beneficiaryInstituteLine3"),
				"beneficiaryInstituteLine4" => $this->getTextFieldValue("beneficiaryInstituteLine4"),
				"beneficiaryInstituteLine5" => $this->getTextFieldValue("beneficiaryInstituteLine5")
				);
			}

			// define the default field length
			$length = 24;
			if ($transactionType == 836) {
				// TA 836 has a specific field length of 35 characters
				$length = 35;
			}

			// examine each line		
			foreach($itemList as $key => $value) {

				// remove spaces at the beginning and at the end of the string
				$value = trim($value);
				$value = $this->adjustString($value);

				// extend the string by the according numbers of spaces
				// as specified 
				$value = str_pad($value ,$length," ", STR_PAD_RIGHT);

				// update the stored field value
				$this->setTextFieldValue($key, $value);
			}
		}
		return;
	}

	function validateBankPaymentInstruction() {
		// validate the bank payment instruction 
		// field: bankPaymentInstruction (72)
		// for TA 830,832

		// retrieve the type of transaction
		$transactionType = $this->getTransactionType();

		// validate this for TA 830 and 832, only
		if (in_array($transactionType, Array(830,832))){

			// retrieve the bank payment instruction
			$bankPaymentInstruction = $this->getTextFieldValue("bankPaymentInstruction");

			// remove all spaces
			$value = trim($bankPaymentInstruction);

			// valid instructions are "CHG/OUR", "CHG/BEN", and ""
			// if we have one of those the entry is valid
			if (in_array($value, Array("CHG/OUR", "CHG/BEN", ""))) {
				$this->validationResult["bankPaymentInstruction"] = True;
				return True;
			}
		}
		// ... otherwise return False
		return False;
	}

	function adjustBankPaymentInstruction() {
		// auto-adjust the bank payment instruction
		// field: bankPaymentInstruction (72)
		// for TA 830,832

		// retrieve type of transaction
		$transactionType = $this->getTransactionType();

		// validate this for TA 830 and 832, only
		if (in_array($transactionType, Array(830,832))){

			// retrieve the bank payment instruction
			$bankPaymentInstruction = $this->getTextFieldValue("bankPaymentInstruction");

			// change the value to 30 spaces
			$bankPaymentInstruction = str_pad($bankPaymentInstruction,30," ",STR_PAD_RIGHT);

			// update the stored value
			$this->setTextFieldValue("bankPaymentInstruction", $bankPaymentInstruction);
		}
		return;
	}

	function validateIban(){
		// validate the IBAN value (58)
		// TA 836 and 837, only

		// retrieve the type of transaction
		$transactionType = $this->getTransactionType();

		// evaluate this for TA 836 and 837, only
		if (in_array($transactionType, Array(836, 837))) {

			// retrieve the stored value vor IBAN
			$iban = $this->getTextFieldValue("iban");

			// define a regex pattern: two characters followed by digits
			$pattern = '/^[A-Z]{2}\d+\s*$/';

			// see if the pattern matches, and the string has the length
			// of 34 characters
			if (preg_match($pattern, $iban)) {
				if(strlen($iban) == 34) {
					return True;
				}
			}
		}

		// ... return False, instead
		return False;
	}

	function adjustIban() {
		// auto-adjust the IBAN value (58)
		// TA 836 and 837, only

		// retrieve the type of transaction
		$transactionType = $this->getTransactionType();

		// evaluate this for TA 836 and 837, only
		if (in_array($transactionType, Array(836, 837))) {

			// retrieve the stored value vor IBAN
			$value = $this->getTextFieldValue("iban");

			// remove all spaces
			$value = trim($value);
			$value = $this->adjustString($value);

			// add as many spaces as specified to reach a length of 34 characters
			$value = str_pad($value ,34," ", STR_PAD_RIGHT);

			// update the IBAN value
			$this->setTextFieldValue("iban", "$value");
		}
		return;
	}

	function validatePurpose(){
		// validate transaction purpose for both TA 836 and 837

		// retrieve type of transaction
		$transactionType = $this->getTransactionType();

		if ($transactionType == 836) {
			return $this->validatePurpose836();
		}
		if ($transactionType == 837) {
			return $this->validatePurpose837();
		}
		return False;
	}

	function validatePurpose836(){
		// validate transaction purpose for TA 836

		// retrieve the purpose information
		$purposeStructure = $this->getTextFieldValue("purposeStructure");

		// purpose: I
		if ($purposeStructure == "I") {
			$this->validationResult["purposeStructure"] = True;

			// line 1
			// remove spaces at the beginning and at the end of the string
			$v = trim($this->getTextFieldValue("purposeLine1"));

			// define a regex pattern for twenty upper-case characters and digits
			$pattern = '/^[A-Z\d]{20}\s*$/';

			// see if this pattern matches with the purpose description
			if (preg_match($pattern, $v)) {
				$this->validationResult["purposeLine1"] = True;
			}

			// line 2+3
			// these two lines have to be empty
			// define a regex pattern for 35 spaces
			$pattern = '/^\s{35}$/';

			// see if this pattern matches with purpose lines 2 and 3
			$v = $this->getTextFieldValue("purposeLine2");
			if (preg_match($pattern, $v)) {
				$this->validationResult["purposeLine2"] = True;
			}
			$v = $this->getTextFieldValue("purposeLine3");
			if (preg_match($pattern, $v)) {
				$this->validationResult["purposeLine3"] = True;
			}

			// return True if all lines have valid content
			if ($this->validationResult["purposeLine1"] and $this->validationResult["purposeLine2"] and $this->validationResult["purposeLine3"]) {
				return True;
			}
		}

		// purpose: U
		if ($purposeStructure == "U") {
			$this->validationResult["purposeStructure"] = True;

			// verify that all the purpose lines have the specified
			// length of 35 characters
			$pList = Array("purposeLine1", "purposeLine2", "purposeLine3");
			foreach($pList as $entry) {
				$v = $this->getTextFieldValue($entry);
				if (strlen($v) == 35) {
					$this->validationResult[$entry] = True;
				}
			}

			// return True if all the purpose fields have valid content
			if ($this->validationResult["purposeLine1"] and $this->validationResult["purposeLine2"] and $this->validationResult["purposeLine3"]) {
				return True;
			}
		}

		// ... otherwise return False
		return False;
	}

	function validatePurpose837(){
		// validate transaction purpose for TA 837

		$purposeStructure = $this->getTextFieldValue("purposeStructure");
		switch($purposeStructure) {
		case "I":
			// structured with reference number
		case "U":
			// unstructured with reference number
			$this->validationResult["purposeStructure"] = True;
			// line 1
			$v = trim($this->getTextFieldValue("purposeLine1"));
			$pattern = '/^[A-Z\d]{20}\s*$/';
			if (preg_match($pattern, $v)) {
				$this->validationResult["purposeLine1"] = True;
			}

			// line 2+3
			$pattern = '/^\s{35}$/';
			$v = $this->getTextFieldValue("purposeLine2");
			if (preg_match($pattern, $v)) {
				$this->validationResult["purposeLine2"] = True;
			}
			$v = $this->getTextFieldValue("purposeLine3");
			if (preg_match($pattern, $v)) {
				$this->validationResult["purposeLine3"] = True;
			}
			if ($this->validationResult["purposeLine1"] and $this->validationResult["purposeLine2"] and $this->validationResult["purposeLine3"]) {
				return True;
			}
			break;
		case "F":
			// **non-standard**
			// free text without ipi reference number, should be U, instead
			$this->validationResult["purposeStructure"] = True;
			$pList = Array("purposeLine1", "purposeLine2", "purposeLine3");
			foreach($pList as $entry) {
				$v = $this->getTextFieldValue($entry);
				if (strlen($v) == 35) {
					$this->validationResult[$entry] = True;
				}
			}
			if ($this->validationResult["purposeLine1"] and $this->validationResult["purposeLine2"] and $this->validationResult["purposeLine3"]) {
				return True;
			}
			break;
		}
		return False;
	}

	function adjustPurpose836(){
		// adjust the transaction purpose for TA 836

		// retrieve the purpose structure
		$purposeStructure = $this->getTextFieldValue("purposeStructure");

		// evaluate structure type: I, and U
		if ($purposeStructure == "I") {
			// remove all spaces from line 1, and add the according
			// spaces to the string length
			$v = trim($this->getTextFieldValue("purposeLine1"));
			$v = $this->adjustString($v);
			$v = str_pad($v,35," ", STR_PAD_RIGHT);
			$this->setTextFieldValue("purposeLine1", $v);

			// line 2+3
			// substitute the value by 35 spaces
			$v = "";
			$v = str_pad($v,35," ", STR_PAD_RIGHT);
			$this->setTextFieldValue("purposeLine2", $v);
			$this->setTextFieldValue("purposeLine3", $v);
		}

		if ($purposeStructure == "U") {
			// remove all spaces from lines 1 to 3, and add the according
			// spaces to the string length
			$pList = Array("purposeLine1", "purposeLine2", "purposeLine3");
			foreach($pList as $entry) {
				$v = trim($this->getTextFieldValue($entry));
				$v = str_pad($v,35," ", STR_PAD_RIGHT);
				$this->setTextFieldValue($entry, $v);
			}
		}
		return True;
	}

	function adjustPurpose837(){
		// adjust the transaction purpose for TA 837

		// retrieve the purpose structure
		$purposeStructure = $this->getTextFieldValue("purposeStructure");

		// evaluate structure type: I, U, and F
		switch($purposeStructure) {
		case "I":
		case "U":
			// remove all spaces from line 1, and add the according
			// spaces to the string length
			$v = trim($this->getTextFieldValue("purposeLine1"));
			$v = $this->adjustString($v);
			$v = str_pad($v,35," ", STR_PAD_RIGHT);
			$this->setTextFieldValue("purposeLine1", $v);

			// line 2+3
			// substitute the value by 35 spaces
			$v = "";
			$v = str_pad($v,35," ", STR_PAD_RIGHT);
			$this->setTextFieldValue("purposeLine2", $v);
			$this->setTextFieldValue("purposeLine3", $v);
			break;

		case "F":
			// remove all spaces from lines 1 to 3, and add the according
			// spaces to the string length
			$pList = Array("purposeLine1", "purposeLine2", "purposeLine3");
			foreach($pList as $entry) {
				$v = trim($this->getTextFieldValue($entry));
				$v = str_pad($v,35," ", STR_PAD_RIGHT);
				$this->setTextFieldValue($entry, $v);
			}
			break;
		}

		return True;
	}

	function adjustPurpose() {
		// auto-adjust the transaction purpose

		// retrieve the type of transaction for TA 836 and 837
		// and call the TA-specific adjustment function
		$transactionType = $this->getTransactionType();
		if ($transactionType == 836) {
			return $this->adjustPurpose836();
		}
		if ($transactionType == 837) {
			return $this->adjustPurpose837();
		}
		return True;
	}

	function validateRulesForCharges(){
		// validate the rules for charges (71ia)

		// retrieve the type of transaction
		$transactionType = $this->getTransactionType();

		// set values for charges
		$chargesValues = array(0,1,2);

		// examine this field for TA 836 and 837, only
		if (in_array($transactionType, Array(837,836))) {
			// retrieve the rules for Charges
			$rulesForCharges = $this->getTextFieldValue("rulesForCharges");

			// is value in the list?
			if (in_array($rulesForCharges, $chargesValues)) {
				$this->validationResult["rulesForCharges"] = True;
				return True;
			}
			return False;
		}
		return;
	}

	function validateInformationStructure(){
		// validate the information structure of a transaction (TA 837, only)

		// retrieve the type of transaction
		$transactionType = $this->getTransactionType();

		// validate these fields for TA 837, only
		if ($transactionType == 837) {
			// retrieve the information structure
			$informationStructure = $this->getTextFieldValue("informationStructure");
			
			// chaeck for structured (S) or unstructured (U) information
			if (in_array($informationStructure, Array("S", "U"))) {
				$this->validationResult["informationStructure"] = True;
		
				// assume S
				$structureList = Array("instructionLine1", "instructionLine2", "instructionLine3");
				// ... with a length of 35 characters
				$length = 35;

				// in case it is different (unstructured) add an
				// additional field, and reduce the string length to 30 	
				// characters
				if ($informationStructure == "U") {
					$structureList[] = "instructionLine4";
					$length = 30;
				}

				// validate each line for the correct length
				foreach($structureList as $key => $value) {
					$item = $this->getTextFieldValue($value);
					if(strlen($item) == $length) {
						$this->validationResult[$value] = True;
					}
				}
			}
		}
		// ... otherwise return, only
		return;
	}

	function adjustInformationStructure(){
		// auto-correct the information structure of a transaction (TA 837, only)

		// retrieve the type of transaction
		$transactionType = $this->getTransactionType();

		// examine TA 837, only
		if ($transactionType == 837) {

			// retrieve the information structure
			$informationStructure = $this->getTextFieldValue("informationStructure");

			// assume value: S (for structured)
			$structureList = Array("instructionLine1", "instructionLine2", "instructionLine3");
			// ... with a length of 35 characters
			$length = 35;

			// if value is set to U (for unstructured)
			if ($informationStructure == "U") {
				// append a fourth line of instructions
				$structureList[] = "instructionLine4";
				// ... and change the length to 30 characters, only
				$length = 30;
			}

			// validate each line of the information structure
			foreach($structureList as $key => $value) {
				// remove all spaces at the beginning and at the end
				$item = trim($this->getTextFieldValue($value));
				$item = $this->adjustString($item);

				// adjust the acoording number of spaces at the end
				$item = str_pad($item,$length," ", STR_PAD_RIGHT);

				$this->setTextFieldValue($value, $item);
			}
		}
		return;
	}

	function validateTotal(){
		// validate total of transaction

		// retrieve type of transaction
		$transactionType = $this->getTransactionType();

		// this field does exist in TA 890, only
		if ($transactionType == 890) {
			// retrieve total value
			$total = $this->getTextFieldValue("total");

			// define regex pattern with digits
			$pattern = '/^\d+,\d{1,3}\s*$/';

			if (preg_match($pattern, $total)) {
				// validate the string length: 16 (max.)
				if (strlen($total) == 16) {
					return True;
				}
			}
			return False;
		}
		return;
	}

	function adjustTotal(){
		// validate total of transaction

		// retrieve type of transaction
		$transactionType = $this->getTransactionType();

		// this field does exist in TA 890, only
		if ($transactionType == 890) {

			// remove all the spaces at the beginning, and at the end
			$total = trim($this->getTextFieldValue("total"));

			// at the end, add as many spaces as needed to achieve a
			// length of 16 characters
			$total = str_pad($total,16," ", STR_PAD_RIGHT);

			// update the value for the total of the transaction
			$this->setTextFieldValue("total", $total);
		}
		return;
	}

	function validateDataFields() {
		// validate transaction data fields

		// dataFormat
		$v = $this->validateDataFormat();
		$this->validationResult["dataFormat"] = $v;

		// - reference number (20)
		// + orderingPartyIdentification
		$v = $this->validateOrderingPartyIdentification();
		$this->validationResult["orderingPartyIdentification"] = $v;
		
		// + orderingPartyTransactionNumber
		$v = $this->validateOrderingPartyTransactionNumber();
		$this->validationResult["orderingPartyTransactionNumber"] = $v;

		// + reference number
		$v = $this->validateReferenceNumber();
		$this->validationResult["referenceNumber"] = $v;

		// - account to be debited (25)
		// + accountWithoutIban
		// + accountWithIban
		$v = $this->validateAccountToBeDebited();
		$this->validationResult["accountToBeDebited"] = $v;

		// - payment amount (32a)
		// + paymentValueDate
		$v = $this->validatePaymentValueDate();
		$this->validationResult["paymentValueDate"] = $v;
			
		// + paymentCurrencyCode
		$v = $this->validatePaymentCurrencyCode();
		$this->validationResult["paymentCurrencyCode"] = $v;

		// + paymentAmount
		$v = $this->validatePaymentAmount();
		$this->validationResult["paymentAmount"] = $v;

		// - iban (58)
		// TA 836 and 837, only
		$v = $this->validateIban();
		$this->validationResult["iban"] = $v;

		// - reason for payment (70)
		// + isr reference number, TA 826 only
		$v = $this->validateReasonForPayment();
		$this->validationResult["reasonForPayment"] = $v;
		$this->validationResult["isrReferenceNumber"] = $v;

		// + isr check digit
		$v = $this->validateIsrCheckDigit();
		$this->validationResult["isrCheckDigit"] = $v;

		// - beneficiary (59)
		// + beneficiaryTransferType
		$v = $this->validateBeneficiaryTransferType();
		$this->validationResult["beneficiaryTransferType"] = $v;

		// + beneficiaryPartyIdentification
		$v = $this->validateBeneficiaryPartyIdentification();
		$this->validationResult["beneficiaryPartyIdentification"] = $v;

		// + beneficiaryPartyAccount
		$v = $this->validateBeneficiaryPartyAccount();
		$this->validationResult["beneficiaryPartyAccount"] = $v;
		
		// + beneficiaryPartyLine1 to 4
		$v = $this->validateBeneficiaryPartyLine();

		// + beneficiaryMessageLine (70) for TA 827
		// beneficiaryMessageLine1 to 4
		$v = $this->validateBeneficiaryMessageLine();

		// endBeneficiaryParty (55)
		// + endBeneficiaryPartyAccount
		// + endBeneficiaryPartyLine1 to 4
		$v = $this->validateEndBeneficiaryParty();

		// - convertionRate (36)
		// TA 830, 832, 836, only
		$v = $this->validateConvertionRate();
		$this->validationResult["convertionRate"] = $v;

		// - identificationBankAddress (TA 830 und 837, only
		$v = $this->validateIdentificationBankAddress();
		$this->validationResult["identificationBankAddress"] = $v;

		// + beneficiarySwiftAddress and beneficiaryInstituteLine1 to 4
		// option 57a and 57d
		$v = $this->validateBeneficiarySwiftAddress();

		// orderingPartyIdentification
		// orderingPartyTransactionNumber

		// - orderingParty (50)
		$this->validateOrderingParty();

		// - bankPaymentInstruction (72)
		// for TA 830,832
		$v = $this->validateBankPaymentInstruction();
		$this->validationResult["bankPaymentInstruction"] = True;

		// - purpose (70i and 70u)
		// + purpose structure
		// + purposeLine1 to 3
		$v = $this->validatePurpose();

		// rules for charges (71ia)
		// for TA 836,837
		$v = $this->validateRulesForCharges();
		$this->validationResult["rulesForCharges"] = $v;

		// information structure (72s and 72u)
		$v = $this->validateInformationStructure();

		// total (90, in TA 890 only)
		$v = $this->validateTotal();
		$this->validationResult["total"] = $v;

		return;
	}

	function adjustDataFields() {
		// auto-correct transaction data fields

		// dataFormat
		//$v = $this->validateDataFormat();

		// - reference number (20)
		// + orderingPartyIdentification
		$this->adjustOrderingPartyIdentification();
		
		// + orderingPartyTransactionNumber
		$this->adjustOrderingPartyTransactionNumber();

		// + reference number
		// cannot be adjusted, automatically

		// - account to be debited (25)
		// + accountWithoutIban
		$this->adjustAccountWithoutIban();
		// + accountWithIban
		$this->adjustAccountWithIban();
		//$this->adjustAccountToBeDebited();

		// - payment amount (32a)
		// + paymentValueDate
		$this->adjustPaymentValueDate();
			
		// + paymentCurrencyCode
		$this->adjustPaymentCurrencyCode();

		// + paymentAmount
		$this->adjustPaymentAmount();

		// - reason for payment (70)
		// + isr reference number, TA 826 only
		$this->adjustReasonForPayment();

		// - beneficiary (59)
		// + beneficiaryTransferType
		// no adjustment 

		// + beneficiaryPartyIdentification
		// cannot be adjusted, automatically

		// + beneficiaryPartyAccount
		$this->adjustBeneficiaryPartyAccount();
		
		// + beneficiaryPartyLine1 to 4
		$this->adjustBeneficiaryPartyLine();

		// + beneficiaryMessageLine (70) for TA 827
		// beneficiaryMessageLine1 to 4
		$this->adjustBeneficiaryMessageLine();

		// endBeneficiaryParty (55)
		// + endBeneficiaryPartyAccount
		// + endBeneficiaryPartyLine1 to 4
		$this->adjustEndBeneficiaryParty();

		// - convertionRate (36)
		// TA 830, 832, 836, only
		$this->adjustConvertionRate();

		// - identificationBankAddress (TA 830 und 837, only
		$this->adjustIdentificationBankAddress();

		// + beneficiarySwiftAddress and beneficiaryInstituteLine1 to 4
		// option 57a and 57d
		$this->adjustBeneficiarySwiftAddress();

		// orderingPartyIdentification
		// orderingPartyTransactionNumber

		// - orderingParty (50)
		$this->adjustOrderingParty();

		// - iban (58)
		// TA 836 and 837, only
		$this->adjustIban();

		// - bankPaymentInstruction (72)
		// for TA 830,832
		$this->adjustBankPaymentInstruction();

		// - purpose (70i and 70u)
		// + purpose structure
		// + purposeLine1 to 3
		$this->adjustPurpose();

		// rules for charges (71ia)
		// cannot be adjusted, automatically

		// information structure (72s and 72u)
		$this->adjustInformationStructure();

		// total (90, in TA 890 only)
		$this->adjustTotal();

		return;
	}

	// conversion functions

	function toHex ($binaryString) {
		// transform a binary string into its according hex value

		// assume an empty header string
		$headerString = "";

		// convert the string character by character separated by a space
		for($i=0; $i<strlen($binaryString); $i++){
			$headerString .= bin2hex($binaryString[$i]) . ' ';
		}
		return $headerString;
	}


	// output functions

	function getFullHeader() {
		// fill transaction header
	
		// Start of Header (SOH), 01hex
		$headerString = "00 01 ";

		// requested processing date
		$v = $this->getRequestedProcessingDate();

		// bank clearing number of the receiver
		$v .= $this->getBankClearingNumberReceiver();

		// output sequence number
		$v .= $this->getOutputSequenceNumber();

		// creation date
		$v .= $this->getCreationDate();

		// bank clearing number of the sender
		$v .= $this->getBankClearingNumberSender();

		// data file sender identification
		$v .= $this->getDataFileSenderIdentification();

		// entry sequence number
		$v .= $this->getEntrySequenceNumber();

		// transaction type
		$v .= (string) $this->getTransactionType();

		// payment type
		$v .= $this->getPaymentType();

		// processing flag
		$v .= $this->getProcessingFlag();

		$headerString .= $this->toHex($v);
		//for($i=0; $i<strlen($v); $i++){
		//	$headerString .= bin2hex($v[$i]) . ' ';
		//}

		return $headerString;
	}

	function getFullText() {
		// fill transaction details

		// retrieve the output format
		$dataFormat = $this->getDataFormat();

		// retrieve the type of transaction
		$transactionType = $this->getTransactionType();

		// Start of text (SOT): CRLF+, 0D254E
		// $textString = "0D 25 4E ";
		$v = "";

		switch($transactionType) {
		case 826:
			// reference number
			// $v = "20:";
			// retrieve the identification id
			$v .= $this->getTextFieldValue("orderingPartyIdentification");
			// retrieve the transaction number
			$v .= $this->getTextFieldValue("orderingPartyTransactionNumber");
			$textString .= $this->toHex($v); // . "0D 25 7A ";

			// account to be debited
			// $v = "25:";
			// retrieve the account information without, and with IBAN
			$a1 = $this->getTextFieldValue("accountWithoutIban");
			$a2 = $this->getTextFieldValue("accountWithIban");
			if (trim($a1) == "") {
				$v = $a2;
			} else {
				$v = $a1;
			}
			$textString .= $this->toHex($v); // . "0D 25 7A ";

			// payment amount
			// $v = "32A:";
			// retrieve the value date of the payment
			$v = $this->getTextFieldValue("paymentValueDate");
			// retrieve the ISO currency code
			$v .= $this->getTextFieldValue("paymentCurrencyCode");
			// retrieve the payment amount
			$v .= $this->getTextFieldValue("paymentAmount");

			$textString .= $this->toHex($v);

			// 14x spaces
			$v = str_repeat(" ", 14);
			$textString .= $this->toHex($v);

			// continue with segment #2
			$textString .= "00 02 ";

			// ordering party
			// $v = "50:";
			$v = $this->getTextFieldValue("orderingPartyLine1");
			$textString .= $this->toHex($v);// . "0D 25 ";
			$v = $this->getTextFieldValue("orderingPartyLine2");
			$textString .= $this->toHex($v);// . "0D 25 ";
			$v = $this->getTextFieldValue("orderingPartyLine3");
			$textString .= $this->toHex($v);// . "0D 25 ";
			$v = $this->getTextFieldValue("orderingPartyLine4");
			$textString .= $this->toHex($v);// . "0D 25 7A ";

			// 46x spaces
			$v = str_repeat(" ", 46);
			$textString .= $this->toHex($v);

			// continue with segment #3
			$textString .= "00 03 ";

			// beneficiary
			// $v = "59:";
			// retrieve the indentification of the beneficiary
			$v = $this->getTextFieldValue("beneficiaryPartyIdentification");
			$textString .= $this->toHex($v);
			$v = $this->getTextFieldValue("beneficiaryPartyLine1");
			$textString .= $this->toHex($v);// . "0D 25 ";
			$v = $this->getTextFieldValue("beneficiaryPartyLine2");
			$textString .= $this->toHex($v);// . "0D 25 ";
			$v = $this->getTextFieldValue("beneficiaryPartyLine3");
			$textString .= $this->toHex($v);// . "0D 25 ";
			$v = $this->getTextFieldValue("beneficiaryPartyLine4");
			$textString .= $this->toHex($v);// . "0D 25 7A ";

			// reason for payment
			//$v = "70:";
			// retrieve the ISR reference number
			$v = $this->getTextFieldValue("isrReferenceNumber");
			// retrieve the ISR check digit
			$v .= $this->getTextFieldValue("isrCheckDigit");

			$textString .= $this->toHex($v);
		
			// 5x spaces
			$v = str_repeat(" ", 5);
			$textString .= $this->toHex($v);
			break;

		case 827:
			// reference number
			// $v = "20:";
			// retrieve the identification of the ordering party
			$v .= $this->getTextFieldValue("orderingPartyIdentification");
			// retrieve the transaction number of the ordering party
			$v .= $this->getTextFieldValue("orderingPartyTransactionNumber");
			$textString .= $this->toHex($v); // . "0D 25 7A ";

			// account to be debited
			// $v = "25:";
			// retrieve the account information without, and with IBAN
			$a1 = $this->getTextFieldValue("accountWithoutIban");
			$a2 = $this->getTextFieldValue("accountWithIban");
			if (trim($a1) == "") {
				$v = $a2;
			} else {
				$v = $a1;
			}
			$textString .= $this->toHex($v); // . "0D 25 7A ";

			// payment amount
			// $v = "32A:";
			// retrieve payment value date
			$v = $this->getTextFieldValue("paymentValueDate");
			// retrieve payment ISO currency code
			$v .= $this->getTextFieldValue("paymentCurrencyCode");
			// retrieve payment amount
			$v .= $this->getTextFieldValue("paymentAmount");
			$textString .= $this->toHex($v);

			// 14x spaces
			$v = str_repeat(" ", 14);
			$textString .= $this->toHex($v);

			// continue with segment #2
			$textString .= "00 02 ";

			// ordering party
			// $v = "50:";
			$v = $this->getTextFieldValue("orderingPartyLine1");
			$textString .= $this->toHex($v);// . "0D 25 ";
			$v = $this->getTextFieldValue("orderingPartyLine2");
			$textString .= $this->toHex($v);// . "0D 25 ";
			$v = $this->getTextFieldValue("orderingPartyLine3");
			$textString .= $this->toHex($v);// . "0D 25 ";
			$v = $this->getTextFieldValue("orderingPartyLine4");
			$textString .= $this->toHex($v);// . "0D 25 7A ";

			// 30x spaces
			$v = str_repeat(" ", 30);
			$textString .= $this->toHex($v);

			// continue with segment #3
			$textString .= "00 03 ";

			// beneficiary (59)
			// $v = "59:";
			$v = $this->getTextFieldValue("beneficiaryPartyAccount");
			$textString .= $this->toHex($v);
			$v = $this->getTextFieldValue("beneficiaryPartyLine1");
			$textString .= $this->toHex($v);// . "0D 25 ";
			$v = $this->getTextFieldValue("beneficiaryPartyLine2");
			$textString .= $this->toHex($v);// . "0D 25 ";
			$v = $this->getTextFieldValue("beneficiaryPartyLine3");
			$textString .= $this->toHex($v);// . "0D 25 ";
			$v = $this->getTextFieldValue("beneficiaryPartyLine4");
			$textString .= $this->toHex($v);// . "0D 25 7A ";

			// continue with segment #4
			$textString .= "00 04 ";

			// reason for payment / message of the beneficiary
			//$v = "70:";
			$v = $this->getTextFieldValue("beneficiaryMessageLine1");
			$v .= $this->getTextFieldValue("beneficiaryMessageLine2");
			$v .= $this->getTextFieldValue("beneficiaryMessageLine3");
			$v .= $this->getTextFieldValue("beneficiaryMessageLine4");
			$textString .= $this->toHex($v);
		
			// 14x spaces
			$v = str_repeat(" ", 14);
			$textString .= $this->toHex($v);

			// continue with segment #5
			$textString .= "00 05 ";

			// end beneficiary (55)
			//$v = "70:";
			$v = $this->getTextFieldValue("endBeneficiaryPartyAccount");
			$v .= $this->getTextFieldValue("endBeneficiaryPartyLine1");
			$v .= $this->getTextFieldValue("endBeneficiaryPartyLine2");
			$v .= $this->getTextFieldValue("endBeneficiaryPartyLine3");
			$v .= $this->getTextFieldValue("endBeneficiaryPartyLine4");
			$textString .= $this->toHex($v);
			break;

		case 830:
			// reference number
			// $v = "20:";
			// retrieve the identification of the ordering party
			$v .= $this->getTextFieldValue("orderingPartyIdentification");
			// retrieve the transaction number of the ordering party
			$v .= $this->getTextFieldValue("orderingPartyTransactionNumber");
			$textString .= $this->toHex($v); // . "0D 25 7A ";

			// account to be debited
			// $v = "25:";
			// retrieve the account with, and without IBAN
			$a1 = $this->getTextFieldValue("accountWithoutIban");
			$a2 = $this->getTextFieldValue("accountWithIban");
			if (trim($a1) == "") {
				$v = $a2;
			} else {
				$v = $a1;
			}
			$textString .= $this->toHex($v); // . "0D 25 7A ";

			// payment amount
			// $v = "32A:";
			// retrieve the payment value date
			$v = $this->getTextFieldValue("paymentValueDate");
			// retrieve the payment ISO currency code
			$v .= $this->getTextFieldValue("paymentCurrencyCode");
			// retrieve the payment amount
			$v .= $this->getTextFieldValue("paymentAmount");
			$textString .= $this->toHex($v);

			// 11x spaces
			$v = str_repeat(" ", 11);
			$textString .= $this->toHex($v);

			// continue with segment #2
			$textString .= "00 02 ";

			// convertion rate (36)
			$v = $this->getTextFieldValue("convertionRate");
			$textString .= $this->toHex($v);

			// ordering party
			// $v = "50:";
			$v = $this->getTextFieldValue("orderingPartyLine1");
			$textString .= $this->toHex($v);// . "0D 25 ";
			$v = $this->getTextFieldValue("orderingPartyLine2");
			$textString .= $this->toHex($v);// . "0D 25 ";
			$v = $this->getTextFieldValue("orderingPartyLine3");
			$textString .= $this->toHex($v);// . "0D 25 ";
			$v = $this->getTextFieldValue("orderingPartyLine4");
			$textString .= $this->toHex($v);// . "0D 25 7A ";

			// 18x spaces
			$v = str_repeat(" ", 18);
			$textString .= $this->toHex($v);

			// continue with segment #3
			$textString .= "00 03 ";

			// beneficiary's institution (57a or 57d)
			// - identification bank address, A or D
			//   assume: A
			$option = "A";
			$s = $this->getTextFieldValue("beneficiaryInstituteLine1");
			if(trim($s) == "") {
				// no information, so assume D
				$option = "D";
			}
			$textString .= $this->toHex($option);

			// - beneficiary institution
			$v = $this->getTextFieldValue("beneficiaryInstituteLine1");
			$v .= $this->getTextFieldValue("beneficiaryInstituteLine2");
			$v .= $this->getTextFieldValue("beneficiaryInstituteLine3");
			$v .= $this->getTextFieldValue("beneficiaryInstituteLine4");
			$v .= $this->getTextFieldValue("beneficiaryInstituteLine5");
			$textString .= $this->toHex($v);

			// 5x spaces
			$v = str_repeat(" ", 5);
			$textString .= $this->toHex($v);

			// continue with segment #4
			$textString .= "00 04 ";

			// beneficiary (59)
			$v = $this->getTextFieldValue("beneficiaryPartyAccount");
			$v .= $this->getTextFieldValue("beneficiaryPartyLine1");
			$v .= $this->getTextFieldValue("beneficiaryPartyLine2");
			$v .= $this->getTextFieldValue("beneficiaryPartyLine3");
			$v .= $this->getTextFieldValue("beneficiaryPartyLine4");
			$textString .= $this->toHex($v);

			// 6x spaces
			$v = str_repeat(" ", 6);
			$textString .= $this->toHex($v);

			// continue with segment #5
			$textString .= "00 05 ";

			// reason for payment (70)
			$v = $this->getTextFieldValue("beneficiaryMessageLine1");
			$v .= $this->getTextFieldValue("beneficiaryMessageLine2");
			$v .= $this->getTextFieldValue("beneficiaryMessageLine3");
			$v .= $this->getTextFieldValue("beneficiaryMessageLine4");
			$textString .= $this->toHex($v);

			// 6x spaces
			$v = str_repeat(" ", 6);
			$textString .= $this->toHex($v);

			// continue with segment #6
			$textString .= "00 06 ";

			// bank payment instructions (72)
			$v = $this->getTextFieldValue("bankPaymentInstruction");
			$v .=  str_repeat(" ", 90);
			$textString .= $this->toHex($v);

			// 6x spaces
			$v = str_repeat(" ", 6);
			$textString .= $this->toHex($v);

			break;

		case 832:
			// reference number
			// $v = "20:";
			// retrieve identification of the ordering party
			$v .= $this->getTextFieldValue("orderingPartyIdentification");
			// retrieve the transaction number of the ordering party
			$v .= $this->getTextFieldValue("orderingPartyTransactionNumber");
			$textString .= $this->toHex($v); // . "0D 25 7A ";

			// account to be debited
			// $v = "25:";
			// retrieve account information without, and with IBAN
			$a1 = $this->getTextFieldValue("accountWithoutIban");
			$a2 = $this->getTextFieldValue("accountWithIban");
			if (trim($a1) == "") {
				$v = $a2;
			} else {
				$v = $a1;
			}
			$textString .= $this->toHex($v); // . "0D 25 7A ";

			// payment amount
			// $v = "32A:";
			// retrieve the payment value date
			$v = $this->getTextFieldValue("paymentValueDate");
			// retrieve the payment ISO currency code
			$v .= $this->getTextFieldValue("paymentCurrencyCode");
			// retrieve the payment amount
			$v .= $this->getTextFieldValue("paymentAmount");
			$textString .= $this->toHex($v);

			// 11x spaces
			$v = str_repeat(" ", 11);
			$textString .= $this->toHex($v);

			// continue with segment #2
			$textString .= "00 02 ";

			// convertion rate (36)
			$v = $this->getTextFieldValue("convertionRate");
			$textString .= $this->toHex($v);

			// ordering party
			// $v = "50:";
			$v = $this->getTextFieldValue("orderingPartyLine1");
			$textString .= $this->toHex($v);// . "0D 25 ";
			$v = $this->getTextFieldValue("orderingPartyLine2");
			$textString .= $this->toHex($v);// . "0D 25 ";
			$v = $this->getTextFieldValue("orderingPartyLine3");
			$textString .= $this->toHex($v);// . "0D 25 ";
			$v = $this->getTextFieldValue("orderingPartyLine4");
			$textString .= $this->toHex($v);// . "0D 25 7A ";

			// 18x spaces
			$v = str_repeat(" ", 18);
			$textString .= $this->toHex($v);

			// continue with segment #3
			$textString .= "00 03 ";

			// beneficiary (59)
			// $v = "59:";
			$v = $this->getTextFieldValue("beneficiaryPartyAccount");
			$textString .= $this->toHex($v);
			$v = $this->getTextFieldValue("beneficiaryPartyLine1");
			$textString .= $this->toHex($v);// . "0D 25 ";
			$v = $this->getTextFieldValue("beneficiaryPartyLine2");
			$textString .= $this->toHex($v);// . "0D 25 ";
			$v = $this->getTextFieldValue("beneficiaryPartyLine3");
			$textString .= $this->toHex($v);// . "0D 25 ";
			$v = $this->getTextFieldValue("beneficiaryPartyLine4");
			$textString .= $this->toHex($v);// . "0D 25 7A ";

			// 6x spaces
			$v = str_repeat(" ", 6);
			$textString .= $this->toHex($v);

			// continue with segment #4
			$textString .= "00 04 ";

			// reason for payment
			//$v = "70:";
			$v = $this->getTextFieldValue("beneficiaryMessageLine1");
			$v .= $this->getTextFieldValue("beneficiaryMessageLine2");
			$v .= $this->getTextFieldValue("beneficiaryMessageLine3");
			$v .= $this->getTextFieldValue("beneficiaryMessageLine4");
			$textString .= $this->toHex($v);

			// 6x spaces
			$v = str_repeat(" ", 6);
			$textString .= $this->toHex($v);

			// continue with segment #5
			$textString .= "00 05 ";

			// bank payment instructions (72)
			$v = $this->getTextFieldValue("bankPaymentInstruction");
			$v .=  str_repeat(" ", 90);
			$textString .= $this->toHex($v);

			// 6x spaces
			$v = str_repeat(" ", 6);
			$textString .= $this->toHex($v);

			break;

		case 836:
			// reference number
			// $v = "20:";
			// retrieve the identification if the ordering party
			$v .= $this->getTextFieldValue("orderingPartyIdentification");
			// retrieve the transaction number of the ordering party
			$v .= $this->getTextFieldValue("orderingPartyTransactionNumber");
			$textString .= $this->toHex($v); // . "0D 25 7A ";

			// account to be debited
			// $v = "25:";
			// retrieve the account information without, and with IBAN
			$a1 = $this->getTextFieldValue("accountWithoutIban");
			$a2 = $this->getTextFieldValue("accountWithIban");
			if (trim($a1) == "") {
				$v = $a2;
			} else {
				$v = $a1;
			}
			$textString .= $this->toHex($v); // . "0D 25 7A ";

			// payment amount
			// $v = "32A:";
			// retrieve the payment value date
			$v = $this->getTextFieldValue("paymentValueDate");
			// retrieve the payment ISO currency code
			$v .= $this->getTextFieldValue("paymentCurrencyCode");
			// retrieve the payment amount
			$v .= $this->getTextFieldValue("paymentAmount");
			$textString .= $this->toHex($v);

			// 11x spaces
			$v = str_repeat(" ", 11);
			$textString .= $this->toHex($v);

			// continue with segment #2
			$textString .= "00 02 ";

			// convertion rate (36)
			$v = $this->getTextFieldValue("convertionRate");
			$textString .= $this->toHex($v);

			// ordering party
			// $v = "50:";
			$v = $this->getTextFieldValue("orderingPartyLine1");
			$textString .= $this->toHex($v);// . "0D 25 ";
			$v = $this->getTextFieldValue("orderingPartyLine2");
			$textString .= $this->toHex($v);// . "0D 25 ";
			$v = $this->getTextFieldValue("orderingPartyLine3");
			$textString .= $this->toHex($v);// . "0D 25 7A ";

			// 9x spaces
			$v = str_repeat(" ", 9);
			$textString .= $this->toHex($v);

			// continue with segment #3
			$textString .= "00 03 ";

			// beneficiary's institution (57a or 57d)
			// - identification bank address, A or D
			//   assume: D
			$option = "D";
			$s = $this->getTextFieldValue("beneficiaryInstituteLine2");
			if(trim($s) == "") {
				// if option is not set assume A
				$option = "A";
			}
			$textString .= $this->toHex($option);

			// - beneficiary institute
			$v = $this->getTextFieldValue("beneficiaryInstituteLine1");
			$v .= $this->getTextFieldValue("beneficiaryInstituteLine2");
			$textString .= $this->toHex($v);

			// iban (58)
			$v = $this->getTextFieldValue("iban");
			$textString .= $this->toHex($v);

			// 21 spaces
			$v = str_repeat(" ", 21);
			$textString .= $this->toHex($v);

			// continue with segment #4
			$textString .= "00 04 ";

			// beneficiary (59)
			// $v = "59:";
			$v = $this->getTextFieldValue("beneficiaryPartyLine1");
			$textString .= $this->toHex($v);// . "0D 25 ";
			$v = $this->getTextFieldValue("beneficiaryPartyLine2");
			$textString .= $this->toHex($v);// . "0D 25 ";
			$v = $this->getTextFieldValue("beneficiaryPartyLine3");
			$textString .= $this->toHex($v);// . "0D 25 ";

			// 21x spaces
			$v = str_repeat(" ", 21);
			$textString .= $this->toHex($v);

			// continue with segment #5
			$textString .= "00 05 ";

			// purpose structure
			$v = $this->getTextFieldValue("purposeStructure");
			$textString .= $this->toHex($v);

			// purpose (70i and 70u)
			$v = $this->getTextFieldValue("purposeLine1");
			$v .= $this->getTextFieldValue("purposeLine2");
			$v .= $this->getTextFieldValue("purposeLine3");
			$textString .= $this->toHex($v);

			// rules for charges
			$v = $this->getTextFieldValue("rulesForCharges");
			if ($v == 'CHG/OUR') {$v = '0';}
			if ($v == 'CHG/BEN') {$v = '1';}
			if ($v == 'CHG/SHA') {$v = '2';}

			$textString .= $this->toHex($v);

			// 19x spaces
			$v = str_repeat(" ", 19);
			$textString .= $this->toHex($v);

			break;

		case 837:
			// reference number
			// $v = "20:";
			// retrieve the identification of the ordering party
			$v .= $this->getTextFieldValue("orderingPartyIdentification");
			// retrieve the tansaction number of the ordering party
			$v .= $this->getTextFieldValue("orderingPartyTransactionNumber");
			$textString .= $this->toHex($v); // . "0D 25 7A ";

			// account to be debited
			// $v = "25:";
			// retrieve the account information without, and with IBAN
			$a1 = $this->getTextFieldValue("accountWithoutIban");
			$a2 = $this->getTextFieldValue("accountWithIban");
			if (trim($a1) == "") {
				$v = $a2;
			} else {
				$v = $a1;
			}
			$textString .= $this->toHex($v); // . "0D 25 7A ";

			// payment amount
			// $v = "32A:";
			// retrieve the payment value date
			$v = $this->getTextFieldValue("paymentValueDate");
			// retrieve the payment ISO currency code
			$v .= $this->getTextFieldValue("paymentCurrencyCode");
			// retrieve the payment amount
			$v .= $this->getTextFieldValue("paymentAmount");
			$textString .= $this->toHex($v);

			// 1x spaces
			$v = str_repeat(" ", 1);
			$textString .= $this->toHex($v);

			// continue with segment #2
			$textString .= "00 02 ";

			// convertion rate (36)
			$v = $this->getTextFieldValue("convertionRate");
			$textString .= $this->toHex($v);

			// ordering party
			// $v = "50:";
			$v = $this->getTextFieldValue("orderingPartyLine1");
			$v .= $this->getTextFieldValue("orderingPartyLine2");
			$v .= $this->getTextFieldValue("orderingPartyLine3");
			$v .= $this->getTextFieldValue("orderingPartyLine4");
			$textString .= $this->toHex($v);// . "0D 25 7A ";

			// 18x spaces
			$v = str_repeat(" ", 18);
			$textString .= $this->toHex($v);

			// continue with segment #3
			$textString .= "00 03 ";

			// beneficiary's institution (57a or 57d)
			// - identification bank address, A or D
			//   assume: D
			$option = "D";
			$s = $this->getTextFieldValue("beneficiaryInstituteLine1");
			if(trim($s) == "") {
				// if option is not set assume A
				$option = "A";
			}
			$textString .= $this->toHex($option);

			// - beneficiary institute
			$v = $this->getTextFieldValue("beneficiaryInstituteLine1");
			$v .= $this->getTextFieldValue("beneficiaryInstituteLine2");
			$v .= $this->getTextFieldValue("beneficiaryInstituteLine3");
			$v .= $this->getTextFieldValue("beneficiaryInstituteLine4");
			$v .= $this->getTextFieldValue("beneficiaryInstituteLine5");
			$textString .= $this->toHex($v);

			// 5x spaces
			$v = str_repeat(" ", 5);
			$textString .= $this->toHex($v);

			// continue with segment #4
			$textString .= "00 04 ";

			// beneficiary (59)
			// $v = "59:";
			$v = $this->getTextFieldValue("beneficiaryPartyAccount");
			$textString .= $this->toHex($v);
			$v = $this->getTextFieldValue("beneficiaryPartyLine1");
			$textString .= $this->toHex($v);// . "0D 25 ";
			$v = $this->getTextFieldValue("beneficiaryPartyLine2");
			$textString .= $this->toHex($v);// . "0D 25 ";
			$v = $this->getTextFieldValue("beneficiaryPartyLine3");
			$textString .= $this->toHex($v);// . "0D 25 ";
			$v = $this->getTextFieldValue("beneficiaryPartyLine4");
			$textString .= $this->toHex($v);// . "0D 25 7A ";

			// 6x spaces
			$v = str_repeat(" ", 6);
			$textString .= $this->toHex($v);

			// continue with segment #5
			$textString .= "00 05 ";

			// iban (58)
			$v = $this->getTextFieldValue("iban");
			$textString .= $this->toHex($v);

			// 92x spaces
			$v = str_repeat(" ", 92);
			$textString .= $this->toHex($v);
			
			// continue with segment #6
			$textString .= "00 06 ";

			// purpose structure
			$v = $this->getTextFieldValue("purposeStructure");
			$textString .= $this->toHex($v);

			// purpose (70i and 70u)
			$v = $this->getTextFieldValue("purposeLine1");
			$v .= $this->getTextFieldValue("purposeLine2");
			$v .= $this->getTextFieldValue("purposeLine3");
			$textString .= $this->toHex($v);

			// rules for charges (71a)
			$v = $this->getTextFieldValue("rulesForCharges");
			if ($v == 'CHG/OUR') {$v = '0';}
			if ($v == 'CHG/BEN') {$v = '1';}
			if ($v == 'CHG/SHA') {$v = '2';}

			$textString .= $this->toHex($v);

			// 19x spaces
			$v = str_repeat(" ", 19);
			$textString .= $this->toHex($v);

			// continue with segment #7
			$textString .= "00 07 ";

			// information structure (72s and 72u)
			$v = $this->getTextFieldValue("informationStructure");
			$textString .= $this->toHex($v);

			$v = $this->getTextFieldValue("instructionLine1");
			$v .= $this->getTextFieldValue("instructionLine2");
			$v .= $this->getTextFieldValue("instructionLine3");
			$textString .= $this->toHex($v);

			// in case the information structure is defined as
			// unstructured ("U") add a fourth line
			if($this->getTextFieldValue("informationStructure") == "U") {
				$v = $this->getTextFieldValue("instructionLine4");

				// add 5x spaces
				$v .= str_repeat(" ", 5);
			} else {
				// add 20x spaces
				$v = str_repeat(" ", 20);
			}

			$textString .= $this->toHex($v);

			break;

		case 890:

			// retrieve the total amount (90)
			$v = $this->getTextFieldValue("total");
			$textString .= $this->toHex($v);

			// 59x spaces
			$v = str_repeat(" ", 59);
			$textString .= $this->toHex($v);
			
			break;
		}

		// End of Text (EOT): CRLF-, 0D2560
		//$textString .= "0D 25 60 ";

		return $textString;
	}

	function getEndOfRecord() {
		// End of Record (ETX): 03hex
		return ""; //"03 ";
	}

	function getFullRecord() {
		// create full transaction record
		// consists of full header, full text, and end of record
		$record = $this->getFullHeader() . $this->getFullText() . $this->getEndOfRecord();

		return $record;
	}

	function outputFullRecord() {
		// visualize a single transaction record as coloured HTML table

		// retrieve full transaction record
		$dtaRecord = $this->getFullRecord();

		// define output table
		// - table header		
		echo "<table border=\"0\" col=\2\" width=\"100%\">\n";
		echo "<tr>\n";
			echo "<td width=\"5%\" bgcolor=\"silver\"><b>Position</b></td>\n";
			echo "<td width=\"65%\" bgcolor=\"silver\"><b>Value (hexadecimal)</b></td>\n";
			echo "<td width=\"30%\" bgcolor=\"silver\"><b>Value (decimal)</b></td>\n";
		echo "</tr>\n";

		// - go through the transaction record character chunk by
		//   character chunk
		$i=0;
		$j=0;
		$itemSelector = 0;
		$asciiString = "";
		while($i<strlen($dtaRecord)){
			// read data from DTA record with a length of three characters
			$partialString = substr($dtaRecord,$i,3);

			if ($j == 0) {
				echo "<tr valign=\"top\">\n";
				echo "<td width=\"5%\">". $i/3 . "</td>\n";
				echo "<td width=\"65%\">\n";
				//echo "<pre>\n";
			}

			// define the default color list
			$colorList = Array();

			// retrieve type of transaction
			$taId = $this->getTransactionType();

			// load pre-defined string chunks with associated colours
			switch($taId) {
				case 826:
				$colorList = Array(
					0 => Array(0,3,"yellow","segment1"),
					1 => Array(6,21,"cyan","requestedProcessingDate"),
					2 => Array(24,57,"lime", "bankClearingNumberBeneficiary"),
					3 => Array(60,72,"red", "outputSequenceNumber"),
					4 => Array(75,90,"blue","creationDate"),
					5 => Array(93,111,"magenta", "bankClearingNumberOrder"),
					6 => Array(114,126,"yellow","dataFileSenderidentification"),
					7 => Array(129,141,"lime","entrySequenceNumber"),
					8 => Array(144,150,"green", "transactionType"),
					9 => Array(153,153,"red","paymentType"),
					10 => Array(156,156,"magenta","processingFlag"),
					11 => Array(159,171,"cyan","orderingPartyIdentification"),
					12 => Array(174,204,"lime","referenceNumber"),
					13 => Array(207,276,"blue","accountToBeDebited"),
					14 => Array(279,294,"cyan","paymentValueDate"),
					15 => Array(297,303,"blue","paymentCurrencyCode"),
					16 => Array(306,339,"lime","paymentAmount"),
					17 => Array(342,381,"magenta","reserve"),
					18 => Array(384,387,"yellow", "segment2"),
					19 => Array(390,447,"lime","address1"),
					20 => Array(450,507,"cyan","address2"),
					21 => Array(510,567,"lime","address3"),
					22 => Array(570,627,"white","address4"),
					23 => Array(630,765,"magenta","reserve"),
					24 => Array(768,771,"yellow", "segment3"),
					25 => Array(774,807,"cyan","beneficiaryPartyIdentification"),
					26 => Array(810,867,"lime","beneficiaryPartyLine1"),
					27 => Array(870,927,"cyan","beneficiaryPartyLine2"),
					28 => Array(930,987,"lime","beneficiaryPartyLine3"),
					29 => Array(990,1047,"cyan","beneficiaryPartyLine4"),
					30 => Array(1050,1128,"red","isrReferenceNumber"),
					31 => Array(1131,1134,"green","checkBytes"),
					32 => Array(1137,1149,"yellow","reserve"),
				);
				break;
			case 827:
				$colorList = Array(
					0 => Array(0,3,"yellow","segment1"),
					1 => Array(6,21,"cyan","requestedProcessingDate"),
					2 => Array(24,57,"lime", "bankClearingNumberBeneficiary"),
					3 => Array(60,72,"red", "outputSequenceNumber"),
					4 => Array(75,90,"blue","creationDate"),
					5 => Array(93,111,"magenta", "bankClearingNumberOrder"),
					6 => Array(114,126,"yellow","dataFileSenderidentification"),
					7 => Array(129,141,"lime","entrySequenceNumber"),
					8 => Array(144,150,"green", "transactionType"),
					9 => Array(153,153,"red","paymentType"),
					10 => Array(156,156,"magenta","processingFlag"),
					11 => Array(159,171,"cyan","orderingPartyIdentification"),
					12 => Array(174,204,"lime","referenceNumber"),
					13 => Array(207,276,"blue","accountToBeDebited"),
					14 => Array(279,294,"cyan","paymentValueDate"),
					15 => Array(297,303,"blue","paymentCurrencyCode"),
					16 => Array(306,339,"lime","paymentAmount"),
					17 => Array(342,381,"magenta","reserve"),
					18 => Array(384,387,"yellow", "segment2"),
					19 => Array(390,447,"lime","address1"),
					20 => Array(450,507,"cyan","address2"),
					21 => Array(510,567,"lime","address3"),
					22 => Array(570,627,"white","address4"),
					23 => Array(630,765,"magenta","reserve"),
					24 => Array(768,771,"yellow", "segment3"),
					25 => Array(774,861,"cyan","beneficiaryPartyAccount"),
					26 => Array(864,933,"lime","beneficiaryPartyLine1"),
					27 => Array(936,1005,"cyan","beneficiaryPartyLine2"),
					28 => Array(1008,1077,"lime","beneficiaryPartyLine3"),
					29 => Array(1080,1149,"cyan","beneficiaryPartyLine4"),
					30 => Array(1152,1155,"yellow", "segment4"),
					31 => Array(1158,1239,"lime","beneficiaryMessageLine1"),
					32 => Array(1242,1323,"blue","beneficiaryMessageLine2"),
					33 => Array(1326,1407,"red","beneficiaryMessageLine3"),
					34 => Array(1410,1491,"green","beneficiaryMessageLine4"),
					35 => Array(1494,1533,"yellow","reserve"),
					36 => Array(1536,1539,"yellow", "segment5"),
					37 => Array(1542,1629,"cyan","endBeneficiaryPartyAccount"),
					38 => Array(1632,1701,"yellow","endBeneficiaryPartyLine1"),
					39 => Array(1704,1773, "red","endBeneficiaryPartyLine2"),
					40 => Array(1776,1845,"blue","endBeneficiaryPartyLine3"),
					41 => Array(1848,1917,"green","endBeneficiaryPartyLine4")
				);
				break;

			case 830:
				$colorList = Array(
					0 => Array(0,3,"yellow","segment1"),
					1 => Array(6,21,"cyan","requestedProcessingDate"),
					2 => Array(24,57,"lime", "bankClearingNumberBeneficiary"),
					3 => Array(60,72,"red", "outputSequenceNumber"),
					4 => Array(75,90,"blue","creationDate"),
					5 => Array(93,111,"magenta", "bankClearingNumberOrder"),
					6 => Array(114,126,"yellow","dataFileSenderidentification"),
					7 => Array(129,141,"lime","entrySequenceNumber"),
					8 => Array(144,150,"green", "transactionType"),
					9 => Array(153,153,"red","paymentType"),
					10 => Array(156,156,"magenta","processingFlag"),
					11 => Array(159,171,"cyan","orderingPartyIdentification"),
					12 => Array(174,204,"lime","referenceNumber"),
					13 => Array(207,276,"blue","accountToBeDebited"),
					14 => Array(279,294,"cyan","paymentValueDate"),
					15 => Array(297,303,"blue","paymentCurrencyCode"),
					16 => Array(306,348,"lime","paymentAmount"),
					17 => Array(351,381,"silver","reserve"),
					18 => Array(384,387,"yellow","segment2"),
					19 => Array(390,423,"cyan","convertionRate"),
					20 => Array(426,495,"magenta","orderingPartyLine1"),
					21 => Array(498,567,"orange","orderingPartyLine2"),
					22 => Array(570,639,"lime","orderingPartyLine3"),
					23 => Array(642,711,"yellow","orderingPartyLine4"),
					24 => Array(714,765,"silver","reserve"),
					25 => Array(768,771,"yellow","segment3"),
					26 => Array(774,774,"red","identificationBankAddress"),
					27 => Array(777,846,"cyan","beneficiaryInstituteLine1"),
					28 => Array(849,918,"lime","beneficiaryInstituteLine2"),
					29 => Array(921,990,"yellow","beneficiaryInstituteLine3"),
					30 => Array(993,1062,"cyan","beneficiaryInstituteLine4"),
					31 => Array(1065,1134,"red","beneficiaryInstituteLine5"),
					32 => Array(1137,1149,"silver","reserve"),
					33 => Array(1152,1155,"yellow","segment4"),
					34 => Array(1158,1227,"orange","beneficiaryPartyAccount"),
					35 => Array(1230,1299,"magenta","beneficiaryPartyLine1"),
					36 => Array(1302,1371,"blue","beneficiaryPartyLine2"),
					37 => Array(1374,1443,"brown","beneficiaryPartyLine3"),
					38 => Array(1446,1515,"yellow","beneficiaryPartyLine4"),
					39 => Array(1518,1533,"silver","reserve"),
					40 => Array(1536,1539,"yellow","segment5"),
					41 => Array(1542,1629,"green","beneficiaryMessageLine1"),
					42 => Array(1632,1719,"cyan","beneficiaryMessageLine2"),
					43 => Array(1722,1809,"magenta","beneficiaryMessageLine3"),
					44 => Array(1812,1899,"blue","beneficiaryMessageLine4"),
					45 => Array(1902,1917,"silver","reserve"),
					46 => Array(1920,1923,"yellow","segment6"),
					47 => Array(1926,2013,"cyan","bankPaymentInstruction1"),
					48 => Array(2016,2103,"yellow","bankPaymentInstruction2"),
					49 => Array(2106,2193,"red","bankPaymentInstruction3"),
					50 => Array(2196,2283,"magenta","bankPaymentInstruction4"),
					51 => Array(2286,2301,"silver","reserve")
				);
				break;

			case 832:
				$colorList = Array(
					0 => Array(0,3,"yellow","segment1"),
					1 => Array(6,21,"cyan","requestedProcessingDate"),
					2 => Array(24,57,"lime", "bankClearingNumberBeneficiary"),
					3 => Array(60,72,"red", "outputSequenceNumber"),
					4 => Array(75,90,"blue","creationDate"),
					5 => Array(93,111,"magenta", "bankClearingNumberOrder"),
					6 => Array(114,126,"yellow","dataFileSenderidentification"),
					7 => Array(129,141,"lime","entrySequenceNumber"),
					8 => Array(144,150,"green", "transactionType"),
					9 => Array(153,153,"red","paymentType"),
					10 => Array(156,156,"magenta","processingFlag"),
					11 => Array(159,171,"cyan","orderingPartyIdentification"),
					12 => Array(174,204,"lime","referenceNumber"),
					13 => Array(207,276,"blue","accountToBeDebited"),
					14 => Array(279,294,"cyan","paymentValueDate"),
					15 => Array(297,303,"blue","paymentCurrencyCode"),
					16 => Array(306,348,"lime","paymentAmount"),
					17 => Array(351,381,"silver","reserve"),
					18 => Array(384,387,"yellow","segment2"),
					19 => Array(390,423,"cyan","convertionRate"),
					20 => Array(426,495,"magenta","orderingPartyLine1"),
					21 => Array(498,567,"orange","orderingPartyLine2"),
					22 => Array(570,639,"lime","orderingPartyLine3"),
					23 => Array(642,711,"yellow","orderingPartyLine4"),
					24 => Array(714,765,"silver","reserve"),
					25 => Array(768,771,"yellow","segment3"),
					26 => Array(774,843,"cyan","beneficiaryPartyAccount"),
					27 => Array(846,915,"blue","beneficiaryPartyLine1"),
					28 => Array(918,987,"red","beneficiaryPartyLine2"),
					29 => Array(990,1059,"orange","beneficiaryPartyLine3"),
					30 => Array(1062,1131,"lime","beneficiaryPartyLine4"),
					31 => Array(1134,1149,"silver","reserve"),
					32 => Array(1152,1155,"yellow","segment4"),
					33 => Array(1158,1245,"lime","beneficiaryMessageLine1"),
					34 => Array(1248,1335,"cyan","beneficiaryMessageLine2"),
					35 => Array(1338,1425,"magenta","beneficiaryMessageLine3"),
					36 => Array(1428,1515,"blue","beneficiaryMessageLine4"),
					37 => Array(1518,1533,"silver","reserve"),
					38 => Array(1536,1539,"yellow","segment5"),
					39 => Array(1542,1629,"cyan","bankPaymentInstructions1"),
					40 => Array(1632,1719,"blue","bankPaymentInstructions2"),
					41 => Array(1722,1809,"magenta","bankPaymentInstructions3"),
					42 => Array(1812,1899,"yellow","bankPaymentInstructions4"),
					43 => Array(1902,1917,"silver","reserve")
				);
				break;
			case 836:
				$colorList = Array(
					0 => Array(0,3,"yellow","segment1"),
					1 => Array(6,21,"cyan","requestedProcessingDate"),
					2 => Array(24,57,"lime", "bankClearingNumberBeneficiary"),
					3 => Array(60,72,"red", "outputSequenceNumber"),
					4 => Array(75,90,"blue","creationDate"),
					5 => Array(93,111,"magenta", "bankClearingNumberOrder"),
					6 => Array(114,126,"yellow","dataFileSenderidentification"),
					7 => Array(129,141,"lime","entrySequenceNumber"),
					8 => Array(144,150,"green", "transactionType"),
					9 => Array(153,153,"red","paymentType"),
					10 => Array(156,156,"magenta","processingFlag"),
					11 => Array(159,171,"cyan","orderingPartyIdentification"),
					12 => Array(174,204,"lime","referenceNumber"),
					13 => Array(207,276,"blue","accountToBeDebited"),
					14 => Array(279,294,"cyan","paymentValueDate"),
					15 => Array(297,303,"blue","paymentCurrencyCode"),
					16 => Array(306,348,"lime","paymentAmount"),
					17 => Array(351,381,"silver","reserve"),
					18 => Array(384,387,"yellow","segment2"),
					19 => Array(390,423,"cyan","convertionRate"),
					20 => Array(426,528,"magenta","orderingPartyLine1"),
					21 => Array(531,633,"orange","orderingPartyLine2"),
					22 => Array(636,738,"lime","orderingPartyLine3"),
					23 => Array(741,765,"silver","reserve"),
					24 => Array(768,771,"yellow","segment3"),
					25 => Array(774,774,"red","identificationBankAddress"),
					26 => Array(777,879,"orange","beneficiaryInstituteLine1"),
					27 => Array(882,984,"magenta","beneficiaryInstituteLine2"),
					28 => Array(987,1086,"lime","iban"),
					29 => Array(1089,1149,"silver","reserve"),
					30 => Array(1152,1155,"yellow","segment4"),
					31 => Array(1158,1260,"cyan","beneficiaryPartyLine1"),
					32 => Array(1263,1365,"magenta","beneficiaryPartyLine2"),
					33 => Array(1368,1470,"lime","beneficiaryPartyLine3"),
					34 => Array(1473,1533,"silver","reserve"),
					35 => Array(1536,1539,"yellow","segment5"),
					36 => Array(1542,1542,"red","identificationPurpose"),
					37 => Array(1545,1647,"lime","purposeLine1"),
					38 => Array(1650,1752,"blue","purposeLine2"),
					39 => Array(1755,1857,"red","purposeLine3"),
					40 => Array(1860,1860,"green","rulesForCharges"),
					41 => Array(1863,1917,"silver","reserve")
				);
				break;
			case 837:
				$colorList = Array(
					0 => Array(0,3,"yellow","segment1"),
					1 => Array(6,21,"cyan","requestedProcessingDate"),
					2 => Array(24,57,"lime", "bankClearingNumberBeneficiary"),
					3 => Array(60,72,"red", "outputSequenceNumber"),
					4 => Array(75,90,"blue","creationDate"),
					5 => Array(93,111,"magenta", "bankClearingNumberOrder"),
					6 => Array(114,126,"yellow","dataFileSenderidentification"),
					7 => Array(129,141,"lime","entrySequenceNumber"),
					8 => Array(144,150,"green", "transactionType"),
					9 => Array(153,153,"red","paymentType"),
					10 => Array(156,156,"magenta","processingFlag"),
					11 => Array(159,171,"cyan","orderingPartyIdentification"),
					12 => Array(174,204,"lime","referenceNumber"),
					13 => Array(207,306,"blue","accountToBeDebited"),
					14 => Array(309,324,"cyan","paymentValueDate"),
					15 => Array(327,333,"blue","paymentCurrencyCode"),
					16 => Array(336,378,"lime","paymentAmount"),
					17 => Array(381,381,"silver","reserve"),
					18 => Array(384,387,"yellow","segment2"),
					19 => Array(390,423,"red","conversionRate"),
					20 => Array(426,495,"cyan","orderingPartyLine1"),
					21 => Array(498,567,"blue","orderingPartyLine2"),
					22 => Array(570,639,"lime","orderingPartyLine3"),
					23 => Array(642,711,"red","orderingPartyLine4"),
					24 => Array(714,765,"silver","reserve"),
					25 => Array(768,771,"yellow","segment3"),
					26 => Array(774,774,"magenta","identificationBankAddress"),
					27 => Array(777,846,"orange","beneficiaryInstituteLine1"),
					28 => Array(849,918,"blue","beneficiaryInstituteLine2"),
					29 => Array(921,990,"green","beneficiaryInstituteLine3"),
					30 => Array(993,1062,"lime","beneficiaryInstituteLine4"),
					31 => Array(1065,1134,"cyan","beneficiaryInstituteLine5"),
					32 => Array(1137,1149,"silver","reserve"),
					33 => Array(1152,1155,"yellow","segment4"),
					34 => Array(1158,1227,"blue","beneficiaryAccountNumber"),
					35 => Array(1230,1299,"orange","beneficiaryPartyLine1"),
					36 => Array(1302,1371,"magenta","beneficiaryPartyLine2"),
					37 => Array(1374,1443,"cyan","beneficiaryPartyLine3"),
					38 => Array(1446,1515,"red","beneficiaryPartyLine4"),
					39 => Array(1518,1533,"silver","reserve"),
					40 => Array(1536,1539,"yellow","segment5"),
					41 => Array(1542,1641,"lime","iban"),
					42 => Array(1644,1917,"silver","reserve"),
					43 => Array(1920,1923,"yellow","segment6"),
					44 => Array(1926,1926,"red","identificationPurpose"),
					45 => Array(1929,2031,"cyan","purposeLine1"),
					46 => Array(2034,2136,"yellow","purposeLine2"),
					47 => Array(2139,2241,"magenta","purposeLine3"),
					48 => Array(2244,2244,"green","rulesForCharges"),
					49 => Array(2247,2301,"silver","reserve"),
					50 => Array(2304,2307,"yellow","segment7"),
					51 => Array(2310,2310,"red","informationStructure")
				);

				if($this->getTextFieldValue("informationStructure") == "U") {
					$colorList[52] = Array(2313,2400,"magenta","instructionLine1");
					$colorList[53] = Array(2403,2490,"blue","instructionLine2");
					$colorList[54] = Array(2493,2580,"lime","instructionLine3");
					$colorList[55] = Array(2583,2670,"red","instructionLine4");
					$colorList[56] = Array(2673,2685,"silver","reserve");
				} else {
					$colorList[52] = Array(2313,2415,"magenta","instructionLine1");
					$colorList[53] = Array(2418,2520,"blue","instructionLine2");
					$colorList[54] = Array(2523,2625,"lime","instructionLine3");
					$colorList[55] = Array(2628,2685,"silver","reserve");
				};
				break;

			case 890:
				$colorList = Array(
					0 => Array(0,3,"yellow","segment1"),
					1 => Array(6,21,"cyan","requestedProcessingDate"),
					2 => Array(24,57,"lime", "bankClearingNumberBeneficiary"),
					3 => Array(60,72,"red", "outputSequenceNumber"),
					4 => Array(75,90,"blue","creationDate"),
					5 => Array(93,111,"magenta", "bankClearingNumberOrder"),
					6 => Array(114,126,"yellow","dataFileSenderidentification"),
					7 => Array(129,141,"lime","entrySequenceNumber"),
					8 => Array(144,150,"green", "transactionType"),
					9 => Array(153,153,"red","paymentType"),
					10 => Array(156,156,"magenta","processingFlag"),
					11 => Array(159,204,"cyan","total"),
					12 => Array(207,381,"silver","reserve")
				);
				break;
			};

			// prepare output
			if(array_key_exists($itemSelector, $colorList)) {

				// string start position
				$from = $colorList[$itemSelector][0];

				// string end position
				$to = $colorList[$itemSelector][1];

				// output background colour
				$bgColor = $colorList[$itemSelector][2];

				// adjust position if needed
				if($i == $to) {$itemSelector++;}
			} else {
				// set default colour to white
				$bgColor = "white";
			}

			// start output as typewriter style
			echo "<kbd style=\"background-color:$bgColor\">" . $partialString . "</kbd>";

			// evaluate asciiString to remove characters that cannot 
			// be displayed, properly
			$asciiValue = base_convert(trim($partialString), 16, 10);
			if ($asciiValue < 20) {
				$asciiValue = "- &nbsp;";
			} else {
				$asciiValue = chr($asciiValue) . "&nbsp;";
			}
			$asciiString .= "<kbd style=\"background-color:$bgColor\">" . $asciiValue . "</kbd>";

			$i += 3;
			$j++;

			// start a new table row after 384 characters
			if (($i % 384) == 0) {
				echo "<br>&nbsp;<br>\n";
				echo "</td>\n";
				echo "<td width=\"30%\">$asciiString</td>\n";
				echo "</tr>\n";
				$j = 0;
				$asciiString = "";
			}

			// start a new table cell after 32 characters
			if ($j == 32) {
				// echo "</pre>\n";
				echo "</td>\n";
				echo "<td width=\"30%\">$asciiString</td>\n";
				echo "</tr>\n";
				$j = 0;
				$asciiString = "";
			}
		}
			
		//$dtaRecord;
		//echo "</pre>\n";

		// end of table
		echo "</table>\n";
		echo "<br>\n";

		return;
	}

	// csv import and export functions

	function toCsv() {
		// no content, currently
		return;
	}

	function importCsv($csvLine) {
		// fill dta object based on csv line

		if (trim($csvLine) == "") {
			// we got empty spaces, only, so return False
			return False;
		}
		
		// split line into several pieces
		$transactionDetails = explode(";", $csvLine);

		// validate number of fields
		// define the according value per transaction
		$fieldList = array(
			826 => 27,
			827 => 35,
			830 => 37,
			832 => 31,
			836 => 32,
			837 => 43,
			890 => 2
		);
		
		// validate field entry 0 - this is the transaction id
		$taId = $transactionDetails[0];
		// - valid transaction id
		if (in_array($taId, array_keys($fieldList)) == False) {
			// transaction id not in list -- abort
			echo "<i>transaction id not in list</i>\n";
			return False;
		}

		// - according number of fields
		if (count($transactionDetails) != $fieldList[$taId]) {
			// expected number of fields differs from the actual list of csv fields
			echo "<i>field mismatch</i>\n";
			return False;
		}

		// read dta header -- used for all transactions
		// transactionType (entry 0)
		$this->setTransactionType($taId);

		// requestedProcessingDate (entry 1)
		$this->setRequestedProcessingDate($transactionDetails[1]);

		// bankClearingNumberBeneficiary (entry 2)
		$this->setBankClearingNumberReceiver($transactionDetails[2]);

		// outputSequenceNumber (entry 3)
		$this->setOutputSequenceNumber($transactionDetails[3]);
				
		// creationDate (entry 4)
		$this->setCreationDate($transactionDetails[4]);
				
		// bankClearingNumberOrder (entry 5)
		$this->setBankClearingNumberSender($transactionDetails[5]);
				
		// dataFileSenderidentification (entry 6)
		$this->setDataFileSenderIdentification($transactionDetails[6]);
				
		// entrySequenceNumber (entry 7)
		$this->setEntrySequenceNumber($transactionDetails[7]);
				
		// paymentType (entry 8)
		$this->setPaymentType($transactionDetails[8]);
				
		// processingFlag (entry 9)
		$this->setProcessingFlag($transactionDetails[9]);
		
		// orderingPartyIdentification (entry 10)
		$this->addTextField("orderingPartyIdentification", $transactionDetails[10]);

		// orderingPartyTransactionNumber (entry 11)
		$this->addTextField("orderingPartyTransactionNumber", $transactionDetails[11]);


		// accountToBeDebited (entry 12)
		$accountToBeDebited = $transactionDetails[12];
		$this->addTextField("accountWithoutIban", "");
		$this->addTextField("accountWithIban", "");

		if ($this->isIban($accountToBeDebited)) {
			$this->setTextFieldValue("accountWithIban", $accountToBeDebited);
		} else {
			$this->setTextFieldValue("accountWithoutIban", $accountToBeDebited);
		}

		// paymentValueDate (entry 13)
		$this->addTextField("paymentValueDate", $transactionDetails[13]);

		// paymentCurrencyCode (entry 14)
		$this->addTextField("paymentCurrencyCode", $transactionDetails[14]);

		// paymentAmount (entry 15)
		$this->addTextField("paymentAmount", $transactionDetails[15]);

		switch($taId) {
		case 826:

			// orderingPartyLine1 (entry 16)
			$this->addTextField("orderingPartyLine1", $transactionDetails[16]);

			// orderingPartyLine2 (entry 17)
			$this->addTextField("orderingPartyLine2", $transactionDetails[17]);

			// orderingPartyLine3 (entry 18)
			$this->addTextField("orderingPartyLine3", $transactionDetails[18]);

			// orderingPartyLine4 (entry 19)
			$this->addTextField("orderingPartyLine4", $transactionDetails[19]);

			// beneficiaryPartyIdentification (entry 20)
			$this->addTextField("beneficiaryPartyIdentification", $transactionDetails[20]);

			// beneficiaryPartyLine1 (entry 21)
			$this->addTextField("beneficiaryPartyLine1", $transactionDetails[21]);

			// beneficiaryPartyLine2 (entry 22)
			$this->addTextField("beneficiaryPartyLine2", $transactionDetails[22]);

			// beneficiaryPartyLine3 (entry 23)
			$this->addTextField("beneficiaryPartyLine3", $transactionDetails[23]);

			// beneficiaryPartyLine4 (entry 24)
			$this->addTextField("beneficiaryPartyLine4", $transactionDetails[24]);

			// isrReferenceNumber (entry 25)
			$this->addTextField("isrReferenceNumber", $transactionDetails[25]);

			// checkDigit (entry 26)
			$this->addTextField("isrCheckDigit", $transactionDetails[26]);
			
			break;

		case 827:
			// orderingPartyLine1 (entry 16)
			$this->addTextField("orderingPartyLine1", $transactionDetails[16]);

			// orderingPartyLine2 (entry 17)
			$this->addTextField("orderingPartyLine2", $transactionDetails[17]);

			// orderingPartyLine3 (entry 18)
			$this->addTextField("orderingPartyLine3", $transactionDetails[18]);

			// orderingPartyLine4 (entry 19)
			$this->addTextField("orderingPartyLine4", $transactionDetails[19]);

			// beneficiaryPartyAccount (entry 20)
			$this->addTextField("beneficiaryPartyAccount", $transactionDetails[20]);

			// beneficiaryPartyLine1 (entry 21)
			$this->addTextField("beneficiaryPartyLine1", $transactionDetails[21]);

			// beneficiaryPartyLine2 (entry 22)
			$this->addTextField("beneficiaryPartyLine2", $transactionDetails[22]);

			// beneficiaryPartyLine3 (entry 23)
			$this->addTextField("beneficiaryPartyLine3", $transactionDetails[23]);

			// beneficiaryPartyLine4 (entry 24)
			$this->addTextField("beneficiaryPartyLine4", $transactionDetails[24]);

			// beneficiaryTransferType (entry 25)
			$this->addTextField("beneficiaryTransferType", $transactionDetails[25]);

			// beneficiaryMessageLine1 (entry 26)
			$this->addTextField("beneficiaryMessageLine1", $transactionDetails[26]);

			// beneficiaryMessageLine2 (entry 27)
			$this->addTextField("beneficiaryMessageLine2", $transactionDetails[27]);

			// beneficiaryMessageLine3 (entry 28)
			$this->addTextField("beneficiaryMessageLine3", $transactionDetails[28]);

			// beneficiaryMessageLine4 (entry 29)
			$this->addTextField("beneficiaryMessageLine4", $transactionDetails[29]);

			// endBeneficiaryPartyAccount (entry 30)
			$this->addTextField("endBeneficiaryPartyAccount", $transactionDetails[30]);

			// endBeneficiaryPartyLine1 (entry 31)
			$this->addTextField("endBeneficiaryPartyLine1", $transactionDetails[31]);

			// endBeneficiaryPartyLine2 (entry 32)
			$this->addTextField("endBeneficiaryPartyLine2", $transactionDetails[32]);

			// endBeneficiaryPartyLine3 (entry 33)
			$this->addTextField("endBeneficiaryPartyLine3", $transactionDetails[33]);

			// endBeneficiaryPartyLine4 (entry 34)
			$this->addTextField("endBeneficiaryPartyLine4", $transactionDetails[34]);
			
			break;

		case 830:
			// convertionRate (entry 16) (36)
			$this->addTextField("convertionRate", $transactionDetails[16]);

			// orderingPartyLine1 (entry 17) (50)
			$this->addTextField("orderingPartyLine1", $transactionDetails[17]);

			// orderingPartyLine2 (entry 18) (50)
			$this->addTextField("orderingPartyLine2", $transactionDetails[18]);

			// orderingPartyLine3 (entry 19) (50)
			$this->addTextField("orderingPartyLine3", $transactionDetails[19]);

			// orderingPartyLine4 (entry 20) (50)
			$this->addTextField("orderingPartyLine4", $transactionDetails[20]);

			// identification bank address (entry 21) (57)
			$this->addTextField("identificationBankAddress", $transactionDetails[21]);

			// beneficiarySwiftAddress (entry 22) (57a or 57d)
			//$dta->addTextField("beneficiarySwiftAddress", $transactionDetails[22]);

			// beneficiaryInstituteLine1 (entry 22) (57a or 57d)
			$this->addTextField("beneficiaryInstituteLine1", $transactionDetails[22]);

			// beneficiaryInstituteLine2 (entry 23) (57a or 57d)
			$this->addTextField("beneficiaryInstituteLine2", $transactionDetails[23]);

			// beneficiaryInstituteLine3 (entry 24) (57a or 57d)
			$this->addTextField("beneficiaryInstituteLine3", $transactionDetails[24]);

			// beneficiaryInstituteLine4 (entry 25) (57a or 57d)
			$this->addTextField("beneficiaryInstituteLine4", $transactionDetails[25]);

			// beneficiaryInstituteLine5 (entry 26) (57a or 57d)
			$this->addTextField("beneficiaryInstituteLine5", $transactionDetails[26]);

			// beneficiaryPartyAccount (entry 27) (59)
			$this->addTextField("beneficiaryPartyAccount", $transactionDetails[27]);

			// beneficiaryPartyLine1 (entry 28) (59)
			$this->addTextField("beneficiaryPartyLine1", $transactionDetails[28]);

			// beneficiaryPartyLine2 (entry 29) (59)
			$this->addTextField("beneficiaryPartyLine2", $transactionDetails[29]);

			// beneficiaryPartyLine3 (entry 30) (59)
			$this->addTextField("beneficiaryPartyLine3", $transactionDetails[30]);

			// beneficiaryPartyLine4 (entry 31) (59)
			$this->addTextField("beneficiaryPartyLine4", $transactionDetails[31]);

			// reason for payment (70)
			// beneficiaryMessageLine1 (entry 32)
			$this->addTextField("beneficiaryMessageLine1", $transactionDetails[32]);

			// beneficiaryMessageLine2 (entry 33)
			$this->addTextField("beneficiaryMessageLine2", $transactionDetails[33]);

			// beneficiaryMessageLine3 (entry 34)
			$this->addTextField("beneficiaryMessageLine3", $transactionDetails[34]);

			// beneficiaryMessageLine4 (entry 35)
			$this->addTextField("beneficiaryMessageLine4", $transactionDetails[35]);

			// bankPaymentInstruction (entry 36) (72)
			$this->addTextField("bankPaymentInstruction", $transactionDetails[36]);

			break;

		case 832:
			// convertionRate (entry 16) (36)
			$this->addTextField("convertionRate", $transactionDetails[16]);

			// orderingPartyLine1 (entry 17) (50)
			$this->addTextField("orderingPartyLine1", $transactionDetails[17]);

			// orderingPartyLine2 (entry 18) (50)
			$this->addTextField("orderingPartyLine2", $transactionDetails[18]);

			// orderingPartyLine3 (entry 19) (50)
			$this->addTextField("orderingPartyLine3", $transactionDetails[19]);

			// orderingPartyLine4 (entry 20) (50)
			$this->addTextField("orderingPartyLine4", $transactionDetails[20]);

			// beneficiaryPartyAccount (entry 21) (59)
			$this->addTextField("beneficiaryPartyAccount", $transactionDetails[21]);

			// beneficiaryPartyLine1 (entry 22) (59)
			$this->addTextField("beneficiaryPartyLine1", $transactionDetails[22]);

			// beneficiaryPartyLine2 (entry 23) (59)
			$this->addTextField("beneficiaryPartyLine2", $transactionDetails[23]);

			// beneficiaryPartyLine3 (entry 24) (59)
			$this->addTextField("beneficiaryPartyLine3", $transactionDetails[24]);

			// beneficiaryPartyLine4 (entry 25) (59)
			$this->addTextField("beneficiaryPartyLine4", $transactionDetails[25]);

			// reason for payment (70)
			// beneficiaryMessageLine1 (entry 26)
			$this->addTextField("beneficiaryMessageLine1", $transactionDetails[26]);

			// beneficiaryMessageLine2 (entry 27)
			$this->addTextField("beneficiaryMessageLine2", $transactionDetails[27]);

			// beneficiaryMessageLine3 (entry 28)
			$this->addTextField("beneficiaryMessageLine3", $transactionDetails[28]);

			// beneficiaryMessageLine4 (entry 29)
			$this->addTextField("beneficiaryMessageLine4", $transactionDetails[29]);

			// bankPaymentInstruction (entry 30) (72)
			$this->addTextField("bankPaymentInstruction", $transactionDetails[30]);

			break;
		case 836:
			// convertionRate (entry 16) (36)
			$this->addTextField("convertionRate", $transactionDetails[16]);

			// orderingPartyLine1 (entry 17) (50)
			$this->addTextField("orderingPartyLine1", $transactionDetails[17]);

			// orderingPartyLine2 (entry 18) (50)
			$this->addTextField("orderingPartyLine2", $transactionDetails[18]);

			// orderingPartyLine3 (entry 19) (50)
			$this->addTextField("orderingPartyLine3", $transactionDetails[19]);

			// identification bank address (entry 20) (57)
			$this->addTextField("identificationBankAddress", $transactionDetails[20]);

			// beneficiaryInstituteLine1 (entry 21) (57a or 57d)
			$this->addTextField("beneficiaryInstituteLine1", $transactionDetails[21]);

			// beneficiaryInstituteLine1 (entry 22) (57a or 57d)
			$this->addTextField("beneficiaryInstituteLine2", $transactionDetails[22]);

			// iban (entry 23) (58)
			$this->addTextField("iban", $transactionDetails[23]);

			// beneficiaryPartyLine1 (entry 24) (59)
			$this->addTextField("beneficiaryPartyLine1", $transactionDetails[24]);

			// beneficiaryPartyLine2 (entry 25) (59)
			$this->addTextField("beneficiaryPartyLine2", $transactionDetails[25]);

			// beneficiaryPartyLine3 (entry 26) (59)
			$this->addTextField("beneficiaryPartyLine3", $transactionDetails[26]);

			// purpose
			// identificationPurpose (entry 27) (70)
			$this->addTextField("purposeStructure", $transactionDetails[27]);

			// purposeLine1 (entry 28) (70)
			$this->addTextField("purposeLine1", $transactionDetails[28]);

			// purposeLine2 (entry 29) (70)
			$this->addTextField("purposeLine2", $transactionDetails[29]);

			// purposeLine3 (entry 30) (70)
			$this->addTextField("purposeLine3", $transactionDetails[30]);

			// rulesForCharges (entry 31) (71a)
			$this->addTextField("rulesForCharges", $transactionDetails[31]);

			break;
		case 837:
			// convertionRate (entry 16) (36)
			$this->addTextField("convertionRate", $transactionDetails[16]);

			// orderingPartyLine1 (entry 17) (50)
			$this->addTextField("orderingPartyLine1", $transactionDetails[17]);

			// orderingPartyLine2 (entry 18) (50)
			$this->addTextField("orderingPartyLine2", $transactionDetails[18]);

			// orderingPartyLine3 (entry 19) (50)
			$this->addTextField("orderingPartyLine3", $transactionDetails[19]);

			// orderingPartyLine4 (entry 20) (50)
			$this->addTextField("orderingPartyLine4", $transactionDetails[20]);

			// identification bank address (entry 21) (57)
			$this->addTextField("identificationBankAddress", $transactionDetails[21]);

			// beneficiaryInstituteLine1 (entry 22) (57a or 57d)
			$this->addTextField("beneficiaryInstituteLine1", $transactionDetails[22]);

			// beneficiaryInstituteLine2 (entry 23) (57a or 57d)
			$this->addTextField("beneficiaryInstituteLine2", $transactionDetails[23]);

			// beneficiaryInstituteLine3 (entry 24) (57a or 57d)
			$this->addTextField("beneficiaryInstituteLine3", $transactionDetails[24]);

			// beneficiaryInstituteLine4 (entry 25) (57a or 57d)
			$this->addTextField("beneficiaryInstituteLine4", $transactionDetails[25]);

			// beneficiaryInstituteLine5 (entry 26) (57a or 57d)
			$this->addTextField("beneficiaryInstituteLine5", $transactionDetails[26]);

			// iban (entry 27) (58)
			$this->addTextField("iban", $transactionDetails[27]);

			// beneficiaryPartyAccount (entry 28) (59)
			$this->addTextField("beneficiaryPartyAccount", $transactionDetails[28]);

			// beneficiaryPartyLine1 (entry 29) (59)
			$this->addTextField("beneficiaryPartyLine1", $transactionDetails[29]);

			// beneficiaryPartyLine2 (entry 30) (59)
			$this->addTextField("beneficiaryPartyLine2", $transactionDetails[30]);

			// beneficiaryPartyLine3 (entry 31) (59)
			$this->addTextField("beneficiaryPartyLine3", $transactionDetails[31]);

			// beneficiaryPartyLine4 (entry 32) (59)
			$this->addTextField("beneficiaryPartyLine4", $transactionDetails[32]);

			// purpose
			// identificationPurpose (entry 33) (70)
			$this->addTextField("purposeStructure", $transactionDetails[33]);

			// purposeLine1 (entry 34) (70)
			$this->addTextField("purposeLine1", $transactionDetails[34]);

			// purposeLine2 (entry 35) (70)
			$this->addTextField("purposeLine2", $transactionDetails[35]);

			// purposeLine3 (entry 36) (70)
			$this->addTextField("purposeLine3", $transactionDetails[36]);

			// rulesForCharges (entry 37) (71a)
			$this->addTextField("rulesForCharges", $transactionDetails[37]);

			// informationStructure (entry 38) (72)
			$this->addTextField("informationStructure", $transactionDetails[38]);

			// instructionLine1 (entry 39)
			$this->addTextField("instructionLine1", $transactionDetails[39]);

			// instructionLine2 (entry 40)
			$this->addTextField("instructionLine2", $transactionDetails[40]);

			// instructionLine3 (entry 41)
			$this->addTextField("instructionLine3", $transactionDetails[41]);

			// instructionLine4 (entry 42)
			$this->addTextField("instructionLine4", $transactionDetails[42]);

			break;

		}

		return True;
	}
}
?>
