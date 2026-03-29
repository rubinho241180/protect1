<?php 

  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);
  error_reporting(E_ALL);


$key = "mykey";
$iv = "myiv";
$plaintext = "amor";

    
require_once "rijndael.php";
require_once "mcrypt_compat/lib/mcrypt.php";
//require_once "phpseclib3/lib/mcrypt.php";

$encrypted = AES_Rijndael_Encrypt("amor", $key, $iv);

echo 'enc1: ' . $encrypted;
echo "<hr>";
echo 'dec1: ' . AES_Rijndael_Decrypt($encrypted, $key, $iv);

echo "<hr>";
echo 'enc2: ' . encrypt_openssl("amor", $key, $iv);

echo "<hr>";
echo 'dec2: ' . decrypt_openssl($encrypted, $key, $iv);

echo "<hr>";


$key = str_pad($key,16,@chr(0));
$iv  = str_pad($iv, 16,@chr(0));

echo $key;

echo "<hr>";
echo "$iv";
echo "<hr>";
echo strlen($iv);

/*
$compat = phpseclib_mcrypt_encrypt('rijndael-128', $key, $plaintext, 'cbc', $iv);

echo "<hr>";
echo "compact: $compat";
*/



/*
require_once "phpseclib3/phpseclib/bootstrap.php";

$rijndael = new phpseclib3\phpseclib\Crypt\Rijndael(phpseclib3\phpseclib\Crypt\Rijndael::MODE_ECB);
$rijndael->setKey($key);
$rijndael->setKeyLength(128);
$rijndael->disablePadding();
$rijndael->setBlockLength(128);

$decoded = $rijndael->encrypt($plaintext);

echo "<hr>";
echo "compact: $decoded";
*/


/*
//$key previously generated safely, ie: openssl_random_pseudo_bytes
$plaintext = "message to be encrypted";
$ivlen = openssl_cipher_iv_length($cipher="AES-128-CBC");
$iv = openssl_random_pseudo_bytes($ivlen);
$ciphertext_raw = openssl_encrypt($plaintext, $cipher, $key, $options=OPENSSL_RAW_DATA, $iv);
$hmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary=true);
$ciphertext = base64_encode( $iv.$hmac.$ciphertext_raw );

//decrypt later....
$c = base64_decode($ciphertext);
$ivlen = openssl_cipher_iv_length($cipher="AES-128-CBC");
$iv = substr($c, 0, $ivlen);
$hmac = substr($c, $ivlen, $sha2len=32);
$ciphertext_raw = substr($c, $ivlen+$sha2len);
$original_plaintext = openssl_decrypt($ciphertext_raw, $cipher, $key, $options=OPENSSL_RAW_DATA, $iv);
$calcmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary=true);


if (hash_equals($hmac, $calcmac))//PHP 5.6+ timing attack safe comparison
{
   // echo $original_plaintext."\n";
}
*/