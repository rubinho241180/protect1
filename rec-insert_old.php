<?php 

error_reporting(E_ALL);

// Same as error_reporting(E_ALL);
ini_set('error_reporting', E_ALL);




$key_id = $_POST["key_id"];
$met_id = $_POST["met_id"];
$val    = $_POST["val"];
$dis    = $_POST["dis"];
$liq    = $_POST["liq"];
$dat    = date('Y-m-d H:i:s', strtotime($_POST["dat"]));

$timestamp = date('Y-m-d H:i:s');

require_once "db.php";
$pdo = connect_pdo();
$sql = 
"
	insert into 
		rechist 
	set 
		date      = :dat,
		value     = :val,
		discount  = :dis,
		liquid    = :liq,
		serial_id = :key_id,
		recmethod_id = :method_id,
		timestamp = :timestamp
";


$stmt = $pdo->prepare($sql);
$qry  = 
$stmt->execute(
	array(
		"dat" => $dat,
		"val" => $val,
		"dis" => $dis,
		"liq" => $liq,
		"key_id" => $key_id,
		"method_id" => $met_id,
		"timestamp" => $timestamp,
	)
);


$json =
array(
	"inserted" => $qry,
	"errors" => array(),
	"post" => $_POST,
);

if (!$qry) {
	array_push(
		$json["errors"],
		$pdo->errorInfo()
	);
} else {
	$json["inserted_id"] = $pdo->lastInsertId();
}


if (isset($_POST["formated"]))
	echo "<pre>";
echo json_encode($json, JSON_PRETTY_PRINT);

if (isset($_POST["formated"]))
	echo "</pre>";

