<?php


include 'inc/query.php';

// Verificar se o adm está logado
verificarPermissao($conn); // Chame a função para verificar a permissão


    // Recupera os dados dos usuarios do banco de dados

    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        selectID("filiais", $id, $conn);
    
        if ($resultado && $resultado->num_rows > 0) {
            $row = $resultado->fetch_assoc();
            $nome = $row['nome'];
            $endereco = $row['endereco'];
        } else {
            echo "Nenhum elemento encontrado com o ID fornecido.";
            exit();
        }
    }
    
    // Se o formulário de edição for submetido
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nome = $_POST['nome'];
        $endereco = $_POST['endereco'];
    
        if (!empty($nome) && !empty($endereco)) {
            // Atualiza os dados do fornecedor no banco de dados
            update_F($nome, $endereco, $id, $conn);
    
            // Redireciona de volta para a página após a edição
            header("Location: lista_filiais.php");
            exit();
        } else {
            echo "Erro ao editar filial: " . $conn->error;
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
				<h1>Editar Filial</h1>
				<p>Sobrescreva os dados para edição.</p>
				<label for="nome">Nome filial</label>
				<input id="nome" type="text" name="nome" autofocus="true" value="<?php echo isset($nome) ? $nome : ''; ?>">
				
				<label for="endereco">Endereço</label>
				<input id="endereco" type="text" name="endereco" autofocus="true" value="<?php echo isset($endereco) ? $endereco : ''; ?>">
							
				
				<input id="button" type="submit" value="Salvar" class="btn" id="enviar">

          
				
			</form>
		</div>
	</main>
</body>
</html>
