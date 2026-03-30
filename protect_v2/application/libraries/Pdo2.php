<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pdo2 {
    public function __construct() {
        $CI = & get_instance();
        if (!isset($CI->pdo2)) {
            $CI->pdo2 = $this->pdo_connect();
        }
    }

    public function pdo_connect() {
        $offset = "-03:00";

        $host   = getenv('DB_HOST');
        $dbname = getenv('DB_NAME');
        $user   = getenv('DB_USER');
        $pass   = getenv('DB_PASS');

        return new PDO(
            "mysql:host=$host;dbname=$dbname;charset=utf8",
            $user,
            $pass,
            array(
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_PERSISTENT         => false,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET time_zone = '$offset'",
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
            )
        );
    }

    public function mypdo() {
        $CI = & get_instance();
        if (!isset($CI->pdo2)) {
            $CI->pdo2 = $this->pdo_connect();
        }
        return $CI->pdo2;
    }
}