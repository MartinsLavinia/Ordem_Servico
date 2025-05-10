<?php
session_start();
$host = "localhost";
$user = "root";
$pass = "";
$db = "oscd_lamanna";

// Criar conexão com o banco de dados
$conn = new mysqli($host, $user, $pass, $db);

// Verificar se a conexão falhou
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

$email = $_POST['email'] ?? '';
$senha = $_POST['senha'] ?? '';

// Verificar se os campos foram preenchidos
if (empty($email) || empty($senha)) {
    $_SESSION['erro'] = "Por favor, preencha todos os campos.";
    header("Location: login-usuario.php");
    exit();
}

// Verificar se o email existe no banco de dados
$verifica = $conn->prepare ("SELECT * FROM cliente WHERE email = ?");
$verifica->bind_param("s", $email);
$verifica->execute();
$result = $verifica->get_result();

if ($result->num_rows == 0) {
   $_SESSION['erro'] = "Email não encontrado.";
header("Location: login-usuario.php"); // Ou o caminho correto
exit();
}

$usuario = $result->fetch_assoc();

// Verificar a senha
if (password_verify($senha, $usuario['senha'])) {
    // A senha está correta, iniciar a sessão
    $_SESSION['usuario_id'] = $usuario['CodigoCliente']; // Armazenar o ID do cliente na sessão
    header("Location: criaros.php"); // Redirecionar para a página inicial
    exit();
} else {
    // Senha incorreta
    $_SESSION['erro'] = "Senha incorreta.";
    header("Location: login-usuario.php");
    exit();
}

$conn->close();
?>
