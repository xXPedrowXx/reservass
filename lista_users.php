<?php

include 'inc/query.php'; 

// Iniciar sessão


// Verificar se o adm está logado
verificarPermissao($conn); // Chame a função para verificar a permissão

$filiais = $_SESSION['filial'];

// Consulta SQL para selecionar todos os usuários
$usuarios = select_user($filiais, $conn);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Usuários</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
        integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.css">
    <link rel="stylesheet" href="assets/css/listas.css">
</head>

<body>

    <div class="container" style="margin-top: 40px">

        <center>
            <h3>Lista de Usuários</h3>
        </center>
        <br>
        <br>
        <table class="table" id="table_id">
            <thead>
                <tr>
                    <th scope="col">Usuário</th>
                    <th scope="col">Email</th>
                    <th scope="col">Permissão</th>
                    <th scope="col">Filiais</th>
               
                </tr>
            </thead>
            <tbody>
                <?php
                // Verifica se a consulta retornou algum resultado
                if ($usuarios) {
                    // Loop através de todos os resultados da consulta
                    foreach ($usuarios as $usuario) {
                        echo "<tr>";
                        echo "<td id='borda'>" . htmlspecialchars($usuario["conta"]) . "</td>";
                        echo "<td id='borda'>" . htmlspecialchars($usuario["email"]) . "</td>";
                        echo "<td id='borda'>" . htmlspecialchars($usuario["permissao"]) . "</td>";
                        echo "<td id='borda'>" . htmlspecialchars($usuario["filiais"]) . "</td>";

                        echo "<td>";
                        echo '<a href="edita_login.php?id=' . $usuario["id"] . '" role="button" class="btn btn-warning btn-sm"><i class="far fa-edit"></i>&nbsp; Editar</a>';
                        echo ' ';
                        echo '<a href="exclui_login.php?id=' . $usuario["id"] . '" role="button" class="btn btn-danger btn-sm"><i class="far fa-trash-alt"></i>&nbsp; </a>';
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    // Se não houver usuários, exibe uma mensagem na tabela
                    echo '<tr><td colspan="5">Nenhum usuário encontrado</td></tr>';
                }
                ?>
            </tbody>
        </table>

        <div style="text-align: right;">
            <a href="cadastro.php" role="button" class="btn btn-success btn-sm">Novo Usuário</a>
            <a href="cadastro_Adm.php" role="button" class="btn btn-success btn-sm">Novo Adm</a>
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