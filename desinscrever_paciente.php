<?php
require_once 'config.php';

if (!isset($_GET['id'])) {
    header("Location: gerenciar_pacientes.php");
    exit();
}

$id = intval($_GET['id']);

$stmt = $conn->prepare("UPDATE pacientes SET ativo = 0 WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: gerenciar_pacientes.php");
exit();
?>
