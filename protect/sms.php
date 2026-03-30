<?php 

header('Content-Type: application/json');
error_reporting( E_ALL );

require_once "functions.php";

$id   = isset($_GET['id'  ]) ? $_GET['id'  ] : 0;
$from = '558137210661';
$to   = isset($_GET['to'  ]) ? $_GET['to'  ] : '5581998636365';
$text = isset($_GET['text']) ? urlencode($_GET['text']) : 'Teste!!!';



$tim1 =  microtime(true); 

//$resu = curl_get_contents("http://smsmkt.pro/sms/api?action=send-sms&api_key=Y3JvYmluOiQyeSQxMCRvelFuSm1wdTRrb2VDelVTUkhjakV1VFg1LjVIUUZoaGVnZy53Y2svN1ZLU3QuN3FaeTAzNg==&to={$to}&from={$from}&sms={$text}&response=json");
//$resu = file_get_contents("http://smsmkt.pro/sms/api?action=send-sms&api_key=Y3JvYmluOiQyeSQxMCRvelFuSm1wdTRrb2VDelVTUkhjakV1VFg1LjVIUUZoaGVnZy53Y2svN1ZLU3QuN3FaeTAzNg==&to={$to}&from={$from}&sms={$text}&response=json");
$resu = 'Sent!!!';


$json = 
	array(
		"id"   => $id,
		"to"   => $to,
		"text" => $text,
		"time" => round(microtime(true) - $tim1, 3),
		"json" => $resu,
    "cred" => number_format(rand(1,1000000), 0, ",", "."),
	); 



//sleep(rand(1, 3));

echo json_encode($json, JSON_PRETTY_PRINT+JSON_UNESCAPED_UNICODE);
