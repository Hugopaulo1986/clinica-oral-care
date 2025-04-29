<?php
require 'config.php';

header('Content-Type: application/json');

// Define o número máximo de consultas por dia
$maxVagas = 18;

// Consulta o número de consultas por data (exceto canceladas)
$sql = "SELECT data_consulta, COUNT(*) as total 
        FROM consultas 
        WHERE status != 'cancelado' 
        GROUP BY data_consulta";

$result = $conn->query($sql);

$eventos = [];

while ($row = $result->fetch_assoc()) {
    $eventos[] = [
        'data' => $row['data_consulta'],
        'disponivel' => $row['total'] < $maxVagas
    ];
}

// Opcional: adicionar dias futuros sem marcações como disponíveis
// Exemplo: preencher próximos 30 dias como disponíveis (caso não estejam no banco ainda)
$hoje = new DateTime();
for ($i = 0; $i < 30; $i++) {
    $data = $hoje->format('Y-m-d');

    // Verifica se a data já foi incluída
    $existe = array_filter($eventos, fn($e) => $e['data'] === $data);

    if (empty($existe)) {
        $eventos[] = [
            'data' => $data,
            'disponivel' => true
        ];
    }

    $hoje->modify('+1 day');
}

echo json_encode($eventos);
