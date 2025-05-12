<?php

include 'conexao.php';

if (!isset($_GET['numero_os'])) {
    echo "Número da OS não fornecido.";
    exit;
}

$numero_os = $_GET['numero_os'];

// Consulta a OS no banco de dados
$stmt = $conexao->prepare("
    SELECT OS.*, CLIENTE.NomeCliente 
    FROM OS 
    JOIN CLIENTE ON OS.CodigoCliente = CLIENTE.CodigoCliente 
    WHERE NumeroOS = ?
");
$stmt->bind_param("s", $numero_os);
$stmt->execute();
$result = $stmt->get_result();
$os = $result->fetch_assoc();

if (!$os) {
    echo "Ordem de serviço não encontrada.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Imprimir OS <?= htmlspecialchars($numero_os) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
        }
        body {
            padding: 2rem;
        }
        .linha {
            border-top: 1px dashed #999;
            margin: 1.5rem 0;
        }
    </style>
</head>
<body onload="window.print()">
<div class="container border p-4 shadow-sm bg-white">
    <h1 class="text-center">Ordem de Serviço</h1>
    <h4 class="text-center text-muted">Nº <?= htmlspecialchars($os['NumeroOS']) ?></h4>

    <div class="linha"></div>

    <div class="row mb-2">
        <div class="col-md-6"><strong>Data:</strong> <?= date('d/m/Y', strtotime($os['Data'])) ?></div>
        <div class="col-md-6"><strong>Cliente:</strong> <?= htmlspecialchars($os['NomeCliente']) ?></div>
    </div>
    <div class="row mb-2">
        <div class="col-md-6"><strong>Equipamento:</strong> <?= htmlspecialchars($os['Equipamento']) ?></div>
        <div class="col-md-6"><strong>Defeito:</strong> <?= htmlspecialchars($os['Defeito']) ?></div>
    </div>
    <div class="row mb-2">
        <div class="col-md-6"><strong>Serviço:</strong> <?= htmlspecialchars($os['Servico']) ?></div>
        <div class="col-md-6"><strong>Valor Total:</strong> R$ <?= number_format($os['ValorTotal'], 2, ',', '.') ?></div>
    </div>

    <div class="linha"></div>

    <p class="text-center text-muted small">
        Emitido em <?= date('d/m/Y H:i') ?> — Sistema de OS
    </p>

</div>
<div class="text-center mt-4 no-print">
        <a href="consulta.php" class="btn btn-secondary">🔙 Voltar para Consulta</a>
    </div>
</body>
</html>
