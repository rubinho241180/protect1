<?php 

error_reporting( E_ALL );
ini_set('display_errors', 1);


require 'FileDownloadClass.php';

$download_path = dirname( dirname(__FILE__) )."/download/";
$download_path = "../download/";
$file = "sender_TSI901_install.exe";


$fileDownload = FileDownload::createFromFilePath($download_path.$file);
$fileDownload->sendDownload($file);