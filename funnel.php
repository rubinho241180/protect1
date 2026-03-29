<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//define("TIMEZONE", "America/Recife");
//date_default_timezone_set(TIMEZONE);

//header('Content-Type: application/json');
ob_start();

require_once "db.php";

$ip = "SUBSTRING_INDEX(ip, '.', 3)";
$ip = "ip";

//echo $ip;
//exit;


$dataPoints = [
    "period" => [
        
        array(
            "from"  => "2021-04-16",
            "to"    => "2021-04-30",
            "label" => "16-30/abri",
            "color" => "#FFD700",
            "lineDashType" => "solid",
            "markerType" => "square",
            "type"  => "line",
        ),
        array(
            "from"  => "2021-05-01",
            "to"    => "2021-05-15",
            "label" => "01-15/mai",
            "color" => "#FFA500",
            "lineDashType" => "solid",
            "markerType" => "square",
            "type"  => "line",
        ),
        
        array(
            "from"  => "2021-01-16",
            "to"    => "2021-01-31",
            "label" => "16-31/mai",
            "color" => "#FF4500",
            "lineDashType" => "solid",
            "markerType" => "square",
            "type"  => "line",
        ),
        array(
            "from"  => date('Y-m-d'),
            "to"    => date('Y-m-d'),
            "label" => "hoje",
            "color" => "lime",
            "lineDashType" => "solid",
            "markerType" => "square",
            "type"  => "line",
        ),
    ], 
    "data" => []
];


foreach($dataPoints["period"] as $period) {

    echo "<h4>$period[label]</h4><hr>";

    $downloads  = [];

    $downs = 
        $ndb->download()
            ->select("$ip as ip, min(timestamp) as timestamp")
            ->where("lower(os) = ? AND (DATE(timestamp) between ? AND ?)", array("windows", $period["from"], $period["to"]))
            ->group("$ip")
            ->order("id desc");


    //DOWNLOADS
    foreach ($downs as $download) {

        $finished =
            $ndb->download()->where("$ip = ? AND status = ?", array($download["ip"], "FINISHED") )->limit(1)->fetch();

        array_push(
            $downloads, 
            array(
                "ip" => $download["ip"],
                "date" => $download["timestamp"],
                "finished" => !!$finished ? $finished["status"] : NULL,
            )
        );
    }


    //SETUPS
    foreach ($downloads as &$download) {

        $is_installed  = false;
        $was_installed = false;

        $setups = [];
        $status  = NULL;

        foreach ($ndb->setup()->where("$ip = ?", $download["ip"]) as $setup) {

            $was_installed = (($was_installed) || ($setup["status"] == 'INSTALLED'));
        
            if ($setup["status"] != 'ABORTED') $status = $setup["status"];

            if ((!$status) && ($setup["status"] == 'ABORTED')) $status = 'ABORTED';


            $setups[] = array(
                "step" => $setup["step"],
                "page" => $setup["page"],
                "status" => $setup["status"],
                "timestamp" => $setup["timestamp"]
                );
        }

        
        $download["installed"] = $status;

    //}


    //REGISTER
    //foreach ($downloads as &$download) {
        $registered = 
            $ndb->ins()->where("$ip = ?", $download["ip"])->fetch();

        $download["registered"] = !$registered ? NULL : array("id" => $registered["id"], "name" => $registered->cus["name"], "date" => $registered["timestamp"]);

        //buy   
        if ($registered) {

            $buyed = 
                $ndb->rechist()->where("(seri.ins_id = ?) AND (confirmed IS NOT NULL) AND (DATE(confirmed) >= ?)", array($registered["id"], $period["from"]) )->fetch();

            $download["buyed"] = !$buyed ? NULL : array('status' => 'BUYED', 'serial' => $buyed->seri['skey']);

            if (!!$buyed) echo $buyed->seri['timestamp'].' - '.$buyed->seri['skey'].'<br>';
        } else {

            $download["buyed"] = null;
        }
    }



    //echo json_encode($downloads); exit;


    $qtHome = 2279; 
    $qtDow1 = 0; 
    $qtDow2 = 0; 
    $qtSet1 = 0; 
    $qtSet2 = 0; 
    $qtReg1 = 0; 
    $qtBuy1 = 0; 

    foreach ($downloads as &$download) {
        
        $qtDow1++;

        $incDow2 = false;
        $incSet1 = false;
        $incSet2 = false;
        $incReg1 = false;
        $incBuy1 = false;

        if ($download['finished' ] == 'FINISHED' ) {
         
            $incDow2 = true;
        }

        if ($download['installed']) {

            $incDow2 = true;
            $incSet1 = true;
        }

        if ($download['installed'] == 'INSTALLED') {

            $incDow2 = true;
            $incSet1 = true;
            $incSet2 = true;
        }

        if ($download['registered']) {

            $incDow2 = true;
            $incSet1 = true;
            $incSet2 = true;
            $incReg1 = true;
        }

        if ($download['buyed']) {

            $incDow2 = true;
            $incSet1 = true;
            $incSet2 = true;
            $incReg1 = true;
            $incBuy1 = true;
            //echo "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx/";
        } //else  {echo "ops!";}

        if ($incDow2) $qtDow2++;
        if ($incSet1) $qtSet1++;
        if ($incSet2) $qtSet2++;
        if ($incReg1) $qtReg1++;
        if ($incBuy1) $qtBuy1++;

    }


    $qtBase = $qtDow1;



    $pcHome = 100;
    $pcDow1 = 100;//($qtDow1*100) / $qtBase;
    $pcDow2 = ($qtDow2*100) / $qtBase;
    $pcSet1 = ($qtSet1*100) / $qtBase;
    $pcSet2 = ($qtSet2*100) / $qtBase;
    $pcReg1 = ($qtReg1*100) / $qtBase;
    $pcBuy1 = ($qtBuy1*100) / $qtBase;

    $pcDow1 = number_format($pcDow1, 3, '.', '');
    $pcDow2 = number_format($pcDow2, 3, '.', '');
    $pcSet1 = number_format($pcSet1, 3, '.', '');
    $pcSet2 = number_format($pcSet2, 3, '.', '');
    $pcReg1 = number_format($pcReg1, 3, '.', '');
    $pcBuy1 = number_format($pcBuy1, 3, '.', '');


    $lossDow1 = 100-(($qtDow1*100)/$qtHome);
    $lossDow2 = 100-(($qtDow2*100)/$qtDow1);
    $lossSet1 = 100-(($qtSet1*100)/$qtDow2);
    $lossSet2 = 100-(($qtSet2*100)/$qtSet1);
    $lossReg1 = 100-(($qtReg1*100)/$qtSet2);

    if ($qtReg1 == 0) $lossBuy1 = 100; else
    $lossBuy1 = 100-(($qtBuy1*100)/$qtReg1);

    $lossDow1 = number_format($lossDow1, 3, '.', '');
    $lossDow2 = number_format($lossDow2, 3, '.', '');
    $lossSet1 = number_format($lossSet1, 3, '.', '');
    $lossSet2 = number_format($lossSet2, 3, '.', '');
    $lossReg1 = number_format($lossReg1, 3, '.', '');
    $lossBuy1 = number_format($lossBuy1, 3, '.', '');




    array_push(
        $dataPoints["data"], 
        array(
            "type" => $period["type"],
            "showInLegend" => true,
            "name" => $period["label"],
            "lineDashType" => $period["lineDashType"],
            "markerType" => $period["markerType"],
            //xValueFormatString: "DD MMM, YYYY",
            "color" => $period["color"],
            //yValueFormatString: "#,##0K",
            //yValueFormatString: "0%",
            "toolTipContent" => "<b style='\"'color: {color};'\"'>{name}</b> </br> Qt: {qt} </br> Step: {y}% </br> Loss: {loss}% <br>",
            "indexLabel" => "{y}%",
            "dataPoints" => 


            [
                //["label" => "Home"             , "y" => $pcHome, "qt" => $qtHome, "loss" => 0        ],
                ["label" => "Download Started" , "y" => $pcDow1, "qt" => $qtDow1, "loss" => $lossDow1],
                ["label" => "Download Finished", "y" => $pcDow2, "qt" => $qtDow2, "loss" => $lossDow2],
                ["label" => "Setup Started"    , "y" => $pcSet1, "qt" => $qtSet1, "loss" => $lossSet1],
                ["label" => "Setup Finished"   , "y" => $pcSet2, "qt" => $qtSet2, "loss" => $lossSet2],
                ["label" => "Register Finished", "y" => $pcReg1, "qt" => $qtReg1, "loss" => $lossReg1],
                ["label" => "Buyed"            , "y" => $pcBuy1, "qt" => $qtBuy1, "loss" => $lossBuy1],
            ]
        )
    );


}

//echo json_encode($dataPoints);

  $resultado = ob_get_contents();
  ob_end_clean();
?>


<script type="text/javascript">

    var myyy = <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK ); ?>;
    myyy.data[0].indexLabel = '';
    myyy.data[1].indexLabel = '';
    myyy.data[2].indexLabel = '';
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
        text: "Actual vs Projected Sales"
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
        title: "Funnel",
        suffix: "%",
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
        <?= $resultado; ?>
    </div>
</body>
</html>


