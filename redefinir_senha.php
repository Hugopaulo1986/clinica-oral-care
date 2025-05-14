<?php
require 'config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


$mensagem = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $nova_senha = trim($_POST["nova_senha"]);
    $confirmar_senha = trim($_POST["confirmar_senha"]);

    if (empty($email) || empty($nova_senha) || empty($confirmar_senha)) {
        $mensagem = "<p class='erro'>❌ Preencha todos os campos!</p>";
    } elseif ($nova_senha !== $confirmar_senha) {
        $mensagem = "<p class='erro'>❌ As senhas não coincidem!</p>";
    } elseif (strlen($nova_senha) < 6) {
        $mensagem = "<p class='erro'>❌ A nova senha deve ter pelo menos 6 caracteres!</p>";
    } else {
        $nova_senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);

        $atualizado = false;

        // 1. Tenta atualizar em pacientes_login
        $stmt = $conn->prepare("UPDATE pacientes_login SET senha = ? WHERE email = ?");
        $stmt->bind_param("ss", $nova_senha_hash, $email);
        $stmt->execute();
        if ($stmt->affected_rows > 0) {
            $atualizado = true;
        }
        $stmt->close();

        // 2. Se não atualizou, tenta em dentistas
        if (!$atualizado) {
            $stmt = $conn->prepare("UPDATE dentistas SET senha = ? WHERE email = ?");
            $stmt->bind_param("ss", $nova_senha_hash, $email);
            $stmt->execute();
            if ($stmt->affected_rows > 0) {
                $atualizado = true;
            }
            $stmt->close();
        }

        // 3. Se ainda não atualizou, tenta em recepcionistas
        if (!$atualizado) {
            $stmt = $conn->prepare("UPDATE recepcionistas SET senha = ? WHERE email = ?");
            $stmt->bind_param("ss", $nova_senha_hash, $email);
            $stmt->execute();
            if ($stmt->affected_rows > 0) {
                $atualizado = true;
            }
            $stmt->close();
        }

        if ($atualizado) {
            $mensagem = "<p class='sucesso'>✅ Senha atualizada com sucesso! Agora você pode fazer login.</p>";
        } else {
            $mensagem = "<p class='erro'>❌ E-mail não encontrado!</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Redefinir Senha</title>
    <link rel="stylesheet" href="css/estilo.css?v=<?= time(); ?>">
</head>
<body>

<?php include('navbar.php'); ?>

<main class="container">
    <h2>Redefinir Senha</h2>

    <?= $mensagem; ?>

    <form action="redefinir_senha.php" method="POST" autocomplete="off">
        <label for="email">Seu E-mail:</label>
        <input type="email" id="email" name="email" required placeholder="Digite seu e-mail" autocomplete="off" readonly onfocus="this.removeAttribute('readonly');">

        <label for="nova_senha">Nova Senha:</label>
        <input type="password" id="nova_senha" name="nova_senha" required placeholder="Nova senha" minlength="6" autocomplete="off" readonly onfocus="this.removeAttribute('readonly');">

        <label for="confirmar_senha">Confirmar Nova Senha:</label>
        <input type="password" id="confirmar_senha" name="confirmar_senha" required placeholder="Confirme a nova senha" minlength="6" autocomplete="off" readonly onfocus="this.removeAttribute('readonly');">

        <button type="submit">Redefinir Senha</button>
    </form>
</main>

<footer>
    <p>&copy; 2025 Clínica Oral Care. Todos os direitos reservados.</p>
</footer>

</body>
</html>
