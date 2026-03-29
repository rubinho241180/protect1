<style type="text/css">
.yes {
    background-color: lime;
}
.no {
    background-color: yellow;
}

.refunded,.charged_back {
    opacity: .5;
}
</style>

<?php 



    $i = 0;
    $last_date = "";


function showList($offset) {

    global $i;
    global $last_date;

    require_once "const.php";
    require_once "functions.php";
    include "db.php";

    //echo "<h1>$offset</h1>";


    $balances = curl_get_contents("https://api.mercadopago.com/v1/balance/history?access_token=".MP_ACCESS_TOKEN."&range=date_created&begin_date=2017-07-15T00:00:00Z&end_date=2017-10-30T23:59:00Z&offset=$offset");
    $balances = json_decode($balances);

    $paging = $balances->paging;


    foreach ($balances->results as $key => $balance) {

        if (($balance->source->type == "payment") && (count($balance->fee_details) == 1)) {

            $ser1 = $ndb->seri("gtw_id = ?", $balance->source->id)->fetch();
            $ser2 = $ndb->seri("info LIKE ?", "%".$balance->source->id."%")->fetch();
            



            if (($ser1) || ($ser2)) {
             //   $payment = NULL;
             //   echo '<pre class="yes">';
            } else {
                if ($last_date != date('d-m-Y', strtotime($balance->date_created))) {
                    $last_date = date('d-m-Y', strtotime($balance->date_created));
                    echo "<h2>".$last_date."</h2>";
                }

                $payment = curl_get_contents("https://api.mercadopago.com/v1/payments/{$balance->source->id}?access_token=".MP_ACCESS_TOKEN);
                $payment = json_decode($payment);
                echo '<pre class="no '.$payment->status.'">';

                            $i++;
                            echo "<h3>#".$i."</h3>";

                            echo json_encode($balance, JSON_PRETTY_PRINT+JSON_UNESCAPED_UNICODE);
                            
                            if (isset($payment)) {
                                echo "<h3>".$payment->status."</h3>";
                            }
                            
                            echo "</pre>";
                            echo "<hr>";

            }

        }
    }


    if ($paging->offset < $paging->total) {
        showList($paging->offset+30);
    }

}

showList(0);