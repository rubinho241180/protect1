<?php 


function AES_Rijndael_Encrypt($data, $key, $iv)
{
    return encrypt_openssl($data, $key, $iv);
}

function AES_Rijndael_Decrypt($data, $key, $iv)
{
    return decrypt_openssl($data, $key, $iv);
}



function AES_Rijndael_Encrypt_old($data, $key, $iv) {
        $key = str_pad($key,16,@chr(0));
        $iv  = str_pad($iv, 16,@chr(0));
        
        /*
        echo $key;
        echo "<hr>";
        echo $iv;
        echo "<hr>";
        */

        return 
            mcrypt_encrypt(
                MCRYPT_RIJNDAEL_128,
                $key,
                $data,
                MCRYPT_MODE_CBC,
                $iv
            );        
}

function AES_Rijndael_Decrypt_old($data, $key, $iv) {
        $key = str_pad($key,16,@chr(0));
        $iv  = str_pad($iv, 16,@chr(0));

        //hex to str
        $data = pack("H*", $data);

        return 
            mcrypt_decrypt(
                MCRYPT_RIJNDAEL_128, 
                $key, 
                $data, 
                MCRYPT_MODE_CBC, 
                $iv
            );
}



function encrypt_openssl($msg, $key, $iv = null) {

    $key = str_pad($key,16,@chr(0));
    $iv  = str_pad($iv, 16,@chr(0));


    //$iv_size = openssl_cipher_iv_length('AES-128-CBC');
    //if (!$iv) {
    //    $iv = openssl_random_pseudo_bytes($iv_size);
    //}
    
    $encryptedMessage = openssl_encrypt($msg, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $iv);
    return $encryptedMessage;//base64_encode($iv . $encryptedMessage);
}

function decrypt_openssl($msg, $key, $iv = null) {

    $key = str_pad($key,16,@chr(0));
    $iv  = str_pad($iv, 16,@chr(0));


    //$iv_size = openssl_cipher_iv_length('AES-128-CBC');
    //if (!$iv) {
    //    $iv = openssl_random_pseudo_bytes($iv_size);
    //}
    
    $decryptedMessage = openssl_decrypt($msg, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $iv);
    return $decryptedMessage;//base64_encode($iv . $encryptedMessage);
}