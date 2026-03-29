<?php 

error_reporting(E_ALL);

// Same as error_reporting(E_ALL);
ini_set('error_reporting', E_ALL);




$rec_id = $_POST["rec_id"];
$timestamp = date('Y-m-d H:i:s');

require_once "db.php";
$pdo = connect_pdo();
$sql = 
"
	update 
		rechist 
	set 
		confirmed = :confirmed
	where 
		id = :rec_id
";


$stmt = $pdo->prepare($sql);
$qry  = 
$stmt->execute(
	array(
		"rec_id" => $rec_id,
		"confirmed" => $timestamp,
	)
);


$json =
array(
	"updated" => $qry,
	"errors" => array(),
	"post" => $_POST,
);

if (!$qry) {
	array_push(
		$json["errors"],
		$pdo->errorInfo()
	);
}


if (isset($_POST["formated"]))
	echo "<pre>";
echo json_encode($json, JSON_PRETTY_PRINT);

if (isset($_POST["formated"]))
	echo "</pre>";

