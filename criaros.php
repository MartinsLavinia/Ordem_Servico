<?php
include 'conexao.php'; // Inclua a conexão com o banco de dados

class ServiceOrder {
    private $connection;

    public function __construct($connection) {
        $this->connection = $connection;
    }

    // Função para gerar o Número da OS
    private function generateNumeroOS() {
        $today = date('Ymd');
        $stmt = $this->connection->prepare("SELECT COUNT(*) AS total FROM OS WHERE NumeroOS LIKE ?");
        $prefix = "OS$today%";
        $stmt->bind_param("s", $prefix);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $countToday = $result['total'] + 1;

        return "OS" . $today . str_pad($countToday, 3, "0", STR_PAD_LEFT);
    }

    // Função para salvar a ordem de serviço no banco
    public function save($data) {
        $numero_os   = $this->generateNumeroOS();
        $date        = $data['date'];
        $equipment   = $data['equipment'];
        $defect      = $data['defect'] === 'Outros' ? $data['defect_other'] : $data['defect'];
        $service     = $data['service'];
        $defect_value = $data['defect_value'];
        $service_value = $data['service_value'];  // Recebe o valor do serviço
        $total_value = $defect_value + $service_value;
        $client_id   = $data['client_id'];
        $client_name = $data['client_name'];

        // Verifica se o cliente existe
        $stmt = $this->connection->prepare("SELECT 1 FROM CLIENTE WHERE CodigoCliente = ?");
        $stmt->bind_param("i", $client_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            $insertClient = $this->connection->prepare("INSERT INTO CLIENTE (CodigoCliente, NomeCliente) VALUES (?, ?)");
            $insertClient->bind_param("is", $client_id, $client_name);
            $insertClient->execute();
        }

        // Insere a ordem de serviço no banco de dados
        $insertOS = $this->connection->prepare(
            "INSERT INTO OS (NumeroOS, Data, Equipamento, Defeito, Servico, ValorTotal, CodigoCliente)
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );

        // Passando os parâmetros corretamente, incluindo o valor do serviço
        $insertOS->bind_param("ssssdsi", $numero_os, $date, $equipment, $defect, $service, $total_value, $client_id);

        if ($insertOS->execute()) {
            return $numero_os;
        }

        return false;
    }
}

$mensagem = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Para este exemplo, o código do cliente pode ser fornecido via uma variável de sessão ou outro método
    // Aqui estamos simulando com um código de cliente estático.
    $client_id = 123;  // Isso deve ser vindo de alguma parte do sistema, como um login.
    $_POST['client_id'] = $client_id;

    $serviceOrder = new ServiceOrder($connection);
    $numero_os = $serviceOrder->save($_POST);

    if ($numero_os !== false) {
        $mensagem = "✅ Cadastro enviado com sucesso!<br>🧾 Seu número de Ordem de Serviço é: <strong>$numero_os</strong>";
    } else {
        $mensagem = "❌ Erro ao salvar a Ordem de Serviço. Tente novamente.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Ordem de Serviço</title>
</head>
<body>
    <h1>Cadastro de Ordem de Serviço</h1>

    <?php if ($mensagem): ?>
        <p style="padding:10px; border:1px solid #ccc; background:#f8f8f8;">
            <?= $mensagem ?>
        </p>
    <?php endif; ?>

    <form method="POST" action="">
        <label>Data:</label><br>
        <input type="date" name="date" required><br>

        <label>Equipamento:</label><br>
        <select name="equipment" required>
            <option value="">Selecione...</option>
            <option value="Celular">Celular</option>
            <option value="Computador">Computador</option>
            <option value="Notebook">Notebook</option>
        </select><br>

        <label>Serviço:</label><br>
        <select name="service" id="service" required onchange="atualizaDefeitos()">
            <option value="">Selecione...</option>
            <option value="Reparo">Reparo</option>
            <option value="Troca de peça">Troca de peça</option>
            <option value="Limpeza">Limpeza</option>
            <option value="Instalação">Instalação</option>
            <option value="Outros">Outros</option>
        </select><br>

        <label>Defeito:</label><br>
        <select name="defect" id="defect" required onchange="atualizaValorTotal()">
            <option value="">Selecione...</option>
        </select><br>

        <!-- Campo extra para defeito personalizado -->
        <div id="outroDefeitoBox" style="display:none;">
            <label>Descreva o defeito:</label><br>
            <input type="text" name="defect_other" id="defect_other"><br>
            <small><em>💡 Valor aproximado. Pode mudar após avaliação do técnico.</em></small><br>
        </div>

        <label>Valor Total (R$):</label><br>
        <input type="number" step="0.01" name="total_value" id="total_value" readonly required><br>

        <input type="hidden" name="service_value" id="service_value"><!-- Campo oculto para valor do serviço -->

        <label>Nome do Cliente:</label><br>
        <input type="text" name="client_name" required><br><br>

        <input type="submit" value="Salvar Ordem de Serviço">
    </form>

    <script>
    // Definição dos defeitos para cada serviço
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

    // Função para atualizar os defeitos de acordo com o serviço selecionado
    function atualizaDefeitos() {
        const serviceSelect = document.getElementById("service");
        const defectSelect = document.getElementById("defect");
        const outroBox = document.getElementById("outroDefeitoBox");

        // Limpa a lista de defeitos
        defectSelect.innerHTML = '<option value="">Selecione...</option>';

        const defeitos = defeitosPorServico[serviceSelect.value] || {};

        // Preenche os defeitos no select de acordo com o serviço
        if (serviceSelect.value === "Outros") {
            outroBox.style.display = "block";
            return;
        }

        outroBox.style.display = "none";

        for (const [defeito, valor] of Object.entries(defeitos)) {
            const option = document.createElement("option");
            option.value = defeito;
            option.text = defeito;
            defectSelect.appendChild(option);
        }

        // Atualiza o valor total quando um defeito é selecionado
        atualizaValorTotal();
    }

    // Função para atualizar o valor total de acordo com o defeito selecionado
    function atualizaValorTotal() {
        const defectSelect = document.getElementById("defect");
        const serviceSelect = document.getElementById("service");
        const defectValue = defeitosPorServico[serviceSelect.value][defectSelect.value] || 0;

        const serviceValue = {
            "Reparo": 100,
            "Troca de peça": 150,
            "Limpeza": 80,
            "Instalação": 120,
            "Outros": 50
        };

        // Atualiza o valor total
        const serviceAmount = serviceValue[serviceSelect.value] || 50;
        const valorInput = document.getElementById("total_value");
        valorInput.value = (defectValue + serviceAmount).toFixed(2);

        // Atualiza o campo oculto para enviar o valor do serviço ao PHP
        document.getElementById("service_value").value = serviceAmount;  // Atualiza o valor do serviço aqui

        // Atualiza o valor do defeito para enviar ao PHP (não é necessário, mas é uma boa prática)
        document.getElementById("defect_value").value = defectValue;
    }

    // Função para inicializar a lista de defeitos dependendo do serviço
    document.addEventListener("DOMContentLoaded", () => {
        atualizaDefeitos();
    });
    </script>
</body>
</html>
