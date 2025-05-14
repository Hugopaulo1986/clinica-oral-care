<?php
require 'config.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';
require 'PHPMailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!(
    (isset($_SESSION["usuario_id"]) && in_array($_SESSION["tipo"], ["dentista", "recepcionista"]))
    || isset($_SESSION["paciente_id_login"])
)) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    echo "<script>alert('ID da consulta não especificado.'); history.back();</script>";
    exit();
}

$id_consulta = intval($_GET['id']);

// Pega informações da consulta
$sql = "SELECT c.data_consulta, c.hora_consulta, p.nome AS paciente_nome, p.email AS paciente_email
        FROM consultas c
        INNER JOIN pacientes p ON c.paciente_id = p.id
        WHERE c.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_consulta);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<script>alert('Consulta não encontrada.'); history.back();</script>";
    exit();
}

$consulta = $result->fetch_assoc();

// Atualiza status para "cancelado"
$sql_update = "UPDATE consultas SET status = 'cancelado' WHERE id = ?";
$stmt_update = $conn->prepare($sql_update);
$stmt_update->bind_param("i", $id_consulta);

if ($stmt_update->execute()) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'oralcare.consultas@gmail.com';
        $mail->Password = 'mhge xelw vkrs emll';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->CharSet = 'UTF-8';
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true,
            ]
        ];

        $mail->setFrom('oralcare.consultas@gmail.com', 'Clínica Oral Care');
        $mail->addAddress($consulta['paciente_email'], $consulta['paciente_nome']);
        $mail->isHTML(true);
        $mail->Subject = 'Cancelamento de Consulta - Clínica Oral Care';
        $mail->Body = "<h2>Cancelamento de Consulta</h2>
            <p>Olá, <strong>{$consulta['paciente_nome']}</strong>!</p>
            <p>Informamos que sua consulta agendada para o dia 
            <strong>" . date('d/m/Y', strtotime($consulta['data_consulta'])) . "</strong> às 
            <strong>" . date('H:i', strtotime($consulta['hora_consulta'])) . "</strong> foi 
            <strong>cancelada</strong>.</p>
            <p>Se desejar, agende uma nova consulta pelo nosso site.</p>
            <p>Atenciosamente,<br>Clínica Oral Care</p>";

        $mail->send();
    } catch (Exception $e) {
        // Falha ao enviar e-mail (mas a consulta já foi cancelada)
    }

    // ✅ Aqui detectamos se é paciente ou funcionário para redirecionar corretamente
    if (isset($_SESSION["paciente_id_login"])) {
        echo "<script>alert('Consulta cancelada!'); window.location.href='minhas_consultas.php';</script>";
    } else {
        echo "<script>alert('Consulta cancelada!'); window.location.href='visualizar_consultas.php';</script>";
    }
} else {
    echo "<script>alert('Erro ao cancelar a consulta.'); history.back();</script>";
}

$conn->close();
?>
