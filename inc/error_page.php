<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erro</title>
    <style>
        body {
           
            color: white;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 30vh;
            margin: 0;
        }
        .container {
            text-align: center;
            background-color: rgb(0, 0, 0);
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h3 {
            margin-bottom: 10px;
        }
        p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h3>Ops!! Alguma informação inserida não está correta.</h3>
        <p>Por favor, recarregue a página e corrija as informações.</p>
        <?php
        if (isset($_GET['error'])) {
            $errorMessage = urldecode($_GET['error']);
            echo "<p>$errorMessage</p>";
        } else {
            echo "<p>Ocorreu um erro desconhecido.</p>";
        }
        ?>
    </div>
</body>
</html>