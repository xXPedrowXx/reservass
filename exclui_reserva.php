<?php

include 'email_cancelamento.php';

if(isset($_GET['id'])) {
    $id = $_GET['id'];
    $sala_id = $_GET['sala_id'];

    // Seleciona a reserva pelo ID

    if (cancelEmail($id, $conn)) {
        // Chama a função para excluir a reserva
        delete_reserva($id, $conn);
        echo "Reserva excluída com sucesso.";

        header("Location: calendario.php?sala_id=$sala_id");
    } else {
       echo "Erro ao enviar o email. A reserva não foi excluída.";
    }

     header("Location: calendario.php?");
} else {
    echo "ID da categoria não foi fornecido.";
}