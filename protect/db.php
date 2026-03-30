<?php
function connect_pdo() {
    $dt = new DateTime();
    $offset = $dt->format("P");
    $offset = "-03:00";
    
    $host = getenv('DB_HOST');
    $dbname = getenv('DB_NAME');
    $user = getenv('DB_USER');
    $pass = getenv('DB_PASS');
    
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass,
        array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT,
            PDO::ATTR_PERSISTENT => false,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET time_zone = '$offset'",
        )
    );
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
    
    if(!$db) die('Erro ao criar a conexão');
    return $db;
}
require_once "NotORM.php";
$pdo = connect_pdo();
$ndb = new NotORM($pdo);
function ndb() {
    return new NotORM(connect_pdo());
}