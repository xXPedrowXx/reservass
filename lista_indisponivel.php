<?php

include 'inc/query.php'; 


// Iniciar sessão
// Verificar se o adm está logado
verificarPermissao($conn); // Chame a função para verificar a permissão
$user_id = $_SESSION['id'];

// Consulta SQL para selecionar todos os usuários
select_indiponivel($conn)





?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>CRUD PHP - Usuários</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
        integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.css">
    <link rel="stylesheet" href="assets/css/listas.css">
</head>

<body>

    <div class="container" style="margin-top: 40px">

        <center>
            <h3>Lista de salas Indisponiveis</h3>
        </center>
        <br>
        <br>
        <table class="table" id="table_id">
            <thead>
                <tr>
                    <th scope="col">Sala</th>
                    <th scope="col">Periodo </th>
                    <th scope="col">Motivo</th>
                    <th scope="col">ADM</th>

                </tr>
            </thead>
            <tbody>
                <?php
                // Verifica se a consulta retornou algum resultado
                if ($resultado->num_rows > 0) {
                    // Loop através de todos os resultados da consulta
                    while ($row = $resultado->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td id='borda'>" . $row["nome_sala"] . "</td>";
                        $periodo_reserva = $row["periodo_reserva"];
                        $mesI = substr($periodo_reserva, 5, 2); // Do 5º ao 14º caractere
                        $diaI = substr($periodo_reserva, 8, 2); // Do 7º ao 8º caractere
                        $horaI = substr($periodo_reserva, 11, 5); // Do 9º ao 13º caractere
                        
                        $mesF = substr($periodo_reserva, 27, 2); // Do 27º ao 28º caractere
                        $diaF = substr($periodo_reserva, 30, 2); // Do 29º ao 30º caractere
                        $horaF = substr($periodo_reserva, 33, 5); // Do 31º ao 35º caractere
                        
                        echo "<td id='borda'> De: " . $mesI . "/" . $diaI . " " . $horaI . " Até " . $mesF . "/" . $diaF . " " . $horaF . "</td>";

                        echo "<td id='borda'>" . $row["motivo"] . "</td>";
                        echo "<td id='borda'>" . $row["nome_usuario"] . "</td>";
                        echo "<td>";
                        echo '<a href="edita_indisponibilidade.php?id=' . $row["ind_id"] . '" role="button" class="btn btn-warning btn-sm"><i class="far fa-edit"></i>&nbsp; Editar</a>';
                        echo ' ';
                        echo '<a href="exclui_indisponibilidade.php?id=' . $row["ind_id"] . '" role="button" class="btn btn-danger btn-sm"><i class="far fa-trash-alt"></i>&nbsp;</a>';
                        echo "</td>";
                        echo "</tr>";

                    }
                } else {
                    // Se não houver usuários, exibe uma mensagem na tabela
                    echo '<tr><td colspan="5">Nenhum Dashboard encontrado</td></tr>';
                }
                ?>
            </tbody>
        </table>

  

    </div>

    <script src="https://kit.fontawesome.com/cae6919cdb.js" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"
        integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"
        integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous">
    </script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"
        integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous">
    </script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.js">
    </script>

</body>

</html>