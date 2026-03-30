<?php 

defined('BASEPATH') OR exit('No direct script access allowed');

class Mp {
	public static function getPayment($id) {
		$payment = curl_get_contents("https://api.mercadopago.com/v1/payments/$id?access_token=".MP_ACCESS_TOKEN);
		return json_decode($payment);
	}
}