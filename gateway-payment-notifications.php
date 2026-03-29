<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);

header("HTTP/1.1 200 OK");
/*

function curl_get_contents($url) {
	$ch = curl_init();
	$timeout = 5;

	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);

	$data = curl_exec($ch);

	curl_close($ch);

	return $data;
}


*/


$topic = $_GET["topic"];
$id    = $_GET["id"];


//echo "gateway: YES";


//ADD SMS NOTIFICATION ************
require_once "functions.php";
include "NotORM.php";
require_once "db.php";


if ($topic == "merchant_order") {
	$json = curl_get_contents("https://api.mercadopago.com/merchant_orders/$id?access_token=APP_USR-6473599862616950-061613-9ad3cdc16658ca5cd06ff8573c947177__LD_LA__-208334493");
} else {
	$json = curl_get_contents("https://api.mercadopago.com/v1/payments/$id?access_token=APP_USR-6473599862616950-061613-9ad3cdc16658ca5cd06ff8573c947177__LD_LA__-208334493");
}

$ir = $ndb->apagar()->insert(
	array(
		"mp" => $id,
		"topic" => $topic,
		"data" => $json,
	)
);

exit;

if ($topic == "merchant_order") {
	$order = curl_get_contents("https://api.mercadopago.com/merchant_orders/$id?access_token=APP_USR-6473599862616950-061613-9ad3cdc16658ca5cd06ff8573c947177__LD_LA__-208334493");
	$order = json_decode($order);
	
	$title = $order->items[0]->title;

	$text =
	strtoupper($topic)."\n".
	"id:\n=> $id\n".
	"item:\n=> $title";

} else {


	$payment = curl_get_contents("https://api.mercadopago.com/v1/payments/$id?access_token=APP_USR-6473599862616950-061613-9ad3cdc16658ca5cd06ff8573c947177__LD_LA__-208334493");
	$payment = json_decode($payment);
	
	$order_id = $payment->order->id;

	$text =
	strtoupper($topic)."\n".
	//"PAY> ".strtoupper($payment->status)."\n".
	"id: $id\n".
	//"order:\n=> $order_id\n".
	"status: $payment->status\n".
	"detail: $payment->status_detail\n".
	"amount: $payment->transaction_amount";


	//SE JÁ FOI AOPROVADO, GERAR OS RECEBIMENTOS
	if ($payment->status == "approved") {

		$payment_date_approved = date('Y-m-d', strtotime($payment->date_approved));
		$payment_date_rettired = strtotime("next tuesday", strtotime($payment_date_approved));
		$money_release_date    = date('Y-m-d', strtotime($payment->money_release_date));

		//$pdo = connect_pdo();
		//$orm = new NotORM($pdo);

		$serials = $ndb->seri()->where("gtw_id = '$id'");

		foreach ($serials as $ser) {

			$ir = $ndb->rechist()->insert(
				array(
					"date"         => $money_release_date, //date("Y-m-d", $payment_date_rettired),
					"value" 	   => floatval($ser["price"]),
					"discount" 	   => floatval($ser["discount"]),
					//*** "liquid"       => floatval($ser["liquid"]),
					"seri_id"    => intval($ser["id"]),
					"recmethod_id" => 22,
					"timestamp"    => date('Y-m-d H:i:s'),
					"gtw_auto"     => 1,
				)
			);


			echo "<pre>";
			var_dump($ir);
			echo "</pre>";
		}


		//if (!$serials)
		//{
	
			$ir = $ndb->captured()->insert(
				array(
					"gtw_id"         => $id,
					"value" 	   => $payment->transaction_amount,
					"timestamp"    => date('Y-m-d H:i:s'),
					"email"     => $payment->payer->email,
				)
			);

		//}


	}

	addSMS("5581998636365,5581996066141", $text);
	//addSMS("5581982379937,5581998636365", $text);
}


//echo "<pre>";
//echo json_encode($payment, JSON_PRETTY_PRINT+JSON_UNESCAPED_UNICODE);
//echo "</pre>";



echo "<pre>".$text."</pre>";


//addSMS("81998636365", $text);
//SMS END; ************************


//http_response_code(400);


//$app = \Slim\Slim::getInstance();
//$app->response->setStatus(200);