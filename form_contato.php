<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Contato - Clínica Oral Care</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/estilo.css">
</head>
<body>

<?php include 'navbar.php'; ?>

<?php if (isset($_SESSION['alert'])): ?>
  <div class="alert alert-<?php echo $_SESSION['alert']['type']; ?> text-center w-75 mx-auto" style="margin-top: 90px;" role="alert">
      <?php echo $_SESSION['alert']['message']; ?>
  </div>
  <?php unset($_SESSION['alert']); ?>
<?php endif; ?>

<main class="container my-5">
  <h2 class="text-center mb-4">Entre em Contato</h2>

  <div class="contato-box">
    <!-- Formulário de Contato -->
    <div class="card">
      <h4><i class="fas fa-paper-plane me-2"></i>Formulário</h4>
      <form action="enviar_contato.php" method="POST" class="needs-validation" novalidate>
        <div class="mb-3">
          <label for="nome" class="form-label">Nome:</label>
          <input type="text" class="form-control" name="nome" required>
          <div class="invalid-feedback">Informe seu nome completo.</div>
        </div>
        <div class="mb-3">
          <label for="email" class="form-label">E-mail:</label>
          <input type="email" class="form-control" name="email" required>
          <div class="invalid-feedback">Informe um e-mail válido.</div>
        </div>
        <div class="mb-3">
          <label for="mensagem" class="form-label">Mensagem:</label>
          <textarea class="form-control" name="mensagem" rows="4" required></textarea>
          <div class="invalid-feedback">Digite sua mensagem.</div>
        </div>
        <div class="text-center">
          <button type="submit" class="btn btn-success">
            <i class="fas fa-paper-plane"></i> Enviar
          </button>
        </div>
      </form>
    </div>

    <!-- Informações da Clínica -->
    <div class="card">
      <h4><i class="fas fa-info-circle me-2"></i>Informações da Clínica</h4>
      <p><i class="fas fa-map-marker-alt me-2"></i><strong>Endereço:</strong><br>Rua das Flores, 123 – São Paulo – SP</p>
      <p><i class="fas fa-phone me-2"></i><strong>Telefone:</strong><br>(11) 98765-4321<br>(11) 91234-5678 (WhatsApp)</p>
      <p><i class="fas fa-envelope me-2"></i><strong>E-mail:</strong><br>oralcare.consultas@gmail.com</p>
    </div>
  </div>

  <!-- Mapa -->
  <div class="mapa-container">
    <h2 class="text-center mb-4">Nossa Localização</h2>
    <div class="ratio ratio-21x9">
      <iframe 
        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3657.37667469358!2d-46.65855792544171!3d-23.55485047880036!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x94ce59cd0250f36b%3A0x15c5b4e8a5d64a0d!2sAv.%20Paulista%2C%201234%20-%20Bela%20Vista%2C%20SP!5e0!3m2!1spt-BR!2sbr!4v1717122836150!5m2!1spt-BR!2sbr" 
        width="100%" height="450" style="border:0;" allowfullscreen loading="lazy">
      </iframe>
    </div>
  </div>
</main>

<footer>
  <p>&copy; 2025 Clínica Oral Care. Todos os direitos reservados.</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Validação do formulário
(() => {
  'use strict'
  const forms = document.querySelectorAll('.needs-validation')
  Array.from(forms).forEach(form => {
    form.addEventListener('submit', event => {
      if (!form.checkValidity()) {
        event.preventDefault()
        event.stopPropagation()
      }
      form.classList.add('was-validated')
    }, false)
  })
})()
</script>

</body>
</html>
