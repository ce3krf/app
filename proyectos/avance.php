<?php 
// ************************************************************
// Autor: Marcelo Jiménez
// Fulltrust Software
// https://www.fulltrust.net
// Fecha última actualización: 2025-10-09
// ************************************************************
?>
<?php
@session_start();
include("../conf/db.php");		
include('functions.php');

$sql = "set names utf8";
if(!$tabla = $db->query($sql)){
    die('Hay un error [' . $db->error . ']');
}   

include("inc_tabla.php");

// Obtener las etapas dinámicamente
$sql="SELECT etapas_id, etapas_descripcion FROM etapas ORDER BY etapas_id";
if(!$etapas_result = $db->query($sql)){
    die('Hay un error [' . $db->error . ']');
}
$etapas_headers = [];
while($row_e = $etapas_result->fetch_assoc()) {
    $etapas_headers[$row_e['etapas_id']] = $row_e['etapas_descripcion'];
}
$num_etapas = count($etapas_headers);
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1">	
<title><?php echo $row_param["parametros_titulo"];?></title>

<link rel="icon" type="image/png" href="../favicon.png">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js" integrity="sha512-GsLlZN/3F2ErC5ifS5QtgpiJtWd43JWSuIgh7mbzZ8zBps+dvLusV+eNQATqgA/HdeKFVgA5v3S/cIrLF7QnIg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<link rel="stylesheet" type="text/css" href="css/styles.css"/>		

<style>
:root {
  --raya: #457b9d;
}

table {
    font-size:12px !important;
}

.td_estados{
    background-color: #457b9d;
    color:white;
    font-weight: bold;
}

.td_titulo{
    background-color: #8ecae6;
    color:black;
    font-weight: bold;
    border-bottom-style:solid;
    border-bottom-width: 1px;
    border-bottom-color: var(--raya);    
}

.td_totales{
    background-color: #8ecae6;
    color:black;
    font-weight: bold;
}

.tr_data {
    border-bottom-style:solid;
    border-bottom-width: 1px;
    border-bottom-color: var(--raya);
}

.raya_r {
    border-right-style:solid;
    border-right-width: 1px;
    border-right-color: var(--raya);
}

#pprint {
    margin: 10px !important;
}
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<script>
async function pdf() {
    const element = document.getElementById('pprint');
    
    const canvas = await html2canvas(element, {
        scale: window.devicePixelRatio,
        useCORS: true,
        scrollY: 0
    });

    const imgData = canvas.toDataURL('image/jpeg', 0.98);

    const { jsPDF } = window.jspdf;
    const pdf = new jsPDF({
        orientation: 'landscape',
        unit: 'in',
        format: 'letter'
    });

    const pdfWidth = pdf.internal.pageSize.getWidth();
    const pdfHeight = pdf.internal.pageSize.getHeight();
    const imgWidth = canvas.width / 96;
    const imgHeight = canvas.height / 96;
    const aspectRatio = imgWidth / imgHeight;

    let finalWidth = pdfWidth;
    let finalHeight = pdfWidth / aspectRatio;

    if (finalHeight > pdfHeight) {
        finalHeight = pdfHeight;
        finalWidth = pdfHeight * aspectRatio;
    }

    pdf.addImage(imgData, 'JPEG', 0, 0, finalWidth, finalHeight);

    const totalPages = pdf.internal.getNumberOfPages();
    const pageWidth = pdf.internal.pageSize.getWidth();
    const pageHeight = pdf.internal.pageSize.getHeight();

    for (let i = 1; i <= totalPages; i++) {
        pdf.setPage(i);
        pdf.setFontSize(10);
        pdf.text(`Página ${i} de ${totalPages}`, pageWidth / 2, pageHeight - 0.5, { align: 'center' });
    }

    pdf.save('estado_avance.pdf');
}
</script>

</head>
<body>

<?php include("header.php");?>

<div class="container-fluid">


<!-- Formulario de filtros -->
<form method="get" target="_self">
<div class="row">
<div class="col">

<select class="form-select" name="sector" id="sector" style="width:150px; float:left">
    <option value="">Sectores (todos)</option>
    <?php if(isset($row_sectores) && $row_sectores) { ?>
    <?php do { ?>
    <option <?php if(isset($_GET["sector"]) && $row_sectores["sector"]==$_GET["sector"]){ echo "selected";};?> value="<?php echo $row_sectores["sector"];?>"><?php echo $row_sectores["sector"];?></option>
    <?php } while ( $row_sectores = $sectores->fetch_assoc() ); ?>
    <?php } ?>
</select>  

<select class="form-select" name="tipo" id="tipo" style="width:150px;float:left">
    <option value="">Tipos (todos)</option>
    <?php if(isset($row_tipos) && $row_tipos) { ?>
    <?php do { ?>
    <option <?php if(isset($_GET["tipo"]) && $row_tipos["tipo"]==$_GET["tipo"]){ echo "selected";};?> value="<?php echo $row_tipos["tipo"];?>"><?php echo $row_tipos["tipo"];?></option>
    <?php } while ( $row_tipos = $tipos->fetch_assoc() ); ?>
    <?php } ?>
</select>        

<button type="submit" class="btn btn-primary">Aplicar</button>
<button onclick="pdf();" type="button" class="btn btn-secondary">Exportar como PDF</button>

</div>
</div>

<div style="height:15px; clear:both"></div>

</form>

<?php
// CORRECCIÓN: Usar 'area' en lugar de 'sector' porque así se llama la columna en la tabla 'tabla'
$sql = "SELECT * FROM tabla ORDER BY area, tipo";
if(!$tabla = $db->query($sql)){
    die('Hay un error [' . $db->error . ']');
}   
$row_tabla = $tabla->fetch_assoc();
?>    

<div id="pprint">
<table width="100%" border="0" cellspacing="0" cellpadding="5">
  <tbody>
    <tr>
      <td></td>
      <td></td>
      <td class="td_estados" colspan="<?php echo ($num_etapas * 2);?>" align="center">ETAPAS</td>
      <td colspan="2" align="center"></td>
    </tr>
    <tr>
      <td class="td_titulo">SECTOR</td>
      <td class="td_titulo raya_r">TIPO</td>
      
      <?php 
      // Generar headers dinámicamente
      foreach($etapas_headers as $etapa_id => $etapa_nombre) { 
      ?>
      <td class="td_titulo raya_r" colspan="2" align="center"><?php echo strtoupper($etapa_nombre);?></td>
      <?php } ?>
      
      <td class="td_titulo" colspan="2" align="center">TOTAL</td>
    </tr>

<?php 
$num=0;
// Inicializar array de totales dinámicamente
$totales = array_fill(1, 8, 0);
?>

<?php if($row_tabla) { ?>
<?php do { ?>  
 
<?php
$num++;    
if (($num % 2) == 0) {
    $bg="#f1faee";
} else {
    $bg="#ffffff";
}    
?>

    <tr style="background-color: <?php echo $bg;?>">
      <td class="tr_data"><?php echo $row_tabla["area"];?></td>
      <td class="tr_data raya_r"><?php echo $row_tabla["tipo"];?></td>
      
      <?php 
      // Mostrar valores dinámicamente según las etapas existentes
      for($i=1; $i<=8; $i++) { 
      ?>
        <?php if (isset($row_tabla["e".$i."v"]) && $row_tabla["e".$i."v"] > 0){ ?>
        <td class="tr_data" align="center"><?php echo number_format($row_tabla["e".$i."v"], 0);?></td>
        <?php } else { ?>
        <td class="tr_data" align="center"></td>
        <?php } ?>

        <?php if (isset($row_tabla["e".$i."p"]) && $row_tabla["e".$i."p"] > 0){ ?>
        <td class="tr_data raya_r" align="center"><?php echo number_format($row_tabla["e".$i."p"], 2)."%";?></td>
        <?php } else { ?>
        <td class="tr_data raya_r" align="center"></td>
        <?php } ?>
        
        <?php
        // Acumular totales
        if(isset($row_tabla["e".$i."v"])) {
            $totales[$i] += $row_tabla["e".$i."v"];
        }
        ?>
      <?php } ?>

      <td class="tr_data" align="center"><?php echo number_format($row_tabla["tt"], 0);?></td>
      <td class="tr_data" align="center"><?php echo number_format($row_tabla["tp"], 2)."%";?></td>
    </tr>

<?php } while( $row_tabla = $tabla->fetch_assoc() ); ?>

<?php
// Calcular total general y porcentajes
$total_general = array_sum($totales);

$porcentajes = [];
if($total_general > 0) {
    for($i=1; $i<=8; $i++) {
        $porcentajes[$i] = ($totales[$i] / $total_general) * 100;
    }
    $total_porcentaje = array_sum($porcentajes);
} else {
    $porcentajes = array_fill(1, 8, 0);
    $total_porcentaje = 0;
}
?>

    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      
      <?php for($i=1; $i<=8; $i++) { ?>
      <td class="td_totales" align="center"><?php echo number_format($totales[$i], 0);?></td>
      <td class="td_totales" align="center"><?php echo number_format($porcentajes[$i], 2)."%";?></td>
      <?php } ?>
      
      <td class="td_totales" align="center"><?php echo number_format($total_general, 0);?></td>
      <td class="td_totales" align="center"><?php echo number_format($total_porcentaje, 2)."%";?></td>      
    </tr>
<?php } else { ?>
    <tr>
      <td colspan="<?php echo (4 + ($num_etapas * 2));?>" align="center" style="padding:20px">No hay datos para mostrar</td>
    </tr>
<?php } ?>
  </tbody>
</table>  
</div>  

<div style="height:20px; clear:both"></div>

<div class="row">
    <div class="col text-center">
        <button onclick="pdf();" type="button" class="btn btn-secondary">Exportar como PDF</button>
    </div>  
</div>

<div style="height:20px; clear:both"></div>



<!-- Enlace a JavaScript de Bootstrap 5 y dependencias -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js" integrity="sha384-cuYeSxntonz0PPNlHhBs68uyIAVpIIOZZ5JqeqvYYIcEL727kskC66kF92t6Xl2V" crossorigin="anonymous"></script>
<script src="app.js"></script>
</body>
</html>