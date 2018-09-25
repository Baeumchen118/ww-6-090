<?php
/*------------------------------------------------------------------------------------------------------------------
 Beschreibung: Formular und Klasse für den Aufruf von Feeds von unterschiedlichen Web-Adressen Feedreader für RSS-
                und Atom-Feeds. Weitere Einzelheiten unter: http://www.coder-welten.de/projekte/feedreader.htm
		 Copyright:    © 2006/2013 - Verlag Horst Müller - Stendal
		  -------------------------------------------------------------------------------------------------------------------
		  */
		  echo "<div class=\"form\">\n".
		       "\x20\x20<form action=\"".basename($_SERVER["PHP_SELF"])."\" method=\"get\">\n".
		            "\t<input type=\"text\" name=\"abrufen\" size=\"60\" maxlength=\"120\"".SCHLIESSE."<br".SCHLIESSE."\n".
			         "\t<input type=\"reset\" value=\" Reset \"".SCHLIESSE."\n".
				      "\t<input type=\"submit\" value=\" Abrufen \"".SCHLIESSE."\n".
				           "\x20\x20</form>\n".
					        "</div>\n";

/*------------------------------------------------------------------------------------------------------------------
 Überprüfung der vom Formular übermittelten URL, wobei die Klasse und das Objekt nur benötigt wird, falls das
  Formular eingeblendet werden soll.
   -------------------------------------------------------------------------------------------------------------------
   */
   class EmpfangMessage {

    private $dompath;
        public  $domain;

    public function pruefeMessage() {

        if (isset($_GET["abrufen"]) and !empty($_GET["abrufen"])) {

            $abrufen = trim($_GET["abrufen"]);
	                $hrefpos = stripos($abrufen, "http://");
			            $abrufen = preg_replace("/[^a-zA-Z0-9.:?&\/=_-]/", "", $abrufen);

            if ($hrefpos === false) {
	                    $abrufen = "http://".$abrufen;
			                }
					            $this->dompath = $abrufen;
						                return $this->dompath;
								        }
									        else {
										            $this->dompath = $this->domain;
											                return $this->dompath;
													        }
														    }
														    }
														    /*-- Erzeugen und Instanziieren des Objektes EmpfangMessage, falls $formu gleich true. ---------------------------*/

$vonform = new EmpfangMessage();
$vonform->domain = $seite;
$seite   = $vonform->pruefeMessage();

?>