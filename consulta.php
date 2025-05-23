<?php
session_start();
include 'conexao.php';

// Verifica se está logado
if (!isset($_SESSION['CodigoCliente'])) {
    echo "<div class='alert alert-danger text-center'>Você precisa estar logado para acessar esta página.</div>";
    exit;
}

$codigoClienteLogado = $_SESSION['CodigoCliente'];

// Função para excluir OS garantindo que pertence ao usuário logado
function excluirOS($numero_os, $codigoCliente) {
    global $conexao;
    $stmt = $conexao->prepare("DELETE FROM OS WHERE NumeroOS = ? AND CodigoCliente = ?");
    $stmt->bind_param("si", $numero_os, $codigoCliente);
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo "<div class='alert alert-success'>✅ Ordem de serviço excluída com sucesso!</div>";
        } else {
            echo "<div class='alert alert-danger'>❌ Ordem de serviço não encontrada ou você não tem permissão para excluir.</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>❌ Erro ao excluir a ordem de serviço.</div>";
    }
}

if (isset($_GET['excluir']) && isset($_GET['numero_os'])) {
    excluirOS($_GET['numero_os'], $codigoClienteLogado);
}

// Consulta OSs com filtros e restrição por cliente
$sql = "SELECT OS.NumeroOS, OS.Data, OS.Equipamento, OS.Defeito, OS.Servico, OS.ValorTotal, CLIENTE.NomeCliente 
        FROM OS
        JOIN CLIENTE ON OS.CodigoCliente = CLIENTE.CodigoCliente
        WHERE OS.CodigoCliente = ?";

$params = [$codigoClienteLogado];
$types = "i";

if (!empty($_GET['numero_os'])) {
    $sql .= " AND OS.NumeroOS LIKE ?";
    $params[] = "%" . $_GET['numero_os'] . "%";
    $types .= "s";
}
if (!empty($_GET['cliente_nome'])) {
    $sql .= " AND CLIENTE.NomeCliente LIKE ?";
    $params[] = "%" . $_GET['cliente_nome'] . "%";
    $types .= "s";
}
if (!empty($_GET['servico'])) {
    $sql .= " AND OS.Servico LIKE ?";
    $params[] = "%" . $_GET['servico'] . "%";
    $types .= "s";
}

$stmt = $conexao->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

// Consulta o nome do cliente logado
$stmt = $conexao->prepare("SELECT NomeCliente FROM cliente WHERE CodigoCliente = ?");
$stmt->bind_param("i", $codigoClienteLogado);
$stmt->execute();
$stmt->bind_result($nome_cliente);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Consulta de Ordens de Serviço</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <style>
        /* Seu CSS existente... */
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

        .link-hover-blue::after,
        .link-hover-red::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        height: 2px;
        width: 0;
        transition: width 0.3s ease;
        }
        .link-hover-blue:hover::after {
        width: 100%;
        background-color: #0d6efd;
        }
        .link-hover-red:hover::after {
        width: 100%;
        background-color: red;
        }
        nav a {
        position: relative;
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
      <div class="dropdown">
        <a class="nav-link dropdown-toggle text-dark fw-semibold" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fas fa-user-circle me-2"></i><?= htmlspecialchars($nome_cliente) ?>
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

<div class="container mt-4">
    <h2 class="text-center">Consulta de Ordens de Serviço</h2>

    <form method="GET" class="row g-3 my-4 p-4 rounded shadow-sm custom-form-box">
        <div class="col-md-3">
            <label for="numero_os" class="form-label fw-semibold">Número da OS</label>
            <input type="text" id="numero_os" name="numero_os" class="form-control rounded-pill" placeholder="Digite o número da OS" value="<?= htmlspecialchars($_GET['numero_os'] ?? '') ?>">
        </div>
        <div class="col-md-3">
            <label for="cliente_nome" class="form-label fw-semibold">Nome do Cliente</label>
            <input type="text" id="cliente_nome" name="cliente_nome" class="form-control rounded-pill" placeholder="Digite o nome do cliente" value="<?= htmlspecialchars($_GET['cliente_nome'] ?? '') ?>">
        </div>
        <div class="col-md-3">
            <label for="servico" class="form-label fw-semibold">Serviço</label>
            <input type="text" id="servico" name="servico" class="form-control rounded-pill" placeholder="Digite o serviço" value="<?= htmlspecialchars($_GET['servico'] ?? '') ?>">
        </div>
        <div class="col-md-3 d-flex align-items-end">
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
                    <th>Serviço</th>
                    <th>Valor Total</th>
                    <th>Cliente</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['NumeroOS']) ?></td>
                    <td><?= htmlspecialchars($row['Data']) ?></td>
                    <td><?= htmlspecialchars($row['Equipamento']) ?></td>
                    <td><?= htmlspecialchars($row['Defeito']) ?></td>
                    <td><?= htmlspecialchars($row['Servico']) ?></td>
                    <td>R$ <?= number_format($row['ValorTotal'], 2, ',', '.') ?></td>
                    <td><?= htmlspecialchars($row['NomeCliente']) ?></td>
                    <td class="text-center">
                        <a href="alterar.php?numero_os=<?= urlencode($row['NumeroOS']) ?>" class="btn btn-sm btn-warning me-1">✏️ Alterar</a>
                        <a href="?excluir=1&numero_os=<?= urlencode($row['NumeroOS']) ?>" 
                           class="btn btn-sm btn-danger me-1" 
                           onclick="return confirm('Tem certeza que deseja excluir esta ordem de serviço?')">🗑️ Excluir</a>
                        <button onclick="imprimir('<?= htmlspecialchars($row['NumeroOS']) ?>')" class="btn btn-sm btn-info" title="Imprimir OS">
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
    window.open('imprimir_os.php?numero_os=' + encodeURIComponent(numero_os), '_blank');
}
</script>

<br><br><br>

<footer class="text-white pt-5 pb-4" style="background: linear-gradient(rgba(0,0,0,0.85), rgba(0,0,0,0.85)), url('engrenagens.jpg') center center / cover no-repeat;">
  <div class="container text-md-left">
    <div class="row text-center text-md-start">

      <div class="col-md-4 col-lg-4 col-xl-3 mx-auto mt-3">
        <h5 class="text-uppercase mb-4 fw-bold">Contato</h5>
        <p><i class="fas fa-home me-3"></i> Rua dos Engrenagens, 123</p>
        <p><i class="fas fa-envelope me-3"></i> contato@empresa.com</p>
        <p><i class="fas fa-phone me-3"></i> +55 11 99999-9999</p>
      </div>

      <div class="col-md-4 col-lg-4 col-xl-3 mx-auto mt-3">
        <h5 class="text-uppercase mb-4 fw-bold">Sobre Nós</h5>
        <p>Somos uma empresa dedicada a fornecer os melhores serviços técnicos para você.</p>
      </div>

      <div class="col-md-4 col-lg-4 col-xl-3 mx-auto mt-3">
        <h5 class="text-uppercase mb-4 fw-bold">Redes Sociais</h5>
        <p>
          <a href="#" class="text-white me-4"><i class="fab fa-facebook-f"></i></a>
          <a href="#" class="text-white me-4"><i class="fab fa-twitter"></i></a>
          <a href="#" class="text-white me-4"><i class="fab fa-instagram"></i></a>
          <a href="#" class="text-white me-4"><i class="fab fa-linkedin-in"></i></a>
        </p>
      </div>

    </div>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
