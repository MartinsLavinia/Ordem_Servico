<?php

include 'conexao.php';
$conexao = new mysqli("localhost", "root", "", "oscd_lamanna");

$tecnico_id = 1; // Substitua pelo ID real do técnico logado

$sql = "SELECT * FROM `OS` WHERE CodigoColaborador = $tecnico_id";
$result = $conexao->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Minhas Ordens de Serviço</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <h2>Ordens de Serviço do Técnico</h2>
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Número OS</th>
                <th>Data</th>
                <th>Equipamento</th>
                <th>Defeito</th>
                <th>Serviço</th>
                <th>Valor Total</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($os = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $os['OS'] ?></td>
                    <td><?= $os['NumeroOS'] ?></td>
                    <td><?= date('d/m/Y', strtotime($os['Data'])) ?></td>
                    <td><?= $os['Equipamento'] ?></td>
                    <td><?= $os['Defeito'] ?></td>
                    <td><?= $os['Servico'] ?></td>
                    <td>R$ <?= number_format($os['ValorTotal'], 2, ',', '.') ?></td>
                    <td>
                        <a href="aceitar.php?os=<?= $os['OS'] ?>" class="btn btn-success btn-sm">Aceitar</a>
                        <a href="recusar.php?os=<?= $os['OS'] ?>" class="btn btn-danger btn-sm">Recusar</a>
                        <a href="gerenciar_os.php?os=<?= $os['OS'] ?>" class="btn btn-primary btn-sm">Gerenciar</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="8" class="text-center">Nenhuma OS encontrada.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
