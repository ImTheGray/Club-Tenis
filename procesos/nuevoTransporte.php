<?php
session_name("aplicacion");
session_start();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Standard Meta -->
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">

    <!-- Site Properties -->
    <title>Nueva Transporte</title>

    <!-- Stylesheets -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.1/css/font-awesome.min.css">
    <!-- Bootstrap core CSS -->
    <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="../css/blog-home.css" rel="stylesheet">
    <script src="../vendor/jquery/jquery.min.js"></script>
    <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="../jquery-ui/jquery-ui.css">
    <script type="text/javascript" src="../jquery-ui/jquery-ui.js"></script>
    <script src="../script/index.js"></script>
    <style>
      .form{
        margin-top: 4%;
      }
    </style>
    <script type="text/javascript">
    $( function() {
      $( "#txtfechaEvento" ).datepicker({dateFormat: 'yy-mm-dd'});
      $( "#txtEspacio" ).spinner();
    } );

    </script>
</head>
<body>
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <div class="container">
      <a class="navbar-brand" href="#">Tenis Oromana</a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" >
        <ul class="navbar-nav ml-auto">
          <li class="nav-item active">
            <a class="nav-link" href="../index.php">Inicio
              <span class="sr-only">(current)</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="../eventos.php">Eventos Deportivos</a>
          </li>
          <!-- Solo para admins, se abren en una pestaña nueva lista para imprimir o guardar en pdf-->
          <?php
            if ($_SESSION['tipo'] == "Administrador") {
          ?>
          <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Listados
              </a>
              <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                <li><a class="dropdown-item" id="listadoSocios" href="#">Listado de socios</a></li>
                <li><a class="dropdown-item" id="listadoEventos" href="#">Listado de eventos</a></li>
                <li><a class="dropdown-item" id="listadosInscritos" href="#">Listado de inscritos a un evento</a></li>
                <li><a class="dropdown-item" id="listadoResultados" href="#">Listado de resultados de un evento</a></li>
              </ul>
          </li>
          <?php
          }
        ?>
          <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Panel de control de usuario
              </a>
              <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                <?php
                if ($_SESSION['tipo'] == "") {
                  # code...
                  ?>
                <!-- Quitar si se conecta alguien only-->
                <li><a class="dropdown-item" href="login.php">Conectarse</a></li>
                <li><a class="dropdown-item" href="#" id="Registro">Registrarse</a></li>

                <?php
              }
                if ($_SESSION['tipo'] == "Administrador") {
                  ?>

                  <!-- Admins only-->
                <li><a class="dropdown-item" id="altaUsuario" href="#">Alta de Usuario</a></li>
                <li><a class="dropdown-item" id="modificacionUsuario" href="editarUsuario.php">Modificación de Usuario</a></li>
                  <?php
                }

                if ($_SESSION['tipo'] != "") {
                ?>
                <hr>
                 <!-- Colocar si se conecta alguien-->
                 <form action="index.php" method="post">
                   <li>
                     <button type="submit" name="destruirSesion" class="btn btn-danger cerrarSesion">Cerrar Sesión</button>
                   </li>
                 </form>
                 <?php
               }

                 ?>
              </ul>
          </li>
        </ul>
      </div>
    </div>
  </nav>
  <?php

  if ($_SESSION['tipo'] == 'Administrador') {

    if (isset($_POST["btnTransporte"])) {
      $espacio = $_POST['txtEspacio'];

      $bValido = true;
      $sError = "";

      if ($espacio == "") {
        $sError .= "El espacio debe tener algún número.<br>";
        $bValido = false;
      }

      if ($espacio == 0) {
        $sError .= "El espacio debe ser distinto de 0.<br>";
        $bValido = false;
      }


      if ($bValido == false) {
        echo '<div class="alert alert-warning alert-dismissable" role="alert"><button type="button" class="close" data-dismiss="alert">&times;</button>'.$sError.'</div>';
      }
      else{
        include("../php/altas/altaTransporte.php");
      }
    }

  ?>
        <div class="container form">
        <form class="form-horizontal" role="form" method="POST" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-3"></div>
                <div class="col-md-6">
                    <h2>Añadir Transporte</h2>
                    <hr>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3"></div>
                <div class="col-md-6">
                    <div class="form-group has-danger">
                        <label class="sr-only" for="txtEspacio">Espacio</label>
                        <div class="input-group mb-2 mr-sm-2 mb-sm-0">
                            <input type="number" name="txtEspacio" class="form-control" value="1" autofocus>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3"></div>
                <div class="col-md-6">
                    <div class="form-group">
                        <div class="input-group mb-2 mr-sm-2 mb-sm-0">
                          <select class="form-control" name="elegirCompeticion">
                            <?php
                            $usuario = 'root';
                            $contraRoot = '';

                            try {
                              $con = new PDO('mysql:host=localhost;dbname=club', $usuario, $contraRoot);
                              $mbd = null;
                            } catch (PDOException $e) {
                                print "¡Error!: " . $e->getMessage() . "<br/>";
                                die();
                            }

                            $date = date("Y-m-d");
                            //Realización de
                            $sql = $con->prepare("SELECT * FROM competiciones WHERE fechaEvento >= '".$date."'");
                            $sql->execute();

                            $row = $sql->fetchAll(PDO::FETCH_ASSOC);

                            for ($i=0; $i < count($row); $i++) {
                              echo '<option value="'.$row[$i]["idCompeticion"].'">';
                              echo $row[$i]['nombreEvento'];
                              echo "</option>";
                            }
                            ?>
                          </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row" style="padding-top: 1rem">
                <div class="col-md-3"></div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-success" name="btnTransporte">Añadir Transporte</button>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-danger active" name="btnVolver" formaction="../index.php">Volver al Indice</button>
                </div>
            </div>
        </form>
    </div>
    <?php
  }
  else{
    echo '<div class="alert alert-warning alert-dismissable" role="alert">No tiene acceso a este característica, <a href="../index.php">vuelva al inicio</a>.</div>';
  }
    ?>
</body>
</html>