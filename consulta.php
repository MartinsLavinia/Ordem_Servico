<?php
include 'conexao.php';

// Excluir OS se solicitado
function excluirOS($numero_os) {
    global $connection;
    $stmt = $connection->prepare("DELETE FROM OS WHERE NumeroOS = ?");
    $stmt->bind_param("s", $numero_os);
    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>✅ Ordem de serviço excluída com sucesso!</div>";
    } else {
        echo "<div class='alert alert-danger'>❌ Erro ao excluir a ordem de serviço.</div>";
    }
}

if (isset($_GET['excluir']) && isset($_GET['numero_os'])) {
    excluirOS($_GET['numero_os']);
}

// Preparar consulta com filtros
$sql = "SELECT OS.NumeroOS, OS.Data, OS.Equipamento, OS.Defeito, OS.Servico, OS.ValorTotal, CLIENTE.NomeCliente 
        FROM OS
        JOIN CLIENTE ON OS.CodigoCliente = CLIENTE.CodigoCliente
        WHERE 1";

$params = [];
if (!empty($_GET['numero_os'])) {
    $sql .= " AND OS.NumeroOS LIKE ?";
    $params[] = "%" . $_GET['numero_os'] . "%";
}
if (!empty($_GET['cliente_nome'])) {
    $sql .= " AND CLIENTE.NomeCliente LIKE ?";
    $params[] = "%" . $_GET['cliente_nome'] . "%";
}

$stmt = $connection->prepare($sql);
if (!empty($params)) {
    $types = str_repeat('s', count($params));
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Consulta de Ordens de Serviço</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h2 class="text-center">Consulta de Ordens de Serviço</h2>

    <form method="GET" class="row g-3 my-4">
        <div class="col-md-4">
            <input type="text" name="numero_os" class="form-control" placeholder="Número da OS" value="<?= htmlspecialchars($_GET['numero_os'] ?? '') ?>">
        </div>
        <div class="col-md-4">
            <input type="text" name="cliente_nome" class="form-control" placeholder="Nome do Cliente" value="<?= htmlspecialchars($_GET['cliente_nome'] ?? '') ?>">
        </div>
        <div class="col-md-4">
            <button type="submit" class="btn btn-primary w-100">🔍 Buscar</button>
        </div>
    </form>

    <?php if ($result->num_rows > 0): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Número OS</th>
                        <th>Data</th>
                        <th>Equipamento</th>
                        <th>Defeito</th>
                        <th>Serviço</th>
                        <th>Valor Total</th>
                        <th>Cliente</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['NumeroOS'] ?></td>
                        <td><?= $row['Data'] ?></td>
                        <td><?= $row['Equipamento'] ?></td>
                        <td><?= $row['Defeito'] ?></td>
                        <td><?= $row['Servico'] ?></td>
                        <td>R$ <?= number_format($row['ValorTotal'], 2, ',', '.') ?></td>
                        <td><?= $row['NomeCliente'] ?></td>
                        <td>
                            <a href="alterar.php?numero_os=<?= $row['NumeroOS'] ?>" class="btn btn-sm btn-outline-warning">✏️ Alterar</a>
                            <a href="?excluir=1&numero_os=<?= $row['NumeroOS'] ?>" 
                               class="btn btn-sm btn-outline-danger" 
                               onclick="return confirm('Tem certeza que deseja excluir esta ordem de serviço?')">🗑️ Excluir</a>
                            <button onclick="imprimir('<?= $row['NumeroOS'] ?>')" class="btn btn-sm btn-outline-info">🖨️ Imprimir</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-warning">❌ Nenhuma ordem de serviço encontrada com os critérios informados.</div>
    <?php endif; ?>
</div>

<script>
function imprimir(numero_os) {
    window.open('imprimir_os.php?numero_os=' + numero_os, '_blank');
}
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
