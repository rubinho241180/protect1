<?php 

header('Content-Type: application/json');


require "db.php";


function get_jwt($url, $token)
{

    $curl = curl_init();

    curl_setopt_array($curl, array(
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_HTTPHEADER => array(
            "Authorization: Bearer $token"
      ),
     ));

    $response = curl_exec($curl);
    //$data = json_decode($response, true);

    if (curl_errno($curl)) {
        $error_msg = curl_error($curl);
        var_dump($error_msg);
    }
    curl_close($curl);


    //echo $data;

    return $response;

}


$phone = isset($_GET['phone']) ? $_GET['phone'] : '';

if (substr($phone, 0, 2) == '55')
{
    $ddi    = substr($phone, 0, 2 );
    $ddd    = substr($phone, 2, 2 );
    $num    = substr($phone, 4, 20);

    $num    = (strlen($num)  > 8) ? substr($phone, -8) : $num; 
    $phoneX = "{$ddi}{$ddd}%{$num}";
    $phone9 = "{$ddi}{$ddd}9{$num}";
}



$subscribers = 
    $ndb->cus()->where("CONCAT(ddi, phone) LIKE ?", $phoneX);

$json = 
    array(
        "phoneX"   => $phoneX,
        "phone9"   => $phone9,
        "protect"  => [],
        "manychat" => [],
    );


if ($subscribers)
{

    foreach ($subscribers as $subscriber)
    {
        $installations
            = [];

        foreach ($subscriber->ins() as $installation)
        {
            array_push(
                $installations, 
                array(
                    "device" => $installation['mac_id'],
                    "application" => $installation['appl_id'],
                )
            );
        }


        array_push(
            $json['protect'], 
            array(
                "id"    => intval($subscriber['id']),
                "name"  => $subscriber['name'],
                "email" => $subscriber['email'],
                "city"  => $subscriber['city'],
                "state" => $subscriber['state'],
                "date"  => date('d/m/Y', strtotime($subscriber['timestamp'])),
                "installations" => $installations,
            )
        );
    }
}

$url      = "https://api.manychat.com/fb/subscriber/findBySystemField?phone=".urlencode("+{$phone9}");
$manychat = get_jwt($url, '353637:bb3126f80015f4513cb0a1c1da179e9d');
$manychat = json_decode($manychat, TRUE);

if (empty($manychat['data']))
    $json['manychat'] = []; else
    $json['manychat'] = array( $manychat['data'] );





echo json_encode($json);