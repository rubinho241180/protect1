<?php 

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

define("TIMEZONE", "America/Recife");
date_default_timezone_set(TIMEZONE);


require_once "db.php";
require_once "const.php";
require_once "functions.php";

$id         = isset($_GET['id']) ? $_GET['id'] : -1;
$hash       = isset($_GET['hash']) ? $_GET['hash'] :  NULL;
$ip         = client_ip() ;
$progress   = isset($_GET['progress']) ? $_GET['progress'] : NULL;
$status     = isset($_GET['status'  ]) ? $_GET['status'  ] : NULL;
$screen_x   = isset($_GET['screen_x']) ? $_GET['screen_x'] : NULL;
$screen_y   = isset($_GET['screen_y']) ? $_GET['screen_y'] : NULL;
//$browser_id = isset($_COOKIE['browser_id']) ? $_COOKIE['browser_id'] : NULL;

$download = $ndb->download()->where('id = ? AND hash = ?', array($id, $hash))->fetch();

if ($download) {

    $download['screen_x']  = $screen_x;
    $download['screen_y']  = $screen_y;

    if ($progress) {

        $download['progress'] = $progress;
        $download->update();
    }



    if ($status) {

        $download['status']    = $status;
        $download['status_at'] = date('Y-m-d H:i:s');
        $download->update();

        $referral_usr_id = 
            $download->referral["usr_id"];

        $distribuition_id = 
            $download->referral->distribuition['id'];

        $publisher_id = 
            $download->referral->distribuition['publisher_id'];

        /*
        **  NOTIFICATION
        */
        if ($status == 'CANCELED')
            $text = "⬇⛔️ {$distribuition_id} download ($hash) has canceled from {$ip}."; else
        if ($status == 'FINISHED')
            $text = "⬇✅ {$distribuition_id} download ($hash) has completed from {$ip}.";

        //AddSMS(TELEGRAM_TSGROUP, $text);
        AddSMS(TELEGRAM_RUBINHO, $text);

        if ($referral_usr_id == 4) 
        {
            AddSMS(TELEGRAM_LELO, $text);
        }

        if ($publisher_id == 2) 
        {
            AddSMS(TELEGRAM_LELO, $text);
        }

        if ($publisher_id == 4) 
        {
            addSMS(TELEGRAM_FIDCASH, $text);
            AddSMS(TELEGRAM_LELO, $text);
        }
    }
}



$json = $_GET;

//$json['browser_id'] = $browser_id;

echo json_encode($json);