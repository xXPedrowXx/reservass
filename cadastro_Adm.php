<?php

include 'inc/query.php';
// Iniciar sessão
// Verificar se o adm está logado
verificarPermissao($conn); // Chame a função para verificar a permissão

$numero1 = rand(1, 100);
$numero2 = rand(1, 100);
$numero3 = rand(1, 100);
$numero =   ''.$numero1 .'' .$numero2. '' .$numero3.'';

// Verifica se os dados foram submetidos via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recupera os valores dos campos do formulário
    $conta = $_POST['conta'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];


    // Verifica se todos os campos foram preenchidos
    if (!empty($conta) && !empty($email) && !empty($senha)) {
        // Insere os dados do novo fornecedor no banco de dados
        insertU($conta, $email, $senha,5,$numero,  $conn);
        // Redireciona de volta para a página listar_fornecedores.php após adicionar o fornecedor
        header("Location: email_confirmacao.php?email=$email");
        exit();
    } else {
        echo "Erro ao adicionar usuario: " . $conn->error;
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/login.css">

</head>
<body>
    <main>
        <div class="page">
            <form method="post" class="formLogin" id="formLogin">
                <h1>Cadastro de usuario</h1>
                <label for="conta">Username</label>
                <input id="conta" type="text" name="conta" autofocus="true" placeholder="Digite seu username">
                
                <label for="email">Email</label>
                <input id="email" type="email" name="email" autofocus="true" placeholder="Digite seu Email">
                
                <label for="senha">Senha</label>
                <input id="senha" type="password" name="senha" autofocus="true" placeholder="Digite sua Senha">
                
                <input id="button" type="submit" value="Cadastrar" class="btn" id="enviar">
                
            </form>
        </div>

       
    </main>
</body>
</html>
