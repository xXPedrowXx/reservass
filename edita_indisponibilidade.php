<?php


include 'inc/query.php';

// Verificar se o adm está logado
verificarPermissao($conn); // Chame a função para verificar a permissão

select_sala($conn);
    // Recupera os dados dos usuarios do banco de dados

    if(isset($_GET['id'])) {
        $id = $_GET['id'];


// select(dashboads)
selectID($tabela="indisponivel",$id,$conn);


    if ($resultado->num_rows > 0) {
        $row = $resultado->fetch_assoc();
        $data_inicio = $row['data_inicio'];      
        $data_fim = $row['data_fim'];
        $motivo = $row['motivo'];

    } else {
        echo "Nenhum elemento encontrado com o ID fornecido.";
        exit();
    }

    }

    // Se o formulário de edição for submetido
    if ($_SERVER["REQUEST_METHOD"] == "POST") {


        
        // Recupera os valores dos campos do formulário
        $motivo = $_POST['motivo'];
        $data_inicio = $_POST['data_inicio'];
        $data_fim = $_POST['data_fim'];



        if (!empty($data_inicio) && !empty($data_fim) && !empty($motivo)) {
        // Atualiza os dados do fornecedor no banco de dados
        update_Ind($motivo,$data_inicio,$data_fim,$id,$conn);

            // Redireciona de volta para a página  após a edição
            header("Location: lista_indisponivel.php");
            exit();
        }else {
                echo "Erro ao editar Indisponibilidade: " . $conn->error;
            }
        } 
    

?>


<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar indisponivel</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/login.css">
</head>
<body>
	
	<main>
		<div class="page">
			<form method="post" class="formLogin" id="formLogin">
				<h1>Editar indisponivel</h1>
				<p>Sobrescreva os dados para edição.</p>

                
                
                <label for="motivo">Motivo</label>
                <input id="motivo" type="text" value="<?php echo isset($motivo) ? $motivo : ''; ?>" name="motivo" required>
        

                <label for="data_inicio">Data de início de reserva</label>
                <input id="data_inicio" type="datetime-local" value="<?php echo isset($data_inicio) ? $data_inicio : ''; ?>" name="data_inicio" required>
                
                <label for="data_fim">Data de fim de reserva</label>
                <input id="data_fim" type="datetime-local" value="<?php echo isset($data_fim) ? $data_fim : ''; ?>" name="data_fim" required>
							
				
				<input id="button" type="submit" value="Salvar" class="btn" id="enviar">
				
			</form>
		</div>
	</main>

   
</body>
</html>
