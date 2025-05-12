<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ordem_Serviço - Cadastro</title>
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
   <nav class="nav-adm">
  <a href="login-adm.php" class="btn-adm" title="Área do Administrador">
    🔒
  </a>
</nav>
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

       <form action="processa_cadastro.php" method="POST">

     <h2 style="margin-bottom: 10px; font-weight: bold; text-align: left;">Cadastro - Usuário</h2>
         <!-- Nome input -->
          <div data-mdb-input-init class="form-outline mb-4">
            <label class="form-label" for="form3Example1">Nome</label>
            <input type="text" id="form1Example1" name="NomeCliente" class="form-control input-menor" required />
          </div>
          <!-- Email input -->
          <div data-mdb-input-init class="form-outline mb-4">
          <label class="form-label" for="form3Example3">Email</label>
       <input type="email" id="form2Example2" name="email" class="form-control input-menor" required />
  
          </div>

          <!-- Password input -->
          <div data-mdb-input-init class="form-outline mb-3">
          <label class="form-label" for="form3Example4">Senha</label>
         <input type="password" id="form3Example3" name="senha" class="form-control input-menor" required />
          </div>


          <div class="text-center text-lg-start mt-4 pt-2">
          <button type="submit" class="btn btn-primary btn-lg">Cadastrar</button>
            <p class="small fw-bold mt-2 pt-1 mb-0">Possui conta? <a href="login-usuario.php"
                class="link-danger" >Entrar</a></p>
          </div>
          </div>

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
.nav-adm {
    position: absolute;
    top: 10px;
    left: 10px;
    z-index: 1000;
}

.btn-adm {
    background-color: #0056b3;
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

.btn-adm:hover {
    background-color: #0056b3;
}


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