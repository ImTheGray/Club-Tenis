<?php
session_name("aplicacion");
session_start();


?>

<!DOCTYPE html>
<html lang="en">

  <head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Club de Tenis</title>

    <!-- Bootstrap core CSS -->
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="css/blog-home.css" rel="stylesheet">
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="jquery-ui/jquery-ui.css">
    <script type="text/javascript" src="jquery-ui/jquery-ui.js"></script>
    <script src="script/index.js"></script>


    <style>
      .cerrarSesion{
        margin-left: 10%;
        width: 80%;
      }

    </style>

  </head>

  <body>
    <?php

      if (isset($_POST["destruirSesion"])) {
        session_destroy();
        header("location: index.php");
      }

      if (!isset($_SESSION['tipo'])) {
        $_SESSION['tipo'] = "";
      }

      //print_r($_SESSION);
    ?>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
      <div class="container">
        <a class="navbar-brand" href="index.php">Tenis Oromana</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarResponsive">
          <ul class="navbar-nav ml-auto">
            <li class="nav-item active">
              <a class="nav-link" href="index.php">Inicio
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="eventos.php">Eventos Deportivos</a>
            </li>
            <!-- Solo para admins, se abren en una pestaña nueva lista para imprimir o guardar en pdf-->
            <?php
              if ($_SESSION['tipo'] == "Administrador") {
            ?>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  Listados
                </a>
                <span class="sr-only">(current)</span>
                <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                  <li><a class="dropdown-item" id="listadoSocios" href="listados.php?Socios=1">Listado de socios</a></li>
                  <li><a class="dropdown-item" id="listadoEventos" href="listados.php?Eventos=1">Listado de eventos</a></li>
                  <li><a class="dropdown-item" id="listadosInscritos" href="listados.php?Inscritos=1">Listado de inscritos a un evento</a></li>
                  <li><a class="dropdown-item" id="listadoResultados" href="listados.php?Evento=1">Listado de resultados de un evento</a></li>
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
                  <li><a class="dropdown-item" href="procesos/login.php">Conectarse</a></li>
                  <li><a class="dropdown-item" href="#" id="Registro">Registrarse</a></li>

                  <?php
                }
                  if ($_SESSION['tipo'] == "Administrador") {
                    ?>

                    <!-- Admins only-->
                  <li><a class="dropdown-item" id="altaUsuario" href="#">Alta de Usuario</a></li>
                  <li><a class="dropdown-item" id="modificacionUsuario" href="procesos/editarUsuario.php">Modificación de Usuario</a></li>
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
<div id="formularios"></div>
<div id="divMensajes"><p id="pMensaje"></p></div>
<div class="container">

  <div class="row">

    <!-- Blog Entries Column -->

      <?php
      include('php/Mobile_Detect.php');
      $detect = new Mobile_Detect();

      if ( $detect->isAndroidtablet() || $detect->isIpad() || $detect->isBlackberrytablet() || $detect->isAndroid() || $detect->isIphone() || $detect->isMobile() ) {
        echo '<div class="alert alert-warning alert-dismissable" role="alert"><button type="button" class="close" data-dismiss="alert">&times;</button>No tiene acceso a esta características en el móvil.</div>';
      }
      else{
        $usuario = 'root';
        $contraRoot = '';

        try {
          $con = new PDO('mysql:host=localhost;dbname=club;charset=UTF8', $usuario, $contraRoot);
          $mbd = null;
        } catch (PDOException $e) {
          print "¡Error!: " . $e->getMessage() . "<br/>";
          die();
        }

      if (isset($_GET['Socios'])) {
        echo '<h1 class="my-4">Listado de Socios</h1>';
        include("procesos/listadoSocios.php");
      }

      if (isset($_GET['Eventos'])) {
        echo '<h1 class="my-4">Listado de Eventos</h1>';
        include("procesos/listadoEventos.php");
      }

      if (isset($_GET['Inscritos'])) {
        echo '<h1 class="my-4">Listado de Inscritos a Competición</h1>';
        include("procesos/listadoInscritos.php");
      }


      if (isset($_GET['Evento'])) {
        echo '<h1 class="my-4">Listado de Resultados de Competición</h1>';
        include("procesos/listadoResultados.php");
      }

    }
      ?>
  </div>
</div>
