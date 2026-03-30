<?php 

  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);
  error_reporting(E_ALL);

  header("Access-Control-Allow-Origin: *");

require_once "functions.php";

function strToHex($string){
    $hex='';
    for ($i=0; $i < strlen($string); $i++){
        $hex .= dechex(ord($string[$i]));
    }
    return $hex;
}

echo "<pre>";
var_dump($_COOKIE);
echo "</pre>";

$found = false;

foreach ($_COOKIE as $key => $value) {
    echo "$key => $value <br>";
    $found = $found || (substr($key, 0, 4) == "__j_"); 
}

if ($found)
{
    echo "found";
} else {

    $json = array(
      "browser_id" => generateRandomString(8),     
    );


    $cookie_name = "__j_".time()."_".generateRandomString(8);
    //$cookie_name = "__j_".time()."_".strToHex(json_encode($json));
    $cookie_value = time();
    setcookie($cookie_name, $cookie_value, 2147483647, "/", "r2.rfidle.com", false, true);

    echo "NOT found, created! $cookie_name";
}

