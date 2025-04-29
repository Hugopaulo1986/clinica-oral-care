<?php
require_once('config.php');

$sql = "SELECT id, nome_paciente, data_consulta, horario, status FROM consultas";
$result = mysqli_query($conn, $sql);

$eventos = [];
while ($row = mysqli_fetch_assoc($result)) {
    $eventos[] = [
        'id' => $row['id'],
        'title' => $row['nome_paciente'],
        'start' => $row['data_consulta'] . 'T' . $row['horario'], // Formato: 2024-12-05T15:00:00
        'status' => $row['status'], // 'ocupado' ou 'disponivel'
    ];
}

header('Content-Type: application/json');
echo json_encode($eventos); // Retorna os eventos em formato JSON
?>
