<?php


include 'inc/query.php';

// Verificar se o adm está logado
verificarPermissao($conn); // Chame a função para verificar a permissão


    // Recupera os dados dos usuarios do banco de dados

    if(isset($_GET['id'])) {
        $id = $_GET['id'];


// select(dashboads)
selectID($tabela="salas",$id,$conn);


    if ($resultado->num_rows > 0) {
        $row = $resultado->fetch_assoc();
        $nome_sala = $row['nome_sala'];      
        $descricao = $row['descricao'];

    } //else {
        //echo "Nenhum elemento encontrado com o ID fornecido.";
       // exit();
    //}

    }

    // Se o formulário de edição for submetido
    if ($_SERVER["REQUEST_METHOD"] == "POST") {


        
        // Recupera os valores dos campos do formulário
        $nome_sala = $_POST['nome_sala'];
        $descricao = $_POST['descricao'];



        if (!empty($nome_sala) && !empty($descricao)) {
        // Atualiza os dados do fornecedor no banco de dados
       update_s($nome_sala,$descricao,$id,$conn);

            // Redireciona de volta para a página  após a edição
            header("Location: lista_salas.php");
            exit();
        }else {
                echo "Erro ao editar dashboard: " . $conn->error;
            }
        } 



?>


<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar dash</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/login.css">
</head>
<body>
	
	<main>
		<div class="page">
			<form method="post" class="formLogin" id="formLogin">
				<h1>Editar Sala</h1>
				<p>Sobrescreva os dados para edição.</p>
				<label for="nome_sala">Nome sala</label>
				<input id="nome_sala" type="text" name="nome_sala" autofocus="true" value="<?php echo isset($nome_sala) ? $nome_sala : ''; ?>">
				
				<label for="descricao">Descrição</label>
				<input id="descricao" type="text" name="descricao" autofocus="true" value="<?php echo isset($descricao) ? $descricao : ''; ?>">
							
				
				<input id="button" type="submit" value="Salvar" class="btn" id="enviar">

          
				
			</form>
		</div>
	</main>
</body>
</html>
