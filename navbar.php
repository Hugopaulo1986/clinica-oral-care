<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<nav class="navbar">
    <ul class="nav-list">
        <li><a href="index.php">Início</a></li>
        <li><a href="agendamento_consulta.php">Agendamentos</a></li>
        <li><a href="tratamentos.php">Tratamentos</a></li>
        <li><a href="sobre.php">Sobre Nós</a></li>
        <li><a href="form_contato.php">Contato</a></li>

        <?php 
        if (!isset($_SESSION["usuario_id"]) && !isset($_SESSION["paciente_id_login"])) : 
        ?>
            <li><a href="login.php" class="btn-login">Login</a></li>
            <li><a href="registrar_paciente.php" class="btn-area-dentista">Registrar-se</a></li>
        <?php 
        else : 
            if (isset($_SESSION["tipo"])) :
                if ($_SESSION["tipo"] == "dentista") : ?>
                    <li><a href="painel_dentista.php" class="btn-area-dentista">Área do Dentista</a></li>
                <?php elseif ($_SESSION["tipo"] == "recepcionista") : ?>
                    <li><a href="painel_recepcionista.php" class="btn-area-recepcionista">Área da Recepção</a></li>
                <?php 
                endif;
            elseif (isset($_SESSION["paciente_id_login"])) : ?>
                <li><a href="painel_paciente.php" class="btn-area-dentista">Área do Paciente</a></li>
            <?php 
            endif; 
        ?>
            <li><a href="logout.php" class="btn-logout">Sair</a></li>
        <?php endif; ?>
    </ul>
</nav>


<style>
    .navbar {
        width: 100%;
        background-color: #1ABC9C;
        padding: 15px 0;
        display: flex;
        justify-content: center;
        position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    z-index: 1030;
    }

    .nav-list {
        list-style: none;
        display: flex;
        gap: 20px;
        padding: 0;
        margin: 0;
    }

    .nav-list li {
        display: inline;
    }

    .nav-list a {
        text-decoration: none;
        font-weight: bold;
        padding: 12px 20px;
        font-size: 18px;
        transition: 0.3s;
        border-radius: 5px;
        color: white;
    }

    /* Botão Login */
    .btn-login {
        background-color: #FFA500;
        color: white;
    }

    .btn-login:hover {
        background-color: #FF8C00;
    }

    /* Botão Registrar-se */
    .btn-area-dentista {
        background-color: #4CAF50;
        color: white;
    }

    .btn-area-dentista:hover {
        background-color: #3E8E41;
    }

    /* Botão Área Recepção */
    .btn-area-recepcionista {
        background-color: #007BFF;
        color: white;
    }

    .btn-area-recepcionista:hover {
        background-color: #0056b3;
    }

    /* Botão Logout */
    .btn-logout {
        background-color: #FF4C4C;
        color: white;
    }

    .btn-logout:hover {
        background-color: #D43F3F;
    }
</style>
