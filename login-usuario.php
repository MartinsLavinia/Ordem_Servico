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
  <!-- Font Awesome (ícones) -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
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
        <img src="logo.gif" class="floating" alt="GIF animado">
      </div>
      <div class="col-md-8 col-lg-6 col-xl-4 offset-xl-1">

        
     <form action="processa_login.php" method="POST">
  <h2 class="mb-4 fw-bold text-start">Login - Bem-vindo, Usuário!</h2>

  <div class="form-outline mb-4">
    <label class="form-label" for="form3Example3">Email</label>
    <input type="email" name="email" id="form3Example3" class="form-control form-control-lg" required />
  </div>

  <div class="form-outline mb-3">
    <label class="form-label" for="form3Example4">Senha</label>
    <input type="password" name="senha" id="form3Example4" class="form-control form-control-lg" required />
  </div>

  <button type="submit" class="btn btn-primary btn-lg">Login</button>

  <p class="small fw-bold mt-2 pt-1 mb-0">
    Não possui conta? <a href="cadastro-usuario.php" class="link-danger">Cadastre-se</a>
  </p>
</form>


      </div>
    </div>
  </div>

  <!-- Rodapé -->
  <div class="d-flex flex-column flex-md-row text-center text-md-start justify-content-between py-4 px-4 px-xl-5 bg-primary">
    <div class="text-white mb-3 mb-md-0">
      Copyright © 2025. Aims.
    </div>
    <div>
      <a href="#!" class="text-white me-4"><i class="fab fa-facebook-f"></i></a>
      <a href="#!" class="text-white me-4"><i class="fab fa-twitter"></i></a>
      <a href="#!" class="text-white me-4"><i class="fab fa-google"></i></a>
      <a href="#!" class="text-white"><i class="fab fa-linkedin-in"></i></a>
    </div>
  </div>
</section>

<!-- ESTILO PERSONALIZADO -->
<style>
  .form-outline input {
    border: 2px solid #0d6efd !important;
    background-color: #f0f8ff;
    box-shadow: 0 0 5px rgba(13, 110, 253, 0.4);
  }

  .form-outline input:focus {
    border-color: #0a58ca !important;
    box-shadow: 0 0 8px rgba(13, 110, 253, 0.6);
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
    width: 100%;
    max-width: 500px;
    height: auto;
    display: block;
    margin: 0 auto;
  }

  html, body {
    height: 100%;
    margin: 0;
  }
</style>

<!-- MDBootstrap JS (opcional, mas útil se usar componentes interativos) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.0/mdb.min.js"></script>



</body>
</html>
