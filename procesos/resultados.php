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

    <?php

    if (isset($_GET['idCompeticion'])) {
      $idCompeticion = $_GET['idCompeticion'];
          //echo "<script>alert($idCompeticion)</script>";
          # code...
    }
    else{
      $idCompeticion = $_POST['idCompeticion'];

      //echo "<script>alert($idCompeticion)</script>";

    }

    $usuario = 'root';
$contraRoot = '';

try {
  $con = new PDO('mysql:host=localhost;dbname=club;charset=UTF8', $usuario, $contraRoot);
  $mbd = null;
} catch (PDOException $e) {
  print "¡Error!: " . $e->getMessage() . "<br/>";
  die();
}



    $nombreEventoSql = $con->prepare("SELECT nombreEvento FROM competiciones WHERE idCompeticion = $idCompeticion");
    $nombreEventoSql->execute();

    $row = $nombreEventoSql->fetchAll(PDO::FETCH_ASSOC);
    $nombreEvento = $row[0]['nombreEvento'];

    echo "<title>".$nombreEvento."</title>";

    ?>

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
    <style>
      .cerrarSesion{
        margin-left: 10%;
        width: 80%;
      }

      .table > tbody > tr > td {
       vertical-align: middle;
       text-align: center;
      }
      .table > thead > tr > th {
       vertical-align: middle;
       text-align: center;
      }
    </style>
</head>
<body>
  <?php

  include("../php/navbar.php");
  ?>
  <div id="formularios"></div>
  <div id="divMensajes"><p id="pMensaje"></p></div>
  <?php

    if (isset($_POST["btnTransporte"])) {
      $espacio = $_POST['elegirContrincante'];

      $bValido = true;
      $sError = "";


      if ($espacio == 0) {
        $sError .= "No hay contrincantes disponibles para colocar resultados.<br>";
        $bValido = false;
      }


      if ($bValido == false) {
        echo '<div class="alert alert-warning alert-dismissable" role="alert"><button type="button" class="close" data-dismiss="alert">&times;</button>'.$sError.'</div>';
      }
      else{
        include("../php/altas/altaResultado.php");
      }
    }

    if (isset($_SESSION['idJugador'])) {
      $jugador = $_SESSION['idJugador'];
      # code...
    }
    else{
      $jugador = 0;
    }

    //Se comprueba que el usuario esté inscrito. Si la cuenta es 1 entonces va bien, sino falla.
    $comprorbarInscripcion = $con->prepare("SELECT * FROM inscripciones WHERE idCompeticionFK = $idCompeticion AND idJugadorFK = $jugador");
    $comprorbarInscripcion->execute();
    $comprobarI = $comprorbarInscripcion->fetchAll(PDO::FETCH_ASSOC);
    $cuenta = count($comprobarI);

    if ($cuenta == 1) {

    //Comprobamos cual es la última fase del usuario, si no tiene es preliminares si tiene se determina.
    $comprobarFase = $con->prepare("SELECT * FROM resultados WHERE idCompeticionFK = $idCompeticion AND (idGanador = $jugador OR idPerdedor = $jugador) ORDER BY Fase DESC");
    $comprobarFase->execute();

    $row = $comprobarFase->fetchAll(PDO::FETCH_ASSOC);

    if (count($row) == 0) {
      $fase = "Preliminares";
      $numFase = 1;
    }
    else{
      $fase = $row[0]['Fase'];

      //Se añade un número a la fase, después se comprueba si venció.
      switch ($fase) {
        case 1:
          $numFase = 2;
          $fase = "Dieciseisavos";
          break;

        case 2:
          $numFase = 3;
          $fase = "Octavos";
          break;

        case 3:
          $numFase = 4;
          $fase = "Cuartos";
          break;

        case 4:
          $fase = "SemiFinal";
          $numFase = 5;
          break;

        case 5:
          $fase = "Final";
          $numFase = 6;
          break;
      }


      //Se comprueba si el jugador perdió en su anterior partido.
      if ($row[0]['idPerdedor'] == $jugador) {
        $meterResultado = false;
      }
      else{
        $meterResultado = true;
      }
    }

    //Seleccionamos los contrincantes inscritos.
    $conseguirContrincante = $con->prepare("SELECT * FROM inscripciones WHERE idCompeticionFK = $idCompeticion AND NOT idJugadorFK = $jugador");
    $conseguirContrincante->execute();
    $row = $conseguirContrincante->fetchAll(PDO::FETCH_ASSOC);


    $contrincantes = array();

    for ($i=0; $i < count($row); $i++) {
      $idContrincante = $row[$i]['idJugadorFK'];

      //Si el numero de la fase es 1, comprueba que no tenga resultados.
      if ($numFase == 1) {
        $comprobarOponenteRegistro = $con->prepare("SELECT * FROM resultados WHERE idCompeticionFK = $idCompeticion AND (idGanador = $idContrincante OR idPerdedor = $idContrincante)");
        $comprobarOponenteRegistro->execute();
        $comprobarOponenteCuenta = $comprobarOponenteRegistro->fetchAll(PDO::FETCH_ASSOC);

        if (count($comprobarOponenteCuenta) == 0) {
          $nombreContrincante = $con->prepare("SELECT * FROM jugadores WHERE idJugador = $idContrincante");
          $nombreContrincante->execute();
          $nombreLista = $nombreContrincante->fetchAll(PDO::FETCH_ASSOC);
          $nombreC = $nombreLista[0]['nombreJugador'];
          $contrincantes[$i]['idContrincante'] = $idContrincante;
          $contrincantes[$i]['nombreContrincante'] = $nombreC;
        }
      }
      else{

        //Se comprueban los contrincantes de la ronda anterior, si han ganado se añaden a los contrincantes.
          $comprobarContrincante = $con->prepare("SELECT * FROM resultados WHERE idCompeticionFK = $idCompeticion AND (idGanador = $idContrincante OR idPerdedor = $idContrincante) AND Fase = $numFase-1 ORDER BY Fase");
          $comprobarContrincante->execute();
          $perdedorOponente = $comprobarContrincante->fetchAll(PDO::FETCH_ASSOC);

          if (count($perdedorOponente) > 0) {

            if ($perdedorOponente[0]['idGanador'] == $idContrincante) {

              $nombreContrincante = $con->prepare("SELECT * FROM jugadores WHERE idJugador = $idContrincante");
              $nombreContrincante->execute();
              $nombreLista = $nombreContrincante->fetchAll(PDO::FETCH_ASSOC);
              $nombreC = $nombreLista[0]['nombreJugador'];
              $contrincantes[$i]['idContrincante'] = $idContrincante;
              $contrincantes[$i]['nombreContrincante'] = $nombreC;
            }
        }
      }

    }
    if (isset($meterResultado)) {
      # code...
    if ($meterResultado == false) {
      echo '<div class="alert alert-warning alert-dismissable" role="alert">Perdió en su partido anterior.</div>';

    }
  }
  ?>
        <div class="container form">
        <form class="form-horizontal" role="form" method="POST" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-3"></div>
                <div class="col-md-6">
                    <h2>Añadir Resultados</h2>
                    <hr>
                </div>
            </div>
            <div class="row">
              <div class="col-md-3"></div>
              <div class="col-md-6">
                <div class="form-group has-danger">
                  <label  for="txtEspacio">Fase</label>

                    <?php
                    echo '<input type="text" name="txtNombreFase" class="form-control" id="nombreEvento" value="'.$fase.'" readonly="true">';
                    echo '<input type="hidden" name="numFase" value="'.$numFase.'">'

                    ?>
                </div>
              </div>
            </div>
            <div class="row">
                <div class="col-md-3"></div>
                <div class="col-md-6">
                    <div class="form-group has-danger">
                        <label  for="txtEspacio">Resultado</label>

                          <select class="form-control" name="elegirResultado">
                            <option value="0">Victoria</option>
                            <option value="1">Derrota</option>
                          </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3"></div>
                <div class="col-md-6">
                    <div class="form-group">
                      <label for="elegirContrincante">Elegir Contrincante</label>
                          <select class="form-control" name="elegirContrincante">
                            <?php

                            if (count($contrincantes) == 0) {
                              echo "<option value='0'> No hay contrincantes</option>";
                            }
                            else{
                              foreach ($contrincantes as $key => $value) {
                                echo '<option value="'.$value["idContrincante"].'">';
                                echo $value['nombreContrincante'];
                                echo "</option>";
                              }
                          }
                            ?>
                          </select>

                          <?php
                          //print_r($contrincantes);
                          echo '<input type="hidden" name="idCompeticion" value="'.$idCompeticion.'">'
                          ?>
                    </div>
                </div>
            </div>
            <div class="row" style="padding-top: 1rem">
              <?php
                echo '<div class="col-md-3"></div>';
                echo '<div class="col-md-4">';
                if (isset($meterResultado)) {
                  if ($meterResultado == false) {
                    echo '<button type="submit" class="btn" name="btnTransporte" disabled>Añadir resultado</button>';
                      }
                  else{
                    echo '<button type="submit" class="btn btn-success" name="btnTransporte">Añadir Resultado</button>';
                    }
                }
                else{
                  echo '<button type="submit" class="btn btn-success" name="btnTransporte">Añadir Resultado</button>';
                }
              ?>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-danger active" name="btnVolver" formaction="../eventos.php">Volver a Eventos</button>
                </div>
            </div>
        </form>
    <?php
  }
    else{
      if ($_SESSION['tipo'] != "") {
        echo '<div class="alert alert-warning alert-dismissable" role="alert">No estaba inscrito a esta competición, no puede ingresar resultados. <a href="../eventos.php">Vuelva a Eventos</a>.</div>';
    }
    else{
      echo '<div class="alert alert-danger alert-dismissable text-center" role="alert">No estás conectado, <a href="login.php">conéctese</a> para acceder a la funcionalidad de la web.</div>';
    }

    }

    $resultadosGenerales = $con->prepare("SELECT * FROM resultados WHERE idCompeticionFK=$idCompeticion");
    $resultadosGenerales->execute();
    $resultados = $resultadosGenerales->fetchAll(PDO::FETCH_ASSOC);
    if (count($resultados) > 0) {

    //Obtenemos ganadores
    $obtenerGanadores = $con->prepare("SELECT DISTINCT idGanador FROM resultados WHERE idCompeticionFK=$idCompeticion");
    $obtenerGanadores->execute();
    $ganadores = $obtenerGanadores->fetchAll(PDO::FETCH_ASSOC);
    for ($i=0; $i < count($ganadores); $i++) {
      $participantes[] = $ganadores[$i]['idGanador'];
    }


    //obtenemos Perdedores
    $obtenerPerdedores = $con->prepare("SELECT DISTINCT idPerdedor FROM resultados WHERE idCompeticionFK=$idCompeticion");
    $obtenerPerdedores->execute();
    $perdedores = $obtenerPerdedores->fetchAll(PDO::FETCH_ASSOC);
    //print_r($perdedores);
    for ($i=0; $i < count($perdedores); $i++) {
      $perdedor = $perdedores[$i]['idPerdedor'];
      if (!in_array($perdedor, $participantes)) {
        $participantes[] = $perdedores[$i]['idPerdedor'];
      }
    }


    echo "<div class='container'><div class='row' style='padding-top: 1rem'><table class='table table-bordered'>";
    echo "<thead>";
      echo "<tr>";
      echo "<th>";
        echo "Participante";
      echo "</th>";
      echo "<th>";
        echo "Fase Alcanzada";
      echo "</th>";
      echo "<th>";
        echo "Victorias";
      echo "</th>";
      echo "</tr>";
    echo "</thead>";


    for ($i=0; $i < count($participantes); $i++) {
      $participante = $participantes[$i];

      $victorias = $con->prepare("SELECT COUNT(idGanador) FROM resultados WHERE idCompeticionFK = $idCompeticion AND idGanador = $participante");
      $victorias->execute();

      $nombreJugador = $con->prepare("SELECT nombreJugador, emailJugador FROM jugadores WHERE idJugador = $participante");
      $nombreJugador->execute();

      $row = $victorias->fetchAll(PDO::FETCH_ASSOC);

      $fase = $row[0]['COUNT(idGanador)'];
      switch ($fase) {
        case 0:
          $fase = "Preliminares";
          break;

        case 1:
          $fase = "Dieciseisavos";
          break;

        case 2:
          $fase = "Octavos";
          break;

        case 3:
          $fase = "Cuartos";
          break;

        case 4:
          $fase = "Semifinal";
          break;

        case 5:
          $fase = "Final";
          break;

        case 6:
          $fase = "Ganador Final";
          break;
      }

      $data[$i]['Victorias'] = $row[0]['COUNT(idGanador)'];

      $data[$i]['Fase'] = $fase;


      $row = $nombreJugador->fetchAll(PDO::FETCH_ASSOC);
      $data[$i]['Nombre'] = $row[0]['nombreJugador'];
      $data[$i]['Email'] = $row[0]['emailJugador'];

    }
    /*echo "<pre>";
    print_r($ganadores);
    echo "</pre>";*/
    array_multisort($data, SORT_DESC);
    foreach ($data as $key => $value) {
      echo "<tr>";
        echo "<td>";
          echo $value['Nombre'];
        echo " - ";
          echo $value['Email'];
        echo "</td>";
        echo "<td>";
          echo $value['Fase'];
        echo "</td>";
        echo "<td>";
          echo $value['Victorias'];
        echo "</td>";
      echo "</tr>";
    }

    echo "</table></div>";
}
  else{
    echo '<br><div class="alert alert-warning alert-dismissable" role="alert"><button type="button" class="close" data-dismiss="alert">&times;</button>No hay Resultados</div>';
  }
  echo "</div>";

    ?>
  </div>
  </body>
  </html>
