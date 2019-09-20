<?php
function console( $data ) {
  $output = $data;
  if ( is_array( $output ) )
      $output = implode( ',', $output);
      echo "<script>console.log( '" . $output . "' );</script>";
    }
  console ("Connect to database...");
  error_reporting(E_ALL);

  $mysqlhost = "rdbms.strato.de";
  $mysqluser = "U3468070";
  $mysqlpwd = "gNLNXnFvfphL8+9";
  $dbname = "DB3468070";
  $dbtable = "Kurse";

  $connection = mysqli_connect($mysqlhost, $mysqluser, $mysqlpwd) or die("Verbindung zur Datenbank fehlgeschlagen.");
  mysqli_set_charset($connection,'utf-8');
  console("done");

?>
