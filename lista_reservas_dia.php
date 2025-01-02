<?php
include 'inc/query.php'; 

// Iniciar sessão
// Verificar se o adm está logado

$id = $_SESSION['id'];

$dia = $_GET['dia'];
$mes = $_GET['mes'];
$ano = $_GET['ano'];

$filial = $_SESSION['filial'];
// Consulta SQL para selecionar todos os usuários

selectID('users', $id, $conn);
$row2 = $resultado->fetch_assoc();
$user_permissao = $row2["permissao"];

select_por_dia($conn, $ano, $mes, $dia,$filial);

// Initialize an array to store reservations by hour
$reservations_by_hour = array_fill(0, 24, []);

while ($row = $resultado->fetch_assoc()) {
    $start_hour = (int)substr($row["data_inicio"], 11, 2);
    $end_hour = (int)substr($row["data_fim"], 11, 2);
    for ($hour = $start_hour; $hour <= $end_hour; $hour++) {
        $reservations_by_hour[$hour][] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reservas</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/listas.css">
</head>
<body>
    <div class="container" style="margin-top: 40px">
        <center>
            <h3>Lista de Reservas para o dia <?php echo $dia . '/' . $mes . '/' . $ano ?></h3>
        </center>
        <br>
        <br>
        <table class="table" id="table_id">
            <thead>
                <tr>
                    <th scope="col">Hora</th>
                    <th scope="col">Reservas</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $i = 7;
                while ($i < 21) {
                    if (!empty($reservations_by_hour[$i])) {
                        foreach ($reservations_by_hour[$i] as $reservation) {
                            $start_hour = (int)substr($reservation["data_inicio"], 11, 2);
                            $end_hour = (int)substr($reservation["data_fim"], 11, 2);
                            echo "<tr>";
                            echo "<td id='borda-bottom'>" . sprintf('%02d:00', $start_hour) . " - " . sprintf('%02d:00', $end_hour) . "</td>";
                            echo "<td id='borda'>";
                            echo "Na sala : " . $reservation["nome_sala"] . "<br>";
                            echo "Reservado por: " . $reservation["nome_usuario"] . "<br>";


                            
                            $periodo_reserva = $reservation["periodo_reserva"];
                            $mesI = substr($periodo_reserva, 5, 2); // Do 5º ao 14º caractere
                        $diaI = substr($periodo_reserva, 8, 2); // Do 7º ao 8º caractere
                        $horaI = substr($periodo_reserva, 11, 5); // Do 9º ao 13º caractere
                        
                        $mesF = substr($periodo_reserva, 27, 2); // Do 27º ao 28º caractere
                        $diaF = substr($periodo_reserva, 30, 2); // Do 29º ao 30º caractere
                        $horaF = substr($periodo_reserva, 33, 5); // Do 31º ao 35º caractere
                        
                            echo "De: " . $mesI . "/" . $diaI . " " . $horaI . " Até " . $mesF . "/" . $diaF . " " . $horaF . "<br>";

                            echo "Sendo membros: " . $reservation["membros"] . "<br>";
                            echo "" . $reservation["membros_temp"] . "<br>";
                          
                            if ($user_permissao == 5 || $reservation["user_id"] == $id) {
                                echo '<a href="lista_membros.php?reserva_id=' . $reservation["reserva_id"] . '" role="button" class="btn btn-primary btn-sm">+</a> ';
                                echo "<td>";
                                echo '<a href="edita_reserva.php?id=' . $reservation["reserva_id"].' role="button" class="btn btn-warning btn-sm"><i class="far fa-edit"></i>&nbsp; edita</a>';
                                echo '<a href="exclui_reserva.php?id=' . $reservation["reserva_id"] . '&sala_id=' . $reservation["sala_id"] . '" role="button" class="btn btn-danger btn-sm"><i class="far fa-trash-alt"></i>&nbsp; Excluir</a>';
                                echo "</td>";
                            }
                            echo "</td>";
                            echo "</tr>";
                            $i = $end_hour + 1;
                        }
                    } else {
                        echo "<tr>";
                        echo "<td id='borda-bottom'>" . sprintf('%02d:00', $i) . "</td>";
                        echo "<td id='borda'></td>";
                        echo "</tr>";
                        $i++;
                    }
                }
                ?>
            </tbody>
        </table>
        <div style="text-align: right; margin-top:20px;">
            <?php
            echo '<a href="cadastro_reserva.php?user_id='.$id.'&dia='.$dia.'&mes='.$mes.'&ano='.$ano.'&sala_id=1&nome_sala=Escolha a sala" role="button" class="btn btn-success btn-sm">Nova reserva</a>';
            echo '<a href="calendario.php" role="button" class="btn btn-primary btn-sm">Voltar</a>';
            ?>
        </div>
    </div>
    <script src="https://kit.fontawesome.com/cae6919cdb.js" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" crossorigin="anonymous"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.js"></script>
</body>
</html>