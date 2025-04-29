<?php
session_start();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tratamentos - Clínica Oral Care</title>
    <link rel="stylesheet" href="css/estilo.css">
    <style>
        /* 🔹 Fundo e Estilização Geral */
        body {
            background: url('img/fundo-tratamentos.jpg') no-repeat center center;
            background-size: cover;
            color: white;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        /* 🔹 Container ocupando 100% da largura */
        .container {
            text-align: center;
            padding: 40px;
            width: 100%;
            max-width: 100%;
            margin: 0 auto;
        }

        /* 🔹 Grid centralizado com espaçamento reduzido */
        .tratamentos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 10px; /* Espaçamento entre os botões */
            width: 80%;
            margin: 0 auto;
            justify-content: center;
        }

        /* 🔹 Estilização de Cada Tratamento (Botões Quadrados) */
        .tratamento {
            position: relative;
            width: 250px; /* Quadrado */
            height: 250px;
            overflow: hidden;
            border-radius: 10px;
            transition: transform 0.2s, box-shadow 0.2s;
            cursor: pointer;
        }

        .tratamento:hover {
            transform: scale(1.05);
            box-shadow: 0px 0px 10px rgba(255, 255, 255, 0.3);
        }

        /* 🔹 Imagens Ajustadas */
        .tratamento img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 10px;
            transition: filter 0.3s;
        }

        /* 🔹 Efeito ao Passar o Mouse */
        .tratamento:hover img {
            filter: brightness(40%);
        }

        /* 🔹 Texto dentro dos Botões */
        .descricao {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            background: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 6px;
            border-radius: 5px;
            opacity: 0;
            transition: opacity 0.3s;
            width: 80%;
        }

        .tratamento:hover .descricao {
            opacity: 1;
        }

        /* 🔹 Responsividade */
        @media (max-width: 1024px) {
            .tratamentos-grid {
                width: 90%;
                grid-template-columns: repeat(2, 1fr); /* 2 colunas em telas médias */
            }
        }

        @media (max-width: 768px) {
            .tratamentos-grid {
                width: 100%;
                grid-template-columns: repeat(2, 1fr); /* 2 colunas em celulares maiores */
            }
        }

        @media (max-width: 500px) {
            .tratamentos-grid {
                width: 100%;
                grid-template-columns: 1fr; /* 1 coluna em telas pequenas */
            }
        }
    </style>
</head>
<body>

    <?php include('navbar.php'); ?>

    <div class="container">
        <h1>Tratamentos Odontológicos</h1>
        <p>Passe o mouse sobre um tratamento para ver mais detalhes.</p>

        <div class="tratamentos-grid">
            <div class="tratamento">
                <img src="img/implante.jpg" alt="Implante">
                <div class="descricao">O implante dentário substitui dentes perdidos com parafusos de titânio.</div>
            </div>

            <div class="tratamento">
                <img src="img/limpeza.jpg" alt="Limpeza">
                <div class="descricao">A limpeza odontológica remove placas bacterianas e evita doenças gengivais.</div>
            </div>

            <div class="tratamento">
                <img src="img/protese.jpg" alt="Próteses Dentárias">
                <div class="descricao">Próteses fixas ou móveis para substituir dentes ausentes.</div>
            </div>

            <div class="tratamento">
                <img src="img/extracao.jpg" alt="Extração">
                <div class="descricao">Remoção de dentes danificados ou com indicação cirúrgica.</div>
            </div>

            <div class="tratamento">
                <img src="img/clareamento.jpg" alt="Clareamento Dental">
                <div class="descricao">Técnica segura para deixar seus dentes mais brancos.</div>
            </div>

            <div class="tratamento">
                <img src="img/raiox.jpg" alt="Raio X Panorâmico">
                <div class="descricao">Imagem detalhada da arcada dentária para diagnósticos precisos.</div>
            </div>

            <div class="tratamento">
                <img src="img/canal.jpg" alt="Tratamento de Canal">
                <div class="descricao">Tratamento que remove a polpa do dente comprometido e evita extrações.</div>
            </div>

            <div class="tratamento">
                <img src="img/periodontal.jpg" alt="Tratamento Periodontal">
                <div class="descricao">Cuidado com gengivas inflamadas para evitar perda dentária.</div>
            </div>

            <div class="tratamento">
                <img src="img/siso.jpg" alt="Extração de Siso">
                <div class="descricao">Remoção dos dentes do siso quando causam desconforto ou desalinhamento.</div>
            </div>

            <div class="tratamento">
                <img src="img/restauracao.jpg" alt="Restauração">
                <div class="descricao">Restauração dentária para corrigir cáries e recuperar a estrutura do dente.</div>
            </div>

            <div class="tratamento">
                <img src="img/ortodontia.jpg" alt="Ortodontia">
                <div class="descricao">Correção do alinhamento dos dentes com aparelhos ortodônticos.</div>
            </div>

            <div class="tratamento">
                <img src="img/acido_hialuronico.jpg" alt="Ácido Hialurônico">
                <div class="descricao">Rejuvenescimento facial e hidratação da pele com ácido hialurônico.</div>
            </div>
        </div>
    </div>

</body>
</html>
