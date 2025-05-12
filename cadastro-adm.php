<?php
include("conexao.php");

$cargos = [];
$query = "SELECT CodigoCargo, NomeCargo FROM cargo";
$result = $conexao->query($query);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $cargos[] = $row;
    }
}
?>

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
  </div>
</header>


<?php if (isset($_GET['cadastro']) && $_GET['cadastro'] == 'sucesso'): ?>
  <div id="mensagem" class="alert alert-success text-center shadow" style="position: absolute; top: -60px; left: 0; right: 0; margin: auto; z-index: 999;">
    Cadastro realizado!
  </div>
<?php endif; ?>

<script>
  const mensagem = document.getElementById("mensagem");
  if (mensagem) {
    setTimeout(() => {
      window.location.href = "login-adm.php";
    }, 5000); // 5 segundos
  }
</script>


<section class="vh-100 section-content">
  <div class="container-fluid h-custom">
    <div class="row d-flex justify-content-center align-items-start h-100 mt-5">
      <div class="col-md-9 col-lg-6 col-xl-5">
        <img src="logo-verde.gif" class="floating animated" alt="GIF animado" style="margin-top: 100px;">
      </div>
      <div class="col-md-8 col-lg-6 col-xl-4 offset-xl-1">


        <form action="processa_cadastro_colaborador.php" method="POST" class="mt-4">
          <h2 style="margin-top: 80px; margin-bottom: 20px; font-weight: bold; text-align: left;">Cadastro - Colaborador</h2>
          <!-- Nome input -->
          <div data-mdb-input-init class="form-outline mb-4">
            <label class="form-label" for="form3Example1">Nome</label>
            <input type="text" id="form3Example1" name="NomeColaborador" class="form-control input-menor" required />
          </div>
          <!-- Email input -->
          <div data-mdb-input-init class="form-outline mb-4">
            <label class="form-label" for="form3Example2">Email</label>
            <input type="email" id="form3Example2" name="email" class="form-control input-menor" required />
          </div>
          <!-- Campo Select para Cargo -->
          <div class="form-outline mb-4">
            <label class="form-label" for="form3Example2">Cargo</label>
            <select id="cargoSelect" name="CodigoCargo" class="form-control input-menor" required>
              <option value="" disabled selected>Selecione o Cargo</option>
              <?php
              include('conexao.php');
              $sql = "SELECT * FROM cargo";
              $result = $conexao->query($sql);
              while ($row = $result->fetch_assoc()) {
                  echo "<option value='" . $row['CodigoCargo'] . "'>" . $row['NomeCargo'] . "</option>";
              }
              ?>
            </select>
          </div>
          <!-- Senha input -->
          <div data-mdb-input-init class="form-outline mb-3">
            <label class="form-label" for="form3Example3">Senha</label>
            <input type="password" id="form3Example3" name="senha" class="form-control input-menor" required />
          </div>
          
          <!-- Botão de Cadastro -->
<div class="text-center text-lg-start mt-4 pt-2">
  <button type="submit" class="btn btn-primary btn-lg" style="padding-left: 2.5rem; padding-right: 2.5rem; background-color:#2B7540;">Cadastrar</button>
  <p class="small fw-bold m-0 p-0 mt-3">Possui cadastro? Faça <a href="login-adm.php" class="link-danger custom-link">login</a></p>
</div>

        
   
  </div>
        </form>
      </div>
    </div> 
  </div>

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
 



<!-- Estilos CSS -->
<style>
.custom-footer {
  background-color: #2B7540;
  color: white;
  padding: 1.2rem 0;
  text-align: center;
  width: 100%;
  
  
}

.custom-footer .social-links a {
  color: white;
  font-size: 1.2rem;
  text-decoration: none;
  transition: color 0.3s ease;
}

 /* Cor personalizada para os links */
.custom-link {
  color: #FF5733; /* Escolha a cor que você preferir */
  padding-top: 10px;
}

.custom-link:hover {
  color: #C70039; /* Cor do link ao passar o mouse (efeito hover) */
}

  .custom-green {
  background-color: #2B7540 !important;
}

.custom-link {
  padding-top: 10px;
}


  .input-menor {
  max-width: 300px; /* ou 250px, 200px — escolha o tamanho ideal */
  width: 100%;
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


  #form3Example1,
  #form3Example2,
  #cargoSelect,
  #form3Example3 {
    border: 2px solid #2B7540;
  }
  #form3Example1:focus,
  #form3Example2:focus,
  #cargoSelect:focus,
  #form3Example3:focus {
    border: 2px solid #2B7540;
    box-shadow: 0 0 8px rgba(43, 117, 64, 0.5);
  }
  #form3Example1:valid + label,
  #form3Example2:valid + label,
  #cargoSelect:valid + label,
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
   overflow-y: auto;
    
  }
</style>

</body>
</html>