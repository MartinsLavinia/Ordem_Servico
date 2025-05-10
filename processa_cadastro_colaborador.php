<?php
// Inclui o arquivo de conexão com o banco
include_once 'conexao.php';

// Verifica se os dados foram enviados via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recebe os dados do formulário
    $nome  = mysqli_real_escape_string($conn, $_POST['nome']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT); // senha criptografada

    // Insere no banco de dados
    $sql = "INSERT INTO contas (nome, email, senha) VALUES ('$nome', '$email', '$senha')";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Cadastro realizado com sucesso!'); window.location.href='login-adm.php';</script>";
    } else {
        echo "Erro ao cadastrar: " . mysqli_error($conn);
    }

    // Fecha a conexão
    mysqli_close($conn);
} else {
    // Acesso direto à página sem POST
    header("Location: cadastro-adm.php");
    exit();
}
?>
