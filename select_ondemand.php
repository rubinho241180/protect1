<?php 

// Turn on output buffering with the gzhandler
//if ($_GET["gzip"]) {
//	ob_start('ob_gzhandler');
//}

header('Content-Type: application/json');


function dateDiff1($d1, $d2) {
  return round(abs(strtotime($d1)-strtotime($d2))/86400);
}

function dateDiff($start, $end) {
	$start_ts = $start;//strtotime($start);
	$end_ts = $end;//strtotime($end);
	$diff = $end_ts - $start_ts;
	return round($diff / 86400);
}
//sleep(1);

$curTime1 = microtime(true);



$json = 
	array(
		"version" => 
			array(
				"int"=>10500,
				"str"=>"1.5.0",
			),

		"partners" => 
			array(
			),
			
		"paymethod" => 
			array(
			),
			
		"terminals"=>
			array(
			),

		"parameters" =>	$_GET,
		"metrics" => array(),
	);

require_once "db.php";

$pdo = connect_pdo();

$qry = 
$pdo->query("
	select 
		i.*, 
		c.name as cus_name, 
		c.email as cus_email, 
		c.phone as cus_phone, 
		c.city as cus_city,
		c.state as cus_state,
		c.ddi as cus_ddi,
		c.country as cus_country,
		a.name as app_name,
		(select count(*) from ins where cus_id = c.id and id < i.id) as cus_ins_count,
		(select count(*) from ins where cus_id = c.id and mac_id = i.mac_id and id < i.id) as mac_ins_count,
		(select count(*) from ins where cus_id = c.id and app_id = i.app_id and id < i.id) as app_ins_count,
		v.major,
		v.minor,
		v.revision,
		o.name as os_name,
		o.arch as os_arch,
		o.vmaj as os_vmaj,
		o.vmin as os_vmin,
		o.vbui as os_vbui,
		o.smaj as os_smaj,
		o.smin as os_smin
	from 
		ins i 
	left join 
		cus c on c.id = i.cus_id 
	left join 
		app a on a.id = i.app_id
	left join 
		ver v on v.id = i.ver_id and v.app_id = i.app_id
	left join
		ins_os o on o.ins_id = i.id	
	where
		i.deleted = 0 /*and i.cus_id = 22*/
	order by
		i.id desc	
	LIMIT 100	
");

while ($row = $qry->fetch()) { 
	//echo $row->cus_name."<br>";
	$serials = array();

	$sql = 
	"select 
		s1.*,
    	DATEDIFF(s1.dlimit, DATE(s1.timestamp)) as diff,       
    	DATEDIFF(s1.dlimit, curdate()) as dleft,       
    	now() as now,
    	curdate() as currdate,
		p.name as par_name,
		(select id from serial where ser_id = s1.id LIMIT 1) as ser_recycled,
		(select ins_id from serial where ser_id = s1.id  LIMIT 1) as ser_recycled_ins_id,

		(select sum(value) from rechist where serial_id = s1.id) as rectota,
		(select count(id) from rechist where serial_id = s1.id and date < curdate() and confirmed is null) as recfail_count

	FROM
		serial s1 
	LEFT JOIN
		par p on p.id = s1.par_id	
	WHERE
		s1.ins_id = $row->id order by id desc";

	$qr2 =
	$pdo->query($sql);
	//$pdo->query("select * from serial where ins_id = $row->id order by id desc");

	while ($ro2 = $qr2->fetch()) { 

		$dlimit = ($ro2->dlimit == NULL) ? NULL : strtotime($ro2->dlimit);
		$time   = strtotime(date('Y-m-d 00:00:00', time()));

		$expired  = (($dlimit != NULL) && (dateDiff($time, $dlimit) < 0));
		$blocked  = $ro2->blocked == 1;
		$reseted  = $ro2->ser_recycled != NULL;
		$recycled = $ro2->ser_id != NULL;
		$recneed  = (($ro2->liquid > 1.00) && (dateDiff(strtotime($ro2->timestamp), $time) > 0) && ($ro2->rectota < $ro2->liquid)); 
		$recfail  = $ro2->recfail_count > 0; 
		$enabled  = ((!$expired) && (!$blocked) && (!$reseted) && (!$recneed) && (!$recfail));

		$sql = "select CONCAT(mac_id, '-', app_id) as ikey from ins where id = :id";
		//echo $sql;
		$qr3 = $pdo->prepare($sql);
		$qr3->execute(array("id" => $ro2->ser_recycled_ins_id));
		$ro3 = $qr3->fetch();

		if ($ro3) {
			$parent_key = $ro3->ikey;
		} else {
			$parent_key = NULL;
		}


		array_push(
			$serials, 
			array(
				"id" => $ro2->id,
				"type" => $ro2->type,
				"skey" => strtoupper($ro2->skey),
				"dbuild"=>date("d-m-Y", strtotime($ro2->timestamp)),
				"dlimit"=>date("d-m-Y", $dlimit),
				"time"	=>date("H:i:s", strtotime($ro2->timestamp)),
				"info"	=>/*base64_encode(*/$ro2->info/*)*/,
				"ilimit"	=>$ro2->ilimit,
				"enabled" => $enabled,
				//"enabled" => ($dlimit == NULL) || ($dlimit >= $time),
				//"_dlimit" => $dlimit,
				//"K_date" => $time,
				//"F_date" => date("d-m-Y H:i:s", $time),
				"diff" => $ro2->diff, //dateDiff($dlimit, $ro2->timestamp),
				"dleft" => $ro2->dleft, //dateDiff($dlimit, $time),
				"now" => $ro2->now,
				"currdate" => $ro2->currdate,
				"price" => $ro2->price,
				"discount" => $ro2->discount,
				"liquid" => $ro2->liquid,
				//"blocked" => $ro2->blocked == 1,
				"tags" => 
							array(
								"expired"  => $expired,
								"blocked"  => $blocked,
								"reseted"  => $reseted,
								"recycled" => $recycled,
								"recneed"  => $recneed,
								"recfail"  => $recfail,
								"automatic" => $ro2->auto == 1,
							),
				"recycled_id" => $ro2->ser_recycled,
				"recycled_ikey" => $parent_key,
				"partner" => 
							array(
								"id" => $ro2->par_id,
								"name" => $ro2->par_name,
							),
			)
		);
	}




	//INS_HIST
	$hist = array();

	$sql3 = 
	//"select * from ins_hist WHERE ins_id = $row->id order by id desc";
	"
	select text, timestamp from ins_hist WHERE ins_id = $row->id
	union
	select info, timestamp from serial WHERE ins_id = $row->id
	order by 2 desc";


	$qr3 =
	$pdo->query($sql3);
	
	while ($ro3 = $qr3->fetch()) { 
		$hist[] = 
		array(
			"resp" => 'BOT',//$ro3->resp,
			"text" => $ro3->text,
			"time" => $ro3->timestamp,
		);
	}







	$os = array();

	if ($row->os_arch != NULL) {
		$os[] = 
		array(
			"name" => $row->os_name,
			"arch" => $row->os_arch,
			"vmaj" => $row->os_vmaj,
			"vmin" => $row->os_vmin,
			"vbui" => $row->os_vbui,
			"smaj" => $row->os_smaj,
			"smin" => $row->os_smin,
		);
	}


	array_push(
		$json["terminals"],

		array(
			"id"		=>$row->id,
			"date"		=>date("d-m-Y", strtotime($row->timestamp)),
			"time"		=>date("H:i", strtotime($row->timestamp)),
			"cus_id"	=>$row->cus_id,
			"cus_name"	=>/*base64_encode(*/$row->cus_name/*)*/,
			"cus_email"	=>$row->cus_email,
			"cus_phone"	=>$row->cus_phone,
			"cus_ddi"	=>$row->cus_ddi,
			"cus_country"	=>$row->cus_country,
			"cus_city"	=>/*base64_encode(*/$row->cus_city/*)*/,
			"cus_state"	=>$row->cus_state,
			
			"mac_id"	=>$row->mac_id,
			"mac_name"	=>/*base64_encode(*/$row->mac_name/*)*/,

			"app_id"	=>$row->app_id,

			"cus_ins_count"	=>$row->cus_ins_count,
			"mac_ins_count"	=>$row->mac_ins_count,
			"app_ins_count"	=>$row->app_ins_count,


			"ver_id"	=> /*"~",//*/($row->ver_id != NULL) ? "~			".$row->major.".".$row->minor.".".$row->revision : NULL,
			"app_name"	=>$row->app_name,
			"keys"		=>$serials,
			"ACENROS"=> "isto é nós!",
			"os" => $os,
			"hist" => $hist,
		)
	);


}

//PARTNERS
$qry = 
$pdo->query("select * from par order by id");

while ($row = $qry->fetch()) { 
	array_push(
		$json["partners"], 
		array(
			"id" => $row->id,
			"name" => $row->name,
		)
	);
}



//PAYMETHOD
$qry = 
$pdo->query("select * from paymethod order by id");



while ($row = $qry->fetch()) { 
	array_push(
		$json["paymethod"], 
		array(
			"id" => $row->id,
			"name" => $row->name,
		)
	);
}


$json["metrics"]["elapsed"] = round(microtime(true) - $curTime1,3)/**1000*/; 


if (isset($_GET["formated"]))
	echo "<pre>";
echo json_encode($json, JSON_PRETTY_PRINT+JSON_UNESCAPED_UNICODE);

if (isset($_GET["formated"]))
	echo "</pre>";

//ob_end_flush(); /*<=--- SE RETIRAR ESSA LINHA O JSON NÃO É FORMATADO NO NAVEGADOR*/
