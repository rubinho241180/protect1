<?php 

  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);
  error_reporting(E_ALL);

$time = date('H:i:s');
$timestamp = date('Y-m-d H:i:s');


/*---PDO $sql  = "
select 
	i.*
from
	ins i
where
	cus_id = $cus_id and 
	mac_id = '$mac_id' and 
	app_id = $app_id
";

$qry = $pdo->query($sql);
*/


require_once "db.php";
require_once "const.php";
require_once "rijndael.php";
require_once "functions.php";

//try {


$ip = client_ip();

$distribuition_id = isset($_REQUEST["distribuition"]) ? trim($_REQUEST["distribuition"]) : NULL;
$dist_str         = (!!$distribuition_id) ? " — ".$distribuition_id : "";

//find distribuition
$dist_db            = $ndb->distribuition()->where("id = ?", $distribuition_id)->fetch();
$distribuition_name = ($dist_db) ? $dist_db['name']         : NULL;
$publisher_id       = ($dist_db) ? $dist_db['publisher_id'] : 1;


$device_data      = isset($_REQUEST["d"]) ? $_REQUEST["d"] : NULL;

if ($device_data)
{
	$ikey = $mac_id . '-' . $app_id;
	
	//$device_data = pack("H*", $device_data);

	//DECRYPT
	/*
	$device_data = "pre;"."\n".

		
		AES_Rijndael_Decrypt(
			$device_data, 
			$ikey.":1", 
			$ikey.":2"
		) 

		. "\n".";pos";
		*/
}



$sql  = "
select 
	i.*
from
	ins i
where
	cus_id = :cus_id and 
	mac_id = :mac_id and 
	appl_id = :app_id
";

$qry = $pdo->prepare($sql);
$qry->execute(
	array(
		":cus_id" => $cus_id, 
		":mac_id" => $mac_id, 
		":app_id" => $app_id
	)
);



while ($row = $qry->fetch()) { 
	array_push(
		$json["ins"],

		array(
			"mac_id"=>$row->mac_id,
			"app_id"=>$row->appl_id,
		)
	);
}

$json["ins_length"] = $qry->rowCount();

if ($qry->rowCount() == 0) {

	//get deploy
	$deploy =
		$ndb->deploy()->where("ip = ?", $ip)->order("id DESC")->limit(1)->fetch();

	$deploy_id =
		(!!$deploy)	? $deploy["id"] : NULL;

	$sql = "insert into ins set deploy_id = :deploy_id, cus_id = :cus_id, mac_id = :mac_id, mac_name = :mac_name, appl_id = :app_id, distribuition_id = :dist_id, timestamp = :timestamp, ip = :ip, data = :data";
	$sta = $pdo->prepare($sql);
	$qry = $sta->execute(
				array(
					"deploy_id" => $deploy_id,
					"cus_id" => $cus_id,
					"mac_id" => $mac_id,
					"mac_name" => $mac_name,
					"app_id" => $app_id,
					"dist_id" => $distribuition_id,
					"timestamp" => $timestamp,
					"ip" => client_ip(),
					"data" => "d:".$device_data.":d",
				)
		   );

	//----$json["ins_sql"] = $sql;
	if (!$qry) {
		array_push(
			$json["errors"],
			$pdo->errorInfo()
		);
	} else {

		$ins_id = $pdo->lastInsertId();
		$json["ins_inserted"] = $qry;

		//INSERT OS VERSION
		if (isset($_GET["arch"])) {

			$name = base64_decode($_GET["name"]);
			$arch = $_GET["arch"];
			$vmaj = $_GET["vmaj"];
			$vmin = $_GET["vmin"];
			$vbui = $_GET["vbui"];
			$smaj = $_GET["smaj"];
			$smin = $_GET["smin"];

			$sql = "insert into ins_os set ins_id = :ins_id, name = :name, arch = :arch, vmaj = :vmaj, vmin = :vmin, vbui = :vbui, smaj = :smaj, smin = :smin, timestamp = :timestamp";
			$sta = $pdo->prepare($sql);
			$qry = $sta->execute(
						array(
							"ins_id" => $ins_id,
							"name" => $name,
							"arch" => $arch,
							"vmaj" => $vmaj,
							"vmin" => $vmin,
							"vbui" => $vbui,
							"smaj" => $smaj,
							"smin" => $smin,
							"timestamp" => $timestamp,
						)
				   );
		}


		//ADD SMS NOTIFICATION ************


		$sql = "
		select
			c.name,
			c.phone,
			c.city,
			c.state,
			c.ddi,
			c.country,
			c.usr_id,
			(select count(1) from ins where cus_id = c.id) as ins_count
		from
			cus c
		inner join 
			ins i on i.cus_id = c.id
		where
			i.id = :ins_id	
		";


		$qry = $pdo->prepare($sql);
		$qry->execute(
			array(
				":ins_id" => $ins_id
			)
		);



		$cus = $qry->fetch();
		$key = $mac_id."-".$app_id;// . $dist_str;
		//$key_with_dist = $key . $dist_str;
		$ip  = client_ip();

		$is_new_str = ($cus->ins_count == 1) ? "🎯🎯\n" : "";
		
		$text  = $is_new_str;
		$text .= "{$key}{$dist_str}\n";
		$text .= "$cus->name"." *".$cus->ins_count."(".$cus->usr_id.")\n";
		$text .= "$cus->city-$cus->state, $cus->country\n";

		$text_with_link  = $text;
		$text_with_link .= "r2.rfidle.com/chat/%s/{$ins_id}/".$cus->ddi.$cus->phone."\n";

		$text .= "$time from $ip";
		$text_with_link .= "$time from $ip";








		//STAFF
		if ($publisher_id == 1) {
			//addSMS(TELEGRAM_LELO   , sprintf($text_with_link, 4));
			//addSMS(TELEGRAM_FLAVIA , sprintf($text_with_link, 4));
			//addSMS(TELEGRAM_TSGROUP, sprintf($text_with_link, 2));
			addSMS(TELEGRAM_RUBINHO, sprintf($text_with_link, 1));
			addSMS(TELEGRAM_SAMARA, sprintf($text_with_link, 11));
			//addSMS(TELEGRAM_RUBEM  , sprintf($text_with_link, 2));
		} else {

			//FIDCASH
			if ($publisher_id == 4)	
			addSMS(TELEGRAM_FIDCASH, sprintf($text_with_link, 9));

			//addSMS(TELEGRAM_LELO   , $text);
			//addSMS(TELEGRAM_FLAVIA , $text);
			//addSMS(TELEGRAM_TSGROUP, $text);
			addSMS(TELEGRAM_RUBINHO, $text);
			//addSMS(TELEGRAM_RUBEM  , $text);
		}

		
		//addSMS("-1001385929252" /*TS - Privated*/, $text);
		//addSMS('1087969787' /*my*/, $text);


	} 




} else {


		$sql  = "
		UPDATE 
			ins
		SET
		    data = :data
		WHERE
			cus_id = :cus_id and 
			mac_id = :mac_id and 
			appl_id = :app_id
		";

		//$sql = "UPDATE data FROM ins WHERE "

		$qry = $pdo->prepare($sql);
		$qry->execute(
			array(
				":data"   => $device_data, 
				":cus_id" => $cus_id, 
				":mac_id" => $mac_id, 
				":app_id" => $app_id
			)
		);


}



//}

//catch exception
//catch(Exception $e) {

//	addSMS(TELEGRAM_RUBINHO, "ins-insert: ".$e->getMessage());
  
//}

//echo "distribuition_id: ". $distribuition_id;