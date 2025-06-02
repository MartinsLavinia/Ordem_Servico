<?php
// Habilita a exibição de todos os erros do PHP para depuração.
// REMOVA OU COMENTE ESSAS LINHAS EM AMBIENTES DE PRODUÇÃO!
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Inclui o arquivo de conexão com o banco de dados
include("conexao.php");
// Inclui o script de verificação de login para proteger a página
include('verifica_login.php');

// Verifica se o colaborador está logado. Se não, redireciona para a página de login.
if (!isset($_SESSION['colaborador']) || !isset($_SESSION['colaborador']['codigo'])) {
    header("Location: login-usuario.php");
    exit;
}

// Obtém o código do colaborador logado da sessão
$colaboradorId = $_SESSION['colaborador']['codigo'];
$nome_colaborador = $_SESSION['colaborador']['nome']; // Pega o nome do colaborador para o cabeçalho

// Tabela fixa de valores dos serviços oferecidos
$servicosValores = [
    'Limpeza' => 50.00,
    'Reparo' => 100.00,
    'Troca de peça' => 150.00,
    'Outros' => null // 'Outros' permite que o valor seja inserido manualmente
];

// Variáveis para armazenar mensagens de feedback (sucesso ou erro)
$msgSucesso = "";
$msgErro = "";

// Verifica se há mensagens de sucesso ou erro passadas via URL (após redirecionamento)
if (isset($_GET['msgSucesso'])) {
    $msgSucesso = htmlspecialchars($_GET['msgSucesso']);
}
if (isset($_GET['msgErro'])) {
    $msgErro = htmlspecialchars($_GET['msgErro']);
}

// Variáveis para controle da funcionalidade de pesquisa
$searchTerm = ''; // Termo de pesquisa
$searchType = 'numeroos'; // Tipo de pesquisa padrão

// Processa a pesquisa se um termo e tipo forem submetidos via GET
if (isset($_GET['search']) && isset($_GET['type'])) {
    $searchTerm = trim($_GET['search']);
    $searchType = $_GET['type'];
}

// --- Processa requisições POST para ações de Finalização, Atualização de Andamento e Alteração de Serviço ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Valida o ID da OS recebido via POST
    $os_id = isset($_POST['os_id']) ? (int)$_POST['os_id'] : 0;

    if ($os_id <= 0) {
        $msgErro = "ID da OS inválido.";
        header("Location: andamento.php?msgErro=" . urlencode($msgErro));
        exit;
    } else {
        // --- AÇÃO DE FINALIZAR OS ---
        if (isset($_POST['finalizar'])) {
            // Verifica se o ID do colaborador está disponível
            if (!isset($colaboradorId) || empty($colaboradorId)) {
                $msgErro = "Colaborador não identificado para finalizar a OS.";
            } else {
                // Antes de finalizar, verificar se a OS realmente pertence ao colaborador e não está finalizada
                // A subconsulta verifica se JÁ existe um registro de finalização para esta OS.
                $stmt_check = $conexao->prepare("
                    SELECT os.OS
                    FROM os
                    WHERE os.OS = ?
                    AND os.CodigoColaborador = ?
                    AND NOT EXISTS (
                        SELECT 1
                        FROM andamentoos ao
                        WHERE ao.OS = os.OS AND ao.TipoAtualizacao = 'finalizacao'
                    )
                ");
                if (!$stmt_check) {
                    $msgErro = "Erro na preparação da verificação de finalização: " . $conexao->error;
                } else {
                    $stmt_check->bind_param("ii", $os_id, $colaboradorId);
                    $stmt_check->execute();
                    $result_check = $stmt_check->get_result();

                    if ($result_check->num_rows > 0) {
                        // A OS pertence ao colaborador e NÃO está finalizada na tabela andamentoos

                        // Insere um registro de finalização na tabela andamentoos
                        $stmt = $conexao->prepare("INSERT INTO andamentoos (OS, Situacao, Descricao, TipoAtualizacao, DataAtualizacao) VALUES (?, ?, ?, ?, NOW())");
                        if (!$stmt) {
                            $msgErro = "Erro na preparação da consulta de finalização em andamentoos: " . $conexao->error;
                        } else {
                            $situacao_final = "Finalizada";
                            $descricao_final = "OS finalizada pelo colaborador.";
                            $tipo_final = "finalizacao";
                            $stmt->bind_param("isss", $os_id, $situacao_final, $descricao_final, $tipo_final);

                            if ($stmt->execute()) {
                                // Não precisamos mais atualizar os.SituacaoAtual e os.DescricaoAtual com "Finalizada" aqui
                                // porque a consulta principal vai buscar a última situação de andamentoos.
                                $msgSucesso = "OS " . $os_id . " finalizada com sucesso.";
                                header("Location: andamento.php?msgSucesso=" . urlencode($msgSucesso));
                                exit;
                            } else {
                                $msgErro = "Erro ao registrar finalização da OS em andamentoos: " . $stmt->error;
                            }
                            $stmt->close();
                        }
                    } else {
                        $msgErro = "Não foi possível finalizar a OS " . $os_id . ". Verifique se ela pertence a você ou se já está finalizada.";
                    }
                    $stmt_check->close();
                }
            }
            if (!empty($msgErro)) {
                header("Location: andamento.php?msgErro=" . urlencode($msgErro));
                exit;
            }
        }
        // --- FIM DA AÇÃO DE FINALIZAR OS ---

        // --- AÇÃO DE ATUALIZAR ANDAMENTO ---
        if (isset($_POST['situacao']) && isset($_POST['descricao'])) {
            $situacao = trim($_POST['situacao']);
            $descricao = trim($_POST['descricao']);

            if ($situacao === '' || $descricao === '') {
                $msgErro = "Preencha a situação e a descrição para atualizar o andamento.";
            } else {
                // Insere um novo registro de andamento na tabela 'andamentoos' com TipoAtualizacao = 'andamento'
                $stmt = $conexao->prepare("INSERT INTO andamentoos (OS, Situacao, Descricao, TipoAtualizacao, DataAtualizacao) VALUES (?, ?, ?, 'andamento', NOW())");
                if (!$stmt) {
                    $msgErro = "Erro na preparação da consulta de andamento: " . $conexao->error;
                } else {
                    $stmt->bind_param("iss", $os_id, $situacao, $descricao);
                    if ($stmt->execute()) {
                        // Não precisamos mais atualizar os.SituacaoAtual e os.DescricaoAtual aqui
                        // porque a consulta principal vai buscar a última situação de andamentoos.
                        $msgSucesso = "Andamento da OS " . $os_id . " atualizado com sucesso.";
                    } else {
                        $msgErro = "Erro ao inserir novo andamento na OS: " . $stmt->error;
                    }
                    $stmt->close();
                }
            }
            header("Location: andamento.php?msgSucesso=" . urlencode($msgSucesso) . "&msgErro=" . urlencode($msgErro));
            exit;
        }
        // --- FIM DA AÇÃO DE ATUALIZAR ANDAMENTO ---

        // --- AÇÃO DE ALTERAR SERVIÇO E VALOR TOTAL ---
        if (isset($_POST['alterar_servico']) && isset($_POST['servico'])) {
            $novo_servico = trim($_POST['servico']);
            $novo_valor = null;

            if (strtolower($novo_servico) == 'outros') {
                if (isset($_POST['valor_total']) && $_POST['valor_total'] !== '') {
                    $novo_valor = (float)str_replace(',', '.', $_POST['valor_total']);
                    if ($novo_valor < 0) {
                        $msgErro = "Valor inválido para serviço 'Outros'.";
                    }
                } else {
                    $msgErro = "Informe o valor para o serviço 'Outros'.";
                }
            } else {
                if (isset($servicosValores[$novo_servico])) {
                    $novo_valor = $servicosValores[$novo_servico];
                } else {
                    $msgErro = "Serviço inválido selecionado.";
                }
            }

            if ($msgErro == "") {
                // Atualiza o serviço e o valor total na tabela 'os'
                // Mantemos esta atualização na tabela 'os' pois é um dado mestre da OS
                $stmt = $conexao->prepare("UPDATE os SET Servico = ?, ValorTotal = ? WHERE OS = ? AND CodigoColaborador = ?");
                if (!$stmt) {
                    $msgErro = "Erro na preparação da consulta de alteração de serviço: " . $conexao->error;
                } else {
                    $stmt->bind_param("sdii", $novo_servico, $novo_valor, $os_id, $colaboradorId);
                    if ($stmt->execute()) {
                        $msgSucesso = "Serviço e valor da OS " . $os_id . " atualizados com sucesso.";
                    } else {
                        $msgErro = "Erro ao atualizar serviço e valor da OS: " . $stmt->error;
                    }
                    $stmt->close();
                }
            }
            header("Location: andamento.php?msgSucesso=" . urlencode($msgSucesso) . "&msgErro=" . urlencode($msgErro));
            exit;
        }
        // --- FIM DA AÇÃO DE ALTERAR SERVIÇO E VALOR TOTAL ---
    }
}

// --- CONSULTA PRINCIPAL PARA EXIBIR AS OS EM ANDAMENTO ---
// Esta consulta agora busca a última situação e descrição da tabela 'andamentoos'.
$sql = "
    SELECT
        os.OS,
        os.NumeroOS,
        os.Data,
        os.Equipamento,
        os.Defeito,
        os.Servico,
        os.ValorTotal,
        cliente.NomeCliente,
        COALESCE(sub.Situacao, 'Aguardando Início') AS SituacaoAtual, -- Pega a última situação de andamentoos ou um padrão
        COALESCE(sub.Descricao, 'Nenhum andamento registrado.') AS DescricaoAtual -- Pega a última descrição de andamentoos ou um padrão
    FROM
        os
    INNER JOIN
        cliente ON os.CodigoCliente = cliente.CodigoCliente
    LEFT JOIN (
        SELECT
            ao.OS,
            ao.Situacao,
            ao.Descricao,
            ao.DataAtualizacao,
            ao.id, -- Inclui o ID para desempate em caso de mesma DataAtualizacao
            ROW_NUMBER() OVER(PARTITION BY ao.OS ORDER BY ao.DataAtualizacao DESC, ao.id DESC) as rn
        FROM
            andamentoos ao
    ) AS sub ON os.OS = sub.OS AND sub.rn = 1 -- Junta com a subconsulta que encontra o último andamento
    WHERE
        os.CodigoColaborador = ?
        AND NOT EXISTS (
            SELECT 1
            FROM andamentoos ao_final
            WHERE ao_final.OS = os.OS AND ao_final.TipoAtualizacao = 'finalizacao'
        )
"; // Condição para filtrar as OS finalizadas através da tabela andamentoos

$params = [$colaboradorId];
$types = "i";

// Adiciona condições de pesquisa se um termo de pesquisa foi fornecido
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

// Adiciona ordenação para exibir as OS mais recentes primeiro
$sql .= " ORDER BY os.Data DESC, os.OS DESC";

// Prepara e executa a consulta principal
$stmt = $conexao->prepare($sql);
if (!$stmt) {
    die("Erro na preparação da consulta principal: " . $conexao->error);
}
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

// --- Fim da Consulta Principal ---
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <title>Andamento de Serviço</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="style-adm.css" rel="stylesheet">
    <style>
        /* Estilo para o botão de cancelar no modal */
        .btn-outline-cancel {
            border: 2px solid #6c757d;
            color: #6c757d;
            background-color: transparent;
            transition: background-color 0.2s ease;
        }

        .btn-outline-cancel:hover {
            background-color: #f0f0f0;
            border: 2px solid #6c757d;
            color: #6c757d;
        }
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
      <div class="dropdown">
        <a class="nav-link dropdown-toggle text-dark fw-semibold" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fas fa-user-circle me-2"></i><?= htmlspecialchars($nome_colaborador) ?>
        </a>
        <ul class="dropdown-menu dropdown-menu-end">
            <li>
            <a class="dropdown-item text-danger" href="logout.php">
                <i class="fas fa-sign-out-alt me-2"></i>Logout
            </a>
            </li>
        </ul>
        </div>
    </nav>
  </div>
</header>

<div class="container mt-5 mb-5">

    <h2 class="mb-4 text-center fw-bold" style="color: #198754;">
        <i class="bi bi-wrench-adjustable-circle"></i> Andamento de Ordens de Serviço
    </h2>

    <?php if ($msgSucesso): ?>
        <div class="alert alert-success text-center"><?= htmlspecialchars($msgSucesso) ?></div>
    <?php endif; ?>
    <?php if ($msgErro): ?>
        <div class="alert alert-danger text-center"><?= htmlspecialchars($msgErro) ?></div>
    <?php endif; ?>

    <form method="GET" class="mb-4 shadow-sm p-3 bg-light rounded">
        <div class="input-group">
            <input type="text" name="search" value="<?= htmlspecialchars($searchTerm) ?>" class="form-control" placeholder="Pesquisar por Número OS ou Nome do Cliente" />
            <select name="type" class="form-select">
                <option value="numeroos" <?= $searchType == 'numeroos' ? 'selected' : '' ?>>Número da OS</option>
                <option value="nomecliente" <?= $searchType == 'nomecliente' ? 'selected' : '' ?>>Nome do Cliente</option>
            </select>
            <button type="submit" class="btn text-white" style="background-color: #198754;">
                <i class="bi bi-search"></i> Pesquisar
            </button>
        </div>
    </form>

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
        <small class="text-muted">* Para o serviço **Outros**, o valor é editável manualmente.</small>
    </div>

    <?php
    $counter = 0;
    $orders = [];
    // Busca todos os resultados em um array primeiro para lidar com a lógica "Mostrar Mais" de forma mais limpa
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }

    if (empty($orders)) {
        echo '<div class="alert alert-info text-center" role="alert">Nenhuma Ordem de Serviço em andamento encontrada.</div>';
    } else {
        foreach ($orders as $row):
            $counter++;
            // Define uma classe CSS para ocultar OSs além das duas primeiras, se houver muitas
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
            <p><strong>Última Situação:</strong> <?= htmlspecialchars($row['SituacaoAtual'] ?? 'N/A') ?></p>
            <p><strong>Última Descrição:</strong> <?= htmlspecialchars($row['DescricaoAtual'] ?? 'N/A') ?></p>

            <hr>

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

            <hr>

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

            <hr>

            <button type="button" class="btn btn-danger mt-3" data-bs-toggle="modal" data-bs-target="#confirmModal<?= $row['OS'] ?>">
                Finalizar OS
            </button>

            <div class="modal fade" id="confirmModal<?= $row['OS'] ?>" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="confirmModalLabel">Confirmar Finalização</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                        </div>
                        <div class="modal-body">
                            Tem certeza que deseja finalizar esta OS? Esta ação é irreversível.
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-cancel" data-bs-dismiss="modal">
                                Cancelar
                            </button>
                            <form method="POST">
                                <input type="hidden" name="os_id" value="<?= htmlspecialchars($row['OS']) ?>">
                                <button type="submit" class="btn btn-danger" name="finalizar">Sim, Finalizar</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            </div>
    </div>
    <?php endforeach;
    } // Fim do 'else' para empty($orders) ?>

    <?php if ($counter > 2): // Exibe o botão "Mostrar Mais" se houver mais de 2 OSs ?>
    <div class="text-center mb-4">
        <button id="btnMostrarMais" class="btn btn-outline-success">Mostrar Mais</button>
    </div>
    <?php endif; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Função JavaScript para mostrar/esconder o campo de valor quando o serviço é 'Outros'
    function toggleValorInput(osId) {
        const select = document.getElementById('servico_' + osId);
        const valorDiv = document.getElementById('valorDiv_' + osId);

        if (select.value.toLowerCase() === 'outros') {
            valorDiv.style.display = 'block';
        } else {
            valorDiv.style.display = 'none';
        }
    }

    // Adiciona um listener ao botão "Mostrar Mais" para exibir as OSs ocultas
    document.getElementById('btnMostrarMais')?.addEventListener('click', () => {
        document.querySelectorAll('.extra-os').forEach(el => {
            el.classList.remove('d-none'); // Remove a classe 'd-none' para mostrar as OSs
        });
        document.getElementById('btnMostrarMais').style.display = 'none'; // Esconde o botão "Mostrar Mais"
    });

    // Garante que o input de valor "Outros" seja exibido corretamente ao carregar a página
    // se o serviço atual for 'Outros'
    document.addEventListener('DOMContentLoaded', function() {
        <?php
        // Itera sobre as OSs que foram exibidas para aplicar a lógica inicial do toggleValorInput
        // Usamos a array $orders que já contém todos os resultados
        foreach ($orders as $row):
            if (strtolower(trim($row['Servico'])) == 'outros') {
                echo "toggleValorInput(" . htmlspecialchars($row['OS']) . ");\n";
            }
        endforeach;
        ?>
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

<?php
// Fecha o statement da consulta principal
if (isset($stmt)) {
    $stmt->close();
}
// Fecha a conexão com o banco de dados
$conexao->close();
?>