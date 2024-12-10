<?php
include 'inc/query.php'; 

error_reporting(E_ALL);
ini_set("display_errors", 1);



if (!isset($_SESSION['access_token'])) {
    header("Location: google-login.php");
    exit();
}

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
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.1.9/jquery.datetimepicker.min.js"></script>

</head>
<body>
    <main>
        <div id="form-container">
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
                    
                    // Ajusta o fuso horário e adiciona 1 hora de tolerância
                    $timezone = new DateTimeZone('America/Sao_Paulo'); // Substitua pelo fuso horário correto
                    $now = new DateTime('now', $timezone);
                    $now->modify('-4 hours'); // Ajusta o fuso horário
                    $now->modify('+1 hour'); // Adiciona 1 hora de tolerância
                    $todayH = $now->format('G'); // Formata a hora sem zero à esquerda
                    
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
                    <input onclick="myFunction('grupoDropdown')" readonly placeholder="Escolha o(s) membro(s)" class="dropbtn">
                    <div id="grupoDropdown" class="dropdown-content">
                        <input type="text" placeholder="aplique o filtro" id="grupoInput" onkeyup="filterFunction('grupoInput', 'grupoDropdown')" onclick="event.stopPropagation()">
                        <?php
                        select_cadastro_membro($user_id, $conn);
                        while ($sala = $resultado->fetch_assoc()) {
                            echo '<a onclick="selectGroup(' . $sala['user_id'] . ', \'' . $sala['user_conta'] . '\')">' . $sala['user_conta'] . '</a>';
                        }
                        ?>
                    </div>
                </div>

                <input type="hidden" id="user_id" name="user_id">
                <p id="membrosList"></p>

                <button type="submit" class="btn btn-primary">Reservar</button>
            </form>
        </div>
    </main>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script>
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
            event.preventDefault();
   // createCalendar()
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

            const data_inicio_str = "<?php echo "$ano-$mes-$dia"; ?> " + dataInicioInput + ":00";
            const duracao_str = duracaoInput;

            const data_inicio = moment(data_inicio_str, "YYYY-MM-DD HH:mm:ss");
            const duracao = moment.duration(duracao_str);
            const data_fim = data_inicio.clone().add(duracao);

            $.ajax({
                type: "POST",
                url: "create_reserva.php",
                data: {
                    data_inicio: data_inicio.format("YYYY-MM-DD HH:mm:ss"),
                    data_fim: data_fim.format("YYYY-MM-DD HH:mm:ss"),
                    salaId: salaIdInput,
                    url: urlInput
                },
                success: function(response) {
                    if (response.success) {
                        const reserva_id = response.reserva_id;
                        addMembers(reserva_id);
                        createCalendar(reserva_id)
                       
                    } else {
                        console.error("Erro ao criar a reserva:", response.message);
                    }
                    
                },
                error: function(xhr, status, error) {
                    console.error("Erro na requisição:", xhr.responseText);
                }
            });
        }

        function addMembers(reserva_id) {
            $.ajax({
                type: "POST",
                url: "post_membro.php",
                data: {
                    values: membros,
                    reserva_id: reserva_id
                },
                success: function(response) {
                    if (response.success) {
                        window.location.href = 'calendario.php';
                    } else {
                        console.error("Erro ao adicionar membros:", response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Erro na requisição:", xhr.responseText);
                }
            });
        }


        function createCalendar(reserva_id) {
    const dataInicioInput = document.getElementById("data_inicio").value;
    const duracaoInput = document.getElementById("duracao").value;
    const salaIdInput = document.getElementById("sala_id").value;
 const membrosList = document.getElementById("membrosList").innerText;

    if (!dataInicioInput || !duracaoInput || !salaIdInput) {
        console.error("Erro: algum campo obrigatório está vazio!");
        return;
    }
    const nome_salas = "<?php echo $nome_sala; ?>";

    const data_inicio_str = "<?php echo "$ano-$mes-$dia"; ?>T" + dataInicioInput + ":00";
    const data_inicio = moment(data_inicio_str, 'YYYY-MM-DDTHH:mm:ss');
const duracao = moment.duration(duracaoInput); // Ensure duracaoInput is in a valid format like 'PT1H' for 1 hour
const data_fim = data_inicio.clone().add(duracao);
 const guests = membrosList.split(',').map(email => email.trim());

  
 parameters = {     
    title: nome_salas, 
    event_time: {
        start_time: moment(data_inicio, 'YYYY-MM-DDTHH:mm:ss').toISOString(),
        end_time: moment(data_fim, 'YYYY-MM-DDTHH:mm:ss').toISOString()
    },
    all_day: 0,
    operation: 'create',
 guests: guests ,
 reserva_id: reserva_id
 
};

$.ajax({
    type: 'POST',
    url: 'teste_api_calendar/calendar-API-tutorial-main/ajax.php',
    data: { event_details: parameters },
    dataType: 'json',
    success: function(response) {
        $("#create-event").removeAttr('disabled');
        alert('Evento criado com ID: ' + response.event_id);
      
    },
    error: function(response) {
        $("#create-event").removeAttr('disabled');
        alert(response.responseJSON ? response.responseJSON.message : 'Erro desconhecido');
        console.log(data_inicio);
        console.log(data_fim);
       
    }
});


};

        
    </script>

</body>
</html>