<?php
session_start();


include 'conexao.php';

// Verifica se o cliente está logado
if (!isset($_SESSION['codigo_cliente'])) {
    header('Location: login_cliente.php');
    exit();
}

$codigoCliente = $_SESSION['codigo_cliente'];

$sql = "SELECT os.NumeroOS, ao.Situacao, ao.Descricao, ao.DataAtualizacao
        FROM andamentoos ao
        JOIN os ON ao.OS = os.OS
        WHERE os.CodigoCliente = ?
        ORDER BY ao.DataAtualizacao DESC";

$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $codigoCliente);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Atualizações da Ordem de Serviço</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center mb-4">Atualizações da Ordem de Serviço</h2>

    <?php if ($result->num_rows > 0): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-dark text-center">
                    <tr>
                        <th>Número OS</th>
                        <th>Situação</th>
                        <th>Descrição</th>
                        <th>Data da Atualização</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td class="text-center"><?= htmlspecialchars($row['NumeroOS']) ?></td>
                            <td class="text-center"><?= htmlspecialchars($row['Situacao']) ?></td>
                            <td><?= htmlspecialchars($row['Descricao']) ?></td>
                            <td class="text-center"><?= date('d/m/Y H:i', strtotime($row['DataAtualizacao'])) ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-warning text-center">Nenhuma atualização encontrada para suas ordens de serviço.</div>
    <?php endif; ?>

    <div class="text-center mt-4">
        <a href="cliente_painel.php" class="btn btn-secondary">🔙 Voltar para o Painel</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
