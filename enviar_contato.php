<?php
use SendGrid\Mail\Mail;

require 'config.php';
require __DIR__ . '/vendor/autoload.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['alert'] = ['type' => 'danger', 'message' => 'Acesso inválido!'];
    header('Location: form_contato.php');
    exit();
}

// Forçar uso do certificado SSL correto (evita erro cURL 60)
putenv('CURL_CA_BUNDLE=C:\xampp\php\extras\ssl\cacert.pem');

// Sanitizar entrada
$nome = trim($_POST['nome'] ?? '');
$email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
$mensagem = trim($_POST['mensagem'] ?? '');

if ($nome === '' || $email === '' || $mensagem === '') {
    $_SESSION['alert'] = ['type' => 'danger', 'message' => 'Preencha todos os campos obrigatórios!'];
    header('Location: form_contato.php');
    exit();
}

// Criar email
$emailObj = new Mail();
$emailObj->setFrom("oralcare.consultas@gmail.com", "Clínica Oral Care");
$emailObj->setSubject("📩 Novo Contato via Site");
$emailObj->addTo("oralcare.consultas@gmail.com", "Clínica Oral Care");

$htmlContent = "<h3>📬 Novo contato recebido</h3>"
    . "<p><strong>Nome:</strong> {$nome}</p>"
    . "<p><strong>E-mail:</strong> {$email}</p>"
    . "<p><strong>Mensagem:</strong><br>{$mensagem}</p>";

$emailObj->addContent("text/html", $htmlContent);

// Enviar
$sendgrid = new \SendGrid(SENDGRID_API_KEY);
try {
    $response = $sendgrid->send($emailObj);
    if ($response->statusCode() >= 200 && $response->statusCode() < 300) {
        $_SESSION['alert'] = ['type' => 'success', 'message' => '✅ Mensagem enviada com sucesso!'];
    } else {
        $_SESSION['alert'] = ['type' => 'warning', 'message' => 'Mensagem não pôde ser enviada. Erro HTTP ' . $response->statusCode()];
    }
} catch (Exception $e) {
    $_SESSION['alert'] = ['type' => 'danger', 'message' => 'Erro: ' . $e->getMessage()];
}

header('Location: form_contato.php');
exit();
