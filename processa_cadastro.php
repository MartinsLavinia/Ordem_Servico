<?php
include 'conexao.php';

// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = trim($_POST["nome"]);
    $email = trim($_POST["email"]);
    $senha = $_POST["senha"];

    // Criptografar a senha
    $senhaCriptografada = password_hash($senha, PASSWORD_DEFAULT);

    // Verifica se o email já existe
    $verifica = $conexao->prepare("SELECT CodigoCliente FROM cliente WHERE email = ?");
    $verifica->bind_param("s", $email);
    $verifica->execute();
    $verifica->store_result();

    if ($verifica->num_rows > 0) {
        // Redireciona com erro
        header("Location: cadastro.php?erro=email");
        exit();
    } else {
        // Cadastra o novo cliente
        $stmt = $conexao->prepare("INSERT INTO cliente (NomeCliente, email, senha) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $nome, $email, $senhaCriptografada);

        if ($stmt->execute()) {
            header("Location: login-usuario.php?cadastro=sucesso");
            exit();
        } else {
            header("Location: cadastro.php?erro=banco");
            exit();
        }
    }

    $verifica->close();
    $stmt->close();
    $conexao->close();
}
?>
