<?php
ob_start(); // Inicia o buffer de saída para evitar problemas com headers
header_remove("Content-Type");
header("Content-Type: application/json; charset=utf-8");

error_reporting(E_ALL);
ini_set('display_errors', 1);

$appid  = isset($_GET["appid"]) ? $_GET["appid"] : null;
$appver = isset($_GET["appver"]) ? $_GET["appver"] : null;

if (!$appid || !$appver) {
    echo json_encode(array("error" => "Parâmetros 'appid' e 'appver' são obrigatórios"));
    exit;
}

require_once "db.php";

$json = array("versions" => array());

try {
    $versions = $ndb->ver()
        ->where("app_id = ? AND id > ?", array($appid, $appver))
        ->order('id');

    foreach ($versions as $ver) {
        $json["versions"][] = array(
            "date"      => date('Y-m-d', strtotime($ver["timestamp"])),
            "int"       => (int) $ver["id"],
            "formatted" => $ver["major"] . "." . $ver["minor"] . "." . $ver["revision"],
            "content"   => $ver["md"],
        );
    }

    echo json_encode($json, JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    echo json_encode(array("error" => "Erro ao buscar versões: " . $e->getMessage()));
}

// Garante que todo o conteúdo seja enviado corretamente
ob_end_flush();
