<?php

include 'conexao.php';

// Excluir OS se solicitado
function excluirOS($numero_os) {
    global $conexao;
    $stmt = $conexao->prepare("DELETE FROM OS WHERE NumeroOS = ?");
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

// Preparar consulta com filtros, sem o campo Servico
$sql = "SELECT OS.NumeroOS, OS.Data, OS.Equipamento, OS.Defeito, OS.ValorTotal, CLIENTE.NomeCliente 
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

$stmt = $conexao->prepare($sql);
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
    <link href="style.css" rel="stylesheet">
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

        button.btn-info {
            background: transparent;
            border: none;
            padding: 0;
            display: inline-flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
        }

        button.btn-info img.icon-btn {
            display: block;
            width: 40px;
            height: 40px;
            transition: transform 0.2s ease-in-out;
        }

        button.btn-info:hover img.icon-btn {
            transform: scale(1.2);
        }

        button.btn-info:hover {
            background: transparent;
            border: none;
        }
    </style>
</head>
<body>

<header class="top-0 w-100 shadow-sm" style="z-index: 1030; height: 80px;">
  <div class="bg-white bg-opacity-75 px-4 py-3 d-flex justify-content-between align-items-center" style="backdrop-filter: blur(10px);">
    <a href="index.php" class="text-decoration-none text-primary fs-4 fw-bold">
      🔧 Ordem de Serviço
    </a>
    <nav class="d-flex align-items-center">
      <a href="criaros.php" class="nav-link text-primary mx-3 fw-semibold link-hover-blue">Cadastrar OS</a>
      <a href="consulta.php" class="nav-link text-primary mx-3 fw-semibold link-hover-blue">Consultar OS</a>
      <a href="atualizacoes.php" class="nav-link text-primary mx-3 fw-semibold link-hover-blue">Atualizações</a>
      <a href="logout.php" class="nav-link text-danger mx-3 fw-semibold link-hover-red">Logout</a>
    </nav>
  </div>
</header>

<div class="container mt-4">
    <h2 class="text-center">Consulta de Ordens de Serviço</h2>

    <form method="GET" class="row g-3 my-4 p-4 rounded shadow-sm custom-form-box">
        <div class="col-md-4">
            <label for="numero_os" class="form-label fw-semibold">Número da OS</label>
            <input type="text" id="numero_os" name="numero_os" class="form-control rounded-pill" placeholder="Digite o número da OS" value="<?= htmlspecialchars($_GET['numero_os'] ?? '') ?>">
        </div>
        <div class="col-md-4">
            <label for="cliente_nome" class="form-label fw-semibold">Nome do Cliente</label>
            <input type="text" id="cliente_nome" name="cliente_nome" class="form-control rounded-pill" placeholder="Digite o nome do cliente" value="<?= htmlspecialchars($_GET['cliente_nome'] ?? '') ?>">
        </div>
        <div class="col-md-4 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100 rounded-pill py-2 fw-bold">🔍 Buscar</button>
        </div>
    </form>

    <?php if ($result->num_rows > 0): ?>
    <div class="table-responsive custom-table-box mt-4">
        <table class="table table-bordered table-hover align-middle rounded-3 overflow-hidden">
            <thead class="table-light text-center">
                <tr>
                    <th>Número OS</th>
                    <th>Data</th>
                    <th>Equipamento</th>
                    <th>Defeito</th>
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
                    <td>R$ <?= number_format($row['ValorTotal'], 2, ',', '.') ?></td>
                    <td><?= $row['NomeCliente'] ?></td>
                    <td class="text-center">
                        <a href="alterar.php?numero_os=<?= $row['NumeroOS'] ?>" class="btn btn-sm btn-warning me-1">✏️ Alterar</a>
                        <a href="?excluir=1&numero_os=<?= $row['NumeroOS'] ?>" 
                           class="btn btn-sm btn-danger me-1" 
                           onclick="return confirm('Tem certeza que deseja excluir esta ordem de serviço?')">🗑️ Excluir</a>
                        <button onclick="imprimir('<?= $row['NumeroOS'] ?>')" class="btn btn-sm btn-info">
                            <img src="icon-imprimir.png" alt="Imprimir" class="icon-btn">
                        </button>
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

<br><br><br>

<footer class="text-white pt-5 pb-4" style="background: linear-gradient(rgba(0,0,0,0.85), rgba(0,0,0,0.85)), url('engrenagens.jpg') center center / cover no-repeat;">
  <div class="container text-md-left">
    <div class="row text-center text-md-start">

      <div class="col-md-4 col-lg-4 col-xl-4 mx-auto mb-4">
        <h5 class="text-uppercase fw-bold text-primary mb-3">🔧 Ordem de Serviço</h5>
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
