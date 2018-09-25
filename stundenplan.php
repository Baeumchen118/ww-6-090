<html lang="de">
<head>
	<title>Fakultät Wirtschaftsingeneurwesen</title>
	<link rel="stylesheet" href="css/style_neu.css">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<script src="https://code.jquery.com/jquery-1.11.2.min.js"></script>
    <link href="https://www.jqueryscript.net/css/jquerysctipttop.css" rel="stylesheet" type="text/css">

	<script type="text/javascript" src="scripts/date.js"></script>

	<script type="text/javascript" src="js/jquery.csvToTable.js"></script>
	<script type="text/javascript" src="js/jquery.tablesorter.dev.js"></script>
	
	<script>
		
		$(function() {
			$('#CSVTable2').CSVToTable('data.csv', {startLine: 2, headers: ['Zeit', 'Raum', 'Bezeichnung', 'Gruppe(n)', 'Dozent(en)'] }).bind("loadComplete",function() { 
				$('#CSVTable2').find('TABLE').tablesorter();
			});;
		});
	
		
		setTimeout(function () { location.reload(1); }, 600000);
					</script>
	</head>

	<body>

		<div class="head"><img src="img/Logo_HSMW_150Jahre_wei%D1%81_CMYK.jpg" alt="Logo Hochschule Mittweida"></div>
		<div class="content"><div id="time" class="time"></div>
		<div id="CSVTable2"></div>



	</body>

	</html>
