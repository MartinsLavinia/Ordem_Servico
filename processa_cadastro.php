<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "oscd_lamanna";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

$nome  = $_POST['nome'] ?? '';
$email = $_POST['email'] ?? '';
$senha = $_POST['senha'] ?? '';

if (empty($nome) || empty($email) || empty($senha)) {
    die("Por favor, preencha todos os campos.");
}

$senha_hash = password_hash($senha, PASSWORD_DEFAULT);

$sql = "INSERT INTO cliente (NomeCliente, email, senha) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $nome, $email, $senha_hash);

if ($stmt->execute()) {
    header("Location: login-usuario.php");
    exit();
} else {
    echo "Erro ao cadastrar: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
