<?php 

header('Content-Type: application/json');

error_reporting( E_ALL );
ini_set('display_errors', 1);


//require_once "curl.php";

require_once "db.php";
$pdo = connect_pdo();


$timestamp = date('Y-m-d H:i:s');
$timestamp = date('H:i:s');


$sql = "select * from sms where status = 1 order by id desc LIMIT 1";
$sql = "select * from sms where status = 0 order by id";
$qry = $pdo->query($sql);
	
$results = array();

while ($row = $qry->fetch()) { 
    $to   = $row->target;
    $text = $row->text;//urlencode($row->text)
    $text = urlencode($text);
    //$text = str_replace("\r\n", "\n", $text);
    //$text = str_replace("\n", "XYZ", $text);

    $results[] = 
        array(
            "to" => $row->target, 
            "pure" => $row->text,
            "text" => $text, 
            "result" => file_get_contents("http://gateway.rfidle.com/sms/send?to={$to}&text={$text}&gateway=telegram")
        );
    //$results[] = file_get_contents("http://www.truesistemas.com.br/gateway_wapp.php?target=$row->target&message=$text");
	$pdo->exec("update sms set status = 1 where id = $row->id");

}

$curTime1 = microtime(true);




echo json_encode($results, JSON_PRETTY_PRINT+JSON_UNESCAPED_UNICODE);


/*require_once "functions.php";

$text =
"
$timestamp
12345678-201
JAIME SILVA
55 81999331090
CARUARU-PE
";

addSMS("81998636365", urlencode($text));*/

//echo $result.': '.round(microtime(true) - $curTime1,3);
