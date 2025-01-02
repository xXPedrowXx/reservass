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
    $filial = $_POST['filial'];


    // Verifica se todos os campos foram preenchidos
    if (!empty($nome_sala) && !empty($descricao) ) {
        // Insere os dados do novo dash no banco de dados
        insertS($nome_sala, $descricao,$filial,  $conn);
            // Redireciona de volta para a página listas_dash após adicionar o dash
            header("Location: lista_salas.php");
            exit();
     } else {
            echo "Erro ao adicionar Sala: " . $conn->error;
        }
    } 

    select_filiais($conn);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro Salas</title>
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


                <label for="filial">Filial</label>
                <div class="dropdown">
                    <input readonly onclick="myFunction('salaDropdown')" placeholder="<?php echo !empty($nome) ? $nome : 'Select Filial'; ?>" class="dropbtn">
                    <div id="salaDropdown" class="dropdown-content">
                        <input type="text" placeholder="Filtre" id="salaInput" onkeyup="filterFunction('salaInput', 'salaDropdown')" onclick="event.stopPropagation()">
                        <?php
                        while ($sala = $resultado->fetch_assoc()) {
                            echo '<a href="#" onclick="selectSala(' . $sala['id'] . ', \'' . $sala['nome'] . '\')">' . $sala['nome'] . '</a>';
                        }
                        ?>
                    </div>
                </div>

                <input type="hidden" id="filial" name="filial" value="<?php echo $filial; ?>" required>

                <input id="button" type="submit" value="Cadastrar" class="btn">


                
            </form>
        </div>


        <script>
    function myFunction(dropdownId) {
        document.getElementById(dropdownId).classList.toggle("show");
    }

    function filterFunction(inputId, dropdownId) {
        var input, filter, div, a, i;
        input = document.getElementById(inputId);
        filter = input.value.toUpperCase();
        div = document.getElementById(dropdownId);
        a = div.getElementsByTagName("a");
        for (i = 0; i < a.length; i++) {
            txtValue = a[i].textContent || a[i].innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                a[i].style.display = "";
            } else {
                a[i].style.display = "none";
            }
        }
    }

    function selectSala(id, name) {
        document.getElementById("filial").value = id;
        document.querySelector(".dropbtn").innerText = name;
        document.getElementById("salaDropdown").classList.remove("show");
        updateSala(id, name);
    }

    function updateSala(id, name) {
        // Add your logic here if needed
        console.log("Sala updated: ", id, name);
    }

    window.onclick = function(event) {
        if (!event.target.matches('.dropbtn') && !event.target.matches('#grupoInput')) {
            var dropdowns = document.getElementsByClassName("dropdown-content");
            for (var i = 0; i < dropdowns.length; i++) {
                var openDropdown = dropdowns[i];
                if (openDropdown.classList.contains('show')) {
                    openDropdown.classList.remove('show');
                }
            }
        }
    }
</script>
    </main>
</body>
</html>