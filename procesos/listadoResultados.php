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
    <?php
if ($_SESSION['tipo'] == 'Administrador') {
if (!isset($_POST['btnCompeticion'])) {
  ?>
  <div class="container form">
  <form class="form-horizontal" role="form" method="POST">
  <div class="row">
      <div class="col-md-3"></div>
      <div class="col-md-6">
          <div class="form-group has-danger">
              <label  for="email">Evento</label>

                  <select class="form-control" name="elegirEvento">
                    <?php
                    $date = date("Y-m-d");
                    //Realización de
                    $sql = $con->prepare("SELECT * FROM competiciones WHERE fechaEvento < '".$date."' ORDER BY fechaEvento DESC");
                    $sql->execute();

                    $row = $sql->fetchAll(PDO::FETCH_ASSOC);

                    for ($i=0; $i < count($row); $i++) {
                      echo '<option value="'.$row[$i]["idCompeticion"].'">';
                      echo $row[$i]['nombreEvento'];
                      echo " - ";
                      echo $row[$i]['fechaEvento'];
                      echo "</option>";
                    }
                    ?>
                  </select>
          </div>
      </div>
  </div>
  <div class="row" style="padding-top: 1rem">
      <div class="col-md-3"></div>
      <div class="col-md-4">
          <button type="submit" class="btn btn-success" name="btnCompeticion">Seleccionar Competicion</button>
      </div>
      <div class="col-md-3">
          <button type="submit" class="btn btn-danger active" name="btnVolver" formaction="index.php"> Volver al Indice</button>
      </div>
  </div>
</form>
</div>
  <?php
}
else{

  $idCompeticion= $_POST['elegirEvento'];
  $usuario = 'root';
$contraRoot = '';

try {
  $con = new PDO('mysql:host=localhost;dbname=club;charset=UTF8', $usuario, $contraRoot);
  $mbd = null;
} catch (PDOException $e) {
  print "¡Error!: " . $e->getMessage() . "<br/>";
  die();
}



  $comprobarResultados = $con->prepare("SELECT * FROM resultados WHERE idCompeticionFK=$idCompeticion");
  $comprobarResultados->execute();
  $cuenta = $comprobarResultados->fetchAll(PDO::FETCH_ASSOC);

  if (count($cuenta) == 0) {
    echo '</div><div class="alert alert-warning alert-dismissable" role="alert">No hay resultados, <a href="index.php">vuelva al inicio</a>.</div>';
  }
  else{
  $content = '<html>';
  $content .= '<head>';
  $content .= '<style>';
  $content .= '</style>';
  $content .= '</head><body>';
  //Resultados para la competición
  $participantes = array();

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


  $content .= "<div class='container'><div class='row' style='padding-top: 1rem'><table class='table table-bordered table-hover'>";
  $content .= "<thead>";
    $content .= "<tr>";
    $content .= "<th>";
      $content .= "Participante";
    $content .= "</th>";
    $content .= "<th>";
      $content .= "Fase Alcanzada";
    $content .= "</th>";
    $content .= "<th>";
      $content .= "Victorias";
    $content .= "</th>";
    $content .= "</tr>";
  $content .= "</thead>";

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

  array_multisort($data, SORT_DESC);
  for ($i=0; $i < count($data); $i++) {
    $content .= "<tr>";
      $content .= "<td>";
        $content .= $data[$i]['Nombre'];
      $content .= " - ";
        $content .= $data[$i]['Email'];
      $content .= "</td>";
      $content .= "<td>";
        $content .= $data[$i]['Fase'];
      $content .= "</td>";
      $content .= "<td>";
        $content .= $data[$i]['Victorias'];
      $content .= "</td>";
    $content .= "</tr>";
  }
  $content .= "</table></div>";

  $content .= '</body></html>';
  echo $content;
  ?>
  <button id="exportButtonR" class="btn btn-lg btn-danger clearfix"><span class="fa fa-file-pdf-o"></span> Exportar a PDF</button>
  <button type="button" name="imprimir" onclick="window.print();" class="btn btn-lg btn-info"><i class="fa fa-print" aria-hidden="true"></i> Imprimir</button></div>
  <?php

  ?>
  <!-- you need to include the shieldui css and js assets in order for the components to work -->
  <link rel="stylesheet" type="text/css" href="http://www.shieldui.com/shared/components/latest/css/light/all.min.css" />
  <script type="text/javascript" src="http://www.shieldui.com/shared/components/latest/js/shieldui-all.min.js"></script>
  <script type="text/javascript" src="http://www.shieldui.com/shared/components/latest/js/jszip.min.js"></script>

  <script type="text/javascript">
      jQuery(function ($) {
          $("#exportButtonR").click(function () {
              // parse the HTML table element having an id=exportTable
              var dataSource = shield.DataSource.create({
                  data: "#exportTable",
                  schema: {
                      type: "table",
                      fields: {
                          Nombre: { type: String },
                          Victorias: { type: Number },
                          Derrotas: { type: Number }
                      }
                  }
              });

              // when parsing is done, export the data to PDF
              dataSource.read().then(function (data) {
                  var pdf = new shield.exp.PDFDocument({
                      author: "Darío",
                      created: new Date()
                  });

                  pdf.addPage("a4", "landscape");

                  pdf.table(
                      50,
                      50,
                      data,
                      [
                          { field: "Nombre", title: "Nombre"},
                          { field: "Victorias", title: "Victorias"},
                          { field: "Derrotas", title: "Derrotas"}
                      ],
                      {
                          margins: {
                              top: 50,
                              left: 50
                          }
                      }
                  );

                  pdf.saveAs({
                      fileName: "Listado de Resultados"
                  });
              });
          });
      });
  </script>
  <?php
  }
}
}
  else{
    echo '<div class="alert alert-warning alert-dismissable" role="alert">No tiene acceso a este característica, <a href="../index.php">vuelva al inicio</a>.</div>';
  }
    ?>
  </body>
  </html>
