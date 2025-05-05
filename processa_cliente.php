<?php
include('connection.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = mysqli_real_escape_string($connection, $_POST['nome'] ?? '');
    $email = mysqli_real_escape_string($connection, $_POST['email'] ?? '');
    $senha = mysqli_real_escape_string($connection, $_POST['senha'] ?? '');

    if ($nome && $email && $senha) {
        $sql = "INSERT INTO cliente (NomeCliente, email, senha) VALUES ('$nome', '$email', '$senha')";
        if (mysqli_query($connection, $sql)) {
            echo "<script>alert('Cliente cadastrado com sucesso!'); window.location.href='index.php';</script>";
        } else {
            echo "<script>alert('Erro ao cadastrar: " . mysqli_error($connection) . "'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('Preencha todos os campos.'); window.history.back();</script>";
    }

    mysqli_close($connection);
}
?>
