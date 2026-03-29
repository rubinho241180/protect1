<?php
/**
 * shaCrypt
 * v1.1
 * Author: Mark Holtzhausen <nemesarial@gmail.com>
 * URL: http://codeonfire.cthru.biz
 * 
 * This is a for fun encryption package not intended to use in a production environment
 * but rather as proof of concept. I am not a cryptologist - so if you have comments, make them
 * understandable to a layman.
 * 
 * It is rather slow (in it's current form) able to handle about 22KB per second.
 * In a 10KB string it will use about 2500 hashes dependent on what you set the rehashSize value.
 * It is not optimized for speed and is merely a showcase for a concept.
 * 
 * In a following build, the rehashSize might become a function of the current hash, rather than a set
 * value. This variable rehash size will further diminish predictability.
 * 
 * Some steps are included to hide the format of the stored data in order to make algorithmic brute
 * force attempts orders more complex.
 * 
 * Random bytes are injected in the header to obfuscate the starting byte of the meaningful portion of
 * the string. This means:
 * 1) Encrypting the same string twice will render completely different strings.
 * 2) Encrypting the same string twice will render encrypted strings of different sizes.
 * The number of random bytes injected can be capped using the randombytemax static variable.
 */

class shaCrypt{
	
	public static $useGzip=true;				//Gzip Flag
	public static $randombytemax=128;			//Set the maximum number of random bytes used to obfuscate
	public static $rehashSize=40;				//Set the number of hash-bytes to use before rehashing
	
	public static function encode($string=NULL,$key=NULL,$b64=false){

		//Applying Gzip to obfuscate & shorten message
		if(self::$useGzip && function_exists('gzcompress'))$string=base64_encode(gzcompress($string));

		//Padding meaningful package start byte
		$pack=array();
		$pack[]=str_pad('',rand(1,self::$randombytemax/2),sha1(rand()));
		$pack['payload']=$string;
		
		$string=str_pad('',rand(1,self::$randombytemax/2),sha1(rand())).json_encode($pack);
			
		$string=base64_encode($string);
		
		
		//Start hash encryption
		$hkey=sha1($key,$full);
		$ret='';
		$ptr=0;
		for($i=0; $i<strlen($string);$i++){
			$chr=ord($string[$i]);
			$mod=ord($hkey[$ptr++]);
			$ret.=chr($chr ^ $mod);
			if($ptr>=self::$rehashSize){
				$hkey=sha1($hkey,$full);
				$ptr=0;
			}
		}
		
		//If requested, output format into 74byte wide base64
		if($b64)$ret=chunk_split(base64_encode($ret),74);
		
		//Returning encrypted string
		return $ret;
	}
	
	
	public static function decode($string=NULL,$key=NULL,$b64=false){
		//If requested, base64 decode string
		if($b64)$string=base64_decode($string);
		
		//Start hash decryption
		$hkey=sha1($key);
		$ret='';
		$ptr=0;
		for($i=0; $i<strlen($string);$i++){
			$chr=ord($string[$i]);
			$mod=ord($hkey[$ptr++]);
			$ret.=chr($chr ^ $mod);
			if($ptr>=self::$rehashSize){
				$hkey=sha1($hkey);
				$ptr=0;
			}
		}
		
		$string=base64_decode($ret);
		
		//Remove random bytes
		$string=substr($string,strpos($string,'{'));
		
		//Unwrap 
		$string=json_decode($string,true);
		$ret=NULL;
		if(is_array($string)){
			$ret=$string['payload'];
			//Decompress
			if(self::$useGzip&&function_exists('gzuncompress'))$ret=gzuncompress(base64_decode($ret));
		}
		
		//Return decoded string
		return $ret;
	}

}

?>