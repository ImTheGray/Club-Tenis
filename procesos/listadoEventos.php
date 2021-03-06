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
$usuario = 'root';
$contraRoot = '';

try {
$con = new PDO('mysql:host=localhost;dbname=club;charset=UTF8', $usuario, $contraRoot);
$mbd = null;
} catch (PDOException $e) {
print "¡Error!: " . $e->getMessage() . "<br/>";
die();
}


  //Realización de
  $sql = $con->prepare("SELECT * FROM competiciones");
  $sql->execute();
  $content = '<html>';
  $content .= '<head>';
  $content .= '<style>';
  $content .= '</style>';
  $content .= '</head><body>';
  $content .= "<div class='container'><div class='row' style='padding-top: 1rem'><table id='exportTable' class='table table-bordered table-hover'>";
  $content .= "<thead>";
    $content .= "<tr>";
    $content .= "<th>";
      $content .= "Nombre";
    $content .= "</th>";
    $content .= "<th>";
      $content .= "Fecha";
    $content .= "</th>";
    $content .= "<th>";
      $content .= "Descripcion";
    $content .= "</th>";
    $content .= "</tr>";
  $content .= "</thead>";
  $content .= "<tbody>";

  $row = $sql->fetchAll(PDO::FETCH_ASSOC);

  for ($i=0; $i < count($row); $i++) {
    $content .= "<tr>";
      $content .= "<td>";
        $content .= $row[$i]['nombreEvento'];
      $content .= "</td>";
      $content .= "<td>";
        $content .= date("Y/m/d", strtotime($row[$i]['fechaEvento']));
      $content .= "</td>";
      $content .= "<td>";
        $content .= $row[$i]['descripcion'];
      $content .= "</td>";
    $content .= "</tr>";
  }
  $content .= "</tbody>";
  $content .= "</table></div>";
  $content .= '</body></html>';

  echo $content;
?>
<button id="exportButton" class="btn btn-lg btn-danger clearfix"><span class="fa fa-file-pdf-o"></span> Exportar a PDF</button>
<button type="button" name="imprimir" onclick="window.print();" class="btn btn-lg btn-info"><i class="fa fa-print" aria-hidden="true"></i> Imprimir</button>
  <!-- you need to include the shieldui css and js assets in order for the components to work -->
  <link rel="stylesheet" type="text/css" href="http://www.shieldui.com/shared/components/latest/css/light/all.min.css" />
  <script type="text/javascript" src="http://www.shieldui.com/shared/components/latest/js/shieldui-all.min.js"></script>
  <script type="text/javascript" src="http://www.shieldui.com/shared/components/latest/js/jszip.min.js"></script>

  <script type="text/javascript">
      jQuery(function ($) {
          $("#exportButton").click(function () {
              // parse the HTML table element having an id=exportTable
              var dataSource = shield.DataSource.create({
                  data: "#exportTable",
                  schema: {
                      type: "table",
                      fields: {
                          Nombre: { type: String },
                          Fecha: { type: String },
                          Descripcion: { type: String }
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
                          { field: "Nombre", title: "Nombre",},
                          { field: "Fecha", title: "Fecha"},
                          { field: "Descripcion", title: "Descripcion"},
                      ],
                      {
                          margins: {
                              top: 50,
                              left: 50
                          }
                      }
                  );

                  pdf.saveAs({
                      fileName: "Listado de Eventos"
                  });
              });
          });
      });
  </script>
  <?php
  }
  else{
    echo '<div class="alert alert-warning alert-dismissable" role="alert">No tiene acceso a este característica, <a href="../index.php">vuelva al inicio</a>.</div>';
  }
    ?>
  </body>
  </html>
