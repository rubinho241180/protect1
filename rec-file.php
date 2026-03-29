<?php  


require_once "db.php";

$pdo = connect_pdo();

$qry = $pdo->query("select picture from rechist where id = $_GET[id]");

$row = $qry->fetch();

$pic = $row->picture;


echo '<img src="data:image/png;base64,'.$pic.'">';
?>

