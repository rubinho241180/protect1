<?php 
header('Content-Type: text/html; charset=utf-8');
require_once "db.php";

$pdo = connect_pdo();

$dias = array('ZER', 'SEG', 'TER', 'QUA', 'QUI', 'SEX', 'SÁB', 'DOM');

$ip = "SUBSTRING_INDEX(ip, '.', 3)";
$hoje = date("Y-m-d");
$interval = 'month'; 

if (isset($_GET['interval']))
    $interval = $_GET['interval']; 

$m4 = date("Y-m", strtotime("-4 " . $interval));
$m3 = date("Y-m", strtotime("-3 " . $interval));
$m2 = date("Y-m", strtotime("-2 " . $interval));
$m1 = date("Y-m", strtotime("-1 " . $interval));
$mx = date("Y-m");

$months2 = array(
    $m3 => array(
        "label" => $m3,
        "color" => "silver",
        "lineDashType" => "dotted",
        "markerType" => "square",
        "type" => "line",
        "days" => array()
    ),
    $m2 => array(
        "label" => $m2,
        "color" => "#FFD700",
        "lineDashType" => "dotted",
        "markerType" => "square",
        "type" => "line",
        "days" => array()
    ),
    $m1 => array(
        "label" => $m1,
        "color" => "#ff950e",
        "lineDashType" => "solid",
        "markerType" => "square",
        "type" => "line",
        "days" => array()
    ),
    $mx => array(
        "label" => $mx,
        "color" => "#FF4500",
        "lineDashType" => "solid",
        "markerType" => "square",
        "type" => "line",
        "days" => array()
    )
);

$months = array(
    $m3 => array(),
    $m2 => array(),
    $m1 => array(),
    $mx => array(),
);

// Processar dados de vendas
foreach ($months as $month => $info) {
    $total = 0;
    $qt_vl = 0;
    $tt_vl = 0;

    for ($x = 1; $x <= 31; $x++) {
        $date = $month."-".$x;

        if (strtotime($date) <= strtotime($hoje)) {
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
                        (? <= (select max(date) from rechist where seri_id = s.id AND date >= DATE(NOW()) ))
                    )    
                GROUP BY
                    DATE(s.timestamp)
                ");
                    
            $sth->execute(array($date, $date, $date));
            $recebi = $sth->fetch();

            if ($recebi) {
                $total_day = $recebi->total;
                $total = $total + $recebi->total;
                $qt_vl++;
                $tt_vl = $total;
            } else {
                $total_day = 0;
            }
        } else {
            $total_day = 0;
            $total = bcadd($total, ($tt_vl / max($qt_vl, 1)), 2);
        }

        if ($month == date('Y-m')) {
            try {
                $t_passado = floatval($months2[$m1]['days'][$x-1]['total']);
            } catch(Exception $e) {
                $t_passado = 0;
                $perct = 0;
            }            

            if ($t_passado > 0)
                $perct = ($total*100)/$t_passado; 
            else
                $perct = 0;
        } else {
            $perct = 0;
        }

        array_push(
            $months2[$month]["days"], 
            array(
                "total_day" => $total_day,
                "day_of_week" => $dias[date("N", strtotime($date))],
                "total" => $total,
                "perct" => number_format($perct, 0, ',', '.'),
            )
        );
    }
}

// Funções de análise - COMPARATIVO 4 MESES
function analisarPeriodosComparativo($months2, $m3, $m2, $m1, $mx) {
    $meses = array($m3, $m2, $m1, $mx);
    $resultado = array();
    
    foreach ($meses as $mes) {
        $periodos = array(
            'inicio' => array('dias' => range(1, 10), 'total' => 0, 'label' => 'Início (1-10)'),
            'meio' => array('dias' => range(11, 20), 'total' => 0, 'label' => 'Meio (11-20)'),
            'final' => array('dias' => range(21, 31), 'total' => 0, 'label' => 'Final (21-31)')
        );
        
        foreach ($periodos as $periodo => &$info) {
            foreach ($info['dias'] as $dia) {
                if (isset($months2[$mes]['days'][$dia-1])) {
                    $info['total'] += $months2[$mes]['days'][$dia-1]['total_day'];
                }
            }
        }
        
        $resultado[$mes] = $periodos;
    }
    
    return $resultado;
}

function identificarExtremosComparativo($months2, $m3, $m2, $m1, $mx) {
    $meses = array($m3, $m2, $m1, $mx);
    $resultado = array();
    
    foreach ($meses as $mes) {
        $dias_com_vendas = array();
        
        foreach ($months2[$mes]['days'] as $index => $dia) {
            if ($dia['total_day'] > 0) {
                $dias_com_vendas[] = array(
                    'dia' => $index + 1,
                    'valor' => $dia['total_day'],
                    'dia_semana' => $dia['day_of_week'],
                    'total_acumulado' => $dia['total']
                );
            }
        }
        
        usort($dias_com_vendas, function($a, $b) {
            return $b['valor'] - $a['valor'];
        });
        
        $resultado[$mes] = array(
            'mais_quentes' => array_slice($dias_com_vendas, 0, 3), // Top 3 por mês
            'mais_frios' => array_slice(array_reverse($dias_com_vendas), 0, 3)
        );
    }
    
    return $resultado;
}

function analisePorDiaSemanaComparativo($months2, $m3, $m2, $m1, $mx) {
    $meses = array($m3, $m2, $m1, $mx);
    $resultado = array();
    
    foreach ($meses as $mes) {
        $dias_semana = array(
            'SEG' => array('total' => 0, 'count' => 0),
            'TER' => array('total' => 0, 'count' => 0),
            'QUA' => array('total' => 0, 'count' => 0),
            'QUI' => array('total' => 0, 'count' => 0),
            'SEX' => array('total' => 0, 'count' => 0),
            'SÁB' => array('total' => 0, 'count' => 0),
            'DOM' => array('total' => 0, 'count' => 0)
        );
        
        foreach ($months2[$mes]['days'] as $dia) {
            if ($dia['total_day'] > 0) {
                $dow = $dia['day_of_week'];
                if (isset($dias_semana[$dow])) {
                    $dias_semana[$dow]['total'] += $dia['total_day'];
                    $dias_semana[$dow]['count']++;
                }
            }
        }
        
        foreach ($dias_semana as $dia => &$info) {
            $info['media'] = $info['count'] > 0 ? $info['total'] / $info['count'] : 0;
        }
        
        $resultado[$mes] = $dias_semana;
    }
    
    return $resultado;
}

// Processar análises COMPARATIVAS
$analise_periodos = analisarPeriodosComparativo($months2, $m3, $m2, $m1, $mx);
$extremos = identificarExtremosComparativo($months2, $m3, $m2, $m1, $mx);
$analise_dia_semana = analisePorDiaSemanaComparativo($months2, $m3, $m2, $m1, $mx);

// Preparar dados para gráfico
$dataPoints = array("data" => array());

foreach ($months2 as $month) {
    $days = array();
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

    array_push(
        $dataPoints["data"], 
        array(
            "type" => $month["type"],
            "showInLegend" => true,
            "name" => $month["label"],
            "lineDashType" => $month["lineDashType"],
            "markerType" => $month["markerType"],
            "color" => $month["color"],
            "toolTipContent" => "<b style='color: {color};'>{name}</b></br> R$ {y} <b style='color: silver;'>— R$ {total_day} <sup style='font-size: 10px; color: darkslateblue;'>({day_of_week})</sup></b><br>",
            "indexLabel" => "{p}%",
            "dataPoints" => $days
        )
    );
}

$dataPoints['analise'] = array(
    'periodos' => $analise_periodos,
    'extremos' => $extremos,
    'dia_semana' => $analise_dia_semana
);
?>
<!DOCTYPE HTML>
<html>
<head>
    <meta charset="utf-8">
    <title>Análise de Vendas - Heatmap</title>
</head>
<body>
    <h2>📊 Análise de Vendas - Períodos Quentes/Frios</h2>
        
        <div style="display: flex; gap: 20px; margin-bottom: 20px;">
            <div style="flex: 1;">
                <h4>🔥 Top 3 Dias Mais Quentes por Mês</h4>
                <div id="diasQuentes"></div>
            </div>
            
            <div style="flex: 1;">
                <h4>🧊 Top 3 Dias Mais Frios por Mês</h4>
                <div id="diasFrios"></div>
            </div>
        </div>
        
        <div style="display: flex; gap: 20px;">
            <div style="flex: 1;">
                <h4>📊 Períodos do Mês - Comparativo 4 Meses</h4>
                <div id="chartPeriodos" style="height: 400px;"></div>
            </div>
            
            <div style="flex: 1;">
                <h4>📅 Dia da Semana - Comparativo 4 Meses</h4>
                <div id="chartDiaSemana" style="height: 400px;"></div>
            </div>
        </div>
        
        <div style="margin-top: 20px;">
            <h4>🌡️ Heatmap - Últimos 4 Meses</h4>
            <div id="chartHeatmap" style="height: 500px;"></div>
        </div>
    </div>

    <script src="https://canvasjs.com/assets/script/jquery-1.11.1.min.js"></script>
    <script src="https://canvasjs.com/assets/script/jquery.canvasjs.min.js"></script>

    <script type="text/javascript">
        var myyy = <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK); ?>;
        var analise = myyy.analise;
        
        myyy.data[0].indexLabel = '';
        myyy.data[1].indexLabel = '';
        myyy.data[2].indexLabel = '';
        
        console.log(myyy);

        window.onload = function () {
            // Análises de heatmap
            renderizarExtremos();
            criarGraficoPeriodos();
            criarGraficoDiaSemana();
            criarHeatmap();
        };

        function renderizarExtremos() {
            // Dias quentes por mês
            var htmlQuentes = '';
            for (var mes in analise.extremos) {
                htmlQuentes += '<h5>' + mes + '</h5><ul>';
                analise.extremos[mes].mais_quentes.forEach(function(dia) {
                    htmlQuentes += '<li><strong>Dia ' + dia.dia + ' (' + dia.dia_semana + ')</strong>: R$ ' + 
                                  dia.valor.toLocaleString('pt-BR', {minimumFractionDigits: 2}) + '</li>';
                });
                htmlQuentes += '</ul>';
            }
            document.getElementById('diasQuentes').innerHTML = htmlQuentes;
            
            // Dias frios por mês
            var htmlFrios = '';
            for (var mes in analise.extremos) {
                htmlFrios += '<h5>' + mes + '</h5><ul>';
                analise.extremos[mes].mais_frios.forEach(function(dia) {
                    htmlFrios += '<li><strong>Dia ' + dia.dia + ' (' + dia.dia_semana + ')</strong>: R$ ' + 
                                dia.valor.toLocaleString('pt-BR', {minimumFractionDigits: 2}) + '</li>';
                });
                htmlFrios += '</ul>';
            }
            document.getElementById('diasFrios').innerHTML = htmlFrios;
        }

        function criarGraficoPeriodos() {
            var series = [];
            var cores = ["#FF4500", "#ff950e", "#FFD700", "silver"]; // Do mais atual para o mais antigo
            var mesesArray = [];
            
            // Converter objeto em array e inverter ordem (mais recente primeiro)
            for (var mes in analise.periodos) {
                mesesArray.push(mes);
            }
            mesesArray.reverse();
            
            var i = 0;
            // Criar uma série para cada mês (ordem invertida)
            mesesArray.forEach(function(mes) {
                var dadosPeriodos = [];
                for (var periodo in analise.periodos[mes]) {
                    dadosPeriodos.push({
                        label: analise.periodos[mes][periodo].label,
                        y: analise.periodos[mes][periodo].total
                    });
                }
                
                series.push({
                    type: "column",
                    name: mes,
                    color: cores[i],
                    showInLegend: true,
                    dataPoints: dadosPeriodos
                });
                i++;
            });
            
            var optionsPeriodos = {
                animationEnabled: true,
                theme: "light2",
                title: { 
                    text: "🔥 Períodos Quentes vs Frios - Comparativo 4 Meses",
                    fontSize: 16
                },
                axisY: {
                    title: "Vendas (R$)",
                    prefix: "R$ ",
                    labelFormatter: function(e) {
                        return "R$ " + (e.value / 1000).toFixed(0) + "K";
                    }
                },
                legend: {
                    verticalAlign: "top"
                },
                data: series
            };
            
            $("#chartPeriodos").CanvasJSChart(optionsPeriodos);
        }

        function criarGraficoDiaSemana() {
            // Preparar dados para barras agrupadas
            var diasSemana = ['SEG', 'TER', 'QUA', 'QUI', 'SEX', 'SÁB', 'DOM'];
            var series = [];
            var cores = ["#FF4500", "#ff950e", "#FFD700", "silver"];
            var mesesArray = [];
            
            // Converter objeto em array e inverter ordem (mais recente primeiro)
            for (var mes in analise.dia_semana) {
                mesesArray.push(mes);
            }
            mesesArray.reverse();
            
            // Criar uma série para cada mês
            mesesArray.forEach(function(mes, index) {
                var dadosDiaSemana = [];
                
                diasSemana.forEach(function(dia) {
                    var valor = analise.dia_semana[mes][dia] ? analise.dia_semana[mes][dia].media : 0;
                    dadosDiaSemana.push({
                        label: dia,
                        y: valor
                    });
                });
                
                series.push({
                    type: "column",
                    name: mes,
                    color: cores[index],
                    showInLegend: true,
                    dataPoints: dadosDiaSemana
                });
            });
            
            var optionsDiaSemana = {
                animationEnabled: true,
                theme: "light2",
                title: { 
                    text: "📅 Performance por Dia da Semana - Barras Agrupadas",
                    fontSize: 16
                },
                axisY: {
                    title: "Média de Vendas (R$)",
                    prefix: "R$ ",
                    labelFormatter: function(e) {
                        return "R$ " + (e.value / 1000).toFixed(0) + "K";
                    }
                },
                axisX: {
                    title: "Dias da Semana"
                },
                legend: {
                    verticalAlign: "top",
                    horizontalAlign: "center"
                },
                toolTip: {
                    shared: true
                },
                data: series
            };
            
            $("#chartDiaSemana").CanvasJSChart(optionsDiaSemana);
        }

        function criarHeatmap() {
            // Debug: verificar dados
            console.log("Debug - myyy.data:", myyy.data);
            console.log("Debug - número de meses:", myyy.data.length);
            
            // Preparar dados para barras empilhadas por dia
            var series = [];
            var cores = ["#FF4500", "#ff950e", "#FFD700", "silver"];
            
            // CORREÇÃO: Garantir que temos exatamente 4 meses e o último é o atual
            var totalMeses = myyy.data.length;
            console.log("Total de meses disponíveis:", totalMeses);
            
            // Usar os últimos 4 meses disponíveis
            var inicioIndice = Math.max(0, totalMeses - 4);
            
            for (var i = totalMeses - 1; i >= inicioIndice; i--) {
                var dadosHeatmap = [];
                var mesData = myyy.data[i];
                var corIndex = totalMeses - 1 - i; // 0=mais atual, 3=mais antigo
                
                console.log("Processando mês:", mesData.name, "índice:", i, "cor:", cores[corIndex]);
                
                mesData.dataPoints.forEach(function(dia, index) {
                    var valor = dia.total_day;
                    var numeroDia = index + 1;
                    
                    dadosHeatmap.push({
                        x: numeroDia,
                        y: valor
                    });
                });
                
                series.push({
                    type: "stackedColumn",
                    name: mesData.name,
                    color: cores[corIndex],
                    showInLegend: true,
                    dataPoints: dadosHeatmap
                });
            }
            
            var optionsHeatmap = {
                animationEnabled: true,
                theme: "light2",
                title: { 
                    text: "🌡️ Vendas por Dia - Empilhadas (Mês Atual em Vermelho)",
                    fontSize: 16
                },
                axisX: {
                    title: "Dias do Mês",
                    interval: 2,
                    minimum: 1,
                    maximum: 31,
                    labelFormatter: function(e) {
                        return e.value;
                    }
                },
                axisY: {
                    title: "Vendas Acumuladas (R$)",
                    prefix: "R$ ",
                    labelFormatter: function(e) {
                        return "R$ " + (e.value / 1000).toFixed(0) + "K";
                    }
                },
                legend: {
                    verticalAlign: "top",
                    horizontalAlign: "center"
                },
                toolTip: {
                    shared: true,
                    content: function(e) {
                        var content = "<b>Dia " + e.entries[0].dataPoint.x + "</b><br/>";
                        var total = 0;
                        for (var i = 0; i < e.entries.length; i++) {
                            content += "<span style='color: " + e.entries[i].dataSeries.color + "'>" + 
                                      e.entries[i].dataSeries.name + "</span>: R$ " + 
                                      e.entries[i].dataPoint.y.toLocaleString('pt-BR') + "<br/>";
                            total += e.entries[i].dataPoint.y;
                        }
                        content += "<b>Total: R$ " + total.toLocaleString('pt-BR') + "</b>";
                        return content;
                    }
                },
                data: series
            };
            
            $("#chartHeatmap").CanvasJSChart(optionsHeatmap);
        }
    </script>

    <hr>
    <span style="color:gray;">SALES HEATMAP</span> — <a href="__x_admin-month-sales.php" style="color:blue;">VOLTAR</a>
</body>
</html>