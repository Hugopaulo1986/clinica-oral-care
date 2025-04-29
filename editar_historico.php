<?php
require 'config.php';
session_start();

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $novo_historico = trim($_POST['historico']);
    $update = $conn->prepare("UPDATE pacientes SET historico = ? WHERE id = ?");
    $update->bind_param("si", $novo_historico, $id);
    if ($update->execute()) {
        header("Location: gerenciar_pacientes.php");
        exit();
    } else {
        echo "Erro ao atualizar histórico.";
    }
}

$busca = $conn->prepare("SELECT nome, historico FROM pacientes WHERE id = ?");
$busca->bind_param("i", $id);
$busca->execute();
$result = $busca->get_result();
$paciente = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Histórico</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h3 class="mb-4">✏️ Editar Histórico de <strong><?= htmlspecialchars($paciente['nome']) ?></strong></h3>
        
        <form method="post">
            <div class="mb-3">
                <label for="historico" class="form-label">Histórico clínico</label>
                <textarea name="historico" id="historico" class="form-control" rows="6"><?= htmlspecialchars($paciente['historico'] ?? '') ?></textarea>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Salvar</button>
                <a href="gerenciar_pacientes.php" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</body>
</html>
