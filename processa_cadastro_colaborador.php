 <?php
// Conectar ao banco de dados
$mysql = 'localhost';
$user = 'root';
$password = '';
$database = 'oscd_lamanna';

$conn = new mysqli($mysql, $user, $password, $database);

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Recebe os dados do formulário
$NomeColaborador = $_POST['NomeColaborador'];
$CodigoCargo = isset($_POST['CodigoCargo']) ? $_POST['CodigoCargo'] : NULL;
$email = $_POST['email'];
$senha = $_POST['senha'];

// Prevenir SQL Injection
$stmt = $conn->prepare("INSERT INTO colaborador (NomeColaborador, CodigoCargo, email, senha) 
                        VALUES (?, ?, ?, ?)");
$stmt->bind_param("siss", $NomeColaborador, $CodigoCargo, $email, $senha);

// Executar a consulta
if ($stmt->execute()) {
    echo "Colaborador cadastrado com sucesso!";
} else {
    echo "Erro ao cadastrar colaborador: " . $stmt->error;
}

// Fechar conexão
$stmt->close();
$conn->close();
?>