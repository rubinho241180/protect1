<?php 

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require "os-browser-functions.php";

/*
require 'os-browser-detector/sinergi/vendor/autoload.php';
require 'os-browser-detector/cbschuld/vendor/autoload.php';

use Sinergi\BrowserDetector\Os as SinergiOs;
use Sinergi\BrowserDetector\Browser as SinergiBrowser;

$os = new SinergiOs();
echo $os->getName() . " ".$os->getVersion();
echo "<br>";

$browser = new SinergiBrowser();
echo $browser->getName() . " ".$browser->getVersion();
echo '<hr>';

$browser = new Browser();
echo $browser->getBrowser() . " ".$browser->getVersion()."<br>";
echo '<hr>';
*/

$info = client_info();
echo $info->os->name . " ".$info->os->version;
echo "<br>";

echo $info->browser->name . " ".$info->browser->version;
echo '<hr>';



echo $_SERVER['HTTP_USER_AGENT'];