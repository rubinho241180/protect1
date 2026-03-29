<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Inclua os arquivos da biblioteca phpredis
require 'predis-1.1.10/autoload.php';

Predis\Autoloader::register();

$options = [
    'host'     => 'redis-14330.c308.sa-east-1-1.ec2.cloud.redislabs.com',
    'port'     => 14330,
    'password' => 'yiXLwRds7cnggJYRtCTyxwYZNYdAyMBX',
];


$client = new Predis\Client($options);
$client->set('foo', 'bar');
$value = $client->get('foo2');

echo gettype(isset($value));