<?php
require 'config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["paciente_id_login"])) {
    header("Location: login_paciente.php");
    exit();
}

$paciente_id = $_SESSION["paciente_id_login"];

// Query correta com nome do dentista e filtro de status
$sql = "SELECT c.id, c.data_consulta, c.hora_consulta, d.nome AS dentista_nome, c.observacoes
        FROM consultas c
        INNER JOIN dentistas d ON c.dentista_id = d.id
        WHERE c.paciente_id = ? AND c.status != 'cancelado'
        ORDER BY c.data_consulta ASC, c.hora_consulta ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $paciente_id);
$stmt->execute();
$resultado = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Minhas Consultas</title>
    <link rel="stylesheet" href="css/estilo.css?v=<?= time(); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
    <div class="form-container mx-auto" style="max-width: 1100px;">
        <h2 class="mb-4 text-center" style="color: #0f665a;">Minhas Consultas</h2>

        <?php if ($resultado->num_rows > 0): ?>
            <table class="table table-bordered table-hover text-center align-middle">
                <thead class="table-success">
                    <tr>
                        <th>Data</th>
                        <th>Hora</th>
                        <th>Dentista</th>
                        <th>Observações</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($consulta = $resultado->fetch_assoc()): ?>
                        <tr>
                            <td><?= date('d/m/Y', strtotime($consulta['data_consulta'])) ?></td>
                            <td><?= substr($consulta['hora_consulta'], 0, 5) ?></td>
                            <td><?= htmlspecialchars($consulta['dentista_nome']) ?></td>
                            <td><?= htmlspecialchars($consulta['observacoes']) ?></td>
                            <td>
                                <a href="reagendar_consulta.php?id=<?= $consulta['id'] ?>" class="btn btn-outline-primary btn-sm mb-2">
                                    🔄 Reagendar
                                </a>
                                <a href="desmarcar_consulta.php?id=<?= $consulta['id'] ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Tem certeza que deseja cancelar esta consulta?');">
                                    ❌ Cancelar
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="alert alert-info text-center">Você ainda não possui consultas agendadas.</div>
        <?php endif; ?>
    </div>
</div>

<footer>
    <p>&copy; 2025 Clínica Oral Care. Todos os direitos reservados.</p>
</footer>

</body>
</html>
