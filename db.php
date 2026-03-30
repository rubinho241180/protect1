<?php



function connect_pdo2() {

	//GET TIMEZONE OFFSET AND SYNCHRONIZE BETWEEN PHP AND MYSQL
	$dt = new DateTime();
	$offset = $dt->format("P");
	$offset = "-03:00";

	$host = getenv('DB_HOST');
    $dbname = getenv('DB_NAME');
    $user = getenv('DB_USER');
    $pass = getenv('DB_PASS');


    //$db = new PDO("mysql:host=localhost;dbname=gateway_sms;charset=utf8", "rfidle", "Xyz461300",
    $db = new PDO("mysql:host=$host;dbname=bulkfy;charset=utf8", "$user", "$pass",

			array(
				//PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, 
                PDO::ATTR_PERSISTENT => false,
                PDO::ATTR_EMULATE_PREPARES => false,
                //PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET time_zone = '$offset'",

            )

		);

	$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);

	//$db->exec("SET time_zone='$offset';");

	//$db->exec("set names utf8");
	//$db->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, "SET NAMES 'utf8' COLLATE 'utf8_general_ci'");
	
	/*$db-> exec("SET CHARACTER SET utf8");
	$db-> exec("SET COLLATE SET utf8_unicode_ci");*/
	
	
   	if(!$db){
       die('Erro ao criar a conexão');
   	}	



   	return $db;
}


require_once "../r2.rfidle.com/protect/NotORM.php";

$pdo = connect_pdo2();
$ndb2 = new NotORM($pdo);

function ndb2() {
	return new NotORM(connect_pdo2());
}
