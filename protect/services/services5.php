<?php 

error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');


//SMS 1x
define('SMS_ID', 11);
define('MMS_ID', 12);
define('CALL_ID', 13);

//MAIL 2x
define('EMAIL_ID', 21);   

    //define('SMTP_ID', 2101);
    define('PROVIDER_GMAIL_ID', 211);
    define('PROVIDER_YAHOO_ID', 212);
    define('PROVIDER_SMTP_ID', 219);
    //define('OUTLOOK_ID', 212);
    //define('SENDGRID_ID', 213);
    //define('MAILCHIMP_ID', 214);



//providers
    define('PROVIDER_TRUE_ID', 1);
    define('PROVIDER_GOIP_ID', 11);
    //define('PROVIDER_SMTP_ID', 21);
    define('WHATSAPP_ID', 31);
    define('TELEGRAM_ID', 32);
    define('MESSAGES_ID', 51);
    define('TEXTNOW_ID',  52);
    define('TWILIO_ID',   61);
    define('ZENVIA_ID',   62);


//METHODS
    define('METHOD_API_ID', 1);
    define('METHOD_API_LABEL', 'API');
    define('METHOD_WEB_ID', 2);
    define('METHOD_WEB_NAME', 'WEB');
    define('METHOD_WEB_LABEL', 'WEB');
    define('METHOD_GSM_ID', 3);
    define('METHOD_GSM_LABEL', 'USB');
    define('METHOD_GOIP_ID', 4);
    define('METHOD_GOIP_LABEL', 'GoIP');
    define('METHOD_SMTP_ID', 5);
    define('METHOD_SMTP_LABEL', 'SMTP');



//MISC
    define('SW_HIDE', 0);
    define('SW_SHOWMINNOACTIVE', 7);

    define('uidNone', 'uidNone');
    define('uidRegistrable', 'uidRegistrable');
    define('uidDetectable', 'uidDetectable');

    define('mcrText',         'mcrText'        );
    define('mcrMedia',        'mcrMedia'       );
    define('mcrTextOrMedia',  'mcrTextOrMedia' );
    define('mcrTextAndMedia', 'mcrTextAndMedia');

    define('mprUnsupported',    'mprUnsupported');
    define('mprOr',    'mprOr'   );
    define('mprAnd',   'mprAnd'  );
    define('mprAndOr', 'mprAndOr');

    //ProvidersListType
    define('ptStatic' , 'ptStatic' );
    define('ptDynamic', 'ptDynamic');


    //AuthType
    //define('atNone'      , 'atNone'      );
    define('atManual'    , 'atManual'    );
    define('atCredential', 'atCredential');



include "services_telegram.php";
include "services_whatsapp.php";
include "services_sms.php";
include "services_mms.php";
include "services_email.php";
include "services_call.php";




$json = [

    'services' => [



        /*
        ** WHATSAPP
        */
        $service_whatsapp,        

        /*
        ** TELEGRAM
        */
        $service_telegram,

        /*
        ** SMS
        */
        $service_sms,

        /*
        ** MMS
        */
        $service_mms,

        /*
        ** CALL
        */
        $service_call,

        /*
        ** MAIL
        */
        $service_email,

    ]

];


/*

    threadMode  channelMode openMode supportedThreadMode

        cmStatic    (lista os canais) DB
        cmDinamyc   (lista as posições threads/sessions) not DB
        cmBoth

    threadType

        ttSession
        ttChannel

*/


//DRIVERS
//1 BROWSER (truechrome2.exe)
//2 APPLICATION



echo json_encode($json/*, JSON_PRETTY_PRINT*/);    

//echo json_encode(['settings' => ['a', 'b', 'c']]);    



/*

SMS
    MODEM       xxx
        TIM     xxx
        VIVO    xxx

    TWILIO
        API
    TIM
        GSM
    VIVO
        GSM




WHATSAPP
    WHATSAPP
        BROWSER
        API
    TWILIO
        API



MMS
    MESSAGES
        BROWSER
    TWILIO
        API 


****************************************************

type
    method
        provider
            driver

whatsapp
    whatsapp
        browser
    twilio
        api

sms
    twilio
        api
    usb
        program    

sms
    gsm
        vivo
            gsm
    


service
    provider
        driver


whatsapp
    whatsapp
        browser
        api
    twilio
        api


telegram
    telegram
        browser

mms
    messages
        browser

sms
    gsm/operators
        program
    twilio
        api

*/