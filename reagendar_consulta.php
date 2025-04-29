<?php
session_start();

if (!isset($_SESSION["usuario_id"]) || !in_array($_SESSION["tipo"], ["dentista", "recepcionista"])) {
    header("Location: login.php");
    exit();
}

require_once('config.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';
require 'PHPMailer/Exception.php';

$erro = "";
$sucesso = "";

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Consulta não especificada.");
}

$id_consulta = intval($_GET['id']);

$sql = "SELECT * FROM consultas WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_consulta);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Consulta não encontrada.");
}

$consulta = $result->fetch_assoc();
$dentista_id = $consulta['dentista_id'];
$paciente_id = $consulta['paciente_id'];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nova_data = $_POST["nova_data"] ?? '';
    $novo_horario = $_POST["novo_horario"] ?? '';

    if (empty($nova_data) || empty($novo_horario)) {
        $erro = "⚠ Preencha todos os campos.";
    } elseif ($nova_data === $consulta['data_consulta'] && $novo_horario === $consulta['hora_consulta']) {
        $erro = "⚠ A nova data e horário são os mesmos da consulta atual.";
    } else {
        $verifica = $conn->prepare("SELECT id FROM consultas WHERE data_consulta = ? AND hora_consulta = ? AND dentista_id = ? AND id != ?");
        $verifica->bind_param("ssii", $nova_data, $novo_horario, $dentista_id, $id_consulta);
        $verifica->execute();
        $res_verifica = $verifica->get_result();

        if ($res_verifica->num_rows > 0) {
            $erro = "⚠ Horário já ocupado. Escolha outro.";
        } else {
            $update = $conn->prepare("UPDATE consultas SET data_consulta = ?, hora_consulta = ?, status = 'pendente' WHERE id = ?");
            $update->bind_param("ssi", $nova_data, $novo_horario, $id_consulta);

            if ($update->execute()) {
                $stmt_paciente = $conn->prepare("SELECT nome, email FROM pacientes WHERE id = ?");
                $stmt_paciente->bind_param("i", $paciente_id);
                $stmt_paciente->execute();
                $res_paciente = $stmt_paciente->get_result();
                $paciente = $res_paciente->fetch_assoc();

                if ($paciente && !empty($paciente['email'])) {
                    $mail = new PHPMailer(true);

                    try {
                        $mail->isSMTP();
                        $mail->Host = 'smtp.gmail.com';
                        $mail->SMTPAuth = true;
                        $mail->Username = 'oralcare.consultas@gmail.com';
                        $mail->Password = 'mejevfmrxligfmzb';
                        $mail->SMTPSecure = 'tls';
                        $mail->Port = 587;

                        $mail->SMTPOptions = [
                            'ssl' => [
                                'verify_peer' => false,
                                'verify_peer_name' => false,
                                'allow_self_signed' => true
                            ]
                        ];

                        $mail->setFrom('oralcare.consultas@gmail.com', 'Clínica Oral Care');
                        $mail->addAddress($paciente['email'], $paciente['nome']);

                        $mail->isHTML(true);
                        $mail->CharSet = 'UTF-8';
                        $mail->Subject = 'Reagendamento de Consulta';
                        $mail->Body = "<p>Olá <strong>{$paciente['nome']}</strong>,</p>
                            <p>Sua consulta foi reagendada para:</p>
                            <p><strong>Data:</strong> " . date("d/m/Y", strtotime($nova_data)) . "<br>
                            <strong>Horário:</strong> " . date("H:i", strtotime($novo_horario)) . "</p>
                            <p>Se tiver dúvidas, entre em contato conosco.</p>
                            <p><em>Atenciosamente,<br>Clínica Oral Care</em></p>";

                        $mail->send();
                        $sucesso = "✅ Consulta reagendada e e-mail enviado com sucesso!";
                    } catch (Exception $e) {
                        $sucesso = "⚠ Consulta reagendada, mas erro no envio do e-mail: {$mail->ErrorInfo}";
                    }
                } else {
                    $sucesso = "✅ Consulta reagendada. (E-mail do paciente não encontrado)";
                }
            } else {
                $erro = "❌ Erro ao reagendar consulta.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Reagendar Consulta</title>
    <link rel="stylesheet" href="css/estilo.css?v=<?= time(); ?>">
    <script>
    async function carregarHorarios() {
        const data = document.getElementById('nova_data').value;
        const dentista = <?= $dentista_id ?>;
        const select = document.getElementById('novo_horario');
        select.innerHTML = '<option>Carregando...</option>';

        const response = await fetch(`horarios_disponiveis.php?data=${data}&dentista_id=${dentista}`);
        const horarios = await response.json();

        select.innerHTML = '';
        if (horarios.length === 0) {
            select.innerHTML = '<option disabled>Sem horários disponíveis</option>';
        } else {
            horarios.forEach(horario => {
                const opt = document.createElement('option');
                opt.value = horario;
                opt.textContent = horario;
                select.appendChild(opt);
            });
        }
    }
    </script>
</head>
<body>
<?php include('navbar.php'); ?>
<div class="container mt-5">
    <div class="card shadow p-4">
        <h2 class="text-center mb-3">Reagendar Consulta</h2>

        <?php if (!empty($erro)) : ?>
            <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
        <?php elseif (!empty($sucesso)) : ?>
            <div class="alert alert-success"><?= htmlspecialchars($sucesso) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label for="nova_data" class="form-label">Nova Data</label>
                <input type="date" id="nova_data" name="nova_data" class="form-control" required onchange="carregarHorarios()">
            </div>

            <div class="mb-3">
                <label for="novo_horario" class="form-label">Novo Horário</label>
                <select id="novo_horario" name="novo_horario" class="form-control" required>
                    <option>Selecione uma data</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary w-100">🔄 Reagendar Consulta</button>
        </form>
    </div>
</div>
</body>
</html>