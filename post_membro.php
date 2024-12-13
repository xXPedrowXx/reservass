<?php
include 'inc/query.php';
require_once('teste_api_calendar/calendar-API-tutorial-main/google-calendar-api.php');

// Configura cabeçalho para retornar JSON
header('Content-Type: application/json');

// Verifica se a requisição é do tipo POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $values = $_POST['values'] ?? null;
    $externos = $_POST['externos'] ?? null;
    $reserva_id = $_POST['reserva_id'] ?? null;

    // Validação dos dados recebidos
    if (!$reserva_id) {
        error_log("Dados incompletos: reserva_id: $reserva_id, values: " . json_encode($values) . ", externos: " . json_encode($externos));
        echo json_encode(['success' => false, 'message' => 'Dados incompletos']);
        exit;
    }

    // Seleciona dados da reserva
selectId('reservas',$reserva_id,$conn);
if ($resultado->num_rows > 0) {
    $row = $resultado->fetch_assoc();
        $data_inicio = $row['data_inicio'];
        $data_fim = $row['data_fim'];

        // Ajuste de 3 horas para o fuso horário
        $data_inicio_obj = new DateTime($data_inicio);
        $data_fim_obj = new DateTime($data_fim);

        // Ajuste de +3 horas no horário
        $data_inicio_obj_ajustada = clone $data_inicio_obj;
        $data_fim_obj_ajustada = clone $data_fim_obj;

        $data_inicio_obj_ajustada->modify('+3 hours');
        $data_fim_obj_ajustada->modify('+3 hours');

        // Converte as datas ajustadas para o formato de string
        $data_inicio_api = $data_inicio_obj_ajustada->format('Y-m-d H:i:s');
        $data_fim_api = $data_fim_obj_ajustada->format('Y-m-d H:i:s');

    } else {
        echo json_encode(['success' => false, 'message' => 'Reserva não encontrada']);
        exit;
    }

    $calendar_api = getCalendarEvent($data_inicio_api, $data_fim_api, $conn);
    if ($calendar_api) {
        $event_id = $calendar_api['event_id'];
        $titulo = $calendar_api['titulo'];
    } else {
        error_log("Erro ao adicionar membros: Evento no calendário não encontrado. Data início: $data_inicio_api, Data fim: $data_fim_api");
        echo json_encode(['success' => false, 'message' => 'Evento no calendário não encontrado', 'data_inicio' => $data_inicio_api, 'data_fim' => $data_fim_api]);
        exit;
    }

    $event_id = $calendar_api['event_id'];
    $titulo = $calendar_api['titulo'];

    // Prepara os dados de tempo para o evento
    $event_time = array(
        'start_time' => $data_inicio_obj->format('Y-m-d\TH:i:s'), // Formato ISO 8601 com T
        'end_time' => $data_fim_obj->format('Y-m-d\TH:i:s')
    );

    // Se $externos for null, busca os emails dos membros e usuários

   if (is_null($externos)) {
    $externos = getEmails($reserva_id, $conn);
} else {
    // Se $externos não for null, adiciona os membros existentes
    $externos = array_merge($externos, getEmails($reserva_id, $conn));
}

    // Valida os emails
    $valid_emails = [];
    foreach ($externos as $email) {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $valid_emails[] = $email;
        } else {
            error_log("Email inválido: $email");
        }
    }

    // Insere cada membro associado à reserva
    if ($values) {
        foreach ($values as $id) {
            insertM($id, $reserva_id, $conn);
        }
    }

    // Insere cada membro externo na tabela user_temp
    if ($valid_emails) {
        foreach ($valid_emails as $email) {
            insert_user_temp($reserva_id, $email, $conn);
        }
    }

    // Atualiza o evento no Google Calendar
    try {
        $googleCalendarApi = new GoogleCalendarApi();
        $googleCalendarApi->UpdateCalendarEvent(
            $event_id, 
            'primary', 
            $titulo, 
            false, 
            $event_time, 
            $_SESSION['user_timezone'], 
            $_SESSION['access_token'], 
            $valid_emails
        );
        echo json_encode(['success' => true, 'message' => 'Membros associados à reserva com sucesso']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    // Método HTTP não permitido
    error_log("Método não permitido: " . $_SERVER['REQUEST_METHOD']);
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
}

// Fecha a conexão com o banco
$conn->close();
?>