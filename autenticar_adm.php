<?php
session_start();

// Dados de autenticação (substitua com os dados reais ou com consulta ao banco de dados)
$admin_email = "admin@exemplo.com";
$admin_senha = "senha_segura";

// Verificar se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    // Autenticação
    if ($email === $admin_email && $senha === $admin_senha) {
        // Criar variável de sessão para o administrador
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_email'] = $email;

        // Redirecionar para a página de consulta ou outra página após o login bem-sucedido
        header("Location: consulta.php"); //MUDAR PRA ONDE VAI -isa
        exit();
    } else {
        // Se o login falhar, exibir mensagem de erro
        $_SESSION['message'] = "Credenciais inválidas! Tente novamente.";
        header("Location: login-adm.php");
        exit();
    }
}
?>
