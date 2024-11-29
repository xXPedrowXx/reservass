<?php


include 'inc/query.php';
// Iniciar sessão
// Verificar se o adm está logado





// Verifica se o ID da categoria foi passado via GET
if(isset($_GET['id'])) {
    $id = $_GET['id'];
    // Chama a função para excluir a categoria
    deletee('indisponivel',$id,$conn,);

    header ("Location: lista_indisponivel.php");
} else {
    echo "ID da categoria não foi fornecido.";
}
