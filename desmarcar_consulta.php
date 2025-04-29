<?php
require 'config.php';

if (!isset($_GET['id'])) {
    echo "<script>alert('ID da consulta não especificado.'); history.back();</script>";
    exit();
}

$id_consulta = intval($_GET['id']);

// Exclui a consulta do banco de dados
$sql = "DELETE FROM consultas WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_consulta);

if ($stmt->execute()) {
    echo "<script>alert('Consulta desmarcada com sucesso!'); window.location.href='visualizar_consultas.php';</script>";
} else {
    echo "<script>alert('Erro ao desmarcar a consulta.'); history.back();</script>";
}

$conn->close();
?>
