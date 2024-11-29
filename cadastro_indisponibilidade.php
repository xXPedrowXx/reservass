<?php

include 'inc/query.php';
// Iniciar sessão
// Verificar se o adm está logado
verificarPermissao($conn); // Chame a função para verificar a permissão

$user_id = $_GET['user_id'];
$sala_id = $_GET['sala_id'];

selectID($tabela="salas",$sala_id,$conn);
$row = $resultado->fetch_assoc();
$nome_sala = $row['nome_sala']; 

// Verifica se os dados foram submetidos via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recupera os valores dos campos do formulário
    $motivo = $_POST['Motivo'];
    $data_inicio = $_POST['data_inicio'];
    $data_fim = $_POST['data_fim'];

    // Verifica se todos os campos foram preenchidos
    if (!empty($motivo) && !empty($data_fim) && !empty($data_inicio)) {
        // Insere os dados do novo fornecedor no banco de dados
        if (insertI($user_id, $sala_id, $data_inicio, $data_fim, $motivo, $conn)) {
            // Redireciona de volta para a página listar_fornecedores.php após adicionar o fornecedor
            header("Location: lista_indisponivel.php");
            exit();
        } else {
            echo "Erro ao adicionar Indisponibilidade.";
        }
    } else {
        echo "Erro ao adicionar Indisponibilidade: " . $conn->error;
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
                <h1>Indisponibilidade Sala <?php echo $nome_sala ?></h1>

                <label for="Motivo">Motivo</label>
                <input id="Motivo" type="text" name="Motivo" autofocus="true" placeholder="Digite o motivo">
                
                <label for="data_inicio">Data inicio</label>
                <input id="data_inicio" type="datetime-local" name="data_inicio" autofocus="true" placeholder="Digite o Tempo ">
                
                <label for="data_fim">Data fim</label>
                <input id="data_fim" type="datetime-local" name="data_fim" autofocus="true" placeholder="Digite o Tempo ">
                
                <input id="button" type="submit" value="Cadastrar" class="btn" id="enviar">
            </form>
        </div>
        <script>
        document.getElementById('data_inicio').addEventListener('input', function (e) {
            validateYear(e.target);
        });

        document.getElementById('data_fim').addEventListener('input', function (e) {
            validateYear(e.target);
        });

        function validateYear(input) {
            const value = input.value;
            const year = value.split('T')[0].split('-')[0];
            if (year.length > 4) {
                input.value = value.slice(0, 4) + value.slice(5);
            }
        }
        </script>
    </main>
</body>
</html>