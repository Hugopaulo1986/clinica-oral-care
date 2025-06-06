<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'config.php'; // conexão com banco
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    $_SESSION['alert'] = ['type' => 'danger', 'message' => 'Acesso inválido'];
    header('Location: agendamento_consulta.php');
    exit();
}

// Captura dados do formulário
$dados = [
    'nome' => trim($_POST['nome'] ?? ''),
    'email' => filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL),
    'telefone' => preg_replace('/[^0-9]/', '', $_POST['telefone'] ?? ''),
    'data' => $_POST['data'] ?? '',
    'hora' => $_POST['hora'] ?? '',
    'dentista_id' => (int) ($_POST['dentista_id'] ?? 0),
    'observacoes' => trim($_POST['observacoes'] ?? '')
];

// Validação básica
if (in_array('', [$dados['nome'], $dados['email'], $dados['data'], $dados['hora']]) || $dados['dentista_id'] === 0) {
    $_SESSION['alert'] = ['type' => 'danger', 'message' => 'Preencha todos os campos obrigatórios!'];
    header('Location: agendamento_consulta.php');
    exit();
}

try {
    $conn->begin_transaction();

    $stmt = $conn->prepare("SELECT nome FROM dentistas WHERE id = ?");
    $stmt->bind_param("i", $dados['dentista_id']);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows === 0) throw new Exception("Dentista não encontrado.");
    $dentista = $res->fetch_assoc();

    $stmt = $conn->prepare("SELECT id FROM pacientes WHERE email = ?");
    $stmt->bind_param("s", $dados['email']);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows > 0) {
        $paciente_id = $res->fetch_assoc()['id'];
    } else {
        $stmt = $conn->prepare("INSERT INTO pacientes (nome, telefone, email) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $dados['nome'], $dados['telefone'], $dados['email']);
        $stmt->execute();
        $paciente_id = $conn->insert_id;
    }

    $stmt = $conn->prepare("SELECT id FROM consultas WHERE dentista_id = ? AND data_consulta = ? AND hora_consulta = ?");
    $stmt->bind_param("iss", $dados['dentista_id'], $dados['data'], $dados['hora']);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        throw new Exception("Horário já está ocupado.");
    }

    $stmt = $conn->prepare("INSERT INTO consultas (data_consulta, hora_consulta, dentista_id, observacoes, paciente_id, nome_paciente, telefone, email)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssisssss", $dados['data'], $dados['hora'], $dados['dentista_id'], $dados['observacoes'],
                      $paciente_id, $dados['nome'], $dados['telefone'], $dados['email']);
    $stmt->execute();

    $conn->commit();

    // ENVIO DE E-MAIL
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'oralcare.consultas@gmail.com';
    $mail->Password = 'mhge xelw vkrs emll'; 
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;
    $mail->CharSet = 'UTF-8';
    
    // Desativar verificação pesada de SSL para localhost
    $mail->SMTPOptions = [
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true,
        ]
    ];

    $mail->setFrom('oralcare.consultas@gmail.com', 'Clínica Oral Care');
    $mail->addAddress($dados['email'], $dados['nome']);

    $mail->isHTML(true);
    $mail->Subject = 'Confirmação de Agendamento';
    $mail->Body    = "
        <h2>Confirmação de Agendamento</h2>
        <p>Olá, {$dados['nome']}!</p>
        <p>Sua consulta foi agendada com sucesso:</p>
        <ul>
            <li><strong>Data:</strong> {$dados['data']}</li>
            <li><strong>Hora:</strong> {$dados['hora']}</li>
            <li><strong>Dentista:</strong> {$dentista['nome']}</li>
        </ul>
        " . (!empty($dados['observacoes']) ? "<p><strong>Observações:</strong> {$dados['observacoes']}</p>" : "");

    $mail->send();
    $_SESSION['alert'] = ['type' => 'success', 'message' => '✅ Consulta marcada e e-mail enviado!'];

} catch (Exception $e) {
    $conn->rollback();
    $_SESSION['alert'] = ['type' => 'danger', 'message' => 'Erro: ' . $e->getMessage()];
}

header('Location: agendamento_consulta.php');
exit();
