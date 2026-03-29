<?php 

require 'os-browser-detector/sinergi/vendor/autoload.php';
require 'os-browser-detector/cbschuld/vendor/autoload.php';

use Sinergi\BrowserDetector\Os as SinergiOs;
//use Sinergi\BrowserDetector\Browser as SinergiBrowser;

function client_info() {
    $os = new SinergiOs();
    $browser = new Browser();

    return json_decode( json_encode(
        array(
            "os" => array(
                "name" => $os->getName(),
                "version" => $os->getVersion(),
            ),
            "browser" => array(
                "name" => $browser->getBrowser(),
                "version" => $browser->getVersion(),
            ),
        ))
    );


}