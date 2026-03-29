<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer_2020/src/Exception.php';
require '../PHPMailer_2020/src/PHPMailer.php';
require '../PHPMailer_2020/src/SMTP.php';

// Load Composer's autoloader
//require 'PHPMailer_2020/vendor/autoload.php';


function SendMAIL_HTML($params, $template, $fields) {


    // codificação UTF-8, a codificação mais usada recentemente
    $mail->CharSet = "UTF-8";

    $body      = file_get_contents('templates/'.$template);
    //$body          = preg_replace('/\\\\/','', $body); //Strip backslashes

    echo "will<br>";
    //preenche parametros
    foreach ($fields as $key => $value) {
        echo "param: {$key} = {$value}<br>";
        $body = str_replace($key, $value, $body);
    }





    //Create a new PHPMailer instance
    $mail = new PHPMailer;
    $mail->setFrom('naoresponda@truesistemas.com.br', 'MrSENDER HUB');
    //Set an alternative reply-to address
    //$mail->addReplyTo('replyto@example.com', 'First Last');
    //Set who the message is to be sent to
    $mail->addAddress($params["to"]);
    //Set the subject line
    $mail->Subject = $params["subject"];
    //Read an HTML message body from an external file, convert referenced images to embedded,
    //convert HTML into a basic plain-text alternative body
    //$mail->msgHTML(file_get_contents('buy.html'), __DIR__);

    //Replace the plain text body with one created manually
    $mail->AltBody = 'This is a plain-text message body';
    //Attach an image file
    //$mail->addAttachment('images/phpmailer_mini.png');

    /*
        $mail->isHTML(true);                                  // Set email format to HTML
        $mail->Subject = 'Here is the subject';
        $mail->Body    = 'This is the HTML message body <b>in bold!</b>';
        $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
    */

        $mail->Body    = $body;


    //send the message, check for errors
    if (!$mail->send()) {
        echo 'Mailer Error: '. $mail->ErrorInfo;
    } else {
        echo 'Message sent!!!!';
    }
}


$params = array(
  "to" => "rubinho241180@gmail.com",
  "replyTo" => "naoresponda@truesistemas.com.br",
  "replyToName" => "SAMARA VIEIRA OLIVEIRA",
  "subject" => "Serial TrueSMS TU Go"
);



$fields = array(
  "{serial}" => "sdghfshdggsdjgsfjg"
  );

echo SendMAIL_HTML($params, "buy.html", $fields);
