<?php
    session_start();
    /*
    if (isset($_GET['id_token'])){
        $_SESSION['id_user'] = $_GET['id_token'];
        header("Location:" . $_GET['link']);
    }*/
    // Fazer isso por jwt

    if (!isset($_SESSION['id_user'])){
        session_destroy();
        http_response_code(401);
        die("Usuário não autenticado!");
    }
?>