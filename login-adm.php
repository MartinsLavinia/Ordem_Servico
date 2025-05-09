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
        <form>
        <h2 style="margin-bottom: 30px; font-weight: bold; text-align: left; ">Login - Bem-vindo, Colaborador!</h2>
          <div class="d-flex flex-row align-items-center justify-content-center justify-content-lg-start">
            <p class="lead fw-normal mb-0 me-3">Entrar com</p>
            <button type="button" data-mdb-button-init data-mdb-ripple-init class="btn btn-primary btn-floating mx-1">
              <i class="fab fa-google"></i>
            </button>

            <button type="button" data-mdb-button-init data-mdb-ripple-init class="btn btn-primary btn-floating mx-1">
              <i class="fab fa-linkedin-in"></i>
            </button>
          </div>

          <div class="divider d-flex align-items-center my-4">
            <p class="text-center fw-bold mx-3 mb-0">Ou</p>
          </div>

          <!-- Email input -->
          <div data-mdb-input-init class="form-outline mb-4">
          <label class="form-label" for="form3Example3">Email</label>
            <input type="email" id="form3Example3" class="form-control form-control-lg"
               />
            
          </div>

          <!-- Password input -->
          <div data-mdb-input-init class="form-outline mb-3">
          <label class="form-label" for="form3Example4">Senha</label>
            <input type="password" id="form3Example4" class="form-control form-control-lg"
             />
           
          </div>

          <div class="d-flex justify-content-between align-items-center">
            <!-- Checkbox -->
            <div class="form-check mb-0">
              <input class="form-check-input me-2" type="checkbox" value="" id="form2Example3" />
              <label class="form-check-label" for="form2Example3">
                Lembre de mim
              </label>
            </div>
            <a href="#!" class="text-body">Esqueceu a senha?</a>
          </div>

          <div class="text-center text-lg-start mt-4 pt-2">
            <button type="button" data-mdb-button-init data-mdb-ripple-init class="btn btn-primary btn-lg"
              style="padding-left: 2.5rem; padding-right: 2.5rem; background-color:#2B7540;">Login</button>
           <p class="small fw-bold mt-2 pt-1 mb-0">Possui conta? <a href="login-adm.php" class="link-danger custom-link">Entrar</a></p>
<p class="small fw-bold mt-2 pt-1 mb-0">É um colaborador? <a href="cadastro-adm.php" class="link-danger custom-link">Cadastre-se</a></p>
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
  /* Cor personalizada para os links */
.custom-link {
  color: #FF5733; /* Escolha a cor que você preferir */
}

.custom-link:hover {
  color: #C70039; /* Cor do link ao passar o mouse (efeito hover) */
}

/* Borda verde ao redor dos inputs */
#form3Example3,
#form3Example4 {
  border: 2px solid #2B7540;
}

/* Borda verde mais destacada quando o campo está em foco */
#form3Example3:focus,
#form3Example4:focus {
  border: 2px solid #2B7540;
  box-shadow: 0 0 8px rgba(43, 117, 64, 0.5);
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
