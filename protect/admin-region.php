<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//header('Content-Type: application/json');
//ob_start();

require_once "db.php";


$estados = array(
'AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO'
);

$downloads = 
    $ndb->download()
        ->select("region, count(distinct ip) as total")
        ->where("region is not null AND LENGTH(region) = 2 AND (NOT region REGEXP '[0-9]+')")
        ->group("region")
        ->order("count(distinct ip)");

$dataPoints = [
];

foreach ($downloads as $download) {

    if (in_array($download['region'], $estados))

    array_push(
        $dataPoints, 
        array(
            "label" => $download['region'],
            "total" => $download['total'],
        )
    );

}        

$total = 0;

foreach ($dataPoints as $data) {
    $total = $total + intval($data["total"]);
}

foreach ($dataPoints as &$data) {
    $data["y"] = round((intval($data["total"]) * 100) / $total, 1);
    //$data["y"] = number_format($data["y"], 1, ',', '.');
}

?>


<script type="text/javascript">

    var myyy = <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK ); ?>;
    //myyy.data[0].indexLabel = '';
    //myyy.data[1].indexLabel = '';
    console.log(myyy);

</script>


<!DOCTYPE HTML>
<html>
<head>
<script>
window.onload = function() {

var chart = new CanvasJS.Chart("chartContainer", {
    theme: "light2", // "light1", "light2", "dark1", "dark2"
    exportEnabled: true,
    animationEnabled: true,
    //height: 500,
    title: {
        text: "Downloads by Region"
    },
    data: [{
        type: "pie",
        startAngle: 25,
        toolTipContent: "<b>{label}</b>: {y}%",
        showInLegend: "true",
        legendText: "{label}",
        indexLabelFontSize: 16,
        indexLabel: "{label}: {y}%",
        dataPoints: myyy
    }]
});
chart.render();

}
</script>
</head>
<body>
<div id="chartContainer" style="height: 300px; width: 100%;"></div>
<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
</body>
</html>