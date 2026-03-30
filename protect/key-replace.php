<?php 

$reseted_id = $_POST["res_id"];
$ins_id = $_POST["ins_id"];
$mac_id = $_POST["mac_id"];
$app_id = $_POST["app_id"];

$timestamp = date('Y-m-d H:i:s');

require_once "db.php";
require_once "const.php";

$pdo = connect_pdo();

$sql = 
"
select
	CONCAT(i.mac_id, '-', i.appl_id) as ikey,
	s.ins_id,
	s.id, 
	s.type,
	s.subtype, 
	s.timestamp, 
	s.dlimit as dlimit5, 
	s.ilimit, 
	s.skey, 
	s.par_id,
	DATEDIFF(s.dlimit, curdate()) as dleft 
from 
	seri s
inner join	
	ins i on i.id = s.ins_id
where 
	s.id = $reseted_id
";


$qry = $pdo->query($sql);
$row = $qry->fetch();


//echo $reseted_id;

//MARCA O OLD COMO "TRANSFERRED"
$old = $ndb->seri()->where('id = ?', $reseted_id)->fetch();

if ($old['firs_id'] != NULL) {
	$firs_id = $old['firs_id'];
} else {
	$firs_id = $old['id'];
}

$old['_v2_disabled'] = DISABLED_TRANSFERRED;
$old->update();



$typ = $row->type;
$sub = $row->subtype; 
$dbu = /*str_replace("-", "", date("d-m-y", strtotime($row->timestamp)));//*/  str_replace("-", "", date("d-m-y"));
$dli = ($row->dlimit5 != null) ? str_replace("-", "", date("d-m-y", strtotime($row->dlimit5))) : "000000";
$ili = $row->ilimit; 



//GENERATE NEW KEY
$key = str_pad(dechex($typ), 2, "0", STR_PAD_LEFT).
	   str_pad(dechex($dbu), 5, "0", STR_PAD_LEFT).
	   str_pad(dechex($dli), 5, "0", STR_PAD_LEFT).
	   str_pad(dechex($sub), 2, "0", STR_PAD_LEFT).
	   str_pad(dechex($ili), 2, "0", STR_PAD_LEFT);

require_once "rijndael.php";

$enc = AES_Rijndael_Encrypt(strtoupper($key), $mac_id."-".$app_id.":1", $mac_id."-".$app_id.":2");
$skey = strtoupper(bin2hex($enc));
$skey = substr($skey, 0, 32);






//INSERT A NEW SERIAL
$sql = 
"
	insert into 
		seri 
	set 
		ins_id   = :ins_id,
		_v2_flag = :flag,
		type     = :typ,
		subtype  = :sub,
		timestamp = :dbu,
		dlimit   = :dli,
		ilimit   = :ili,
		skey     = :key,
		info     = :inf,
		par_id   = :par_id,
		ser_id   = :ser_id,
		firs_id   = :firs_id,
		_v1_blocked  = 0
";

$params = array(
				"ins_id" => $ins_id,
				"flag" 	 => FLAG_TRANSFER,
				"typ" 	 => $row->type,
				"sub" 	 => $row->subtype,
				"dbu" 	 => $timestamp,
				"dli" 	 => $row->dlimit5,
				"ili" 	 => $row->ilimit,
				"key" 	 => $skey,
				"inf" 	 => "RECYCLED\nKEY: ".$row->ikey."\nSERIAL: ".$row->skey,
				"par_id" => $row->par_id,
				"ser_id" => $reseted_id,
				"firs_id" => $firs_id,
			);

$params2= array(
				"dif"    => ($row->dleft == NULL) ? 0 : $row->dleft,
				"dbu2"   => date("d/m/Y", strtotime($timestamp)),
			);

$stmt = $pdo->prepare($sql);
$qry  = $stmt->execute($params);


$old['new_id'] = $pdo->lastInsertId();
$old->update();



//JSON RESULT
$json =
array(
	"post" => $_POST,
	"row"  => $row,	
	"dbu"  => $dbu,
	"dli"  => $dli,
	"typ"  => $typ,
	"sub"  => $sub,
	"pkey" => $key,
	"skey" => $skey,
	"inserted" => $qry,
	"params" => $params,
	"params2" => $params2,
	"errorCode2" => $stmt->errorCode(),
	"errorInfo2" => $stmt->errorInfo(),
	'firs_id' => $firs_id,
	'old' => $old,

);


echo json_encode($json, JSON_PRETTY_PRINT);
