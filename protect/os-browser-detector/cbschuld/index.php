<?php 

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'vendor/autoload.php';

//use Sinergi\BrowserDetector\Os;
//use Sinergi\BrowserDetector\Browser;


$browser = new Browser();
echo $browser->getBrowser() . " ".$browser->getVersion();
echo '<hr>';



