<?php 

require_once "db.php";
require_once "const.php";
require_once "functions.php";

$timestamp = date('Y-m-d H:i:s');

$ip     = client_ip();

$osid   = isset($_POST["osid"])   ? $_POST["osid"]   : $ip;
//only 16 first characters
$osid   = substr($osid, 0, 16);

$dist   = isset($_POST["dist"])   ? $_POST["dist"]   : NULL;
$step   = isset($_POST["step"])   ? $_POST["step"]   : NULL;
$page   = isset($_POST["page"])   ? $_POST["page"]   : NULL;
$vers   = isset($_POST["vers"])   ? $_POST["vers"]   : NULL;
$comp   = isset($_POST["comp"])   ? $_POST["comp"]   : NULL;
$user   = isset($_POST["user"])   ? $_POST["user"]   : NULL;
$status = isset($_POST["status"]) ? $_POST["status"] : NULL;


if (($step == 'InitializeSetup') || ($step == 'InitializeUninstall')) {

    //find download
    $download    =
        $ndb->download()->where("ip = ?", $ip)->order("id DESC")->limit(1)->fetch();
    
    $download_id = 
        (!!$download) ? $download["id"] : NULL;   

    $setup      =
        $ndb->setup()->insert(
            array(
                "download_id"      => $download_id,
                "timestamp"        => $timestamp,
                "osid"             => $osid,
                "ip"               => $ip,
                "vers"             => $vers,
                "comp"             => $comp,
                "user"             => $user,
                "distribuition_id" => $dist,
                "step"             => $step,
                "page"             => $page,
            )
        );

    if ($setup) echo "inserted"; else echo "not inserted: .$pdo->errorInfo()";

}


$setup =
    $ndb->setup()->where("osid = ?", $osid)->order("id DESC")->limit(1)->fetch();


if ($setup) {

    //STEP
    if ($step) {
        $setup["step"]   = $step;
        $setup["page"]   = $page;
        $setup->update();
    }

    //PAGE
    if ($page) {
        $setup["page"]   = $page;
        $setup->update();
    }


    //STATUS
    if ($step == 'ssPostInstall')
        $status = 'INSTALLED';
    if ($step == 'ssDone')
        $status = 'INSTALLED';

    if ($step == 'usPostUninstall')
        $status = 'UNINSTALLED';
    if ($step == 'usDone')
        $status = 'UNINSTALLED';

    if ($status) {
        $setup["status"] = $status;
        $setup->update();
    }




    $dist         = $setup['distribuition_id'];
    $publisher_id = $setup->distribuition['publisher_id'];

    //NOTIFICATION
    if ($step == "InitializeSetup")
        $text = "💾⏳ {$dist} setup has started from {$ip}.";

    if ($step == "ssDone") 
        $text = "💾✅ {$dist} setup has installed successfully from {$ip}.";

    if ($step == "usDone") 
        $text = "💾🗑 {$dist} setup has uninstalled from {$ip}.";

    if ($status == 'ABORTED')
        $text = "💾⛔️ {$dist} setup has aborted from {$ip}.";


    if (isset($text)) {
        
        //addSMS(TELEGRAM_TSGROUP, $text);
        addSMS(TELEGRAM_RUBINHO, $text);

        //VJR
        if ($publisher_id == 2) {
            addSMS(TELEGRAM_LELO, $text);
        }

        //FID
        if ($publisher_id == 4) {
            addSMS(TELEGRAM_FIDCASH, $text);
            addSMS(TELEGRAM_LELO, $text);
        }
    }
    
    echo json_encode($_POST);    
} else {
    //echo "ops!\n".json_encode($_POST);
}



