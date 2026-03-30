<?php 
error_reporting( E_ALL );
ini_set('display_errors', 1);


function curl_get_contents($url) {
  $ch = curl_init();
  //$timeout = 5;

  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_HEADER, false);
  //curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);

  $data = curl_exec($ch);

  $info = curl_getinfo($ch);

  curl_close($ch);

  return $data;

}

$text = urlencode("💣😍 line1". PHP_EOL ."line2". PHP_EOL ."line3");
//$text = "line1";
$rrr  = curl_get_contents("http://gateway.rfidle.com/sms/send?to=333147213&text={$text}&gateway=telegram");

var_dump($rrr);
