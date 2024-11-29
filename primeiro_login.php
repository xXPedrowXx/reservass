<?php
include 'inc/query.php'; 


$err_email = $err_password = $err_missing = $err_confiração = "";

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $codigo_verificação = $_POST['codigo_verificação'];

    // Verificação dos campos obrigatórios
    if (empty($email) || empty($senha) || empty($codigo_verificação)) {
        $err_missing = "Todos os campos devem ser preenchidos";
    } else {
        // Utilizando consultas preparadas para evitar SQL Injection
        $resultado = selectlogin($email, $conn);

        if ($resultado && $resultado->num_rows > 0) {
            $user_data = $resultado->fetch_assoc();

            // Verificando a senha
            if ($user_data['senha'] === $senha && $user_data['codigo_verificação'] === $codigo_verificação) {
                $_SESSION['id'] = $user_data['id'];
                $_SESSION['email'] = $user_data['email']; 
                $_SESSION['permissao'] = $user_data['permissao']; 
               
                confirmação($_SESSION['id'],$conn);

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
            <form method="post" id="loginForm" class="formLogin">
                <h1 id="top">Confirme seu Email</h1>
                <p>Digite os seus dados de acesso nos campos abaixo.</p>
                <label for="email">Email</label>
                <input id="text" type="text" name="email" autofocus="true">
                <?php if (!empty($err_email)) { echo "<p id='error'>" . $err_email . "</p>"; } ?>

                <label for="senha">Senha</label>
                <input id="text" type="password" name="senha" autofocus="true">
                <?php if (!empty($err_password)) { echo "<p id='error'>" . $err_password . "</p>"; } ?>

                <label for="codigo_verificação">Codigo de confirmação</label>
                <input id="codigo_verificação" type="number" name="codigo_verificação" autofocus="true">
                <?php if (!empty($err_confiração)) { echo "<p id='error'>" . $err_confiração . "</p>"; } ?>

                <input id="button" type="submit" value="Logar" class="btn">

            
                

                <?php if (!empty($err_missing)) { echo "<p id='error'>" . $err_missing . "</p>"; }
                  ?>
            </form>
        </div>
    </main>
</body>
</html>