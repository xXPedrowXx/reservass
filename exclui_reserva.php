<?php
include('teste_api_calendar/calendar-API-tutorial-main/google-calendar-api.php');
include './inc/query.php';

if(isset($_GET['id'])) {
    $id = $_GET['id'];
    $sala_id = $_GET['sala_id'];

    // Seleciona a reserva pelo ID
    selectID('reservas', $id, $conn);
    global $resultado;
    $reserva = $resultado->fetch_assoc();
    if (!$reserva) {
        die("Reserva não encontrada.");
    }

    // Ajusta o fuso horário
    $timezone = new DateTimeZone('America/Sao_Paulo'); // Substitua pelo fuso horário correto

    // Converte e ajusta a data de início
    $data_inicio = new DateTime($reserva['data_inicio'], $timezone);
    $data_inicio->modify('+3 hours');
    $data_inicio = $data_inicio->format('Y-m-d H:i:s');

    // Converte e ajusta a data de fim
    $data_fim = new DateTime($reserva['data_fim'], $timezone);
    $data_fim->modify('+3 hours');
    $data_fim = $data_fim->format('Y-m-d H:i:s');

    // Seleciona o nome da sala pelo ID
    selectID('salas', $sala_id, $conn);
    $sala = $resultado->fetch_assoc();
    if (!$sala) {
        die("Sala não encontrada.");
    }
    $nome_sala = $sala['nome_sala'];

    // Seleciona o event_id do calendar_api
    $sql = "SELECT event_id FROM calendar_api WHERE titulo = ? AND data_inicio = ? AND data_fim = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $nome_sala, $data_inicio, $data_fim);
    $stmt->execute();
    $result = $stmt->get_result();
    $event = $result->fetch_assoc();
    $stmt->close();

    if (!$event) {
      
        die("Evento não encontrado no calendar_api.");
       
    }
    $event_id = $event['event_id'];

    // Instancia a classe GoogleCalendarApi
    $googleCalendarApi = new GoogleCalendarApi();

    // Chama o método para excluir o evento do Google Calendar
    try {
        $googleCalendarApi->DeleteCalendarEvent($event_id, 'primary', $_SESSION['access_token']);
    } catch (Exception $e) {
        echo $event_id ;
        echo '<br>';
        echo $_SESSION['access_token'];
        echo '<br>';
        die("Erro ao excluir evento do Google Calendar: " . $e->getMessage());
    }

    // Chama a função para excluir a reserva
    delete_reserva($id, $conn);

    header("Location: calendario.php?");
} else {
    echo "ID da categoria não foi fornecido.";
}