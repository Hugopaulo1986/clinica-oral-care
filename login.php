<?php
session_start();
require 'config.php';

$mensagem = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST["usuario_login"]);
    $senha = $_POST["usuario_senha"];

    if (empty($email) || empty($senha)) {
        $mensagem = "❌ Preencha todos os campos!";
    } else {
        $sql_dentista = "SELECT * FROM dentistas WHERE email = ?";
        $stmt_dentista = mysqli_prepare($conn, $sql_dentista);
        mysqli_stmt_bind_param($stmt_dentista, "s", $email);
        mysqli_stmt_execute($stmt_dentista);
        $resultado_dentista = mysqli_stmt_get_result($stmt_dentista);
        $dentista = mysqli_fetch_assoc($resultado_dentista);

        $sql_recepcionista = "SELECT * FROM recepcionistas WHERE email = ?";
        $stmt_recepcionista = mysqli_prepare($conn, $sql_recepcionista);
        mysqli_stmt_bind_param($stmt_recepcionista, "s", $email);
        mysqli_stmt_execute($stmt_recepcionista);
        $resultado_recepcionista = mysqli_stmt_get_result($stmt_recepcionista);
        $recepcionista = mysqli_fetch_assoc($resultado_recepcionista);

        if ($dentista) {
            $usuario = $dentista;
            $_SESSION["tipo"] = "dentista";
        } elseif ($recepcionista) {
            $usuario = $recepcionista;
            $_SESSION["tipo"] = "recepcionista";
        } else {
            $usuario = null;
        }

        if ($usuario) {
            if ($senha == $usuario["senha"]) {
                $_SESSION["usuario_id"] = $usuario["id"];
                $_SESSION["usuario_nome"] = $usuario["nome"];

                if ($_SESSION["tipo"] == "dentista") {
                    $_SESSION["dentista_id"] = $usuario["id"];
                    header("Location: painel_dentista.php");
                    exit();
                } elseif ($_SESSION["tipo"] == "recepcionista") {
                    $_SESSION["recepcionista_id"] = $usuario["id"];
                    header("Location: painel_recepcionista.php");
                    exit();
                } else {
                    $mensagem = "🚨 ERRO: Tipo de usuário desconhecido!";
                }
            } else {
                $mensagem = "❌ Senha incorreta!";
            }
        } else {
            $mensagem = "❌ Usuário não encontrado!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Login (Dentista/Recepcionista)</title>
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
    <h2>Login (Dentista/Recepcionista)</h2>

    <p style="text-align: center; margin-bottom: 20px;">
        👉 <a href="login_paciente.php" style="color: #007BFF; text-decoration: underline;">É Paciente? Clique aqui para fazer login</a>
    </p>

    <form action="login.php" method="POST" autocomplete="off">
        <label for="email">Email:</label>
        <input type="email" name="usuario_login" id="email" required placeholder="Digite seu email" autocomplete="off" readonly onfocus="this.removeAttribute('readonly');">

        <label for="senha">Senha:</label>
        <input type="password" name="usuario_senha" id="senha" required placeholder="Digite sua senha" autocomplete="off" readonly onfocus="this.removeAttribute('readonly');">

        <button type="submit">Entrar</button>
    </form>

    <p style="text-align:center; margin-top:15px;">
        <a href="recuperar_senha.php">🔒 Esqueceu a senha?</a>
    </p>

    <?php if (!empty($mensagem)) echo "<p style='color:red; text-align:center; margin-top:15px;'>$mensagem</p>"; ?>
</main>

<footer>
    <p>&copy; 2025 Clínica Oral Care. Todos os direitos reservados.</p>
</footer>

</body>
</html>
