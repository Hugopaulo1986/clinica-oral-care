<?php
session_start();
require 'config.php';

$mensagem = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = trim($_POST["nome"]);
    $email = trim($_POST["email"]);
    $senha = trim($_POST["senha"]);

    if (empty($nome) || empty($email) || empty($senha)) {
        $mensagem = "<p class='erro'>❌ Preencha todos os campos!</p>";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensagem = "<p class='erro'>❌ E-mail inválido!</p>";
    } else {
        // Verifica se o e-mail já está cadastrado
        $check = $conn->prepare("SELECT id FROM pacientes_login WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $mensagem = "<p class='erro'>⚠️ E-mail já cadastrado!</p>";
        } else {
            $hash = password_hash($senha, PASSWORD_DEFAULT);
            $insert = $conn->prepare("INSERT INTO pacientes_login (nome, email, senha) VALUES (?, ?, ?)");
            $insert->bind_param("sss", $nome, $email, $hash);

            if ($insert->execute()) {
                header("Location: login_paciente.php?cadastro=sucesso");
                exit;
            } else {
                $mensagem = "<p class='erro'>❌ Erro ao cadastrar!</p>";
            }
            $insert->close();
        }
        $check->close();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Registrar-se</title>
    <link rel="stylesheet" href="css/estilo.css?v=<?= time(); ?>">
    <style>
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        main.container {
            flex: 1;
            padding: 20px;
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

<main class="container">
    <h2>Registrar-se</h2>
    <?= $mensagem; ?>
    <form action="registrar_paciente.php" method="POST">
        <label for="nome">Nome Completo:</label>
        <input type="text" id="nome" name="nome" required>

        <label for="email">E-mail:</label>
        <input type="email" id="email" name="email" required>

        <label for="senha">Senha:</label>
        <input type="password" id="senha" name="senha" required minlength="6">

        <button type="submit">Registrar</button>
    </form>

    <p class="link-login">Já tem conta? <a href="login_paciente.php">Faça login aqui</a>.</p>
</main>

<footer>
    <p>&copy; 2025 Clínica Oral Care. Todos os direitos reservados.</p>
</footer>

</body>
</html>
