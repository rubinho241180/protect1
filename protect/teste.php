<?php
  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);
  error_reporting(E_ALL);
//header('Content-Type: application/json');



//$date = date('Y-m-d', strtotime("monday this week"));
//echo $date;

//exit;

// if ($_SERVER['HTTPS'] != "on") {
//     $url = "https://". $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
//     header("Location: $url");
//     exit;
// }

require_once "rijndael.php";

$hex = "3B045E24582458C066494593F5E1B843";

echo $hex;
echo "<br>";
echo "<hr>";
echo "hex: ".strlen($hex);
echo "<br>";
echo "<br>";


$hex = pack("H*", $hex);

echo $hex;
echo "<br>";
echo "<hr>";
echo "raw: ".strlen($hex);
echo "<br>";
echo "<br>";

$ikey = '12345678-901';


$device_data = "pre;"."\n".

        
        AES_Rijndael_Decrypt(
            $hex, 
            $ikey.":100", 
            $ikey.":200"
        ) 

        . "\n".";pos";
        


echo $device_data;        

echo "<br>";
echo "<hr>";
echo "dec: ".strlen($device_data);


echo "<hr>";
echo "<hr>";
echo "<hr>";


$enc =        AES_Rijndael_Encrypt(
            "amor", 
            $ikey.":100", 
            $ikey.":200"
        ) ;

echo $enc;


echo "<hr>";
echo "<hr>";
echo "<hr>";
$hex = bin2hex($enc);

echo $hex;

exit;

//header('Content-Type: text/html; charset=utf-8');

define("TIMEZONE", "America/Recife");
date_default_timezone_set(TIMEZONE);
require_once "db.php";
require_once "const.php";
require_once "functions.php";
require_once "os-browser-functions.php";
require_once "mp.php";

$date = date("Y-m-d");


            // $recebi = 
            //     $ndb->seri()
            //         ->select("sum(seri.price-seri.discount) as total, max(rechist.data) as data")
            //         ->where("DATE(seri.timestamp) = ? AND ( (seri.__settled_at IS NOT NULL) OR ( ? >= DATE(NOW())) ) OR (MAX(rechist.date) > ?)", array($date, $date, $date))->fetch();

$date = "2021-04-15";

//$qry = $pdo->query("
$sth = $pdo->prepare("
SELECT 
    DATE(s.timestamp), IFNULL(sum(s.price-s.discount), 0) as total, MAX(r.date)
FROM 
    seri s
LEFT JOIN
    rechist r on r.seri_id = s.id

WHERE 
    DATE(s.timestamp) = ? AND ( 

        (s.__settled_at IS NOT NULL) OR 
        (? >= DATE(NOW())) 
    )    
GROUP BY
    DATE(s.timestamp)
");



                //if (!$sth) {
                    echo "\nPDO::errorInfo() #1:\n";
                    print_r($pdo->errorInfo());
                //}

                   
                $sth->execute(array(
                    date($date), 
                    //strtotime($date), 
                    date($date)
                ));
                

                //die('dddd: '.$sth->rowCount());

                if (!$sth) {
                    echo "\nPDO::errorInfo() #2:\n";
                    print_r($pdo->errorInfo());
                    echo "<hr>";
                }


                //$recebi = $qry->fetch();
                $recebi = $sth->fetch();

                if ($recebi) echo "yes"; else echo "no";

var_dump($recebi->total);


// foreach ($recebi as $serial) {
//     echo $serial['total'];
// }

//addSMS(TELEGRAM_FLAVIA , 'TESTE FLÁVIA');
//addSMS(TELEGRAM_RUBINHO , 'TESTE FLÁVIA');


exit;

$browser = $ndb->ai_browser()->where("id = ?", 'unHjgTRXLTM1HzRj')->fetch();

$browser->ai_os["pc_name"] = 'changed';
$browser->ai_os->update();





exit;

foreach ($ndb->setup()->order("id desc") as $setup) {
    
    $ndb->ai_os()->insert(
        array(
            "id" => $setup["osid"],
            "version" => $setup["vers"],
            "pc_name" => $setup["comp"],
            "ip" => $setup["ip"],
            "timestamp" => $setup["timestamp"],
        )
    );

}

foreach ($ndb->download()->where("browser_id is not null")->order("id desc") as $download) {
    
    $setup =
        $ndb->setup()
            ->where("ip = ?", $download["ip"])
            ->order("id desc")
            ->limit(1)
            ->fetch();


    $ndb->ai_browser()->insert(
        array(
            "id"        => $download["browser_id"],
            "name"      => $download["browser"],
            "ip"        => $download["ip"],
            "timestamp" => $download["timestamp"],
            "ai_os_id"  => !!$setup ? $setup["osid"] : NULL,
        )
    );

}

echo "ok";