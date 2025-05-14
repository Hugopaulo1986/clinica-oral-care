<?php
require 'config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
echo "ID do paciente da sessão: " . ($_SESSION['paciente_id_login'] ?? 'NÃO DEFINIDO');

// Proteção: apenas dentista, recepcionista ou paciente autenticado podem acessar
if (!isset($_SESSION["dentista_id"]) && !isset($_SESSION["recepcionista_id"]) && !isset($_SESSION["paciente_id_login"])) {
    header("Location: login_paciente.php");
    exit();
}

$paciente_id = null;

// 🔐 Se for paciente, força o uso do ID da sessão
if (isset($_SESSION["paciente_id_login"])) {
    $paciente_id = $_SESSION["paciente_id_login"];
}
// 🔐 Se for dentista ou recepcionista, usa o ID vindo da URL
elseif ((isset($_SESSION["dentista_id"]) || isset($_SESSION["recepcionista_id"])) && isset($_GET['id']) && !empty($_GET['id'])) {
    $paciente_id = intval($_GET['id']);
}
else {
    echo "<script>alert('Paciente não especificado!'); window.history.back();</script>";
    exit();
}

$mensagem = "";

// Atualizar dados
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nome = trim($_POST["nome"]);
    $cpf = trim($_POST["cpf"]);
    $telefone = trim($_POST["telefone"]);
    $email = trim($_POST["email"]);
    $endereco = trim($_POST["endereco"]);
    $data_nasc = $_POST["data_nasc"];

    if (empty($nome) || empty($cpf) || empty($telefone) || empty($email) || empty($endereco) || empty($data_nasc)) {
        $mensagem = "<div class='alert alert-danger text-center'>❌ Preencha todos os campos obrigatórios!</div>";
    } else {
        $sql = "UPDATE pacientes SET nome = ?, cpf = ?, telefone = ?, email = ?, endereco = ?, data_nasc = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssi", $nome, $cpf, $telefone, $email, $endereco, $data_nasc, $paciente_id);
        if ($stmt->execute()) {
            if (isset($_SESSION["paciente_id_login"])) {
                header("Location: painel_paciente.php?atualizado=1");
            } else {
                header("Location: gerenciar_pacientes.php?atualizado=1");
            }
            exit();
        } else {
            $mensagem = "<div class='alert alert-danger text-center'>❌ Erro ao atualizar paciente.</div>";
        }
    }
}

// Buscar dados atuais do paciente
$stmt = $conn->prepare("SELECT nome, cpf, telefone, email, endereco, data_nasc FROM pacientes WHERE id = ?");
$stmt->bind_param("i", $paciente_id);
$stmt->execute();
$result = $stmt->get_result();
$paciente = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Editar Paciente</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/estilo.css?v=<?= time(); ?>">
</head>
<body>

<?php include('navbar.php'); ?>

<div class="container mt-5">
    <div class="form-container mx-auto" style="max-width: 800px;">
        <h2 class="text-center mb-4" style="color: #0f665a;">Editar Dados do Paciente</h2>

        <?= $mensagem; ?>

        <form method="POST" autocomplete="off">
            <div class="mb-3">
                <label for="nome" class="form-label">Nome:</label>
                <input type="text" class="form-control" id="nome" name="nome" required value="<?= htmlspecialchars($paciente['nome'] ?? '') ?>">
            </div>

            <div class="mb-3">
                <label for="cpf" class="form-label">CPF:</label>
                <input type="text" class="form-control" id="cpf" name="cpf" required value="<?= htmlspecialchars($paciente['cpf'] ?? '') ?>">
            </div>

            <div class="mb-3">
                <label for="telefone" class="form-label">Telefone:</label>
                <input type="text" class="form-control" id="telefone" name="telefone" required value="<?= htmlspecialchars($paciente['telefone'] ?? '') ?>">
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">E-mail:</label>
                <input type="email" class="form-control" id="email" name="email" required value="<?= htmlspecialchars($paciente['email'] ?? '') ?>">
            </div>

            <div class="mb-3">
                <label for="endereco" class="form-label">Endereço:</label>
                <input type="text" class="form-control" id="endereco" name="endereco" required value="<?= htmlspecialchars($paciente['endereco'] ?? '') ?>">
            </div>

            <div class="mb-3">
                <label for="data_nasc" class="form-label">Data de Nascimento:</label>
                <input type="date" class="form-control" id="data_nasc" name="data_nasc" required value="<?= htmlspecialchars($paciente['data_nasc'] ?? '') ?>">
            </div>

            <div class="text-center">
                <button type="submit" class="btn btn-success">Salvar Alterações</button>
                <a href="<?= isset($_SESSION['paciente_id_login']) ? 'painel_paciente.php' : 'gerenciar_pacientes.php' ?>" class="btn btn-secondary ms-2">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<footer>
    <p>&copy; 2025 Clínica Oral Care. Todos os direitos reservados.</p>
</footer>

</body>
</html>
