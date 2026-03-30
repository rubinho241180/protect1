<?php 



function AES_Rijndael_Decrypt($data, $key, $iv) {
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

function AES_Rijndael_Encrypt($data, $key, $iv) {
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
