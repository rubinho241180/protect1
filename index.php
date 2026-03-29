<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

define("TIMEZONE", "America/Recife");
date_default_timezone_set(TIMEZONE);

header("Access-Control-Allow-Origin: *");

function toJSON($app, $content) {
    $response = $app->response;
    $response['Content-Type'] = 'application/json';
    $response->body( json_encode($content) );
};


require 'Slim/Slim.php';
\Slim\Slim::registerAutoloader();

/*
 * Step 2: Instantiate a Slim application
 *
 * This example instantiates a Slim application using
 * its default settings. However, you will usually configure
 * your Slim application now by passing an associative array
 * of setting names and values into the application constructor.
 */

$app = new \Slim\Slim();


$app->get(
    '/',
    function () {
		echo date("d-m-Y H:i:s");	
    }
);

            $app->get(
                '/teste2/',
                function () {
                    include "teste2.php";
                }
            );


            $app->group('/__x_admin', function() use ($app) {

                $app->get(
                    '/',
                    function () {
                        echo "hello, admin!";
                    }
                );

                $app->get(
                    '/overview/',
                    function () {
                        include "admin-overview.php";
                    }
                );

                $app->get(
                    '/conciliation/',
                    function () {
                        include "admin-conciliation.php";
                    }
                );

                $app->get(
                    '/funnel/',
                    function () {
                        include "funnel.php";
                    }
                );

                $app->get(
                    '/month/',
                    function () {
                        if (isset($_GET['type']) && $_GET['type'] == 'heatmap')
                            include "__x_admin-month-heatmap.php"; else
                        if (isset($_GET['type']) && $_GET['type'] == 'receipts')
                            include "__x_admin-month-receipts.php"; else
                            include "__x_admin-month-sales.php";
                    }
                );

                $app->get(
                    '/region/',
                    function () {
                        include "admin-region.php";
                    }
                );

            });






    $app->group('/gateway', function() use ($app) {


        $app->group('/payment', function() use ($app) {

            $app->get(
                '/notifications/:gateway/',
                function ($gateway) {
                    include "gateway-payment-notifications.php";
                }
            );

        });

    });




$app->get(
    '/redirect/',
    function () {
        include "redirect.php";
    }
);

$app->get(
    '/dist-info/',
    function () {
        include "dist-info.php";
    }
);


$app->get(
    '/temp-to-serial/',
    function () {
        include "temp-to-serial.php";
    }
);







$app->get(
    '/login/',
    function () {
        include "login.php";
    }
);



$app->get(
    '/select/',
    function () {
        include "select.php";
    }
);


$app->get(
    '/v2/select/',
    function () {
        include "select_ondemand.php";
    }
);


$app->get(
    '/key/list/',
    function () {
        include "key-lst.php";
    }
);



$app->get(
    '/app/',
    function () {
        include "app-select.php";
    }
);

$app->get(
    '/customer/',
    function () {
        include "customer-select.php";
    }
);

$app->get(
    '/update/',
    function () {
        include "ver-update.php";
    }
);


                                        $app->get(
                                            '/illegal/',
                                            function () {
                                                //echo $app;
                                                include "illegal-whatsapp.php";
                                            }
                                        );







$app->post(
    '/customer/',
    function () {
        include "customer-insert.php";
    }
);

$app->post(
    '/key/receipt/',
    function () {
        include "rec-insert.php";
    }
);

    $app->post(
        '/key/receipt/update/',
        function () {
            include "rec-update.php";
        }
    );

$app->post(
    '/key/payment/',
    function () {
        include "pay-insert.php";
    }
);

    $app->post(
        '/key/payment/update/',
        function () {
            include "pay-update.php";
        }
    );

$app->post(
    '/key/',
    function () {
        include "key-insert.php";
    }
);

    $app->post(
        '/key/insert/trial/',
        function () {
            include "key-insert-trial.php";
        }
    );

    $app->post(
        '/key/replace/',
        function () {
            include "key-replace.php";
        }
    );

    $app->post(
        '/key/update/',
        function () {
            include "key-update.php";
        }
    );
    $app->post(
        '/key/block/',
        function () {
            include "key-block.php";
        }
    );
    

$app->post(
    '/version/',
    function () {
        include "ver-insert.php";
    }
);


$app->get(
    '/newsletter/',
    function () {
        include "newsletter.php";
    }
);


$app->get(
    '/application/:app_id/download/',
    function ($app_id) {
        include "download.php";
    }
);

$app->get(
    '/download/',
    function () {

        //if ($_SERVER['HTTPS'] != "on") {
        //    $url = "https://". $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
        //    header("Location: $url");
            //exit;
        //} else {

            $mode = isset($_GET['mode']) ? $_GET['mode'] : 'bootstrap';// 'header-redirect';
            require "download-{$mode}.php";
        //}

    }
);

    $app->post(
        '/deploy/step/',
        function () {
            include "deploy-step.php";
        }
    );


$app->get(
    '/download2/',
    function () {
        require "download2.php";
    }
);

$app->get(
    '/download3/',
    function () {
        require "download3.php";
    }
);


$app->get(
    '/flow/',
    function () {
        require "flow.php";
    }
);



$app->get(
    '/open-chat/:phone/',
    function ($phone) {
        echo "open-wapp $phone";
    }
);

$app->get(
    '/log/:id/',
    function ($id) {
        
        require_once "db.php";

        $logg = 
            $ndb->log()->where("id", $id)->fetch();
        
        echo $logg['text'];


    }
);



$app->group('/mp', function() use ($app) {

    $app->get(
        '/order/:id',
        function ($id) use ($app) {
            require 'mp.php';
            toJSON($app, MP::getOrder($id));
        }
    );
    $app->get(
        '/payment/:id',
        function ($id) use ($app) {
            require 'mp.php';
            toJSON($app, MP::getPayment($id));
        }
    );
    $app->get(
        '/approved/',
        function () use ($app) {
            require 'mp-approved.php';
        }
    );

});







$app->group('/v2', function() use ($app) {


    $app->group('/hello', function() use ($app) {

        $app->get(
            '/world/',
            function () {
                echo "hello wordl!";
            }
        );

        $app->get(
            '/notifications/:gateway/',
            function ($gateway) {
                include "gateway-payment-notifications.php";
            }
        );

    });

});









$app->run();

?>