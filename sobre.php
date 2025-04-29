<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sobre Nós - Clínica Oral Care</title>
    <link rel="stylesheet" href="css/estilo.css?v=<?php echo time(); ?>"> <!-- Atualiza o CSS automaticamente -->
    <link rel="stylesheet" href="css/sobre.css">
</head>
<body>

<?php include('navbar.php'); ?> <!-- 🔹 Incluindo a navbar padrão -->

<!-- Seção Sobre Nós -->
<main>
    <section class="sobre">
        <h1>Sobre a Clínica Oral Care</h1>
        <p>Há mais de 10 anos cuidando do seu sorriso com excelência e profissionalismo. Nossa missão é oferecer tratamentos odontológicos de alta qualidade, combinando tecnologia de ponta com um atendimento humanizado.</p>
        
        <div class="sobre-conteudo">
            <img src="img/clinica.jpg" alt="Nossa Clínica">
            <div class="texto">
                <h2>Nossa Equipe</h2>
                <p>Contamos com uma equipe de dentistas altamente qualificados e especializados em diversas áreas da odontologia. Nossa prioridade é garantir o seu bem-estar e a saúde do seu sorriso.</p>
            </div>
        </div>
    </section>
</main>

<!-- Rodapé -->
<footer>
    <p>&copy; 2025 Clínica Oral Care. Todos os direitos reservados.</p>
</footer>

<script>
    document.getElementById('mobile-menu').addEventListener('click', function() {
        document.querySelector('.nav-list').classList.toggle('active');
    });
</script>

</body>
</html>
