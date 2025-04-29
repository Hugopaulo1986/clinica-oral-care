<?php
require 'vendor/autoload.php';
require 'config.php'; // onde está definida a constante SENDGRID_API_KEY

$email = [
    'to' => 'oralcare.consultas@gmail.com', // <- coloque seu e-mail real aqui
    'nome' => 'Hugo Teste'
];

$conteudo = "
    <h2>Teste de Envio SendGrid</h2>
    <p>Olá, {$email['nome']}! Este é um teste de envio via SendGrid com cURL e certificado funcionando.</p>
";

$payload = [
    "personalizations" => [[
        "to" => [[ "email" => $email['to'], "name" => $email['nome'] ]],
        "subject" => "📬 Teste de Envio - Clínica Oral Care"
    ]],
    "from" => [ "email" => "oralcare.consultas@gmail.com", "name" => "Clínica Oral Care" ],
    "content" => [[ "type" => "text/html", "value" => $conteudo ]]
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://api.sendgrid.com/v3/mail/send');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . SENDGRID_API_KEY,
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_CAINFO, 'C:\xampp\php\extras\ssl\cacert.pem');;
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

$response = curl_exec($ch);
$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "<strong>Status:</strong> $status<br>";
echo "<strong>Erro:</strong> $error<br>";
echo "<strong>Resposta:</strong><pre>$response</pre>";
