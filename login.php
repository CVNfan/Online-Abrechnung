<?php
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
include("GiveItToMe.php");
include("wochentage.php");
include("feiertage.php");
include("ferientage.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
  <meta content="de" http-equiv="Content-Language" />
  <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
  <title>Area 42</title>
  <script type='text/javascript'>
        function addFields(){
            // Container <div> where dynamic content will be placed
            var container = document.getElementById("container");
            // Clear previous contents of the container
                // Append a node with a random text
                container.appendChild(document.createTextNode("Vertretung für:"));
                container.appendChild(document.createElement("br"));

                container.appendChild(document.createTextNode("Vorname:"));
                var input = document.createElement("input");
                input.type = "text";
                input.name = "vertreteneKurse[]";
                container.appendChild(input);
                container.appendChild(document.createElement("br"));

                container.appendChild(document.createTextNode("Nachname:"));
                var input = document.createElement("input");
                input.type = "text";
                input.name = "vertreteneKurse[]";
                container.appendChild(input);
                container.appendChild(document.createElement("br"));

                container.appendChild(document.createTextNode("Datum:"));
                var input = document.createElement("input");
                input.type = "date";
                input.min = "$year-$monthInt-01";
                input.max = "$year-$monthInt-31";
                input.name = "vertreteneKurse[]";
                container.appendChild(input);
                container.appendChild(document.createElement("br"));

                container.appendChild(document.createTextNode("Kursnummer:"));
                var input = document.createElement("input");
                input.type = "text";
                input.name = "vertreteneKurse[]";
                container.appendChild(input);
                container.appendChild(document.createElement("br"));

                container.appendChild(document.createElement("br"));
        }
        function deleteFields()
        {
          var container = document.getElementById("container");
          container.innerHTML = "";
        }
    </script>
</head>

<body>
  <?php
  if($_POST)
  {
    mysqli_select_db($connection,$dbname) or die("Fehler bei der Datenbanktabellenauswahl.");

    $month = isset($_POST['Monat']) ? $_POST['Monat'] : "";
    if (empty(trim($month))) die ("Monat muss aufgefüllt werden.");

    //$year = $_POST['Jahr'];
    $year = isset($_POST['Jahr']) ? $_POST['Jahr'] : "";
    if (empty(trim($year))) die ("Jahr muss aufgefüllt werden.");

    //$firstname = $_POST['firstname'];
    $firstname = isset($_POST['firstname']) ? $_POST['firstname'] : "";
    if (empty(trim($firstname))) die ("Vorname muss aufgefüllt werden.");

    //$lastname = $_POST['lastname'];
    $lastname = isset($_POST['lastname']) ? $_POST['lastname'] : "";
    if (empty(trim($lastname))) die ("Nachname muss aufgefüllt werden.");

    //$userpw = $_POST['pw'];
    $userpw = isset($_POST['pw']) ? $_POST['pw'] : "";
    if (empty(trim($userpw))) die ("Passwort muss aufgefüllt werden.");

    //Abfrage ob PW korrekt
    $query = "SELECT ID, Vorname, Nachname, PLZ, Ort, Strasse, Hausnummer, SteuerID FROM `User` WHERE Vorname='$firstname' AND Nachname='$lastname' AND Passwort='$userpw'";
    $result = mysqli_query($connection,$query) or die (mysqli_error($connection));
    $row = mysqli_fetch_array($result);
    if($row==FALSE)
    {
      sleep(5);
      die("Keine Passwort-Nutzer-Übereinstimmung gefunden.");
    }
    for($i = 0; $i < 8; $i++)
    {
      $user[$i] = $row[$i];
    }
    $userid = $row[0];
  }
  else {
    die("Fehler bei der Formularübermittlung. Probiere es doch nochmal.");
  }
  $query = "SELECT Kursnummer, Kurstitel, Uhrzeit_Beginn, Uhrzeit_Ende, Wochentag, Raum, Kategorie, Sparte FROM `Kurse` WHERE Kursleiter1=$userid OR Kursleiter2=$userid OR Kursleiter3=$userid OR Kursleiter4=$userid";
  $result = mysqli_query($connection,$query);
  if (!mysqli_query($connection,$query)) die("Keine Passwort-Nutzer-Übereinstimmung gefunden.");
  $num = 0;

  //console("convert in data");
  while($row = mysqli_fetch_array($result))
     {
        for($i=0;$i<12; $i++)
        {
          ${'data' . $num . "_" . $i} = $row[$i];
        }
        $num++;
     }
    //console("done");

    if($num != mysqli_num_rows($result)) die("error: no clear rows");
    //console("conversion...");
    for($i=0;$i<$num;$i++)
    {
      ${'kursdata'.$i} =  array(${'data'.$i.'_0'},//ID
                                ${'data'.$i.'_1'},//Kursname
                                ${'data'.$i.'_2'},//Beginn
                                ${'data'.$i.'_3'},//Ende
                                ${'data'.$i.'_4'},//Wochentag
                                ${'data'.$i.'_5'},//Raum
                                ${'data'.$i.'_6'},//Kategorie
                                ${'data'.$i.'_7'});//Sparte
    }
    for($i=0;$i<$num;$i++)
    {
      switch(${'kursdata'.$i}[4])
      {
        case "Montag":     ${'kursdata'.$i}[8] = "1"; break;
        case "Dienstag":   ${'kursdata'.$i}[8] = "2"; break;
        case "Mittwoch":   ${'kursdata'.$i}[8] = "3"; break;
        case "Donnerstag": ${'kursdata'.$i}[8] = "4"; break;
        case "Freitag":    ${'kursdata'.$i}[8] = "5"; break;
        case "Samstag":    ${'kursdata'.$i}[8] = "6"; break;
        case "Sonntag":    ${'kursdata'.$i}[8] = "7"; break;
      }
    }
    switch($month)
    {
      case 'Januar':    $monthInt = 1; break;
      case 'Februar':   $monthInt = 2; break;
      case 'März':      $monthInt = 3; break;
      case 'April':     $monthInt = 4; break;
      case 'Mai':       $monthInt = 5; break;
      case 'Juni':      $monthInt = 6; break;
      case 'Juli':      $monthInt = 7; break;
      case 'August':    $monthInt = 8; break;
      case 'September': $monthInt = 9; break;
      case 'Oktober':   $monthInt = 10; break;
      case 'November':  $monthInt = 11; break;
      case 'Dezember':  $monthInt = 12; break;
    }
    for($i=0;$i<$num;$i++)
    {
      $day = ${'kursdata'.$i}[8];
      $wochentag = ${'kursdata'.$i}[4];
      ${'kurse'.$i} = tage($monthInt,$year,$day);
      $kursid = ${'kursdata'.$i}[0];
      $kursname = ${'kursdata'.$i}[1];
      $kursbeginn = substr(${'kursdata'.$i}[2],0,5);
      $kursende = substr(${'kursdata'.$i}[3],0,5);
      $zeitaufwand = (strtotime($kursende)-strtotime($kursbeginn))/60;
      $sparte = ${'kursdata'.$i}[7];
      //echo $sparte;
      //erzeugt den zu sendenden Kurs
      foreach(${'kurse'.$i} as &$value) $value = array("$value.$monthInt.$year","$wochentag","$kursbeginn","$kursende","$kursid","$kursname","$zeitaufwand","$sparte");
    }
    $alldays[] = '';
    for($i=0;$i<$num;$i++)
    {
      $alldays = array_merge(${'kurse'.$i},$alldays);
    }
    sort($alldays);
    unset($alldays[0]);//entfernt erstes Element (normalerweise leer)
    //console($alldays);
    $date = null;
    ?>
<h2>Willkommen bei unserer Online-Abrechnung!</h2>
<?php echo "für $firstname $lastname." ?>
<p>Bitte wähle alle Kurse aus, die du diesen Monat gehalten hast.</p>
<?php //printed die (hoffentlich) gehaltenen Stunden
    echo "Deine gehaltenen Stunden für $month $year:<form target='_blank' action='send.php' method='post'>";
    $connectorFei = LPLib_Feiertage_Connector::getInstance();
    $connectorFer = LPLib_Ferien_Connector::getInstance();
    $is_ferientag = $connectorFer->getFerientage();
    foreach($is_ferientag as &$value)
    {
      //print_r($value);
      if($value['year']==$year)
      {
        if(substr($value['start'],5,2)=="$monthInt" || substr($value['end'],5,2)=="$monthInt")
        {
          $start = strtotime($value['start']);
          $end = strtotime($value['end']);
        }
      }
    }
    foreach($alldays as &$value)
    {
      $is_feiertag = $connectorFei->isFeiertagInLand($value[0], LPLib_Feiertage_Connector::LAND_BAYERN);
      $outputvalue = implode('#%#',$value);
      $dateInt = strtotime($value[0]);
      if($date!=$value[0])
      {
        echo "<br>$value[1], den $value[0]";
        if($is_feiertag && $date != $value[0]) echo " (Feiertag)";
        else if($start<=$dateInt && $dateInt<=$end) echo " (Ferien)";
      }
      if($is_feiertag || ($start<=$dateInt && $dateInt<=$end))
      {
        $query = "SELECT Kategorie FROM `Kurse` WHERE Kursnummer=$value[4]";
        $result = mysqli_query($connection,$query);
        $row = mysqli_fetch_array($result);
        if($row[0]!="Ferienkurs")
        {
          echo "<br><input type='checkbox' name='gehalteneKurse[]' value='$outputvalue' disabled='true'>$value[2]-$value[3]Uhr - $value[4] - $value[5] => $value[6]min";
        }
        else
        {
          echo "<br><input type='checkbox' name='gehalteneKurse[]' value='$outputvalue'>$value[2]-$value[3]Uhr - $value[4] - $value[5] => $value[6]min";
        }
      }
      else
      {
        echo "<br><input type='checkbox' name='gehalteneKurse[]' value='$outputvalue'>$value[2]-$value[3]Uhr - $value[4] - $value[5] => $value[6]min";
      }
      //echo $value[7];
      $date = $value[0];
    }
    mysqli_close($connection);
    $useroutput = implode('#%#',$user);
    echo "<input type='hidden' name='User' value='$useroutput'>";
    echo "<br><br>Stunden an Feier-/Ferientagen können von <a href='mailto:heidi@circusverein.de?cc=johannes@circusverein.de&subject=abrechnung'>Heidi</a> freigegeben werden.";
    echo "<br><div>Hier kannst du vertretene Stunden angeben</div>";
    echo "<a href='#' id='filldetails' onclick='addFields()'>Vertretungsstunde eintragen</a>";
    echo "<div id='container'></div>";
    echo "<a href='#' id='deletedetails' onclick='deleteFields()'>Vertretungsstunden löschen</a>";
    echo "<br><br><button type='submit'>Abschicken</button></form>";

    ?>

  </body>
</html>
