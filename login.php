<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/login.css">
</head>
<body>
    <main>
        <div class="page">
            <div class="header">
                <h2 id="left">APK</h2> 
                <h3>Reserva de recursos</h3>
            </div>
            <form method="post" id="loginForm" class="formLogin">
                <h1 id="top">Login</h1>
                <p>Digite os seus dados de acesso nos campos abaixo.</p>
                <label for="email">Email</label>
                <input id="text" type="text" name="email" autofocus="true">
                <?php if (!empty($err_email)) { echo "<p id='error'>" . $err_email . "</p>"; } ?>

                <label for="senha">Senha</label>
                <input id="text" type="password" name="senha" autofocus="true">
                <?php if (!empty($err_password)) { echo "<p id='error'>" . $err_password . "</p>"; } ?>

                <input id="button" type="submit" value="Logar" class="btn">

                <a href="cadastro.php">Clique aqui para se cadastrar</a>
                <a href="primeiro_login.php" id="primeiro_login">Primeira vez logando ?</a>
                <?php if (!empty($err_missing)) { echo "<p id='error'>" . $err_missing . "</p>"; } ?>
            </form>
        </div>
    </main>
</body>
</html>

<?php
include 'inc/query.php'; 

$err_email = $err_password = $err_missing = "";

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    // Verificação dos campos obrigatórios
    if (empty($email) || empty($senha)) {
        $err_missing = "Todos os campos devem ser preenchidos";
    } else {
        // Utilizando consultas preparadas para evitar SQL Injection
        $resultado = selectlogin($email, $conn);

        if ($resultado && $resultado->num_rows > 0) {
            $user_data = $resultado->fetch_assoc();

            // Verificando a senha
            if ($user_data['senha'] === $senha) {
                if ($user_data['verificado'] == 0) {
                    header("Location: primeiro_login.php");
                    exit;
                }
                $_SESSION['id'] = $user_data['id'];
                $_SESSION['email'] = $user_data['email']; 
                $_SESSION['permissao'] = $user_data['permissao']; 

                if ($user_data['permissao'] == 5) {
                    header("Location: Adm.php");
                    exit;
                } else {
                    header("Location: index.php"); 
                    exit;
                }
            } else {
                $err_password = "Senha incorreta";
            }
        } else {
            $err_email = "Usuário não encontrado";
        }
    }
}
?>