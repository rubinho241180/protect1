<?php 


$ref = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : ':(';

echo $ref;

exit;


echo "<pre>";
echo json_encode($json, JSON_PRETTY_PRINT+JSON_UNESCAPED_UNICODE);
echo "</pre>";
