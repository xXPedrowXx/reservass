<?php
include 'inc/query.php';
// Iniciar sessão
// Verificar se o adm está logado
verificarPermissao($conn); // Chame a função para verificar a permissão

// Recupera os dados dos usuarios do banco de dados
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    selectID($tabela = "users", $id, $conn);

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
    $filiais = $_POST['filiais'];

    // Atualiza os dados do fornecedor no banco de dados
    if (!empty($conta) || !empty($email) || !empty($senha)  ){
        update_U($conta, $email, $senha, $id, $conn);

        // Remove as filiais antigas
        delete_UF('user_filiais', $id, $conn);

        // Insere as novas filiais
        foreach ($filiais as $filial_id) {
            insertUserFilial($id, $filial_id, $conn);  // Corrigido para usar $id em vez de $user_id
        }

        // Redireciona de volta para a página após a edição
        header("Location: lista_users.php");
        exit();
    } else {
        echo "Erro ao editar login: " . $conn->error;
    }
}

select_filiais($conn);
?>

<!DOCTYPE html>
<html lang="pt-br">
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
                <input id="senha" type="text" name="senha" autofocus="true" value="<?php echo isset($senha) ? $senha : ''; ?>">

                <label for="filiais">Filiais</label>
                <div class="dropdown">
                    <input readonly onclick="myFunction('salaDropdown')" placeholder="Select Filiais" class="dropbtn" required>
                    <div id="salaDropdown" class="dropdown-content">
                        <input type="text" placeholder="Filtre" id="salaInput" onkeyup="filterFunction('salaInput', 'salaDropdown')" onclick="event.stopPropagation()">
                        <?php
                        while ($sala = $resultado->fetch_assoc()) {
                            echo '<a href="#" onclick="selectSala(' . $sala['id'] . ', \'' . $sala['nome'] . '\')">' . $sala['nome'] . '</a>';
                        }
                        ?>
                    </div>
                </div>

                <input type="hidden" id="filiais" name="filiais[]" required>

                <input id="button" type="submit" value="Salvar" class="btn" id="enviar">
            </form>
        </div>

        <script>
var selectedFiliais = [];

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
    if (!selectedFiliais.includes(id)) {
        selectedFiliais.push(id);  // Adiciona a filial ao array
        var filialInput = document.getElementById("filiais");

        // Atualiza o valor do campo oculto com todas as filiais selecionadas
        filialInput.value = selectedFiliais.join(',');

        var dropbtn = document.querySelector(".dropbtn");

        // Exibe todas as filiais selecionadas
        dropbtn.innerText = selectedFiliais.map(function(filialId) {
            return name;  // Usa 'name' para mostrar o nome das filiais selecionadas
        }).join(', ');
    }
    document.getElementById("salaDropdown").classList.remove("show");
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

document.getElementById('formLogin').onsubmit = function() {
    if (selectedFiliais.length === 0) {
        alert('Por favor, selecione pelo menos uma filial.');
        return false;
    }
    return true;
}
        </script>
    </main>
</body>
</html>
