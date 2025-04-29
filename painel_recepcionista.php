<?php
session_start();
if (!isset($_SESSION["usuario_id"]) || $_SESSION["tipo"] != "recepcionista") {
    header("Location: login.php");
    exit();
}

require_once('config.php');

// Buscar o nome da recepcionista logada
$usuario_id = $_SESSION["usuario_id"];
$sql = "SELECT nome FROM recepcionistas WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $usuario_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$usuario = mysqli_fetch_assoc($result);

// Nome tratado para exibição
$nome_usuario = $usuario ? htmlspecialchars($usuario["nome"]) : "Usuário";
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel da Recepcionista</title>
    <link rel="stylesheet" href="css/estilo.css?v=<?= time(); ?>">
</head>
<body>

<?php include('navbar.php'); ?>

<main class="container">
    <h2>Bem-vinda, <?= $nome_usuario; ?>!</h2>
    <p>Aqui você pode gerenciar agendamentos e pacientes.</p>

    <div class="cards">
        <div class="card">
            <h3>📅 Agendamentos</h3>
            <p>Gerencie as consultas dos pacientes.</p>
            <a href="agendamento_consulta.php" class="btn">Acessar</a>
        </div>

        <div class="card">
            <h3>🔍 Consultas</h3>
            <p>Visualize e gerencie todas as consultas.</p>
            <a href="visualizar_consultas.php" class="btn">Acessar</a>
        </div>

        <div class="card">
            <h3>👥 Cadastro de Pacientes</h3>
            <p>Cadastre novos pacientes no sistema.</p>
            <a href="cadastro_paciente.php" class="btn">Acessar</a>
        </div>
    </div>
</main>

<footer>
    <p>&copy; 2025 Clínica Oral Care. Todos os direitos reservados.</p>
</footer>

</body>
</html>
