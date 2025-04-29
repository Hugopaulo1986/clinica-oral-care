<?php
session_start();
if (!isset($_SESSION["usuario_id"]) || $_SESSION["tipo"] !== "dentista") {
    header("Location: login.php");
    exit();
}

require_once 'config.php';

$stmt = $conn->prepare("SELECT nome FROM dentistas WHERE id = ?");
$stmt->bind_param("i", $_SESSION["usuario_id"]);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();
$nome = $usuario ? $usuario['nome'] : "Dentista";
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Painel do Dentista</title>
    <link rel="stylesheet" href="css/estilo.css?v=<?= time(); ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>

<?php include('navbar.php'); ?>

<div class="container mt-5">
    <div class="text-center mb-4">
        <h3>Olá, <?= htmlspecialchars($nome); ?> 🦷</h3>
        <p class="text-muted">Seja bem-vindo(a) à sua área profissional.</p>
    </div>

    <div class="row justify-content-center g-4">
        <div class="col-sm-6 col-md-4">
            <div class="card d-flex flex-column h-100 justify-content-between text-center shadow-sm p-3">
                <div>
                    <h5 class="mb-2">📅 Consultas</h5>
                    <p class="text-muted">Visualize e gerencie suas consultas.</p>
                </div>
                <a href="visualizar_consultas.php" class="btn btn-success mt-3">Acessar</a>
            </div>
        </div>
        <div class="col-sm-6 col-md-4">
            <div class="card d-flex flex-column h-100 justify-content-between text-center shadow-sm p-3">
                <div>
                    <h5 class="mb-2">👥 Pacientes</h5>
                    <p class="text-muted">Veja os pacientes cadastrados e seus históricos.</p>
                </div>
                <a href="gerenciar_pacientes.php" class="btn btn-success mt-3">Acessar</a>
            </div>
        </div>
    </div>
</div>

</body>
</html>
