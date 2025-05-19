<?php
session_start();
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
        $numero_os = $this->generateNumeroOS();
        $date = $data['date'];
        $equipment = $data['equipment'];
        
        $defect = $data['defect'] === 'Outros' ? $data['defect_other'] : $data['defect'];

        $service = $data['service'];
        $service_value = floatval($data['service_value']);
        $defect_value = floatval($data['defect_value']);

        if ($data['defect'] === 'Outros' && isset($data['defect_value_other'])) {
            $defect_value = floatval($data['defect_value_other']);
        }

        $total_value = $service_value + $defect_value;
        
        $client_id = $data['client_id'];

        $insertOS = $this->conexao->prepare(
            "INSERT INTO OS (NumeroOS, Data, Equipamento, Defeito, Servico, ValorTotal, CodigoCliente)
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        
        $insertOS->bind_param("ssssssi", $numero_os, $date, $equipment, $defect, $service, $total_value, $client_id);

        if ($insertOS->execute()) {
            return $numero_os;
        }

        return false;
    }
    
    // Função para buscar os detalhes da OS
    public function getServiceOrder($numero_os) {
        $stmt = $this->conexao->prepare("SELECT * FROM OS WHERE NumeroOS = ?");
        $stmt->bind_param("s", $numero_os);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}

$mensagem = null;
$os_details = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_SESSION['CodigoCliente'])) {
        $mensagem = "❌ Erro: usuário não está logado.";
    } else {
        $_POST['client_id'] = $_SESSION['CodigoCliente'];

        $serviceOrder = new ServiceOrder($conexao);
        $numero_os = $serviceOrder->save($_POST);

        if ($numero_os !== false) {
            $mensagem = "✅ Cadastro enviado com sucesso!<br>🧾 Seu número de Ordem de Serviço é: <strong>$numero_os</strong>";
        } else {
            $mensagem = "❌ Erro ao salvar a Ordem de Serviço. Tente novamente.";
        }
    }
} elseif (isset($_GET['numero_os'])) {
    // Caso queira consultar uma ordem de serviço, passamos o número via URL (?numero_os=OS123)
    $numero_os = $_GET['numero_os'];
    $serviceOrder = new ServiceOrder($conexao);
    $os_details = $serviceOrder->getServiceOrder($numero_os);
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Ordem de Serviço</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
    const serviceValueDisplay = document.getElementById("service_value_display");

    defectSelect.innerHTML = '<option value="">Selecione...</option>';
    const defeitos = defeitosPorServico[serviceSelect.value] || {};

    if (serviceSelect.value === "Outros") {
        outroBox.style.display = "block";
    } else {
        outroBox.style.display = "none";
    }

    for (const defeito in defeitos) {
        const option = document.createElement("option");
        option.value = defeito;
        option.text = defeito;
        defectSelect.appendChild(option);
    }

    // Atualiza display do valor do serviço
    if (serviceSelect.value) {
        const valorServico = valoresServicos[serviceSelect.value] || 0;
        serviceValueDisplay.innerText = `R$ ${valorServico.toFixed(2)}`;
    } else {
        serviceValueDisplay.innerText = "R$ 0.00";
    }

    atualizaValorTotal();
}

function atualizaValorTotal() {
    const serviceSelect = document.getElementById("service");
    const defectSelect = document.getElementById("defect");
    const defectValueOther = document.getElementById("defect_value_other").value;

    let serviceValue = 0;
    let defectValue = 0;

    if (serviceSelect.value) {
        serviceValue = valoresServicos[serviceSelect.value] || 0;
    }

    if (serviceSelect.value === "Outros" && defectValueOther) {
        defectValue = parseFloat(defectValueOther);
    } else if (defectSelect.value && defeitosPorServico[serviceSelect.value]) {
        defectValue = defeitosPorServico[serviceSelect.value][defectSelect.value] || 0;
    }

    const totalValue = (serviceValue + defectValue).toFixed(2);

    document.getElementById("total_value").value = totalValue;
    document.getElementById("service_value").value = serviceValue;
    document.getElementById("defect_value").value = defectValue;
}

function preencherFormulario() {
    const osDetails = <?php echo json_encode($os_details); ?>;

    if (osDetails) {
        document.getElementById("service").value = osDetails.Servico;
        atualizaDefeitos();

        document.getElementById("defect").value = osDetails.Defeito;

        document.getElementById("service_value").value = parseFloat(osDetails.ValorServico).toFixed(2);
        document.getElementById("defect_value").value = parseFloat(osDetails.ValorDefeito).toFixed(2);
        document.getElementById("total_value").value = parseFloat(osDetails.ValorTotal).toFixed(2);

        if (osDetails.Defeito === "Outros") {
            document.getElementById("outroDefeitoBox").style.display = "block";
            document.getElementById("defect_other").value = osDetails.Defeito;
            document.getElementById("defect_value_other").value = parseFloat(osDetails.ValorDefeito).toFixed(2);
        }

        document.getElementById("equipment").value = osDetails.Equipamento;
    }
}

document.addEventListener("DOMContentLoaded", () => {
    atualizaDefeitos();
    preencherFormulario();

    document.getElementById("service").addEventListener("change", atualizaDefeitos);
    document.getElementById("defect").addEventListener("change", atualizaValorTotal);
    document.getElementById("defect_other").addEventListener("input", atualizaValorTotal);
    document.getElementById("defect_value_other").addEventListener("input", atualizaValorTotal);
});

    
    </script>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h4>Cadastro de Ordem de Serviço</h4>
            </div>
            <div class="card-body">
                <?php if (isset($mensagem)): ?>
                    <div class="alert alert-info"><?= $mensagem ?></div>
                <?php endif; ?>
                <form method="POST">
                    <div class="mb-3">
                        <label for="service" class="form-label">Serviço</label>
                        <select id="service" class="form-select" name="service" onchange="atualizaDefeitos()">
                            <option value="">Selecione...</option>
                            <option value="Reparo">Reparo</option>
                            <option value="Troca de peça">Troca de peça</option>
                            <option value="Limpeza">Limpeza</option>
                            <option value="Instalação">Instalação</option>
                            <option value="Outros">Outros</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="defect" class="form-label">Defeito</label>
                        <select id="defect" class="form-select" name="defect" onchange="atualizaValorTotal()">
                            <option value="">Selecione...</option>
                        </select>
                    </div>

                    <div id="outroDefeitoBox" style="display: none;">
                        <div class="mb-3">
                            <label for="defect_other" class="form-label">Descreva o defeito</label>
                            <input type="text" class="form-control" id="defect_other" name="defect_other" oninput="atualizaValorTotal()">
                        </div>
                        <div class="mb-3">
                            <label for="defect_value_other" class="form-label">Valor do defeito</label>
                            <input type="number" class="form-control" id="defect_value_other" name="defect_value_other" step="0.01" oninput="atualizaValorTotal()">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="service_value_display" class="form-label">Valor do Serviço</label>
                        <div id="service_value_display" class="form-control">R$ 0.00</div>
                    </div>

                    <div class="mb-3">
                        <label for="total_value" class="form-label">Valor Total</label>
                        <input type="text" class="form-control" id="total_value" name="total_value" readonly>
                    </div>

                    <div class="mb-3">
                        <input type="hidden" id="service_value" name="service_value">
                        <input type="hidden" id="defect_value" name="defect_value">
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Salvar Ordem de Serviço</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
