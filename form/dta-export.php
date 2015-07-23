<html>
	<head>Exporting DTA transaction data</head>
<body>
	<h2 style="color:white;background:blue">&nbsp;Exporting DTA transaction data</h2>

	<?php

	// include dta-ch object class for payments
	require_once 'dta-ch.php';

	// include basic variables
	require_once 'dta-include.php';

	// validate action parameters
	$action = getAction();
	if ($action == "export"){
		$dfId = getDataFormat();
		$dlId = getDataListFormat();
		$transactions = getTransactions();

		echo "<h3>Transmitted data</h3>";

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
		echo "transmitted values:<br>\n";
		echo "action: $action<br>\n";
		echo "dfId: $dfId<br>\n";
		echo "dlId: $dlId<br>\n";
		echo "adjust: $adjust<br><br>\n";
		echo "transactions: <br>\n";
		if ($transactions != False) {
			$transactionList = array();
			foreach ($transactions as $line) {
				echo "$line<br>\n";

				// - create dtach object
				//   initialize a new dta-ch object
				$dta = new DTACH();

				// - fill object with data
				//   set data format
				$dta->setDataFormat($dfId);

				//   set date of delivery
				$dateOfDelivery = date("ymd");
				$dta->setDateOfDelivery($dateOfDelivery);

				// - import csv data
				$importValue = $dta->importCsv($line);
				if ($importValue == False) {
					echo "error: csv import failed<br>\n";
				}

				// - auto-adjust data
				//   auto-adjust values coming from the input fields
				if ($adjust) {
					$dta->adjustHeader();
					$dta->adjustDataFields();
				}

				// - validate data
				//   validate header and data without automatic correction
				$dta->validateHeader();
				$dta->validateDataFields();

				$transactionList[] = $dta;				
			}

			// create sum record (TA 890)

			// default colors
			$backgroundColorTableHeader = "#add8e6"; // lightblue
			$backgroundColorCellCorrect = "white";
			$backgroundColorCellIncorrect = "red";
			$backgroundColorRow = "silver";

			echo "<h3>Select the transactions to process</h3>\n";

			// display transactions overview as a select form
			echo "<form action=\"dta-display.php\" method=\"post\">\n";

			$transactionListNumber = 0;
			foreach ($transactionList as $dta){

				// prepare dta object
				// retrieve list-value-pairs for the dta header
				$taHeader = $dta->getHeaderFields();

				// retrieve list-value-pairs for the individual dta fields
				$taIndividualFields = $dta->getIndividualFields();

				// combine both entry lists
				$taFull = $taHeader + $taIndividualFields;

				// descriptions for each entry
				$headerList = $dta->getEntryDescriptions();

				$transactionListNumber += 1;
				echo "<table width=\"100%\">\n";
				echo "<tr bgcolor=\"$backgroundColorTableHeader\">\n";
				echo "<td>\n";
					echo "<input type=\"checkbox\" value='" . serialize($dta) . "' name=\"dta[]\">\n";
					echo "&nbsp;Transaction $transactionListNumber\n";
				echo "</td>\n";
				echo "</tr>\n";
				echo "</table>\n";

				// output table with result
				echo "<table>\n";
				foreach ($taFull as $taKey => $taValue) {
					$cellColor = "";
					echo "<tr>\n";
					echo "<td bgcolor=\"$backgroundColorTableHeader\">" . $headerList[$taKey] . "</td>\n";
					if ($dta->validationResult[$taKey] == False) {
						$cellColor = $backgroundColorCellIncorrect;
					}
					echo "<td bgcolor=\"$cellColor\"><kbd>$taValue</kbd></td>\n";
					echo "</tr>\n";
				}
				echo "</table>\n";

				// add hidden field with the transaction object
				//echo "<input type=\"hidden\" value=\"" . serialize($dta) . "\" name=\"dta$transactionListNumber\">\n";
				echo "<br><br>\n";
			}

			// add form buttons
			echo "<input type=\"submit\" value=\"Create transaction data for the selected items\">\n";
			echo "&nbsp;\n";
			echo "<input type=\"reset\" value=\"Reset\">\n";
			echo "<input type=\"hidden\" name=\"comingFrom\" value=\"export\">\n";
			echo "<input type=\"hidden\" name=\"action\" value=\"prepare\">\n";
			echo "<input type=\"hidden\" name=\"dataListFormat\" value=\"$dataListFormat\">\n";
			echo "</form>\n";
		} else {
			echo "False<br>\n";
		}
	} else {
		// something went wrong ... raise an error
		echo "<b>Unexpected parameter received. Export is impossible.</b>\n";
		echo "<br><br>\n";
		echo "Next steps that are possible:<br>\n";
		echo "<ul>\n";
		echo "<li><a href=\"dta-import-csv.php\"> Return to the entry field for csv data</li></a>\n";
		echo "<li><a href=\"dta-intro.php\">Return to the main page</a></li><br>\n";
		echo "</ul>\n";
	}

	?>

	<hr>
</body>
</html>
