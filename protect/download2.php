<?php 

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/*
if ($_SERVER['HTTPS'] != "on") {
    $url = "https://". $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
    header("Location: $url");
    exit;
}
*/

header("Access-Control-Allow-Origin: *");
//header('Content-Type: text/html; charset=utf-8');

require_once "db.php";
require_once "const.php";
require_once "functions.php";
require_once "os-browser-functions.php";

$ip = client_ip();


/*
** CRAWLER DETECT
*/
$crawler = crawlerDetect($_SERVER['HTTP_USER_AGENT']);
 
if ($crawler)
{
   die($crawler . ' detected.');
}

/*
** BROWSER_ID *SESSION*
*/
$browser_id = browserId();

//echo $browser_id;

/*
** BROWSER


$browser =
    $ndb->ai_browser()
        ->where("id = ?", $browser_id)->fetch();

if ($browser) {
    //if has OS, update OS's IP
    if ($browser->ai_os) {
        $browser->ai_os["ip"] = $ip;   
        $browser->ai_os->update();   
    } else {
    //if NOT has OS
        $browser["ip"] = $ip; 
        $browser->update();
    }

    //update IP
    if ($browser["ai_os_id"] != NULL) {



    }
}
*/

$browser_id4   = substr($browser_id, 0, 4);
$download_hash = substr(md5(mt_rand()), 0, 4);

$silent  = isset($_GET["silent"]);
$src     = isset($_GET["src"]) ? $_GET["src"] : NULL;
$cfrom   = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : NULL;

$ref_id  = isset($_GET["ref"]) ? $_GET["ref"] : NULL;


if (!!$ref_id)
{
    $ref = $ndb->referral()->where("id = '$ref_id'")->limit(1)->fetch();

    if (!$ref) 
        die("Ref {$ref_id} not found.");

    $dist    = $ref->distribuition;
    $appl_id = $ref->distribuition["application_id"];
    $dist_id = $ref->distribuition["id"];
    $publ_id = $ref->distribuition["publisher_id"];

} else die("Ref is null. ". time());


if (!!$dist)
{
    /*
    **  DOWNLOAD FILE
    */
    $downBase = "//18.216.171.146/protect/download/";
    $downBase = "//13.58.145.26/protect/download/";
    $downBase = "//54.160.66.249/protect/download/";
    $downBase = "//r2.rfidle.com/protect/dl/";

    //$downBase = "//truesistemas.com/protect/download/";
    //$downBase = "//rfidle.com/download/";
    $distId   = $dist["id"];
    $distName = $dist["name"];
    $fileName = $distId."_Install.exe";
    $downName = str_replace(' ', '_', $distName)."_install.exe";

    //echo $downName . '<hr>';


    /*
    **  OS, BROWSER
    */
    $info  = client_info();

    /*
    **  GEO IP
    */
    
    try {
        $geo         = ip_to_geo($ip);
        $geo_city    = is_null($geo->city)         ? "Unknown"          : $geo->city;
        $geo_region  = is_null($geo->region_code)  ? $geo->region_name  : $geo->region_code ;
        $geo_country = is_null($geo->country_code) ? $geo->country_name : $geo->country_code;
    } catch (Exception $e) {
        
        $geo         = [];
        $geo_city    = "EE";
        $geo_region  = "EE";
        $geo_country = "EE";
    }



    /*
    ** SAVE IP REF 
    */
    $download =
        $ndb->download->insert(
            array(
                'ip'          => $ip,
                'hash'        => $download_hash,
                'browser_id'  => $browser_id,
                'referral_id' => $ref['id'],
                'city'        => $geo_city, 
                'region'      => $geo_region, 
                'country'     => $geo_country, 
                'os'          => mb_strtolower($info->os->name),
                'os_ver'      => $info->os->version,
                'browser'     => mb_strtolower($info->browser->name),
                'browser_ver' => $info->browser->version,
                'agent'       => $_SERVER['HTTP_USER_AGENT'],
                'geo'         => json_encode($geo),
                'cfrom'       => $cfrom,
                'src'         => $src
            )
        );
        
    $download_id = $download['id'];


    /*
    **  NOTIFICATION
    */
    $text  = "⬇⏳ Download ({$browser_id4}-{$download_hash}) {$downName} from {$ip}, {$geo_city}-{$geo_region}, {$geo_country}";

    $text .= "\n{$info->browser->name} {$info->browser->version}, {$info->os->name} {$info->os->version}.";

    //find customer by ip
    $installation =
        $ndb->ins()->where("ip = ?", $ip)->order("id DESC")->limit(1)->fetch();

    if ($installation)
    {
        $customer = $installation->cus;

        $text .= "\n——";
        $text .= "\n$customer[name] (".$customer['usr_id'].")";
        $text .= "\n$customer[city]-$customer[state], $customer[country]";
        $text .= "\n$customer[ddi]$customer[phone]";
    }


    if (isset($ref->usr))
    {
        $text .= "\n——";
        $text .= "\nRef: ".$ref->usr['name'];
    }
    
    
    if (!$silent)
    {
        AddSMS(TELEGRAM_RUBINHO, $text);
        //AddSMS(TELEGRAM_TSGROUP, $text);

        if ($publ_id == 2) 
        {
            AddSMS(TELEGRAM_LELO, $text);
        }

        if ($publ_id == 4) 
        {
            addSMS(TELEGRAM_FIDCASH, $text);
            AddSMS(TELEGRAM_LELO, $text);
        }
    }
    
            
} else {
    echo "ops!";
}

