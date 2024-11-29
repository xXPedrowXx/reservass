<?php
include 'inc/query.php'; 

error_reporting(E_ALL);
ini_set("display_errors", 1);

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$reserva_id = $_GET['reserva_id'] ?? null;
$user_id = $_SESSION['id'];

if (!$reserva_id) {
    echo "Reserva não especificada.";
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Adicionar Membros à Reserva</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/login.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <main>
        <div class="page">
            <form method="post" id="membroForm" class="formLogin">
                <h1 id="top">Add Membros à Reserva</h1>

                <label for="grupo_id">Membros Normais</label>
                <div class="dropdown">
                    <input onclick="myFunction('grupoDropdown')" readonly placeholder="Escolha o(s) membro(s)" class="dropbtn" id="emailInputNormal"></input>
                    <div id="grupoDropdown" class="dropdown-content">
                        <input type="text" placeholder="Aplique o filtro" id="grupoInput" onkeyup="filterFunction('grupoInput', 'grupoDropdown')" onclick="event.stopPropagation()">
                        <?php
                        select_cadastro_membro2($user_id, $reserva_id, $conn);
                        while ($sala = $resultado->fetch_assoc()) {
                            echo '<a onclick="selectGroup(' . $sala['user_id'] . ', \'' . $sala['user_conta'] . '\')">' . $sala['user_conta'] . '</a>';
                        }
                        ?>
                    </div>
                </div>

                <label for="grupo_id">Membros Externos</label>
                <input type="text" placeholder="Digite o email do membro externo" id="emailInputExterno" class="form-control">

                <input type="hidden" id="user_id" name="user_id" required>
                <p id="membrosList"></p>

                <button type="submit" class="btn btn-primary">Adicionar Membros</button>
            </form>
        </div>
    </main>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
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

        let membros = [];
        let membrosName = [];
        let membrosExternos = [];

        function selectGroup(id, name) {
            if (!membros.includes(id) && !membrosName.includes(name)) {
                document.getElementById("user_id").value = id;
                document.querySelector(".dropbtn").innerText = name;
                document.getElementById("grupoDropdown").classList.remove("show");
                membros.push(id);
                membrosName.push(name);
                document.getElementById("membrosList").innerText = membrosName.join(' , ');
                console.log(membrosName);
            }
        }

        function addEmail() {
            let emailInputNormal = document.getElementById("emailInputNormal").value;
            let emailInputExterno = document.getElementById("emailInputExterno").value;

            if (emailInputNormal && !membrosName.includes(emailInputNormal)) {
                membrosName.push(emailInputNormal);
                document.getElementById("membrosList").innerText = membrosName.join(' , ');
                console.log(membrosName);
            }

            if (emailInputExterno && !membrosExternos.includes(emailInputExterno)) {
                membrosExternos.push(emailInputExterno);
                document.getElementById("membrosList").innerText += ' , ' + emailInputExterno;
                console.log(membrosExternos);
            }
        }

        window.onclick = function(event) {
            if (!event.target.matches('.dropbtn') && !event.target.matches('#myInput')) {
                var dropdowns = document.getElementsByClassName("dropdown-content");
                for (var i = 0; i < dropdowns.length; i++) {
                    var openDropdown = dropdowns[i];
                    if (openDropdown.classList.contains('show')) {
                        openDropdown.classList.remove('show');
                    }
                }
            }
        }

        document.getElementById("membroForm").addEventListener("submit", function (event) {
            event.preventDefault(); // Evita envio padrão do formulário
            addEmail();
            addMembers();
        });

        function addMembers() {
            console.log("Membros recebidos:", membros); // Verifica se está correto
            console.log("Membros externos recebidos:", membrosExternos); // Verifica se está correto
            if ((!membros || membros.length === 0) && (!membrosExternos || membrosExternos.length === 0)) {
                console.error("Erro: membros está vazio ou indefinido!");
                return;
            }

            $.ajax({
                type: "POST",
                url: "post_membro.php",
                data: {
                    values: membros,
                    externos: membrosExternos,
                    reserva_id: <?php echo $reserva_id; ?>
                },
                success: function(response) {
                    console.log("Resposta do servidor:", response);
                    if (response.success) {
                        window.location.href = 'calendario.php';
                    } else {
                        console.error("Erro ao adicionar membros:", response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Erro na requisição:", xhr.responseText); // Mostra o erro retornado
                }
            });
        }
    </script>
</body>
</html>