<?php


include 'inc/query.php';
// Iniciar sessão
// Verificar se o adm está logado





// Verifica se o ID da categoria foi passado via GET
if(isset($_GET['id'])) {
    $id = $_GET['id'];
    $meu_id = $_SESSION['id'];

    if($id == $meu_id){
        echo "Você não pode excluir a si mesmo";
        exit();
    }
    // Chama a função para excluir a categoria
    deletee('users',    $id,$conn,);
    header ("Location: lista_users.php");
} else {
    echo "ID da categoria não foi fornecido.";
}
