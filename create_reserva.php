<?php
include 'inc/query.php';

// Configura cabeçalho para retornar JSON
header('Content-Type: application/json');

// Verifica se a requisição é do tipo POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data_inicio = $_POST['data_inicio'] ?? null;
    $data_fim = $_POST['data_fim'] ?? null;
    $sala_id = $_POST['salaId'] ?? null;
    $url = $_POST['url'] ?? null;
    $user_id = $_SESSION['id'] ?? null; // Obtém o user_id da sessão

    // Validação dos dados recebidos
    if (!$data_inicio || !$data_fim || !$sala_id || !$user_id) {
        error_log("Dados incompletos: sala_id: $sala_id, data_inicio: $data_inicio, data_fim: $data_fim, user_id: $user_id");
        echo json_encode(['success' => false, 'message' => 'Dados incompletos']);
        exit;
    }

    // Inserir a reserva
    try {
        $reserva_id = insertR($user_id, $sala_id, $data_inicio, $data_fim, $url, $conn);

        if ($reserva_id) {
            // Retorna apenas o reserva_id
            echo json_encode(['reserva_id' => $reserva_id]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao criar a reserva']);
        }
    } catch (mysqli_sql_exception $e) {
        error_log("Erro ao criar a reserva: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Erro ao criar a reserva: ' . $e->getMessage()]);
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