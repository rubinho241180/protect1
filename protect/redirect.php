<?php 

//var_dump($_REQUEST);

require_once "db.php";


$direction     = trim($_GET['direction']);
$application   = trim($_GET['app']);
$distribuition = trim($_GET['distribuition']);
$distribuition = isset($_GET['distribuition']) ? trim($_GET['distribuition']) : '';

// $appl_id = trim($_GET['appl']);
 //$dist_id = trim($_GET['dist']);

if ($distribuition == '') {
    $distribuition = 'TSI901';
}

//echo $direction;
//echo $application;
//echo $distribuition;


if ($direction == 'PAYMENT')
{
    
    //if ($distribuition == '')
    //{
    //    header('Location: http://truesistemas.com.br/redirectagent.php?id='.$application);
    //} else {

        $dist = $ndb->distribuition()->where("id = '$distribuition'")->limit(1)->fetch();
        //var_dump($distribuition);
        header('Location: '.$dist["url_pay"]);

    //}

}

if ($direction == 'SUPPORT')
{
    
    //if ($distribuition == '')
   // {
   //     header('Location: http://truesistemas.com.br/redirectagent.php?id='.$application);
   // } else {

        $dist = $ndb->distribuition()->where("id = '$distribuition'")->limit(1)->fetch();
        header('Location: '.$dist["url_support"]);

    //}
}