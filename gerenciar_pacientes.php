<?php
session_start();
if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit();
}

require 'config.php';

// Apenas pacientes ativos
$sql = "SELECT * FROM pacientes WHERE ativo = 1 ORDER BY nome ASC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Pacientes</title>
    <link rel="stylesheet" href="css/estilo.css?v=<?= time(); ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        main.container {
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

<main class="container mt-5 mb-4">
    <h2 class="mb-4 text-center">Pacientes Cadastrados</h2>

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle text-center w-100">
            <thead class="table-light">
                <tr>
                    <th>Nome</th>
                    <th>Telefone</th>
                    <th>Email</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                    <tr>
                        <td><?= htmlspecialchars($row['nome']); ?></td>
                        <td><?= htmlspecialchars($row['telefone']); ?></td>
                        <td><?= htmlspecialchars($row['email']); ?></td>
                        <td>
                            <div class="d-flex justify-content-center gap-2 flex-wrap">
                                <a href="historico_paciente.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-info">
                                    📋 Ver Histórico
                                </a>
                                <a href="desinscrever_paciente.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-danger"
                                   onclick="return confirm('Tem certeza que deseja desinscrever este paciente?')">
                                    🚫 Desinscrever
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</main>

<footer>
    <p>&copy; 2025 Clínica Oral Care. Todos os direitos reservados.</p>
</footer>
</body>
</html>
