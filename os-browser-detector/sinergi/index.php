<?php 

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'vendor/autoload.php';

use Sinergi\BrowserDetector\Os;
use Sinergi\BrowserDetector\Browser;

$os = new Os();
echo $os->getName() . " ".$os->getVersion();
echo '<hr>';

$browser = new Browser();
echo $browser->getName() . " ".$browser->getVersion();
echo '<hr>';



