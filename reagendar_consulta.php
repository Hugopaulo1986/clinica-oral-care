<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

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
    isset($_SESSION["paciente_id_login"]) || 
    (isset($_SESSION["usuario_id"]) && in_array($_SESSION["tipo"], ["dentista", "recepcionista"]))
)) {
    header("Location: login_paciente.php");
    exit();
}

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
$paciente_id = $consulta['paciente_id'];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nova_data = $_POST["nova_data"] ?? '';
    $novo_horario = $_POST["novo_horario"] ?? '';
    $novo_dentista_id = $_POST["novo_dentista_id"] ?? '';

    if (empty($nova_data) || empty($novo_horario) || empty($novo_dentista_id)) {
        $erro = "⚠ Preencha todos os campos.";
    } else {
        $verifica = $conn->prepare("SELECT id FROM consultas WHERE data_consulta = ? AND hora_consulta = ? AND dentista_id = ? AND id != ?");
        $verifica->bind_param("ssii", $nova_data, $novo_horario, $novo_dentista_id, $id_consulta);
        $verifica->execute();
        $res_verifica = $verifica->get_result();

        if ($res_verifica->num_rows > 0) {
            $erro = "⚠ Horário já ocupado. Escolha outro.";
        } else {
            $update = $conn->prepare("UPDATE consultas SET data_consulta = ?, hora_consulta = ?, dentista_id = ?, status = 'pendente' WHERE id = ?");
            $update->bind_param("ssii", $nova_data, $novo_horario, $novo_dentista_id, $id_consulta);

            if ($update->execute()) {
                // Buscar dados do paciente
                $stmt_paciente = $conn->prepare("SELECT nome, email FROM pacientes WHERE id = ?");
                $stmt_paciente->bind_param("i", $paciente_id);
                $stmt_paciente->execute();
                $res_paciente = $stmt_paciente->get_result();
                $paciente = $res_paciente->fetch_assoc();

                // Buscar nome do dentista
                $stmt_dentista = $conn->prepare("SELECT nome FROM dentistas WHERE id = ?");
                $stmt_dentista->bind_param("i", $novo_dentista_id);
                $stmt_dentista->execute();
                $res_dentista = $stmt_dentista->get_result();
                $dentista = $res_dentista->fetch_assoc();

                if ($paciente && !empty($paciente['email'])) {
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
                        $mail->addAddress($paciente['email'], $paciente['nome']);

                        $mail->isHTML(true);
                        $mail->Subject = 'Confirmação de Reagendamento de Consulta';
                        $mail->Body = "<p>Olá <strong>{$paciente['nome']}</strong>,</p>
                        <p>A sua consulta foi <strong>Reagendada</strong> para:</p>
                        <ul>
                            <li><strong>Data:</strong> " . date("d/m/Y", strtotime($nova_data)) . "</li>
                            <li><strong>Horário:</strong> " . date("H:i", strtotime($novo_horario)) . "</li>
                            <li><strong>Dentista:</strong> {$dentista['nome']}</li>
                        </ul>
                        <p>Atenciosamente,<br>Clínica Oral Care</p>";

                        $mail->send();
                        $sucesso = "✅ Consulta Reagendada e E-mail enviado!";
                    } catch (Exception $e) {
                        $sucesso = "✅ Consulta reagendada, mas erro ao enviar e-mail.";
                    }
                } else {
                    $sucesso = "✅ Consulta reagendada.";
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/estilo.css?v=<?= time(); ?>" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
</head>
<body>

<?php include('navbar.php'); ?>

<div class="container mt-5">
    <div class="form-container mx-auto" style="max-width: 1100px;">
        <h2 class="mb-4 text-center" style="color: #0f665a;">Reagendar Consulta</h2>

        <?php if (!empty($erro)): ?>
            <div class="alert alert-danger text-center"><?= htmlspecialchars($erro) ?></div>
        <?php elseif (!empty($sucesso)): ?>
            <div class="alert alert-success text-center"><?= htmlspecialchars($sucesso) ?></div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-6">
                <form method="POST">
                    <div class="mb-3">
                        <label for="novo_dentista_id" class="form-label" style="color: #0f665a;">Dentista:</label>
                        <select name="novo_dentista_id" id="novo_dentista_id" class="form-control" required>
                            <option value="">Selecione...</option>
                            <?php
                            $dentistas = $conn->query("SELECT id, nome FROM dentistas ORDER BY nome");
                            while ($d = $dentistas->fetch_assoc()) {
                                echo "<option value='{$d['id']}'>{$d['nome']}</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="nova_data" class="form-label" style="color: #0f665a;">Nova Data:</label>
                        <input type="date" name="nova_data" id="nova_data" class="form-control" min="<?= date('Y-m-d') ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="novo_horario" class="form-label" style="color: #0f665a;">Novo Horário:</label>
                        <select name="novo_horario" id="novo_horario" class="form-control" required>
                            <option value="">Selecione a data primeiro...</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-success w-100">🔄 Confirmar Reagendamento</button>
                </form>
            </div>

            <div class="col-md-6">
                <div id="calendar"></div>
            </div>
        </div>
    </div>
</div>

<footer class="mt-5" style="background-color: #1ABC9C; color: white; padding: 20px 0; text-align: center;">
    <p>&copy; 2025 Clínica Oral Care. Todos os direitos reservados.</p>
</footer>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendar');
    const inputData = document.getElementById('nova_data');
    const selectDentista = document.getElementById('novo_dentista_id');
    const selectHorario = document.getElementById('novo_horario');

    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'pt-br',
        headerToolbar: { left: 'prev,next today', center: 'title', right: '' },
        dateClick: function(info) {
            inputData.value = info.dateStr;
            carregarHorarios();
        }
    });

    calendar.render();

    inputData.addEventListener('change', carregarHorarios);
    selectDentista.addEventListener('change', carregarHorarios);

    async function carregarHorarios() {
        const data = inputData.value;
        const dentistaId = selectDentista.value;

        if (!data || !dentistaId) return;

        const response = await fetch(`horarios_disponiveis.php?data=${data}&dentista_id=${dentistaId}`);
        const horarios = await response.json();

        selectHorario.innerHTML = '';
        if (horarios.length === 0) {
            selectHorario.innerHTML = '<option disabled>Sem horários disponíveis</option>';
        } else {
            horarios.forEach(hora => {
                const opt = document.createElement('option');
                opt.value = hora;
                opt.textContent = hora;
                selectHorario.appendChild(opt);
            });
        }
    }
});
</script>

</body>
</html>
