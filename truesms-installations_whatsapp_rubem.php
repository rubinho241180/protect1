<?php 

error_reporting( E_ALL );

require_once "db.php";
$pdo = connect_pdo();


$timestamp = date('Y-m-d H:i:s');
$timestamp = date('H:i:s');


$sql = "select * from sms where status = 0 order by id";
$qry = $pdo->query($sql);
	
$results = array();

while ($row = $qry->fetch()) { 
	$text = urlencode($row->text);
	$results[] = file_get_contents("http://gateway.rfidle.com/sms/send?to=$row->target&text=$text&gateway=1s2u");
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
