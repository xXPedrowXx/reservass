<?php

include 'inc/query.php'; 



$user_id = $_SESSION['id'];
$filiais = $_SESSION['filial'];


selectID('users', $user_id, $conn);
$row2 = $resultado->fetch_assoc();
$user_permissao = $row2["permissao"];

// Consulta SQL para selecionar todas as salas das filiais do usuário
$salas = select_sala_filial($filiais, $conn);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Salas</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
        integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.css">
    <link rel="stylesheet" href="assets/css/listas.css">
</head>

<body>

    <div class="container" style="margin-top: 40px">

        <center>
            <h3>Lista de Salas</h3>
        </center>
        <br>
        <br>
        <table class="table" id="table_id">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Nome da Sala</th>
                    <th scope="col">Descrição</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Verifica se a consulta retornou algum resultado
                if ($salas) {
                    // Loop através de todos os resultados da consulta
                    foreach ($salas as $sala) {
                        echo "<tr>";
                        echo "<td id='borda'>" . $sala["nome_sala"] . "</td>";
                        echo "<td id='borda'>" . $sala["descricao"] . "</td>";
                        echo '<td id="borda"> <a href="lista_reservas_sala.php?sala_id=' . $sala["id"] . '" role="button" class="btn btn-primary btn-sm">Reservas</a> <div class=""></div>';
                        echo ' ';
                            if ($user_permissao == 5) {
                        echo ' <a href="cadastro_indisponibilidade.php?sala_id='.$sala["id"] .'&user_id='.$user_id.'" role="button" class="btn btn-warning btn-sm">Indisponivel</a>
                        </td>';
                        echo "<td>";
                        echo '<a href="edita_sala.php?id=' . $sala["id"] . '" role="button" class="btn btn-warning btn-sm"><i class="far fa-edit"></i>&nbsp; Editar</a>';
                        echo ' ';
                        echo '<a href="exclui_sala.php?id=' . $sala["id"] . '" role="button" class="btn btn-danger btn-sm"><i class="far fa-trash-alt"></i>&nbsp;</a>';
                        echo "</td>";
                        echo "</tr>";}
                    }
                } else {
                    // Se não houver salas, exibe uma mensagem na tabela
                    echo '<tr><td colspan="3">Nenhuma sala encontrada</td></tr>';
                }
                ?>
            </tbody>
        </table>
        <?php
        if ($user_permissao == 5) {
        echo '<div style="text-align: right;">';
           echo '   <a href="cadastro_sala.php" role="button" class="btn btn-success btn-sm">Nova Sala</a>';
         echo ' </div>';
        }
        ?>
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