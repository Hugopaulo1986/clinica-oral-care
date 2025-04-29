<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'config.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';
require 'PHPMailer/Exception.php';

date_default_timezone_set('America/Sao_Paulo');

// 📅 Define a data alvo (amanhã)
$data_alvo = date('Y-m-d', strtotime('+1 day'));

// 🔍 Busca consultas de amanhã que ainda não receberam lembrete
$sql = "SELECT c.id, c.nome_paciente, c.email, c.telefone, c.data_consulta, c.hora_consulta, c.observacoes, d.nome AS nome_dentista
        FROM consultas c
        INNER JOIN dentistas d ON c.dentista_id = d.id
        WHERE c.data_consulta = ? AND c.lembrete_enviado = 0";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $data_alvo);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    echo "✅ Nenhum lembrete pendente para enviar.\n";
    exit();
}

while ($row = $resultado->fetch_assoc()) {
    $mail = new PHPMailer(true);

    try {
        // 📧 Configurações SMTP (Gmail seguro)
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'oralcare.consultas@gmail.com';
        $mail->Password = 'mejevfmrxligfmzb'; 
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;
        $mail->CharSet = 'UTF-8';

        // ✅ Segurança com certificado verificado
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ];

        // ✉️ Remetente e destinatário
        $mail->setFrom('oralcare.consultas@gmail.com', 'Clínica Oral Care');
        $mail->addAddress($row['email'], $row['nome_paciente']);

        // ✍️ Conteúdo
        $mail->isHTML(true);
        $mail->Subject = '⏰ Lembrete de Consulta - Clínica Oral Care';

        $body = "
            <h2>Olá, {$row['nome_paciente']}!</h2>
            <p>Este é um lembrete da sua consulta agendada para <strong>amanhã</strong>.</p>
            <p><strong>Data:</strong> {$row['data_consulta']}<br>
            <strong>Hora:</strong> {$row['hora_consulta']}<br>
            <strong>Dentista:</strong> {$row['nome_dentista']}<br>
            <strong>Telefone:</strong> {$row['telefone']}</p>";

        if (!empty($row['observacoes'])) {
            $body .= "<p><strong>Observações:</strong> {$row['observacoes']}</p>";
        }

        $body .= "<p>Qualquer dúvida, entre em contato conosco.<br>Até breve!<br><em>Clínica Oral Care</em></p>";

        $mail->Body = $body;
        $mail->send();

        // ✅ Marca como enviado
        $update = $conn->prepare("UPDATE consultas SET lembrete_enviado = 1 WHERE id = ?");
        $update->bind_param("i", $row['id']);
        $update->execute();

        echo "✅ Lembrete enviado para: {$row['nome_paciente']} ({$row['email']})\n";

    } catch (Exception $e) {
        echo "❌ Erro ao enviar para {$row['email']}: {$mail->ErrorInfo}\n";
    }
}
?>

