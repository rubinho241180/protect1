<?php 


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


require_once "db2.php";


$terminals = 
	array(
	);


foreach (Ins::all(array('joins' => array('cus'))) as $ins) 
{
//foreach ($ndb->ins() as $ins) {
    array_push(
    	$terminals, 
    	array(
    		"id"        => $ins->id,
    		"date"      => date("d-m-Y", strtotime($ins->timestamp)),
			"time"		=> date("H:i",   strtotime($ins->timestamp)),
			"cus_id"	=> $ins->cus_id,
			"cus_name"	=> $ins->cus->name,
			/*"cus_email"	=>$row->cus_email,
			"cus_phone"	=>$row->cus_phone,
			"cus_ddi"	=>$row->cus_ddi,
			"cus_country"	=>$row->cus_country,
			"cus_city"	=>$row->cus_city,
			"cus_state"	=>$row->cus_state,
			
			"mac_id"	=>$row->mac_id,
			"mac_name"	=>$row->mac_name,

			"app_id"	=>$row->app_id,

			"cus_ins_count"	=>$row->cus_ins_count,
			"mac_ins_count"	=>$row->mac_ins_count,
			"app_ins_count"	=>$row->app_ins_count,


			"ver_id"	=> ($row->ver_id != NULL) ? "~			".$row->major.".".$row->minor.".".$row->revision : NULL,
			"app_name"	=>$row->app_name,
			//***"keys"		=>$serials,
			//***"os" => $os,
			//***"hist" => $hist,*/
    	)
    );
}




$json = 
	array(
		"terminals" => $terminals,
		"metrics"   => 
			array(
				"elapsed" => round(microtime(true) - $curTime1,3),

			)
	);


echo json_encode($json, JSON_PRETTY_PRINT+JSON_UNESCAPED_UNICODE);



