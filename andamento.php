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
if (isset($_GET['search'])) {
    $searchTerm = $_GET['search'];
    $searchType = $_GET['type'];
}

// Lógica de POST: atualizar andamento ou finalizar OS
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['finalizar'])) {
        $os_id = $_POST['os_id'];
        $stmt = $conexao->prepare("UPDATE os SET Status = 'Finalizada' WHERE OS = ? AND CodigoColaborador = ?");
        $stmt->bind_param("ii", $os_id, $colaboradorId);
        $stmt->execute();
    } else {
        $os_id = $_POST['os_id'];
        $situacao = $_POST['situacao'];
        $descricao = $_POST['descricao'];

        // Inserir andamento
        $stmt = $conexao->prepare("INSERT INTO andamentoos (OS, Situacao, Descricao) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $os_id, $situacao, $descricao);
        $stmt->execute();
    }

    // Alterar valor (se for o caso de defeito "outros")
    if (isset($_POST['alterar_valor'])) {
        $os_id = $_POST['os_id'];
        $novo_valor = $_POST['valor_total'];
        $stmt = $conexao->prepare("UPDATE os SET ValorTotal = ? WHERE OS = ?");
        $stmt->bind_param("di", $novo_valor, $os_id);
        $stmt->execute();
    }
}

// Consulta principal: apenas OS não finalizadas
$sql = "SELECT os.OS, os.NumeroOS, os.Data, os.Equipamento, os.Defeito, os.Servico, os.ValorTotal, cliente.NomeCliente
        FROM os
        INNER JOIN cliente ON os.CodigoCliente = cliente.CodigoCliente
        WHERE os.CodigoColaborador = ? AND os.Status != 'Finalizada'";

if (!empty($searchTerm)) {
    if ($searchType == 'numeroos') {
        $sql .= " AND os.NumeroOS LIKE ?";
    } elseif ($searchType == 'nomecliente') {
        $sql .= " AND cliente.NomeCliente LIKE ?";
    }
}

$stmt = $conexao->prepare($sql);
if (!empty($searchTerm)) {
    $searchTerm = "%$searchTerm%";
    $stmt->bind_param("is", $colaboradorId, $searchTerm);
} else {
    $stmt->bind_param("i", $colaboradorId);
}
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
    <h2 class="mb-4 text-center">Andamento de Ordens de Serviço</h2>

    <!-- Formulário de pesquisa -->
    <form method="GET" class="mb-4">
        <div class="input-group">
            <input type="text" name="search" value="<?= htmlspecialchars($searchTerm) ?>" class="form-control" placeholder="Pesquisar por Número OS ou Nome do Cliente" required>
            <select name="type" class="form-select">
                <option value="numeroos" <?= $searchType == 'numeroos' ? 'selected' : '' ?>>Número da OS</option>
                <option value="nomecliente" <?= $searchType == 'nomecliente' ? 'selected' : '' ?>>Nome do Cliente</option>
            </select>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-search"></i> Buscar
            </button>
        </div>
    </form>

    <?php
    $counter = 0;
    while ($row = $result->fetch_assoc()):
        $counter++;
        $hiddenClass = $counter > 2 ? 'd-none extra-os' : '';
    ?>
        <div class="card mb-4 <?= $hiddenClass ?>" id="os_<?= $row['OS'] ?>">
            <div class="card-header bg-dark text-white d-flex justify-content-between">
                <span><strong>OS Nº:</strong> <?= htmlspecialchars($row['NumeroOS']) ?></span>
                <span><strong>Cliente:</strong> <?= htmlspecialchars($row['NomeCliente']) ?></span>
            </div>
            <div class="card-body">
                <p><strong>Equipamento:</strong> <?= htmlspecialchars($row['Equipamento']) ?></p>
                <p><strong>Defeito:</strong> <?= htmlspecialchars($row['Defeito']) ?></p>
                <p><strong>Serviço:</strong> <?= htmlspecialchars($row['Servico']) ?></p>

                <!-- Formulário de atualização -->
                <form method="POST" class="mt-3 mb-4">
                    <input type="hidden" name="os_id" value="<?= $row['OS'] ?>">
                    <div class="mb-2">
                        <label class="form-label">Situação</label>
                        <input type="text" name="situacao" class="form-control" placeholder="Ex: Aguardando peça" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Descrição da Atualização</label>
                        <textarea name="descricao" rows="3" class="form-control" placeholder="Descreva a ação realizada..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Salvar Atualização
                    </button>
                </form>

                <!-- Botão Finalizar -->
                <form method="POST">
                    <input type="hidden" name="os_id" value="<?= $row['OS'] ?>">
                    <button type="submit" name="finalizar" class="btn btn-success">
                        <i class="bi bi-check-circle"></i> Finalizar OS
                    </button>
                </form>

                <!-- Alteração de valor (se defeito for "outros") -->
                <?php if (strtolower($row['Defeito']) == 'outros'): ?>
                    <form method="POST">
                        <input type="hidden" name="os_id" value="<?= $row['OS'] ?>">
                        <div class="mb-2">
                            <label class="form-label">Alterar Valor Total</label>
                            <input type="number" name="valor_total" class="form-control" value="<?= htmlspecialchars($row['ValorTotal']) ?>" step="0.01" required>
                        </div>
                        <button type="submit" name="alterar_valor" class="btn btn-warning">
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

                <h6 class="mt-4">📋 Histórico de Atualizações:</h6>
                <?php if ($andamentos->num_rows > 0): ?>
                    <ul class="list-group">
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
            <button id="verMaisBtn" class="btn btn-outline-primary">
                <i class="bi bi-plus-circle"></i> Ver mais
            </button>
        </div>
    <?php endif; ?>

    <a href="aceitar_servicos.php" class="btn btn-secondary mb-4">
        <i class="bi bi-arrow-left"></i> Voltar
    </a>
</div>

<!-- Mostrar OS extras -->
<script>
document.getElementById("verMaisBtn")?.addEventListener("click", function () {
    document.querySelectorAll(".extra-os").forEach(el => el.classList.remove("d-none"));
    this.style.display = "none";
});
</script>

</body>
</html>
