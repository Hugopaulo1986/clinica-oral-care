<?php
session_start();

// Remove todas as variáveis de sessão
$_SESSION = [];

// Destroi a sessão
session_destroy();

// Força a regeneração da sessão para evitar reutilização
session_regenerate_id(true);

// Redireciona para a página inicial
header("Location: index.php");
exit();
?>
