<?php
session_start();
include("conexao.php");

// Verifica se o colaborador está logado
if (!isset($_SESSION['colaborador']) || !isset($_SESSION['colaborador']['codigo'])) {
    echo "<div class='alert alert-danger text-center'>Erro: Colaborador não autenticado.</div>";
    exit;
}

$colaboradorId = $_SESSION['colaborador']['codigo'];

// Se o formulário de andamento for enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $os_id = $_POST['os_id'];
    $situacao = $_POST['situacao'];
    $descricao = $_POST['descricao'];

    // Inserir no histórico de andamento
    $stmt = $conexao->prepare("INSERT INTO andamentoos (OS, Situacao, Descricao) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $os_id, $situacao, $descricao);
    
    if ($stmt->execute()) {
        echo "<div class='alert alert-success text-center'>Atualização salva com sucesso!</div>";
    } else {
        echo "<div class='alert alert-danger text-center'>Erro ao salvar atualização.</div>";
    }
}

// Buscar OS em andamento para o colaborador
$sql = "SELECT os.OS, os.NumeroOS, os.Data, os.Equipamento, os.Defeito, os.Servico, os.ValorTotal, cliente.NomeCliente
        FROM os
        INNER JOIN cliente ON os.CodigoCliente = cliente.CodigoCliente
        WHERE os.CodigoColaborador = ?";

$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $colaboradorId);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Andamento de Serviço</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4 text-center">Selecione uma Ordem de Serviço para Atualizar</h2>

    <?php if ($result->num_rows > 0): ?>
        <div class="list-group">
            <?php while ($row = $result->fetch_assoc()): ?>
                <a href="andamento.php?os_id=<?= $row['OS'] ?>" class="list-group-item list-group-item-action">
                    <strong>OS Nº: <?= htmlspecialchars($row['NumeroOS']) ?></strong><br>
                    <small><strong>Cliente:</strong> <?= htmlspecialchars($row['NomeCliente']) ?></small><br>
                    <small><strong>Equipamento:</strong> <?= htmlspecialchars($row['Equipamento']) ?></small><br>
                    <small><strong>Defeito:</strong> <?= htmlspecialchars($row['Defeito']) ?></small>
                </a>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info text-center">Você não tem ordens de serviço em andamento no momento.</div>
    <?php endif; ?>
</div>
</body>
</html>
