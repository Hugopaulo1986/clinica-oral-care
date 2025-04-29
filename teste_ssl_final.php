<?php
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api.sendgrid.com");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CAINFO, "C:/xampp/php/extras/ssl/cacert.pem");
$response = curl_exec($ch);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    echo "❌ Erro cURL: $error";
} else {
    echo "✅ Conexão com SendGrid OK!";
}