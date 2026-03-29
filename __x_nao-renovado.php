<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


$timestamp  = date('Y-m-d H:i:s');
error_reporting(E_ALL);


function dateDiff($start, $end) {
    $start_ts = $start;//strtotime($start);
    $end_ts = $end;//strtotime($end);
    $diff = $end_ts - $start_ts;
    return round($diff / 86400);
}

// Conexão com o banco de dados
$host = "localhost";
$username = "r2_read";
$password = "Xyz461300";
$database = "r2";
$conn = new mysqli($host, $username, $password, $database);

// Verifica a conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// A consulta SQL ajustada para listar apenas os últimos 60 dias
$sql = "
SELECT
    c.name,
    c.email,
    CONCAT(c.ddi, c.phone) AS phone,
    MAX(s.dlimit) AS max_dlimit,
    DATEDIFF(NOW(),  MAX(s.dlimit)) AS days_expired
FROM
    cus c
    JOIN ins i ON c.id = i.cus_id
    JOIN seri s ON i.id = s.ins_id
WHERE 
    s.price > 35
GROUP BY
    c.id, c.name, c.email, c.ddi, c.phone
HAVING
    max_dlimit < CURDATE()
    /*AND max_dlimit >= DATE_SUB(CURDATE(), INTERVAL 365 DAY)*/
ORDER BY
    max_dlimit DESC;
    
";

$result = $conn->query($sql);

// Início do HTML
echo '<!DOCTYPE html>
<html>
<head>
    <title>Não Renovados</title>
    <style>
        table {
            width: 100%;
        }
        table td, table th {
            padding: 3px;
        }
        .red {
            background: #f0808080;
        }        
        .yellow {
            background: moccasin;
        }        
        .lime {
            background: gainsboro;
        }
        a {
            color: black;
        }
    </style>
</head>
<body>
    <h3>Não Renovados</h3>
    <table>
        <tr>
            <td span="1" style="width: 20%;">Nome</td>
            <td span="1" style="width: 20%;">Email</td>
            <td span="1" style="width: 20%;">Telefone</td>
            <td span="1" style="width: 20%;">Vencimento</td>
            <td span="1" style="width: 20%;">Days</td>
        </tr>';

// Verifica se a consulta retornou linhas
if ($result->num_rows > 0) {
    // Saída dos dados de cada linha
    while($row = $result->fetch_assoc()) {
        $days = $row["days_expired"];

        if ($days > 30) {
            $color = "red";
        } else 
        if ($days > 7) {
            $color = "yellow";
        } else {
            $color = "lime";
        }
            

        echo '<tr class="'.$color.'">
                <td>'. $row["name"]. '</td>
                <td>'. $row["email"]. '</td>
                <td><a target="_blank" href="https://wa.me/'.$row["phone"].'">'. $row["phone"]. '</a></td>
                <td>'. $row["max_dlimit"]. '</td>
                <td>'.  $days .' days</td>
              </tr>';
    }
} else {
    echo "<tr><td colspan='5'>0 resultados encontrados</td></tr>";
}

echo '</table>
</body>
</html>';

$conn->close();
?>