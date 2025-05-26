<?php
session_start();

// Verifica se o usuário ou colaborador está logado
if (!isset($_SESSION['usuario']) && !isset($_SESSION['colaborador'])) {
    header("Location: login-usuario.php?login=acesso_negado");
    exit();
}
?>
