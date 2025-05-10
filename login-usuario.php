<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ordem_Serviço - Login Usuário</title>
    <!-- Bootstrap core CSS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet" />

<!-- MDBootstrap CSS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.0/mdb.min.css" rel="stylesheet" />

<!-- Font Awesome (para os ícones) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

<header class="fixed-top bg-primary text-white p-3 shadow">
  <div class="container d-flex justify-content-between align-items-center">
    <h1 class="h4 m-0">Ordem de Serviço</h1>
    <nav>
      <a href="#" class="text-white me-3">Início</a>
      <a href="#" class="text-white me-3">Sobre</a>
      <a href="#" class="text-white">Contato</a>
    </nav>
  </div>
</header>

<section class="vh-100">
  <div class="container-fluid h-custom">
    <div class="row d-flex justify-content-center align-items-center h-100">
      <div class="col-md-9 col-lg-6 col-xl-5">
      <img src="logo.gif" class="floating animated" alt="GIF animado">

     <script type='text/javascript'>document.addEventListener('DOMContentLoaded', function () {window.setTimeout(document.querySelector('svg').classList.add('animated'),1000);})</script>
     </div>
      <div class="col-md-8 col-lg-6 col-xl-4 offset-xl-1">

      <form method="POST" action="processa_login.php">
  <h2 style="margin-bottom: 30px; font-weight: bold; text-align: left;">Login - Bem-vindo, Usuário!</h2>
  
  <div class="form-outline mb-4">
    <label class="form-label" for="form3Example3">Email</label>
    <input type="email" name="email" id="form3Example3" class="form-control form-control-lg" required />
  </div>

  <div class="form-outline mb-3">
    <label class="form-label" for="form3Example4">Senha</label>
    <input type="password" name="senha" id="form3Example4" class="form-control form-control-lg" required />
  </div>

  <button type="submit" class="btn btn-primary btn-lg">Login</button>

   <p class="small fw-bold mt-2 pt-1 mb-0">Possui conta? <a href="login-usuario.php"
                class="link-danger" >Entrar</a></p>
                 <p class="small fw-bold mt-2 pt-1 mb-0">É um colaborador? <a href="login-usuario.php"
                class="link-danger">Cadastre-se</a></p>
</form>

      </div>
    </div>
  </div>
  <div
    class="d-flex flex-column flex-md-row text-center text-md-start justify-content-between py-4 px-4 px-xl-5 bg-primary">
    <!-- Copyright -->
    <div class="text-white mb-3 mb-md-0">
    Copyright © 2025. Aims.
    </div>
    <!-- Copyright -->

    <!-- Right -->
    <div>
      <a href="#!" class="text-white me-4">
        <i class="fab fa-facebook-f"></i>
      </a>
      <a href="#!" class="text-white me-4">
        <i class="fab fa-twitter"></i>
      </a>
      <a href="#!" class="text-white me-4">
        <i class="fab fa-google"></i>
      </a>
      <a href="#!" class="text-white">
        <i class="fab fa-linkedin-in"></i>
      </a>
    </div>
    <!-- Right -->
  </div>
</section>

<style>
  /* Destaque para os campos de formulário */
.form-outline input {
  border: 2px solid #0d6efd !important; /* azul do Bootstrap */
  background-color: #f0f8ff; /* leve azul claro */
  box-shadow: 0 0 5px rgba(13, 110, 253, 0.4); /* sombra suave azul */
}

.form-outline input:focus {
  border-color: #0a58ca !important;
  box-shadow: 0 0 8px rgba(13, 110, 253, 0.6);
}

.divider:after,
.divider:before {
  content: "";
  flex: 1;
  height: 1px;
  background: #333; 
}

.h-custom {
height: calc(100% - 73px);
}
@media (max-width: 450px) {

.h-custom {
height: 100%;
}
}

img.floating {
  width: 500px;     
  height: auto;     
  display: block;   
  margin: 0 auto;   
}

html, body {
  height: 100%;
  margin: 0;
  overflow: hidden; /* impede rolagem */
}


/* Oculta todos os elementos animáveis até a classe "animated" ser adicionada */
svg#freepik_stories-service-247:not(.animated) .animable {
  opacity: 0;
}

/* Aplica animação quando a classe "animated" estiver presente */
svg#freepik_stories-service-247.animated #freepik--Chat--inject-82 {
  animation: floating 1.5s infinite linear;
  animation-delay: 0s;
}

/* Definição da animação "floating" */
@keyframes floating {
  0% {
    opacity: 1;
    transform: translateY(0px);
  }
  50% {
    transform: translateY(-10px);
  }
  100% {
    opacity: 1;
    transform: translateY(0px);
  }
}

</style>
    
</body>
</html>
<?php

                    $servername = "localhost";
                    $username = "root";
                    $password = "";
                    $dbname = "oscd_lamanna";

                    // Conexão
                    $conexao = new mysqli($servername, $username, $password, $dbname);
                    if ($conexao->connect_error) {
                        die("Falha na conexão: " . $conexao->connect_error);
                    }

                    // Verifica se foi enviado via POST
                    if ($_SERVER["REQUEST_METHOD"] == "POST") {
                        $email = trim($_POST['email']);
                        $senha = $_POST['senha'];

                        $stmt = $conexao->prepare("SELECT senha FROM contas WHERE email = ?");
                        $stmt->bind_param("s", $email);
                        $stmt->execute();
                        $stmt->store_result();

                        if ($stmt->num_rows > 0) {
                            $stmt->bind_result($senhaHash);
                            $stmt->fetch();

                            if (password_verify($senha, $senhaHash)) {
                                $_SESSION['email'] = $email;
                                header("Location: criaros.php");
                                exit(); // Encerrar execução após redirecionar
                            } else {
                                $erro = "Senha incorreta!";
                            }
                        } else {
                            $erro = "Email não encontrado!";
                        }

                        $stmt->close();
                        $conexao->close();
                    }
                    ?>
                    
                    <?php if (isset($erro)) echo "<p style='color:red;'>$erro</p>"; ?>
