<?php
session_start();

include("conexao.php");

$email = $_POST['email'];
$senha = $_POST['senha'];

if (!isset($conexao) || $conexao->connect_error) {
    die("Erro na conexão com o banco de dados.");
}

// Buscar só pelo email
$sql = "SELECT * FROM cliente WHERE email = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $usuario = $result->fetch_assoc();

    // Verifica se a senha está correta
    if (password_verify($senha, $usuario['senha'])) {
        $_SESSION['usuario'] = $email;
        echo "<script>
            alert('Login realizado com sucesso!');
            setTimeout(function() {
                window.location.href = 'criaros.php';
            }, 1500);
        </script>";
    } else {
        echo "<script>
            alert('Senha incorreta!');
            window.location.href = 'login-usuario.php';
        </script>";
    }
} else {
    echo "<script>
        alert('Email não encontrado!');
        window.location.href = 'login-usuario.php';
    </script>";
}
?>
