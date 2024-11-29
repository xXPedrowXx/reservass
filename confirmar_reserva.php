<?php


include 'inc/query.php';
// Iniciar sessão
// Verificar se o adm está logado





// Verifica se o ID da categoria foi passado via GET
 
    $reserva_id = $_GET['reserva_id'];
    // Chama a função para excluir a categoria

    insertConfirmacao($reserva_id,$conn,);

        header("Location: lista_membros.php?reserva_id=$reserva_id");
    
