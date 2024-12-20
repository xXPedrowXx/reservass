<?php
include 'inc/query.php'; 

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
    $data_inicio_antiga_obj = new DateTime($data_inicio_antiga);
    $data_fim_antiga_obj = new DateTime($data_fim_antiga); 
    $data_inicio_antiga_obj->modify('+3 hours');
    $data_fim_antiga_obj->modify('+3 hours');

    $teste1 = $data_inicio_antiga_obj->format('Y-m-d H:i:s');
    $teste2 = $data_fim_antiga_obj->format('Y-m-d H:i:s');

    // Busca o event_id na tabela calendar_api com as datas ajustadas
 

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
