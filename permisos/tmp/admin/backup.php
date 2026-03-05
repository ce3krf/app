<?php 
// ************************************************************
// Autor: Marcelo Jiménez
// Fulltrust Software
// https://www.fulltrust.net
// Fecha última actualización: 2024-12-31 10:22:30
// ************************************************************

//error_reporting(E_ALL);
//ini_set('display_errors', 1);

	@session_start();
	include("../conf/db.php");		
    include('functions.php');
    
?>    



<!DOCTYPE html>
<html lang="es">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1">	
<title><?php echo $row_param["parametros_titulo"];?></title>

<link rel="icon" type="image/png" href="../favicon.png">

<script src="js/jquery-3.5.1.min.js"></script>	

    <!-- Enlace a CSS de Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">

    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>



    <script src="functions.js"></script>

    <link rel="stylesheet" type="text/css" href="../css/styles.css"/>    




    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <style>
#pprint {
    margin: 10px !important;
}
</style>    

<script>
    async function pdf() {
        const element = document.getElementById('pprint');
        
        // Capturar el contenido del elemento con html2canvas
        const canvas = await html2canvas(element, {
            scale: window.devicePixelRatio, // Escala alta para mayor fidelidad
            useCORS: true, // Manejo de recursos externos
            scrollY: 0 // Evitar desplazamientos no deseados
        });

        // Convertir el canvas a imagen
        const imgData = canvas.toDataURL('image/jpeg', 0.98);

        // Crear una instancia de jsPDF
        const { jsPDF } = window.jspdf;
        const pdf = new jsPDF({
            orientation: 'landscape', // Orientación del PDF
            unit: 'in',
            format: 'letter' // Formato de la página
        });

        // Obtener las dimensiones del canvas y calcular la escala para el PDF
        const pdfWidth = pdf.internal.pageSize.getWidth();
        const pdfHeight = pdf.internal.pageSize.getHeight();
        const imgWidth = canvas.width / 96; // Convertir píxeles a pulgadas
        const imgHeight = canvas.height / 96;
        const aspectRatio = imgWidth / imgHeight;

        let finalWidth = pdfWidth;
        let finalHeight = pdfWidth / aspectRatio;

        if (finalHeight > pdfHeight) {
            finalHeight = pdfHeight;
            finalWidth = pdfHeight * aspectRatio;
        }

        // Agregar la imagen al PDF
        pdf.addImage(imgData, 'JPEG', 0, 0, finalWidth, finalHeight);

        // Agregar numeración de páginas (si se multiplica en varias)
        const totalPages = pdf.internal.getNumberOfPages();
        const pageWidth = pdf.internal.pageSize.getWidth();
        const pageHeight = pdf.internal.pageSize.getHeight();

        for (let i = 1; i <= totalPages; i++) {
            pdf.setPage(i);
            pdf.setFontSize(10);
            pdf.text(`Página ${i} de ${totalPages}`, pageWidth / 2, pageHeight - 0.5, { align: 'center' });
        }

        // Descargar el PDF
        pdf.save('graficos.pdf');
    }
</script>


</head>
<body>

<?php include("header.php");?>

    <div class="container-fluid">
        <div class="row">

        <?php include("sidebar.php");?>

            <!-- Área de trabajo central -->
            <main role="main" class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h5>HACER UN RESPALDO DE LA BASE DE DATOS</h5>
                </div>
                <div class="container">

                </div>
                







<div style="height: 50px; clear:both"></div>




<?php if($row_tabla["usuarios_profile"] == 'ADMINISTRADOR'){ ?>
<form id="form1" name="form1" method="post" action="backup.php">
<input type="hidden" name="accion" value="backup">
<div class="row">
    <div class="col text-center">
        <button type="submit" class="btn btn-secondary" >Generar y descargar respaldo</button>
    </div>  
</div>
<form>
<?php } else { ?>
    <h3>Acceso denegado</h3>
<?php } ?>




<div style="height: 50px;"></div>









                </div>
            </main>
        </div>
    </div>


<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'backup') {

    $backupFile = "backup_" . date("Y-m-d_H-i-s") . ".sql";
    $zipFile = $backupFile . ".zip";
    
    $sqlScript = "SET FOREIGN_KEY_CHECKS=0;\n";
    $tablesResult = $db->query("SHOW TABLES");
    
    while ($row = $tablesResult->fetch_array()) {
        $table = $row[0];
        
        // Generar DROP TABLE
        $sqlScript .= "DROP TABLE IF EXISTS `$table`;\n";
        
        // Generar CREATE TABLE
        $createTableResult = $db->query("SHOW CREATE TABLE `$table`");
        $createTableRow = $createTableResult->fetch_array();
        $sqlScript .= $createTableRow[1] . ";\n\n";
        
        // Generar INSERT INTO con datos
        $dataResult = $db->query("SELECT * FROM `$table`");
        while ($dataRow = $dataResult->fetch_assoc()) {
            $values = array_map(function($value) use ($db) {
                return isset($value) ? "'" . $db->real_escape_string($value) . "'" : "NULL";
            }, array_values($dataRow));
            
            $sqlScript .= "INSERT INTO `$table` VALUES(" . implode(", ", $values) . ");\n";
        }
        $sqlScript .= "\n";
    }
    
    $sqlScript .= "SET FOREIGN_KEY_CHECKS=1;\n";
    
    // Guardar el archivo SQL
    file_put_contents($backupFile, $sqlScript);
    
    // Crear un archivo ZIP
    $zip = new ZipArchive();
    if ($zip->open($zipFile, ZipArchive::CREATE) === TRUE) {
        $zip->addFile($backupFile);
        $zip->close();
    }
    
    // Eliminar el archivo SQL original
    unlink($backupFile);
    
    // Generar enlace de descarga
    ?><script>window.location='<?php echo $zipFile;?>';</script><?php
    //exit;
} else {
    //die("Acceso no autorizado.");
}
?>


<?php include("footer.php");?>

    <!-- Enlace a JavaScript de Bootstrap 5 y dependencias -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js" integrity="sha384-cuYeSxntonz0PPNlHhBs68uyIAVpIIOZZ5JqeqvYYIcEL727kskC66kF92t6Xl2V" crossorigin="anonymous"></script>
    <script src="app.js"></script>
</body>
</html>
