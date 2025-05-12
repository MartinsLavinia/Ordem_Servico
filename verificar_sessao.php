<?php
function verificarSessao() {
    session_start();

    if (!isset($_SESSION['email'])) {
        header("Location: login-usuario.php");
        exit();
    }
}