<?php

require_once('teste_api_calendar/calendar-api-tutorial-main/google-calendar-api.php');
require_once('teste_api_calendar/calendar-api-tutorial-main/settings.php');
include 'inc/query.php'; 

delete_hour($conn);
delete_indisponivel($conn);

// Verificar se o usuário está logado
if (!isset($_SESSION['id'])) {
    header("Location: login.php"); // Redirecionar para a página de login se o usuário não estiver logado
    exit;
}

if(!isset($_SESSION['access_token'])) {
	header('Location: ./teste_api_calendar/calendar-api-tutorial-main/google-login.php');
	exit();	
}


$user_id = $_SESSION['id'];
$id = $user_id;

// Buscar dados do usuário
selectID($tabela='users',$id,$conn);

if ($resultado->num_rows > 0) {
    $user = $resultado->fetch_assoc();
    $user_conta = $user['conta'];
    $user_email = $user['email'];
    $user_permissao = $user['permissao'];


    
    if ($user_permissao != 5) {
        header("Location: login.php"); // Redirecionar para a página de login se o usuário não tiver a permissão 5
        exit;
    }
} else {
    die("Usuário não encontrado.");
}



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    
</head>
<body>
    <div class="Menu-content">
        <h1>APK</h1>
        <ul>
         
            <li><a href="logout.php">Logout</a></li>
        </ul>
        <div class="search-toggle">
            <button id="toggleButton" class="Button-28">Menu</button>
        </div>
    </div>

    <div class="main">
        <div class="search-dashboards" id="searchDashboards">
            <h3>Meus dados</h3>
            <p>Conta: <?php echo htmlspecialchars($user_conta); ?></p>
            <p>Email: <?php echo htmlspecialchars($user_email); ?></p>
<?php
            if ($user_permissao == 5) {
               echo ' <p>Permissão: Adm</p>';
            } else {
               echo ' <p>Permissão: Usuário</p>';
            }
   ?>      
            <div class="item_search">
                <div class="menu">
                     <h5>Menu</h5>
                    <?php
                        echo '<button class="button-27" onclick="changeIframe(\'calendario.php\')">Calendario</button>';
                        echo '<button class="button-27" onclick="changeIframe(\'lista_salas.php\')">Salas</button>';
                        echo '<button class="button-27" onclick="changeIframe(\'lista_users.php\')">Usuários</button>';
                        echo '<button class="button-27" onclick="changeIframe(\'lista_indisponivel.php\')">Indisponiveis</button>';
                    ?>
                </div>
            </div>
        </div>

        <iframe class="grafico_1" id="contentFrame" src="calendario.php"></iframe>

    </div>
    
    <script>
        // js para trocar a url do Iframe 
        function changeIframe(url) {
            document.getElementById('contentFrame').src = url;
        }
            // js para  deixar o menu responsivo 
        document.getElementById('toggleButton').addEventListener('click', function() {
            var panel = document.getElementById('searchDashboards');
            var iframe = document.getElementById('contentFrame');

            if (panel.style.display === 'none' || panel.style.display === '') {
                panel.style.display = 'block';
                iframe.style.width = '85%';
            } else {
                panel.style.display = 'none';
                iframe.style.width = '100%';
            }
        });

        // Inicialmente esconde o painel e ajusta o iframe
        document.getElementById('searchDashboards').style.display = 'none';
        document.getElementById('contentFrame').style.width = '100%';
    </script>




</body>
</html>

<?php
// Fechar conexão
$conn->close();
?>