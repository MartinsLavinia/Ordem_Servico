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
  <a href="cadastro-usuario.php" class="btn-adm" title="Área do Cliente">👤</a>
  </a>
</nav>
</header>

<section class="vh-100 section-content">>

  <div class="container-fluid h-custom">
    <div class="row d-flex justify-content-center align-items-center h-100">
      <div class="col-md-9 col-lg-6 col-xl-5">
        <img src="logo-verde.gif" class="floating animated" alt="GIF animado">
        <script type='text/javascript'>document.addEventListener('DOMContentLoaded', function () {window.setTimeout(document.querySelector('svg').classList.add('animated'),1000);})</script>
      </div>
      <div class="col-md-8 col-lg-6 col-xl-4 offset-xl-1">
 
      
      <form action="processa_login_colaborador.php" method="POST">
  <h2 style="margin-bottom: 10px; font-weight: bold; text-align: left;">
  Login - Bem-vindo, Colaborador!
</h2>

  <!-- Email input -->
  <div data-mdb-input-init class="form-outline mb-4">
    <label class="form-label" for="form3Example3">Email</label>
    <input type="email" id="form3Example3" name="email" class="form-control input-menor" required />
  </div>

  <!-- Password input -->
  <div data-mdb-input-init class="form-outline mb-3">
    <label class="form-label" for="form3Example4">Senha</label>
   <input type="password" id="form3Example3" name="senha" class="form-control input-menor" required />
  </div>

 <div class="text-center text-lg-start mt-4 pt-2">
  <button type="submit" class="btn btn-primary btn-lg" style="padding-left: 2.5rem; padding-right: 2.5rem; background-color:#2B7540;">Login</button>
  <p class="small fw-bold mt-3 pt-1 mb-0">Não tem cadastro, colaborador? <a href="cadastro-adm.php" class="link-danger custom-link">Cadastre-se</a></p>
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

  .input-menor {
  max-width: 300px; /* ou 250px, 200px — escolha o tamanho ideal */
  width: 100%;
}

  /* Cor personalizada para os links */
.custom-link {
  color: #FF5733; /* Escolha a cor que você preferir */
}

.custom-link:hover {
  color: #C70039; /* Cor do link ao passar o mouse (efeito hover) */
}


 .nav-cliente {
    position: absolute;
    top: 20px;   /* Mais afastado do topo */
    left: 20px;  /* Mais afastado da esquerda */
    z-index: 1000;
  }

 .btn-cliente {
  background-color:rgb(66, 160, 88);
  width: 90px;
  height: 90px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 36px; /* Emoji maior */
  color: white;
  border: none;
  transition: background-color 0.3s ease;
  text-decoration: none;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
}

  .btn-cliente:hover {
    background-color: #218838;
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
    overflow-y: auto
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