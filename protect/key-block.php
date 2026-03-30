<?php 

$id = $_POST["id"];

require_once "db.php";
require_once "const.php";

$timestamp = date('Y-m-d H:i:s');


$pdo = connect_pdo();
$sql = "update seri set _v1_blocked = 1, blocked_at = :timestamp where id = :id";

$params = array(
        //"disabled" => DISABLED_BLOCKED,
        "id" => $id,
        'timestamp' => $timestamp,
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
	"inserted" => $qry,
	"errors" => array(),
);

if (isset($_POST["formated"]))
	echo "<pre>";
echo json_encode($json, JSON_PRETTY_PRINT);

if (isset($_POST["formated"]))
	echo "</pre>";

