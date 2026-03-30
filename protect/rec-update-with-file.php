<?php

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

$rec_id = $_POST["rec_id"];
$timestamp = date('Y-m-d H:i:s');

require_once "db.php";



$recebimento = $ndb->rechist()->where("id = ?", $rec_id)->fetch();


$json =
array(
    //"updated" => true,
    "errors" => array(),
    "post" => $_POST,
    "is_old_file" => true,
    "file" => (isset($recebimento["file"])) ? $recebimento["file"] : "",
);


/*
**  FILE
*/
if (isset($_FILES["file"]))
{

    /* create new name file */
    $filename   = uniqid() . "-" . time(); // 5dab1961e93a7-1571494241
    $filename   = $rec_id . "_" . uniqid();// . "_" . time();
    $extension  = pathinfo( $_FILES["file"]["name"], PATHINFO_EXTENSION ); // jpg
    $basename   = strtolower($filename . "." . $extension); // 5dab1961e93a7_1571494241.jpg
    $directory  = "uploads/rechist/";     
    $source       = $_FILES["file"]["tmp_name"];
    $destination  = "{$directory}{$basename}";

    if (move_uploaded_file($source, $destination))
    {

        //if (!!$recebimento)
        //{
            $oldfile = $recebimento["file"];

            if (is_file($directory.$oldfile))
            {
                unlink($directory.$oldfile);
            }            


            $recebimento["file"] = $basename;
            $recebimento->update();

            $json["is_old_file"] = false;
            $json["file"] = $basename;

        //} else {

        //    array_push(
        //        $json["errors"],
        //        $pdo->errorInfo()
        //    );
        //}
    }    
}




if (is_null($recebimento["confirmed"]))
{
    $recebimento["confirmed"] = $timestamp;    
    $recebimento->update();
}



//JSON
if (isset($_POST["formated"]))
    echo "<pre>";
echo json_encode($json, JSON_PRETTY_PRINT);

if (isset($_POST["formated"]))
    echo "</pre>";

