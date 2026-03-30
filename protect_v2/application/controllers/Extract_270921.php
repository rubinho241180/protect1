<?php
defined('BASEPATH') OR exit('No direct script access allowed');


		function dateDiff($start, $end) {
			$start_ts = $start;//strtotime($start);
			$end_ts = $end;//strtotime($end);
			$diff = $end_ts - $start_ts;
			return round($diff / 86400);
		}


class Extract extends CI_Controller {


	public function __construct() {
		parent::__construct();
		$this->load->library("pdo2");
	}

	public function index()
	{

		$hoje    = strtotime(date('Y-m-d 00:00:00', time()));
		$user_id = (isset($_GET["user_id"])) ? $_GET["user_id"] : 0;


		$conditions =
			array(
				"B" => 
					array(
						//'field' => 's.timestamp',
						'sql' => "s.__settled_at is null",
					),
				"D" => 
					array(
						//'field' => 's.confirmed',
						'sql' => "s.__settled_at is NOT null",
					),
			);


		if (in_array($user_id, [1, 2])) {

			$conditions['B']['sql'] .= " AND blocked_as_available_in_extract = 0";// AND (s.__settled_at is not null) AND (DATEDIFF(s.__settled_at, $hoje) < 10)";
			$conditions['D']['sql'] .= "  OR blocked_as_available_in_extract = 1";
		}	

		$data = 
			array(
				'movements' =>
					array(
						'B' => [],
						'D' => [],
					)
			);




		foreach ($conditions as $key => $condition) {


			$sql1 =
							"
			SELECT result.* 
			FROM (				
							/*
							select
								4 as type,
								i.id,
								o.timestamp as date2,
								DATE_ADD(DATE(o.timestamp), INTERVAL 3 DAY) as dfree,
								'TEMP1' as hist,
								i.price as vbase,
								(i.price/2)-i.fee as value 
							from
								items i
							INNER JOIN
								orders o on o.id = i.orders_id
							where
								serial IS NOT NULL AND seri_id IS NULL
							



							UNION /*******/



							select 
								1 as type, 
								s.id, 
								s.timestamp as date2,
								s.__settled_at as dfree, 
								'VENDA' as hist, 
								s.price-s.discount as vbase, 
								p.valu as value 
							from
								seri_prod p
							INNER JOIN
								seri s on s.id = p.seri_id	
							where 
								p.prod_id = ? and 
								s.price > 10 and 
								$condition[sql]





							UNION /*******/

							select 
								2 as type, 
								draw.id,
								draw.timestamp as date2,
								draw.timestamp as dfree,
								hist,
								value as vbase,
								value 
							from
								draw
							where
								usr_id = ?





							UNION /*******/

							select 
								3 as type, 
								s.id,
								s.__settled_at as date2, /*dfree, */
								charged_back as dfree,/*date2,*/
								'ESTORNO' as hist,
								s.price-s.discount as vbase,
								p.valu as value
							from
								seri_prod p
							INNER JOIN
								seri s on s.id = p.seri_id	
							where
								p.prod_id = ? and
								s.price > 10 and
								s.charged_back is not null and 
								$condition[sql]

			) result
			ORDER BY result.dfree, result.date2				
							";

			//echo $sql1;
			//exit;




			$sql2 =
							"
			SELECT result.* 
			FROM (				
							/*
							select
								4 as type,
								i.id,
								o.timestamp as date2,
								DATE_ADD(o.timestamp, INTERVAL 3 DAY) as dfree,
								'TEMP' as hist,
								i.price as vbase,
								(i.price/2)-i.fee as value 
							from
								items i
							INNER JOIN
								orders o on o.id = i.orders_id
							where
								serial IS NOT NULL AND seri_id IS NULL
							



							UNION /*******/


							select 
								1 as type, 
								id, 
								timestamp as date2, 
								s.__settled_at as dfree, 
								'VENDA' as hist, 
								s.price-s.discount as vbase, 
								sal_valu as value 
							from
								seri s
							where 
								usr_id = ? and 
								price > 10 and 
								$condition[sql]



							UNION /*******/

							select 
								2 as type, 
								draw.id,
								draw.timestamp as date2,
								draw.timestamp as dfree,
								hist,
								value as vbase,
								value 
							from
								draw
							where
								usr_id = ?



							UNION /*******/

							select 
								3 as type, 
								id,
								s.__settled_at as date2, /*dfree, */
								charged_back as dfree,/*date2,*/
								'ESTORNO2' as hist,
								s.price-s.discount as vbase,
								sal_valu as value
							from
								seri s
							where
								usr_id = ? and
								price > 10 and
								charged_back is not null and 
								$condition[sql]

							

			) result
			ORDER BY result.dfree, result.date2				
							";




			$sql = ($user_id > 2) ? $sql2 : $sql1;


			$stmt = $this->pdo2->pdo_connect()->prepare($sql);
			$stmt->execute(array($user_id, $user_id, $user_id));


			//$data["movements"] = [];
			$versions = ["STD", "ADV", "PRO", "XYZ"];

			$bala = 0.00;





			while ($row = $stmt->fetch()) {

				$fact = in_array($row->type, array(1, 4)) ? $row->value : $row->value*-1;

				if (($key != 'B') || ($row->type != 2)) 
				{
					$bala = bcadd($bala, $fact, 2);
				}

				if ($row->type == 1) {
					$seri = $this->db->seri()->where("id = ?", $row->id)->fetch();
					$appl = $seri->ins["appl_id"];
					
					$hist = 
						$seri->ins["mac_id"]."-".
						$seri->ins["appl_id"].", ".
						$seri["skey"].", ";

					$hist = 
						$versions[$seri["subtype"]-1]."-".$seri["ilimit"];

					$subh = 
						$seri->ins["mac_id"]."-".
						$seri->ins["appl_id"].", ".
						$seri["skey"].", ".
						$seri->ins->cus["name"].", ".
						$row->vbase;

				} else 


				if ($row->type == 3) {

					$hist = '<span style="color:red;">ESTORNO ⥂</span>';
					$subh = $row->id.', '. date("d/m/Y", strtotime($seri["timestamp"]));

				} else 

				if ($row->type == 4) {

					$hist = '<span style="color:green;">TEMPPPPPPPPPPPPPPPPPPPP ⥂</span>';
					$subh = 'sdfsdfsfdf';

				} else 

				{	
					$hist = '<span style="color:red;">SAQUE ⥂</span>';
					$subh = $row->hist;
				}



				/*21-03-18*/
				if ($row->dfree == NULL) {
					$sql2  = "select max(date) as dfree from rechist where seri_id = ?";
					$stmt2 = $this->pdo2->pdo_connect()->prepare($sql2);
					$stmt2->execute(array($row->id));
					$row2 = $stmt2->fetch();

					$dfree = $row2->dfree;
				} else {
					$dfree = $row->dfree;
				}


				/*21-03-18 end */



				if (
						(($key != 'B') || ($row->type != 2))  /* novo ---=> */ 
						&& ($dfree != NULL) 
						&& (($key != 'B') || (strtotime('+1 day', strtotime($dfree)) >= $hoje))
				)
				array_push(
					$data["movements"][$key], 
					array(
						"type" => $row->type,
						"id" => $row->id,
						"date" => $row->date2,
						"free" => $dfree,
						"appl" => isset($appl) ? $appl : 0,
						"hist" => strtoupper($hist),
						"subh" => strtoupper($subh),
						"valu" => $fact,
						"bala" => $bala,
						//"date" => $paym["date"],
						//"date" => $paym["date"],
					)
				);
			}





			/*
			** DO BALANCE
			*/
			$vtot = 0.00;
			foreach ($data["movements"][$key] as &$value)
			{
				$vtot = bcadd($vtot, $value["valu"], 2); 
				$value["bala"] = $vtot;
			}


			$data["movements"][$key] = array_reverse($data["movements"][$key]);

			//header('Content-Type: application/json');

			//echo json_encode($data["movements"], JSON_PRETTY_PRINT+JSON_UNESCAPED_UNICODE);
			//exit;

			$data["saldo"]  = 0.00;
			/*$data["orders"] = $this->db->rechist()->where("serial.usr_id = ?", 3);
			$data["movements"]  = $movements;*/


		}

		//echo json_encode($data);
		$this->load->view('extract', $data);
	}

	public function teste()
	{
		echo 'Hi, list!';
	}
}
