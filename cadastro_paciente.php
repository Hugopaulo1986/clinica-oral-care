<?php
session_start();
if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit();
}

// Incluir a conexão com o banco de dados
require_once('config.php');

$mensagem = "";

// Se o formulário for enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = trim($_POST["nome"]);
    $sexo = trim($_POST["sexo"]);
    $data_nasc = trim($_POST["data_nasc"]);
    $historico = trim($_POST["historico"]);
    $endereco = trim($_POST["endereco"]);
    $telefone = trim($_POST["telefone"]);
    $email = trim($_POST["email"]);

    // Verifica se os campos obrigatórios estão preenchidos
    if (empty($nome) || empty($sexo) || empty($data_nasc) || empty($telefone) || empty($email)) {
        $mensagem = "<p class='erro'>❌ Preencha todos os campos obrigatórios!</p>";
    } else {
        // Preparar a consulta para evitar SQL Injection
        $sql = "INSERT INTO pacientes (nome, sexo, data_nasc, historico, endereco, telefone, email) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "sssssss", $nome, $sexo, $data_nasc, $historico, $endereco, $telefone, $email);
            if (mysqli_stmt_execute($stmt)) {
                $mensagem = "<p class='sucesso'>✅ Paciente cadastrado com sucesso!</p>";
            } else {
                $mensagem = "<p class='erro'>❌ Erro ao cadastrar: " . mysqli_stmt_error($stmt) . "</p>";
            }
            mysqli_stmt_close($stmt);
        } else {
            $mensagem = "<p class='erro'>❌ Erro ao preparar a consulta: " . mysqli_error($conn) . "</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Paciente</title>
    <link rel="stylesheet" href="css/estilo.css?v=<?php echo time(); ?>"> <!-- Atualiza o CSS automaticamente -->
</head>
<body>

<?php include('navbar.php'); ?> <!-- 🔹 Incluindo a navbar padrão -->

<main class="container">
    <h2>Cadastro de Paciente</h2>
    <?= $mensagem; ?>
    <form action="cadastro_paciente.php" method="POST">
        <label for="nome">Nome Completo:</label>
        <input type="text" id="nome" name="nome" required>

        <label for="sexo">Sexo:</label>
        <select id="sexo" name="sexo" required>
            <option value="">Selecione</option>
            <option value="Masculino">Masculino</option>
            <option value="Feminino">Feminino</option>
            <option value="Outro">Outro</option>
        </select>

        <label for="data_nasc">Data de Nascimento:</label>
        <input type="date" id="data_nasc" name="data_nasc" required>

        <label for="historico">Histórico Médico:</label>
        <textarea id="historico" name="historico"></textarea>

        <label for="endereco">Endereço:</label>
        <input type="text" id="endereco" name="endereco" required>

        <label for="telefone">Telefone:</label>
        <input type="text" id="telefone" name="telefone" required>

        <label for="email">E-mail:</label>
        <input type="email" id="email" name="email" required>

        <button type="submit">Cadastrar</button>
    </form>
</main>

<footer>
    <p>&copy; 2025 Clínica Oral Care. Todos os direitos reservados.</p>
</footer>

</body>
</html>
