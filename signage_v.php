<?php

	$agent = "Mozilla/5.0 (compatible; HM-Feedleser +".$_SERVER["HTTP_HOST"].$_SERVER["PHP_SELF"].")";
	ini_set("user_agent", $agent);
	header("Content-Type: text/html; charset=UTF-8");

	/*-- Die folgenden Werte sollten bei Bedarf editiert werden, $seite = "" muss editiert werden. -------------------*/

	error_reporting(E_ALL);                  // Der Wert kann nach Erprobung von E_ALL auf 0 gesetzt werden

	/*-- Die Datei oder die Web-Adresse des zu ladenden Feeds, Datei ohne http:// und URL mit http:// eintragen. -----*/

	$seite = "http://www.tagesschau.de/xml/atom/";
	$linkt = "";              		// Linktext für den unteren Link
	$feedt = "";                    	// Für den einleitenden Titel des Feeds, kann auch leer bleiben
	$maxim = 4;                             // Maximale Anzahl der Ergebnisse pro Seite
	$formu = false;                         // Formular anzeigen gleich "true" oder nicht anzeigen gleich "false"
	$descr = true;                          // Description anzeigen gleich "true" oder ausblenden gleich "false"
	$conte = true;                          // Content anzeigen gleich "true" oder ausblenden gleich "false"
	$summa = true;                          // Summary anzeigen gleich "true" oder ausblenden gleich "false"
	$maxho = 250;                           // Maximale Höhe für eingebundene Bilder angeben
	$maxwe = 250;                           // Maximale Weite für eingebundene Bilder angeben
	define("SCHLIESSE", ">");               // Für valides HTML je nach Dokumenttyp-Deklaration bei HTML ">" oder bei XHTML " />"

?>

<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="utf-8">
<link rel="stylesheet" href="css/style_v.css">
</head>

<body>
  <div class="wrapper">

    <div id="header">
    </div>

    <div id="laufschrift">
      <h1>Herzlich willkommen zum Wintersemester 2018 wünscht das Fakultätsteam!</h1>
    </div>


	<div id="video">
		<video autoplay="1" height="576" width="1024" loop="1" src="video/Video_Foyer_HD.mp4">
	</div>
<!--
	<div id="video">
      		<iframe width="1024" height="576" src="https://www.youtube.com/embed/videoseries?list=PLBak0bryMLI4mOTDRruyR_AStEsCeJI9_&autoplay=1&mute=0&loop=1" ></iframe>
	    </div>
-->
        
<!--
	<div class="plakat">
		<img class="plakat" src="img/Aushang_Probevorlesung.png"
	</div>
-->

<?php
	/*------------------------------------------------------------------------------------------------------------------
	 Ab hier sollte ohne einschlägige Kenntnisse nichts mehr editiert werden. Das Formular für den Aufruf von Feeds von
	 unterschiedlichen Web-Adressen kann bei Bedarf über den Wert von $formu ein- oder ausgeblendet werden.
	 -------------------------------------------------------------------------------------------------------------------
	*/
	if ($formu == true) {

		if (file_exists("feedformular.php")) {

			include_once "feedformular.php";

		} else {echo "Formular konnte nicht geladen werden!\n";
		}
	}

	/*-- Die Funktion wandelt Tags und behandelt Entities außerhalb von CDATA-Abschnitten, falls erforderlich. -------*/

	function wandleTags($daten) {

		$wandle  = array(

			"content:encoded" => "content",
			"dc:creator"      => "creator",
			"dc:date"         => "published"
		);
		$daten = strtr($daten, $wandle);
		$daten = preg_replace_callback("/(&[#a-z0-9]+;)/",

			create_function(

				'$enti',
				'return htmlspecialchars(mb_convert_encoding($enti[1], "UTF-8", "HTML-ENTITIES"));'
			), $daten);

		/*-- Für den Fall, dass der Feed nur als String ohne Zeilenumbrüche ausgeliefert wird. -----------------------*/

		if (strpos($daten, "><item>") !== false) {

			$eing  = array(

				"><item>"        => ">\r\n<item>",
				"><title>"       => ">\r\n<title>",
				"><link>"        => ">\r\n<link>",
				"><description>" => ">\r\n<description>",
				"><content>"     => ">\r\n<content>",
				"><summary>"    => ">\r\n<summary>",
				"><enclosure>"   => ">\r\n<enclosure>",
				"><pubDate>"     => ">\r\n<pubDate>",
				"><updated>"     => ">\r\n<updated>",
				"><published>"   => ">\r\n<published",
				"><author>"      => ">\r\n<author>",
				"><creator>"     => ">\r\n<creator>"
			);
			$daten = strtr($daten, $eing);
		}
		return $daten;
	}

	/*------------------------------------------------------------------------------------------------------------------
	 Die Funktion filtert HTML-Tags innerhalb und außerhalb von CDATA-Abschnitten und wandelt erlaubte Tags in BBCode.
	 Weiterhin findet eine erste Überprüfung von Images statt, falls $image nicht gleich false.
	 -------------------------------------------------------------------------------------------------------------------
	*/
	function filtereHTML($daten, $image) {

		$daten = preg_replace("/<p.*?>(.+?)<\/p>/is", "$1[br]", $daten);
		$daten = preg_replace("/<div.*?>(.+?)<\/div>/is", "$1[br]", $daten);
		$daten = preg_replace("/<span.*?>(.*?)<\/span>/is", "$1", $daten);
		$daten = preg_replace("/<br.*?>/i", "[br]", $daten);
		$daten = preg_replace("/<a href.+?>(.*?)<\/a>/is", "$1", $daten);

		if ($image === true) {
			$daten = preg_replace("/<img.*?src=\"([a-z0-9_\/=.:;&?-]+?)\.(jpg|png).*?>/is", "[img]$1.$2[/img]", $daten);
		}
		$eing  = array(

			"<b>"        => "[b]",
			"</b>"       => "[/b]",
			"<i>"        => "[i]",
			"</i>"       => "[/i]",
			"<em>"       => "[i]",
			"</em>"      => "[/i]",
			"<ul>"       => "[ul]",
			"</ul>"      => "[/ul]",
			"<li>"       => "[li]",
			"</li>"      => "[/li]",
			"<strong>"   => "[b]",
			"</strong>"  => "[/b]",
			"{"          => "",
			"}"          => ""
		);
		$daten = strtr($daten, $eing);
		$daten = preg_replace("/<.+?>/is", "", $daten);
		return $daten;
	}

	/*------------------------------------------------------------------------------------------------------------------
	 Die Funktion wandelt erlaubte BBcode-Tags in HTML-Tags zurück und überprüft Images. Falls Bilder die angegeben
	 Höchstmaße überschreiten, werden diese gleichmäßig skaliert.
	 -------------------------------------------------------------------------------------------------------------------
	*/
	function formeHTML($daten) {

		$eing  = array(

			"[br]"       => "<br".SCHLIESSE,
			"[b]"        => "<b>",
			"[/b]"       => "</b>",
			"[i]"        => "<em>",
			"[/i]"       => "</em>",
			"[ul]"       => "<ul>",
			"[/ul]"      => "</ul>",
			"[li]"       => "<li>",
			"[/li]"      => "</li>"
		);
		$daten = strtr($daten, $eing);

		$daten = preg_replace("/[\n]/", "\n\t", $daten);
		$daten = preg_replace_callback("/\[img\](.+?)\[\/img\]/",

		create_function ('$bilder',

			'@$format = getimagesize($bilder[1]);

			if($format != false) {

				if ($format["mime"] == "image/jpeg" or $format["mime"] == "image/png") {

					global $maxho, $maxwe;

					$height = $format[1];
					$width  = $format[0];

					if ($height > $maxho){

						$height  = $maxho;
						$prozent = ($format[1] / $height);
						$width   = ($format[0] / $prozent);
					}
					if ($width > $maxwe){

						$width   = $maxwe;
						$prozent = ($format[0] / $width);
						$height  = ($format[1] / $prozent);
					}
					return "<img src=\"".htmlspecialchars($bilder[1], ENT_QUOTES)."\" alt=\"Bild\"".
						   " height=\"".round($height)."\" width=\"".round($width)."\"".SCHLIESSE;
				}
			}'
		), $daten);
		return $daten;
	}

	/*-- Die Funktion wandelt Datum und Uhrzeit in ein lesbares Format um. -------------------------------------------*/

	function formeDatumZeit($daten) {

		$daten = preg_replace("/(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2})/", "$3.$2.$1 um $4:$5 Uhr", $daten);
		$daten = substr($daten, 0, 23);
		return $daten;
	}

	/*-- Die Funktion prüft enthaltene Links auf unerlaubte Zeichen und entfernt diese erforderlichenfalls. ----------*/

	function filtereLinks($daten) {

		if (($pos = strpos($daten, "#")) !== false) {
			$daten = substr($daten, 0, $pos);
		}
		$daten = preg_replace("/[^a-z0-9_\/=.:;&?-]/is", "", $daten);
		return $daten;
	}

	$link  = false;                     // Bekanntmachung der Variablen $link, $lesen und $enco sowie Startwert von $si
	$lesen = false;
	$image = false;                     // $image false zuweisen
	$enco  = "";
	$si =  0;

	echo "<div class=\"feed\">\n";

	/*-- Der Feed wird mit cURL geladen und im Anschluß erfolgt die Ausgabe des Feeds --------------------------------*/

	class WieBot {

		public  $antwort;
		public  $agent;
		private $httpcode = array(200, 301, 304);

		public function getVerbindung() {

			$cwbot = curl_init($this->antwort);                     // cURL-Session initialisieren

			curl_setopt($cwbot, CURLOPT_HEADER, false);             // Response Header nicht mit in die Ausgabe aufnehmen
			curl_setopt($cwbot, CURLOPT_USERAGENT, $this->agent);   // User Agent für den Request Header setzen
			curl_setopt($cwbot, CURLOPT_RETURNTRANSFER, true);      // Rückgabe als String

			if (curl_exec($cwbot) !== false) {
				$this->antwort = curl_exec($cwbot);                 // Ausführung der cURL-Session oder eine Fehler-Mitteilung ausgeben
			} else {
				$this->antwort = "Fehler: ".curl_error($cwbot);
			}
			if (stripos($this->antwort, "Fehler:") !== 0) {
				$status = curl_getinfo($cwbot, CURLINFO_HTTP_CODE);

				if (!in_array($status, $this->httpcode, true)) {
					$this->antwort = "Fehler: ".$status;
				}
			}
			curl_close($cwbot);                                     // Session beenden und Resourcen freigeben
			return $this->antwort;
		}
	}

	$vonbot = new WieBot();                                         // Erzeugen und Instanziieren des Objektes WieBot
	$vonbot->agent   = $agent;                                      // User Agent als Wert für Eigenschaften übernehmen
	$vonbot->antwort = $seite;                                      // Der Eigenschaft $antwort den Wert von $seite zuweisen
	$data   = $vonbot->getVerbindung();                             // Den Rückgabewert der Methode getVerbindung an SimpleXML übergeben

	/*-- Beginn der Ausgabe ------------------------------------------------------------------------------------------*/

	if (stripos($data, "Fehler:") !== 0 and $data != false) {

		$pxml = simplexml_load_string(wandleTags($data), "SimpleXMLElement", LIBXML_NOCDATA);

		/*--------------------------------------------------------------------------------------------------------------
		 Auswählen, ob Feedtitel und Eintritt für RSS oder für Atom, wobei channel->title und channel->item der RSS 2.0
		 Specification für Feeds entspricht, title und entry hingegen der Specification für Atom-Feeds und item RSS 1.0
		 ---------------------------------------------------------------------------------------------------------------
		*/
		if ($pxml->channel->title) echo "<h2>".$feedt.formeHTML(htmlspecialchars(filtereHTML($pxml->channel->title, $image), ENT_QUOTES))."</h2>\n";
		if ($pxml->title)          echo "<h2>".$feedt.formeHTML(htmlspecialchars(filtereHTML($pxml->title, $image), ENT_QUOTES))."</h2>\n";

		if ($pxml->channel->item) $lesen = $pxml->channel->item;
		if ($pxml->entry)         $lesen = $pxml->entry;
		if ($pxml->item)          $lesen = $pxml->item;

		if (strlen($lesen[0]->title) > 0 or strlen($lesen[1]->title) > 0) {

			foreach ($lesen as $nachricht) {

				if ($nachricht->title != false) {

					/*-- Auswählen, ob Link für RSS $nachricht->link oder Atom $nachricht->link->attributes() --------*/

					if ($nachricht->link) {

						if ((string)$nachricht->link) {
							$link = $nachricht->link;
						}
						elseif ($nachricht->link->attributes()) {
							$attr = $nachricht->link->attributes();
							$link = $attr["href"];
						}
					}

					/*-- Titel mit Link ------------------------------------------------------------------------------*/

					if ($nachricht->title) {
						echo "\x20\x20<h3><a href=\"".htmlspecialchars(filtereLinks($link), ENT_QUOTES)."\" target=\"_blank\">".
						formeHTML(htmlspecialchars(filtereHTML($nachricht->title, $image), ENT_QUOTES))."</a></h3>\n";
					}

					/*--------------------------------------------------------------------------------------------------
					 Einen Hinweis anzeigen, falls Bilder als Beilage mit enclosure der Nachricht hinzugefügt wurden.
					 ---------------------------------------------------------------------------------------------------
					*/
					if ($nachricht->enclosure) {

						if ($nachricht->enclosure->attributes()) {
							$url = $nachricht->enclosure->attributes();

							if (strpos($url["url"], ".jpg") or strpos($url["url"], ".png")) {

								$enco = "<br".SCHLIESSE."\n\x20\x20<span class=\"klein\">Beilage Medien: ".
										 htmlspecialchars(filtereLinks($url["url"]), ENT_QUOTES)."</span>";
							}
						}
					}

					/*-- true erst einmal nur für description und falls kein Fund, dann  auch für content und summary */

					$image = true;

					/*-- description und content für RSS und summary für Atom ----------------------------------------*/

					if ($nachricht->description and $descr != false) {
						echo "\x20\x20<p>".formeHTML(htmlspecialchars(filtereHTML($nachricht->description, $image), ENT_QUOTES))."</p>\n";

						if ((strpos((string)$nachricht->description, "<img")) !== false) {
							$image = false;
						}
					}
					if ($nachricht->content and $conte != false) {
						echo "\x20\x20<p>".formeHTML(htmlspecialchars(filtereHTML($nachricht->content, $image), ENT_QUOTES))."</p>\n";
					}
					if ($nachricht->summary and $summa != false) {
						echo "\x20\x20<p>".formeHTML(htmlspecialchars(filtereHTML($nachricht->summary, $image), ENT_QUOTES))."</p>\n";
					}

					$image = false;  /*- Nur für den Fall, dass false nicht durch description ausgelöst wurde */

					/*-- Link unten ----------------------------------------------------------------------------------*/
					
					if ($link != false) {
					echo "\x20\x20<p class=\"unten\"><a href=\"".htmlspecialchars(filtereLinks($link), ENT_QUOTES)."\" target=\"_blank\">".
							 $linkt."</a><br".SCHLIESSE."\n";
					}
					
					/*-- pubDate für RSS und published und updated für Atom ------------------------------------------*/

					if ($nachricht->pubDate) {
						echo "\x20\x20<br".SCHLIESSE."".htmlspecialchars(substr($nachricht->pubDate,  0, 16), ENT_QUOTES).
							 " um ".htmlspecialchars(substr($nachricht->pubDate, 17,  5), ENT_QUOTES)." Uhr";

					} elseif ($nachricht->updated) {
						echo "\x20\x20<br".SCHLIESSE.formeDatumZeit($nachricht->updated)."\n";

					} elseif ($nachricht->published) {
						echo "\x20\x20<br".SCHLIESSE.formeDatumZeit($nachricht->published)."\n";
					}

					/*-- Autor oder Creator für RSS und Atom ---------------------------------------------------------*/

					if ($nachricht->author) {
						echo " von ".htmlspecialchars(filtereHTML($nachricht->author, $image), ENT_QUOTES).$enco."</p>\n";

					} elseif ($nachricht->creator) {
						echo " von ".htmlspecialchars(filtereHTML($nachricht->creator, $image), ENT_QUOTES).$enco."</p>\n";

					} else {
						echo "\x20\x20".$enco."</p>\n";
					}

					/*-- Trennlinie zwischen den einzelnen Mitteilungen ----------------------------------------------*/

					echo "\x20\x20<hr class=\"linie\"".SCHLIESSE."\n";
					$si++;
					if ($si == $maxim) {break;
					}
				}
			}
		} else { echo "<br".SCHLIESSE."Mehrere Titel oder Items scheinen fehlerhaft zu sein!\n";
		}
	} else { echo "<br>".htmlspecialchars($data, ENT_QUOTES)."\n<br".SCHLIESSE."Feed konnte nicht geladen werden!\n";
	}
?>

</div>
</body>
</html>
