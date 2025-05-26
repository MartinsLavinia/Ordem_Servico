<?php
include 'conexao.php';
include('verifica_login.php');
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
        $client_id     = $data['client_id']; // Correto agora!

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
        $_POST['client_id'] = $_SESSION['CodigoCliente'];

        $serviceOrder = new ServiceOrder($conexao);
        $numero_os = $serviceOrder->save($_POST);

        if ($numero_os !== false) {
            $mensagem = "✅ Cadastro enviado com sucesso!<br>🧾 Seu número de Ordem de Serviço é: <strong>$numero_os</strong>";
        } else {
            $mensagem = "❌ Erro ao salvar a Ordem de Serviço. Tente novamente.";
        }
    }
}

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
    <title>Cadastro de Ordem de Serviço</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">


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

    .card-body2 {
        margin-bottom: 150px;
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

<!-- Conteúdo da página com espaçamento para o cabeçalho fixo -->
<div class="content" style="padding-top: 40px;">
  <div class="text-center mb-4">
      <h2 class="mb-4 text-center fw-bold" style="color: #0d6efd;">Bem-vindo, <?= htmlspecialchars($nome_cliente) ?>!</h2>
      <p>Use o sistema para cadastrar, acompanhar e gerenciar suas ordens de serviço com facilidade.</p>
    </div>
</div>





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
