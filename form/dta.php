<html>
	<head>Testing the DTA-CH class</head>
<body>
	<h2 style="color:white;background:blue">&nbsp;Testing the DTA-CH class</h2>

	<h3>Step 1: Select transaction type and data format</h3>

	<?php	
		require_once('dta-include.php');

		// validate action parameters
		$action = getAction();
		switch ($action) {
		case "selectagain":
		case "selectnext":
			$preSelect = preSelect();
			$comingFrom = comingFrom();
			$dataFormat = getDataFormat();
			break;
		default:
			// default values
			$action = "new";
			$preSelect = "826";
			$comingFrom = "dta";
			$dataFormat = "fixed";
			break;
		}

		echo "Transaction type:<br>";
		echo "<form action=\"dta-formular.php\" method=\"post\">\n";
		echo ("<select name=\"transactionType\" size=\"1\">\n");
		foreach($dtaTransactionList as $transactionId=>$transactionDesc)
		{
			$selected = "";
			if ($transactionId == $preSelect) {
				$selected = "selected";
			}
			echo ("<option $selected value=\"$transactionId\">$transactionDesc</option>\n");
		}
		echo ("</select>\n");
		echo "<br>\n";
		echo "<br>\n";

		echo "Data format:<br>\n";
		echo "<select name=\"dataFormat\" size=\"1\">\n";
		foreach($dtaDataFormatList as $dataFormatId=>$dataFormatDesc)
		{
			$selected = "";
			if ($dataFormatId == $dataFormat) {
				$selected = "selected";
			}
			echo ("<option $selected value=\"$dataFormatId\">$dataFormatDesc</option>\n");
		}
		echo ("</select>\n");

		echo "<br>\n";
		echo "<br>\n";
		echo "<input type=\"submit\" value=\"Select transaction\">\n";
		echo "&nbsp;\n";
		echo "<input type=\"reset\" value=\"Reset\">\n";
		echo "<input type=\"hidden\" name=\"comingFrom\" value=\"$comingFrom\">\n";
		echo "<input type=\"hidden\" name=\"action\" value=\"new\">\n";
		echo "</form>\n";
	?>

	<hr>
</body>
</html>
