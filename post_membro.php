<?php
include 'inc/query.php';

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
    selectId('reservas', $reserva_id, $conn);
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
        echo json_encode(['success' => false, 'message' => 'Reserva não encontrada'.$reserva_id]);
        exit;
    }

    // Valida os emails
    $valid_emails = [];
    if ($externos) {
        foreach ($externos as $email) {
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $valid_emails[] = $email;
            } else {
                error_log("Email inválido: $email");
            }
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

    echo json_encode(['success' => true, 'message' => 'Membros adicionados com sucesso']);
} else {
    // Método HTTP não permitido
    error_log("Método não permitido: " . $_SERVER['REQUEST_METHOD']);
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
}

// Fecha a conexão com o banco
$conn->close();
?>