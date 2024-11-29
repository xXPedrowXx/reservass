<?php


include 'inc/conexao.php'; 

function verificarPermissao($conn) {

    // Verificar se o usuário está logado
    if (!isset($_SESSION['id'])) {
        header("Location: login.php"); // Redirecionar para a página de login se o usuário não estiver logado
        exit;
    }

    $user_id = $_SESSION['id'];

    // Buscar dados do adm
    $sql_user = "SELECT permissao FROM users WHERE id = ?";
    $stmt_user = $conn->prepare($sql_user);
    $stmt_user->bind_param("i", $user_id);
    $stmt_user->execute();
    $result_user = $stmt_user->get_result();

    if ($result_user->num_rows > 0) {
        $user = $result_user->fetch_assoc();
        $user_permissao = $user['permissao'];

        $_SESSION['permissao'] = $user['permissao']; 
    } else {
        die("Usuário não encontrado.");
    }

    if ($user_permissao != 5) {
        header("Location: index.php"); // Redirecionar para a página de login se o usuário não tiver a permissão 5
        exit;
    }
}