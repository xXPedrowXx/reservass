<?php
include 'inc/query.php';
require_once('teste_api_calendar/calendar-API-tutorial-main/google-calendar-api.php');

// Configura cabeçalho para retornar JSON
header('Content-Type: application/json');

// Verifica se a requisição é do tipo POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $values = $_POST['values'] ?? null;
    $externos = $_POST['externos'] ?? [];
    $reserva_id = $_POST['reserva_id'] ?? null;

    // Validação dos dados recebidos
    if (!$reserva_id) {
        error_log("Dados incompletos: reserva_id: $reserva_id, values: " . json_encode($values) . ", externos: " . json_encode($externos));
        echo json_encode(['success' => false, 'message' => 'Dados incompletos']);
        exit;
    }

    // Seleciona dados da reserva
    $query = "SELECT * FROM reservas WHERE id = $reserva_id";
    $resultado = $conn->query($query);
    if ($resultado && $resultado->num_rows > 0) {
        $reservas = $resultado->fetch_assoc();
        $data_inicio = $reservas['data_inicio'];
        $data_fim = $reservas['data_fim'];

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

        // Data sem ajuste de fuso horário para comparação no banco

    } else {
        echo json_encode(['success' => false, 'message' => 'Reserva não encontrada']);
        exit;
    }

    // Seleciona dados do evento no calendário com a data ajustada
    $query = "SELECT * FROM calendar_api WHERE data_inicio = '$data_inicio_api' AND data_fim = '$data_fim_api'";
    $result = $conn->query($query);
    if ($result && $result->num_rows > 0) {
        $calendar_api = $result->fetch_assoc();
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

    // Insere cada membro associado à reserva
    if ($values) {
        foreach ($values as $id) {
            insertM($id, $reserva_id, $conn);
        }
    }

    // Insere cada membro externo na tabela user_temp
    if ($externos) {
        foreach ($externos as $email) {
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
            $externos
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
