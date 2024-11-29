<?php
include 'inc/query.php'; 

error_reporting(E_ALL);
ini_set("display_errors", 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conta = $_POST['conta'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    // Validação do email
    if (!preg_match('/@apklog\.com\.br$|@apk\.com\.br$/', $email)) {
        echo "Email inválido. O email deve terminar com @apklog.com.br ou @apk.com.br.";
        echo "<br>";
        echo" Favor atualizar a pagina e inserir um email valido ";
       
        exit();
    }

    $numero1 = rand(1, 100);
    $numero2 = rand(1, 100);
    $numero3 = rand(1, 100);
    $numero = ''.$numero1 .'' .$numero2 .''. $numero3;

    insertU($conta, $email, $senha, '1' ,$numero ,$conn);

    header('Location: email_confirmacao.php');
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