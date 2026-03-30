<?php 
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

//define("TIMEZONE", "America/Recife");
//date_default_timezone_set(TIMEZONE);

header('Content-Type: application/json');
ob_start();

require_once "db.php";

$ip = "SUBSTRING_INDEX(ip, '.', 3)";

$hoje = date("Y-m-d", strtotime("-9 days"));
$hoje = date("Y-m-d");

echo "today: ".$hoje;
//$ip = "ip";

//echo $ip;
//exit;

$months = [
    //date("Y-m", strtotime("-2 month")) => [],
    date("Y-m", strtotime("-1 month")) => [],
    date("Y-m") => [],
];

$m0 = date("Y-m", strtotime("-2 month"));
$m1 = date("Y-m", strtotime("-1 month"));
$m2 = date("Y-m");

$months2 = [
    "$m0" => [
        //"month" => $m1,
        "label" => $m0,
        "color" => "#a1a1a7",
        "lineDashType" => "dotted",
        "markerType" => "square",
        "type"  => "line",
        "days" => [
            //
        ]
    ],
    "$m1" => [
        //"month" => $m1,
        "label" => $m1,
        "color" => "#FFD700",
        "lineDashType" => "solid",
        "markerType" => "square",
        "type"  => "line",
        "days" => [
            //
        ]
    ],
    "$m2" => [
        //"month" => $m2,
        "label" => $m2,
        "color" => "#FF4500",
        "lineDashType" => "solid",
        "markerType" => "square",
        "type"  => "line",
        "days" => [
            //
        ]
    ]
];


//M-1
foreach ($months as $month => $info) {
    
    $total = 0;
    $qt_vl = 0;
    $tt_vl = 0;

    for ($x = 1; $x <= 31/*date('d')*/; $x++) {
      
        $date = $month."-".$x;

        //if (strtotime($date) <= strtotime(date('Y-m-d')))
        if (strtotime($date) <= strtotime($hoje))
        {

            $recebi = 
                $ndb->rechist()
                    ->select("sum(value-discount) as total")
                    ->where("date = ? AND ( (confirmed IS NOT NULL) OR ( ? >= DATE(NOW())) )", array($date, $date))->fetch();


            if ($recebi) {
                $total = $total + $recebi['total'];

                $qt_vl++;
                $tt_vl = $total;
            }

        } else {

            //$total = $total + ($tt_vl / $qt_vl);
            $total = bcadd($total, ($tt_vl / $qt_vl), 2);
        }
        


        if ($month == date('Y-m')) {

            //try {

            $t_passado = floatval($months2[$m1]['days'][$x-1]['total']);

            //}

            //catch(Exception $e) {
            //   $t_passado = 1;
                //echo 'Message: ' .$e->getMessage();
            //}            


            if ($t_passado > 0)
                $perct = ($total*100)/$t_passado; else
                $perct = 0;




            
        } else {

            $perct = 0;
        }

        array_push(
            $months2[$month]["days"], 
            array(
                "value" => floatval($recebi['total']), 
                "total" => $total,
                "perct" => number_format($perct, 0, ',', '.'),
            )
        );


        //end($months2[$month]['days'])['perct'] = 155;
    }
}

//M-2


//echo json_encode($months2);


//exit;


$dataPoints = [
    "period" => [
        
        array(
            "from"  => "2020-10-01",
            "to"    => "2020-10-15",
            "label" => "01-15/out",
            "color" => "#FFD700",
            "lineDashType" => "solid",
            "markerType" => "square",
            "type"  => "line",
        ),
        array(
            "from"  => "2020-10-16",
            "to"    => "2020-10-31",
            "label" => "16-30/out",
            "color" => "#FFA500",
            "lineDashType" => "solid",
            "markerType" => "square",
            "type"  => "line",
        ),
        
        array(
            "from"  => "2020-11-01",
            "to"    => "2020-11-15",
            "label" => "01-15/nov",
            "color" => "#FF4500",
            "lineDashType" => "solid",
            "markerType" => "square",
            "type"  => "line",
        ),
    ], 
    "data" => []
];


foreach ($months2 as $month) {

    $days = [];

    $d = 1;

    foreach ($month["days"] as $day) {
      
        array_push(
            $days, 
            array(
                "label" => $d, 
                "y" => $month["days"][$d-1]["total"],
                "p" => $month["days"][$d-1]["perct"]
            )
        );  

        $d++;
    }
    # code...

    array_push(
        $dataPoints["data"], 
        array(
            "type" => $month["type"],
            "showInLegend" => true,
            "name" => $month["label"],
            "lineDashType" => $month["lineDashType"],
            "markerType" => $month["markerType"],
            //xValueFormatString: "DD MMM, YYYY",
            "color" => $month["color"],
            //yValueFormatString: "#,##0K",
            //yValueFormatString: "0%",
            "toolTipContent" => "<b style='\"'color: {color};'\"'>{name}</b> </br> R$ {y}<br>",
            //"toolTipContent" => "<b style='\"'color: {color};'\"'>{name}</b> </br> Qt: {qt} </br> Step: {y}% </br> Loss: {loss}% <br>",
            "indexLabel" => "{p}%",
            "dataPoints" => 


            $days
        )
    );
}

echo json_encode($dataPoints);

  $resultado = ob_get_contents();
  ob_end_clean();
?>


<script type="text/javascript">

    var myyy = <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK ); ?>;
    myyy.data[0].indexLabel = '';
    myyy.data[1].indexLabel = '';
    //myyy.data[1].indexLabel = '';
    console.log(myyy);

</script>


<!DOCTYPE HTML>
<html>
<head>
<script>
window.onload = function () {

var options = {
    height: 360,
    animationEnabled: true,
    theme: "light2",
    title:{
        text: "Mes Atual vs Mes Anterior — Receipts"
    },
    //axisX:{
    //    valueFormatString: "DD MMM"
    //},
    /*
    axisX:{
        //valueFormatString: "DD MMM",
        crosshair: {
            enabled: true,
            snapToDataPoint: true
        }
    },
    */
    axisY: {
        title: "VENDAS",
        suffix: "",
        prefix: "R$ ",
        //interval: 5,
        //viewportMinimum: -50,
        //viewportMaximum: 50
    },
    
    toolTip:{
        shared:true
    },  
    legend:{
        cursor:"pointer",
        verticalAlign: "bottom",
        horizontalAlign: "left",
        dockInsidePlotArea: true,
        itemclick: toogleDataSeries
    },
    data: myyy["data"]
};
console.log(options);

$("#chartContainer").CanvasJSChart(options);

function toogleDataSeries(e){
    if (typeof(e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
        e.dataSeries.visible = false;
    } else{
        e.dataSeries.visible = true;
    }
    e.chart.render();
}

}
</script>
</head>
<body>
    <div id="chartContainer" style="height: 300px; width: 100%;"></div>
    <script src="https://canvasjs.com/assets/script/jquery-1.11.1.min.js"></script>
    <script src="https://canvasjs.com/assets/script/jquery.canvasjs.min.js"></script>
    <div style="padding-top:90px;">
        <!--?= //$resultado; ?-->
    </div>
    <a href="http://r2.rfidle.com/protect/admin/month" style="color:blue;">SALES</a> — <span style="color:gray;">RECEIPTS</span>
</body>
</html>


