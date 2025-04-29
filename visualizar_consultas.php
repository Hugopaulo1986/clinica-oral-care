<?php
session_start();

if (!isset($_SESSION["usuario_id"]) || !in_array($_SESSION["tipo"], ["dentista", "recepcionista"])) {
    header("Location: login.php");
    exit();
}

require_once('config.php');

$usuario_id = $_SESSION["usuario_id"];
$tipo = $_SESSION["tipo"];
$tabela = ($tipo === "dentista") ? "dentistas" : "recepcionistas";

// Pega nome do usuário
$stmt = $conn->prepare("SELECT nome FROM $tabela WHERE id = ?");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$res = $stmt->get_result();
$usuario = $res->fetch_assoc();
$nome_usuario = $usuario ? htmlspecialchars($usuario['nome']) : "Usuário";

// Busca consultas
$sql = "SELECT c.id, c.nome_paciente, c.telefone, c.data_consulta, c.hora_consulta, 
               d.nome AS nome_dentista, c.observacoes, c.status 
        FROM consultas c
        JOIN dentistas d ON c.dentista_id = d.id
        WHERE c.status != 'cancelado'
        ORDER BY c.data_consulta, c.hora_consulta";
$result = mysqli_query($conn, $sql);
if (!$result) {
    die("Erro ao buscar consultas: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Consultas Agendadas</title>
    <link rel="stylesheet" href="css/estilo.css?v=<?= time(); ?>">
    <style>
        .tabela-consultas {
            max-width: 1000px;
            margin: 40px auto;
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 12px rgba(0, 0, 0, 0.1);
        }

        .tabela-consultas h2 {
            text-align: center;
            margin-bottom: 10px;
        }

        .tabela-consultas p {
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ccc;
            text-align: center;
        }

        th {
            background-color: #f2f2f2;
        }

        a.confirmar {
            color: green;
            font-weight: bold;
            text-decoration: none;
            margin-right: 8px;
        }

        a.desmarcar {
            color: purple;
            font-weight: bold;
            text-decoration: none;
        }

        a.confirmar:hover,
        a.desmarcar:hover {
            text-decoration: underline;
        }

        .btn-warning {
            background-color:rgb(197, 255, 7);
            color: #000;
            text-decoration: none;
            padding: 6px 10px;
            border-radius: 4px;
            display: inline-block;
        }

        .btn-warning:hover {
            background-color:rgb(0, 224, 52);
        }
    </style>
</head>
<body>

<?php include('navbar.php'); ?>

<div class="tabela-consultas">
    <h2>Consultas Agendadas</h2>
    <p>Bem-vindo(a), <?= $nome_usuario ?>!</p>

    <table>
        <thead>
            <tr>
                <th>Paciente</th>
                <th>Telefone</th>
                <th>Data</th>
                <th>Horário</th>
                <th>Dentista</th>
                <th>Observações</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = mysqli_fetch_assoc($result)) :
            $status = strtolower($row['status']); ?>
            <tr>
                <td><?= htmlspecialchars($row['nome_paciente']); ?></td>
                <td><?= htmlspecialchars($row['telefone']); ?></td>
                <td><?= date("d/m/Y", strtotime($row['data_consulta'])); ?></td>
                <td><?= date("H:i", strtotime($row['hora_consulta'])); ?></td>
                <td><?= htmlspecialchars($row['nome_dentista']); ?></td>
                <td><?= htmlspecialchars($row['observacoes']); ?></td>
                <td>
                    <?php
                    switch ($status) {
                        case 'pendente':
                            echo '<span style="color: orange;">🔴 Pendente</span>';
                            break;
                        case 'confirmado':
                            echo '<span style="color: green;">🟢 Confirmado</span>';
                            break;
                        case 'cancelado':
                            echo '<span style="color: red;">❌ Cancelado</span>';
                            break;
                        default:
                            echo htmlspecialchars($status);
                    }
                    ?>
                </td>
                <td>
                    <?php if ($status === 'pendente') : ?>
                        <a class="confirmar" href="confirmar_consulta.php?id=<?= $row['id']; ?>" onclick="return confirm('Confirmar esta consulta?')">✅ Confirmar</a>
                    <?php endif; ?>
                    <a class="desmarcar" href="desmarcar_consulta.php?id=<?= $row['id']; ?>" onclick="return confirm('Deseja mesmo desmarcar esta consulta?')">❌ Desmarcar</a>
                    <br>
                    <a class="btn-warning" href="reagendar_consulta.php?id=<?= $row['id']; ?>" style="margin-top: 5px;">🔄 Reagendar</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>
