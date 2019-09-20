<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

$gesamtStunden = 0;
$vertretungStunden = 0;
$sparten = null;

echo '
<table cellpadding="5" cellspacing="0" style="width: 100%; ">

 <tr>
 <td colspan="1" style="font-size:1.3em; font-weight: bold; text-align: left;">
<br>
Zusammenfassung:
<br>
 </td>
 </tr>
 <tr style="background-color: #cccccc; padding:5px;">
 <td style="padding:5px;"><b>Datum</b></td>
 <td style="text-align: center;"><b>Kurs</b></td>
 <td style="text-align: center;"><b>Sparte</b></td>
 <td style="text-align: center;"><b>Dauer</b></td>
 </tr>';
//HIER SIND DIE SPEZIFISCHEN DATEN
  console("start showing");
  $date = null;
  if(isset($bigdata))
  {
    foreach($bigdata as &$value)
    {
      if(!isset($value))
      {
        continue;
      }
      //hinmalen der Kurse, ungültige Kurse bereits im Vorraus gelöscht
      if($date == $value[0])
      {
        echo "<tr><td>Kursnummer: $value[4]</td><td>$value[5]</td><td>$value[7]</td><td>$value[6]min</td></tr>";
      }
      else
      {
        $date = $value[0];
        echo "<tr><td>$value[1], den $date:</td></tr>";
        echo "<tr><td>Kursnummer: $value[4]</td><td>$value[5]</td><td>$value[7]</td><td>$value[6]min</td></tr>";
      }
      if(isset(${$value[7].'Stunden'}))
      {
        ${$value[7].'Stunden'} += intval($value[6]);
      }
      else
      {
        ${$value[7].'Stunden'} = 0;
        ${$value[7].'Stunden'} += intval($value[6]);
        $sparten[count($sparten)] = $value[7];
      }
      $gesamtStunden = $gesamtStunden + intval($value[6]);
      //echo "<br>Datum - Beginn-EndeUhr - Kursnummer - Kurstitel => DauerMin";
    }
  }
  if(isset($vertretungen))
  {
    echo "<tr style='width:100%;'><td><b>Vertretungen:</b></td></tr>";
    $date = null;
    foreach($vertretungen as &$value)
    {
      if(!isset($value))
      {
        continue;
      }
      //hinmalen der Kurse, ungültige Kurse bereits im Vorraus gelöscht
      if($date == $value[0])
      {
        echo "<tr style='width:100%;'><td>Kursnummer: $value[4]</td><td>$value[5]</td><td>$value[7]</td><td>$value[6]min</td></tr>";
      }
      else
      {
        $date = $value[0];
        echo "<tr><td style='text-align:center;'>$value[1], den $date:</td></tr>";
        echo "<tr style='width:100%;'><td>Kursnummer: $value[4]</td><td>$value[5]</td><td>$value[7]</td><td>$value[6]min</td></tr>";
      }
      $vertretungStunden += $value[6];
      //echo "<br>Datum - Beginn-EndeUhr - Kursnummer - Kurstitel => DauerMin";
    }
  }
//HIER ZUENDE
if(isset($sparten))
{
  foreach($sparten as &$value)
  {
    $stundenzahl = ${$value.'Stunden'}/60;
    echo '
      <tr style="background-color: #cccccc; padding:5px;">
        <td style="text-align: left;"><b>'.$value.'stunden: </b></td>
        <td></td>
        <td></td>
        <td><b>'.number_format(${$value.'Stunden'}/60, 2, ',', '').' h</b></td>
      </tr>';
    $month = substr($date,3,2);
    switch($month)
    {
      case "01": $month = "Januar"; break;
      case "02": $month = "Februar"; break;
      case "03": $month = "März"; break;
      case "04": $month = "April"; break;
      case "05": $month = "Mai"; break;
      case "06": $month = "Juni"; break;
      case "07": $month = "Juli"; break;
      case "08": $month = "August"; break;
      case "09": $month = "September"; break;
      case "10": $month = "Oktober"; break;
      case "11": $month = "November"; break;
      case "12": $month = "Dezember"; break;
      default: $month = "ungültiger Monat"; break;
    }
    $query = "INSERT INTO `Rechnungen` (`Vorname`, `Nachname`, `ID`, `Monat`,`Sparte`,`Stundenzahl`) VALUES ('$firstname', '$lastname', '$user[0]', '$month', '$value','$stundenzahl');";
    $result = mysqli_query($connection,$query);
  }
}
if($vertretungStunden > 0)
{
  echo '
    <tr style="background-color: #cccccc; padding:5px;">
      <td style="text-align: left;"><b>Vertretungen: </b></td>
      <td></td>
      <td></td>
      <td><b>'.number_format($vertretungStunden/60, 2, ',', '').' h</b></td>
    </tr>';
}

echo'
            <tr style="background-color: #cccccc; padding:5px; border: thin black;">
                <td style="text-align: left;"><b>Gesamtstunden: </b></td>
                <td></td>
                <td></td>
                <td><b>'.number_format($gesamtStunden/60, 2, ',', '').' h</b></td>
            </tr>
        </table>
<br><br><br>';
