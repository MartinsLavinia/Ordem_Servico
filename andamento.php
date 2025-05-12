<?php
include 'verificar_sessao.php'; // Inclui a verificação
verificarSessao(); // Verifica se o usuário está autenticado

include 'conexao.php';

$id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $valor_outros = $_POST['valor_outros'];
    $situacao = $_POST['situacao'];
    $andamento_id = $_POST['andamento_id'];

    $conexao->query("
        UPDATE ordens_servico 
        SET valor_outros = '$valor_outros', 
            situacao = '$situacao', 
            andamento_id = '$andamento_id' 
        WHERE id = $id
    ");

    // Atualiza dataAtualizacao na tabela andamento
    $conexao->query("UPDATE andamento SET dataAtualizacao = NOW() WHERE id = $andamento_id");

    header("Location: listar_os.php");
    exit;
}

$os = $conexao->query("SELECT * FROM ordens_servico WHERE id = $id")->fetch_assoc();
$andamentos = $conexao->query("SELECT * FROM andamento WHERE situacao = 'Ativo'");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Gerenciar OS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <h3>Gerenciar OS #<?= $os['id'] ?></h3>
    <form method="post">
        <div class="mb-3">
            <label class="form-label">Valor (Outros)</label>
            <input type="number" step="0.01" name="valor_outros" class="form-control" value="<?= $os['valor_outros'] ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Situação</label>
            <select name="situacao" class="form-select">
                <option <?= $os['situacao'] == 'Em aberto' ? 'selected' : '' ?>>Em aberto</option>
                <option <?= $os['situacao'] == 'Em andamento' ? 'selected' : '' ?>>Em andamento</option>
                <option <?= $os['situacao'] == 'Concluída' ? 'selected' : '' ?>>Concluída</option>
                <option <?= $os['situacao'] == 'Cancelada' ? 'selected' : '' ?>>Cancelada</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Andamento</label>
            <select name="andamento_id" class="form-select">
                <?php while ($a = $andamentos->fetch_assoc()): ?>
                    <option value="<?= $a['id'] ?>" <?= $os['andamento_id'] == $a['id'] ? 'selected' : '' ?>>
                        <?= $a['descricao'] ?> (última atualização: <?= date('d/m/Y H:i', strtotime($a['dataAtualizacao'])) ?>)
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Salvar</button>
        <a href="listar_os.php" class="btn btn-secondary">Voltar</a>
    </form>
</body>
</html>
