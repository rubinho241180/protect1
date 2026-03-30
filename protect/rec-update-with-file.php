<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

$rec_id    = $_POST["rec_id"];
$timestamp = date('Y-m-d H:i:s');

require_once "db.php";

$recebimento = $ndb->rechist()->where("id = ?", $rec_id)->fetch();

$json = array(
    "errors"      => array(),
    "post"        => $_POST,
    "is_old_file" => true,
    "file"        => (isset($recebimento["file"])) ? $recebimento["file"] : "",
);

// Configurações S3/MinIO via variáveis de ambiente
$s3_endpoint = getenv('S3_ENDPOINT');
$s3_bucket   = getenv('S3_BUCKET');
$s3_access   = getenv('S3_ACCESS_KEY');
$s3_secret   = getenv('S3_SECRET_KEY');
$s3_region   = getenv('S3_REGION') ?: 'us-east-1';

function s3_upload($endpoint, $bucket, $access, $secret, $region, $key, $file_path, $mime_type) {
    $date         = gmdate('Ymd\THis\Z');
    $date_short   = gmdate('Ymd');
    $host         = parse_url($endpoint, PHP_URL_HOST);
    $content      = file_get_contents($file_path);
    $content_hash = hash('sha256', $content);

    $canonical_request = implode("\n", [
        "PUT",
        "/{$bucket}/{$key}",
        "",
        "content-type:{$mime_type}",
        "host:{$host}",
        "x-amz-content-sha256:{$content_hash}",
        "x-amz-date:{$date}",
        "",
        "content-type;host;x-amz-content-sha256;x-amz-date",
        $content_hash,
    ]);

    $credential_scope = "{$date_short}/{$region}/s3/aws4_request";
    $string_to_sign   = implode("\n", [
        "AWS4-HMAC-SHA256",
        $date,
        $credential_scope,
        hash('sha256', $canonical_request),
    ]);

    $signing_key = hash_hmac('sha256', 'aws4_request',
        hash_hmac('sha256', 's3',
            hash_hmac('sha256', $region,
                hash_hmac('sha256', $date_short, "AWS4{$secret}", true),
            true),
        true),
    true);

    $signature     = hash_hmac('sha256', $string_to_sign, $signing_key);
    $authorization = "AWS4-HMAC-SHA256 Credential={$access}/{$credential_scope}, SignedHeaders=content-type;host;x-amz-content-sha256;x-amz-date, Signature={$signature}";
    $url           = "{$endpoint}/{$bucket}/{$key}";

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_CUSTOMREQUEST  => "PUT",
        CURLOPT_POSTFIELDS     => $content,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER     => [
            "Content-Type: {$mime_type}",
            "Host: {$host}",
            "x-amz-content-sha256: {$content_hash}",
            "x-amz-date: {$date}",
            "Authorization: {$authorization}",
        ],
    ]);

    $response  = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return $http_code === 200;
}

/*
**  FILE
*/
if (isset($_FILES["file"]) && $_FILES["file"]["error"] === UPLOAD_ERR_OK && !empty($_FILES["file"]["tmp_name"])) {
    $filename  = $rec_id . "_" . uniqid();
    $extension = strtolower(pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION));
    $basename  = $filename . "." . $extension;
    $source    = $_FILES["file"]["tmp_name"];
    $mime_type = mime_content_type($source);

    $uploaded = s3_upload(
        $s3_endpoint,
        $s3_bucket,
        $s3_access,
        $s3_secret,
        $s3_region,
        $basename,
        $source,
        $mime_type
    );

    if ($uploaded) {
        $oldfile = $recebimento["file"];
        if ($oldfile) {
            // opcional: implementar delete do S3 aqui
        }

        $recebimento["file"] = $basename;
        $recebimento->update();
        $json["is_old_file"] = false;
        $json["file"]        = $basename;
        $json["url"]         = "{$s3_endpoint}/{$s3_bucket}/{$basename}";
    } else {
        array_push($json["errors"], "Falha no upload para o S3.");
    }
}

if (is_null($recebimento["confirmed"])) {
    $recebimento["confirmed"] = $timestamp;
    $recebimento->update();
}

if (isset($_POST["formated"])) echo "<pre>";
echo json_encode($json, JSON_PRETTY_PRINT);
if (isset($_POST["formated"])) echo "</pre>";