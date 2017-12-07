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

    $con = conexion();

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
</head>
<body>
  <?php

  include("../php/navbar.php");
  if ($_SESSION['tipo'] == 'Administrador') {

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
    $con = conexion();

    $jugador = $_SESSION['idJugador'];
    $comprorbarInscripcion = $con->prepare("SELECT * FROM inscripciones WHERE idCompeticionFK = $idCompeticion AND idJugadorFK = $jugador");
    $comprorbarInscripcion->execute();
    $comprobarI = $comprorbarInscripcion->fetchAll(PDO::FETCH_ASSOC);
    $cuenta = count($comprobarI);

    if ($cuenta == 1) {
      # code...

    $jugador = $_SESSION['idJugador'];
    $comprobarFase = $con->prepare("SELECT * FROM resultados WHERE idCompeticionFK = $idCompeticion AND (idGanador = $jugador OR idPerdedor = $jugador) ORDER BY Fase");
    $comprobarFase->execute();

    $row = $comprobarFase->fetchAll(PDO::FETCH_ASSOC);

    if (count($row) == 0) {
      $fase = "Preliminares";
      $numFase = 1;
    }
    else{
      $fase = $row[0]['Fase'];

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
          $fase = "SemiFinal";
          break;

        case 4:
          $fase = "Final";
          $numFase = 5;
          break;
      }


      if ($row[0]['idPerdedor'] == $jugador) {
        $meterResultado = false;
      }
      else{
        $meterResultado = true;
      }
    }

    $conseguirContrincante = $con->prepare("SELECT * FROM inscripciones WHERE idCompeticionFK = $idCompeticion AND NOT idJugadorFK = $jugador");
    $conseguirContrincante->execute();
    $row = $conseguirContrincante->fetchAll(PDO::FETCH_ASSOC);

    $contrincantes = array();
    for ($i=0; $i < count($row); $i++) {
      $idContrincante = $row[$i]['idJugadorFK'];

      $comprobarContrincante = $con->prepare("SELECT * FROM resultados WHERE idCompeticionFK = $idCompeticion AND (idGanador = $idContrincante OR idPerdedor = $idContrincante) AND Fase = $numFase ORDER BY Fase");
      $comprobarContrincante->execute();
      //print_r($comprobarContrincante);

      $oponentes = $comprobarContrincante->fetchAll(PDO::FETCH_ASSOC);

      if (count($oponentes) == 0) {
        $comprobarContrincante = $con->prepare("SELECT * FROM resultados WHERE idCompeticionFK = $idCompeticion AND (idGanador = $idContrincante OR idPerdedor = $idContrincante) AND Fase = $numFase-1 ORDER BY Fase");
        $comprobarContrincante->execute();
        $perdedorOponente = $comprobarContrincante->fetchAll(PDO::FETCH_ASSOC);

        if ($perdedorOponente[0]['idPerdedor'] != $idContrincante) {


        $nombreContrincante = $con->prepare("SELECT * FROM jugadores WHERE idJugador = $idContrincante");
        $nombreContrincante->execute();
        $nombreLista = $nombreContrincante->fetchAll(PDO::FETCH_ASSOC);
        $nombreC = $nombreLista[0]['nombreJugador'];
        $contrincantes[$i]['idContrincante'] = $idContrincante;
        $contrincantes[$i]['nombreContrincante'] = $nombreC;
      }

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
                  <label class="sr-only" for="txtEspacio">Fase</label>
                  <div class="input-group mb-2 mr-sm-2 mb-sm-0">
                    <?php
                    echo '<input type="text" name="txtNombreFase" class="form-control" id="nombreEvento" value="'.$fase.'" readonly="true">';
                    echo '<input type="hidden" name="numFase" value="'.$numFase.'">'

                    ?>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
                <div class="col-md-3"></div>
                <div class="col-md-6">
                    <div class="form-group has-danger">
                        <label class="sr-only" for="txtEspacio">Resultado</label>
                        <div class="input-group mb-2 mr-sm-2 mb-sm-0">
                          <select class="form-control" name="elegirResultado">
                            <option value="0">Victoria</option>
                            <option value="1">Derrota</option>
                          </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3"></div>
                <div class="col-md-6">
                    <div class="form-group">
                        <div class="input-group mb-2 mr-sm-2 mb-sm-0">
                          <select class="form-control" name="elegirContrincante">
                            <?php

                            if (count($contrincantes) == 0) {
                              echo "<option value='0'> No hay contrincantes</option>";
                            }
                            else{
                            for ($i=0; $i < count($contrincantes); $i++) {
                              echo '<option value="'.$contrincantes[$i]["idContrincante"].'">';
                              echo $contrincantes[$i]['nombreContrincante'];
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
            </div>
            <div class="row" style="padding-top: 1rem">
                <div class="col-md-3"></div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-success" name="btnTransporte">Añadir Resultado</button>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-danger active" name="btnVolver" formaction="../eventos.php">Volver a Eventos</button>
                </div>
            </div>
        </form>
    <?php

    //Resultados para la competición
    $participantes = array();

    //Obtenemos ganadores
    $obtenerGanadores = $con->prepare("SELECT DISTINCT idGanador FROM resultados WHERE idCompeticionFK=$idCompeticion ORDER BY COUNT(idGanador)");
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


    echo "<div class='row' style='padding-top: 1rem'><table class='table table-bordered'>";
    echo "<thead>";
      echo "<tr>";
      echo "<th>";
        echo "Participante";
      echo "</th>";
      echo "<th>";
        echo "Victorias";
      echo "</th>";
      echo "<th>";
        echo "Derrotas";
      echo "</th>";
      echo "</tr>";
    echo "</thead>";

    for ($i=0; $i < count($participantes); $i++) {
      $participante = $participantes[$i];
      $victorias = $con->prepare("SELECT COUNT(idGanador) FROM resultados WHERE idCompeticionFK = $idCompeticion AND idGanador = $participante");
      $victorias->execute();
      $derrotas = $con->prepare("SELECT COUNT(idPerdedor) FROM resultados WHERE idCompeticionFK = $idCompeticion AND idPerdedor = $participante");
      $derrotas->execute();

      $nombreJugador = $con->prepare("SELECT nombreJugador FROM jugadores WHERE idJugador = $participante");
      $nombreJugador->execute();
      //$sql = $con->prepare("SELECT COUNT(resultados.idGanador), resultados.idGanador, COUNT(resultados.idPerdedor), resultados.idPerdedor, competiciones.nombreEvento, jugadores.nombreJugador FROM ((resultados INNER JOIN competiciones ON resultados.idCompeticionFK = competiciones.idCompeticion) INNER JOIN jugadores ON resultados.idPerdedor = jugadores.idJugador OR resultados.idGanador = jugadores.idJugador) WHERE idCompeticionFK = $idCompeticion AND idGanador=$participante");
      //$sql->execute();

      $row = $victorias->fetchAll(PDO::FETCH_ASSOC);
      $data[$i]['Victorias'] = $row[0]['COUNT(idGanador)'];

      $row = $derrotas->fetchAll(PDO::FETCH_ASSOC);
      $data[$i]['Derrotas'] = $row[0]['COUNT(idPerdedor)'];

      $row = $nombreJugador->fetchAll(PDO::FETCH_ASSOC);
      $data[$i]['Nombre'] = $row[0]['nombreJugador'];


    }
    array_multisort($data, SORT_DESC);
    for ($i=0; $i < count($data); $i++) {
      echo "<tr>";
        echo "<td>";
        echo $data[$i]['Nombre'];
        echo "</td>";
        echo "<td>";
        echo $data[$i]['Victorias'];
        echo "</td>";
        echo "<td>";
        echo $data[$i]['Derrotas'];
        echo "</td>";
      echo "</tr>";
    }
    echo "</table></div>";

  }
  else{
    echo '<div class="alert alert-warning alert-dismissable" role="alert">No estaba inscrito a esta competición, <a href="../eventos.php">vuelva a Eventos</a>.</div>';

  }
  }
  else{
    echo '<div class="alert alert-warning alert-dismissable" role="alert">No tiene acceso a este característica, <a href="../eventos.php">vuelva a Eventos</a>.</div>';
  }

  function conexion(){
    $usuario = 'root';
    $contraRoot = '';

    try {
      $con = new PDO('mysql:host=localhost;dbname=club', $usuario, $contraRoot);
      $mbd = null;
    } catch (PDOException $e) {
        print "¡Error!: " . $e->getMessage() . "<br/>";
        die();
    }
    return $con;
  }
    ?>
  </div>
  </body>
  </html>
