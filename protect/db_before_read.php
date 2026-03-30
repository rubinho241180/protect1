<?php



function connect_pdo() {

	//GET TIMEZONE OFFSET AND SYNCHRONIZE BETWEEN PHP AND MYSQL
	$dt = new DateTime();
	$offset = $dt->format("P");



	$db = new PDO("mysql:host=localhost;dbname=r2;charset=utf8", "r2_user", "Xyz461300",

			array(
				PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT,
                //PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, 
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
