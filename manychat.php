<?php 

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


require_once "const.php";
require_once "functions.php";
require_once "db.php";


//echo "defsd";


try {
    
    $inputJSON = file_get_contents('php://input');
    $input = json_decode($inputJSON);


    $ndb->manychat()->insert(
        array(
            "sy_from" => $input->from,
            "sy_to" => $input->to,
            "sy_text" => $input->text,
        )
    );


        /*

        //APAGAR
        $ndb->apagar()->insert(
            array(
                "mp"    => '555225',
                "topic" => 'topic',
                "data"  => $inputJSON,
            )
        );







    $text = $input['mytext'];

    //str_replace("\r\n", "\\n", $text);

    $text = urlencode( $text );

    //echo $text;


    $to = TELEGRAM_RUBINHO;


    $resp = file_get_contents("http://gateway.rfidle.com/sms/send?to={$to}&text={$text}&gateway=telegram");


    var_dump($resp);

    */

}

catch(Exception $e) {
  
    //APAGAR
    $ndb->apagar()->insert(
        array(
            "mp"    => '555225',
            "topic" => 'erro',
            "data"  => $e->getMessage(),
        )
    );


}