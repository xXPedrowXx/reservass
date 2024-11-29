<?php


include 'inc/query.php';
// Iniciar sessão
// Verificar se o adm está logado


// Verifica se o ID da categoria foi passado via GET
if(isset($_GET['id'])) {
    $id = $_GET['id'];
    $reserva_id = $_GET['reserva_id'];
    $meu_id = $_SESSION['id'];
    $user_id = $_GET['user_id'];

    if($user_id == $meu_id){
        echo "Você não pode excluir a si mesmo";
        exit();
    }

    // Chama a função para excluir a categoria
    deletee('membros', $id, $conn);
    header ("Location: lista_membros.php?reserva_id=$reserva_id");
} else {
    echo "ID da categoria não foi fornecido.";
}

