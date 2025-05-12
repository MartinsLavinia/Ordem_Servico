<?php
session_start();
include 'verificar_sessao.php'; // Inclui a verificação
verificarSessao(); // Verifica se o usuário está autenticado

include("conexao.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = strtolower(trim($_POST['email']));
    $senha = $_POST['senha'];

    // Verifica se o colaborador existe
    $stmt = $conexao->prepare("SELECT CodigoColaborador, senha, NomeColaborador FROM colaborador WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $colaborador = $resultado->fetch_assoc();

        // Verifica a senha
        if (password_verify($senha, $colaborador['senha'])) {
            $_SESSION['colaborador'] = [
                'codigo' => $colaborador['CodigoColaborador'],
                'nome' => $colaborador['NomeColaborador'],
                'email' => $email
            ];

            header("Location: aceitar_servicos.php");
            exit();
        }
    }

    // Erro no login
    header("Location: login-adm.php?erro=1");
    exit();
}
?>
