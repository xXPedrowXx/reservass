<?php
include 'inc/query.php'; 

// Iniciar sessão


$id = $_SESSION['id'];
$sala_id = $_GET['sala_id'];

// Consulta SQL para selecionar todos os usuários
selectID('users', $id, $conn);
$row2 = $resultado->fetch_assoc();
$user_permissao = $row2["permissao"];

select_por_sala($sala_id, $conn);
$row3 = $resultado->fetch_assoc();

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
            <?php
        if ($resultado->num_rows > 0) {
           echo" <h3>Lista de reservas ativas para a sala  ".$row3["nome_sala"] ."</h3>";}
            else{
               echo " <h3>Nenhuma reserva marcada para a sala escolhida</h3>";
            }
            ?>
        </center>
        <br>
        <br>
        <table class="table" id="table_id">
            <thead>
                <tr>
                    <th scope="col">Nome</th>
                    <th scope="col">Hora reserva</th>
                    <th scope="col">Sala</th>
                    <th scope="col">Membros</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Verifica se a consulta retornou algum resultado
        select_por_sala($sala_id, $conn);
                if ($resultado2->num_rows > 0) {
                    // Loop através de todos os resultados da consulta
                    while ($row3 = $resultado2->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td id='borda'>" . $row3["nome_usuario"] . "</td>";
                        $periodo_reserva = $row3["periodo_reserva"];
                        $mesI = substr($periodo_reserva, 5, 2); // Do 5º ao 14º caractere
                        $diaI = substr($periodo_reserva, 8, 2); // Do 7º ao 8º caractere
                        $horaI = substr($periodo_reserva, 11, 5); // Do 9º ao 13º caractere
                        
                        $mesF = substr($periodo_reserva, 27, 2); // Do 27º ao 28º caractere
                        $diaF = substr($periodo_reserva, 30, 2); // Do 29º ao 30º caractere
                        $horaF = substr($periodo_reserva, 33, 5); // Do 31º ao 35º caractere
                        
                        echo "<td id='borda'> De: " . $mesI . "/" . $diaI . " " . $horaI . " Até " . $mesF . "/" . $diaF . " " . $horaF . "</td>";

                        echo "<td id='borda'>" . $row3["nome_sala"] . "</td>";

                        echo '<td id="borda"> '
                        . $row3["membros"] . ''
                        . $row3["membros_temp"] .'</td>';
                    
    '                        <a href="lista_membros.php?reserva_id=' . $row3["reserva_id"] . '" role="button" class="btn btn-warning btn-sm">Membros</a>';

                        if ($user_permissao == 5 || $row3["user_id"] == $id) {
                            echo "<td>";
                            $perm = ($user_permissao == 5) ? 5 : 1;
                            echo '<a href="edita_reserva.php?id=' . $row3["reserva_id"] . '" role="button" class="btn btn-warning btn-sm"><i class="far fa-edit"></i>&nbsp; Editar</a>';

                            echo ' ';
                            echo '<a href="exclui_reserva.php?id=' . $row3["reserva_id"] . '&sala_id='.$row3["sala_id"].'" role="button" class="btn btn-danger btn-sm"><i class="far fa-trash-alt"></i>&nbsp; Excluir</a>';
                            echo "</td>";
                        } else {
                            echo "</td>";
                        }
                    }
                } else {
                    // Se não houver usuários, exibe uma mensagem na tabela
                    echo '<tr><td colspan="5">Nenhuma reserva encontrada</td></tr>';
                }
                ?>
            </tbody>
        </table>
        <div style="text-align: right; margin-top:20px;">
            <?php
            if ($row2["permissao"] == 5){
                echo'<a href="lista_salas.php" role="button" class="btn btn-primary btn-sm">Voltar</a>';
            } else {               
                echo'<a href="lista_salas.php" role="button" class="btn btn-primary btn-sm">Voltar</a>';   
            }
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