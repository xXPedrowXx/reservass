<?php

include 'inc/query.php';

// Iniciar sessão
// Verificar se o adm está logado
verificarPermissao($conn); // Chame a função para verificar a permissão

// Verifica se os dados foram submetidos via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recupera os valores dos campos do formulário
    $nome_sala = $_POST['nome_sala'];
    $descricao = $_POST['descricao'];


    // Verifica se todos os campos foram preenchidos
    if (!empty($nome_sala) && !empty($descricao) ) {
        // Insere os dados do novo dash no banco de dados
        insertS($nome_sala, $descricao,  $conn);
            // Redireciona de volta para a página listas_dash após adicionar o dash
            header("Location: lista_salas.php");
            exit();
     } else {
            echo "Erro ao adicionar Sala: " . $conn->error;
        }
    } 
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro Dashboards</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/login.css">
</head>
<body>
 
    <main>
        <div class="page">
            <form method="post" id="loginForm" class="formLogin">
                <h1 id="top">Cadastro salas</h1>
                <p>Digite os dados de cadastro nos campos abaixo.</p>
                <label for="nome_sala">Nome da sala</label>
                <input id="nome_sala" type="text" name="nome_sala" autofocus="true">
                
                <label for="descricao">Descrição</label>
                <input id="descricao" type="text" name="descricao" autofocus="true" required>

                <input id="button" type="submit" value="Cadastrar" class="btn">


                
            </form>
        </div>
    </main>
</body>
</html>