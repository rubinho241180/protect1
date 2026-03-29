<?php 

function client_ip() {

    if (isset($_SERVER)) {

        if (isset($_SERVER["HTTP_X_FORWARDED_FOR"]))
            return $_SERVER["HTTP_X_FORWARDED_FOR"];

        if (isset($_SERVER["HTTP_CLIENT_IP"]))
            return $_SERVER["HTTP_CLIENT_IP"];

        return $_SERVER["REMOTE_ADDR"];
    }

    if (getenv('HTTP_X_FORWARDED_FOR'))
        return getenv('HTTP_X_FORWARDED_FOR');

    if (getenv('HTTP_CLIENT_IP'))
        return getenv('HTTP_CLIENT_IP');

    return getenv('REMOTE_ADDR');
}


function ip_to_geo($ip)
{
    $geo = curl_get_contents("http://api.ipstack.com/{$ip}?access_key=8d9de96b722e80c481a935c4db1b0cfd");
    return json_decode($geo);
}


function curl_get_contents($url) {
    $ch = curl_init();
    $timeout = 5;

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);

    $data = curl_exec($ch);

    curl_close($ch);

    return $data;
}


function dateDiff($start, $end) {
    $start_ts = $start;//strtotime($start);
    $end_ts = $end;//strtotime($end);
    $diff = $end_ts - $start_ts;
    return round($diff / 86400);
}

function is_email2($email){
    //verifica se e-mail esta no formato correto de escrita
    if (!ereg('^([a-zA-Z0-9.-_])*([@])([a-z0-9]).([a-z]{2,3})',$email)){
        $mensagem='E-mail Inv&aacute;lido!';
        return $mensagem;
    }
    else{
        //Valida o dominio
        $dominio=explode('@',$email);
        if(!checkdnsrr($dominio[1],'A')){
            $mensagem='E-mail Inv&aacute;lido!';
            return $mensagem;
        }
        else{return true;} // Retorno true para indicar que o e-mail é valido
    }
}

function is_mail($email){
    $er = "/^(([0-9a-zA-Z]+[-._+&])*[0-9a-zA-Z]+@([-0-9a-zA-Z]+[.])+[a-zA-Z]{2,6}){0,1}$/";
    if ((trim($email != '')) && (preg_match($er, $email))){
    return true;
    } else {
    return false;
    }
}

function only_numbers($str) {
    return preg_replace("/[^0-9]/", "", $str);
}



function change_key( &$array, $old_key, $new_key) {

    if( ! array_key_exists( $old_key, $array ) )
        return $array;

    $keys = array_keys( $array );
    $keys[ array_search( $old_key, $keys ) ] = $new_key;

    //return array_combine( $keys, $array );
    $array = array_combine( $keys, $array );
}



function addSMS($to, $text) {
    require_once "db.php";
    $pdo = connect_pdo();
    //$qry = $pdo->exec("insert into sms (target, text) values ('$to', '$text')");

    $sql = "insert into sms (target, text) values (:to, :text)";
    $sta = $pdo->prepare($sql);

    $tgs = explode(",", $to);


    foreach ($tgs as $key => $value) {

        $qry = $sta->execute(
                    array(
                        "to" => $value,
                        "text" => $text,
                    )
               );
    }




    if (!$qry) {
        var_dump($pdo->errorInfo());
    } 

}



function _crawlerDetect($USER_AGENT)
{

    $crawlers = array(
        'TelegramBot (like TwitterBot)',
    );


    // to get crawlers string used in function uncomment it
    // it is better to save it in string than use implode every time
    // global $crawlers
     $crawlers_agents = implode('|',$crawlers);
    //$crawlers_agents = 'Google|msnbot|Rambler|Yahoo|AbachoBOT|accoona|AcioRobot|ASPSeek|CocoCrawler|Dumbot|FAST-WebCrawler|GeonaBot|Gigabot|Lycos|MSRBOT|Scooter|AltaVista|IDBot|eStyle|Scrubby';
 
    if ( strpos($crawlers_agents , $USER_AGENT) === false )
       return false;
    // crawler detected
    // you can use it to return its name
    /*
    else {
       return array_search($USER_AGENT, $crawlers);
    }
    */
}

function crawlerDetect($USER_AGENT)
{

    $crawlers = array(
        'Telegram',
        'WhatsApp',
        'google',
        'semrush',
        'DotBot',
        'Crawler',
        'bing'
    );
    
    // See if one of the identifiers is in the UA string.
    foreach ($crawlers as $each)
    {
        if (strpos(strtolower($USER_AGENT), strtolower($each)) !== FALSE)
        {
            return TRUE;
        }
    }

  return FALSE;
  
}
 

function randomStr($length = 32)
{
    return substr(sha1(rand()), 0, $length);
}

function readable_random_string($length = 6)
{  
    $string = '';
    $vowels = array("a","e","i","o","u");  
    $consonants = array(
        'b', 'c', 'd', 'f', 'g'/*, 'h'*/, 'j'/*, 'k'*/, 'l', 'm', 
        'n', 'p', 'r', 's', 't', 'v'/*, 'w'*/, 'x'/*, 'y'*/, 'z'
    );  

    $max = $length / 2;
    for ($i = 1; $i <= $max; $i++)
    {
        $string .= $consonants[rand(0, sizeof($consonants)-1)];
        $string .= $vowels[rand(0,4)];
    }

    return $string;
}

function generateRandomString($length = 60) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}


function browserId() {

    $cookie_options = array(
      'Expires' => 2147483647,//ime() + 60*60*24*30,
      //'Path' => '/',
      //'domain' => '.domain.com', // leading dot for compatibility or use subdomain
      'Secure' => true, // or false
      'HttpOnly' => false, // or false
      'SameSite' => 'None' // None || Lax || Strict
    );    


    /*
    ** BROWSER_ID *SESSION*
    */
    if (isset($_COOKIE['browser_id'])) {

      $vbrowser_id = $_COOKIE['browser_id'];
    } else {

      $vbrowser_id = generateRandomString(16);
      //setcookie("browser_id", $vbrowser_id, 2147483647, "/; SameSite=None; Secure");
      //setcookie("browser_id", $vbrowser_id, $cookie_options);
      setcookie("browser_id", $vbrowser_id, 2147483647, "/", "r2.rfidle.com", false, true);
    }

    return $vbrowser_id;
}

//echo readable_random_string();