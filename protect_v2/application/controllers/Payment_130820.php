<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payment extends MY_Controller {

	public function index()
	{

		header('Content-Type: application/json');
		
		$this->load->helper("curl");
		$this->load->library("mp");

		$key_id  = $_POST["key_id"];
		$met_id  = $_POST["met_id"];
		$val     = $_POST["val"];
		$dis     = $_POST["dis"];
		$liq     = $_POST["liq"];
		$dat     = date('Y-m-d H:i:s', strtotime($_POST["dat"]));
		$gate_id = isset($_POST["gate_id"]) ? $_POST["gate_id"] : NULL; 
		//$pic    = $_POST["pic"];

		$timestamp = date('Y-m-d H:i:s');


		$gate_id = isset($_POST["gate_id"]) ? $_POST["gate_id"] : NULL; 

		//PAYMENT STATUS IN GATEWAY
		if (isset($_POST["gate_id"])) {

			/*retrieve payment********************************************************/
			
				$payment = $this->mp->getPayment($gate_id);
				$payment_status  = $payment->status;
			
				//SE NÃO EXISTE
				if ($payment_status == 404) {
					echo json_encode(array("errors"=>array("::payment->status = ".$payment_status)), JSON_PRETTY_PRINT);
					exit;
				}

			/*************************************************************************/





			/*payment fields**********************************************************/

				$payment_type_id = $payment->payment_type_id;
				$created_at      = date('Y-m-d H:i:s', strtotime($payment->date_created));
				$approved_at     = ($payment->date_approved)        ? date('Y-m-d H:i:s', strtotime($payment->date_approved))     : NULL;
				$changed_at      = (!in_array($payment->status, array("pending", "in_process"))) ? date('Y-m-d H:i:s', strtotime($payment->date_last_updated)) : NULL;

			/*************************************************************************/





			/*calculate fee and values************************************************/

				$fee1 = ($val * MP_TAX1) / 100;
				$fee1 = bcadd($fee1, 0, 2); // <=-- APENAS PARA REMOVER A DÍZIMA
				$liqu = bcsub($val, $fee1, 2);
				$fee2 = ($liqu * MP_TAX2) / 100;
				$fee2 = bcadd($fee2, 0, 2); // <=-- APENAS PARA REMOVER A DÍZIMA
				$liqu = bcsub($liqu, $fee2, 2);

			/*************************************************************************/





			/*calculate used gateway_id total*****************************************/

				$ser       = $this->db->rechist()->select("IFNULL(sum(value), 0) as has_total")->where("gate_id = ?", $gate_id)->fetch();
				$has_total = floatval($ser["has_total"]);

				//SE JÁ FORAM GERADAS AS LICENÇAS...
				if (($has_total+$val) > $payment->transaction_amount) {
					echo json_encode(array("errors"=>array("payment_total = ".$payment->transaction_amount." AND "."has_total = ".$has_total)), JSON_PRETTY_PRINT);
					exit;
				}
			
			/*************************************************************************/





			/*calculate next withdraw date********************************************/
				
				$payment_date_rettired = strtotime("next tuesday", strtotime($approved_at));

			/*************************************************************************/


		} else {
			$payment_status  = "pending";
			$payment_type_id = NULL;
			$created_at      = $timestamp;
			$approved_at     = NULL;
			$changed_at      = NULL;
			$fee1			 = 0;
			$fee2			 = 0;
			$liqu            = $liq;
		}





		$paym = 
			$this->db->rechist()->insert(
				array(
					"date" 				=> $dat,
					"value" 			=> $val,
					"discount" 			=> $dis,
					"fee1"				=> $fee1,
					"fee2"				=> $fee2,
					//"liquid" 			=> $liqu,
					"seri_id" 		=> $key_id,
					"recmethod_id" 		=> $met_id,
					"gate_id" 			=> $gate_id,
					"payment_type_id"	=> $payment_type_id,
					"timestamp" 		=> $created_at,
					"approved_at"		=> $approved_at,
					"changed_at"		=> $changed_at,
					"status"			=> $payment_status,
					//"pic" => str_replace(" ", "+", $pic),
				)
			);




		$json =
			array(
				"inserted" => $paym,
				"errors" => array(),
				"post" => $_POST,
				"payment_status" => $payment_status,
				"payment_type_id" => $payment_type_id,
			);


		if (!$paym) {
			//$this->load->library("pdo2");

			array_push(
				$json["errors"],
				//$this->db->pdo->errorInfo()
				[]
				//get_class($this->pdo2)
			);
		} else {
			$json["inserted_id"] = $paym["id"];
		}

		echo json_encode($json, JSON_PRETTY_PRINT);
	}
}
