<?php 

$id = $_POST["id"];
$par_id = $_POST["par_id"];

require_once "db.php";
$pdo = connect_pdo();
$sql = "update seri set par_id = :par_id where id = :id";

$params = array(
				"id" => $id,
				"par_id" => ($par_id > 0) ? $par_id : NULL,
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
	"updated" => $qry,
	"errors" => array(),
);

if (isset($_POST["formated"]))
	echo "<pre>";
echo json_encode($json, JSON_PRETTY_PRINT);

if (isset($_POST["formated"]))
	echo "</pre>";

