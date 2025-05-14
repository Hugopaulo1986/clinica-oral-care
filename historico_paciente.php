<?php
require 'config.php';
date_default_timezone_set('America/Sao_Paulo');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Pega o ID do paciente pela URL
$paciente_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($paciente_id <= 0) {
    echo "<script>alert('ID inválido!'); history.back();</script>";
    exit();
}

// Tenta buscar no pacientes
$sql_paciente = "SELECT nome, cpf, telefone, endereco, data_nasc, email FROM pacientes WHERE id = ?";
$stmt_paciente = $conn->prepare($sql_paciente);
$stmt_paciente->bind_param("i", $paciente_id);
$stmt_paciente->execute();
$resultado_paciente = $stmt_paciente->get_result();

if ($resultado_paciente->num_rows > 0) {
    $paciente = $resultado_paciente->fetch_assoc();
} else {
    // Se não achar, tenta buscar no pacientes_login
    $sql_login = "SELECT nome, cpf, telefone, endereco, data_nasc AS data_nasc, email FROM pacientes_login WHERE id = ?";
    $stmt_login = $conn->prepare($sql_login);
    $stmt_login->bind_param("i", $paciente_id);
    $stmt_login->execute();
    $resultado_login = $stmt_login->get_result();

    if ($resultado_login->num_rows > 0) {
        $paciente = $resultado_login->fetch_assoc();
    } else {
        echo "<script>alert('Paciente não encontrado!'); history.back();</script>";
        exit();
    }
}

// Buscar histórico de consultas do paciente
$sql_consultas = "SELECT 
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
            c.status
        FROM consultas c
        INNER JOIN dentistas d ON c.dentista_id = d.id
        WHERE c.paciente_id = ?
        ORDER BY c.data_consulta ASC, c.hora_consulta ASC";

$stmt = $conn->prepare($sql_consultas);
$stmt->bind_param("i", $paciente_id);
$stmt->execute();
$resultado_consultas = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Histórico de Consultas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .info-paciente {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<?php include('navbar.php'); ?>

<div class="container mt-5">
    <div class="card p-4 shadow" style="max-width: 100%; margin: auto;">
        <h2 class="text-center mb-4">📋 Histórico de Consultas do Paciente</h2>

        <div class="info-paciente">
            <p><strong>Nome:</strong> <?= htmlspecialchars($paciente['nome']) ?></p>
            <p><strong>CPF:</strong> <?= htmlspecialchars($paciente['cpf']) ?></p>
            <p><strong>Telefone:</strong> <?= htmlspecialchars($paciente['telefone']) ?></p>
            <p><strong>Data de Nascimento:</strong> 
                <?= !empty($paciente['data_nasc']) ? date('d/m/Y', strtotime($paciente['data_nasc'])) : 'Não informado' ?>
            </p>
            <p><strong>Email:</strong> <?= htmlspecialchars($paciente['email']) ?></p>
        </div>

        <div class="mb-4 text-center">
            <a href="gerenciar_pacientes.php" class="btn btn-success me-2">
                🔙 Voltar
            </a>
            <a href="editar_paciente.php?id=<?= $paciente_id ?>" class="btn btn-primary">
                ✏️ Editar Paciente
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-striped text-center align-middle">
                <thead class="table-success">
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
                        <th>Status</th>
                        <th>Ação</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($resultado_consultas->num_rows > 0): ?>
                    <?php while ($row = $resultado_consultas->fetch_assoc()): ?>
                        <tr>
                            <td><?= date('d/m/Y', strtotime($row['data_consulta'])) ?></td>
                            <td><?= substr($row['hora_consulta'], 0, 5) ?></td>
                            <td><?= htmlspecialchars($row['dentista']) ?></td>
                            <td><?= htmlspecialchars($row['procedimento']) ?></td>
                            <td><?= htmlspecialchars($row['dentes']) ?></td>
                            <td>R$ <?= number_format($row['valor'], 2, ',', '.') ?></td>
                            <td><?= htmlspecialchars($row['duracao']) ?></td>
                            <td><?= htmlspecialchars($row['forma_pagamento']) ?></td>
                            <td><?= htmlspecialchars($row['convenio']) ?></td>
                            <td><?= htmlspecialchars($row['observacoes']) ?></td>
                            <td>
                                <?php if ($row['status'] == 'cancelado'): ?>
                                    <span class="badge bg-danger">Cancelado</span>
                                <?php else: ?>
                                    <span class="badge bg-success">Confirmado</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="registrar_procedimento.php?id=<?= $row['id'] ?>" class="btn btn-outline-primary btn-sm">
                                    ✏️ Registrar
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="12">Nenhuma consulta registrada.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>


<footer class="mt-5" style="background-color: #1ABC9C; color: white; padding: 20px 0; text-align: center;">
  <p>&copy; 2025 Clínica Oral Care. Todos os direitos reservados.</p>
</footer>


</body>

</html>
