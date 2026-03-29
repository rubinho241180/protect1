<?php 
    // setup the URL, the JavaScript and the form data
    $url = 'https://javascript-minifier.com/raw';
    $js = file_get_contents('resource/301/5.0.0/run.js');
    $data = array(
        'input' => $js,
    );

//echo "<pre>";
//echo $js;
//echo "</pre>";


function getMinified($url, $content) {
    $postdata = array('http' => array(
          'method'  => 'POST',
          'header'  => 'Content-type: application/x-www-form-urlencoded',
          'content' => http_build_query( array('input' => $content) ) ) );
    return file_get_contents($url, false, stream_context_create($postdata));
  }

$url = 'https://javascript-minifier.com/raw';
$js = file_get_contents('resource/301/5.0.0/run.js');

$t = microtime(true);

$min =  getMinified($url, $js);

//echo round(microtime(true) - $t,3);
//echo "<hr>";

echo $r;

exit;

    // init the request, set some info, send it and finally close it
    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $minified = curl_exec($ch);

    curl_close($ch);

    // output the $minified
    echo $minified;





exit;

echo substr(date("Y-m-d H-i", time()-date("Z")), 0, -1)."x.12345678-101:1";
exit;


error_reporting(E_ALL);
ini_set('display_errors', 1);

error_reporting(E_STRICT);

// setup the URL, the JavaScript and the form data
    $url = 'https://javascript-minifier.com/raw';
    $js = file_get_contents('resource/301/5.0.0/contact_const.js');
    $data = array(
        'input' => $js,
    );

    // init the request, set some info, send it and finally close it
    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $minified = curl_exec($ch);

    // output the $minified
    echo "min: $minified";

    curl_close($ch);









exit;

require_once "lib/shaCrypt.class.php";

$ky = date("Y-m-d H-i", time()-date("Z")).".12345678-101:1";



$enc = shaCrypt::encode("amor!123456789012345678901234567890", $ky, true);
$dec = shaCrypt::decode($enc, $ky, true);

$json = 
array(
  "ky" => str_pad('046b782b',16,@chr(0)),
  "shaCrypt::encode" => $enc,
  "shaCrypt::decode" => $dec,
);



//if (isset($_GET["formated"]))
  echo "<pre>";
echo json_encode($json, JSON_PRETTY_PRINT/*+JSON_UNESCAPED_UNICODE*/);
//var_dump($json);

//if (isset($_GET["formated"]))
  echo "</pre>";


exit;


$tx = "amor!";
$ky = "12345678";//date("Y-m-d H-i", time()-date("Z")).".12345678-101:1";
$iv = "12345678"; //date("Y-m-d H-i", time()-date("Z")).".12345678-101:2";


echo $ky;

require_once "rijndael.php";
require_once "crc16.php";


echo "<hr>";
echo $tx;

echo "<hr>";
$cr = AES_Rijndael_Encrypt("amor!", $ky, $iv);
echo $cr;
echo "<hr>";
echo AES_Rijndael_Decrypt($cr, $ky, $iv);



echo "<hr>";
echo dechex(crc16b("amor!"));
echo "<hr>";
echo CRC16HexDigest("amor!");
echo "<hr>";
echo dechex(crc32($ky));







function Encrypt($src, $key, $iv)
{
  $block = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, 'cbc');
  //echo "Block size: " . $block . "\r\n";
  $pad = $block - (strlen($src) % $block);
  $src .= str_repeat(chr($pad), $pad);  

  $enc = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $src, MCRYPT_MODE_CBC, $iv);
  $r = base64_encode($enc);
  return $r;
}

function Decrypt($src, $key, $iv)
{
  $enc = base64_decode($src);
  $dec = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $enc, MCRYPT_MODE_CBC, $iv);

  $block = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, 'cbc');
  $pad = ord($dec[($len = strlen($dec)) - 1]);
  return substr($dec, 0, strlen($dec) - $pad);
}


echo "<hr>";
$i = Encrypt("amor!", "955f94fd", "0c56c547");
echo "Enc: ".$i;
echo "<hr>";

$o = Decrypt("amor!", "955f94fd", "0c56c547");
echo "Dec: ".$o;



