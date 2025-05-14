<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$mensagem = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"] ?? '');
    $senha = trim($_POST["senha"] ?? '');

    if (!empty($email) && !empty($senha)) {
        $query = $conn->prepare("SELECT id, nome, senha FROM pacientes_login WHERE email = ?");
        $query->bind_param("s", $email);
        $query->execute();
        $query->store_result();

        if ($query->num_rows == 1) {
            $query->bind_result($id_login, $nome, $senha_hash);
            $query->fetch();

            if (password_verify($senha, $senha_hash)) {
                // 🔍 Busca o ID do cadastro completo na tabela 'pacientes'
                $stmt = $conn->prepare("SELECT id FROM pacientes WHERE email = ?");
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $result = $stmt->get_result();
                $paciente = $result->fetch_assoc();

                if ($paciente) {
                    $_SESSION["paciente_id_login"] = $paciente["id"];  // ✅ ID da tabela 'pacientes'
                    $_SESSION["usuario_nome"] = $nome;

                    header("Location: painel_paciente.php");
                    exit();
                } else {
                    $mensagem = "❌ Cadastro do paciente não encontrado.";
                }
            } else {
                $mensagem = "❌ Senha incorreta!";
            }
        } else {
            $mensagem = "❌ E-mail não encontrado!";
        }
    } else {
        $mensagem = "❌ Preencha todos os campos!";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Login Paciente</title>
    <link rel="stylesheet" href="css/estilo.css?v=<?= time(); ?>">
</head>
<body>

<?php include('navbar.php'); ?>

<main class="container" style="max-width: 400px; margin-top: 50px;">
    <h2>Login de Paciente</h2>

    <?php if (!empty($mensagem)): ?>
        <div style="color: red; margin-bottom: 20px;">
            <?= htmlspecialchars($mensagem) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="login_paciente.php" autocomplete="off">
        <label for="email">E-mail:</label>
        <input type="email" name="email" required class="form-control">

        <label for="senha">Senha:</label>
        <input type="password" name="senha" required class="form-control">

        <button type="submit" class="btn-login" style="margin-top: 20px;">Entrar</button>
    </form>

    <div style="margin-top: 20px;">
        <a href="recuperar_senha.php" style="font-size: 0.9em;">Esqueceu a senha?</a>
    </div>
</main>

<footer style="margin-top: 50px;">
    <p>&copy; 2025 Clínica Oral Care. Todos os direitos reservados.</p>
</footer>

</body>
</html>
