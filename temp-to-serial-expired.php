<?php 

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
error_reporting(E_ALL);
ini_set('display_errors', 'On');

//USES
require_once "AppProtectKey.php";
require_once "functions.php";
require_once "const.php";
require_once "db.php";


$timestamp = date('Y-m-d H:i:s');



$items =
    $ndb->items()
        ->where("serial IS NOT NULL AND seri_id IS NULL AND orders.approved IS NOT NULL AND DATE(orders.approved) < DATE_ADD(DATE(NOW()), INTERVAL -3 DAY)");


$last_mail = NULL;


foreach ($items as $item) {

    if ($last_mail != $item->orders["email"]) {

        $last_mail = $item->orders["email"];
        $mac_id    = generateRandomString(4);

        echo $mac_id;
        echo "<hr>";
    }


    echo $item->orders["email"]." - ".$item->orders["approved"]."<br>";

    $mp1 = 
        json_decode(
            curl_get_contents("http://r2.rfidle.com/protect/mp/order/".$item->orders["gid"])
        );

    $payment =
        end($mp1->payments);

    $mp2 = 
        json_decode(
            curl_get_contents("http://r2.rfidle.com/protect/mp/payment/".$payment->id)
        );


    echo "<br>".$payment->id;
    echo "<br>".$mp2->payer->first_name." ".$mp2->payer->last_name;

    echo "<hr>";


    $customer = 
        $ndb->cus()->insert(
            array(
                "name" => $mp2->payer->first_name." ".$mp2->payer->last_name,
                "email" => $last_mail,
                "phone" => $mp2->payer->phone->area_code.$mp2->payer->phone->number,
                "city" => "unknown",
                "state" => "KN",
                "ddi" => "55",
                "country" => "BR",
                "usr_id" => 4
            )
        );

    if ($customer) {

        echo "<br>CUSTOMER INSERTED!";

        $arr_installation =
            array(
                "cus_id"   => $customer["id"],
                "mac_id"   => $mac_id,
                "mac_name" => "AUTO",
                "appl_id"  => $item["appl_id"],
                "ip"       => $mp2->additional_info->ip_address,
            );

        var_dump($arr_installation);

        $ins = 
            $ndb->ins()->insert(
                $arr_installation
            );


        if ($ins) {

            echo "<br>INSTALLATION INSERTED!";
            //SERIAL
            $inst       = new AppProtectKey\inst($mac_id, $item["appl_id"]);
            $dlimit     = date('Y-m-d', strtotime("+1 year"));

            $gtid = $item->orders["gid"];
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
                        "info" => "MERCADOPAGO *AUTO\n#{$gtid}\n{$last_mail}",

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
        } else {

            echo "<br>";
            print_r($pdo->errorInfo());
        }  
    }
}
