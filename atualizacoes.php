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
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Histórico de Atualizações - Ordens de Serviço</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
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
</body>
</html>
