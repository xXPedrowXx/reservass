<?php
include 'inc/query.php'; 

error_reporting(E_ALL);
ini_set("display_errors", 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conta = $_POST['conta'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $filiais = $_POST['filiais']; // Recebe os IDs das filiais como um array

    // Verifica se algum campo está vazio
    if (empty($conta) || empty($email) || empty($senha) || empty($filiais) || !is_array($filiais) || count($filiais) === 0) {
        echo "Todos os campos são obrigatórios.";
        exit();
    }

    /* Validação do email
    if (!preg_match('/@apklog\.com\.br$|@apk\.com\.br$/', $email)) {
        echo "Email inválido. O email deve terminar com @apklog.com.br ou @apk.com.br.";
        echo "<br>";
        echo "Favor atualizar a pagina e inserir um email valido";
        exit();
    }
*/
    $numero1 = rand(1, 100);
    $numero2 = rand(1, 100);
    $numero3 = rand(1, 100);
    $numero = $numero1 . $numero2 . $numero3;

    // Insere o usuário e obtém o ID do usuário recém-inserido
    $user_id = insertU($conta, $email, $senha, '1', $numero, $conn);

    // Associa o usuário às filiais selecionadas
    if ($user_id) {
        foreach ($filiais as $filial_id) {
            insertUserFilial($user_id, $filial_id, $conn);
        }
    }

    header('Location: email_confirmacao.php?email=' . urlencode($email));
    exit();
}

select_filiais($conn);
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
                <input id="conta" type="text" name="conta" autofocus="true" placeholder="Digite seu username" required>
                
                <label for="email">Email</label>
                <input id="email" type="email" name="email" placeholder="Digite seu Email" required>
                
                <label for="senha">Senha</label>
                <input id="senha" type="password" name="senha" placeholder="Digite sua Senha" required>

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
                
                <input id="button" type="submit" value="Cadastrar" class="btn">
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
            selectedFiliais.push(id);
            var filialInput = document.getElementById("filiais");
            filialInput.value = selectedFiliais.join(',');
            var dropbtn = document.querySelector(".dropbtn");
            dropbtn.innerText = selectedFiliais.map(f => name).join(', ');
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