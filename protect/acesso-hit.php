<?php 

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

define("TIMEZONE", "America/Recife");
date_default_timezone_set(TIMEZONE);

//header("Access-Control-Allow-Origin: *");

require_once "db.php";
require_once "functions.php";
require_once "os-browser-functions.php";

$ip = client_ip();
$date = date('Y-m-d');

/*
**  OS, BROWSER
*/
$info  = client_info();

/*
** BROWSER_ID *SESSION*
*/
$browser_id = browserId();


$hit =
    $ndb->acesso()
        ->where("(date = ?) AND (browser_id = ? OR ip = ?)", array($date, $browser_id, $ip))
            ->fetch();

if ($hit) {

    //UPDATE
    $hit["qt"] = $hit["qt"]+1;
    $hit->update();

} else {

    //INSERT
    $ndb->acesso()->insert(
        array(
            "date"       => $date,
            "browser_id" => $browser_id,
            "ip"         => $ip,
            "os"         => mb_strtolower($info->os->name),
            "uri"        => "/",
            "qt"         => 1,   
        )
    );
}

echo $browser_id.": ".$hit["qt"];
