<?php 
//header("Content-Type: application/json;charset=utf-8");
//header('Content-Type: text/html; charset=utf-8');
header('Content-Type: application/json');
//ob_start('ob_gzhandler');


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



require_once "db.php";




//SESSION
$usr = $ndb->usr()->where("session_id = ?", isset($_GET["session_id"]) ? $_GET["session_id"] : "xxx")->fetch();


// $andMail = 
// 	(isset($_GET["email"])) ? " AND c.email IN ('".  str_replace(",", "','", $_GET["email"]) ."')" : "";

if (isset($_GET["email"])) {
	// echo $_GET["email"].'<hr>';
	$mail = "%".$_GET["email"]."%";
	// $mail = $_GET["email"];
	$andMail = " AND ((c.name LIKE '".$mail."') OR (c.email LIKE '".$mail."') OR (CONCAT(c.ddi, c.phone) LIKE '".$mail."'))";
	 // echo $andMail;
} else {
	$andMail = "";
}

// $andMail = 
// 	(isset($_GET["email"])) ? " AND ((c.nome LIKE %".$_GET["email"]."%) OR (c.email LIKE %".$_GET["email"]."%) OR (CONCAT(c.ddi, c.phone) LIKE %".$_GET["email"]."%))" : "";



if ($usr) {

	if ($usr["is_admin"] == 1) {
		$andUsr = "";
	} else {
		$andUsr = " and s.usr_id = ".$usr["id"];
	}

} else {
	$json = 
		array(
			"session" => false,
		);
	echo json_encode($json, JSON_PRETTY_PRINT+JSON_UNESCAPED_UNICODE);
	exit;
}

//SESSION END/




$json = 
	array(
		"terminals"=>
			array(
			),

		"partners" => 
			array(
			),

		"recmethod" => 
			array(
			),

		"paymethod" => 
			array(
			),

		"parameters" =>	$_GET
	);









$pdo = connect_pdo();


	$filter = "";

	if (isset($_GET["par_id"])) {
		$par_id = $_GET["par_id"];

		if ($par_id == -1) {
			$filter .= "";
		} else 
		if ($par_id == 0) {
			$filter .= " and s.par_id is NULL";
		} else {
			$filter .= " and s.par_id = $_GET[par_id]";
		}
	}

	if (isset($_GET["app_id"])) {
		$app_id = $_GET["app_id"];

		if ($app_id == 0) {
			$filter .= "";
		} else {
			$filter .= " and i.appl_id = $app_id";
		}
	}



$qry = 
$pdo->query("
select 
    s.id,
    s.skey,          
    s.dlimit,          
    s.ilimit,          
    s.type,          
    s.subtype,          
    s.info,          
    s.price,          
    s.discount,          
    /* -------- s.liquid,   */
    s.prevision,   
    s.timestamp as dbuild,
    DATEDIFF(s.dlimit, s.timestamp) as diff,       
    DATEDIFF(s.dlimit, curdate()) as dleft,       
    /* -------- s.blocked,  _v1_ */
    /* -------- s._v2_disabled, _v2_ */
    s.blocked_at,
    s.usr_id,
    s.par_id,
    u.name as usr_name,
    p.name as par_name,

		i.cus_id, 
		i.mac_id, 
		i.mac_name, 
		i.appl_id, 
		i.distribuition_id,
		i.ver_id,
		i.timestamp, 

		(select count(*) from ins where cus_id = c.id and id < i.id) as cus_ins_count,
		(select count(*) from ins where cus_id = c.id and appl_id = i.appl_id and id < i.id) as app_ins_count,
		(select count(*) from ins where cus_id = c.id and mac_id = i.mac_id and id < i.id) as mac_ins_count,


		v.major,
		v.minor,
		v.revision,
   
		c.name as cus_name, 
		c.email as cus_email, 
		c.phone as cus_phone, 
		c.city as cus_city,
		c.state as cus_state,
		c.ddi as cus_ddi,
		c.country as cus_country,

		(select sum(value) from rechist where seri_id = IFNULL(s.firs_id, s.id)) as rectota,
		(select  count(id) from rechist where seri_id = IFNULL(s.firs_id, s.id) and date < curdate() and confirmed is null) as recfail_count,

		(select items.id from items where seri_id = s.id LIMIT 1) as mp_items_id,

	s.gtw_id


	from 
		seri s 
  	left join
    	ins i on i.id = s.ins_id
	left join 
		cus c on c.id = i.cus_id 
	left join 
		appl a on a.id = i.appl_id
	LEFT JOIN
		usr u on u.id = s.usr_id	
	LEFT JOIN
		par p on p.id = s.par_id	
	left join 
		ver v on v.id = i.ver_id and v.app_id = i.appl_id

	

	where
	    s.ser_id is null 
	    /*and i.cus_id in (22)*/ 
	    /*and s.usr_id in (3)*/ 

	    /*and i.appl_id > 1*/
	    and DATEDIFF(s.dlimit, s.timestamp /*curdate()*/) > 1 
	    and s.hide = 0 ".$andMail." ".$andUsr.$filter." 
	order by
		s.timestamp desc, i.id desc	
		LIMIT 200
");

while ($row = $qry->fetch()) { 

	$liquid = $row->price-$row->discount;
	//echo $row->cus_name."<br>";
	$rechist = array();
	$payhist = array();

	$qr2 =
	$pdo->query("
		select 
			rh.*, 
			rm.name
		from
			rechist rh
		inner join 
			recmethod rm on rm.id = rh.recmethod_id
		where
			rh.seri_id = $row->id
		order by
			id
	");
	

	while ($ro2 = $qr2->fetch()) { 
		array_push(
			$rechist, 
			array(
				"id" => $ro2->id,
				"date"=>date("d-m-Y", strtotime($ro2->date)),
				"value" => $ro2->value,
				"discount" => $ro2->discount+$ro2->gateway_fee,
				"liquid" => $ro2->value-$ro2->gateway_fee-$ro2->discount,
				"method_name" => $ro2->name,
				"confirmed" => $ro2->confirmed != null,
				"file" => is_null($ro2->file) ? "" : $ro2->file,
				"gtw_auto" => $ro2->gtw_auto,
			)
		);
	}




	$qr2 =
	$pdo->query("
		select 
			ph.*, 
			pm.name
		from
			payhist ph
		inner join 
			paymethod pm on pm.id = ph.paymethod_id
		where
			ph.serial_id = $row->id
		order by
			id desc
	");

	while ($ro2 = $qr2->fetch()) { 
		array_push(
			$payhist, 
			array(
				"id" => $ro2->id,
				"date"=>date("d-m-Y", strtotime($ro2->date)),
				"date_created"=>date("d-m-Y", strtotime($ro2->timestamp)),
				"value" => $ro2->value,
				"discount" => $ro2->discount,
				"liquid" => $ro2->value-$ro2->discount,
				"method_name" => $ro2->name,
				"confirmed" => $ro2->confirmed != null,
			)
		);
	}


	$dbuild    = strtotime(date("Y-m-d 00:00:00", strtotime($row->dbuild)));
	$dlimit    = ($row->dlimit == NULL) ? NULL : strtotime($row->dlimit);
	$timestamp = strtotime(date('Y-m-d 00:00:00', time()));
	$expired   = (($dlimit != NULL) && (dateDiff($timestamp, $dlimit) < 0));
	//_v1_ $blocked   = $row->blocked == 1;
	$blocked   = $row->blocked_at != NULL;
	

	$tot_rec   = is_null($row->rectota) ? 0 : floatval($row->rectota);
	$recneed   = (
					($liquid > 1.00) && 

					(
						(dateDiff($dbuild, $timestamp) > 0) && ($tot_rec < $liquid)
						//($timestamp > strtotime($row->dbuild)) && ($tot_rec < $liquid)
					)

				); 
	

	$recfail   = $row->recfail_count > 0; 
	$enabled   = ((!$expired) && (!$blocked) && (!$recneed) && (!$recfail));

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
			"app_id"	=>$row->appl_id,
			"distribuition_id"	=>$row->distribuition_id,
			"ver_id"	=> "~",//($row->ver_id != NULL) ? $row->major.".".$row->minor.".".$row->revision : NULL,

			"cus_ins_count" =>$row->cus_ins_count,
			"mac_ins_count"	=>$row->mac_ins_count,
			"app_ins_count"	=>$row->app_ins_count,

			"keys"		=>
			array(
				array(
					"id" => $row->id,
					"mp_items_id" => !!$row->mp_items_id ? $row->mp_items_id : "",
					"type" => $row->type,
					"subtype" => $row->subtype,
					"skey" => strtoupper($row->skey),
					"dbuild"=>date("d-m-Y", strtotime($row->dbuild)),
					"dlimit"=>date("d-m-Y", $dlimit),
					"time"	=>date("H:i:s", strtotime($row->timestamp)),
					"info"	=>/*base64_encode(*/$row->info/*)*/,
					"ilimit"	=>$row->ilimit,
					"enabled" => $enabled, //($dlimit == NULL) || ($dlimit >= $timestamp),// || ($recfail_count > 0),
					//"_dlimit" => $dlimit,
					//"K_date" => $time,
					//"F_date" => date("d-m-Y H:i:s", $time),
					//--- era assim "diff" => dateDiff($time, $dlimit),
					"diff" => $row->diff,
					"dleft" => $row->dleft,
					"cus_id" => $row->cus_id,
					"app_id" => $row->appl_id,
					"price" => $row->price,
					"discount" => $row->discount,
					"liquid" => $liquid,
					"prevision" => $row->prevision,
					"tot_rec" => $tot_rec,
					"timestamp" => date("d-m-Y", $timestamp),
					"diffff" => dateDiff($dbuild, $timestamp),
					"tags" => 
								array(
									"blocked" => $blocked,
									"expired" => $expired,
									//"recfail_count" => $row->recfail_count,
									"recneed" => $recneed,
									"recfail" => $recfail,
									"d1" => $dbuild,
									"d2" => $timestamp,
									"d3" => dateDiff($dbuild, $timestamp),

									"reseted" => false,
									"recycled" => false,
								),

					"rechist" => $rechist,
					"payhist" => $payhist,
					"usr" => 
								array(
									"id" => $row->usr_id,
									"name" => $row->usr_name,
								),
					"partner" => 
								array(
									"id" => $row->par_id,
									"name" => $row->par_name,
								),
					"gtw_id"  => $row->gtw_id
				)
			),
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


//RECMETHOD
$qry = 
$pdo->query("select * from recmethod /*where usr_id = $usr[id]*/ order by id");

while ($row = $qry->fetch()) { 
	array_push(
		$json["recmethod"], 
		array(
			"id" => $row->id,
			"name" => $row->name,
		)
	);
}


//PAYMETHOD
$qry = 
$pdo->query("select * from paymethod /*where usr_id = $usr[id]*/ order by id");

while ($row = $qry->fetch()) { 
	array_push(
		$json["paymethod"], 
		array(
			"id" => $row->id,
			"name" => $row->name,
		)
	);
}



if (isset($_GET["formated"]))
	echo "<pre>";
echo json_encode($json, JSON_PRETTY_PRINT+JSON_UNESCAPED_UNICODE);

if (isset($_GET["formated"]))
	echo "</pre>";


//ob_end_flush();
//echo ob_get_contents();