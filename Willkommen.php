	<?php
		/*------------------------------------------------------------------------------------------------------------------
		 Software-Name: HM-Feedleser (http://www.coder-welten.de/projekte/feedreader.htm)
		 Beschreibung:  PHP-Script für einen sicheren Feedreader mit SimpleXML für RSS- und Atom-Feeds.
		 Einzelheiten:  http://www.coder-welten.de/projekte/feedreader-mit-feedklasse.htm
		 Autor:         Horst Müller
		 Version:       Release 1.06  (mit Feed-Klasse)
		 Datum:         16. Juni 2013
		 Lizenz:        Lizenz für Software - http://www.coder-welten.de/projekte/lizenz-fuer-software.htm
		 Copyright:     © 2006/2013 - Verlag Horst Müller - Stendal
		 -------------------------------------------------------------------------------------------------------------------
		*/
		 ini_set("user_agent", "Mozilla/5.0 (compatible; HM-Feedleser +".$_SERVER["HTTP_HOST"].$_SERVER["PHP_SELF"].")");
		 header("Content-Type: text/html; charset=UTF-8");

		 /*-- Die folgenden Werte sollten bei Bedarf editiert werden, $seite = "" muss editiert werden. -------------------*/

		error_reporting(E_ALL);                   // Der Wert kann nach Erprobung auf 0 gesetzt werden
		define("SCHLIESSE", ">");                 // Für valides HTML je nach Dokumenttyp-Deklaration bei HTML ">" oder bei XHTML " />"
		define("MAXWEITE", 250);                  // Maximale Höhe für eingebundene Bilder angeben
		define("MAXHOEHE", 250);                  // Maximale Weite für eingebundene Bilder angeben
		$formu  = false;                           // Formular anzeigen gleich "true" oder nicht anzeigen gleich "false"

		/*-- Bei $seite die Datei mit Pfad oder Web-Adresse des zu ladenden Feeds editieren, URL mit http:// eintragen. --*/

		$seite  = "https://www.welt.de/feeds/section/wirtschaft.rss";
		$feedkl = "feedklasse.php";               // Pfad und Seite mit der Feedklasse
		$feedfo = "feedformular.php";             // Pfad und Seite mit dem Feedformular und der Formular-Klasse
		$feedst = "feedstyle.css";                // Pfad und Seite mit den CSS Stylesheets

		/*------------------------------------------------------------------------------------------------------------------
		 Die HTML-Header kann zwischen <head> und </head> und der HTML-Body zwischen <body> und </body> beliebig erweitert
		 und an bestehende Seiten angepasst werden.
		 -------------------------------------------------------------------------------------------------------------------
		*/
 ?>


<!DOCTYPE html>
<html lang="de">
<head>
	<title>Herzlich Wilkommen</title>
	<meta charset="UTF-8">
	<meta name="author" content="Thomas Kleebaum-Nagy">
	<meta name="description" content="Willkommen">
	<meta name="keywords" content="Willkommen">
	<link rel="stylesheet" href="css/marquee.css">
	<link rel="stylesheet" href="feedstyle.css">
</head>

<body>

	<h1><marquee>Herzlich willkommen zum Wintersemester 2018/19 an der Fakultät Wirtschaftsingenieurwesen</marquee></h1>

	<div class="video">
		<video width="1040px" height="585px" autoplay="autoplay" loop="loop">
			<source src="video/Video_Foyer_HD_2.mp4" type="video/mp4">
				Your browser does not support the video tag.
		</video>
	</div>
	<div class="feed">
		
		<?php
			 if ($formu == true) {

			 	if (file_exists($feedfo)) {include_once $feedfo;
			 		} else {echo "Formular konnte nicht geladen werden!\n";}
			}
			 if (file_exists($feedkl)) {

			 	include_once $feedkl;

			 	$feedladen = new FeedKlasse();
			    $feedladen->seite = $seite;                // Die URI des zu ladenden Feeds oder der Name der Datei
			    $feedladen->maxim = 8;                    // Maximale Anzahl der Ergebnisse pro Seite
			    $feedladen->descr = true;                  // Description anzeigen gleich "true" oder ausblenden gleich "false"
			    $feedladen->conte = false;                  // Content anzeigen gleich "true" oder ausblenden gleich "false"
			    $feedladen->summa = false;                  // Summary anzeigen gleich "true" oder ausblenden gleich "false"
			    $feedladen->linkt = "";     				// Linktext für den unteren Link
			    $feedladen->feedt = "Feed - ";             // Für den einleitenden Titel des Feeds, kann auch leer bleiben

			    $feedladen->liefereFeedContent();

				} else {echo $feedkl." konnte nicht geladen werden!\n";}
		?>
		
	</div>
	
</body>
</html>

