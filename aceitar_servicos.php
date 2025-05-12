<?php
session_start();
include("conexao.php");

// Verifica se o colaborador está logado
if (!isset($_SESSION['colaborador']) || !isset($_SESSION['colaborador']['codigo'])) {
    echo "<div class='alert alert-danger text-center'>Erro: Colaborador não autenticado.</div>";
    exit;
}

$colaboradorId = $_SESSION['colaborador']['codigo'];

// Aceitar serviço
if (isset($_GET['aceitar']) && is_numeric($_GET['aceitar'])) {
    $os_id = $_GET['aceitar'];

    // Verifica o defeito para saber se é "outros"
    $stmt = $conexao->prepare("SELECT Defeito FROM os WHERE OS = ?");
    $stmt->bind_param("i", $os_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    // Se o defeito for "outros", o técnico pode alterar o valor
    if (strtolower($row['Defeito']) == 'outros') {
        // Atualiza a OS com o colaborador e altera o valor
        if (isset($_POST['valor_total'])) {
            $valor_total = floatval($_POST['valor_total']);
            $stmt = $conexao->prepare("UPDATE os SET CodigoColaborador = ?, ValorTotal = ? WHERE OS = ?");
            $stmt->bind_param("idi", $colaboradorId, $valor_total, $os_id);
            $stmt->execute();

            // Insere o andamento
            $stmt = $conexao->prepare("INSERT INTO andamentoos (OS, Situacao, Descricao) VALUES (?, ?, ?)");
            $situacao = 'Em andamento';
            $descricao = 'Serviço iniciado pelo colaborador, valor alterado';
            $stmt->bind_param("iss", $os_id, $situacao, $descricao);
            $stmt->execute();

            echo "<div class='alert alert-success text-center'>Serviço aceito e movido para andamento com sucesso! O valor foi alterado.</div>";
        }
    } else {
        // Se o defeito não for "outros", apenas atribui o colaborador sem alterar o valor
        $stmt = $conexao->prepare("UPDATE os SET CodigoColaborador = ? WHERE OS = ?");
        $stmt->bind_param("ii", $colaboradorId, $os_id);

        if ($stmt->execute()) {
            // Insere o andamento
            $stmt = $conexao->prepare("INSERT INTO andamentoos (OS, Situacao, Descricao) VALUES (?, ?, ?)");
            $situacao = 'Em andamento';
            $descricao = 'Serviço iniciado pelo colaborador';
            $stmt->bind_param("iss", $os_id, $situacao, $descricao);
            $stmt->execute();

            echo "<div class='alert alert-success text-center'>Serviço aceito e movido para andamento com sucesso!</div>";
        } else {
            echo "<div class='alert alert-danger text-center'>Erro ao aceitar o serviço.</div>";
        }
    }
}

// Buscar OS pendentes
$sql = "SELECT os.OS, os.NumeroOS, os.Data, os.Equipamento, os.Defeito, os.Servico, os.ValorTotal, cliente.NomeCliente
        FROM os
        INNER JOIN cliente ON os.CodigoCliente = cliente.CodigoCliente
        WHERE os.CodigoColaborador IS NULL";

$result = $conexao->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Serviços Pendentes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4 text-center">Ordens de Serviço Pendentes</h2>

    <?php if ($result->num_rows > 0): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Número OS / Cliente</th>
                        <th>Data</th>
                        <th>Equipamento</th>
                        <th>Defeito</th>
                        <th>Serviço</th>
                        <th>Valor</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <strong><?= htmlspecialchars($row['NumeroOS']) ?></strong><br>
                            <small class="text-muted">Cliente: <?= htmlspecialchars($row['NomeCliente']) ?></small>
                        </td>
                        <td><?= date('d/m/Y', strtotime($row['Data'])) ?></td>
                        <td><?= htmlspecialchars($row['Equipamento']) ?></td>
                        <td><?= htmlspecialchars($row['Defeito']) ?></td>
                        <td><?= htmlspecialchars($row['Servico']) ?></td>
                        <td>R$ <?= number_format($row['ValorTotal'], 2, ',', '.') ?></td>
                        <td class="text-center">
                            <a href="?aceitar=<?= $row['OS'] ?>" class="btn btn-success btn-sm">✔️ Aceitar Serviço</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info text-center">Nenhuma ordem de serviço pendente no momento.</div>
    <?php endif; ?>

    <div class="text-center mt-4">
       <a href="andamento.php" class="btn btn-secondary">
    andamentos <i class="bi bi-arrow-right ms-1"></i>
</a>

    </div>
</div>
</body>
</html>
