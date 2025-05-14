<?php
require 'config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["dentista_id"]) && !isset($_SESSION["recepcionista_id"])) {
    header("Location: login.php");
    exit();
}

$sql = "SELECT c.id, c.nome_paciente, c.telefone, c.data_consulta, c.hora_consulta, 
               d.nome AS dentista_nome, c.status
        FROM consultas c
        JOIN dentistas d ON c.dentista_id = d.id
        WHERE c.status != 'cancelado'
        ORDER BY c.data_consulta, c.hora_consulta";
$result = mysqli_query($conn, $sql);

$consultas = [];
while ($row = mysqli_fetch_assoc($result)) {
    $cor = '';
    switch (strtolower($row['status'])) {
        case 'confirmado':
            $cor = '#28a745';
            break;
        case 'pendente':
            $cor = '#ffc107';
            break;
        default:
            $cor = '#007bff';
    }

    $start = $row['data_consulta'] . 'T' . $row['hora_consulta'];
    $startDateTime = new DateTime($start);
    $endDateTime = clone $startDateTime;
    $endDateTime->modify('+30 minutes');

    $consultas[] = [
        'id' => $row['id'],
        'title' => $row['nome_paciente'] . " - " . $row['dentista_nome'],
        'start' => $startDateTime->format('Y-m-d\TH:i:s'),
        'end' => $endDateTime->format('Y-m-d\TH:i:s'),
        'backgroundColor' => $cor,
        'borderColor' => $cor,
        'extendedProps' => [
            'paciente' => $row['nome_paciente'],
            'dentista' => $row['dentista_nome'],
            'hora' => date("H:i", strtotime($row['hora_consulta'])),
            'status' => ucfirst($row['status']),
            'id' => $row['id']
        ]
    ];
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Consultas Agendadas</title>
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
    <link rel="stylesheet" href="css/estilo.css?v=<?= time(); ?>">
    <style>
    .calendar-container {
        max-width: 90vw;
        margin: 20px auto;
    }
    #calendar {
        background: white;
        padding: 10px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        min-height: 700px;
    }
    .tooltip-custom {
        position: absolute;
        background: white;
        border: 1px solid #ccc;
        padding: 10px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        display: none;
        z-index: 9999;
        font-size: 14px;
        max-width: 250px;
    }
    .btn-reagendar, .btn-desmarcar {
        margin-top: 8px;
        display: inline-block;
        padding: 4px 8px;
        font-size: 13px;
        text-decoration: none;
        border-radius: 5px;
    }
    .btn-reagendar {
        background-color: #17a2b8;
        color: white;
    }
    .btn-desmarcar {
        background-color: #dc3545;
        color: white;
        margin-left: 5px;
    }
    .btn-fechar {
        margin-top: 10px;
        padding: 5px 10px;
        background: #dc3545;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }
    </style>
</head>
<body>

<?php include('navbar.php'); ?>

<div class="container-top">
    <h2 class="text-center my-3">📋 Consultas Agendadas</h2>
</div>

<div class="calendar-container">
    <div id="calendar"></div>
</div>

<div id="tooltip" class="tooltip-custom"></div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var tooltip = document.getElementById('tooltip');
    var tooltipFixo = false;

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'timeGridWeek',
        locale: 'pt-br',
        allDaySlot: false,
        slotMinTime: "08:00:00",
        slotMaxTime: "16:30:00",
        slotDuration: "00:30:00",
        hiddenDays: [0,6],
        contentHeight: "auto",
        expandRows: true,
        events: <?= json_encode($consultas); ?>,
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'timeGridDay,timeGridWeek,dayGridMonth'
        },
        eventMouseEnter: function(info) {
            if (!tooltipFixo) {
                preencherTooltip(info);
            }
        },
        eventMouseLeave: function(info) {
            if (!tooltipFixo) {
                tooltip.style.display = 'none';
            }
        },
        eventClick: function(info) {
            tooltipFixo = true;
            preencherTooltip(info);
        }
    });

    calendar.render();

    window.fecharTooltip = function() {
        tooltip.style.display = 'none';
        tooltipFixo = false;
    };

    document.addEventListener('click', function(event) {
        if (!calendarEl.contains(event.target) && !tooltip.contains(event.target)) {
            tooltip.style.display = 'none';
            tooltipFixo = false;
        }
    });

    function preencherTooltip(info) {
        var event = info.event.extendedProps;
        tooltip.innerHTML = `
            <strong>Paciente:</strong> ${event.paciente}<br>
            <strong>Dentista:</strong> ${event.dentista}<br>
            <strong>Hora:</strong> ${event.hora}<br>
            <strong>Status:</strong> ${event.status}<br><br>
            <a href="reagendar_consulta.php?id=${event.id}" class="btn-reagendar">🔄 Reagendar</a>
            <a href="desmarcar_consulta.php?id=${event.id}" class="btn-desmarcar" onclick="return confirm('Deseja mesmo desmarcar?')">❌ Desmarcar</a>
            <br><button class="btn-fechar" onclick="fecharTooltip()">Fechar</button>
        `;
        tooltip.style.display = 'block';
        tooltip.style.left = (info.jsEvent.pageX + 15) + 'px';
        tooltip.style.top = (info.jsEvent.pageY + 15) + 'px';
    }
});
</script>

  <!-- 🔹 Rodapé -->
  <footer>
    <p>&copy; 2025 Clínica Oral Care. Todos os direitos reservados.</p>
  </footer>


</body>
</html>
