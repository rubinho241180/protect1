<?php 

header('Content-Type: application/json');

$macid = strtolower( $_GET["macid"] );
$email = strtolower( $_GET["email"] );
$passw = strtolower( $_GET["passw"] );


require_once "db.php";

$usr = $ndb->usr()->where("email = ? and password = ?", array($email, md5($passw) ))->fetch();

if ($usr) {

	$per = $usr->usr_permissions()->fetch();// $ndb->usr_permissions()->where("usr_id = ?", $usr["id"])->fetch();

	$usr["session_id"] = md5($email.$passw.$macid.rand(1, 1000000000));
	$usr["session_timestamp"] = date('Y-m-d H:i:s');
	$usr->update();

	$usr["per"] = 
		array(
			"installations" => $per["installations"] == 1,
			"sales" => $per["sales"] == 1,
			"applications" => $per["applications"] == 1,
		);

	$json = 
		array(
			//"session" => ,
			"get" => $_GET,
			"usr" => $usr,
		);

} else {

	$json = 
		array(
			"get" => $_GET,
		);

}




//if (isset($_GET["formated"]))
//	echo "<pre>";
echo json_encode($json, JSON_PRETTY_PRINT+JSON_UNESCAPED_UNICODE);

//if (isset($_GET["formated"]))
//	echo "</pre>";

