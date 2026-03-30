<?php 

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//USES
require_once "AppProtectKey.php";
require_once "functions.php";
require_once "const.php";
require_once "db.php";


$timestamp = date('Y-m-d H:i:s');
$mac_id    = $_GET["mac_id"];
$app_id    = $_GET["app_id"];
$temp      = $_GET["temp"];

//var_dump($_GET);

$item = $ndb->items()->where("appl_id = ? AND serial = ? AND seri_id IS NULL", array($app_id, $temp) )->fetch();


if (!$item) {

    $resp = array(
            "serial" => array(
                "errors" => ["Invalid Serial."],
            )
    );

} else {
    

    $inst       = new AppProtectKey\inst($mac_id, $app_id);
    //$timestamp  = date('Y-m-d H:i:s');
    $dlimit     = date('Y-m-d', strtotime("+1 year"));

    $gtid = $item->orders["gid"];
    $mail = $item->orders["email"];
    $pric = $item["price"];


    //BUILD AND INSERT SERIAL
    $json = 
        $inst->serial->insert(
            array(
                "type" => 53, 
                "subt" => 1, 
                "dlim" => $dlimit,
                "ilim" => $item["quantity"],
                "auto" => 1,
                "info" => "MERCADOPAGO\n#{$gtid}\n{$mail}",

                "gateawy_id" => $gtid,


                "price"      => $pric,
                "discount"   => 0,

                'flag' => FLAG_NEW,
            )
        ); 

    $item["seri_id" ] = $json["id"];
    $item["utilized"] = $timestamp;
    $item->update();


    //INSERT PAYMENT
    foreach ($item->orders->payments() as $payment) {

        $value = $payment['value'];
        $fee   = $payment['fee'];

        $ndb->rechist()->insert(
            array(
                "seri_id"      => $json["id"],
                "date"         => date('Y-m-d', strtotime('+1 day')),
                "value"        => $value,
                "discount"     => 0,
                "gateway_fee"  => $fee,
                "recmethod_id" => 22,
                "timestamp"    => $timestamp,
                "gtw_auto"     => 1,
            )
        );
    }


    /*
    //SPLIT TO COPRODUCERS
    if ($liqu != NULL) {

        //GERA AS COMISSÕES DOS CO-PRODUTORES
        $produtores = $ndb->appl_prod()->where("appl_id = ?", $app_id);

        foreach ($produtores as $appl_prod) {
            
            $sale_valu = ($sal_valu != NULL) ? $sal_valu : 0;
            $liqu_prod = $liqu - $sale_valu - $payment_fee;

            $ndb->seri_prod()->insert(
                array(
                    "seri_id" => $inserted_seri_id,
                    "prod_id" => $appl_prod["prod_id"],
                    "perc"      => $appl_prod["perc"],
                    "valu"      => ($liqu_prod*$appl_prod["perc"])/100,
                )
            );

        }
    }
    */



    //remove o id pra não expor uma informação sensível
    unset($json["id"]);

    $resp = array(
        "email" => $item->orders["email"],
        "serial" => $json,
    );
}

echo json_encode($resp, JSON_PRETTY_PRINT+JSON_UNESCAPED_UNICODE);
