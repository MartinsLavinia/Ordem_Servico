<?php
session_start();
include("conexao.php");

// Verifica se colaborador está logado - se não, redireciona para login
if (!isset($_SESSION['colaborador']) || !isset($_SESSION['colaborador']['codigo'])) {
    header("Location: login.php");
    exit;
}

$colaboradorId = $_SESSION['colaborador']['codigo'];

// Tabela fixa de valores dos serviços
$servicosValores = [
    'Limpeza' => 50.00,
    'Reparo' => 100.00,
    'Troca de peça' => 150.00,
    'Outros' => null // valor editável
];

// Variáveis para feedback de mensagens
$msgSucesso = "";
$msgErro = "";

// Variáveis de pesquisa
$searchTerm = '';
$searchType = 'numeroos';

// Se a pesquisa foi feita
if (isset($_GET['search']) && isset($_GET['type'])) {
    $searchTerm = trim($_GET['search']);
    $searchType = $_GET['type'];
}

// Processar POST para atualizar andamento, finalizar ou alterar serviço e valor
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $os_id = isset($_POST['os_id']) ? (int)$_POST['os_id'] : 0;

    if ($os_id <= 0) {
        $msgErro = "OS inválida.";
    } else {
        // Finalizar OS
        if (isset($_POST['finalizar'])) {
            $stmt = $conexao->prepare("UPDATE os SET Status = 'Finalizada' WHERE OS = ? AND CodigoColaborador = ?");
            $stmt->bind_param("ii", $os_id, $colaboradorId);
            if ($stmt->execute()) {
                $msgSucesso = "OS finalizada com sucesso.";
            } else {
                $msgErro = "Erro ao finalizar OS.";
            }
            $stmt->close();
        }

        // Atualizar andamento
        if (isset($_POST['situacao']) && isset($_POST['descricao'])) {
            $situacao = trim($_POST['situacao']);
            $descricao = trim($_POST['descricao']);

            if ($situacao === '' || $descricao === '') {
                $msgErro = "Preencha situação e descrição para atualizar o andamento.";
            } else {
                // Inserir andamento
                $stmt = $conexao->prepare("INSERT INTO andamentoos (OS, Situacao, Descricao, DataAtualizacao) VALUES (?, ?, ?, NOW())");
                $stmt->bind_param("iss", $os_id, $situacao, $descricao);
                if ($stmt->execute()) {
                    // Atualizar situação atual da OS
                    $stmt2 = $conexao->prepare("UPDATE os SET SituacaoAtual = ?, DescricaoAtual = ? WHERE OS = ?");
                    $stmt2->bind_param("ssi", $situacao, $descricao, $os_id);
                    $stmt2->execute();
                    $stmt2->close();

                    $msgSucesso = "Andamento atualizado com sucesso.";
                } else {
                    $msgErro = "Erro ao atualizar andamento.";
                }
                $stmt->close();
            }
        }

        // Alterar serviço e valor total
        if (isset($_POST['alterar_servico']) && isset($_POST['servico'])) {
            $novo_servico = trim($_POST['servico']);
            $novo_valor = null;

            // Se serviço for 'Outros', permite informar valor manualmente
            if (strtolower($novo_servico) == 'outros') {
                if (isset($_POST['valor_total'])) {
                    $novo_valor = (float)$_POST['valor_total'];
                    if ($novo_valor < 0) {
                        $msgErro = "Valor inválido para serviço 'Outros'.";
                    }
                } else {
                    $msgErro = "Informe o valor para serviço 'Outros'.";
                }
            } else {
                // Busca valor fixo na tabela
                if (isset($servicosValores[$novo_servico])) {
                    $novo_valor = $servicosValores[$novo_servico];
                } else {
                    $msgErro = "Serviço inválido selecionado.";
                }
            }

            if ($msgErro == "") {
                // Atualiza serviço e valor na OS, apenas se colaborador dono da OS
                $stmt = $conexao->prepare("UPDATE os SET Servico = ?, ValorTotal = ? WHERE OS = ? AND CodigoColaborador = ?");
                $stmt->bind_param("sdii", $novo_servico, $novo_valor, $os_id, $colaboradorId);
                if ($stmt->execute()) {
                    $msgSucesso = "Serviço e valor atualizados com sucesso.";
                } else {
                    $msgErro = "Erro ao atualizar serviço e valor.";
                }
                $stmt->close();
            }
        }

        // Alterar valor total (se defeito for "outros" e não alterou serviço)
        if (isset($_POST['alterar_valor']) && isset($_POST['valor_total'])) {
            $novo_valor = (float)$_POST['valor_total'];

            $stmt = $conexao->prepare("SELECT Defeito FROM os WHERE OS = ? AND CodigoColaborador = ?");
            $stmt->bind_param("ii", $os_id, $colaboradorId);
            $stmt->execute();
            $stmt->bind_result($defeito);
            $stmt->fetch();
            $stmt->close();

            if (strtolower(trim($defeito)) === 'outros') {
                $stmt = $conexao->prepare("UPDATE os SET ValorTotal = ? WHERE OS = ?");
                $stmt->bind_param("di", $novo_valor, $os_id);
                if ($stmt->execute()) {
                    $msgSucesso = "Valor total alterado com sucesso.";
                } else {
                    $msgErro = "Erro ao alterar valor total.";
                }
                $stmt->close();
            } else {
                $msgErro = "Alteração de valor só permitida se o defeito for 'outros'.";
            }
        }
    }
}

// Construção da consulta principal
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
    <meta charset="UTF-8" />
    <title>Andamento de Serviço</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet" />
    <style>
        .link-hover-green:hover { color: #14532d !important; }
        .link-hover-red:hover { color: #b91c1c !important; }
    </style>
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

    <!-- Feedback das ações -->
    <?php if ($msgSucesso): ?>
        <div class="alert alert-success text-center"><?= htmlspecialchars($msgSucesso) ?></div>
    <?php endif; ?>
    <?php if ($msgErro): ?>
        <div class="alert alert-danger text-center"><?= htmlspecialchars($msgErro) ?></div>
    <?php endif; ?>

    <!-- Formulário de pesquisa -->
    <form method="GET" class="mb-4 shadow-sm p-3 bg-light rounded">
        <div class="input-group">
            <input type="text" name="search" value="<?= htmlspecialchars($searchTerm) ?>" class="form-control" placeholder="Pesquisar por Número OS ou Nome do Cliente" required />
            <select name="type" class="form-select">
                <option value="numeroos" <?= $searchType == 'numeroos' ? 'selected' : '' ?>>Número da OS</option>
                <option value="nomecliente" <?= $searchType == 'nomecliente' ? 'selected' : '' ?>>Nome do Cliente</option>
            </select>
            <button type="submit" class="btn text-white" style="background-color: #198754;">
                <i class="bi bi-search"></i>
            </button>
        </div>
    </form>

    <!-- Tabela de valores fixos dos serviços -->
    <div class="mb-4 p-3 bg-light rounded shadow-sm">
        <h5 class="fw-bold text-success mb-3">Tabela de Valores dos Serviços</h5>
        <table class="table table-bordered table-striped" style="max-width: 500px;">
            <thead class="table-success">
                <tr>
                    <th>Serviço</th>
                    <th>Valor (R$)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($servicosValores as $servico => $valor): ?>
                    <tr>
                        <td><?= htmlspecialchars($servico) ?></td>
                        <td>
                            <?= $valor === null ? '-' : number_format($valor, 2, ',', '.') ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <small class="text-muted">* Para o serviço <strong>Outros</strong>, o valor é editável manualmente.</small>
    </div>

    <?php
    $counter = 0;
    while ($row = $result->fetch_assoc()):
        $counter++;
        $hiddenClass = $counter > 2 ? 'd-none extra-os' : '';
    ?>
    <div class="card shadow mb-4 <?= $hiddenClass ?>" id="os_<?= htmlspecialchars($row['OS']) ?>">
        <div class="card-header bg-dark text-white d-flex justify-content-between rounded-top">
            <span><strong>OS Nº:</strong> <?= htmlspecialchars($row['NumeroOS']) ?></span>
            <span><strong>Cliente:</strong> <?= htmlspecialchars($row['NomeCliente']) ?></span>
        </div>
        <div class="card-body bg-white">
            <p><strong>Equipamento:</strong> <?= htmlspecialchars($row['Equipamento']) ?></p>
            <p><strong>Defeito:</strong> <?= htmlspecialchars($row['Defeito']) ?></p>
            <p><strong>Serviço Atual:</strong> <?= htmlspecialchars($row['Servico']) ?></p>
            <p><strong>Valor Total Atual:</strong> R$ <?= number_format($row['ValorTotal'], 2, ',', '.') ?></p>

            <!-- Formulário para alterar serviço e valor -->
            <form method="POST" class="mb-4" novalidate>
                <input type="hidden" name="os_id" value="<?= htmlspecialchars($row['OS']) ?>">

                <div class="mb-3">
                    <label for="servico_<?= $row['OS'] ?>" class="form-label fw-semibold">Alterar Serviço</label>
                    <select name="servico" id="servico_<?= $row['OS'] ?>" class="form-select" onchange="toggleValorInput(<?= $row['OS'] ?>)">
                        <?php foreach ($servicosValores as $servico => $valor): ?>
                            <option value="<?= htmlspecialchars($servico) ?>" <?= strtolower($servico) == strtolower($row['Servico']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($servico) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3" id="valorDiv_<?= $row['OS'] ?>" style="display: <?= strtolower(trim($row['Servico'])) == 'outros' ? 'block' : 'none' ?>;">
                    <label for="valor_total_<?= $row['OS'] ?>" class="form-label fw-semibold">Valor Total (R$)</label>
                    <input type="number" step="0.01" min="0" name="valor_total" id="valor_total_<?= $row['OS'] ?>" class="form-control" value="<?= number_format($row['ValorTotal'], 2, '.', '') ?>" />
                </div>

                <button type="submit" name="alterar_servico" class="btn btn-success">Salvar Alterações</button>
            </form>

            <!-- Formulário para atualizar andamento -->
            <form method="POST" novalidate>
                <input type="hidden" name="os_id" value="<?= htmlspecialchars($row['OS']) ?>">
                <div class="mb-3">
                    <label for="situacao_<?= $row['OS'] ?>" class="form-label fw-semibold">Situação Atual</label>
                    <input type="text" name="situacao" id="situacao_<?= $row['OS'] ?>" class="form-control" placeholder="Situação atual do serviço" required />
                </div>
                <div class="mb-3">
                    <label for="descricao_<?= $row['OS'] ?>" class="form-label fw-semibold">Descrição</label>
                    <textarea name="descricao" id="descricao_<?= $row['OS'] ?>" class="form-control" rows="2" placeholder="Descrição do andamento" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary" name="andamento">Atualizar Andamento</button>
            </form>

            <!-- Botão para finalizar OS -->
            <form method="POST" class="mt-3" onsubmit="return confirm('Tem certeza que deseja finalizar esta OS?');">
                <input type="hidden" name="os_id" value="<?= htmlspecialchars($row['OS']) ?>">
                <button type="submit" class="btn btn-danger" name="finalizar">Finalizar OS</button>
            </form>
        </div>
    </div>
    <?php endwhile; ?>

    <?php if ($counter > 2): ?>
    <div class="text-center mb-4">
        <button id="btnMostrarMais" class="btn btn-outline-success">Mostrar Mais</button>
    </div>
    <?php endif; ?>

</div>

<script>
    // Função para mostrar/esconder input de valor se for serviço 'Outros'
    function toggleValorInput(osId) {
        const select = document.getElementById('servico_' + osId);
        const valorDiv = document.getElementById('valorDiv_' + osId);

        if (select.value.toLowerCase() === 'outros') {
            valorDiv.style.display = 'block';
        } else {
            valorDiv.style.display = 'none';
        }
    }

    // Botão mostrar mais
    document.getElementById('btnMostrarMais')?.addEventListener('click', () => {
        document.querySelectorAll('.extra-os').forEach(el => {
            el.classList.remove('d-none');
        });
        document.getElementById('btnMostrarMais').style.display = 'none';
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$stmt->close();
$conexao->close();
?>
