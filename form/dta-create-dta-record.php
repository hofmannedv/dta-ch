<html>
	<head>Testing the DTA-CH class</head>
<body>
	<h2 style="color:white;background:blue">&nbsp;Testing the DTA-CH class</h2>

	<?php

		require_once('dta-include.php');

		// set data format
		$outputFilename = $_POST[outputFilename];
		$data = $_POST[data];

		echo "<h3>Step 5: Output dta record</h3>\n";
		echo "Saving data to <pre>$outputFilename</pre> ...\n";

	?>

	<hr>
</body>
</html>
