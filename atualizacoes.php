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
<header class=" top-0 w-100 shadow-sm" style="z-index: 1030; height: 80px;">
  <div class="bg-white bg-opacity-75 px-4 py-3 d-flex justify-content-between align-items-center" style="backdrop-filter: blur(10px);">
    <a href="index.php" class="text-decoration-none text-primary fs-4 fw-bold">
      🔧 Ordem de Serviço
    </a>
    <nav class="d-flex align-items-center">
      <a href="atualizacoes.php" class="nav-link text-primary mx-3 fw-semibold link-hover-blue">Início</a>
      <a href="criaros.php" class="nav-link text-primary mx-3 fw-semibold link-hover-blue">Cadastrar OS</a>
      <a href="consulta.php" class="nav-link text-primary mx-3 fw-semibold link-hover-blue">Consultar OS</a>
      <a href="logout.php" class="nav-link text-danger mx-3 fw-semibold link-hover-red">Logout</a>
    </nav>
  </div>
</header>

<div class="content" style="padding-top: 40px;">
  <div class="container mt-4">
    <h2>Bem-vindo, <?= htmlspecialchars($_SESSION['nome'] ?? 'Usuário') ?>!</h2>
    <p>Aqui você pode cadastrar, consultar e gerenciar ordens de serviço.</p>
  </div>
</div>
<body>
<div class="container mt-5">
    <h2 class="mb-4 text-center">Selecione uma Ordem de Serviço para Atualizar</h2>

    <?php if ($result->num_rows > 0): ?>
    <div class="table-responsive">
        <table class="table table-striped table-bordered align-middle text-center">
            <thead class="table-primary">
                <tr>
                    <th>Número OS</th>
                    <th>Cliente</th>
                    <th>Equipamento</th>
                    <th>Defeito</th>
                    <th>Data</th>
                    <th>Serviço</th>
                    <th>Valor Total</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['NumeroOS']) ?></td>
                        <td><?= htmlspecialchars($row['NomeCliente']) ?></td>
                        <td><?= htmlspecialchars($row['Equipamento']) ?></td>
                        <td><?= htmlspecialchars($row['Defeito']) ?></td>
                        <td><?= htmlspecialchars(date('d/m/Y', strtotime($row['Data']))) ?></td>
                        <td><?= htmlspecialchars($row['Servico']) ?></td>
                        <td>R$ <?= number_format($row['ValorTotal'], 2, ',', '.') ?></td>
                        <td>
                            <a href="andamento.php?os_id=<?= $row['OS'] ?>" class="btn btn-sm btn-outline-primary">
                                Atualizar
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <div class="alert alert-info text-center">Você não tem ordens de serviço em andamento no momento.</div>
<?php endif; ?>

</div>
</body>
</html>
