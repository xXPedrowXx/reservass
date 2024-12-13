<?php
include 'inc/query.php'; 
require_once('teste_api_calendar/calendar-API-tutorial-main/google-calendar-api.php');
error_reporting(E_ALL);
ini_set("display_errors", 1);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['event_details'])) {
    $event_details = $_POST['event_details'];
    $reserva_id = $event_details['reserva_id'];

    $salaIdInput = $event_details['salaIdInput'];
    selectID('salas', $salaIdInput, $conn);
    $sala = $resultado->fetch_assoc();
    $title = $sala['nome_sala'];

    $url = $event_details['urlinput'];

    $user_id = $_SESSION['id'];
    
    // Obtém os dados da reserva para pegar a data_inicio e data_fim antigas
    selectID('reservas',  $reserva_id, $conn);
    $row = $resultado->fetch_assoc();
    $data_inicio_antiga = $row['data_inicio'];
    $data_fim_antiga = $row['data_fim'];
    
    // Ajuste para garantir que as datas estejam no formato correto (UTC)
    $data_inicio_antiga_obj = new DateTime($data_inicio_antiga, new DateTimeZone('UTC'));
    $data_fim_antiga_obj = new DateTime($data_fim_antiga, new DateTimeZone('UTC'));
    $data_inicio_antiga_obj->modify('+3 hours');
    $data_fim_antiga_obj->modify('+3 hours');

    // Busca o event_id na tabela calendar_api com as datas ajustadas
    $calendar_api = getCalendarEvent($data_inicio_antiga_obj->format('Y-m-d H:i:s'), $data_fim_antiga_obj->format('Y-m-d H:i:s'), $conn);
    if ($calendar_api) {
        $event_id = $calendar_api['event_id'];
    } else {
        error_log("Erro ao adicionar membros: Evento no calendário não encontrado. Data início: $data_inicio_antiga, Data fim: $data_fim_antiga");
        echo json_encode(['success' => false, 'message' => 'Evento no calendário não encontrado', 'data_inicio' => $data_inicio_antiga, 'data_fim' => $data_fim_antiga]);
        exit;
    }

    // Para as novas datas, faça o mesmo ajuste de fuso horário
    $data_inicio = $event_details['event_time']['start_time'];
    $data_fim = $event_details['event_time']['end_time'];
    
    $data_inicio_obj = new DateTime($data_inicio, new DateTimeZone('UTC'));  // UTC
    $data_fim_obj = new DateTime($data_fim, new DateTimeZone('UTC'));        // UTC
    
    // Aqui você pode ajustar para o fuso horário correto, se necessário
    // Exemplo: se a timezone do usuário for a "America/Sao_Paulo":
    $data_inicio_obj->setTimezone(new DateTimeZone('America/Sao_Paulo'));
    $data_fim_obj->setTimezone(new DateTimeZone('America/Sao_Paulo'));

    $data_inicio_api = $data_inicio_obj->format('Y-m-d H:i:s');
    $data_fim_api = $data_fim_obj->format('Y-m-d H:i:s');

    // Debugging statements to check adjusted values
    error_log("Data início ajustada: " . $data_inicio_api);
    error_log("Data fim ajustada: " . $data_fim_api);

    try {
        $googleCalendarApi = new GoogleCalendarApi();
        $googleCalendarApi->UpdateCalendarEvent(
            $event_id,  // Utiliza o event_id encontrado
            'primary', 
            $title, 
            false, 
            $event_details['event_time'], 
            $_SESSION['user_timezone'], 
            $_SESSION['access_token'], 
            [ $_SESSION['email']]  
        );

        updateCalendarEvent($conn, $title, $data_inicio, $data_fim, $event_id);

        // Converte as datas ajustadas para o formato de string antes de chamar update_R
        $data_inicio_str = $data_inicio_obj->format('Y-m-d H:i:s');
        $data_fim_str = $data_fim_obj->format('Y-m-d H:i:s');

        update_R($data_inicio_api, $data_fim_api, $url, $reserva_id, $salaIdInput, $user_id, $conn);

        echo json_encode(['success' => true, 'message' => 'Reserva atualizada com sucesso!']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Dados inválidos ou ausentes.']);
}

exit;
?>
