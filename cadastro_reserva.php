<?php
include 'inc/query.php'; 

error_reporting(E_ALL);
ini_set("display_errors", 1);

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$dia = $_GET['dia'];
$mes = $_GET['mes'];
$ano = $_GET['ano'];
$user_id = $_SESSION['id'];
$sala_id = isset($_GET['sala_id']) ? $_GET['sala_id'] : null;
$nome_sala = isset($_GET['nome_sala']) ? $_GET['nome_sala'] : '';

$resultado = selectdata($conn, $sala_id, $ano, $mes, $dia);

$reservas = [];
while ($row = $resultado->fetch_assoc()) {
    $reservas[] = $row;
}
select_sala($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Reservas</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/login.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <main>
        <div class="page">
            <form method="post" id="reservaForm" class="formLogin">
                <h1 id="top">Reservar sala</h1>

                <label for="sala_id">Salas</label>
                <div class="dropdown">
                    <input readonly onclick="myFunction('salaDropdown')" placeholder="<?php echo !empty($nome_sala) ? $nome_sala : 'Select Sala'; ?>" class="dropbtn">
                    <div id="salaDropdown" class="dropdown-content">
                        <input type="text" placeholder="Filtre" id="salaInput" onkeyup="filterFunction('salaInput', 'salaDropdown')" onclick="event.stopPropagation()">
                        <?php
                        while ($sala = $resultado->fetch_assoc()) {
                            echo '<a href="#" onclick="selectSala(' . $sala['id'] . ', \'' . $sala['nome_sala'] . '\')">' . $sala['nome_sala'] . '</a>';
                        }
                        ?>
                    </div>
                </div>

                <input type="hidden" id="sala_id" name="sala_id" value="<?php echo $sala_id; ?>" required>

                <label for="data_inicio">Hora de início de reserva</label>
                <select name="data_inicio" id="data_inicio">
                    <?php
                    function isAvailable($time, $reservas) {
                        foreach ($reservas as $reserva) {
                            $reserva_inicio = date("H:i", strtotime($reserva['data_inicio']));
                            $reserva_fim = date("H:i", strtotime($reserva['data_fim']));
                            if ($time >= $reserva_inicio && $time < $reserva_fim) {
                                return false;
                            }
                        }
                        return true;
                    }

                    $times = [];
                    $todayArray = getdate();
                    $todayD = $todayArray['mday'];
                    $todayH = $todayArray['hours'] -  5; // 4 de fuso e 1 de tolerancias
                    if ($todayD != $dia) {
                        $todayH = 7;
                    }
                    for ($i = $todayH; $i < 21; $i++) {
                        $time = sprintf('%02d:00', $i);
                        if (isAvailable($time, $reservas)) {
                            $times[] = $time;
                        }
                    }

                    foreach ($times as $time) {
                        echo "<option value=\"$time\">$time</option>";
                    }
                    ?>
                </select>

                <label for="duracao">Duração da reserva:</label>
                <select name="duracao" id="duracao">
                    <?php
                    for ($i = 1; $i <= 8; $i++) {
                        echo '<option value="0'.$i.':00:00">'.$i.' hrs</option>';
                    }
                    ?>
                </select>

                <label for="url">Url da reunião online</label>
                <input id="url" type="text" placeholder="insira aqui o URL da reuniao on-line" name="url">

                <label for="grupo_id">Membros</label>
                <div class="dropdown">
                <input onclick="myFunction('grupoDropdown')"  readonly placeholder="<?php echo !empty($name) ? $user_email : 'Escolha o(s) membro(s)'; ?>"  class="dropbtn"></input>
                    <div id="grupoDropdown" class="dropdown-content">
                        <input type="text" placeholder="aplique o filtro" id="grupoInput" onkeyup="filterFunction('grupoInput', 'grupoDropdown')" onclick="event.stopPropagation()">
                        <?php
                  select_cadastro_membro($user_id,$conn);
                  while ($sala = $resultado->fetch_assoc()) {
                            echo '<a onclick="selectGroup(' . $sala['user_id'] . ', \'' . $sala['user_conta'] . '\')">' . $sala['user_conta'] . '</a>';
                        }
                        ?>
                    </div>
                </div>
                
               
                <input type="hidden" id="user_id" name="user_id" >

            <p id="membrosList"></p>

                <button type="submit" class="btn btn-primary">Reservar</button>

                
            </form>
        </div>
       
    </main>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script >
        
        function myFunction(dropdownId) {
            document.getElementById(dropdownId).classList.toggle("show");
        }

        function updateSala(salaId, nomeSala) {
            window.location.href = `cadastro_reserva.php?sala_id=${salaId}&dia=<?php echo $dia; ?>&mes=<?php echo $mes; ?>&ano=<?php echo $ano; ?>&nome_sala=${nomeSala}`;
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
            document.getElementById("sala_id").value = id;
            document.querySelector(".dropbtn").innerText = name;
            document.getElementById("salaDropdown").classList.remove("show");
            updateSala(id, name);
        }

        let membros = [];
let membrosName = [];

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
        document.getElementById("reservaForm").addEventListener("submit", function (event) {
   event.preventDefault(); // Evita envio padrão do formulário

    createReservation();
});
function createReservation() {
    const dataInicioInput = document.getElementById("data_inicio").value;
    const duracaoInput = document.getElementById("duracao").value;
    const salaIdInput = document.getElementById("sala_id").value;
    const urlInput = document.getElementById("url").value;

    if (!dataInicioInput || !duracaoInput || !salaIdInput) {
        console.error("Erro: algum campo obrigatório está vazio!");
        return;
    }

    // Constrói strings de data e horários usando PHP e inputs
    const data_inicio_str =
        "<?php echo "$ano-$mes-$dia"; ?> " + dataInicioInput + ":00";
    const duracao_str = duracaoInput;

    // Usando moment.js para calcular `data_inicio` e `data_fim`
    const data_inicio = moment(data_inicio_str, "YYYY-MM-DD HH:mm:ss");
    const duracao = moment.duration(duracao_str);
    const data_fim = data_inicio.clone().add(duracao);
    const salaId = document.getElementById("sala_id").value;

    $.ajax({
        type: "POST",
        url: "create_reserva.php",
        data: {
            data_inicio: data_inicio.format("YYYY-MM-DD HH:mm:ss"),
            data_fim: data_fim.format("YYYY-MM-DD HH:mm:ss"),
            salaId: salaId,
            url: urlInput
        },
        success: function(response) {
            console.log("Resposta do servidor:", response);
            if (response.success) {
                const reserva_id = response.reserva_id;
                addMembers(reserva_id);
            } else {
                console.error("Erro ao criar a reserva:", response.message);
            }
        },
        error: function(xhr, status, error) {
            console.error("Erro na requisição:", xhr.responseText); // Mostra o erro retornado
        }
    });
}

function addMembers(reserva_id) {
    console.log("Membros recebidos:", membros); // Verifica se está correto
    

    $.ajax({
        type: "POST",
        url: "post_membro.php",
        data: {
            values: membros,
            reserva_id: reserva_id
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