<?php
require 'config.php';
date_default_timezone_set('America/Sao_Paulo');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verifica o ID da consulta
$id_consulta = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// Busca os dados da consulta e do paciente
$sql = "SELECT c.*, p.nome AS nome_paciente 
        FROM consultas c 
        INNER JOIN pacientes p ON c.paciente_id = p.id 
        WHERE c.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_consulta);
$stmt->execute();
$result = $stmt->get_result();
$consulta = $result->fetch_assoc();

// Se não encontrou a consulta, redireciona ou exibe erro
if (!$consulta) {
    die("Consulta não encontrada.");
}

// Atualiza os dados do procedimento se enviado via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $procedimento = $_POST['procedimento'];
    $dentes = $_POST['dentes'];
    $valor = $_POST['valor'];
    $duracao = $_POST['duracao'];
    $forma_pagamento = $_POST['forma_pagamento'];
    $convenio = $_POST['convenio'];
    $observacoes = $_POST['observacoes'];

    $sql = "UPDATE consultas 
            SET procedimento = ?, dentes = ?, valor = ?, duracao = ?, 
                forma_pagamento = ?, convenio = ?, observacoes = ? 
            WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdssssi", $procedimento, $dentes, $valor, $duracao, $forma_pagamento, $convenio, $observacoes, $id_consulta);
    
    if ($stmt->execute()) {
        header("Location: historico_paciente.php?id=" . $consulta['paciente_id']);
        exit();
    } else {
        echo "Erro ao salvar o procedimento.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Registrar Procedimento</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/estilo.css?v=<?php echo time(); ?>">
    <style>
        body {
            padding-top: 70px; 
            padding-bottom: 120px; 
            background-color: #f8f9fa;
        }

        .container-form {
            max-width: 700px;
            margin: auto;
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.05);
        }

        .titulo-formulario {
            font-size: 1.6rem;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            background-color: #1ABC9C;
            color: white;
            text-align: center;
            padding: 10px;
            z-index: 999;
        }
    </style>
</head>
<body>

<!-- 🔹 Navbar fixa -->
<?php include('navbar.php'); ?>

<div class="container mt-4 container-form">
    <div class="titulo-formulario mb-4">
        <span>✏️</span> 
        <span>Registrar Procedimento para <?php echo htmlspecialchars($consulta['nome_paciente']); ?></span>
    </div>

    <form method="post">
        <input type="hidden" name="paciente_id" value="<?php echo $consulta['paciente_id']; ?>">

        <div class="mb-3">
            <label>Procedimento</label>
            <input type="text" name="procedimento" class="form-control" value="<?php echo htmlspecialchars($consulta['procedimento']); ?>">
        </div>

        <div class="mb-3">
            <label>Dente(s)</label>
            <input type="text" name="dentes" class="form-control" value="<?php echo htmlspecialchars($consulta['dentes']); ?>">
        </div>

        <div class="mb-3">
            <label>Valor (R$)</label>
            <input type="number" step="0.01" name="valor" class="form-control" value="<?php echo $consulta['valor']; ?>">
        </div>

        <div class="mb-3">
            <label>Duração</label>
            <input type="time" name="duracao" class="form-control" value="<?php echo $consulta['duracao']; ?>">
        </div>

        <div class="mb-3">
            <label>Forma de Pagamento</label>
            <input type="text" name="forma_pagamento" class="form-control" value="<?php echo htmlspecialchars($consulta['forma_pagamento']); ?>">
        </div>

        <div class="mb-3">
            <label>Convênio</label>
            <input type="text" name="convenio" class="form-control" value="<?php echo htmlspecialchars($consulta['convenio']); ?>">
        </div>

        <div class="mb-3">
            <label>Observações</label>
            <textarea name="observacoes" class="form-control" rows="3"><?php echo htmlspecialchars($consulta['observacoes']); ?></textarea>
        </div>

        <button type="submit" class="btn btn-success">Salvar Procedimento</button>
        <a href="historico_paciente.php?id=<?php echo $consulta['paciente_id']; ?>" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<!-- 🔸 Rodapé fixo -->
<footer>
    <p>&copy; 2025 Clínica Oral Care. Todos os direitos reservados.</p>
</footer>

</body>
</html>
