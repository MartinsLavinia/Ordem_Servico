<?php
session_start();
include("conexao.php");

if (!isset($_POST['email']) || !isset($_POST['senha'])) {
    header("Location: login-usuario.php?login=preencha_campos");
    exit();
}

$email = $_POST['email'];
$senha = $_POST['senha'];

if (!isset($conexao) || $conexao->connect_error) {
    die("Erro na conexão com o banco de dados.");
}

$sql = "SELECT * FROM cliente WHERE email = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $usuario = $result->fetch_assoc();

    if (password_verify($senha, $usuario['senha'])) {
        $_SESSION['usuario'] = $email;
        $_SESSION['CodigoCliente'] = $usuario['CodigoCliente'];
        $_SESSION['NomeCliente'] = $usuario['nome'];

        header("Location: login-usuario.php?login=sucesso");
        exit();
    } else {
        header("Location: login-usuario.php?login=senha_incorreta");
        exit();
    }
} else {
    header("Location: login-usuario.php?login=email_nao_encontrado");
    exit();
}
?>
