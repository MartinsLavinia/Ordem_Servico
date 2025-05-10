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
    <h1 class="h4 m-0">Ordem de Serviço - Administrador</h1>
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
        <script type='text/javascript'>document.addEventListener('DOMContentLoaded', function () {window.setTimeout(document.querySelector('svg').classList.add('animated'),1000);})</script>
      </div>
      <div class="col-md-8 col-lg-6 col-xl-4 offset-xl-1">


   <form action="processa_cadadm.php" method="POST">
  <h2 style="margin-bottom: 30px; font-weight: bold; text-align: left;">Cadastro - Colaborador</h2>
<!-- Nome input -->
<div data-mdb-input-init class="form-outline mb-4">
  <label class="form-label" for="form3Example1">Nome</label>
  <input type="text" name="nome" id="form3Example1" class="form-control form-control-lg" required />
</div>

<!-- Email input -->
<div data-mdb-input-init class="form-outline mb-4">
  <label class="form-label" for="form3Example2">Email</label>
  <input type="email" name="email" id="form3Example2" class="form-control form-control-lg" required />
</div>

<!-- Senha input -->
<div data-mdb-input-init class="form-outline mb-3">
  <label class="form-label" for="form3Example3">Senha</label>
  <input type="password" name="senha" id="form3Example3" class="form-control form-control-lg" required />
</div>



<div class="text-center text-lg-start mt-4 pt-2">
  <button type="submit" class="btn btn-primary btn-lg"
    style="padding-left: 2.5rem; padding-right: 2.5rem; background-color:#2B7540;">Cadastrar</button>
</div>


    <p class="small fw-bold mt-2 pt-1 mb-0">Possui conta? <a href="login_adm.php" class="link-danger">Entrar</a></p>
    <p class="small fw-bold mt-2 pt-1 mb-0">É um usuário? Faça <a href="login-usuario.php" class="link-danger custom-link">Login</a></p>
  </div>
</form>



      </div>
    </div>
  </div>
  
  <!-- Footer -->
  <div class="d-flex flex-column flex-md-row text-center text-md-start justify-content-between py-4 px-4 px-xl-5 custom-green">

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
/* Borda verde ao redor dos inputs */
#form3Example1,
#form3Example2,
#form3Example3 {
  border: 2px solid #2B7540;
}

/* Borda verde mais destacada quando o campo está em foco */
#form3Example1:focus,
#form3Example2:focus,
#form3Example3:focus {
  border: 2px solid #2B7540;
  box-shadow: 0 0 8px rgba(43, 117, 64, 0.5);
}

/* Ocultar label quando o input for preenchido */
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
  overflow: hidden; /* impede rolagem */
}

svg#freepik_stories-service-247:not(.animated) .animable {
  opacity: 0;
}

svg#freepik_stories-service-247.animated #freepik--Chat--inject-82 {
  animation: floating 1.5s infinite linear;
  animation-delay: 0s;
}

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
