<?php 

error_reporting( E_ALL );
ini_set('display_errors', 1);

require_once "db.php";
require_once "functions.php";

$ip      = client_ip();
$ref     = (isset($_GET["ref"])) ? $_GET["ref"] : NULL;
$action  = (isset($_GET["action"])) ? $_GET["action"] : NULL;
$dist_id = (isset($_GET["dist"])) ? strtolower($_GET["dist"]) : NULL;


if (!!$ref)
{
    $dist_id = 'default';
    $user = $ndb->usr()->where("id = $ref")->limit(1)->fetch();
}



$dist = $ndb->distribuition()->where("application_id = $app_id AND id = '$dist_id'")->limit(1)->fetch();


if (!!$dist)
{


    /*
    **  DOWNLOAD FILE
    */
    $fileName = dirname( dirname(__FILE__) )."/download/" . "sender_".$dist_id."_install.exe";
    $distName = str_replace(' ', '_', $dist["name"]);
    $downName = $distName."_install.exe";

    $readableStream = fopen($fileName, 'rb');
    $writableStream = fopen('php://output', 'wb');

    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="'.$downName.'"');
    stream_copy_to_stream($readableStream, $writableStream);
    ob_flush();
    flush();



    /*
    **  GEO IP
    */
    $geo         = ip_to_geo($ip);
    $geo_city    = is_null($geo->city) ? "Unknown" : $geo->city;
    $geo_region  = is_null($geo->region_code)  ? $geo->region_name  : $geo->region_code ;
    $geo_country = is_null($geo->country_code) ? $geo->country_name : $geo->country_code;



    /*
    ** SAVE IP REF 
    */
    $ndb->downloads->insert(
        array(
            'ip' => $ip,
            'usr_id' => $ref,
            'geo' => json_encode($geo),
        )
    );



    /*
    **  NOTIFICATION
    */
    $text  = "📥 New download $downName";
    $text .= " from {$ip}, {$geo_city}, {$geo_region}, {$geo_country}.";

    if (isset($user))
    {
        $text .= "\nRef: ".$user['name'];
    }

    AddSMS("-1001385929252", $text);

    if ($dist_id == "vjr901") 
    {
        AddSMS("1087969787", $text);
    }



} else {
    echo "ops!";
}


    
    