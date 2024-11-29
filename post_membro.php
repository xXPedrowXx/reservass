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
    if (!$values && !$externos || !$reserva_id) {
        error_log("Dados incompletos: reserva_id: $reserva_id, values: " . json_encode($values) . ", externos: " . json_encode($externos));
        echo json_encode(['success' => false, 'message' => 'Dados incompletos']);
        exit;
    }

    // Insere cada membro associado à reserva
    if ($values) {
        foreach ($values as $id) {
            error_log("Inserindo membro com ID: $id na reserva: $reserva_id");
            try {
                $sql = $conn->prepare("INSERT INTO membros (user_id, reserva_id) VALUES (?, ?)");
                $sql->bind_param("ii", $id, $reserva_id);
                $sql->execute();
                $sql->close();
            } catch (mysqli_sql_exception $e) {
                handleError("Erro ao cadastrar membro atentar-se aos usuarios já adicionados: " . $e->getMessage());
            }
        }
    }

    // Insere cada membro externo na tabela user_temp
    if ($externos) {
        foreach ($externos as $email) {
            error_log("Inserindo membro externo com email: $email na reserva: $reserva_id");
            try {
                $sql = $conn->prepare("INSERT INTO user_temp (reserva_id, email) VALUES (?, ?)");
                $sql->bind_param("is", $reserva_id, $email);
                $sql->execute();
                $sql->close();
            } catch (mysqli_sql_exception $e) {
                handleError("Erro ao cadastrar membro externo: " . $e->getMessage());
            }
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