<?php

include 'inc/query.php'; 


// Iniciar sessão
// Verificar se o adm está logado

$user_id = $_SESSION['id'];

selectID('users',$user_id,$conn);
$row2 = $resultado->fetch_assoc();
$user_permissao = $row2["permissao"];

// Consulta SQL para selecionar todos os usuários
select_filiais($conn);





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
            <h3>Lista de salas</h3>
        </center>
        <br>
        <br>
        <table class="table" id="table_id">
            <thead>
                <tr>
                    <th scope="col">Nome</th>
                    <th scope="col">Endereço</th>
                 

                </tr>
            </thead>
            <tbody>
                <?php
                // Verifica se a consulta retornou algum resultado
                if ($resultado->num_rows > 0) {
                    // Loop através de todos os resultados da consulta
                    while ($row = $resultado->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td id='borda'>" . $row["nome"] . "</td>";
                        echo "<td id='borda'>" . $row["endereco"] . "</td>";

                        echo ' ';
                            if ($user_permissao == 5) {
                  
                        echo "<td>";
                        echo '<a href="edita_filiais.php?id=' . $row["id"] . '" role="button" class="btn btn-warning btn-sm"><i class="far fa-edit"></i>&nbsp; Editar</a>';
                        echo ' ';
                        echo '<a href="exclui_filiais.php?id=' . $row["id"] . '" role="button" class="btn btn-danger btn-sm"><i class="far fa-trash-alt"></i>&nbsp;</a>';
                        echo "</td>";
                        echo "</tr>";}

                    }
                } else {
                    // Se não houver usuários, exibe uma mensagem na tabela
                    echo '<tr><td colspan="5">Nenhuma filial encontrada</td></tr>';
                }
                ?>
            </tbody>
        </table>

        <div style="text-align: right; margin-top:20px;">
            <?php
        if ($user_permissao == 5) {
          echo '<a href="cadastro_filial.php" role="button" class="btn btn-success btn-sm">Nova filial</a>';
        }
?>
        </div>

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