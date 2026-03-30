<?php 

namespace AppProtectKey;

use DateUtils\Date as Date;

error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', 1);
ini_set('display_startup_errors',1);
ini_set('display_errors',1);
error_reporting(-1);




class serial
{
	public $owner;

    public function __construct($owner)
    {
    	$this->owner = $owner;
    }

  	public function insert($params) {

		require_once "db.php";
		$pdo = connect_pdo();
		
		//SELECT INST ID
		$sql = "
		select
			id
		from
			ins
		where
			mac_id = :mac_id and 
			appl_id = :app_id
		";

		$par = 	array(
					"mac_id" => $this->owner->mac_id,
					"app_id" => $this->owner->app_id,
				);
		$stm = $pdo->prepare($sql);
		$qry = $stm->execute($par);
		$row = $stm->fetch();



		//GENERATE NEW KEY
  	    $flag = $params["flag"]; //new, transfer, renew
		$type = $params["type"];
		$subt = 1; 

  		$dbui = date('Y-m-d H:i:s');

  		$dbuf = str_replace("-", "", date("d-m-y", strtotime($dbui)));
  		
  		$dlim = date('Y-m-d H:i:s', strtotime($params["dlim"]));
  		$dlif = str_replace("-", "", date("d-m-y", strtotime($dlim)));

        $ilim = $params["ilim"];
        $info = $params["info"];


		$pkey = str_pad(dechex($type), 2, "0", STR_PAD_LEFT).
			    str_pad(dechex($dbuf), 5, "0", STR_PAD_LEFT).
			    str_pad(dechex($dlif), 5, "0", STR_PAD_LEFT).
			    str_pad(dechex($subt), 2, "0", STR_PAD_LEFT).
			    str_pad(dechex($ilim), 2, "0", STR_PAD_LEFT);

		require_once "rijndael.php";

		//ENCRYPT
		$skey = AES_Rijndael_Encrypt(
							strtoupper($pkey), 
							$this->owner->ikey().":1", 
							$this->owner->ikey().":2"
				);

		$skey = strtoupper(bin2hex($skey));



        //VALUES
        $gtid = isset($params["gateway_id"]) ? $params["gateway_id"] : NULL; //GATEWAY_PAYMENT_ID
        $pric = isset($params["price"])      ? floatval($params["price"]) : NULL;
        $disc = isset($params["discount"])   ? floatval($params["discount"]) : NULL;

		//INSERT A NEW SERIAL
		$sql = 
		"
			insert into 
				seri 
			set 
				ins_id    = :ins_id,
				_v2_flag  = :flag,
				type      = :type,
				subtype   = :subt,
				timestamp = :dbui,
				dlimit    = :dlim,
				ilimit    = :ilim,
				skey      = :skey,
				info      = :info,
                price     = :pric,
                discount  = :disc,
                gtw_id    = :gtw_id,

				auto      = :auto,
				_v1_blocked   = 0
		";

		$param2 = array(
						"ins_id" => $row->id,
						"flag" 	 => $flag,
						"type" 	 => $type,
						"subt" 	 => $subt,
						"dbui" 	 => $dbui,
						"dlim" 	 => $dlim,
						"ilim" 	 => $ilim,
						"skey" 	 => $skey,
						"info" 	 => $info,// "AUTOMATIC\nKEY: ".$this->owner->ikey()."\nSERIAL: ".$skey,
        
                        "pric"   => $pric,
                        "disc"   => $disc,
                        "gtw_id" => $gtid,
		
        				"auto"	 => isset($params["auto"]) ? $params["auto"] : 0,
					);

		$stm = $pdo->prepare($sql);
		$qry = $stm->execute($param2);

        $inserted_seri_id = $pdo->lastInsertId();





  	  	return 	array(
                    "id"   => $inserted_seri_id,
  	  				"flag" => $flag,
  	  				"type" => $type,
  	  				"dbui" => $dbui,
  	  				"dlim" => $dlim,
  	  				"ilim" => $ilim,
  	  				"skey" => $skey,
  	  				"errors" => array(),

  	  			);
  	}
}





class inst
{

	public $mac_id;
	public $app_id;
	public $serial;

    public function __construct($mac_id, $app_id)
    {
        $this->mac_id = $mac_id;
        $this->app_id = $app_id;
        $this->serial = new serial($this);
    }

  	public function ikey() {
  		return $this->mac_id."-".$this->app_id;
  	}
}
 






class MySerial 
{
	public $ndb;
	public static $sdb;

    public function __construct()
    {
    	global $ndb;
        $this->ndb = $ndb;
    }
	
	public static function fetch($ser) 
	{

		require_once "DateUtils.php";
    	global $ndb;
		//$ndb = $this->ndb;


		//SE FOR INTEIRO
		if (gettype($ser) == "integer") {
			$ser = $this->ndb->ser()->where("id = ?", $ser);
		}



		$date   = Date::date();
		$timest = Date::timestamp();

		$dbuild = Date::date($ser["timestamp"]);
		$tbuild = Date::time($ser["timestamp"]);


		$dlimit = ($ser["dlimit"] == NULL) ? NULL : Date::date($ser["dlimit"]);
		$dlimit = ($ser["dlimit"] == NULL) ? NULL : Date::date($ser["dlimit"]);
		$diff   = Date::diff($dbuild, $dlimit)->days;
		$dleft  = Date::diff($date,   $dlimit)->days;


		$expired  = (($dlimit != NULL) && ($dleft < 0));
		$blocked  = $ser["blocked_at"] != NULL;
		$reseted  = $ndb->seri()->where("ser_id = ?", $ser["id"])->count() != 0;
		$recycled = $ser["ser_id"] != NULL;
		//$recneed  = (($ro2->liquid > 1.00) && (dateDiff(strtotime($ro2->timestamp), $time) > 0) && ($ro2->rectota < $ro2->liquid)); 
		//$recfail  = $ro2->recfail_count > 0; 
		//$enabled  = ((!$expired) && (!$blocked) && (!$reseted) && (!$recneed) && (!$recfail));
		$enabled  = ((!$expired) && (!$blocked) && (!$reseted));


		$ret = 
			array(
				"id"   => $ser["id"],
				"type" => $ser["type"],
				"subt" => $ser["subtype"],
				"skey" => $ser["skey"],
				"dbui" => $dbuild,
				"tbui" => $tbuild,
				"dlim" => $dlimit,
				"ilim" => $ser["ilimit"],
				"info" => $ser["info"],
				"diff" => 
					array(
						"buil"=> $diff,
						"left"=> $dleft,
					),
				"tags" =>
					array(
						"expi"  => $expired,
						"bloc"  => $blocked,
						"rese"  => $reseted,
						"recy" => $recycled,
						"recn"  => false,//$recneed,
						"recf"  => false,//$recfail,
						"auto" => $ser["auto"] == 1,
					),
				"enab" => $enabled,
				"recycled_ikey" =>  NULL,
				"user" => 
					array(
						"id" => $ser["usr_id"],
						"name" => $ser["usr_id"],
					),
				"part" => 
					array(
						"id" => $ser["par_id"],
						"name" => $ser["par_id"],
					),
				"pric" => $ser["price"],	
				"disc" => $ser["discount"],	
				"liqu" => $ser["price"]-$ser["discount"],	
			);


		return json_decode(json_encode($ret));

	}
}