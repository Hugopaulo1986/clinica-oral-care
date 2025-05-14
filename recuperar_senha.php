<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'config.php';
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$mensagem = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);

    if (empty($email)) {
        $mensagem = "<p class='erro'>❌ Informe o e-mail cadastrado.</p>";
    } else {
        // Aqui poderia verificar no banco se o email existe 
        try {
            // Preparar PHPMailer
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'oralcare.consultas@gmail.com'; 
            $mail->Password = 'mhge xelw vkrs emll'; 
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            $mail->CharSet = 'UTF-8';

            // Desativar SSL forte para localhost
            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true,
                ]
            ];

            $mail->setFrom('oralcare.consultas@gmail.com', 'Clínica Oral Care');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Recuperação de Senha - Clínica Oral Care';
            $mail->Body    = '
                <h2>Recuperação de Senha</h2>
                <p>Recebemos sua solicitação para redefinir a senha.</p>
                <p><a href="http://localhost/clinica_oral_care/redefinir_senha.php">Clique aqui para redefinir sua senha</a>.</p>
                <p>Se você não solicitou, ignore este e-mail.</p>
            ';

            $mail->send();
            $mensagem = "<p class='sucesso'>📧 Instruções enviadas para seu e-mail!</p>";

        } catch (Exception $e) {
            $mensagem = "<p class='erro'>❌ Erro ao enviar: {$mail->ErrorInfo}</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Recuperar Senha</title>
    <link rel="stylesheet" href="css/estilo.css?v=<?= time(); ?>">
</head>
<body>

<?php include('navbar.php'); ?>

<main class="container">
    <h2>Recuperar Senha</h2>

    <?= $mensagem; ?>

    <form action="recuperar_senha.php" method="POST" autocomplete="off">
        <label for="email">Digite seu e-mail cadastrado:</label>
        <input type="email" id="email" name="email" required placeholder="Seu e-mail" autocomplete="off" readonly onfocus="this.removeAttribute('readonly');">

        <button type="submit">Enviar instruções</button>
    </form>
</main>

<footer>
    <p>&copy; 2025 Clínica Oral Care. Todos os direitos reservados.</p>
</footer>

</body>
</html>
