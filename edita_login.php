<?php

include 'inc/query.php';
// Iniciar sessão
// Verificar se o adm está logado
verificarPermissao($conn); // Chame a função para verificar a permissão



    // Recupera os dados dos usuarios do banco de dados
    
    if(isset($_GET['id'])) {
        $id = $_GET['id'];
        selectID($tabela="users",$id,$conn);

    if ($resultado->num_rows > 0) {
        $row = $resultado->fetch_assoc();
        $conta = $row['conta'];      
        $email = $row['email'];
        $senha = $row['senha'];

    } else {
        echo "Nenhum usuario encontrado com o ID fornecido.";
        exit();
    }
    }
    // Se o formulário de edição for submetido
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Recupera os valores dos campos do formulário
        $conta = $_POST['conta'];
        $email = $_POST['email'];
        $senha = $_POST['senha'];
       


        // Atualiza os dados do fornecedor no banco de dados
        if (!empty($conta) && !empty($email) && !empty($senha) ) {
      update_U($conta,$email,$senha ,$id,$conn);

            // Redireciona de volta para a página  após a edição
            header("Location: lista_users.php");
            exit();
        } else {
            echo "Erro ao editar login: " . $conn->error;
        }
    }   
    

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Login</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/login.css">
</head>
<body>
	
	<main>
		<div class="page">
			<form method="post" class="formLogin" id="formLogin">
				<h1>Editar Login</h1>
				<p>Sobrescreva os dados para edição.</p>
				<label for="conta">Username</label>
				<input id="conta" type="text" name="conta" autofocus="true" value="<?php echo isset($conta) ? $conta : ''; ?>">
				
				<label for="email">Email</label>
				<input id="email" type="email" name="email" autofocus="true" value="<?php echo isset($email) ? $email : ''; ?>">
				
                <label for="senha">Senha</label>
				<input id="senha" type="text" name="senha" autofocus="true"value="<?php echo isset($senha) ? $senha : ''; ?>">
								
             
				
				<input id="button" type="submit" value="Salvar" class="btn" id="enviar">
              
				
			</form>
		</div>
	</main>
</body>
</html>