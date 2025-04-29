<?php
require 'config.php';
session_start();

$id_consulta = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $procedimento = $_POST['procedimento'];
    $dentes = $_POST['dentes'];
    $valor = $_POST['valor'];
    $duracao = $_POST['duracao'];
    $forma_pagamento = $_POST['forma_pagamento'];
    $convenio = $_POST['convenio'];
    $observacoes = $_POST['observacoes'];

    $sql = "UPDATE consultas SET procedimento = ?, dentes = ?, valor = ?, duracao = ?, forma_pagamento = ?, convenio = ?, observacoes = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdssssi", $procedimento, $dentes, $valor, $duracao, $forma_pagamento, $convenio, $observacoes, $id_consulta);
    
    if ($stmt->execute()) {
        header("Location: historico_paciente.php?id=" . $_POST['paciente_id']);
        exit();
    } else {
        echo "Erro ao salvar o procedimento.";
    }
}

// Buscar dados da consulta
$sql = "SELECT c.*, p.nome AS nome_paciente FROM consultas c 
        INNER JOIN pacientes p ON c.paciente_id = p.id 
        WHERE c.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_consulta);
$stmt->execute();
$result = $stmt->get_result();
$consulta = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Registrar Procedimento</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="container mt-4">
    <h3>✏️ Registrar Procedimento para <?= htmlspecialchars($consulta['nome_paciente']) ?></h3>
    <form method="post">
        <input type="hidden" name="paciente_id" value="<?= $consulta['paciente_id'] ?>">
        <div class="mb-3">
            <label>Procedimento</label>
            <input type="text" name="procedimento" class="form-control" value="<?= htmlspecialchars($consulta['procedimento']) ?>">
        </div>
        <div class="mb-3">
            <label>Dente(s)</label>
            <input type="text" name="dentes" class="form-control" value="<?= htmlspecialchars($consulta['dentes']) ?>">
        </div>
        <div class="mb-3">
            <label>Valor (R$)</label>
            <input type="number" step="0.01" name="valor" class="form-control" value="<?= $consulta['valor'] ?>">
        </div>
        <div class="mb-3">
            <label>Duração</label>
            <input type="time" name="duracao" class="form-control" value="<?= $consulta['duracao'] ?>">
        </div>
        <div class="mb-3">
            <label>Forma de Pagamento</label>
            <input type="text" name="forma_pagamento" class="form-control" value="<?= htmlspecialchars($consulta['forma_pagamento']) ?>">
        </div>
        <div class="mb-3">
            <label>Convênio</label>
            <input type="text" name="convenio" class="form-control" value="<?= htmlspecialchars($consulta['convenio']) ?>">
        </div>
        <div class="mb-3">
            <label>Observações</label>
            <textarea name="observacoes" class="form-control" rows="3"><?= htmlspecialchars($consulta['observacoes']) ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Salvar Procedimento</button>
        <a href="historico_paciente.php?id=<?= $consulta['paciente_id'] ?>" class="btn btn-secondary">Cancelar</a>
    </form>
</body>
</html>
