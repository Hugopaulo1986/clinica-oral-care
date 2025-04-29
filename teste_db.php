<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servidor = "localhost";
$usuario = "root";
$senha = "";
$banco = "teste_db";

// Criando conexão
$conn = mysqli_connect($servidor, $usuario, $senha, $banco);

// Testando a conexão
if (!$conn) {
    die("❌ ERRO NA CONEXÃO COM O BANCO: " . mysqli_connect_error());
} else {
    echo "✅ CONEXÃO COM O BANCO FUNCIONANDO!";
}

// Testa a tabela dentistas
$sql = "SELECT * FROM dentistas";
$result = mysqli_query($conn, $sql);

if ($result) {
    echo "<br>✅ Tabela `dentistas` encontrada!";
} else {
    echo "<br>❌ ERRO: A tabela `dentistas` pode não existir!";
}
?>
