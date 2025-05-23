<?php
// Inicia a sessão
session_start();

// Conexão com o banco de dados
include("conexao.php");

// Verifica se o parâmetro de exclusão foi enviado
if (isset($_GET['excluir'])) {
    $os_id = $_GET['excluir'];

    // Primeiro, verifica se a OS está finalizada
    $checkStatus = "SELECT Status FROM os WHERE OS = ?";
    $stmt = $conexao->prepare($checkStatus);
    $stmt->bind_param("i", $os_id);
    $stmt->execute();
    $stmt->bind_result($status);
    $stmt->fetch();
    $stmt->free_result(); // Libera o resultado para a próxima consulta
    
    if ($status == 'Finalizada') {
        // Excluir as atualizações relacionadas à OS
        $deleteAndamento = "DELETE FROM andamentoos WHERE OS = ?";
        $stmt = $conexao->prepare($deleteAndamento);
        $stmt->bind_param("i", $os_id);
        $stmt->execute();
        $stmt->free_result(); // Libera o resultado para a próxima consulta
        
        // Excluir a própria OS
        $deleteOS = "DELETE FROM os WHERE OS = ?";
        $stmt = $conexao->prepare($deleteOS);
        $stmt->bind_param("i", $os_id);
        $stmt->execute();
        
        echo "<div class='alert alert-success text-center'>Ordem de serviço excluída com sucesso!</div>";
    } else {
        echo "<div class='alert alert-danger text-center'>Não é possível excluir uma OS que não está finalizada.</div>";
    }
}

// Consulta para buscar as ordens de serviço e seus respectivos históricos de atualizações
$sql = "SELECT os.OS, os.NumeroOS, os.Equipamento, os.Defeito, os.Servico, os.ValorTotal, andamentoos.Situacao, andamentoos.Descricao, andamentoos.DataAtualizacao, os.Status
        FROM os
        LEFT JOIN andamentoos ON os.OS = andamentoos.OS
        ORDER BY os.OS DESC";

$result = $conexao->query($sql);

// Verifica se o usuário está logado
if (!isset($_SESSION['CodigoCliente'])) {
    header("Location: login-usuario.php");
    exit();
}

// Pega o ID do cliente da sessão
$id_cliente = $_SESSION['CodigoCliente'];

// Consulta o nome do cliente no banco
$stmt = $conexao->prepare("SELECT NomeCliente FROM cliente WHERE CodigoCliente = ?");
$stmt->bind_param("i", $id_cliente);
$stmt->execute();
$stmt->bind_result($nome_cliente);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Histórico de Atualizações - Ordens de Serviço</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
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

<header class=" top-0 w-100 shadow-sm" style="z-index: 1030; height: 80px;">
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

<div class="container mt-5">
    <h2 class="mb-4 text-center">Histórico de Atualizações - Ordens de Serviço</h2>

    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">
                    <strong>OS Nº: <?= htmlspecialchars($row['NumeroOS']) ?> - <?= htmlspecialchars($row['Equipamento']) ?></strong>
                </div>
                <div class="card-body">
                    <p><strong>Defeito:</strong> <?= htmlspecialchars($row['Defeito']) ?></p>
                    <p><strong>Serviço:</strong> <?= htmlspecialchars($row['Servico']) ?></p>
                    <p><strong>Valor Total:</strong> R$ <?= number_format($row['ValorTotal'], 2, ',', '.') ?></p>

                    <!-- Exibe o histórico de atualizações -->
                    <h5 class="mt-4">Histórico de Atualizações:</h5>
                    <ul class="list-group">
                        <?php
                        // Consulta o histórico de atualizações para esta OS
                        $hist_sql = "SELECT Situacao, Descricao, DataAtualizacao FROM andamentoos WHERE OS = ? ORDER BY DataAtualizacao DESC";
                        $hist_stmt = $conexao->prepare($hist_sql);
                        $hist_stmt->bind_param("i", $row['OS']);
                        $hist_stmt->execute();
                        $hist_result = $hist_stmt->get_result();

                        if ($hist_result->num_rows > 0):
                            while ($hist_row = $hist_result->fetch_assoc()):
                        ?>
                                <li class="list-group-item">
                                    <strong><?= date('d/m/Y H:i', strtotime($hist_row['DataAtualizacao'])) ?></strong><br>
                                    <strong>Situação:</strong> <?= htmlspecialchars($hist_row['Situacao']) ?><br>
                                    <strong>Descrição:</strong> <?= nl2br(htmlspecialchars($hist_row['Descricao'])) ?>
                                </li>
                        <?php endwhile; else: ?>
                            <li class="list-group-item text-muted">Nenhuma atualização registrada ainda.</li>
                        <?php endif; ?>
                    </ul>

                    <!-- Se a OS estiver finalizada, exibe a opção de excluir -->
                    <?php if ($row['Status'] == 'Finalizada'): ?>
                        <form method="get" class="mt-3">
                            <input type="hidden" name="excluir" value="<?= $row['OS'] ?>">
                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-trash"></i> Excluir Ordem de Serviço
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p class="alert alert-warning">Nenhuma ordem de serviço encontrada.</p>
    <?php endif; ?>

</div>

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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
