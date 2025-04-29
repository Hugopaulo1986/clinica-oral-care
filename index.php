<?php
session_start(); // Mantém a sessão ativa
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Clínica Oral Care</title>
  <link rel="stylesheet" href="css/estilo.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <link rel="icon" href="img/favicon.ico" type="image/x-icon"> <!-- Sugestão de favicon -->
</head>

<body>

  <!-- 🔹 Navbar -->
  <?php include('navbar.php'); ?>

  <main>
    <!-- 🔹 Banner Principal -->
    <section class="banner">
      <h1>Bem-vindo à Clínica Oral Care</h1>
      <p>Cuidamos do seu sorriso com excelência e profissionalismo!</p>
      <img src="img/dentist.png" alt="Logo Clínica Oral Care" class="logo-central">

      <!-- 🔹 Botão de Agendamento -->
      <a href="agendamento_consulta.php" class="btn-agendar">Agendar Consulta</a>
    </section>

    <!-- 🔹 Benefícios da Clínica -->
    <section class="info">
      <h2>Por que escolher a Oral Care?</h2>
      <p>Nossa equipe altamente qualificada está pronta para proporcionar os melhores tratamentos odontológicos, com tecnologia avançada e atendimento humanizado.</p>
      
      <div class="cards">
        <div class="card">
          <h3>Profissionais Especializados</h3>
          <p>Equipe de dentistas experientes prontos para cuidar do seu sorriso.</p>
        </div>
        <div class="card">
          <h3>Atendimento Personalizado</h3>
          <p>Consultas adaptadas às suas necessidades e conforto.</p>
        </div>
        <div class="card">
          <h3>Agendamentos Rápidos</h3>
          <p>Marque sua consulta de forma simples e prática com nosso sistema online.</p>
        </div>
      </div>
    </section>
  </main>

  <!-- 🔹 Rodapé -->
  <footer>
    <p>&copy; 2025 Clínica Oral Care. Todos os direitos reservados.</p>
  </footer>

  <!-- 🔹 Menu Mobile (se houver) -->
  <script>
    $(document).ready(function() {
      $('#mobile-menu').on('click keypress', function(event) {
        if (event.type === 'click' || event.key === 'Enter') {
          $('.nav-list').toggleClass('active');
        }
      });
    });
  </script>

</body>
</html>
