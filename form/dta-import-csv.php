<html>
	<head>Importing DTA transactions as csv file</head>
<body>
	<h2 style="color:white;background:blue">&nbsp;Importing DTA transactions as csv file</h2>

	<h3>Select the csv data file</h3>

	<?php

	// include dta-ch object class for payments
	require_once 'dta-ch.php';

	// include basic variables
	require_once 'dta-include.php';

	// set default values
	$action = "export";
	$comingFrom = "import";
	$dataListFormat = "csv";

	echo "CSV data with transactions:<br>";
	echo "<form action=\"dta-export.php\" method=\"post\">\n";

	// transactions as csv data
	echo "<textarea name=\"transactions\"></textarea>\n";

	echo "<br>\n";
	echo "<br>\n";

	echo "Options:<br>\n";

	echo "<ul>\n";

		// options:
		// - specify the data format
		echo "<li>Data format: ";
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
		echo "</li>\n";

		// - specify data adjustments
		echo "<li>Data adjustments: ";
		echo "<input type=\"checkbox\" value=\"true\" name=\"adjust\">yes\n";
		echo "</li>\n";
	echo "</ul>\n";

	// add form buttons
	echo "<br>\n";
	echo "<br>\n";
	echo "<input type=\"submit\" value=\"Prepare transaction\">\n";
	echo "&nbsp;\n";
	echo "<input type=\"reset\" value=\"Reset\">\n";
	echo "<input type=\"hidden\" name=\"comingFrom\" value=\"$comingFrom\">\n";
	echo "<input type=\"hidden\" name=\"action\" value=\"$action\">\n";
	echo "<input type=\"hidden\" name=\"dataListFormat\" value=\"$dataListFormat\">\n";
	echo "</form>\n";
	?>

	<hr>
</body>
</html>
