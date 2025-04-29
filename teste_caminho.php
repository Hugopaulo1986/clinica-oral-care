<?php
$cert = realpath('C:/xampp/php/extras/ssl/cacert.pem');
echo "Caminho real: $cert";
echo "\n\nExiste? " . (file_exists($cert) ? 'Sim ✅' : 'Não ❌');
?>
