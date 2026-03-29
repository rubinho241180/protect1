<?php 

try {


	require_once "db.php";
	require_once "const.php";
	require_once "functions.php";

	addSMS(TELEGRAM_RUBINHO, "cus-ins-PHP: START;");


	$json = 
		array(
			"customers"=>
				array(
				),
			"errors"=>
				array(
				),
			"parameters" =>	$_POST
		);

	$timestamp = date('Y-m-d H:i:s');
	$ip = client_ip();



	$name = utf8_encode(trim($_POST["name"]));
	$mail = trim($_POST["mail"]);
	$phon = only_numbers($_POST["phon"]);
	$city = utf8_encode(trim($_POST["city"]));
	//$city = trim($_POST["city"]);
	$stat = trim($_POST["stat"]);
	$ddi  = isset($_POST["ddi"]) ? intval(trim($_POST["ddi"])) : 0;
	$ctry = isset($_POST["ctry"]) ? trim($_POST["ctry"]) : "XX";


	$referral = isset($_POST["referral"]) ? intval(trim($_POST["referral"])) : NULL;

	$names = explode(' ',$name);

	$mac_id = $_POST["mac_id"];


	if (count($names) < 2) {
		array_push($json["errors"], array("msg"=>"Insira o seu nome completo.", "component"=>"edRegisterName"));
	} else {
		$name = "";
		foreach ($names as $key) {
			$name = trim($name)." ".$key;
		}
		$name = trim($name);
	}

	if (!is_mail($mail)) {
		array_push($json["errors"], array("msg"=>"Email inválido.", "component"=>"edRegisterEmail"));
	}

	if ((strlen($phon) < 10) || (strlen($phon) > 11)) {
		array_push($json["errors"], array("msg"=>"Telefone inválido.", "component"=>"edRegisterPhone"));
	}

	if (strlen($city) < 3) {
		array_push($json["errors"], array("msg"=>"Cidade inválida.", "component"=>"edRegisterCity"));
	}

	if (strlen($stat) != 2) {
		array_push($json["errors"], array("msg"=>"Estado inválido.", "component"=>"edRegisterState"));
	}



	if (count($json["errors"]) < 1) {
		require_once "db.php";


		//SE PHONE FOI CONFIRMADO, INSERE CLIENTE...
		//$sms_code = isset($_GET["sms_code"]) ? $_GET["sms_code"] ? NULL;

		

		//*** stopped if (isset($_POST["sms_code"])) {
		if (isset($_POST["mail"])) {
			
			//verifica se confirma o sms
			//**stopped $veri = $ndb->sms_code->where("mac_id = ? and phone = ? and code = ? and verified is null", array($mac_id, $ddi.$phon, $_POST["sms_code"]))->fetch();
			$veri = true;

			if ($veri) {

				//**stopped $veri["verified"] = $timestamp;
				//**stopped $veri->update();

				/*
				if (is_null($referral)) {

					//get last usr_id
					$cus = $ndb->cus()->where("referral_id is NULL")->order("id DESC")->limit(1)->fetch();
					//ATÉ 01-03-18, A PARTIR DESSA DATA VAI TUDO PRA LELO, BELLOW:
					
					$usr_id = ($cus["usr_id"] == 4) ? 3 : 4;
					$usr_id = 4;
				} 
				else {

					$usr_id = $referral;
				}
				*/


				/*
				** FIND REFERRAL
				*/
				$download = $ndb->download()->where("ip", client_ip())->order('id DESC')->limit(1)->fetch();

				if (($download) && ($download->referral["usr_id"] != NULL)) {
					$usr_id = $download->referral["usr_id"];
					$ref_id = $usr_id;
				} else {

					//get last usr_id
					$cus    = $ndb->cus()->where("(referral_id is NULL) AND (usr_id IS NOT NULL)")->order("id DESC")->limit(1)->fetch();
					//$usr_id = ($cus["usr_id"] == 4) ? 8 : 4;
					$usr_id = ($cus["usr_id"] == 4) ? 4 : 4;
					$usr_id = 1;
					$ref_id = NULL;
				}


				
				/*
				** INSERT CUSTOMER
				*/
				$pdo  = connect_pdo();
				$sql  = "insert into cus set name = :name, email = :mail, phone = :phon, city = :city, state = :stat, ddi = :ddi, country = :ctry, timestamp = :timestamp, phone_verified = 1, usr_id = :usr_id, referral_id = :ref_id";
				$stmt = $pdo->prepare($sql);
				$qry  = $stmt->execute(
							array(
								"name" => $name,
								"mail" => $mail,
								"phon" => $phon,
								"city" => $city,
								"stat" => $stat,
								"ctry" => $ctry,
								"ddi"  => $ddi,
								"timestamp"  => $timestamp,
								"usr_id" => $usr_id,
								"ref_id" => $ref_id,
							)
						);

				if ($qry) {
					$cus_id   = $pdo->lastInsertId(); 
					$mac_id   = $mac_id;
					$mac_name = $_POST["mac_name"];
					$app_id   = $_POST["app_id"];


					include "installation-insert.php";

				} else {
					array_push(
						$json["errors"],
						$pdo->errorInfo()
					);
				}
			}

			$json["verified"] = 
				array(
					"phone" => $veri != NULL, 
					"message" => "Este código não é válido."
				);


		} else {

			$full = $ddi.$phon; 
			$code = rand(100,999)."-".rand(100,999);
			
			//$code = "123-123";
			
			$text = urlencode("111-222");
			$sms  = file_get_contents("http://gateway.rfidle.com/sms/send?to=$full&text=$code&gateway=twilio");
			$jsms = json_decode($sms);

			//$jsms->sent = true;


			if ($jsms->sent) {
		
				//insere confirmação...
				$veri = $ndb->sms_code->where("mac_id = ? and verified is null", $mac_id)->fetch();

				if ($veri) {
					$veri["code"] = $code;
					$veri["phone"] = $full;
					$veri->update();
				} else {
					$ndb->sms_code->insert(
						array(
							"mac_id" => $mac_id,
							"phone"  => $full,
							"code" 	 => $code,
						)

					);
				}


			} else {


					foreach ($jsms->result as $value) {
						array_push(
							$json["errors"], 
							array(
								"msg"=>$value, 
								"component"=>"edRegisterPhone"
							)
						);
					}



			}




			$json["verified"] = array("phone" => false);
		}

	}


	/*while ($row = $qry->fetch()) { 
		array_push(
			$json["customers"],

			array(
				"id"		=>$row->id,
				//"name"	=>$row->name,
				//"email"	=>$row->email,
				//"phone"	=>$row->phone,
				//"city"	=>$row->city,
				//"state"	=>$row->state,
			)
		);
	}*/

	//$json["executed"] = $qry;

}

//catch exception
catch(Exception $e) {

	addSMS(TELEGRAM_RUBINHO, "cus-ins-error:\nEXCEPTION");
	addSMS(TELEGRAM_RUBINHO, "cus-ins-error:\nEXCEPTION: ".$e->getMessage());

	$logg = 
		$ndb->log()->insert(
			array(
				"text" => "cus-ins-error: {$ip}<hr>".$e->getMessage(),
			)
		);

	addSMS(TELEGRAM_RUBINHO, "cus-ins-error:\nhttp://r2.rfidle.com/protect/log/$logg[id]");
	exit;  
  
}


$logg = 
	$ndb->log()->insert(
		array(
			"text" => "cus-ins-json: {$ip}<hr>".json_encode($json, JSON_PRETTY_PRINT),
		)
	);

addSMS(TELEGRAM_RUBINHO, "cus-ins-json:\nhttp://r2.rfidle.com/protect/log/$logg[id]");

echo json_encode($json, JSON_PRETTY_PRINT);
