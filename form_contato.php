<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contato - Clínica Oral Care</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>
    <?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    ?>

    <?php include 'navbar.php'; ?>

    <?php if (isset($_SESSION['alert'])): ?>
        <div class="alert alert-<?php echo $_SESSION['alert']['type']; ?> text-center mx-auto mt-4 w-75" role="alert">
            <?php echo $_SESSION['alert']['message']; ?>
        </div>
        <?php unset($_SESSION['alert']); ?>
    <?php endif; ?>

    <div class="container mt-5">
        <div class="card p-4">
            <h2 class="text-center">Entre em Contato</h2>
            <form action="enviar_contato.php" method="POST">
                <div class="mb-3">
                    <label for="nome" class="form-label">Nome:</label>
                    <input type="text" class="form-control" name="nome" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">E-mail:</label>
                    <input type="email" class="form-control" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="mensagem" class="form-label">Mensagem:</label>
                    <textarea class="form-control" name="mensagem" rows="5" required></textarea>
                </div>
                <div class="text-center">
                    <button type="submit" class="btn btn-success">Enviar Mensagem</button>
                </div>
            </form>
        </div>

        <div class="mt-5">
            <h2 class="text-center">Localização da Clínica</h2>
            <div class="ratio ratio-16x9">
                <iframe 
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3153.0194174256476!2d-122.42005078469293!3d37.77851937975825!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8085808cfa60cd65%3A0x4b1a3b5c8e1e72b0!2sCl%C3%ADnica%20Exemplo!5e0!3m2!1spt-BR!2sbr!4v1618234567890!5m2!1spt-BR!2sbr" 
                    allowfullscreen="" loading="lazy">
                </iframe>
            </div>
        </div>
    </div>

    <?php include 'rodape.php'; ?>
</body>
</html>
