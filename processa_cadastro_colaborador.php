<?php

include("conexao.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $NomeColaborador = trim($_POST['NomeColaborador']);
    $CodigoCargo = isset($_POST['CodigoCargo']) ? $_POST['CodigoCargo'] : NULL;
    $email = strtolower(trim($_POST['email']));
    $senha = $_POST['senha'];

    // Criptografa a senha
    $senhaCriptografada = password_hash($senha, PASSWORD_DEFAULT);

    // Verifica se o e-mail já existe
    $verifica = $conexao->prepare("SELECT CodigoColaborador FROM colaborador WHERE email = ?");
    $verifica->bind_param("s", $email);
    $verifica->execute();
    $verifica->store_result();

    if ($verifica->num_rows > 0) {
        header("Location: cadastro-adm.php?erro=email");
        exit();
    } else {
        $stmt = $conexao->prepare("INSERT INTO colaborador (NomeColaborador, CodigoCargo, email, senha) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("siss", $NomeColaborador, $CodigoCargo, $email, $senhaCriptografada);

        if ($stmt->execute()) {
            header("Location: login-adm.php?cadastro=sucesso");
            exit();
        } else {
            header("Location: cadastro-adm.php?erro=banco");
            exit();
        }
    }

    $verifica->close();
    if (isset($stmt)) {
        $stmt->close();
    }
    $conexao->close();
}
?>
