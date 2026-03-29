<?php 

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header("HTTP/1.1 200 OK");


$topic = $_GET["topic"];
$id    = $_GET["id"];


//USES
require_once "functions.php";
require_once "const.php";
require_once "mp.php";
require_once "db.php";
require_once "email-function.php";

var_dump($_GET);
echo '<hr>';


if ($topic == "merchant_order") {

    $mp = MP::getOrder($id);

    if (gettype($mp->status) == "integer") {
        die("Fail: ".$mp->status.".");
    }

    //APAGAR
    $ndb->apagar()->insert(
        array(
            "mp"    => $id,
            "topic" => $topic,
            "data"  => json_encode($mp),
        )
    );


    if (!!$mp->payer->email) {

        //$ipadd = "ip: null"; 
        $payer = $mp->payer;
    } else {

        $pp    = MP::getPayment($mp->payments[0]->id);
        //$ipadd = "ip: ".$pp->additional_info->ip_address; 
        $payer = $pp->payer;
    }


    $email = (!!$payer->email) 
        ? $payer->email 
        : NULL;
    

    //SELECT DB ORDER
    $order = 
        $ndb->orders()->where("gid", $id)->fetch();


    //IF ORDER NOT EXISTS IN DB, INSERT
    if (!$order) {

        //ADD ORDER
        $order = $ndb->orders()->insert(
            array(
                "gid"          => $id,
                "email"        => $email,
                "created"      => date('Y-m-d H:i:s', strtotime($mp->date_created)),
                "status"       => $mp->status,
                "order_status" => "initial",
                "processed"    => 0,
            )
        );

        //ADD ITEMS
        foreach ($mp->items as $item) {
            
            $inserted = 
                $ndb->items()->insert(
                    array(
                        "orders_id"  => $order["id"],
                        "appl_id"    => $item->id,
                        "appl_title" => $item->title,
                        "quantity"   => $item->quantity,
                        "price"      => $item->unit_price,
                        "utilized"   => NULL,
                    )
                );

            if (!$inserted) {
                echo "item insert fail".$order["id"]."<br>";
                var_dump($item);
                echo "<hr>";
                var_dump($pdo->errorInfo());
                echo "<hr>";
            }
        }
    }


    //IS STATUS IS CHANGED
    if ($order["order_status"] != $mp->order_status) {

        $order["email"]        = $email;
        $order["order_status"] = $mp->order_status;
        $order->update();

        //IF PAID
        if ($mp->order_status == "paid") {

            //FIND DATE_APPROVED
            foreach ($mp->payments as $payment) {
                
                if ($payment->status == "approved") {

                    //$pp = MP::getPayment($payment->id);

                    $order["approved"] = date('Y-m-d H:i:s', strtotime($payment->date_approved));
                    $order->update();


                    //INSERT PAYMENTS
                    $ndb->payments()->insert(
                        array(
                            "orders_id" => $order["id"],
                            "gid"       => $payment->id,
                            "value"     => $payment->transaction_amount,
                            "fee"       => ($payment->transaction_amount*4.99) / 100,
                        )
                    );
                }
            }



            //UPDATE BUILD T-SERIAL
            foreach ($ndb->items()->where("orders_id", $order["id"]) as $item) {

                $item["serial"] = "T".strtoupper(randomStr(31));
                $item->update();


                //IF NOT PREVIOUSLY UTILIZED, SEND EMAIL
                if ($item["utilized"] == NULL){

                    $params = 
                        array(
                            "from"     => "truesistemas@rfidle.com",
                            "reply_to" => "suporte@truesistemas.com.br",
                            "to"       => $email,
                            "bcc"      => "jaime120738@gmail.com",
                            "subject"  => "Serial ".$item["appl_title"],
                        );

                    $fields = 
                        array(
                            "{productName}" => $item["appl_title"],
                            "{downloadUrl}" => "http://download.rfidle.com/protect/download?ref=TSI901",
                            "{serialKey}"   => $item["serial"],
                            "{sectorName}"  => $item["appl_title"],
                            "{companySite}" => "www.mrsender.com.br",
                        );

                    echo SendMAIL_HTML($params, "buy.html", $fields);
                }
            }

            //SEND SMS - VENDA REALIZADA
            $txt = 
                "🤑🤑🤑\n".
                "VENDA REALIZADA!\n".
                "{$mp->id}\n".
                "{$mp->payments[0]->id}\n".
                "{$payer->email}\n".
                //"{$ipadd}\n".
                "R$".number_format($mp->total_amount, 2, ",", ".");

            addSMS(TELEGRAM_TSGROUP, $txt);

        } else {

            //SEND SMS - ORDER_STATUS
            $txt = 
                "🤞🏻🤞🏻🤞🏻\n".
                "{$mp->order_status}\n".
                "{$mp->id}\n".
                "{$mp->payments[0]->id}\n".
                "{$payer->email}\n".
                //"{$ipadd}\n".
                "R$".number_format($mp->total_amount, 2, ",", ".");

            addSMS(TELEGRAM_TSGROUP, $txt);

            echo $mp->order_status;
        }
    
    } else {
        echo "nothing to do.";
    }
    
}


//1730487278

//1730600986
//1730566993

//1737972622    