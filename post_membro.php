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
    if ( !$reserva_id) {
        error_log("Dados incompletos: reserva_id: $reserva_id, values: " . json_encode($values) . ", externos: " . json_encode($externos));
        echo json_encode(['success' => false, 'message' => 'Dados incompletos']);
        exit;
    }

    // Insere cada membro associado à reserva
    if ($values) {
        foreach ($values as $id) {
            insertM ($id,$reserva_id,$conn);
        }
    }

    // Insere cada membro externo na tabela user_temp
    if ($externos) {
        foreach ($externos as $email) {
            insert_user_temp($reserva_id, $email, $conn);
        }
    }

    // Retorna sucesso
    echo json_encode(['success' => true, 'message' => 'Membros associados à reserva com sucesso']);
} else {
    // Método HTTP não permitido
    error_log("Método não permitido: " . $_SERVER['REQUEST_METHOD']);
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
}

// Fecha a conexão com o banco
$conn->close();
?>