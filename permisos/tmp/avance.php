<?php 
// ************************************************************
// Autor: Marcelo Jiménez
// Fulltrust Software
// https://www.fulltrust.net
// Fecha última actualización: 2024-09-10 14:52:13
// ************************************************************
?>
<?php

//error_reporting(E_ALL);
//ini_set('display_errors', '1');


@session_start();
include("conf/db.php");		
include('functions.php');


$sql = "set names utf8";
if(!$tabla = $db->query($sql)){
    die('Hay un error [' . $db->error . ']');
}   


include("inc_tabla.php");

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

<link rel="stylesheet" type="text/css" href="../css/styles.css"/>		





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
</style>





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


                <div class="row top-info-div">
                    <div class="col">
                        <h5>ESTADO DE AVANCE</h5>
                    </div>
                    <div class="col text-end">
                    </div>                        
                </div>

                <div class="container">


                    <div class="row">
                        

<!-- ****************************************** -->

<form method="post" target="_self">
<div class="row">
<div class="col">

<select class="form-select" name="area" id="area" style="width:150px; float:left">
        <option value="">Ámbito (todas)</option>
        <?php do { ?>
        <option <?php if($row_areas["area"]==$_GET["area"]){ echo "selected";};?> value="<?php echo $row_areas["area"];?>"><?php echo $row_areas["area"];?></option>
        <?php } while ( $row_areas = $areas->fetch_assoc() ); ?>
</select>  


<select class="form-select" name="tipo" id="tipo" style="width:150px;float:left">
        <option value="">Tipos (todos)</option>
        <?php do { ?>
        <option <?php if($row_tipos["tipo"]==$_GET["tipo"]){ echo "selected";};?> value="<?php echo $row_tipos["tipo"];?>"><?php echo $row_tipos["tipo"];?></option>
        <?php } while ( $row_tipos = $tipos->fetch_assoc() ); ?>
</select>        

<button onclick="lanza();" type="button" class="btn btn-primary">Aplicar</button>
<button onclick="pdf();" type="button" class="btn btn-secondary" >Exportar como PDF</button>

<script>
    
function lanza(){    
var sarea = document.getElementById("area");
var area_value = sarea.value;

var stipo = document.getElementById("tipo");
var tipo_value = stipo.value;

jconfirm.instances[0].close();
avance(area_value, tipo_value);

}
</script>   



</div>
</div>


<div style="height:15px; clear:both"></div>

</form>

<?php



$sql = "select * from tabla ";
if(!$tabla = $db->query($sql)){
    die('Hay un error [' . $db->error . ']');
}   
$row_tabla = $tabla->fetch_assoc();
?>    

<div id="pprint">
<table width="100%" border="0" cellspacing="0" cellpadding="5">
  <tbody>
    <tr>


      <td>
     
      </td>

      <td>

      </td>





      <td class="td_estados" colspan="16" align="center">ESTADOS</td>
      <td colspan="2" align="center"></td>
    </tr>
    <tr>
      <td class="td_titulo">ÁMBITO</td>
      <td class="td_titulo raya_r">TIPO</td>
      <td class="td_titulo raya_r" colspan="2" align="center">EN ESPERA</td>
      <td class="td_titulo raya_r" colspan="2" align="center">FORMULADA</td>
      <td class="td_titulo raya_r" colspan="2" align="center">POSTULADA A FINANCIAMIENTO</td>
      <td class="td_titulo raya_r" colspan="2" align="center">EN LICITACIÓN</td>
      <td class="td_titulo raya_r" colspan="2" align="center">ETAPA EN EJECUCIÓN</td>
      <td class="td_titulo raya_r" colspan="2" align="center">FINALIZADA ETAPA PERFIL / PREFACTIBILIDAD</td>
      <td class="td_titulo raya_r" colspan="2" align="center">FINALIZADA ETAPA DISEÑO</td>
      <td class="td_titulo" colspan="2" align="center">FINALIZADA</td>
      <td class="td_titulo" colspan="2" align="center">TOTAL</td>
    </tr>

<?php 
$num=0;

$tt1=0;
$tt2=0;
$tt3=0;
$tt4=0;
$tt5=0;
$tt6=0;
$tt7=0;
$tt8=0;
?>

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
      <?php if ($row_tabla["e1v"] > 0){ ?>
      <td class="tr_data " align="center"><?php echo number_format($row_tabla["e1v"], 0);?></td>
      <?php } else { ?>
      <td class="tr_data " align="center"></td>
      <?php } ?>

      <?php if ($row_tabla["e1p"] > 0){ ?>
      <td class="tr_data raya_r" align="center"><?php echo number_format($row_tabla["e1p"], 2)."%";?></td>
      <?php } else { ?>
      <td class="tr_data raya_r" align="center"></td>
      <?php } ?>      

      <?php if ($row_tabla["e2v"] > 0){ ?>
      <td class="tr_data " align="center"><?php echo number_format($row_tabla["e2v"], 0);?></td>
      <?php } else { ?>
      <td class="tr_data " align="center"></td>
      <?php } ?>   

      <?php if ($row_tabla["e2p"] > 0){ ?>
      <td class="tr_data raya_r" align="center"><?php echo number_format($row_tabla["e2p"], 2)."%";?></td>
      <?php } else { ?>
      <td class="tr_data raya_r" align="center"></td>
      <?php } ?>   

      <?php if ($row_tabla["e3v"] > 0){ ?>
      <td class="tr_data " align="center"><?php echo number_format($row_tabla["e3v"], 0);?></td>
      <?php } else { ?>
      <td class="tr_data " align="center"></td>
      <?php } ?>   

      <?php if ($row_tabla["e3p"] > 0){ ?>
      <td class="tr_data raya_r" align="center"><?php echo number_format($row_tabla["e3p"], 2)."%";?></td>
      <?php } else { ?>
      <td class="tr_data raya_r" align="center"></td>
      <?php } ?> 

      <?php if ($row_tabla["e4v"] > 0){ ?>      
      <td class="tr_data " align="center"><?php echo number_format($row_tabla["e4v"], 0);?></td>
      <?php } else { ?>
      <td class="tr_data " align="center"></td>
      <?php } ?> 

      <?php if ($row_tabla["e4p"] > 0){ ?>   
      <td class="tr_data raya_r" align="center"><?php echo number_format($row_tabla["e4p"], 2)."%";?></td>
      <?php } else { ?>
      <td class="tr_data raya_r" align="center"></td>
      <?php } ?> 

      <?php if ($row_tabla["e5v"] > 0){ ?>
      <td class="tr_data " align="center"><?php echo number_format($row_tabla["e5v"], 0);?></td>
      <?php } else { ?>
      <td class="tr_data " align="center"></td>
      <?php } ?> 

      <?php if ($row_tabla["e5p"] > 0){ ?>
      <td class="tr_data raya_r" align="center"><?php echo number_format($row_tabla["e5p"], 2)."%";?></td>
      <?php } else { ?>
      <td class="tr_data raya_r" align="center"></td>
      <?php } ?> 

      <?php if ($row_tabla["e6v"] > 0){ ?>
      <td class="tr_data " align="center"><?php echo number_format($row_tabla["e6v"], 0);?></td>
      <?php } else { ?>
      <td class="tr_data " align="center"></td>
      <?php } ?> 

      <?php if ($row_tabla["e6p"] > 0){ ?>
      <td class="tr_data raya_r" align="center"><?php echo number_format($row_tabla["e6p"], 2)."%";?></td>
      <?php } else { ?>
      <td class="tr_data raya_r" align="center"></td>
      <?php } ?> 



      <?php if ($row_tabla["e7v"] > 0){ ?>
      <td class="tr_data " align="center"><?php echo number_format($row_tabla["e7v"], 0);?></td>
      <?php } else { ?>
      <td class="tr_data " align="center"></td>
      <?php } ?> 

      <?php if ($row_tabla["e7p"] > 0){ ?>
      <td class="tr_data raya_r" align="center"><?php echo number_format($row_tabla["e7p"], 2)."%";?></td>
      <?php } else { ?>
      <td class="tr_data raya_r" align="center"></td>
      <?php } ?>       


      <?php if ($row_tabla["e8v"] > 0){ ?>
      <td class="tr_data " align="center"><?php echo number_format($row_tabla["e8v"], 0);?></td>
      <?php } else { ?>
      <td class="tr_data " align="center"></td>
      <?php } ?> 

      <?php if ($row_tabla["e8p"] > 0){ ?>
      <td class="tr_data raya_r" align="center"><?php echo number_format($row_tabla["e8p"], 2)."%";?></td>
      <?php } else { ?>
      <td class="tr_data raya_r" align="center"></td>
      <?php } ?>       




      <td class="tr_data" align="center"><?php echo number_format($row_tabla["tt"], 0);?></td>
      <td class="tr_data" align="center"><?php echo number_format($row_tabla["tp"], 2)."%";?></td>
    </tr>

<?php
// totales

$tt1 = $tt1 + $row_tabla["e1v"];
$tt2 = $tt2 + $row_tabla["e2v"];
$tt3 = $tt3 + $row_tabla["e3v"];
$tt4 = $tt4 + $row_tabla["e4v"];
$tt5 = $tt5 + $row_tabla["e5v"];
$tt6 = $tt6 + $row_tabla["e6v"];
$tt7 = $tt7 + $row_tabla["e7v"];
$tt8 = $tt8 + $row_tabla["e8v"];

$ttt = $tt1 + $tt2 + $tt3 + $tt4 + $tt5 + $tt6 + $tt7 + $tt8;

$ttp1 = ( $tt1 / $ttt ) * 100;
$ttp2 = ( $tt2 / $ttt ) * 100;
$ttp3 = ( $tt3 / $ttt ) * 100;
$ttp4 = ( $tt4 / $ttt ) * 100;
$ttp5 = ( $tt5 / $ttt ) * 100;
$ttp6 = ( $tt6 / $ttt ) * 100;
$ttp7 = ( $tt7 / $ttt ) * 100;
$ttp8 = ( $tt8 / $ttt ) * 100;

$tttp = $ttp1 + $ttp2 + $ttp3 + $ttp4 + $ttp5 + $ttp6 + $ttp7 + $ttp8;

?>



<?php } while( $row_tabla = $tabla->fetch_assoc() ); ?>    

    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td class="td_totales" align="center"><?php echo number_format($tt1, 0);?></td>
      <td class="td_totales" align="center"><?php echo number_format($ttp1, 2)."%";?></td>      
      <td class="td_totales" align="center"><?php echo number_format($tt2, 0);?></td>
      <td class="td_totales" align="center"><?php echo number_format($ttp2, 2)."%";?></td>            
      <td class="td_totales" align="center"><?php echo number_format($tt3, 0);?></td>
      <td class="td_totales" align="center"><?php echo number_format($ttp3, 2)."%";?></td>            
      <td class="td_totales" align="center"><?php echo number_format($tt4, 0);?></td>
      <td class="td_totales" align="center"><?php echo number_format($ttp4, 2)."%";?></td>            
      <td class="td_totales" align="center"><?php echo number_format($tt5, 0);?></td>
      <td class="td_totales" align="center"><?php echo number_format($ttp5, 2)."%";?></td>            
      <td class="td_totales" align="center"><?php echo number_format($tt6, 0);?></td>
      <td class="td_totales" align="center"><?php echo number_format($ttp6, 2)."%";?></td>            
      <td class="td_totales" align="center"><?php echo number_format($tt7, 0);?></td>
      <td class="td_totales" align="center"><?php echo number_format($ttp7, 2)."%";?></td>                  
      <td class="td_totales" align="center"><?php echo number_format($tt8, 0);?></td>
      <td class="td_totales" align="center"><?php echo number_format($ttp8, 2)."%";?></td>                  

      <td class="td_totales" align="center"><?php echo number_format($ttt, 0);?></td>
      <td class="td_totales" align="center"><?php echo number_format($tttp, 2)."%";?></td>      
    </tr>
  </tbody>
</table>  
</div>  

<!-- ****************************************** -->

<div style="height:20px; clear:both"></div>

<div class="row">
    <div class="col text-center">
        <button onclick="pdf();" type="button" class="btn btn-secondary" >Exportar como PDF</button>
    </div>  
</div>

<div style="height:20px; clear:both"></div>


                    </div>    


                </div>

        </div>
    </div>



    <!-- Enlace a JavaScript de Bootstrap 5 y dependencias -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js" integrity="sha384-cuYeSxntonz0PPNlHhBs68uyIAVpIIOZZ5JqeqvYYIcEL727kskC66kF92t6Xl2V" crossorigin="anonymous"></script>
    <script src="app.js"></script>
</body>
</html>
