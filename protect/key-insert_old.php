<?php 

require_once "rijndael.php";


$cus_id = $_POST["cus_id"];
$mac_id = $_POST["mac_id"];
$app_id = $_POST["app_id"];
$inskey = strtolower($mac_id."-".$app_id);
$ins_id = $_POST["ins_id"];

$pkey = $_POST["pkey"];
$info = utf8_encode($_POST["info"]);

$par_id = $_POST["par_id"];


$pric = isset($_POST["pric"]) ? floatval($_POST["pric"]) : NULL;
$disc = isset($_POST["disc"]) ? floatval($_POST["disc"]) : NULL;
$liqu = isset($_POST["liqu"]) ? floatval($_POST["liqu"]) : NULL;
$prev = isset($_POST["prev"]) ? date('Y-m-d H:i:s', strtotime($_POST["prev"])) : NULL;


$type 	= hexdec(substr($pkey,  0, 2));
$dbuild	= hexdec(substr($pkey,  2, 5));
$dbuild	= str_pad($dbuild, 6, "0", STR_PAD_LEFT);

$dlimit = hexdec(substr($pkey,  7, 5));
if ($dlimit > 0) {
	$dlimit	= str_pad($dlimit, 6, "0", STR_PAD_LEFT);
	$day   	= substr($dlimit,  0, 2);
	$month 	= substr($dlimit,  2, 2);
	$year  	= substr($dlimit,  4, 2);
	$dlimit = date("Y-m-d", strtotime($year."-".$month."-".$day));
	$dlimit_sql = $dlimit;

	$dlimit = date("d-m-Y", strtotime($year."-".$month."-".$day));

} else {
	$dlimit_sql = null;
}

$ilimit = hexdec(substr($pkey, 14, 2));


$enc = AES_Rijndael_Encrypt($pkey, $inskey.":1", $inskey.":2");
$skey = strtoupper(bin2hex($enc));

require_once "db.php";
$pdo = connect_pdo();
$sql = 
"
	insert into 
		serial 
	set 
		ins_id = :ins_id,
		type = :type,
		dlimit = :dlimit_sql,
		ilimit = :ilimit,
		skey = :skey,
		info = :info,
		par_id = :par_id,
		price    = :pric,
		discount = :disc,
		liquid   = :liqu,
		prevision = :prev
";

$params = array(
				"ins_id" => $ins_id,
				//"cus_id" => $cus_id,
				//"mac_id" => $mac_id,
				//"app_id" => $app_id,
				"type" 	 => $type,
				"dlimit_sql" => $dlimit_sql,
				"ilimit" => $ilimit,
				"skey" 	 => $skey,
				"info" 	 => $info,
				"par_id"	 => ($par_id > 0) ? $par_id : NULL,
				"pric" 	 => $pric,
				"disc" 	 => $disc,
				"liqu" 	 => $liqu,
				"prev" 	 => $prev,
			);

//echo json_encode($params, JSON_PRETTY_PRINT);

//exit;

$stmt = $pdo->prepare($sql);
$qry  = $stmt->execute(
			$params
		);


//$qry = $pdo->exec($sql);


$json =
array(
	"ins_id"=>$ins_id,
	"cus_id"=>$cus_id,
	"mac_id"=>$mac_id,
	"app_id"=>$app_id,

	"pkey"=>$pkey,
	"type"=>$type,
	"dbuild"=>$dbuild,
	"dlimit"=>$dlimit,
	"ilimit"=>$ilimit,

	"skey"=>strtoupper($skey),
	"errors"=> array(),
	"sql"=>$sql,
);

if (!$qry) {
	array_push(
		$json["errors"],
		$pdo->errorInfo()
	);
} else {
	$json["inserted"] = $qry;
}


if (isset($_POST["formated"]))
	echo "<pre>";
echo json_encode($json, JSON_PRETTY_PRINT);

if (isset($_POST["formated"]))
	echo "</pre>";

