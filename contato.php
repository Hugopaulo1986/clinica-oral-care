<?php
// Ativar exibição de erros
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Incluir PHPMailer manualmente
require 'phpmailer/PHPMailer.php';
require 'phpmailer/SMTP.php';
require 'phpmailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "✅ Formulário chegou!<br>";

    $nome = htmlspecialchars($_POST['nome']);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $mensagem = htmlspecialchars($_POST['mensagem']);

    echo "Nome: $nome<br>Email: $email<br>Mensagem: $mensagem<br>";

    if (empty($nome) || empty($email) || empty($mensagem)) {
        echo "<script>alert('❌ Por favor, preencha todos os campos.'); history.back();</script>";
        exit();
    }

    try {
        $mail = new PHPMailer(true);
        echo "📨 PHPMailer inicializado!<br>";

        // 🛠️ Ativa debug detalhado do SMTP
        $mail->SMTPDebug = 2; // Mostra toda comunicação com o servidor
        $mail->Debugoutput = 'html';

        // Configurações SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'oralcare.consultas@gmail.com';
        $mail->Password = 'mejevfmrxligfmzb'; // App password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->CharSet = 'UTF-8';

        // Endereçamento
        $mail->setFrom($email, $nome);
        $mail->addAddress('oralcare.consultas@gmail.com', 'Clínica Oral Care');

        // Conteúdo do e-mail
        $mail->isHTML(true);
        $mail->Subject = 'Novo Contato - Clínica Oral Care';
        $mail->Body    = "
            <p><strong>Nome:</strong> {$nome}</p>
            <p><strong>E-mail:</strong> {$email}</p>
            <p><strong>Mensagem:</strong><br>{$mensagem}</p>";

        // Enviar e testar
        $resultado = $mail->send();
        var_dump($resultado); // Mostra true ou false
        echo "<br><strong>Erro SMTP:</strong> " . $mail->ErrorInfo;

    } catch (Exception $e) {
        echo "<script>alert('❌ Erro ao enviar a mensagem: {$mail->ErrorInfo}'); history.back();</script>";
    }

} else {
    echo "<script>alert('⚠ Acesso inválido!'); window.location.href='form_contato.php';</script>";
}
?>
