<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require 'config.php';

if (!isset($_SESSION["paciente_id_login"])) {
    header("Location: login_paciente.php");
    exit();
}

$nome_paciente = $_SESSION["usuario_nome"] ?? 'Paciente';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Painel do Paciente</title>
    <link rel="stylesheet" href="css/estilo.css?v=<?= time(); ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .container {
            flex: 1;
        }
        footer {
            background-color: #1ABC9C;
            color: white;
            padding: 20px 0;
            text-align: center;
        }
    </style>
</head>
<body>

<?php include('navbar.php'); ?>

<div class="container mt-5">
    <div class="text-center mb-4">
        <h3>Bem-vindo, <?= htmlspecialchars($nome_paciente) ?> 👋</h3>
        <p class="text-muted">Gerencie suas informações e consultas facilmente.</p>
    </div>

    <div class="row justify-content-center g-4">
        <div class="col-sm-6 col-md-4">
            <div class="card text-center shadow-sm p-3">
                <div class="card-body">
                    <h5 class="card-title mb-3">📝 Atualizar Meus Dados</h5>
                    <p class="card-text">Mantenha seu cadastro atualizado para facilitar seu atendimento.</p>
                    <a href="editar_paciente.php?id=<?= $_SESSION['paciente_id_login'] ?>" class="btn btn-warning mt-3">Acessar</a>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-md-4">
            <div class="card text-center shadow-sm p-3">
                <div class="card-body">
                    <h5 class="card-title mb-3">📅 Agendar Consulta</h5>
                    <p class="card-text">Escolha uma data e horário para sua próxima consulta.</p>
                    <a href="agendamento_consulta.php" class="btn btn-success mt-3">Acessar</a>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-md-4">
            <div class="card text-center shadow-sm p-3">
                <div class="card-body">
                    <h5 class="card-title mb-3">📋 Minhas Consultas</h5>
                    <p class="card-text">Veja, reagende ou cancele suas consultas agendadas.</p>
                    <a href="minhas_consultas.php" class="btn btn-success mt-3">Acessar</a>
                </div>
            </div>
        </div>
    </div>
</div>

<footer class="text-center mt-5 mb-3 text-white">
    &copy; 2025 Clínica Oral Care. Todos os direitos reservados.
</footer>

</body>
</html>
