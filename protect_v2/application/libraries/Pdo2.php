<?php 

defined('BASEPATH') OR exit('No direct script access allowed');

class Pdo2 {
	public function __construct() {

		$CI = & get_instance();
		if (!isset($CI->pdo2)) {
			$CI->pdo2 = $this->pdo_connect();

		}

	}


	public function pdo_connect() {


		//GET TIMEZONE OFFSET AND SYNCHRONIZE BETWEEN PHP AND MYSQL
		$dt = new DateTime();
		$offset = $dt->format("P");
		$offset = "-03:00";


			return new PDO("mysql:host=localhost;dbname=r2;charset=utf8", "r2_read", "Xyz@master321321",

					array(
						//PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT,
		                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, 
		                
		                PDO::ATTR_PERSISTENT => false,
		                PDO::ATTR_EMULATE_PREPARES => false,
		                //PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
		                PDO::MYSQL_ATTR_INIT_COMMAND => "SET time_zone = '$offset'",

		                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,

		            )

				);

		//$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);

		
	   	if(!$db){
	       die('Erro ao criar a conexão');
	   	}	


	   //	require_once "NotORM.php";
		return $db;

	}

	public function mypdo()
	{
		$CI = & get_instance();
		
		if (!isset($CI->pdo2)) {
			$CI->pdo2 = $this->pdo_connect();

		}

		return $CI->pdo2;
	}
}