<?php
session_start();
include("conexao.php");


if (!isset($_POST['email']) || !isset($_POST['senha'])) {
    echo "<script>
        alert('Preencha todos os campos!');
        window.location.href = 'login-usuario.php';
    </script>";
    exit;
}

$email = $_POST['email'];
$senha = $_POST['senha'];

// Verifica a conexão
if (!isset($conexao) || $conexao->connect_error) {
    die("Erro na conexão com o banco de dados.");
}

// Buscar o cliente pelo email
$sql = "SELECT * FROM cliente WHERE email = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $usuario = $result->fetch_assoc();

    // Verifica a senha
    if (password_verify($senha, $usuario['senha'])) {
        // Define variáveis de sessão
        $_SESSION['usuario'] = $email;
        $_SESSION['CodigoCliente'] = $usuario['CodigoCliente']; // ✅ ESSENCIAL
        $_SESSION['NomeCliente'] = $usuario['nome']; // opcional: útil para saudação ou cabeçalho

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
