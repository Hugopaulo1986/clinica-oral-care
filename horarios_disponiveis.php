<?php
require 'config.php';

$data = $_GET['data'] ?? '';
$dentista_id = $_GET['dentista_id'] ?? '';

$horarios_totais = [];
$inicio = strtotime("08:00");
$fim = strtotime("16:30");
while ($inicio <= $fim) {
    $horarios_totais[] = date("H:i", $inicio);
    $inicio = strtotime("+30 minutes", $inicio);
}

$horarios_ocupados = [];

if ($data && $dentista_id) {
    $sql = "SELECT hora_consulta FROM consultas 
            WHERE data_consulta = ? 
              AND dentista_id = ? 
              AND status != 'cancelado'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $data, $dentista_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $horarios_ocupados[] = substr($row['hora_consulta'], 0, 5); // Ex: "08:00"
    }
}

$horarios_disponiveis = array_values(array_diff($horarios_totais, $horarios_ocupados));

header('Content-Type: application/json');
echo json_encode($horarios_disponiveis);
