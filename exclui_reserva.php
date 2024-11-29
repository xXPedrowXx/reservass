<?php


include 'inc/query.php';
// Iniciar sessão
// Verificar se o adm está logado





// Verifica se o ID da categoria foi passado via GET
if(isset($_GET['id'])) {
    $id = $_GET['id'];
    $sala_id = $_GET['sala_id'];
    // Chama a função para excluir a categoria

    delete_reserva($id,$conn,);

        header("Location: calendario.php?");
    


} else {
    echo "ID da categoria não foi fornecido.";
}
