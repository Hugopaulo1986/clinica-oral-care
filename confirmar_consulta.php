<?php
require 'config.php';
session_start();

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Atualiza status para confirmado
    $sql = "UPDATE consultas SET status = 'confirmado' WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        // Pergunta se deseja cadastrar o paciente
        echo "<script>
            if (confirm('✅ Consulta confirmada! Deseja cadastrar o paciente?')) {
                window.location.href = 'cadastro_paciente.php';
            } else {
                window.location.href = 'painel_dentista.php'; // ou painel_recepcionista.php
            }
        </script>";
        exit();
    } else {
        echo "Erro ao confirmar a consulta.";
    }
} else {
    echo "ID da consulta inválido.";
}
?>
