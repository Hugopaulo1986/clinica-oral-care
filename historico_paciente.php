<?php
require 'config.php';
date_default_timezone_set('America/Sao_Paulo');

session_start();

// Pega o ID do paciente pela URL
$paciente_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$sql = "SELECT 
            c.id,
            c.data_consulta,
            c.hora_consulta,
            d.nome AS dentista,
            c.procedimento,
            c.dentes,
            c.valor,
            c.duracao,
            c.forma_pagamento,
            c.convenio,
            c.observacoes,
            c.paciente_id
        FROM consultas c
        INNER JOIN dentistas d ON c.dentista_id = d.id
        WHERE c.paciente_id = ?
        ORDER BY c.data_consulta DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $paciente_id);
$stmt->execute();
$resultado = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Histórico do Paciente</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="container mt-4">
  <h2>📋 Histórico de Consultas do Paciente</h2>

  <table class="table table-bordered table-striped mt-3">
    <thead class="table-dark">
      <tr>
        <th>Data</th>
        <th>Hora</th>
        <th>Dentista</th>
        <th>Procedimento</th>
        <th>Dente(s)</th>
        <th>Valor</th>
        <th>Duração</th>
        <th>Pagamento</th>
        <th>Convênio</th>
        <th>Observações</th>
        <th>Ação</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($row = $resultado->fetch_assoc()): ?>
      <tr>
        <td><?= date('d/m/Y', strtotime($row['data_consulta'])) ?></td>
        <td><?= substr($row['hora_consulta'], 0, 5) ?></td>
        <td><?= $row['dentista'] ?></td>
        <td><?= $row['procedimento'] ?></td>
        <td><?= $row['dentes'] ?></td>
        <td>R$ <?= number_format($row['valor'], 2, ',', '.') ?></td>
        <td><?= $row['duracao'] ?></td>
        <td><?= $row['forma_pagamento'] ?></td>
        <td><?= $row['convenio'] ?></td>
        <td><?= $row['observacoes'] ?></td>
        <td>
          <a href="registrar_procedimento.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-primary">
            ✏️ Registrar
          </a>
        </td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</body>
</html>
