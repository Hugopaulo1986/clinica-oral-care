<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('config.php'); // Conexão com o banco

$mensagem = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST["email"]);
    $senha = $_POST["senha"]; // Senha digitada pelo usuário

    if (empty($email) || empty($senha)) {
        $mensagem = "❌ Preencha todos os campos!";
    } else {
        // Verifica se o usuário existe em dentistas
        $sql_dentista = "SELECT * FROM dentistas WHERE email = ?";
        $stmt_dentista = mysqli_prepare($conn, $sql_dentista);
        mysqli_stmt_bind_param($stmt_dentista, "s", $email);
        mysqli_stmt_execute($stmt_dentista);
        $resultado_dentista = mysqli_stmt_get_result($stmt_dentista);
        $dentista = mysqli_fetch_assoc($resultado_dentista);

        // Verifica se o usuário existe em recepcionistas
        $sql_recepcionista = "SELECT * FROM recepcionistas WHERE email = ?";
        $stmt_recepcionista = mysqli_prepare($conn, $sql_recepcionista);
        mysqli_stmt_bind_param($stmt_recepcionista, "s", $email);
        mysqli_stmt_execute($stmt_recepcionista);
        $resultado_recepcionista = mysqli_stmt_get_result($stmt_recepcionista);
        $recepcionista = mysqli_fetch_assoc($resultado_recepcionista);

        // Define se o usuário é dentista ou recepcionista
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
            // Comparação de senha simples (sem hash)
            if ($senha == $usuario["senha"]) { 
                $_SESSION["usuario_id"] = $usuario["id"];
                $_SESSION["usuario_nome"] = $usuario["nome"];

                // Redirecionamento para o painel correto
                if ($_SESSION["tipo"] == "dentista") {
                    header("Location: painel_dentista.php");
                    exit();
                } elseif ($_SESSION["tipo"] == "recepcionista") {
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
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="css/estilo.css?v=<?php echo time(); ?>"> <!-- CSS correto -->
</head>
<body>

<?php include('navbar.php'); ?> <!-- Incluindo a navbar padrão -->

<main class="container">
    <h2>Login</h2>
    <form action="login.php" method="POST">
        <label for="email">Email:</label>
        <input type="email" name="email" required placeholder="Digite seu email">
        
        <label for="senha">Senha:</label>
        <input type="password" name="senha" required placeholder="Digite sua senha">
        
        <button type="submit">Entrar</button>
    </form>
    
    <?php if (!empty($mensagem)) echo "<p style='color:red; text-align:center;'>$mensagem</p>"; ?>
</main>

<footer>
    <p>&copy; 2025 Clínica Oral Care. Todos os direitos reservados.</p>
</footer>

</body>
</html>
