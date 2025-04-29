<?php
require 'config.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    $sql = "UPDATE consultas SET status = 'confirmado' WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        // 👉 Redireciona direto para o registro do procedimento
        header("Location: gerenciar_pacientes.php");
        exit();
    } else {
        echo "Erro ao confirmar a consulta.";
    }
} else {
    echo "ID da consulta inválido.";
}
?>
