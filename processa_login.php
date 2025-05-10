<?php
session_start();
include("conexao.php"); // Garante que a variável $conn exista e esteja conectada

$email = $_POST['email'];
$senha = $_POST['senha'];

if (!isset($conn) || $conn->connect_error) {
    die("Erro na conexão com o banco de dados.");
}

$sql = "SELECT * FROM cliente WHERE email = ? AND senha = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $email, $senha);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $_SESSION['usuario'] = $email;
    echo "<script>
        alert('Login cadastrado com sucesso!');
        setTimeout(function() {
            window.location.href = 'criaros.php';
        }, 1500);
    </script>";
} else {
    echo "<script>
        alert('Email ou senha incorretos!');
        window.location.href = 'login.php';
    </script>";
}
?>
