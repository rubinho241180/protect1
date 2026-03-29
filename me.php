<?php 
header('Access-Control-Allow-Origin: https://mrsender.site');
header('Access-ContRol-Allow-Credentials: true');

require "functions.php";

echo browserId();
echo "<hr>";

$name = 'x1';
/*
** BROWSER_ID *SESSION*
*/
if (isset($_COOKIE[$name])) {

  $vbrowser_id = $_COOKIE[$name];
} else {

  $vbrowser_id = generateRandomString(16);
  setcookie($name, $vbrowser_id, 0, "/", "r2.rfidle.com", false, true);
}

echo "{$name}: {$vbrowser_id}";
echo "<hr>";

/*
** BROWSER_ID *SESSION*
*/
$name = 'x2';
if (isset($_COOKIE[$name])) {

  $vbrowser_id = $_COOKIE[$name];
} else {

  $vbrowser_id = generateRandomString(16);
  setcookie($name, $vbrowser_id, 2147483647, "/; SameSite=None; Secure", "r2.rfidle.com", false, true);
  //setcookie("xyz12", $vbrowser_id, 2147483647, "/; SameSite=Lex; Secure");
}




echo "{$name}: {$vbrowser_id}";
echo "<hr>";


/*
** BROWSER_ID *SESSION*
*/
$name = 'x3';
if (isset($_COOKIE[$name])) {

  $vbrowser_id = $_COOKIE[$name];
} else {

  $vbrowser_id = generateRandomString(16);
  setcookie($name, $vbrowser_id, 2147483647, "/; SameSite=None;", "r2.rfidle.com", false, false);
}




echo "{$name}: {$vbrowser_id}";
echo "<hr>";

/*
** BROWSER_ID *SESSION*
*/
$name = 'x4';

if (isset($_COOKIE[$name])) {

  $vbrowser_id = $_COOKIE[$name];
} else {

  $vbrowser_id = generateRandomString(16);
  setcookie($name, $vbrowser_id, 2147483647, "/; SameSite=None;", "r2.rfidle.com", false, true);
}




echo "{$name}: {$vbrowser_id}";
echo "<hr>";





?>

<script type="text/javascript">
    console.log(document.cookie);
    console.log(localStorage);
</script>