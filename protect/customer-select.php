<?php 

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once "db.php";
require_once "const.php";
require_once "functions.php";

$ip = client_ip();
$logSubtitle = "logSubtitle";

try {

		$json = 
			array(
				"cus"=>
					array(
					),
				"ins"=>
					array(
					),
				"errors"=>
					array(
					),
				"get" =>	$_GET
			);

		$cus_email 	= $_GET["cus_email"];
		$mac_id 	= $_GET["mac_id"];
		$mac_name  	= $_GET["mac_name"];
		$app_id 	= $_GET["app_id"];


		addSMS(TELEGRAM_RUBINHO, $cus_email);

		if (!is_mail($cus_email)) {
			array_push(
				$json["errors"],

				array(
					"msg" => "Este email não é considerado válido.",
				)
			);
		}


		require_once "db.php";

		$pdo = connect_pdo();



		$sql  =
		"select 
		     i.mac_id,
		     i.appl_id,
		     (select count(*) from seri where ins_id = i.id) as serial_count, 
		     c.* 
		from 
		     ins i 
		         inner join 
		               cus c  on c.id = i.cus_id
		where 
		      i.mac_id = :mac_id and c.email <> :cus_email
		";


		$qry = $pdo->prepare($sql);
		$qry->execute(
			array(
				":mac_id" => $mac_id, 
				":cus_email" => $cus_email
			)
		);



		//SE A MAC JÁ FOI REGISTRADA COM OUTRO EMAIL...
		if ($qry->rowCount() > 0) {

			$row = $qry->fetch();

			$part = explode("@", $row->email);


			//if ($row->email != $cus_email) {
				array_push(
					$json["errors"],

					array(
						"msg" => "Este computador já foi registrado utilizando o email:\r\n".substr($part[0], 0, 2).str_pad("", strlen($part[0])-4, "*").substr($part[0], -2)."@".$part[1].".\r\n\r\n"."Informe o email acima para cotinuar com o registro.",
					)
				);
			//}
		} else {



			//VERIFICA O TOTAL DE SERIAL JÁ GERADOS PARA ESTA INSTALAÇÃO
			/*---PDO $qry = $pdo->query("
				select 
					count(*) as ser_count
				from
					serial s
				inner join
					ins i on i.id = s.ins_id
				where
					i.mac_id = '$mac_id' and
					i.app_id = $app_id
			");*/

			$sql = 
			"
				select 
					count(*) as ser_count
				from
					seri s
				inner join
					ins i on i.id = s.ins_id
				where
					i.mac_id = :mac_id and
					i.appl_id = :app_id
			";

			$qry = $pdo->prepare($sql);
			$qry->execute(
				array(
					":mac_id" => $mac_id, 
					":app_id" => $app_id
				)
			);


			$row = $qry->fetch();
			$json["ser_count"] = $row->ser_count;
			$json["maxdlimit"] = date('d/m/Y', strtotime("+12 hour"));






			//SELECIONA OS DADOS DO EMAIL JÁ CADASTRADO
			//---PDO $qry = $pdo->query("select * from cus where email = '$cus_email'");
			$qry = $pdo->prepare("select * from cus where email = :cus_email ORDER BY id LIMIT 1");
			$qry->execute(
				array(
					":cus_email" => $cus_email
				)
			);

			while ($row = $qry->fetch()) { 
				$cus_id = $row->id;

				array_push(
					$json["cus"],

					array(
						"id"=>$cus_id,
						"name"=>$row->name,
						"fname"=>explode(" ",$row->name)[0],
					)
				);
			}




			//SE JÁ EXISTE O CLIENTE, ENTÃO... INSERIR A INSTALAÇÃO!
			if ($qry->rowCount() > 0) {


				$logSubtitle = $cus_email." exists<br>  --=> installation-insert.php {$ip}";
				include "installation-insert.php";

			} else {
				$logSubtitle = $cus_email." IS NEW {$ip}";
			}

		}






}

//catch exception
catch(Exception $e) {

	$logg = 
		$ndb->log()->insert(
			array(
				"text" => "cus-sel-error {$ip}:<hr>".$e->getMessage(),
			)
		);

	addSMS(TELEGRAM_RUBINHO, "cus-sel-ERROR:\nhttp://r2.rfidle.com/protect/log/$logg[id]");
	exit;  
}


$logg = 
	$ndb->log()->insert(
		array(
			"text" => "cus-sel-json: {$ip}<br>$logSubtitle<hr>".json_encode($json, JSON_PRETTY_PRINT),
		)
	);

addSMS(TELEGRAM_RUBINHO, "cus-sel-json:\nhttp://r2.rfidle.com/protect/log/$logg[id]");


echo json_encode($json, JSON_PRETTY_PRINT);
