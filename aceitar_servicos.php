<?php
include("conexao.php");
include('verifica_login.php');

// Verifica login e busca nome do usuário antes do HTML
if (!isset($_SESSION['colaborador']) || !isset($_SESSION['colaborador']['codigo'])) {
    header("Location: login.php");
    exit;
}

$codigoColaborador = $_SESSION['colaborador']['codigo'];

$stmt = $conexao->prepare("SELECT NomeColaborador FROM colaborador WHERE CodigoColaborador = ?");
$stmt->bind_param("i", $codigoColaborador);
$stmt->execute();
$stmt->bind_result($nome_colaborador);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Serviços Pendentes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="style-adm.css" rel="stylesheet">
    <style>
        .card-body {
            margin-bottom: 35px;
        }

        .custom-table-box {
            background-color: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        }

        .table {
            border-collapse: separate;
            border-spacing: 0;
            border-radius: 10px;
            overflow: hidden;
        }

        .table th {
            background-color: #f8f9fa;
            color: #333;
            font-weight: 600;
        }

        .table td, .table th {
            vertical-align: middle;
        }

        .table tbody tr:hover {
            background-color: #f1f3f5;
        }

        .table .btn {
            border-radius: 50px;
            font-size: 0.9rem;
            padding: 5px 10px;
            transition: 0.2s ease-in-out;
        }

        .table .btn:hover {
            opacity: 0.9;
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


<?php
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

<div class="container mt-5">
    <h2 class="mb-4 text-center fw-bold" style="color: #198754;">Ordens de Serviço Pendentes</h2>

    <?php if ($result->num_rows > 0): ?>
        <div class="table-responsive shadow-sm rounded-3 p-2 custom-table-box">
            <table class="table table-bordered table-hover align-middle rounded-3 overflow-hidden">
                <thead class="table-light text-center">
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
                            <a href="?aceitar=<?= $row['OS'] ?>" class="btn btn-success btn-sm rounded-pill fw-semibold px-3">
                                ✔️ Aceitar Serviço
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-warning text-center mt-4 rounded-3 shadow-sm">
            ❌ Nenhuma ordem de serviço pendente no momento.
        </div>
    <?php endif; ?>

</div>
<br><br><br><br>

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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
