<?php

include 'inc/query.php'; 

header('Content-Type: application/json');
echo json_encode( Ajax($conn, $_GET['ano'], $_GET['mes']));
?>