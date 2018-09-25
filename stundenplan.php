<html lang="de">
<head>
    <title>Fakultät Wirtschaftsingeneurwesen</title>
    <script src="scripts/CsvToTable.js"></script>
    <link rel="stylesheet" href="css/style_neu.css">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <script src="scripts/date.js"></script>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script>
<!-- 
setTimeout(function () { location.reload(1); }, 600000);
-->
</script>
</head>

<body>

 <div class="header"><img src="img/Logo_HSMW_150Jahre_wei%D1%81_CMYK.jpg" alt="Logo Hochschule Mittweida"></div>
    <div class="content"><div id="time" class="time"></div>

	<div id="container">
		<div id="CSVTable"></div>

	<script>
		$(function() {
			$('#CSVTable').CSVToTable('data.csv');
		});
	</script>

	</div>
	

</body>

</html>
