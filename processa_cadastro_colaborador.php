<?php
include "conexao.php"; // conecta ao banco

// Recebe os dados do formulário
$nome = $_POST['nome'];
$email = $_POST['email'];
$senha = password_hash($_POST['senha'], PASSWORD_DEFAULT); // Criptografa a senha

// Prepara a inserção
$sql = "INSERT INTO colaborador (NomeColaborador, email, senha) 
        VALUES (?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $nome, $email, $senha); // Ajuste no tipo de dados (todos são strings)

if ($stmt->execute()) {
    // Cadastro realizado com sucesso, redireciona para login-adm.php
    header("Location: login-adm.php");
    exit; // A função exit para garantir que o script pare aqui
} else {
    echo "Erro ao cadastrar: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
