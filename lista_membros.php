<?php
include 'inc/query.php'; 

// Iniciar sessão


// Verificar se o adm está logado
$id = $_SESSION['id'];
$reserva_id = $_GET['reserva_id'];

// Consulta SQL para selecionar todos os usuários
selectID('users', $id, $conn);
$row2 = $resultado->fetch_assoc();
$user_permissao = $row2["permissao"];
$user_id = $row2["id"];

// Consulta para membros associados à reserva
select_por_membros($reserva_id, $conn);

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
            <h3>Lista de membros para a reunião <?php echo $reserva_id ?>  </h3>
        </center>
        <br>
        <br>
        <table class="table" id="table_id">
            <thead>
                <tr>
                    <th scope="col">Hora reserva</th>
                    <th scope="col">Sala</th>
                    <th scope="col">Membros</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Definir array para armazenar os user_id que já foram exibidos
                $user_ids_exibidos = [];

                // Verifica se a consulta retornou algum resultado
                if ($resultado->num_rows > 0) {
                    // Loop através de todos os resultados da consulta
                    while ($row = $resultado->fetch_assoc()) {
                        // Verificar se o user_id já foi exibido
                        if (in_array($row['user_id'], $user_ids_exibidos)) {
                            continue; // Ignorar este membro (não exibir novamente)
                        }

                        // Adicionar o user_id ao array
                        $user_ids_exibidos[] = $row['user_id'];

                        echo "<tr>";
                        $periodo_reserva = $row["periodo_reserva"];
                        $mesI = substr($periodo_reserva, 5, 2); 
                        $diaI = substr($periodo_reserva, 8, 2); 
                        $horaI = substr($periodo_reserva, 11, 5); 
                        
                        $mesF = substr($periodo_reserva, 27, 2); 
                        $diaF = substr($periodo_reserva, 30, 2); 
                        $horaF = substr($periodo_reserva, 33, 5); 
                        
                        echo "<td id='borda'> De: " . $mesI . "/" . $diaI . " " . $horaI . " Até " . $mesF . "/" . $diaF . " " . $horaF . "</td>";
                        echo "<td id='borda'>" . $row["nome_sala"] . "</td>";
                        echo "<td id='borda'>" . $row["conta"] . "</td>";

                        $locatario_id = $row["criador_id"];
                        $usuario_id = $row["user_id"];
                        $confirmacao = $row["confirmacao"];
                        $temp = $row["temp_user_email"];
                        $nome_sala = $row["nome_sala"];
                        
                        if ($user_permissao == 5 || $locatario_id == $id || $usuario_id == $id) {
                            echo "<td>";
                            echo ' ';
                            echo '<a href="exclui_membros.php?id='.$row["membros_id"].'&reserva_id='.$reserva_id.'&user_id='.$usuario_id.'" role="button" class="btn btn-danger btn-sm"><i class="far fa-trash-alt"></i>&nbsp; Excluir</a>';
                            echo "</td>";
                        } else {
                            echo "<td></td>";
                        }
                        echo "</tr>";
                    }
                } else {
                    // Se não houver usuários, exibe uma mensagem na tabela
                    echo '<tr><td colspan="5">Nenhuma reserva encontrada</td></tr>';
                }

                // Exibir todos os usuários temporários
                if (!empty($temp)) {
                    // O valor de $temp pode conter múltiplos usuários, por exemplo, uma lista separada por vírgulas
                    $temp_users = explode(',', $temp); // Dividir os usuários temporários em um array

                    // Iterar sobre todos os usuários temporários
                    foreach ($temp_users as $temp_user) {
                        if (!in_array($temp_user, $user_ids_exibidos)) {
                            // Adicionar o usuário temporário ao array de exibidos
                            $user_ids_exibidos[] = $temp_user;
                            echo "<tr>";
                            echo "<td id='borda'> De: " . $mesI . "/" . $diaI . " " . $horaI . " Até " . $mesF . "/" . $diaF . " " . $horaF . "</td>";
                            echo "<td id='borda'>" . $nome_sala. "</td>";
                            echo "<td id='borda'>" . $temp_user . "</td>";
                            echo "<td></td>";
                            echo "</tr>";
                        }
                    }
                }
                ?>
            </tbody>
        </table>
        <div style="text-align: right; margin-top:20px;">
            <?php
            if (($confirmacao == 0) and ($user_permissao == 5 || $locatario_id == $id)) {
                echo '<a href="confirmar_reserva.php?reserva_id='.$reserva_id.'" role="button" class="btn btn-warning btn-sm">Confirmar</a>';
            }

            echo '<a href="calendario.php" role="button" class="btn btn-primary btn-sm">Voltar</a>';

            if ($user_permissao == 5 || $locatario_id == $id) {
                echo '<a href="cadastro_membro.php?reserva_id='.$reserva_id.'" role="button" class="btn btn-success btn-sm">Novo Membro</a>';
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
