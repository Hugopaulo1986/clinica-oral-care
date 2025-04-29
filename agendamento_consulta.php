<?php
require 'config.php';
date_default_timezone_set('America/Sao_Paulo');

// Inicia a sessão se ainda não estiver iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Exibe mensagens de sucesso, erro ou alerta após o agendamento
if (isset($_SESSION['alert'])) {
    echo "<div class='alert alert-{$_SESSION['alert']['type']}' style='margin: 20px; padding: 15px; border-radius: 5px;'>";
    echo $_SESSION['alert']['message'];
    echo "</div>";
    unset($_SESSION['alert']);
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head><head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendamento de Consulta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Link para o arquivo CSS -->
    <link href="css/estilo.css" rel="stylesheet">
    
    <!-- FullCalendar -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/locales-all.min.js"></script>

    <style>
        /* Outros estilos, se necessário */
    </style>
</head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendamento de Consulta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
   
    <!-- FullCalendar -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/locales-all.min.js"></script>

    <style>
        
        body {
            background-color: #f8f9fa;
        }
        .form-container {
            background-color: #ffffff;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h2 {
            color: #0d6efd;
        }
        #calendar {
            margin-top: 20px;
            background: #fff;
            border-radius: 8px;
            padding: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
        }
    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container mt-5">
    <div class="form-container">
        <h2 class="mb-4 text-center">Agende sua Consulta</h2>
        <div class="row">
            <!-- Formulário -->
            <div class="col-md-6">
                <form action="enviar_agendamento.php" method="POST">
                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome:</label>
                        <input type="text" class="form-control" name="nome" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">E-mail:</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="telefone" class="form-label">Telefone:</label>
                        <input type="text" class="form-control" name="telefone" required>
                    </div>
                    <div class="mb-3">
                        <label for="data" class="form-label">Data da Consulta:</label>
                        <input type="date" class="form-control" name="data" id="data" min="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="hora" class="form-label">Horário:</label>
                        <select class="form-control" name="hora" required>
                            <option value="">Selecione...</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="dentista_id" class="form-label">Dentista:</label>
                        <select class="form-control" name="dentista_id" required>
                            <option value="">Selecione...</option>
                            <?php
                            $sql = "SELECT id, nome FROM dentistas ORDER BY nome";
                            $resultado = $conn->query($sql);
                            if ($resultado) {
                                while ($row = $resultado->fetch_assoc()) {
                                    echo "<option value=\"{$row['id']}\">{$row['nome']}</option>";
                                }
                            } else {
                                echo "<option value=\"\">Erro ao buscar dentistas</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="observacoes" class="form-label">Observações:</label>
                        <textarea class="form-control" name="observacoes" rows="3" placeholder="Ex: Dor de dente, retorno..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Agendar Consulta</button>
                </form>
            </div>

            <!-- Calendário -->
            <div class="col-md-6">
                <div id="calendar"></div>
            </div>
        </div>
    </div>
</div>

<!-- ✅ Script do FullCalendar -->
<script>
document.addEventListener('DOMContentLoaded', async function () {
    const calendarEl = document.getElementById('calendar');
    const inputData = document.querySelector('input[name="data"]');
    const form = document.querySelector('form');
    const selectDentista = document.querySelector('select[name="dentista_id"]');
    const selectHora = document.querySelector('select[name="hora"]');

    let diasLotados = new Set();
    let horariosIndisponiveis = new Set();

    try {
        const response = await fetch('eventos_calendario.php');
        if (!response.ok) throw new Error('Erro ao buscar eventos do calendário.');
        const dados = await response.json();

        dados.forEach(e => {
            if (!e.disponivel) {
                diasLotados.add(e.data);
                horariosIndisponiveis.add(`${e.data} ${e.hora}`); // Armazena data e hora
            }
        });

        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'pt-br',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: ''
            },
            events: dados.map(evento => ({
                start: evento.data,
                display: 'background',
                color: evento.disponivel && new Date(evento.data).getDay() != 0 && new Date(evento.data).getDay() != 6 ? '#28a745' : '#dc3545' // Não mostra verde no sábado e domingo
            })),
            dateClick: function (info) {
                const day = new Date(info.dateStr).getDay(); // Obtém o dia da semana (0 para domingo, 6 para sábado)
                if (day === 0 || day === 6) { // Verifica se é sábado (6) ou domingo (0)
                    alert("⚠ Sábado e domingo são dias de folga!");
                    return;
                }
                if (diasLotados.has(info.dateStr)) {
                    alert("⚠ Esta data está totalmente lotada!");
                    return;
                }
                inputData.value = info.dateStr;
                inputData.focus();
            }
        });

        calendar.render();
    } catch (error) {
        console.error(error);
        alert("Erro ao carregar o calendário.");
    }

    async function atualizarHorariosDisponiveis() {
        const data = inputData.value;
        const dentistaId = selectDentista.value;

        if (!data || !dentistaId) return;

        try {
            const res = await fetch(`horarios_disponiveis.php?data=${data}&dentista_id=${dentistaId}`);
            if (!res.ok) throw new Error('Erro ao buscar horários.');
            const horarios = await res.json();

            selectHora.innerHTML = '';
            if (horarios.length === 0) {
                selectHora.innerHTML = '<option value="">Nenhum horário disponível</option>';
                return;
            }

            horarios.forEach(hora => {
                const opt = document.createElement('option');
                opt.value = hora;
                opt.textContent = hora;

                // Desabilita horário se já estiver marcado
                if (horariosIndisponiveis.has(`${data} ${hora}`)) {
                    opt.disabled = true; // Desabilita o horário já reservado
                    opt.textContent += ' (Indisponível)';
                }

                selectHora.appendChild(opt);
            });
        } catch (e) {
            console.error(e);
            alert("Erro ao carregar horários disponíveis.");
        }
    }

    inputData.addEventListener('change', atualizarHorariosDisponiveis);
    selectDentista.addEventListener('change', atualizarHorariosDisponiveis);

    form.addEventListener('submit', function (e) {
        const dataSelecionada = inputData.value;
        const horaSelecionada = selectHora.value;
        if (horariosIndisponiveis.has(`${dataSelecionada} ${horaSelecionada}`)) {
            e.preventDefault();
            alert("❌ O horário selecionado não está disponível. Por favor, escolha outro horário.");
        }
    });
});
</script>

</body>
<?php if (isset($_GET['sucesso'])): ?>
    <div class="alert alert-success text-center mt-3 container">
        ✅ Consulta agendada com sucesso! Verifique seu e-mail para a confirmação.
    </div>
<?php endif; ?>
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

</html>
