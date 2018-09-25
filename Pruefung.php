<!DOCTYPE html>
<html lang="de">
<head>
  <title>Herzlich Wilkommen</title>
  <meta charset="UTF-8">
  <meta name="author" content="Thomas Kleebaum-Nagy">
  <meta name="description" content="Prüfung Wintersemester 2017/18">
  <meta name="keywords" content="Willkommen">
  <link rel="stylesheet" href="css/marquee.css">
</head>
<body>
<h1>Prüfungsplanung Wintersemester 2017/18</h1>
<?php

$hasTitle = true;

echo '<table border="1" cellspacing="0" cellpadding="2" width="100%" class="csvTable">';


$handle = fopen("pruefung.csv", "r");
$start = 0;

while (($data = fgetcsv($handle, 1000, ";")) !== FALSE)
{

    echo '<tr>' . "\n";

  for ( $x = 0; $x < count($data); $x++)
   {
    if ($start == 0 && $hasTitle == true)
        echo '<th>'.$data[$x].'</th>' . "\n";
    else
        echo '<td>'.$data[$x].'</td>' . "\n";
    }
    $start++;

    echo '</tr>' . "\n";

}

fclose($handle);

echo '</table>';

?>

</body>
</html>

