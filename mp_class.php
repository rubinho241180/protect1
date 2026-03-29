<?php 

require_once "const.php";
require_once "functions.php";

class MP {
    /*
    public $owner;

    public function __construct($owner)
    {
        $this->owner = $owner;
    }
    */
    public static function getPayment($id) {
        $payment = curl_get_contents("https://api.mercadopago.com/v1/payments/$id?access_token=".MP_ACCESS_TOKEN);
        return json_decode($payment);
    }
}