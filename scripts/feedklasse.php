<?php
/*------------------------------------------------------------------------------------------------------------------
 Software-Name: HM-Feedleser (http://www.coder-welten.de/projekte/feedreader.htm)
  Beschreibung:  PHP-Script mit Feedklasse für den Aufruf von RSS- und Atom-Feeds. Weitere Einzelheiten unter:
   Einzelheiten:  http://www.coder-welten.de/projekte/feedreader-mit-feedklasse.htm
    Autor:         Horst Müller
     Version:       Release 1.06  (Feed-Klasse)
      Datum:         16. Juni 2013
       Lizenz:        Lizenz für Software - http://www.coder-welten.de/projekte/lizenz-fuer-software.htm
        Copyright:     © 2006/2013 - Verlag Horst Müller - Stendal
	 -------------------------------------------------------------------------------------------------------------------
	 */

class FeedKlasse {

    /*--------------------------------------------------------------------------------------------------------------
         Die Werte für die mit public definierten Eigenschaften können in der xml-feedleser.php editiert werden. In der
	      FeedKlasse sollte hingegen ohne einschlägige Kenntnisse nichts editiert werden.
	           ---------------------------------------------------------------------------------------------------------------
		       */
		           public  $seite;                         // Für die aufzurufende Feed-URI
			       public  $maxim;                         // Maximale Anzahl der Ergebnisse pro Seite
			           public  $descr;                         // Description anzeigen oder nicht
				       public  $conte;                         // Content anzeigen oder nicht
				           public  $summa;                         // Summary anzeigen oder nicht
					       public  $linkt;                         // Linktext für den unteren Link
					           public  $feedt;                         // Für den einleitenden Titel des Feeds, kann auch leer bleiben

    private $link  = false;                 // Werte für $link, $lesen und $enco sowie Startwert von $si
        private $lesen = false;
	    private $enco  = "";
	        private $si =  0;

    private function wandleTags($daten) {

        $wandle  = array(

            "content:encoded" => "content",
	                "dc:creator"      => "creator",
			            "dc:date"         => "published"
				            );
					            $daten = strtr($daten, $wandle);
						            $daten = preg_replace_callback("/(&[#a-z0-9]+;)/",

            function($enti) {

                return htmlspecialchars(mb_convert_encoding($enti[1], "UTF-8", "HTML-ENTITIES"));
		            }, $daten);

        /*-- Für den Fall, dass der Feed nur als String ohne Zeilenumbrüche ausgeliefert wird. -------------------*/

        if (strpos($daten, "><item>") !== false) {

            $eing  = array(

                "><item>"        => ">\r\n<item>",
		                "><title>"       => ">\r\n<title>",
				                "><link>"        => ">\r\n<link>",
						                "><description>" => ">\r\n<description>",
								                "><content>"     => ">\r\n<content>",
										                "><summary>>"    => ">\r\n<summary>>",
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

    /*--------------------------------------------------------------------------------------------------------------
         Die Funktion filtert HTML-Tags innerhalb und außerhalb von CDATA-Abschnitten und wandelt erlaubte Tags in
	      BBCode. Weiterhin findet eine erste Überprüfung von Images statt, falls $image nicht gleich false.
	           ---------------------------------------------------------------------------------------------------------------
		       */
		           private function filtereHTML($daten, $image) {

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

    /*--------------------------------------------------------------------------------------------------------------
         Die Funktion wandelt erlaubte BBcode-Tags in HTML-Tags zurück und überprüft Images. Falls Bilder die angegeben
	      Höchstmaße überschreiten, werden diese gleichmäßig skaliert.
	           ---------------------------------------------------------------------------------------------------------------
		       */
		           private function formeHTML($daten) {

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

        function ($bilder) {

            @$format = getimagesize($bilder[1]);

            if($format != false) {

                if ($format["mime"] == "image/jpeg" or $format["mime"] == "image/png") {

                    $height = $format[1];
		                        $width  = $format[0];

                    if ($height > MAXHOEHE){

                        $height  = MAXHOEHE;
			                        $prozent = ($format[1] / $height);
						                        $width   = ($format[0] / $prozent);
									                    }
											                        if ($width > MAXWEITE){

                        $width   = MAXWEITE;
			                        $prozent = ($format[0] / $width);
						                        $height  = ($format[1] / $prozent);
									                    }
											                        return "<img src=\"".htmlspecialchars($bilder[1], ENT_QUOTES)."\" alt=\"Bild\"".
														                           " height=\"".round($height)."\" width=\"".round($width)."\"".SCHLIESSE;
																	                   }
																			               }
																				               }, $daten);
																					               return $daten;
																						           }

    /*-- Die Funktion wandelt Datum und Uhrzeit in ein lesbares Format um. ---------------------------------------*/

    private function formeDatumZeit($daten) {

        $daten = preg_replace("/(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2})/", "$3.$2.$1 um $4:$5 Uhr", $daten);
	        $daten = substr($daten, 0, 23);
		        return $daten;
			    }

    /*-- Die Funktion prüft enthaltene Links auf unerlaubte Zeichen und entfernt diese erforderlichenfalls. ------*/

    function filtereLinks($daten) {

        if (($pos = strpos($daten, "#")) !== false) {
	            $daten = substr($daten, 0, $pos);
		            }
			            $daten = preg_replace("/[^a-z0-9_\/=.:;&?-]/is", "", $daten);
				            return $daten;
					        }

    public function liefereFeedContent() {

        $image = false;                     // $image als Anfangswert false zuweisen
	        echo "<div class=\"feed\">\n";

        /*-- Der Feed wird geladen und im Anschluß erfolgt die Ausgabe des Feeds ---------------------------------*/

        if ((@$data = file_get_contents($this->seite)) != false) {

            $pxml = simplexml_load_string($this->wandleTags($data), "SimpleXMLElement", LIBXML_NOCDATA);

            /*------------------------------------------------------------------------------------------------------
	                 Auswahl, ob Feedtitel und Eintritt für RSS oder für Atom, wobei channel->title und channel->item der
			              RSS 2.0 Specification für Feeds entspricht, title und entry hingegen der Specification für Atom-Feeds.
				                   -------------------------------------------------------------------------------------------------------
						               */
							                   if ($pxml->channel->title) echo "<h2>".$this->feedt.$this->formeHTML(htmlspecialchars($this->filtereHTML($pxml->channel->title, $image), ENT_QUOTES))."</h2>\n";
									               if ($pxml->title)          echo "<h2>".$this->feedt.$this->formeHTML(htmlspecialchars($this->filtereHTML($pxml->title, $image), ENT_QUOTES))."</h2>\n";

            if ($pxml->channel->item) $this->lesen = $pxml->channel->item;  // RSS 2.0
	                if ($pxml->entry)         $this->lesen = $pxml->entry;          // Atom
			            if ($pxml->item)          $this->lesen = $pxml->item;           // RSS 1.0

            /*------------------------------------------------------------------------------------------------------
	                 Kontrolle, ob zumindest das erste oder das zweite Item einen Titel enthält. Falls dies wie erwartet der
			              Fall sein sollte, werden noch einmal alle Titel einzeln auf Vorhandensein überprüft.
				                   -------------------------------------------------------------------------------------------------------
						               */
							                   if (strlen($this->lesen[0]->title) > 0 or strlen($this->lesen[1]->title) > 0) {

                foreach ($this->lesen as $nachricht) {

                    if ($nachricht->title != false) {

                        /*-- Auswahl, ob Link für RSS $nachricht->link oder Atom $nachricht->link->attributes() --*/

                        if ($nachricht->link) {

                            if ((string)$nachricht->link) {
			                                    $this->link = $nachricht->link;
							                                }
											                            elseif ($nachricht->link->attributes()) {
														                                    $attr = $nachricht->link->attributes();
																		                                    $this->link = $attr["href"];
																						                                }
																										                        }

                        /*-- Titel mit Link ----------------------------------------------------------------------*/

                        if ($nachricht->title) {
			                            echo "\x20\x20<h3><a href=\"".htmlspecialchars($this->filtereLinks($this->link), ENT_QUOTES)."\" target=\"_blank\">".
						                                $this->formeHTML(htmlspecialchars($this->filtereHTML($nachricht->title, $image), ENT_QUOTES))."</a></h3>\n";
										                        }

                        /*------------------------------------------------------------------------------------------
			                         Einen Hinweis anzeigen, falls Bilder als Beilage mit enclosure der Nachricht hinzugefügt
						                          wurden.
									                           -------------------------------------------------------------------------------------------
												                          */
															                          if ($nachricht->enclosure) {

                            if ($nachricht->enclosure->attributes()) {
			                                    $url = $nachricht->enclosure->attributes();

                                if (strpos($url["url"], ".jpg") or strpos($url["url"], ".png")) {

                                    $this->enco = "<br".SCHLIESSE."\n\x20\x20<span class=\"klein\">Beilage Medien: ".
				                                                 htmlspecialchars($this->filtereLinks($url["url"]), ENT_QUOTES)."</span>";
										                                 }
														                             }
																	                             }

                        $image = true;       /* true nur für description erst einmal und falls kein Fund, dann auch
			                                                für content und summary */

                        /*-- description und content für RSS und summary für Atom --------------------------------*/

                        if ($nachricht->description and $this->descr != false) {
			                            echo "\x20\x20<p>".$this->formeHTML(htmlspecialchars($this->filtereHTML($nachricht->description, $image), ENT_QUOTES))."</p>\n";

                            if ((strpos((string)$nachricht->description, "<img")) !== false) {
			                                    $image = false;
							                                }
											                        }
														                        if ($nachricht->content and $this->conte != false) {
																	                            echo "\x20\x20<p>".$this->formeHTML(htmlspecialchars($this->filtereHTML($nachricht->content, $image), ENT_QUOTES))."</p>\n";
																				                            }
																							                            if ($nachricht->summary and $this->summa != false) {
																										                                echo "\x20\x20<p>".$this->formeHTML(htmlspecialchars($this->filtereHTML($nachricht->summary, $image), ENT_QUOTES))."</p>\n";
																														                        }

                        $image = false;  /*- Nur für den Fall, dass false nicht durch description ausgelöst wurde */

                        /*-- Link unten --------------------------------------------------------------------------*/

                        if ($this->link != false) {
			                            echo "\x20\x20<p class=\"unten\"><a href=\"".htmlspecialchars($this->filtereLinks($this->link), ENT_QUOTES)."\" target=\"_blank\">".
						                                     $this->linkt."</a><br".SCHLIESSE."\n";
										                             }

                        /*-- pubDate für RSS und published und updated für Atom ----------------------------------*/

                        if ($nachricht->pubDate) {
			                            echo "\x20\x20<br".SCHLIESSE."".htmlspecialchars(substr($nachricht->pubDate,  0, 16), ENT_QUOTES).
						                                     " um ".htmlspecialchars(substr($nachricht->pubDate, 17,  5), ENT_QUOTES)." Uhr";

                        } elseif ($nachricht->updated) {
			                            echo "\x20\x20<br".SCHLIESSE.$this->formeDatumZeit($nachricht->updated)."\n";

                        } elseif ($nachricht->published) {
			                            echo "\x20\x20<br".SCHLIESSE.$this->formeDatumZeit($nachricht->published)."\n";
						                            }

                        /*-- Autor oder Creator für RSS und Atom -------------------------------------------------*/

                        if ($nachricht->author) {
			                            echo " von ".htmlspecialchars($this->filtereHTML($nachricht->author, $image), ENT_QUOTES).$this->enco."</p>\n";

                        } elseif ($nachricht->creator) {
			                            echo " von ".htmlspecialchars($this->filtereHTML($nachricht->creator, $image), ENT_QUOTES).$this->enco."</p>\n";

                        } else {
			                            echo "\x20\x20".$this->enco."</p>\n";
						                            }

                        /*-- Trennlinie zwischen den einzelnen Mitteilungen --------------------------------------*/

                        echo "\x20\x20<hr class=\"linie\"".SCHLIESSE."\n";
			                        $this->si++;
						                        if ($this->si == $this->maxim) {break;
									                        }
												                    }
														                    } echo "</div>\n";
																                } else { echo "<br".SCHLIESSE."Mehrere Titel oder Items scheinen fehlerhaft zu sein!\n";
																		            }
																			            } else { echo "<br".SCHLIESSE."Feed konnte nicht geladen werden!\n";
																				            }
																					        }
																						}

?>