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

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['alert'] = ['type' => 'danger', 'message' => 'Acesso inválido!'];
    header('Location: form_contato.php');
    exit();
}

// Captura dados
$nome = trim($_POST['nome'] ?? '');
$email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
$mensagem = trim($_POST['mensagem'] ?? '');

if ($nome === '' || $email === '' || $mensagem === '') {
    $_SESSION['alert'] = ['type' => 'danger', 'message' => 'Preencha todos os campos obrigatórios!'];
    header('Location: form_contato.php');
    exit();
}

try {
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'oralcare.consultas@gmail.com';
    $mail->Password   = 'mhge xelw vkrs emll'; 
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;
    $mail->CharSet    = 'UTF-8';

    // SSL para localhost
    $mail->SMTPOptions = [
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true,
        ]
    ];

    // De quem o site envia (fixo para não bloquear)
    $mail->setFrom('oralcare.consultas@gmail.com', 'Clínica Oral Care');
    $mail->addAddress('oralcare.consultas@gmail.com', 'Clínica Oral Care');

    $mail->isHTML(true);
    $mail->Subject = '📩 Novo Contato via Site';
    $mail->Body    = "
        <h3>📬 Novo contato recebido</h3>
        <p><strong>Nome:</strong> {$nome}</p>
        <p><strong>E-mail:</strong> {$email}</p>
        <p><strong>Mensagem:</strong><br>{$mensagem}</p>
    ";

    $mail->send();
    $_SESSION['alert'] = ['type' => 'success', 'message' => '✅ Mensagem enviada com sucesso!'];

} catch (Exception $e) {
    $_SESSION['alert'] = ['type' => 'danger', 'message' => 'Erro: ' . $mail->ErrorInfo];
}

header('Location: form_contato.php');
exit();
?>
