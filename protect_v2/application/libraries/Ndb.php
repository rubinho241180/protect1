<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ndb {
    public function __construct() {
        $CI = & get_instance();
        if (!isset($CI->db)) {
            $CI->db = $this->connect();
        }
    }

    public function connect() {
        $offset = "-03:00";

        $host   = getenv('DB_HOST');
        $dbname = getenv('DB_NAME');
        $user   = getenv('DB_USER');
        $pass   = getenv('DB_PASS');

        $db = new PDO(
            "mysql:host=$host;dbname=$dbname;charset=utf8",
            $user,
            $pass,
            array(
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_PERSISTENT         => false,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET time_zone = '$offset'",
            )
        );

        $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);

        if (!$db) {
            die('Erro ao criar a conexão');
        }

        require_once "NotORM.php";
        return new NotORM($db);
    }
}