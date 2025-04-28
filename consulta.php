<?php
include 'conexao.php'; // Inclua a conexão com o banco de dados

// Função para excluir a ordem de serviço
function excluirOS($numero_os) {
    global $connection;

    // Deletar a ordem de serviço
    $stmt = $connection->prepare("DELETE FROM OS WHERE NumeroOS = ?");
    $stmt->bind_param("s", $numero_os);
    if ($stmt->execute()) {
        echo "<p>✅ Ordem de serviço excluída com sucesso!</p>";
    } else {
        echo "<p>❌ Erro ao excluir a ordem de serviço.</p>";
    }
}

// Verificar se a ação de excluir foi chamada
if (isset($_GET['excluir']) && isset($_GET['numero_os'])) {
    excluirOS($_GET['numero_os']);
}

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Consulta de Ordem de Serviço</title>
</head>
<body>
    <h1>Consulta de Ordem de Serviço</h1>

    <!-- Formulário para buscar a OS -->
    <form method="GET">
        <label for="numero_os">Número da OS:</label>
        <input type="text" name="numero_os" id="numero_os" placeholder="Buscar por número da OS">
        
        <label for="cliente_nome">Nome do Cliente:</label>
        <input type="text" name="cliente_nome" id="cliente_nome" placeholder="Buscar por nome do cliente">

        <input type="submit" value="Procurar">
    </form>

    <h2>Resultados da Consulta:</h2>

    <?php
    // Preparar a consulta SQL
    $sql = "SELECT OS.NumeroOS, OS.Data, OS.Equipamento, OS.Defeito, OS.Servico, OS.ValorTotal, CLIENTE.NomeCliente 
            FROM OS
            JOIN CLIENTE ON OS.CodigoCliente = CLIENTE.CodigoCliente
            WHERE 1";

    // Adicionar condições de filtro com base nos parâmetros
    $params = [];
    if (isset($_GET['numero_os']) && !empty($_GET['numero_os'])) {
        $sql .= " AND OS.NumeroOS LIKE ?";
        $params[] = "%" . $_GET['numero_os'] . "%";
    }

    if (isset($_GET['cliente_nome']) && !empty($_GET['cliente_nome'])) {
        $sql .= " AND CLIENTE.NomeCliente LIKE ?";
        $params[] = "%" . $_GET['cliente_nome'] . "%";
    }

    $stmt = $connection->prepare($sql);

    // Verificar se há parâmetros e vincular
    if (!empty($params)) {
        $types = str_repeat('s', count($params));
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    // Verificar se há resultados
    if ($result->num_rows > 0) {
        echo "<table border='1'>
                <tr>
                    <th>Número OS</th>
                    <th>Data</th>
                    <th>Equipamento</th>
                    <th>Defeito</th>
                    <th>Serviço</th>
                    <th>Valor Total</th>
                    <th>Nome Cliente</th>
                    <th>Ações</th>
                </tr>";

        // Exibir os resultados na tabela
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>" . $row['NumeroOS'] . "</td>
                    <td>" . $row['Data'] . "</td>
                    <td>" . $row['Equipamento'] . "</td>
                    <td>" . $row['Defeito'] . "</td>
                    <td>" . $row['Servico'] . "</td>
                    <td>" . $row['ValorTotal'] . "</td>
                    <td>" . $row['NomeCliente'] . "</td>
                    <td>
                        <a href='alterar.php?numero_os=" . $row['NumeroOS'] . "'>Alterar</a> | 
                        <a href='?excluir=1&numero_os=" . $row['NumeroOS'] . "' onclick='return confirm(\"Você tem certeza que deseja excluir esta ordem de serviço?\")'>Excluir</a> | 
                        <a href='javascript:imprimir(\"" . $row['NumeroOS'] . "\")'>Imprimir</a>
                    </td>
                </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>❌ Nenhuma ordem de serviço encontrada com os critérios informados.</p>";
    }
    ?>

    <script>
    function imprimir(numero_os) {
        window.open('imprimir_os.php?numero_os=' + numero_os, '_blank');
    }
    </script>
</body>
</html>
