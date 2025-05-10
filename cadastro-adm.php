  <!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ordem_Serviço - Cadastre-se!</title>
    <!-- Bootstrap core CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet" />
    <!-- MDBootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.0/mdb.min.css" rel="stylesheet" />
    <!-- Font Awesome (para os ícones) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

<header class="fixed-top custom-green text-white p-3 shadow">
  <div class="container d-flex justify-content-between align-items-center">
    <h1 class="h4 m-0">Ordem de Serviço</h1>
    <nav class="nav-cliente">
      <a href="cadastro-usuario.php" class="btn-cliente" title="Área do Cliente">👤</a>
    </nav>
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
        <img src="logo-verde.gif" class="floating animated" alt="GIF animado">
      </div>
      <div class="col-md-8 col-lg-6 col-xl-4 offset-xl-1">
        <form action="processa_cadastro_colaborador.php" method="POST">
          <h2 style="margin-bottom: 30px; font-weight: bold; text-align: left;">Cadastro - Colaborador</h2>
          <!-- Nome input -->
          <div data-mdb-input-init class="form-outline mb-4">
            <label class="form-label" for="form3Example1">Nome</label>
            <input type="text" id="form3Example1" name="NomeColaborador" class="form-control form-control-lg" required />
          </div>
          <!-- Email input -->
          <div data-mdb-input-init class="form-outline mb-4">
            <label class="form-label" for="form3Example2">Email</label>
            <input type="email" id="form3Example2" name="email" class="form-control form-control-lg" required />
          </div>
          <!-- Senha input -->
          <div data-mdb-input-init class="form-outline mb-3">
            <label class="form-label" for="form3Example3">Senha</label>
            <input type="password" id="form3Example3" name="senha" class="form-control form-control-lg" required />
          </div>
          <!-- Botão de Cadastro -->
          <div class="text-center text-lg-start mt-4 pt-2">
            <button type="submit" class="btn btn-primary btn-lg" style="padding-left: 2.5rem; padding-right: 2.5rem; background-color:#2B7540;">Cadastrar</button>
          </div>
          <p class="small fw-bold mt-2 pt-1 mb-0">Possui cadastro? Faça <a href="login-adm.php" class="link-danger custom-link">Login</a></p>
        </form>
      </div>
    </div>
  </div>
  
  <!-- Footer -->
  <div class="d-flex flex-column flex-md-row text-center text-md-start justify-content-between py-4 px-4 px-xl-5 custom-green">
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

<!-- Estilos CSS -->
<style>
  .nav-cliente {
      position: absolute;
      top: 10px;
      left: 65px;
      z-index: 1000;
  }
  .btn-cliente {
      background-color: #28a745;
      width: 45px;
      height: 45px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 20px;
      color: white;
      border: none;
      transition: background-color 0.3s ease;
      text-decoration: none;
  }
  .btn-cliente:hover {
      background-color: #218838;
  }
  #form3Example1,
  #form3Example2,
  #form3Example3 {
    border: 2px solid #2B7540;
  }
  #form3Example1:focus,
  #form3Example2:focus,
  #form3Example3:focus {
    border: 2px solid #2B7540;
    box-shadow: 0 0 8px rgba(43, 117, 64, 0.5);
  }
  #form3Example1:valid + label,
  #form3Example2:valid + label,
  #form3Example3:valid + label {
    visibility: hidden;
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
  .custom-green {
    background-color: #2B7540 !important;
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
    overflow: hidden;
  }
</style>

</body>
</html>