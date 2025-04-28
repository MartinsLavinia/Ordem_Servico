<?php
include 'conexao.php';

if (isset($_GET['numero_os'])) {
    $numero_os = $_GET['numero_os'];

    // Buscar dados da OS
    $stmt = $connection->prepare("SELECT * FROM OS WHERE NumeroOS = ?");
    $stmt->bind_param("s", $numero_os);
    $stmt->execute();
    $result = $stmt->get_result();
    $osData = $result->fetch_assoc();
}

// Atualizar a ordem de serviço
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $numero_os = $_POST['numero_os'];
    $data = $_POST['data'];
    $equipamento = $_POST['equipamento'];
    $defeito = $_POST['defeito'];
    $servico = $_POST['servico'];
    $valor_total = $_POST['valor_total'];

    $stmt = $connection->prepare(
        "UPDATE OS SET Data = ?, Equipamento = ?, Defeito = ?, Servico = ?, ValorTotal = ? WHERE NumeroOS = ?"
    );
    $stmt->bind_param("ssssds", $data, $equipamento, $defeito, $servico, $valor_total, $numero_os);

    if ($stmt->execute()) {
        echo "Ordem de serviço atualizada com sucesso!";
    } else {
        echo "Erro ao atualizar a ordem de serviço.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Alterar Ordem de Serviço</title>
</head>
<body>
    <h1>Alterar Ordem de Serviço</h1>
    <form method="POST">
        <input type="hidden" name="numero_os" value="<?= $osData['NumeroOS'] ?>">

        <label>Data:</label><br>
        <input type="date" name="data" value="<?= $osData['Data'] ?>" required><br>

        <label>Equipamento:</label><br>
        <input type="text" name="equipamento" value="<?= $osData['Equipamento'] ?>" required><br>

        <label>Defeito:</label><br>
        <input type="text" name="defeito" value="<?= $osData['Defeito'] ?>" required><br>

        <label>Serviço:</label><br>
        <input type="text" name="servico" value="<?= $osData['Servico'] ?>" required><br>

        <label>Valor Total (R$):</label><br>
        <input type="number" name="valor_total" value="<?= $osData['ValorTotal'] ?>" required><br>

        <input type="submit" value="Salvar Alterações">
    </form>
</body>
</html>
