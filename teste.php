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
    
    // Ajuste de 3 horas para o fuso horário das datas antigas
    $data_inicio_antiga_obj = new DateTime($data_inicio_antiga);
    $data_fim_antiga_obj = new DateTime($data_fim_antiga);
    $data_inicio_antiga_obj->modify('+3 hours');
    $data_fim_antiga_obj->modify('+3 hours');
    $data_inicio_antiga = $data_inicio_antiga_obj->format('Y-m-d H:i:s');
    $data_fim_antiga = $data_fim_antiga_obj->format('Y-m-d H:i:s');

    // Busca o event_id na tabela calendar_api com as datas ajustadas
    $calendar_api = getCalendarEvent($data_inicio_antiga, $data_fim_antiga, $conn);
    if ($calendar_api) {
        $event_id = $calendar_api['event_id'];
    } else {
        error_log("Erro ao adicionar membros: Evento no calendário não encontrado. Data início: $data_inicio_antiga, Data fim: $data_fim_antiga");
        echo json_encode(['success' => false, 'message' => 'Evento no calendário não encontrado', 'data_inicio' => $data_inicio_antiga, 'data_fim' => $data_fim_antiga]);
        exit;
    }

    // Ajuste de 3 horas para o fuso horário das novas datas
    $data_inicio = $event_details['event_time']['start_time'];
    $data_fim = $event_details['event_time']['end_time'];
    $data_inicio_obj = new DateTime($data_inicio);
    $data_fim_obj = new DateTime($data_fim);
    $data_inicio_obj->modify('+3 hours');
    $data_fim_obj->modify('+3 hours');
    $data_inicio_api = $data_inicio_obj->format('Y-m-d H:i:s');
    $data_fim_api = $data_fim_obj->format('Y-m-d H:i:s');
    $data_fim_obj->format('Y-m-d H:i:s');
    $data_inicio_obj->format('Y-m-d H:i:s');

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
            ['a41992215756@gmail.com','phlopes646@gmail.com']  // Substituir com os emails desejados
        );

        // Atualiza as informações na tabela calendar_api
       

        echo json_encode(['success' => true, 'message' => 'Reserva atualizada com sucesso!']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }

    $query = "UPDATE calendar_api SET titulo = ?, data_inicio = ?, data_fim = ? WHERE event_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssss", $title, $data_inicio_api, $data_fim_api, $event_id);
    $stmt->execute();


    update_R($data_inicio_obj,  $data_fim_obj, $url, $reserva_id, $salaIdInput, $user_id, $conn);
    if ($stmt->error) {
        throw new Exception("Erro na consulta UPDATE: " . $stmt->error);
    }





} else {
    echo json_encode(['success' => false, 'message' => 'Dados inválidos ou ausentes.']);
}

exit;
?>