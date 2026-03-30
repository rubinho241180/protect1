<?php 

//var_dump($_REQUEST);

require_once "db.php";


$seri_id = trim($_GET['skey']);
$dist_id = isset($_GET['dist']) ? trim($_GET['dist']) : '';
// $appl_id = trim($_GET['appl']);
 //$dist_id = trim($_GET['dist']);

if ($dist_id == '') {
    $dist_id = 'TSI901';
}




$seri = $ndb->seri()->where("skey = '$seri_id'")->limit(1)->fetch();
$inst = $seri->ins;

$dist = $ndb->distribuition()->where("id = '$dist_id'")->limit(1)->fetch();
$cust = $inst->cus;

$json = array(
            'dis_name' => $dist["name"],
            'cus_name' => $cust["name"],
            'cus_phone' => $cust["ddi"].$cust["phone"],
            'cus_email' => $cust["email"],
            'tel_support' => $dist["tel_support"],
            'url_support' => $dist["url_support"]
        );

echo json_encode($json, JSON_PRETTY_PRINT);