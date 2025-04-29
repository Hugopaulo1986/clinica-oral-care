<?php
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api.sendgrid.com/");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_CAINFO, 'C:\xampp\php\extras\ssl\cacert.pem'); // mesmo caminho usado no php.ini
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

$result = curl_exec($ch);
if (curl_errno($ch)) {
    echo "❌ Erro: " . curl_error($ch);
} else {
    echo "✅ Conexão SSL OK!";
}
curl_close($ch);
