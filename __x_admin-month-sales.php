<?php 
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

//setlocale(LC_ALL, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
//date_default_timezone_set("America/Recife");



header('Content-Type: application/json');
//ob_start();

require_once "db.php";

$pdo = connect_pdo();


$dias = array('ZER', 'seg', 'ter', 'qua', 'qui', 'sex', 'sáb', 'dom');
$dias = array('ZER', 'SEG', 'TER', 'QUA', 'QUI', 'SEX', 'SÁB', 'DOM');


echo  date("Y-m", strtotime("-1 year"));


$ip = "SUBSTRING_INDEX(ip, '.', 3)";

$hoje = date("Y-m-d", strtotime("-9 days"));
$hoje = date("Y-m-d");

//echo "today: ".$hoje;
//$ip = "ip";

//echo $ip;
//exit;

$interval = 'month'; 

if (isset($_GET['interval']))
    $interval = $_GET['interval']; 


$months = [
    //date("Y-m", strtotime("-4 " . $interval)) => [],
    date("Y-m", strtotime("-3 " . $interval)) => [],
    date("Y-m", strtotime("-2 " . $interval)) => [],
    date("Y-m", strtotime("-1 " . $interval)) => [],
    date("Y-m") => [],
];
/*
$mm = new \DateTime();
$at1 = $mm->modify('-1 month')->format('Y-m-d');
$at2 = $mm->modify('-1 month')->format('Y-m-d');
$at3 = $mm->modify('-1 month')->format('Y-m-d');

$months = [
    //date("Y-m", strtotime("-4 " . $interval)) => [],
    $at3 => [],
    $at2 => [],
    $at1 => [],
    date("Y-m") => [],
];



echo sizeof($months) . '<br>';
echo '1: '.$at1 . '<hr>';
echo '2: '.$at2 . '<hr>';
echo '3: '.$at3 . '<hr>';
foreach ($months as $month => $info) {
    
    //echo $month."<hr><hr>";
}
exit;
*/
$m4 = date("Y-m", strtotime("-4 " . $interval));
$m3 = date("Y-m", strtotime("-3 " . $interval));
$m2 = date("Y-m", strtotime("-2 " . $interval));
$m1 = date("Y-m", strtotime("-1 " . $interval));
$mx  = date("Y-m");

$months2 = [
    "$m4" => [
        //"month" => $m1,
        "label" => $m4,
        "color" => "blue",
        "lineDashType" => "dotted",
        "markerType" => "square",
        "type"  => "line",
        "days" => [
            //
        ]
    ],
    "$m3" => [
        //"month" => $m1,
        "label" => $m3,
        "color" => "silver",
        "lineDashType" => "dotted",
        "markerType" => "square",
        "type"  => "line",
        "days" => [
            //
        ]
    ],
    "$m2" => [
        //"month" => $m1,
        "label" => $m2,
        "color" => "#FFD700",
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
        "color" => "#ff950e",
        "lineDashType" => "solid",
        "markerType" => "square",
        "type"  => "line",
        "days" => [
            //
        ]
    ],
    "$mx" => [
        //"month" => $m2,
        "label" => $mx,
        "color" => "#FF4500",
        "lineDashType" => "solid",
        "markerType" => "square",
        "type"  => "line",
        "days" => [
            //
        ]
    ]
];


//echo json_encode($months2);
//exit;

//M-1
foreach ($months as $month => $info) {
    
   // echo $month."<hr>";
    $total = 0;
    $qt_vl = 0;
    $tt_vl = 0;

    for ($x = 1; $x <= 31/*date('d')*/; $x++) {
      
        $date = $month."-".$x;

        //$date = "2021-04-15";

        //if (strtotime($date) <= strtotime(date('Y-m-d')))
        if (strtotime($date) <= strtotime($hoje))
        {

                // $recebi = 
                //     $ndb->rechist()
                //         ->select("sum(value-discount) as total")
                //         ->where("date = ? AND ( (confirmed IS NOT NULL) OR ( ? >= DATE(NOW())) )", array($date, $date))->fetch();

                // $recebi = 
                //     $ndb->seri()
                //         ->select("sum(price-discount) as total")
                //         ->where("DATE(timestamp) = ? AND ( (__settled_at IS NOT NULL) OR ( ? >= DATE(NOW())) ) OR (MAX(rechist.date) > ?)", array($date, $date, $date))->fetch();





                //$date = "2021-04-15";

                $sth = $pdo->prepare("
                SELECT 
                    DATE(s.timestamp), IFNULL(sum(s.price-s.discount), 0) as total, MAX(r.date) AS max_date
                FROM 
                    seri s
                LEFT JOIN
                    rechist r on r.seri_id = s.id

                WHERE 
                    DATE(s.timestamp) = ? AND ( 

                        (s.__settled_at IS NOT NULL) OR 
                        (? >= DATE(NOW())) OR
                        (? <= (select max(date) from rechist where seri_id = s.id AND date >=  DATE(NOW()) ))
                    )    
                GROUP BY
                    DATE(s.timestamp)
                ");


                /*   
                if (!$sth) {
                    echo "\nPDO::errorInfo() #1:\n";
                    print_r($pdo->errorInfo());
                }
                */
                    
                $sth->execute(array(
                    $date, 
                    $date, 
                    $date
                ));
                

                //die('dddd: '.$sth->rowCount());

                /*
                if (!$sth) {
                    echo "\nPDO::errorInfo() #2:\n";
                    print_r($pdo->errorInfo());
                    echo "<hr>";
                }
                */


                $recebi = $sth->fetch();

                //var_dump($recebi->total);
                //echo "{$date} = {$recebi->total} <hr>";

                if ($recebi) {
                    $total_day = $recebi->total;
                    //echo "YES: {$date}, MaxDate: {$recebi->max_date} = {$total_day} <hr>";
                    $total = $total + $recebi->total;

                    $qt_vl++;
                    $tt_vl = $total;
                } else {
                    $total_day = 0;
                    //echo "NO: {$date}, {$recebi->max_date}<hr>";
                }

//die('sdd');
        } else {

                $total_day = 0;
                //$total = $total + ($tt_vl / $qt_vl);
                $total = bcadd($total, ($tt_vl / $qt_vl), 2);
        }
        


        if ($month == date('Y-m')) {

            try {

            $t_passado = floatval($months2[$m1]['days'][$x-1]['total']);

            }

            catch(Exception $e) {
               $t_passado = 0;
               // $t_passado = floatval(end($months2[$m1]['days'])['total']);
               // echo $m1 . ' <-- $m1, ' . $date . ' - ' . floatval(end($months2[$m1]['days'])['total']) . '<hr>';
                //echo 'Message: ' .$e->getMessage();
                $perct = 0;
            }            


            if ($t_passado > 0)
                $perct = ($total*100)/$t_passado; else
                $perct = 0;
        } else {

            $perct = 0;
        }


        //echo $total_day;
        array_push(
            $months2[$month]["days"], 
            array(
                //"value" => floatval($recebi->total), 
                "total_day"  => $total_day,
                "day_of_week" => $dias[date("N", strtotime($date))],
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
    /*
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
    */
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
                "total_day" => $month["days"][$d-1]["total_day"],
                "day_of_week" => $month["days"][$d-1]["day_of_week"],
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
            "toolTipContent" => "<b style='\"'color: {color};'\"'>{name}</b></br> R$ {y} <b style='\"'color: silver;'\"'>— R$ {total_day} <sup style='\"'font-size: 10px; color: darkslateblue;'\"'>({day_of_week})</sup></b><br>",
            //"toolTipContent" => "<b style='\"'color: {color};'\"'>{name}</b> </br> Qt: {qt} </br> Step: {y}% </br> Loss: {loss}% <br>",
            "indexLabel" => "{p}%",
            "dataPoints" => 


            $days
        )
    );
}

  //echo json_encode($dataPoints);
  //exit;

  //$resultado = ob_get_contents();
  //ob_end_clean();
?>


<script type="text/javascript">

    var myyy = <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK ); ?>;
    myyy.data[0].indexLabel = '';
    myyy.data[1].indexLabel = '';
    myyy.data[2].indexLabel = '';
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
        text: "Mes Atual vs Mes Anterior — Sales"
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

    <hr>
    <div style="padding-top:90px;">
        <!--?= $resultado; ?-->
    </div>
    <span style="color:gray;">SALES</span> — <a href="?type=receipts" style="color:blue;">RECEIPTS</a>
</body>
</html>


