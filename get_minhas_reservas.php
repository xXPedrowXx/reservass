<?php

include 'inc/query.php'; 



header('Content-Type: application/json');
echo json_encode(AjaxF($conn, $_GET['ano'], $_GET['mes'], $_SESSION['id']));
?>