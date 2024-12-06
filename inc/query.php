<?php


include 'inc/permissao.php';



function handleError($errorMessage) {
    // Redirecione para uma página de erro amigável
    header('Location: ./inc/error_page.php?error=' . urlencode($errorMessage));
    exit();
}

function checkAvailability($conn, $sala_id, $data_inicio, $data_fim) {
    $sql = "SELECT * FROM reservas 
            WHERE sala_id = ? 
            AND (
                (data_inicio < ? AND data_fim > ?) OR 
                (data_inicio < ? AND data_fim > ?) OR 
                (data_inicio >= ? AND data_inicio < ?) OR 
                (data_fim > ? AND data_fim <= ?)
            )";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issssssss", $sala_id, $data_fim, $data_inicio, $data_inicio, $data_fim, $data_inicio, $data_fim, $data_inicio, $data_fim);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        $stmt->close();
        return [];
    }

    $reservas = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $reservas;
}

function selectlogin($email, $conn) {
    $sql = $conn->prepare("SELECT * FROM users WHERE email = ?");
   
    $sql->bind_param("s", $email);
    $sql->execute();
   
    $resultado = $sql->get_result();
   
    if ($resultado) {
        return $resultado;
    } else {
        echo "Erro ao executar a consulta: " . $conn->error;
        return null;
    };
    
}

function Ajax($conn, $ano, $mes) {
    $sql = "SELECT r.id ,r.sala_id ,r.data_inicio, r.data_fim , s.nome_sala as nome_sala , s.id 
FROM reservas r
join
      salas s ON r.sala_id = s.id WHERE YEAR(data_inicio) = ? AND MONTH(data_inicio) = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $ano, $mes);
    $stmt->execute();
    $result = $stmt->get_result();

    $reservas = [];
    while ($row = $result->fetch_assoc()) {
        $reservas[] = $row;
    }
    return $reservas;
}

function AjaxF($conn, $ano, $mes, $membro_id) {
    $sql = "SELECT r.id, r.sala_id, r.data_inicio, r.data_fim, s.nome_sala 
            FROM reservas r
            JOIN salas s ON r.sala_id = s.id
            JOIN membros m ON r.id = m.reserva_id
            WHERE YEAR(r.data_inicio) = ? AND MONTH(r.data_inicio) = ? AND m.user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $ano, $mes, $membro_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $reservas = [];
    while ($row = $result->fetch_assoc()) {
        $reservas[] = $row;
    }
    return $reservas;
}




function getMeetingsInOneHour($now,$oneHourLater,$conn) {
    // Check for meetings starting in 1 hour
    $sql1 = "SELECT r.id, r.data_inicio
             FROM reservas r
             WHERE r.data_inicio BETWEEN ? AND ? AND aviso_1hr = false and confirmacao = 1";
    $stmt1 = $conn->prepare($sql1);
    $stmt1->bind_param("ss", $now, $oneHourLater);
    $stmt1->execute();
    $result1 = $stmt1->get_result();
    $stmt1->close();
    return $result1;
    
}

function updateOneHourNotice($reserva_id,$conn) {
    // Update aviso_1hr to true
    $updateSql1 = 'UPDATE reservas SET aviso_1hr = true WHERE id = ?';
    $updateStmt1 = $conn->prepare($updateSql1);
    $updateStmt1->bind_param("i", $reserva_id);
    $updateStmt1->execute();
    $updateStmt1->close();
}

function getMeetingsStartingNow($now,$conn){
    $sqlNow = "SELECT r.id, r.data_inicio
    FROM reservas r
    WHERE r.data_inicio = ? AND aviso_now = false and confirmacao = 1";
$stmtNow = $conn->prepare($sqlNow);
$stmtNow->bind_param("s", $now);
$stmtNow->execute();
$resultNow = $stmtNow->get_result();
$stmtNow->close();
return $resultNow;

}


function updateNowNotice($reserva_id,$conn) {
    // Update aviso_1hr to true
    $updateSql1 = 'UPDATE reservas SET aviso_now = true WHERE id = ?';
    $updateStmt1 = $conn->prepare($updateSql1);
    $updateStmt1->bind_param("i", $reserva_id);
    $updateStmt1->execute();
    $updateStmt1->close();
    updateOneHourNotice($reserva_id,$conn);
}

function getMeetingsIn24Hours($oneDayLater,$dayAfterTomorrow,$conn) {
    // Check for meetings starting in 24 hours
$sql24hrs = 'SELECT r.id, r.data_inicio
FROM reservas r
WHERE r.data_inicio > ? AND r.data_inicio < ? AND aviso_24hrs = false and confirmacao = 1';
$stmt24hrs = $conn->prepare($sql24hrs);
$stmt24hrs->bind_param("ss", $oneDayLater, $dayAfterTomorrow);
$stmt24hrs->execute();

$result24hrs = $stmt24hrs->get_result();
$stmt24hrs->close();
return $result24hrs;


}


function update24HoursNotice($reserva_id,$conn) {
    // Update aviso_1hr to true
    $updateSql24hrs = 'UPDATE reservas SET aviso_24hrs = true WHERE id = ?';
    $updateStmt24hrs = $conn->prepare($updateSql24hrs);
    $updateStmt24hrs->bind_param("i", $reserva_id);
    $updateStmt24hrs->execute();
    $updateStmt24hrs->close();
}




function selectdata($conn, $sala_id, $ano, $mes, $dia) {
    $sql = "SELECT data_inicio, data_fim FROM reservas WHERE sala_id = ? AND (data_inicio BETWEEN ? AND ? OR data_fim BETWEEN ? AND ? OR (data_inicio <= ? AND data_fim >= ?))";
    $stmt = $conn->prepare($sql);
    $start_date = "$ano-$mes-$dia 00:00:00";
    $end_date = "$ano-$mes-$dia 23:59:59";
    $stmt->bind_param("issssss", $sala_id, $start_date, $end_date, $start_date, $end_date, $start_date, $end_date);
    $stmt->execute();
    return $stmt->get_result();
}

function  insertU($conta, $email, $senha, $u ,$codigo_verificação ,$conn) {
try {
        if ($u == 5) {
            $sql = $conn->prepare("INSERT INTO users (conta, email, senha,permissao,codigo_verificação,verificado) VALUES (?, ?, ?,5,?,0)");
        } else {
            $sql = $conn->prepare("INSERT INTO users (conta, email, senha,permissao,codigo_verificação,verificado) VALUES (?, ?, ?,1,?,0)");
        }

    $sql->bind_param("ssss", $conta, $email, $senha,$codigo_verificação);
    $sql->execute();
}
catch (mysqli_sql_exception $e) {
    handleError("Erro ao cadastrar usuario atentar-se a emails repetidos  : " );
}


    $sql->close();
}


function insertI($user_id, $sala_id, $data_inicio, $data_fim, $tempo, $conn) {
    try {
        if ($data_inicio == $data_fim) {
            handleError("Erro ao reservar: a data de início não pode ser igual à data de fim.");
            return false;
        }

        if ($data_inicio > $data_fim) {
            handleError("Erro ao reservar: a data de início não pode ser maior que a data de fim.");
            return false;
        }

        $sql = $conn->prepare("INSERT INTO indisponivel (user_id, sala_id, motivo, data_inicio, data_fim) VALUES (?, ?, ?, ?, ?)");
        $sql->bind_param("iisss", $user_id, $sala_id, $tempo, $data_inicio, $data_fim);
        $sql->execute();
        $sql->close();
        return true;
    } catch (mysqli_sql_exception $e) {
        handleError("Erro ao cadastrar sala atentar-se a nome de salas repetidas: ".$e->getMessage() );
        return false;
    }
}

function insert_user_temp($reserva_id, $email, $conn) {
    $stmt = $conn->prepare("INSERT INTO user_temp (reserva_id, email) VALUES (?, ?)");
    $stmt->bind_param("is", $reserva_id, $email);

    if ($stmt->execute()) {
        return true;
    } else {
        return false;
    }

    
}



function  insertS($nome_sala, $descricao ,$conn) {

    try {  
        $sql = $conn->prepare("INSERT INTO salas (nome_sala, descricao) VALUES (?, ?)");
   

$sql->bind_param("ss", $nome_sala, $descricao);
$sql->execute();
}
catch (mysqli_sql_exception $e) {
    handleError("Erro ao cadastrar sala atentar-se a nome  de salas repetidas  : " );
}
$sql->close();
}




function insertM2($user_id, $data_inicio, $data_fim, $sala_id, $conn) {
    // Selecionar o id da reserva
    $sql = "SELECT id FROM reservas WHERE sala_id = ? AND data_inicio = ? AND data_fim = ? AND id IS NOT NULL";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iss", $sala_id, $data_inicio, $data_fim);
    $stmt->execute();
    $result = $stmt->get_result();
    $reserva = $result->fetch_assoc();
    $stmt->close();

    if ($reserva) {
        $reserva_id = $reserva['id'];

        // Inserir o membro
        try {
            $sql = $conn->prepare("INSERT INTO membros (user_id, reserva_id) VALUES (?, ?)");
            $sql->bind_param("ii", $user_id, $reserva_id);
            $sql->execute();
            $sql->close();
        } catch (mysqli_sql_exception $e) {
            handleError("Erro ao cadastrar membro atentar-se aos usuarios já adicionados: " . $e->getMessage());
        }
    } else {
        handleError("Reserva não encontrada.");
    }
}

function insertM ($user_id,$reserva_id,$conn) {
    global $googleOauthURL; // Declare a variável global
try {  
    $sql = $conn->prepare("INSERT INTO membros (user_id, reserva_id) VALUES (?, ?)");
$sql->bind_param("ii", $user_id, $reserva_id); $sql->execute(); } catch (mysqli_sql_exception $e) { handleError("Erro ao cadastrar membro atentar-se aos usuarios já adicionados : " . $e->getMessage()); } $sql->close(); }
function insertConfirmacao ($reserva_id,$conn) {
    try {  
        $sql = $conn->prepare("UPDATE reservas SET confirmacao = 1 WHERE id = ?;");
   

$sql->bind_param("i", $reserva_id);
$sql->execute();
}
catch (mysqli_sql_exception $e) {
    handleError("Erro ao confirmar reserva atentar-se a confirmação  já realizada  : ");
}
$sql->close();

}


function insertR($user_id, $sala_id, $data_inicio, $data_fim, $url, $conn) {
  
    try {
        if (empty(checkAvailability($conn, $sala_id, $data_inicio, $data_fim))) {
            $sql = $conn->prepare("INSERT INTO reservas (user_id, sala_id, data_inicio, data_fim, url, confirmacao, aviso_1hr, aviso_now, aviso_24hrs) VALUES (?, ?, ?, ?, ?, 0, 0, 0, 0)");
            $sql->bind_param("iisss", $user_id, $sala_id, $data_inicio, $data_fim, $url);

            if ($data_inicio == $data_fim) {
                handleError("Erro ao reservar: a data de início não pode ser igual à data de fim.");
                return false;
            }

            $sql->execute();
            $reserva_id = $sql->insert_id; // Obtém o ID da reserva inserida
            $sql->close();

            insertM($user_id, $reserva_id, $conn);
           
            return $reserva_id; // Retorna o ID da reserva inserida
        } else {
            handleError("Erro ao reservar: sala não disponível.");
            return false;
     
        }
    } catch (mysqli_sql_exception $e) {
        handleError("Erro ao reservar atente-se á outra reserva marcada nessa mesma hora na mesma sala ou escolha a sala antes de reservar: " . $e->getMessage());
        return false;
    }

}




function select($tabela, $conn) {
    
    global $resultado;
    global $id;

    if (preg_match('/^[a-zA-Z0-9_]+$/', $tabela) === 0) {
        echo "Nome da tabela inválido.";
        return;
    }


    $sql = $conn->prepare("SELECT * FROM $tabela");
   

    $sql->execute();
    
    $resultado = $sql->get_result();
    
    if ($resultado) {
        // Consulta bem-sucedida
    } else {
        echo "Erro ao executar a consulta: " . $conn->error;
        $resultado = null; // Garantir que $resultado seja definido
    }

}


function select_sala($conn){
    global $resultado;
    global $id;

    $sql = $conn->prepare("SELECT s.id, s.nome_sala, s.descricao
FROM salas s
LEFT JOIN indisponivel i ON i.sala_id = s.id
WHERE i.sala_id IS NULL 
   OR (NOW() NOT BETWEEN i.data_inicio AND i.data_fim)");
   

    $sql->execute();
    
    $resultado = $sql->get_result();
    
    if ($resultado) {
        // Consulta bem-sucedida
    } else {
        echo "Erro ao executar a consulta: " . $conn->error;
        $resultado = null; // Garantir que $resultado seja definido
    }
}


function selectID($tabela, $id, $conn) {
    global $resultado;

    // Verifica se a tabela tem um nome válido (para evitar SQL Injection)
    if (preg_match('/^[a-zA-Z0-9_]+$/', $tabela) === 0) {
        echo "Nome da tabela inválido.";
        return;
    }

    // Constrói a consulta de forma segura
    $sql = $conn->prepare("SELECT * FROM " . $tabela . " WHERE id = ?");
    $sql->bind_param("i", $id);
    $sql->execute();
    
    $resultado = $sql->get_result();
    
    if ($resultado) {
        // Consulta bem-sucedida
    } else {
        echo "Erro ao executar a consulta: " . $conn->error;
        $resultado = null; // Garantir que $resultado seja definido
    }
}



function selectemail($conn) {
    global $resultado;

    // Constrói a consulta de forma segura
    $sql = $conn->prepare('SELECT * from reservas where (aviso_24hrs != 1 or aviso_1hr != 1 or aviso_now != 1) and confirmacao = 1');
    $sql->execute();

    $resultado = $sql->get_result();
    
    if ($resultado) {
        // Consulta bem-sucedida
    } else {
        echo "Erro ao executar a consulta: " . $conn->error;
        $resultado = null; // Garantir que $resultado seja definido
    }
}



function select_cadastro_membro($user_id,$conn) {
    global $resultado;

    // Constrói a consulta de forma segura
    $sql = $conn->prepare("
SELECT DISTINCT u.id as user_id, u.conta as user_conta , u.email  as email
FROM users u
LEFT JOIN membros m ON m.user_id = u.id
where u.id != ? ;
        
    ");
    $sql->bind_param("i", $user_id);
    $sql->execute();
    
    $resultado = $sql->get_result();
    
    if ($resultado) {
        // Consulta bem-sucedida
    } else {
        echo "Erro ao executar a consulta: " . $conn->error;
    }
}

function select_cadastro_membro2($user_id, $reserva_id, $conn) {
    global $resultado;

    // Constrói a consulta de forma segura
    $sql = $conn->prepare("
SELECT DISTINCT u.id as user_id, u.conta as user_conta, u.email as email
FROM users u
LEFT JOIN membros m ON m.user_id = u.id AND m.reserva_id = ?
WHERE u.id != ? AND m.user_id IS NULL;
    ");
    $sql->bind_param("ii", $reserva_id, $user_id);
    $sql->execute();
    
    $resultado = $sql->get_result();
    
    if ($resultado) {
        // Consulta bem-sucedida
    } else {
        echo "Erro ao executar a consulta: " . $conn->error;
    }
}


function update_s($nome_sala,$descricao,$id, $conn) {
    try {
    verificarPermissao($conn);
    $stmt = $conn->prepare("UPDATE salas SET nome_sala = ?, descricao = ? WHERE id = ?");
    $stmt->bind_param("ssi", $nome_sala, $descricao,$id);

    $stmt->execute();
    }
    catch (mysqli_sql_exception $e) {
        handleError("Erro ao atualizar sala atentar-se a nome  de salas repetidas  : " );
    }

    $stmt->close();
}

function confirmação($id,$conn) {
    try {

    $stmt = $conn->prepare("UPDATE users SET verificado = true WHERE id = ?");
    $stmt->bind_param("i",$id);

    $stmt->execute();
    }
    catch (mysqli_sql_exception $e) {
        handleError("Erro ao atualizar sala atentar-se a nome  de salas repetidas  : " );
    }

    $stmt->close();
}



function update_U($conta,$email,$senha ,$id,$conn) {
 
    try {
    verificarPermissao($conn);
    $stmt = $conn->prepare("UPDATE users SET conta = ?, email = ? , senha = ? WHERE id = ?");
    $stmt->bind_param("sssi", $conta, $email,$senha,$id);
    $stmt->execute();
    }
    catch (mysqli_sql_exception $e) {
        handleError("Erro ao atualizar usuario atentar-se ao emails repetidos  : " );
    }
    
    $stmt->close();
}


function update_Ind($motivo,$data_inicio,$data_fim ,$id,$conn) {
 
    try {
    verificarPermissao($conn);
    $stmt = $conn->prepare("UPDATE indisponivel SET motivo = ?, data_inicio = ? , data_fim = ? WHERE id = ?");
    $stmt->bind_param("sssi",$motivo, $data_inicio, $data_fim,$id);
    $stmt->execute();
    }
    catch (mysqli_sql_exception $e) {
        handleError("Erro ao atualizar indisponibilidade atentar-se a datas repetidas  : " );
    }
    
    $stmt->close();
}




function update_R($data_inicio, $data_fim, $url, $id, $sala_id, $user_id, $conn) {
    try {
        // Start transaction
        $conn->begin_transaction();

        // Create a savepoint before deleting the reservation
        $conn->savepoint("before_delete");

        // Delete the existing reservation
        delete_reserva($id, $conn);

        // Try to insert the new reservation
        insertR($user_id, $sala_id, $data_inicio, $data_fim, $url, $conn);

        // If everything is fine, commit the transaction
        $conn->commit();
        return true;

    } catch (mysqli_sql_exception $e) {
        // Rollback to the savepoint if insert fails
        $conn->rollback_to("before_delete");

        // Handle the error
        handleError("Erro ao editar reserva : Atente-se a reservas na  mesma data e hora  " );
        return false;
    }
}


function deletee($tabela, $id, $conn) {

   

    if (preg_match('/^[a-zA-Z0-9_]+$/', $tabela) === 0) {
        echo "Nome da tabela inválido.";
        return;
    }
    try {
    $sql = "DELETE FROM $tabela WHERE id=?";
    $stmt2 = $conn->prepare($sql) ;
    $stmt2->bind_param("i", $id);
    $stmt2->execute();
} 
catch (mysqli_sql_exception $e) {   
    handleError("Erro ao deletar  atentar-se ha alguama classe filho criada   : " );
}
    $stmt2->close();
    }



    function delete_reserva($reserva_id, $conn) {
    
        try {
            $sql = "DELETE FROM membros WHERE reserva_id=?";
            $stmt3 = $conn->prepare($sql);
            $stmt3->bind_param("i", $reserva_id);
            $stmt3->execute();
        } 
        catch (mysqli_sql_exception $e) {   
            handleError("Erro ao deletar membros. Atentar-se se esse item está sendo usado em outro relacionamento: " );
        }
        $stmt3->close();
    
        try {
            $sql = "DELETE FROM user_temp WHERE reserva_id=?";
            $stmt4 = $conn->prepare($sql);
            $stmt4->bind_param("i", $reserva_id);
            $stmt4->execute();
        } 
        catch (mysqli_sql_exception $e) {   
            handleError("Erro ao deletar usuários temporários. Atentar-se se esse item está sendo usado em outro relacionamento: " );
        }
        $stmt4->close();
    
        try {
            $sql = "DELETE FROM reservas WHERE id=?";
            $stmt2 = $conn->prepare($sql);
            $stmt2->bind_param("i", $reserva_id);
            $stmt2->execute();
        } 
        catch (mysqli_sql_exception $e) {   
            handleError("Erro ao deletar reservas. Atentar-se se esse item está sendo usado em outro relacionamento: " );
        }
        $stmt2->close();
    }

        function delete_hour($conn) {
            $todayArray = getdate();
          $last_month = $todayArray['year'] . '-' . ($todayArray['mon'] = ($todayArray['mon'] - 1))  . '-' . $todayArray['mday'];
            try {
                $sql = "SELECT id FROM reservas WHERE data_inicio < ? AND data_fim < ?";
                $stmt2 = $conn->prepare($sql);
                $stmt2->bind_param('ss', $last_month, $last_month);
                $stmt2->execute();
                $result = $stmt2->get_result();
        
                while ($row = $result->fetch_assoc()) {
                    $reserva_id = $row['id'];
                    delete_reserva($reserva_id, $conn);
                }
        
                $stmt2->close();
            } catch (mysqli_sql_exception $e) {
                handleError("Erro ao deletar: " );
            }
        }



            function delete_indisponivel($conn) {
    
                $todayArray = getdate();
                $today = $todayArray['year'] . '-' .  $todayArray['mon']  . '-' . $todayArray['mday'];
               
                try {
                $sql = "delete  FROM indisponivel WHERE data_inicio < '$today' AND data_fim < '$today'";
                $stmt2 = $conn->prepare($sql) ;
    
                $stmt2->execute();
            } 
            catch (mysqli_sql_exception $e) {   
                handleError("Erro ao deletar  atentar-se se esse item esta sendo usado em outro relacionamento    : " );
            }
                $stmt2->close();
                }



    
    
   
      
                function select_por_sala($sala_id, $conn) {
                    global $resultado;
                    global $resultado2;  
                    $todayArray = getdate();
                    $today = $todayArray['year'] . '-' . $todayArray['mon'] . '-' . $todayArray['mday'];
                
                    $sql = $conn->prepare("SELECT 
                        r.id as reserva_id,
                        r.data_inicio, 
                        r.data_fim,
                        u.conta AS nome_usuario, 
                        u.id AS user_id,
                        u.permissao as user_permissao,
                        s.nome_sala AS nome_sala,
                        CONCAT(CAST(r.data_inicio AS CHAR), ' - ', CAST(r.data_fim AS CHAR)) AS periodo_reserva,
                        GROUP_CONCAT(membros_user.conta SEPARATOR ', ') AS membros,
                        s.id as sala_id
                    FROM 
                        reservas r
                    JOIN 
                        users u ON r.user_id = u.id
                    JOIN 
                        salas s ON r.sala_id = s.id
                    LEFT JOIN
                        membros m ON r.id = m.reserva_id
                    LEFT JOIN
                        users membros_user ON m.user_id = membros_user.id
                    WHERE 
                        ((r.sala_id = ?) and ( r.data_inicio >= '$today'))
                    GROUP BY 
                        reserva_id, u.conta, r.data_inicio, r.data_fim, s.nome_sala
                    ");
                    $sql->bind_param("i", $sala_id);
                    $sql->execute();
                
                    $resultado = $sql->get_result();


                    $sql2 = $conn->prepare("SELECT 
                        r.id as reserva_id,
                        r.data_inicio, 
                        r.data_fim,
                        u.conta AS nome_usuario, 
                        u.id AS user_id,
                        u.permissao as user_permissao,
                        s.nome_sala AS nome_sala,
                        CONCAT(CAST(r.data_inicio AS CHAR), ' - ', CAST(r.data_fim AS CHAR)) AS periodo_reserva,
                        GROUP_CONCAT(membros_user.conta SEPARATOR ', ') AS membros,
                        s.id as sala_id
                    FROM 
                        reservas r
                    JOIN 
                        users u ON r.user_id = u.id
                    JOIN 
                        salas s ON r.sala_id = s.id
                    LEFT JOIN
                        membros m ON r.id = m.reserva_id
                    LEFT JOIN
                        users membros_user ON m.user_id = membros_user.id
                    WHERE 
                        ((r.sala_id = ?) and ( r.data_inicio >= '$today'))
                    GROUP BY 
                        reserva_id, u.conta, r.data_inicio, r.data_fim, s.nome_sala
                    ");
                    $sql2->bind_param("i", $sala_id);
                    $sql2->execute(); 
                    $resultado2 = $sql2->get_result();
                
                    if ($resultado2) {
                        // Consulta bem-sucedida
                    } else {
                        echo "Erro ao executar a consulta: " . $conn->error;
                        $resultado = null; // Garantir que $resultado seja definido
                    }
                }
    

    function select_indiponivel( $conn) {
        global $resultado;
    
        $sql = $conn->prepare("SELECT 
			i.id as ind_id,
            i.data_inicio, 
            i.data_fim,
            i.motivo ,
            u.conta AS nome_usuario, 
            u.id AS user_id,
            s.nome_sala AS nome_sala ,
            CONCAT(CAST(i.data_inicio AS CHAR), ' - ', CAST(i.data_fim AS CHAR)) AS periodo_reserva
        FROM 
            indisponivel i
        JOIN 
            users u ON i.user_id = u.id
        JOIN 
            salas s ON i.sala_id = s.id

        GROUP BY 
            ind_id, u.conta,  s.nome_sala
        ");

        $sql->execute();
    
        $resultado = $sql->get_result();
    
        if ($resultado) {
            // Consulta bem-sucedida
        } else {
            echo "Erro ao executar a consulta: " . $conn->error;
            $resultado = null; // Garantir que $resultado seja definido
        }
    }





function select_por_dia($conn, $ano, $mes, $dia) {
    global $resultado;

    if ($dia < 10) {
        $dia = "0" . $dia;
    }

    $date = "$ano-$mes-$dia";
    $sql = $conn->prepare("SELECT 
    r.id as reserva_id,
    r.data_inicio, 
    r.data_fim,
    u.conta AS nome_usuario, 
    s.nome_sala AS nome_sala,
    CONCAT(CAST(r.data_inicio AS CHAR), ' - ', CAST(r.data_fim AS CHAR)) AS periodo_reserva,
    u.id AS user_id,
    u.permissao AS user_permissao,
    GROUP_CONCAT(DISTINCT membros_user.conta SEPARATOR ', ') AS membros,
    GROUP_CONCAT(DISTINCT user_temp.email SEPARATOR ', ') AS membros_temp,
    s.id as sala_id
FROM 
    reservas r
JOIN 
    users u ON r.user_id = u.id
JOIN 
    salas s ON r.sala_id = s.id
LEFT JOIN
    membros m ON r.id = m.reserva_id
LEFT JOIN
    users membros_user ON m.user_id = membros_user.id
LEFT JOIN
    user_temp ON r.id = user_temp.reserva_id
WHERE 
    DATE(r.data_inicio) = ?
GROUP BY 
    s.id, r.id, u.conta, r.data_inicio, r.data_fim, s.nome_sala, u.id, u.permissao");
    $sql->bind_param("s", $date);

    $sql->execute();
    
    $resultado = $sql->get_result();
    
    if ($resultado) {
        // Consulta bem-sucedida
    } else {
        echo "Erro ao executar a consulta: " . $conn->error;
        $resultado = null; // Garantir que $resultado seja definido
    }
}

    function select_por_membros($reserva_id,$conn) {
        global $resultado;
    
    
        $sql = $conn->prepare("SELECT 
    salas.nome_sala,
    users.conta,
    membros.id as membros_id,
    reservas.user_id as criador_id,
    reservas.confirmacao as confirmacao,
    users.id as user_id,
    CONCAT(CAST(reservas.data_inicio AS CHAR), ' - ', CAST(reservas.data_fim AS CHAR)) AS periodo_reserva,
    user_temp.email as temp_user_email
FROM 
    membros
JOIN 
    reservas ON membros.reserva_id = reservas.id
JOIN 
    salas ON reservas.sala_id = salas.id
JOIN 
    users ON membros.user_id = users.id
LEFT JOIN 
    user_temp ON reservas.id = user_temp.reserva_id
WHERE 
    reservas.id = ?");
        
        if ($sql === false) {
            echo "Erro ao preparar a consulta: " . $conn->error;
            return;
        }
    
        $sql->bind_param("i", $reserva_id);
        $sql->execute();
        $resultado = $sql->get_result();
    
        if ($resultado) {
            // Consulta bem-sucedida
        } else {
            echo "Erro ao executar a consulta: " . $conn->error;
            $resultado = null; // Garantir que $resultado seja definido
        }
    }

    function select_email($reserva_id, $conn) {
        $sql = $conn->prepare("SELECT 
    r.id AS reserva_id,
    r.data_inicio, 
    r.data_fim,
    u.conta AS nome_usuario, 
    u.id AS user_id,
    u.permissao AS user_permissao,
    u.email AS user_email,
    s.nome_sala AS nome_sala,
    CONCAT(CAST(r.data_inicio AS CHAR), ' - ', CAST(r.data_fim AS CHAR)) AS periodo_reserva,
    GROUP_CONCAT(membros_user.conta SEPARATOR ', ') AS membros,
    GROUP_CONCAT(membros_user.email SEPARATOR ', ') AS membros_emails,
    s.id AS sala_id,
    r.url,
    ut.email AS user_temp_email
FROM 
    reservas r
JOIN 
    users u ON r.user_id = u.id
JOIN 
    salas s ON r.sala_id = s.id
LEFT JOIN
    membros m ON r.id = m.reserva_id
LEFT JOIN
    users membros_user ON m.user_id = membros_user.id
LEFT JOIN
    user_temp ut ON r.id = ut.reserva_id
WHERE 
    r.id = ?
GROUP BY 
    reserva_id, u.conta, r.data_inicio, r.data_fim, s.nome_sala, ut.email;");
    
        if ($sql === false) {
            echo "Erro ao preparar a consulta: " . $conn->error;
            return null;
        }
    
        $sql->bind_param("i", $reserva_id);
        $sql->execute();
        $resultado = $sql->get_result();
    
        if ($resultado) {
            return $resultado;
        } else {
            echo "Erro ao executar a consulta: " . $conn->error;
            return null;
        }};



?>