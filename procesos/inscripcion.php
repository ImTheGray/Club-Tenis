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

  if ($_SESSION['tipo'] != "") {
    # code...
    if (isset($_GET['idCompeticion'])) {
      $idCompeticion = $_GET['idCompeticion'];
          //echo "<script>alert($idCompeticion)</script>";
          # code...
    }
    else{
      $idCompeticion = $_POST['idCompeticion'];

      //echo "<script>alert($idCompeticion)</script>";

    }
    if (isset($_POST['btnBorrarse'])) {
      include("../php/Borrado/borrarInscripcion.php");

    }
    if (isset($_POST["btnInscripcion"])) {
      $comentario = $_POST['txtComentario'];

      $bValido = true;
      $sError = "";


      if (preg_match("/^[a-zA-z\s\ñ\Ñ\w\d\D]{5,50}$/", $comentario)) {
      }
        else{

        $sError .= "El comentario debe tener una longitud mínima de 5 caracteres y un máximo de 50.";
        $bValido = false;
      }

      if ($bValido == false) {
        echo '<div class="alert alert-warning alert-dismissable" role="alert"><button type="button" class="close" data-dismiss="alert">&times;</button>'.$sError.'</div>';
      }
      else{
        include("../php/altas/inscripcionCompeticion.php");
      }
    }



    //Realización de
    $jugador = $_SESSION['idJugador'];
    $sql = $con->prepare("SELECT * FROM inscripciones WHERE idCompeticionFK=$idCompeticion AND idJugadorFK = $jugador");
    $sql->execute();

    $cuenta = $sql->rowCount();

    if ($cuenta>=1) {

      $nombreEventoSql = $con->prepare("SELECT nombreEvento FROM competiciones WHERE idCompeticion = $idCompeticion");
      $nombreEventoSql->execute();
      //Parte de borrarse de la competición
      ?>

      <div class="container form">
      <form class="form-horizontal" role="form" method="POST" enctype="multipart/form-data">
          <div class="row">
              <div class="col-md-3"></div>
              <div class="col-md-6">
                  <h2>Borrarse de Competicion</h2>
                  <hr>
              </div>
          </div>
          <div class="row">
              <div class="col-md-3"></div>
              <div class="col-md-6">
                  <div class="form-group has-danger">
                      <label  for="txtNombreEvento">Nombre Evento</label>


                                 <?php
                                  $row = $nombreEventoSql->fetchAll(PDO::FETCH_ASSOC);
                                  $nombreEvento = $row[0]['nombreEvento'];
                                  echo '<input type="text" name="txtNombreEvento" class="form-control" id="nombreEvento"
                                         placeholder="NombreEvento de prueba" autofocus readonly="true" value="'.$nombreEvento.'">';
                                  echo '<input type="hidden" name="idCompeticion" value="'.$idCompeticion.'"/>';
                                 ?>
                  </div>
              </div>
          </div>
          <div class="row" style="padding-top: 1rem">
              <div class="col-md-3"></div>
              <div class="col-md-4">
                  <button type="submit" class="btn btn-warning" name="btnBorrarse">Borrarse</button>
              </div>
              <div class="col-md-3">
                  <button type="submit" class="btn btn-danger active" name="btnVolver" formaction="../eventos.php"> Volver a Eventos</button>
              </div>
          </div>
      </form>

      <?php
    }
    else{
      if (isset($_GET['idCompeticion'])) {
        $idCompeticion = $_GET['idCompeticion'];
            //echo "<script>alert($idCompeticion)</script>";
      }
      else{
        $idCompeticion = $_POST['idCompeticion'];

        //echo "<script>alert($idCompeticion)</script>";

      }

      $nombreEventoSql = $con->prepare("SELECT nombreEvento FROM competiciones WHERE idCompeticion = $idCompeticion");
      $nombreEventoSql->execute();

      $transporteSql = $con->prepare("SELECT * FROM transporte INNER JOIN jugadores ON transporte.idJugadorFK=jugadores.idJugador WHERE idCompeticionFK=$idCompeticion AND espacioDisponible > 0");
      $transporteSql->execute();

      $comentariosSql = $con->prepare("SELECT * FROM inscripciones INNER JOIN jugadores ON inscripciones.idJugadorFK=jugadores.idJugador WHERE idCompeticionFK=$idCompeticion");
      $comentariosSql->execute();
  ?>
        <div class="container form">
        <form class="form-horizontal" role="form" method="POST" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-3"></div>
                <div class="col-md-6">
                    <h2>Inscripción</h2>
                    <hr>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3"></div>
                <div class="col-md-6">
                    <div class="form-group has-danger">
                        <label  for="txtNombreEvento">Nombre Evento</label>


                                   <?php
                                    $row = $nombreEventoSql->fetchAll(PDO::FETCH_ASSOC);
                                    for ($i=0; $i < count($row) ; $i++) {
                                      $nombreEvento = $row[$i]['nombreEvento'];
                                    }
                                    echo '<input type="text" name="txtNombreEvento" class="form-control" id="nombreEvento"
                                           placeholder="NombreEvento de prueba" autofocus readonly="true" value="'.$nombreEvento.'">';
                                           echo '<input type="hidden" name="idCompeticion" value="'.$idCompeticion.'"/>';
                                   ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3"></div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label  for="elegirTransporte">Transporte</label>

                            <select class="form-control" name="elegirTransporte">
                              <?php
                              $cuenta = $transporteSql->rowCount();

                              if ($cuenta>0) {
                                $row = $transporteSql->fetchAll(PDO::FETCH_ASSOC);

                                //Parte de borrarse de la competición
                              for ($i=0; $i < count($row); $i++) {
                                echo '<option value="'.$row[$i]["idTransporte"].'">';
                                echo "Jugador: ";
                                echo $row[$i]['nombreJugador'];
                                echo "  | Espacio disponible: ";
                                echo $row[$i]['espacioDisponible'];
                                echo "</option>";
                              }
                            }
                            else{
                              echo '<option value="0">';
                              echo "Ir por su cuenta";
                              echo "</option>";
                            }
                              ?>
                            </select>
                    </div>
                </div>
            </div>
            <div class="row">
              <div class="col-md-3"></div>
              <div class="col-md-6">
                <div class="form-group">
                    <label  for="txtComentario">Comentario</label>

                        <textarea name="txtComentario" class="form-control" id="txtComentario"
                               placeholder="Comentario" rows="2"></textarea>
                    </div>
                </div>
              </div>
            </div>
            <div class="row" style="padding-top: 1rem">
                <div class="col-md-3"></div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-success" name="btnInscripcion">Inscribirse</button>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-danger active" name="btnVolver" formaction="../eventos.php"> Volver a Eventos</button>
                </div>
            </div>
        </form>
        <div class="row">
          <div class="col-md-3"></div>
          <div class="col-md-6">
            <?php

            $cuenta = $comentariosSql->rowCount();

            if ($cuenta>1) {
              ?>
              <br><div class="row"><table class="table table-bordered table-hover">
                <thead>
                  <tr>
                  <th>Usuario</th>
                  <th>Comentario</th>
                </tr>
                </thead>
                <tbody>

              <?php
            $row = $comentariosSql->fetchAll(PDO::FETCH_ASSOC);
            for ($i=0; $i < count($row); $i++) {
              echo '<tr><td>';
              echo $row[$i]['nombreJugador'];
              echo "</td><td>";
              echo $row[$i]['Comentario'];
              echo "</td></tr>";
            }
          }
          else{
            echo '<br><div class="alert alert-info alert-dismissable" role="alert">No hay comentarios de otros jugadores.</div>';
          }
            ?>
          </tbody>
        </table>
          </div>
          </div>
        </div>
    <?php
  }
}
else{
  echo '<div class="alert alert-danger alert-dismissable text-center" role="alert">No estás conectado, <a href="login.php">conéctese</a> para acceder a la funcionalidad de la web.</div>';
}
    ?>
  </div>
</body>
</html>
