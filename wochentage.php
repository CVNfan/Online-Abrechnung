<?php
function tage($monat, $jahr, $wochentag) {
  $treffer=array();
  $letzterTag=mktime(0,0,0,$monat+1,0,$jahr);
  $tag=1;
  do {
    $zeitpunkt=mktime(0,0,0,$monat,$tag,$jahr);
    if (date("N",$zeitpunkt)==$wochentag) {
			if($tag<10) $tag = "0$tag";
      $treffer[]="$tag";
    }
    $tag++;
  } while ($zeitpunkt < $letzterTag);
  return $treffer;
}
?>
