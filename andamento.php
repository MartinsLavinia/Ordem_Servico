<?php
session_start();
include("conexao.php");

// Verifica se o colaborador está logado
if (!isset($_SESSION['colaborador']) || !isset($_SESSION['colaborador']['codigo'])) {
    echo "<div class='alert alert-danger text-center'>Erro: Colaborador não autenticado.</div>";
    exit;
}

$colaboradorId = $_SESSION['colaborador']['codigo'];

// Variáveis de pesquisa
$searchTerm = '';
$searchType = '';

// Se a pesquisa foi feita
if (isset($_GET['search']) && isset($_GET['type'])) {
    $searchTerm = trim($_GET['search']);
    $searchType = $_GET['type'];
}

// Lógica de POST: atualizar andamento ou finalizar OS
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $os_id = (int)$_POST['os_id'];

    // Finalizar OS
    if (isset($_POST['finalizar'])) {
        $stmt = $conexao->prepare("UPDATE os SET Status = 'Finalizada' WHERE OS = ? AND CodigoColaborador = ?");
        $stmt->bind_param("ii", $os_id, $colaboradorId);
        $stmt->execute();
        $stmt->close();
    }

    // Atualizar andamento
    if (isset($_POST['situacao']) && isset($_POST['descricao'])) {
        $situacao = $_POST['situacao'];
        $descricao = $_POST['descricao'];

        $stmt = $conexao->prepare("INSERT INTO andamentoos (OS, Situacao, Descricao) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $os_id, $situacao, $descricao);
        $stmt->execute();
        $stmt->close();

        $stmt = $conexao->prepare("UPDATE os SET SituacaoAtual = ?, DescricaoAtual = ? WHERE OS = ?");
        $stmt->bind_param("ssi", $situacao, $descricao, $os_id);
        $stmt->execute();
        $stmt->close();
    }

    // Alterar valor total (somente se defeito for "outros")
    if (isset($_POST['alterar_valor'])) {
        $novo_valor = (float)$_POST['valor_total'];

        $stmt = $conexao->prepare("SELECT Defeito FROM os WHERE OS = ? AND CodigoColaborador = ?");
        $stmt->bind_param("ii", $os_id, $colaboradorId);
        $stmt->execute();
        $stmt->bind_result($defeito);
        $stmt->fetch();
        $stmt->close();

        if (strtolower($defeito) === 'outros') {
            $stmt = $conexao->prepare("UPDATE os SET ValorTotal = ? WHERE OS = ?");
            $stmt->bind_param("di", $novo_valor, $os_id);
            $stmt->execute();
            $stmt->close();
        } else {
            echo "<div class='alert alert-danger text-center'>Erro: Alteração de valor só é permitida se o defeito for 'outros'.</div>";
        }
    }
}

// Consulta principal: OS não finalizadas
$sql = "SELECT os.OS, os.NumeroOS, os.Data, os.Equipamento, os.Defeito, os.Servico, os.ValorTotal, cliente.NomeCliente
        FROM os
        INNER JOIN cliente ON os.CodigoCliente = cliente.CodigoCliente
        WHERE os.CodigoColaborador = ? AND os.Status != 'Finalizada'";

$params = [$colaboradorId];
$types = "i";

if (!empty($searchTerm)) {
    if ($searchType == 'numeroos') {
        $sql .= " AND os.NumeroOS LIKE ?";
        $types .= "s";
        $params[] = "%$searchTerm%";
    } elseif ($searchType == 'nomecliente') {
        $sql .= " AND cliente.NomeCliente LIKE ?";
        $types .= "s";
        $params[] = "%$searchTerm%";
    }
}

$stmt = $conexao->prepare($sql);
if (!$stmt) {
    die("Erro na preparação da consulta: " . $conexao->error);
}
$stmt->bind_param($types, ...$params);
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

<header class="top-0 w-100 shadow-sm" style="z-index: 1030; height: 80px;">
  <div class="bg-white bg-opacity-75 px-4 py-3 d-flex justify-content-between align-items-center" style="backdrop-filter: blur(10px);">
    <a href="index.php" class="text-decoration-none" style="color: #2B7540; font-size: 1.5rem; font-weight: bold;">
      🔧 Ordem de Serviço
    </a>
    <nav class="d-flex align-items-center">
      <a href="aceitar_servicos.php" class="nav-link mx-3 fw-semibold link-hover-green" style="color: #2B7540;">Serviços</a>
      <a href="andamento.php" class="nav-link mx-3 fw-semibold link-hover-green" style="color: #2B7540;">Andamento</a>
      <a href="logout.php" class="nav-link text-danger mx-3 fw-semibold link-hover-red">Logout</a>
    </nav>
  </div>
</header>

<div class="container mt-5 mb-5">
    <h2 class="mb-4 text-center fw-bold" style="color: #198754;">
        <i class="bi bi-wrench-adjustable-circle"></i> Andamento de Ordens de Serviço
    </h2>

    <!-- Formulário de pesquisa -->
    <form method="GET" class="mb-4 shadow-sm p-3 bg-light rounded">
        <div class="input-group">
            <input type="text" name="search" value="<?= htmlspecialchars($searchTerm) ?>" class="form-control" placeholder="Pesquisar por Número OS ou Nome do Cliente" required>
            <select name="type" class="form-select">
                <option value="numeroos" <?= $searchType == 'numeroos' ? 'selected' : '' ?>>Número da OS</option>
                <option value="nomecliente" <?= $searchType == 'nomecliente' ? 'selected' : '' ?>>Nome do Cliente</option>
            </select>
            <button type="submit" class="btn text-white" style="background-color: #198754;">
                <i class="bi bi-search"></i>
            </button>
        </div>
    </form>

    <?php
    $counter = 0;
    while ($row = $result->fetch_assoc()):
        $counter++;
        $hiddenClass = $counter > 2 ? 'd-none extra-os' : '';
    ?>
    <div class="card shadow mb-4 <?= $hiddenClass ?>" id="os_<?= $row['OS'] ?>">
        <div class="card-header bg-dark text-white d-flex justify-content-between rounded-top">
            <span><strong>OS Nº:</strong> <?= htmlspecialchars($row['NumeroOS']) ?></span>
            <span><strong>Cliente:</strong> <?= htmlspecialchars($row['NomeCliente']) ?></span>
        </div>
        <div class="card-body bg-white">
            <p><strong>Equipamento:</strong> <?= htmlspecialchars($row['Equipamento']) ?></p>
            <p><strong>Defeito:</strong> <?= htmlspecialchars($row['Defeito']) ?></p>
            <p><strong>Serviço:</strong> <?= htmlspecialchars($row['Servico']) ?></p>

            <!-- Atualização de situação -->
            <form method="POST" class="mt-3 mb-4">
                <input type="hidden" name="os_id" value="<?= $row['OS'] ?>">
                <div class="mb-2">
                    <label class="form-label fw-semibold">Situação</label>
                    <input type="text" name="situacao" class="form-control" placeholder="Ex: Aguardando peça" required>
                </div>
                <div class="mb-2">
                    <label class="form-label fw-semibold">Descrição da Atualização</label>
                    <textarea name="descricao" rows="3" class="form-control" placeholder="Descreva a ação realizada..." required></textarea>
                </div>
                <button type="submit" class="btn btn-primary w-auto ms-0">
                    <i class="bi bi-save"></i> Salvar Atualização
                </button>
            </form>

            <!-- Finalizar OS -->
            <form method="POST" class="mb-3">
                <input type="hidden" name="os_id" value="<?= $row['OS'] ?>">
                <button type="submit" name="finalizar" class="btn btn-success w-auto">
                    <i class="bi bi-check-circle"></i> Finalizar OS
                </button>
            </form>

            <!-- Alteração de valor (se defeito for "outros") -->
            <?php if (strtolower($row['Defeito']) == 'outros'): ?>
                <form method="POST" class="mb-3">
                    <input type="hidden" name="os_id" value="<?= $row['OS'] ?>">
                    <div class="mb-2">
                        <label class="form-label fw-semibold">Alterar Valor Total</label>
                        <input type="number" name="valor_total" class="form-control" value="<?= htmlspecialchars($row['ValorTotal']) ?>" step="0.01" required>
                    </div>
                    <button type="submit" name="alterar_valor" class="btn btn-warning w-100">
                        <i class="bi bi-pencil"></i> Alterar Valor
                    </button>
                </form>
            <?php endif; ?>

            <!-- Histórico -->
            <?php
            $hist = $conexao->prepare("SELECT Situacao, Descricao, DataAtualizacao FROM andamentoos WHERE OS = ? ORDER BY DataAtualizacao DESC");
            $hist->bind_param("i", $row['OS']);
            $hist->execute();
            $andamentos = $hist->get_result();
            ?>

            <h6 class="mt-4 fw-bold text-secondary">Histórico de Atualizações:</h6>
            <?php if ($andamentos->num_rows > 0): ?>
                <ul class="list-group shadow-sm">
                    <?php while ($and = $andamentos->fetch_assoc()): ?>
                        <li class="list-group-item">
                            <strong><?= date('d/m/Y H:i', strtotime($and['DataAtualizacao'])) ?></strong><br>
                            <strong>Situação:</strong> <?= htmlspecialchars($and['Situacao']) ?><br>
                            <strong>Descrição:</strong> <?= nl2br(htmlspecialchars($and['Descricao'])) ?>
                        </li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <p class="text-muted">Nenhuma atualização registrada.</p>
            <?php endif; ?>
        </div>
    </div>
    <?php endwhile; ?>

    <?php if ($counter > 2): ?>
        <div class="text-center mb-4">
            <button id="verMaisBtn" class="btn btn-outline-primary shadow-sm">
                <i class="bi bi-plus-circle"></i> Ver mais ordens
            </button>
        </div>
    <?php endif; ?>
</div>

<script>
document.getElementById("verMaisBtn")?.addEventListener("click", function () {
    document.querySelectorAll(".extra-os").forEach(el => el.classList.remove("d-none"));
    this.style.display = "none";
});
</script>

<footer class="text-white pt-5 pb-4" style="background: linear-gradient(rgba(0,0,0,0.85), rgba(0,0,0,0.85)), url('engrenagens.jpg') center center / cover no-repeat;">
  <div class="container text-md-left">
    <div class="row text-center text-md-start">
      <div class="col-md-4 col-lg-4 col-xl-4 mx-auto mb-4">
        <h5 class="text-uppercase fw-bold mb-3" style="color: #198754">🔧 Ordem de Serviço</h5>
        <p>Sistema eficiente para gerenciamento de atendimentos, reparos e controle de serviços técnicos.</p>
      </div>
      <div class="col-md-2 col-lg-2 col-xl-2 mx-auto mb-4">
        <h6 class="text-uppercase fw-bold mb-3">Navegação</h6>
        <ul class="list-unstyled">
          <li><a href="criaros.php" class="text-white text-decoration-none">Cadastrar OS</a></li>
          <li><a href="consulta.php" class="text-white text-decoration-none">Consultar OS</a></li>
          <li><a href="atualizacoes.php" class="text-white text-decoration-none">Atualizações</a></li>
          <li><a href="logout.php" class="text-white text-decoration-none">Logout</a></li>
        </ul>
      </div>
      <div class="col-md-3 col-lg-3 col-xl-3 mx-auto mb-4">
        <h6 class="text-uppercase fw-bold mb-3">Contato</h6>
        <p><i class="bi bi-geo-alt-fill me-2"></i> Rua Exemplo, 123 - Centro</p>
        <p><i class="bi bi-envelope-fill me-2"></i> suporte@osistema.com</p>
        <p><i class="bi bi-phone-fill me-2"></i> (11) 99999-9999</p>
      </div>
    </div>
  </div>
  <div class="text-center mt-4 border-top pt-3" style="font-size: 0.9rem;">
    &copy; <?= date('Y') ?> Ordem de Serviço. Todos os direitos reservados.
  </div>
</footer>

</body>
</html>