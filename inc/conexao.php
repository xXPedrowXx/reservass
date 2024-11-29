<?php
// Conectar ao banco de dados
session_start();
$servername = "localhost";
$username = "root";
$password = "adm";
$dbname = "controle_salas";

$conn = new mysqli($servername, $username, $password, $dbname);


// Verificar conexão
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

