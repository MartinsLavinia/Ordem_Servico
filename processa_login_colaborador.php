<?php
session_start();

include("conexao.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['email']) || !isset($_POST['senha']) || empty(trim($_POST['email'])) || empty($_POST['senha'])) {
        header("Location: login-adm.php?login=preencha_campos");
        exit();
    }

    $email = strtolower(trim($_POST['email']));
    $senha = $_POST['senha'];

    // Verifica se o colaborador existe
    $stmt = $conexao->prepare("SELECT CodigoColaborador, senha, NomeColaborador FROM colaborador WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $colaborador = $resultado->fetch_assoc();

        // Verifica a senha
        if (password_verify($senha, $colaborador['senha'])) {
            $_SESSION['colaborador'] = [
                'codigo' => $colaborador['CodigoColaborador'],
                'nome' => $colaborador['NomeColaborador'],
                'email' => $email
            ];

            header("Location: login-adm.php?login=sucesso");
            exit();
        } else {
            // Senha incorreta
            header("Location: login-adm.php?login=senha_incorreta");
            exit();
        }
    } else {
        // Email não encontrado
        header("Location: login-adm.php?login=email_nao_encontrado");
        exit();
    }
} else {
    // Caso o método não seja POST, redireciona para login
    header("Location: login-adm.php");
    exit();
}
?>
