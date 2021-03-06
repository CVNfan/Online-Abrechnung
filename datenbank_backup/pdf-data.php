<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

$gesamtStunden = 0;
$vertretungStunden = 0;
$sparten = null;

echo '
<table cellpadding="5" cellspacing="0" style="width: 100%; ">
 <tr>
 <td>'.nl2br(trim($rechnungs_header)).'</td>
    <td style="text-align: right">
Datum: '.$rechnungs_datum.'<br>
<br>
 </td>
 </tr>

 <tr>
 <td style="font-size:1.3em; font-weight: bold;">
<br><br>
Rechnung
<br>
 </td>
 </tr>


 <tr>
 <td colspan="2">'.nl2br(trim($rechnungs_empfaenger)).'</td>
 </tr>
</table>
<br><br><br>

<table cellpadding="5" cellspacing="0" style="width: 100%;" border="0">
 <tr style="background-color: #cccccc; padding:5px;">
 <td style="padding:5px;"><b>Datum</b></td>
 <td style="text-align: center;"><b>Kurs</b></td>
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
        echo "<tr><td>Kursnummer: $value[4]</td><td>$value[5]</td><td>$value[6]min</td></tr>";
      }
      else
      {
        $date = $value[0];
        echo "<tr><td>$value[1], den $date:</td></tr>";
        echo "<tr><td>Kursnummer: $value[4]</td><td>$value[5]</td><td>$value[6]min</td></tr>";
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
    echo "<tr><td><b>Vertretungen:</b></td></tr>";
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
        echo "<tr><td>Kursnummer: $value[4]</td><td>$value[5]</td><td>$value[6]min</td></tr>";
      }
      else
      {
        $date = $value[0];
        echo "<tr><td>$value[1], den $date:</td></tr>";
        echo "<tr><td>Kursnummer: $value[4]</td><td>$value[5]</td><td>$value[6]min</td></tr>";
      }
      $vertretungStunden += $value[6];
      //echo "<br>Datum - Beginn-EndeUhr - Kursnummer - Kurstitel => DauerMin";
    }
  }
echo '</table>';
//HIER ZUENDE
echo '
<hr>
<table cellpadding="5" cellspacing="0" style="width: 100%;" border="0">';
if(isset($sparten))
{
  foreach($sparten as &$value)
  {
    $stundenzahl = ${$value.'Stunden'}/60;
    echo '
      <tr>
        <td colspan="3"><b>'.$value.'stunden: </b></td>
        <td style="text-align: center;"><b>'.number_format(${$value.'Stunden'}/60, 2, ',', '').' h</b></td>
      </tr>';
    $query = "INSERT INTO `Rechnungen` (`Vorname`, `Nachname`, `ID`, `Monat`,`Sparte`,`Anzahl`) VALUES ('$firstname', '$lastname', '$user[0]', 'piep', '$value','$stundenzahl');";
    $result = mysqli_query($connection,$query);
  }
}
if($vertretungStunden > 0)
{
  echo '
    <tr>
      <td colspan="3"><b>Vertretungen: </b></td>
      <td style="text-align: center;"><b>'.number_format($vertretungStunden/60, 2, ',', '').' h</b></td>
    </tr>';
}

echo'
            <tr style="background-color: #cccccc; padding:5px;">
                <td colspan="3"><b>Gesamtstunden: </b></td>
                <td style="text-align: center;"><b>'.number_format($gesamtStunden/60, 2, ',', '').' h</b></td>
            </tr>
        </table>
<br><br><br>';

echo 'Ich versichere, dass ich mein Honorar selbstständig versteuern werde.<br><br>';

echo "<div class='right'><p class='right'>____________________________<br>Unterschrift</p></div>";

echo nl2br($rechnungs_footer);
