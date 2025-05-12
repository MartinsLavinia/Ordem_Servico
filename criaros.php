<?php
session_start();
include 'verificar_sessao.php'; // Inclui a verificação
verificarSessao(); // Verifica se o usuário está autenticado

include 'conexao.php';

class ServiceOrder {
    private $conexao;

    public function __construct($conexao) {
        $this->conexao = $conexao;
    }

    private function generateNumeroOS() {
        $today = date('Ymd');
        $prefix = "OS" . $today;
        $stmt = $this->conexao->prepare("SELECT NumeroOS FROM OS WHERE NumeroOS LIKE ? ORDER BY NumeroOS DESC LIMIT 1");
        $like = $prefix . "%";
        $stmt->bind_param("s", $like);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        if ($result) {
            $lastNumero = $result['NumeroOS'];
            $lastSeq = (int)substr($lastNumero, -3);
            $newSeq = str_pad($lastSeq + 1, 3, "0", STR_PAD_LEFT);
        } else {
            $newSeq = "001";
        }

        return $prefix . $newSeq;
    }

    public function save($data) {
        $numero_os     = $this->generateNumeroOS();
        $date          = $data['date'];
        $equipment     = $data['equipment'];
        $defect        = $data['defect'] === 'Outros' ? $data['defect_other'] : $data['defect'];
        $service       = $data['service'];
        $defect_value  = floatval($data['defect_value']);
        $service_value = floatval($data['service_value']);
        $total_value   = $defect_value + $service_value;
        $client_id     = $data['client_id'];

        // Insere OS diretamente, sem verificar cliente
        $insertOS = $this->conexao->prepare(
            "INSERT INTO OS (NumeroOS, Data, Equipamento, Defeito, Servico, ValorTotal, CodigoCliente)
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        $insertOS->bind_param("ssssdsi", $numero_os, $date, $equipment, $defect, $service, $total_value, $client_id);

        if ($insertOS->execute()) {
            return $numero_os;
        }

        return false;
    }
}

$mensagem = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_SESSION['CodigoCliente'])) {
        $mensagem = "❌ Erro: usuário não está logado.";
    } else {
        $_POST['CodigoCliente'] = $_SESSION['CodigoCliente'];

        $serviceOrder = new ServiceOrder($conexao);
        $numero_os = $serviceOrder->save($_POST);

        if ($numero_os !== false) {
            $mensagem = "✅ Cadastro enviado com sucesso!<br>🧾 Seu número de Ordem de Serviço é: <strong>$numero_os</strong>";
        } else {
            $mensagem = "❌ Erro ao salvar a Ordem de Serviço. Tente novamente.";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Ordem de Serviço</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <style>
    body {
        background-color: #f0f8ff;
        font-family: 'Segoe UI', sans-serif;
    }

    .container {
        margin-top: 40px;
        margin-bottom: 40px;
    }

    .card {
        background-color: #fff;
        border-radius: 12px;
        box-shadow: 0 0 15px rgba(13, 110, 253, 0.1);
        border: none;
    }

    .card-header {
        background-color: #0d6efd;
        color: white;
        font-weight: bold;
        text-align: center;
        padding: 16px 0;
        border-radius: 12px 12px 0 0;
        box-shadow: 0 0 5px rgba(13, 110, 253, 0.4);
    }

    .form-label {
        font-weight: 600;
        color: #0056b3;
    }

    .form-control,
    .form-select {
        background-color: #f8f9fa;
        border: 1px solid #ccc;
        border-radius: 10px;
        padding: 10px 14px;
        box-shadow: 0 0 5px rgba(13, 110, 253, 0.1);
        transition: box-shadow 0.3s ease, border-color 0.3s ease;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #0a58ca !important;
        box-shadow: 0 0 8px rgba(13, 110, 253, 0.6);
        outline: none;
        background-color: #fff;
    }

    .btn-success {
        background-color: #0d6efd;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        padding: 10px 20px;
        box-shadow: 0 4px 12px rgba(13, 110, 253, 0.25);
        transition: background-color 0.3s ease, box-shadow 0.3s ease;
    }

    .btn-success:hover {
        background-color: #0056b3;
        box-shadow: 0 6px 14px rgba(13, 110, 253, 0.4);
    }

    .btn-secondary {
        background-color: #6c757d;
        border: none;
        border-radius: 6px;
        padding: 8px 16px;
        font-weight: 500;
        color: white;
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.15);
    }

    .btn-secondary:hover {
        background-color: #5a6268;
    }

    .form-text {
        font-size: 0.85rem;
        color: #6c757d;
    }

    .alert {
        border-radius: 8px;
        font-size: 0.95rem;
        padding: 12px;
    }

    ::placeholder {
        color: #999;
    }

    input[readonly] {
        background-color: #e9ecef;
    }
</style>

</head>
<body class="bg-light">
    <div class="container mt-5 mb-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white text-center">
                        <h4>📋 Cadastro de Ordem de Serviço</h4>
                    </div>
                    <div class="card-body">
                        <?php if ($mensagem): ?>
                            <div class="alert <?= strpos($mensagem, '✅') !== false ? 'alert-success' : 'alert-danger' ?>">
                                <?= $mensagem ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="mb-3">
                                <label class="form-label">Data:</label>
                                <input type="date" name="date" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Equipamento:</label>
                                <select name="equipment" class="form-select" required>
                                    <option value="">Selecione...</option>
                                    <option value="Celular">Celular</option>
                                    <option value="Computador">Computador</option>
                                    <option value="Notebook">Notebook</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Serviço:</label>
                                <select name="service" id="service" class="form-select" required onchange="atualizaDefeitos()">
                                    <option value="">Selecione...</option>
                                    <option value="Reparo">Reparo</option>
                                    <option value="Troca de peça">Troca de peça</option>
                                    <option value="Limpeza">Limpeza</option>
                                    <option value="Instalação">Instalação</option>
                                    <option value="Outros">Outros</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Defeito:</label>
                                <select name="defect" id="defect" class="form-select" required onchange="atualizaValorTotal()">
                                    <option value="">Selecione...</option>
                                </select>
                            </div>

                            <div class="mb-3" id="outroDefeitoBox" style="display:none;">
                                <label class="form-label">Descreva o defeito:</label>
                                <input type="text" name="defect_other" id="defect_other" class="form-control">
                                <div class="form-text">💡 Valor aproximado. Pode mudar após avaliação do técnico.</div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Valor Total (R$):</label>
                                <input type="number" step="0.01" name="total_value" id="total_value" class="form-control" readonly required>
                            </div>

                            <input type="hidden" name="service_value" id="service_value">
                            <input type="hidden" name="defect_value" id="defect_value">

                            <div class="mb-3">
                                <label class="form-label">Nome do Cliente:</label>
                                <input type="text" name="client_name" class="form-control" required>
                            </div>

                            <div class="text-end">
                                <button type="submit" class="btn btn-success">💾 Salvar Ordem de Serviço</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
                <!-- Link de voltar para consulta com seta para a direita -->
                <a href="consulta.php" class="btn btn-secondary mb-3 float-end">
                    Ir para Consulta <i class="bi bi-arrow-right"></i>
                </a>
            </div>
    </div>

    <script>
    const defeitosPorServico = {
        "Reparo": {
            "Tela quebrada": 250.00,
            "Não liga": 300.00,
            "Lento": 180.00,
            "Superaquecendo": 200.00,
            "Travando": 170.00,
            "Sem som": 150.00,
            "Sem imagem": 160.00
        },
        "Troca de peça": {
            "Tela quebrada": 350.00,
            "Não liga": 400.00,
            "Lento": 250.00
        },
        "Limpeza": {
            "Lento": 100.00,
            "Travando": 120.00
        },
        "Instalação": {
            "Instalação de software": 80.00,
            "Instalação de hardware": 100.00
        },
        "Outros": {
            "Outros": 50.00
        }
    };

    function atualizaDefeitos() {
        const serviceSelect = document.getElementById("service");
        const defectSelect = document.getElementById("defect");
        const outroBox = document.getElementById("outroDefeitoBox");

        defectSelect.innerHTML = '<option value="">Selecione...</option>';
        const defeitos = defeitosPorServico[serviceSelect.value] || {};

        if (serviceSelect.value === "Outros") {
            outroBox.style.display = "block";
        } else {
            outroBox.style.display = "none";
        }

        for (const [defeito, valor] of Object.entries(defeitos)) {
            const option = document.createElement("option");
            option.value = defeito;
            option.text = defeito;
            defectSelect.appendChild(option);
        }

        atualizaValorTotal();
    }

    function atualizaValorTotal() {
        const serviceSelect = document.getElementById("service");
        const defectSelect = document.getElementById("defect");

        const defectValue = defeitosPorServico[serviceSelect.value]?.[defectSelect.value] || 0;
        const serviceValue = {
            "Reparo": 100,
            "Troca de peça": 150,
            "Limpeza": 80,
            "Instalação": 120,
            "Outros": 50
        }[serviceSelect.value] || 50;

        document.getElementById("total_value").value = (defectValue + serviceValue).toFixed(2);
        document.getElementById("service_value").value = serviceValue;
        document.getElementById("defect_value").value = defectValue;
    }

    document.addEventListener("DOMContentLoaded", () => {
        atualizaDefeitos();
    });
    </script>
    </div>
    

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
