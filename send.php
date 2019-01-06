<html>
  <head>
    <meta content="de" http-equiv="Content-Language" />
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
    <title>Abgeschickt</title>
  </head>
<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

  include("GiveItToMe.php");
  include("feiertage.php");
  include("ferientage.php");
  mysqli_select_db($connection,$dbname) or die("Datenbank not found");

  //Prüft auf Nutzer
  if($_POST['User'])
  {
    $user = explode('#%#',$_POST['User']);
    if($user)
    {
      $firstname = $user[1];
      $lastname = $user[2];
      $userid = intval($user[0]);


    }
  }
  else console("kein Nutzer übergeben!");
  echo "Hallo $firstname $lastname!";
  $date = null;

  echo "<br>Deine Angaben wurden überprüft:";

  //Prüft auf gehaltene Kurse
  if(isset($_POST['gehalteneKurse']))
  {
    $i = 0;
    $bigdata[] = null;
    foreach($_POST['gehalteneKurse'] as &$value)
    {
      $bigdata[$i] = explode('#%#',$value);
      $i++;
    }
    foreach ($bigdata as $key => &$value)
    {
      //sieht nach ob dieser Trainer bereits gehalten hat
      console("hat der trainer diesen kurs bereits eingetragen?");
      $query = "SELECT `Kurstitel` FROM `gehalteneKurse` JOIN `User` ON gehalteneKurse.KursleiterTats = User.ID WHERE Datum='$value[0]' AND Vorname ='$user[1]' AND Nachname = '$user[2]' AND Kursnummer = '$value[4]'";
      $result = mysqli_query($connection,$query);
      $row = mysqli_fetch_array($result);
      console("Kurstitel -> Datum $value[0]:".mysqli_error($connection));
      if($row!="")
      {
        echo "<br>Du hast den Kurs $value[5] am $value[0] bereits eingetragen!";
        unset($bigdata[$key]);
        continue;
      }
    }
  }
  else console("keine regulären Kurse übergeben.");



  //Prüft auf Vertretungen
  if(isset($_POST['vertreteneKurse']))
  {
    $ffirstname = isset($_POST['vertreteneKurse'][0]) ? $_POST['vertreteneKurse'][0] : "";


    $llastname = isset($_POST['vertreteneKurse'][0]) ? $_POST['vertreteneKurse'][1] : "";

    $date = isset($_POST['vertreteneKurse'][2]) ? $_POST['vertreteneKurse'][2] : "";
    $_POST['vertreteneKurse'][2] = $date;
    //echo $date;

    $kursnummer = isset($_POST['vertreteneKurse'][3]) ? $_POST['vertreteneKurse'][3] : "";

    console("got vertretungen!");
    $i = 0;
    $j = 0;
    $vertretungen = null;
    if($date)
    foreach($_POST['vertreteneKurse'] as &$value)
    {
      switch($i)
      {
        case 0: $vertretungen[$j][0] = $value; if (empty(trim($ffirstname))) die ("<br>Vertretungen: Vorname muss ausgefüllt werden."); break; //Vorname
        case 1: $vertretungen[$j][1] = $value; if (empty(trim($llastname))) die ("<br>Vertretungen: Nachname muss ausgefüllt werden."); break; //Nachname
        case 2: if (empty(trim($date))) die ("<br>Vertretungen: Datum muss ausgefüllt werden."); $date = substr($date,8,2).'.'.substr($date,5,2).'.'.substr($date,0,4); $vertretungen[$j][2] = $date; break; //Datum
        case 3: if (empty(trim($kursnummer))) die ("<br>Vertretungen: Kursnummer muss ausgefüllt werden."); $vertretungen[$j][3] = $value;  $j++; $i = -1; break; //Kursnummer
      }
      $i++;
    }
    foreach($vertretungen as $key => &$value)
    {//existiert der vertrenen Kurs?
      $query = "SELECT Kursnummer, Kurstitel, Wochentag, Uhrzeit_Beginn, Uhrzeit_Ende, Sparte FROM `Kurse` JOIN `User` ON Kurse.Kursleiter1 = User.ID OR Kurse.Kursleiter2 = User.ID OR Kurse.Kursleiter3 = User.ID OR Kurse.Kursleiter4 = User.ID
                WHERE User.Vorname = '$value[0]' AND User.Nachname = '$value[1]' AND Kursnummer=$value[3]";

      $result = mysqli_query($connection,$query);
      $row = mysqli_fetch_array($result);

      //datum, wochentag, kursbeginn, kursende, kursid,kursname, zeitaufwand
      $kursid = $row[0];
      $kursname = $row[1];
      $sparte = $row[5];
      $zeitaufwand = (strtotime($row[4])-strtotime($row[3]))/60;
      $value = array("$value[2]","$row[2]",substr($row[3],0,5),substr($row[4],0,5),"$kursid","$kursname","$zeitaufwand","$sparte");
      if ($row[0]=="" || $row[1]=="")
      {
        echo "<br>FEHLER: Für $value[0], $value[5] $value[2]-$value[3]Uhr wurde kein übereinstimmenden Kurs gefunden! Bitte überprüfe deine Eingaben.";
        unset($vertretungen[$key]);
      }
      //sieht nach ob dieser Trainer bereits gehalten hat
      $query = "SELECT `Kurstitel` FROM `gehalteneKurse` JOIN `User` ON gehalteneKurse.KursleiterTats = User.ID WHERE Datum='$value[0]' AND Vorname ='$user[1]' AND Nachname = '$user[2]' AND Kursnummer = '$value[4]'";
      $result = mysqli_query($connection,$query);
      $row = mysqli_fetch_array($result);
      if($row!="")
      {
        echo "<br>Du hast den Kurs $value[5] am $value[0] bereits eingetragen!";
        unset($vertretungen[$key]);
        continue;
      }
    }
  }
  else console("keine vertretungen übergeben.");
  $year = substr($value[0],6,4);

  //DAS MALT DIE KURSE
  function drawKurse($connection, $data, $user, $date)
  {
    //console("start showing $data");
      foreach($data as $key => &$value)
      {
        if(!isset($value))
        {
          echo "ungültigen Kurs übersprungen.";
          continue;
        }

        $connectorFei = LPLib_Feiertage_Connector::getInstance();
        $connectorFer = LPLib_Ferien_Connector::getInstance();
        $is_ferientag = $connectorFer->getFerientage();
        $is_feiertag = $connectorFei->isFeiertagInLand($value[0], LPLib_Feiertage_Connector::LAND_BAYERN);
        $dateInt = strtotime($value[0]);
        $monthInt = substr($value[0],3,2);
        $year = substr($value[0],6,4);
        $date = null;

        foreach($is_ferientag as &$vvalue)
        {
          if($vvalue['year']==$year)
          {
            if(substr($vvalue['start'],5,2)=="$monthInt" || substr($vvalue['end'],5,2)=="$monthInt")
            {
              $start = strtotime($vvalue['start']);
              $end = strtotime($vvalue['end']);
            }
          }
        }
        $is_ferientag = ($start<=$dateInt && $dateInt<=$end);

        //Suche Trainerzahl des Kurses
        $query = "SELECT `Trainerzahl` FROM `Kurse` WHERE Kursnummer=$value[4]";
        $result = mysqli_query($connection,$query);
        if (!mysqli_query($connection,$query)) die(mysqli_error($connection)."Befehl für Trainerzahl nicht gültig.");
        $row = mysqli_fetch_array($result);
        $trainerzahl = intval($row[0]);
        //console("trainerzahl $trainerzahl");

        //Suche bereits gehaltene Kurse dieses Datums
        $query = "SELECT COUNT(Kursnummer) AS gehaltenzahl FROM `gehalteneKurse` WHERE Datum='$value[0]' AND Kursnummer=$value[4]";

        $result = mysqli_query($connection,$query);
        if (!mysqli_query($connection,$query)) die(mysqli_error($connection)."Befehl für gehaltenzahl nicht gültig");
        $row = mysqli_fetch_array($result);
        $gehaltenzahl = intval($row["gehaltenzahl"]);
        //console("gehaltenzahl $gehaltenzahl");

        if($is_feiertag || $is_ferientag)
        {
          $query = "SELECT Kategorie FROM `Kurse` WHERE Kursnummer=$value[4]";
          $result = mysqli_query($connection,$query);
          $row = mysqli_fetch_array($result);
          if($row[0]=="Ferienkurs")
          {
            $is_feiertag = false;
            $is_ferientag = false;
          }
        }

        //vgl Trainer- und Kurszahl
        if($gehaltenzahl < $trainerzahl && !$is_feiertag && !$is_ferientag) //wenn noch nicht alle eingetragen, Abrechnung
        {
          //Kurs in 'GehalteneKurse' eintragen
          $query = "INSERT INTO `gehalteneKurse` (`Kursnummer`, `Kurstitel`, `Datum`, `KursleiterTats`) VALUES ('$value[4]', '$value[5]', '$value[0]', '$user[0]');";
          $result = mysqli_query($connection,$query);

          //hinmalen der Kurse
          if($date == $value[0])
          {
            echo "<br>$value[2]-$value[3]Uhr - $value[4] - $value[5] => $value[6]min";
          }
          else
          {
            $date = $value[0];
            echo "<br>$value[1], den $date:";
            echo "<br>$value[2]-$value[3]Uhr - $value[4] - $value[5] => $value[6]min";
          }
          //echo "<br>Datum - Beginn-EndeUhr - Kursnummer - Kurstitel => DauerMin";

        }//Abbruch bei Dopplungen
        else if($is_feiertag && $is_ferientag) echo "<br>$value[0] ist ein Feier-/Ferientag!";
        else if($is_ferientag) echo "<br>$value[0] ist ein Ferientag!";
        else if($is_feiertag) echo "<br>$value[0] ist ein Feiertag!";
        else echo "<br>Der Kurs $value[5] wurde bereits vollständig gehalten.";
      }
    }
?>
  <body>
    <?php
    if(isset($bigdata)) drawKurse($connection, $bigdata, $user, $date); else echo "<br>- keine regulären Kurse empfangen. - ";
    if(isset($vertretungen)) drawKurse($connection, $vertretungen, $user, $date); else echo "<br> - keine vertretenen Kurse empfangen. - ";
    include("pdf-data.php");
    mysqli_close($connection);

    ?>
  </body>
  </html>
