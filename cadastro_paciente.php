<?php
session_start();
if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit();
}

require_once('config.php');

$mensagem = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = trim($_POST["nome"]);
    $sexo = trim($_POST["sexo"]);
    $data_nasc = trim($_POST["data_nasc"]);
    $historico = trim($_POST["historico"]);
    $endereco = trim($_POST["endereco"]);
    $telefone = trim($_POST["telefone"]);
    $email = trim($_POST["email"]);
    $cpf = trim($_POST["cpf"]);
    $convenio = trim($_POST["convenio"]);

    if (empty($nome) || empty($sexo) || empty($data_nasc) || empty($telefone) || empty($email) || empty($cpf)) {
        $mensagem = "<p class='erro'>❌ Preencha todos os campos obrigatórios!</p>";
    } else {
        // Verifica se o CPF já existe
        $checkCpf = $conn->prepare("SELECT id FROM pacientes WHERE cpf = ?");
        $checkCpf->bind_param("s", $cpf);
        $checkCpf->execute();
        $checkCpf->store_result();

        if ($checkCpf->num_rows > 0) {
            $mensagem = "<p class='erro'>⚠️ Este CPF já está cadastrado!</p>";
        } else {
            $sql = "INSERT INTO pacientes (nome, sexo, data_nasc, historico, endereco, telefone, email, cpf, convenio) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);

            if ($stmt) {
                $stmt->bind_param("sssssssss", $nome, $sexo, $data_nasc, $historico, $endereco, $telefone, $email, $cpf, $convenio);
                if ($stmt->execute()) {
                    $mensagem = "<p class='sucesso'>✅ Paciente cadastrado com sucesso!</p>";
                } else {
                    $mensagem = "<p class='erro'>❌ Erro ao cadastrar: " . $stmt->error . "</p>";
                }
                $stmt->close();
            } else {
                $mensagem = "<p class='erro'>❌ Erro ao preparar a consulta: " . $conn->error . "</p>";
            }
        }
        $checkCpf->close();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Paciente</title>
    <link rel="stylesheet" href="css/estilo.css?v=<?= time(); ?>">
</head>
<body>

<?php include('navbar.php'); ?>

<main class="container">
    <h2>Cadastro de Paciente</h2>
    <?= $mensagem; ?>
    <form action="cadastro_paciente.php" method="POST" id="formCadastro">
        <label for="nome">Nome Completo:</label>
        <input type="text" id="nome" name="nome" required>

        <label for="sexo">Sexo:</label>
        <select id="sexo" name="sexo" required>
            <option value="">Selecione</option>
            <option value="Masculino">Masculino</option>
            <option value="Feminino">Feminino</option>
            <option value="Outro">Outro</option>
        </select>

        <label for="data_nasc">Data de Nascimento:</label>
        <input type="date" id="data_nasc" name="data_nasc" required>

        <label for="historico">Histórico Médico:</label>
        <textarea id="historico" name="historico"></textarea>

        <label for="endereco">Endereço:</label>
        <input type="text" id="endereco" name="endereco" required>

        <label for="telefone">Telefone:</label>
        <input type="text" id="telefone" name="telefone" required>

        <label for="email">E-mail:</label>
        <input type="email" id="email" name="email" required>

        <label for="cpf">CPF:</label>
        <input type="text" id="cpf" name="cpf" maxlength="14" required placeholder="000.000.000-00">

        <label for="convenio">Convênio (opcional):</label>
        <input type="text" id="convenio" name="convenio">

        <button type="submit">Cadastrar</button>
    </form>
</main>

<footer>
    <p>&copy; 2025 Clínica Oral Care. Todos os direitos reservados.</p>
</footer>

<!-- Scripts de Validação -->
<script>
// Máscara de CPF
document.getElementById('cpf').addEventListener('input', function() {
    let value = this.value.replace(/\D/g, '');
    if (value.length > 11) value = value.slice(0, 11);
    value = value.replace(/(\d{3})(\d)/, '$1.$2');
    value = value.replace(/(\d{3})(\d)/, '$1.$2');
    value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
    this.value = value;
});

// Máscara de Telefone
document.getElementById('telefone').addEventListener('input', function() {
    let value = this.value.replace(/\D/g, '');
    if (value.length > 11) value = value.slice(0, 11);
    value = value.replace(/^(\d{2})(\d)/g, '($1) $2');
    value = value.replace(/(\d{5})(\d{1,4})$/, '$1-$2');
    this.value = value;
});

// Validação de CPF antes de enviar
document.getElementById('formCadastro').addEventListener('submit', function(e) {
    const cpf = document.getElementById('cpf').value.replace(/\D/g, '');
    if (!validarCPF(cpf)) {
        e.preventDefault();
        alert('❌ CPF já Registrado! Verifique e tente novamente.');
        document.getElementById('cpf').focus();
    }
});

// Função para validar CPF
function validarCPF(cpf) {
    if (cpf.length != 11 || /^(\d)\1+$/.test(cpf)) return false;
    let soma = 0;
    for (let i = 0; i < 9; i++) soma += parseInt(cpf.charAt(i)) * (10 - i);
    let resto = (soma * 10) % 11;
    if (resto == 10 || resto == 11) resto = 0;
    if (resto != parseInt(cpf.charAt(9))) return false;
    soma = 0;
    for (let i = 0; i < 10; i++) soma += parseInt(cpf.charAt(i)) * (11 - i);
    resto = (soma * 10) % 11;
    if (resto == 10 || resto == 11) resto = 0;
    if (resto != parseInt(cpf.charAt(10))) return false;
    return true;
}
</script>

</body>
</html>
