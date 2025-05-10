<?php
include 'conexao.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nome = $_POST['nome'] ?? '';
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';
    $codigoCargo = 1; // Por exemplo, 1 = Administrador (ajuste conforme sua tabela de cargos)

    if (!empty($nome) && !empty($email) && !empty($senha)) {
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

        $sql = "INSERT INTO colaboradores (NomeColaborador, CodigoCargo, email, senha)
                VALUES (?, ?, ?, ?)";

        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("siss", $nome, $codigoCargo, $email, $senhaHash);

        if ($stmt->execute()) {
            header("Location: login_adm.php");
            exit;
        } else {
            echo "Erro ao cadastrar: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Preencha todos os campos!";
    }

    $conexao->close();
} else {
    echo "Requisição inválida.";
}
?>
