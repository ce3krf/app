<?php 
// ************************************************************
// Autor: Marcelo Jiménez
// Fulltrust Software
// https://www.fulltrust.net
// Fecha última actualización: 2025-05-30 10:31:25
// ************************************************************
?>
<?php
//error_reporting(E_ALL);
//ini_set('display_errors', '1');


@session_start();
include("../conf/db.php");		
include('functions.php');

//echo "here<br>";
//exit;



// Código del proyecto (reemplaza esto con el valor correspondiente)
$codigo_proyecto = $_GET['proyectos_id']; // Cambia '123' por el código del proyecto deseado

// Consultar el total de actividades del proyecto
$query_total = "SELECT COUNT(*) AS total FROM actividades WHERE proyecto = ?";
$stmt_total = $db->prepare($query_total);
$stmt_total->bind_param('s', $codigo_proyecto);
$stmt_total->execute();
$result_total = $stmt_total->get_result();
$row_total = $result_total->fetch_assoc();
$total = $row_total['total'];

// Consultar el total de actividades completadas
$query_completadas = "SELECT COUNT(*) AS completadas, sum(monto) as monto FROM actividades WHERE proyecto = ? AND estado = 'Completa'";
$stmt_completadas = $db->prepare($query_completadas);
$stmt_completadas->bind_param('s', $codigo_proyecto);
$stmt_completadas->execute();
$result_completadas = $stmt_completadas->get_result();
$row_completadas = $result_completadas->fetch_assoc();
$completadas = $row_completadas['completadas'];
$monto = $row_completadas['monto'];

// Calcular el porcentaje de actividades completadas
$porcentaje_completadas = ($total > 0) ? ($completadas / $total) * 100 : 0;






$sql="select * from proyectos where id=".$_GET['proyectos_id'];
if(!$result = $db->query($sql)){
    die('Hay un error [' . $db->error . ']');
}
$row = $result->fetch_assoc();

$avance_financiero = ($monto / $row['total']) * 100;



// Actualizar el campo avance_actividades en la tabla proyectos
$query_update = "UPDATE proyectos SET avance_actividades = ?, avance_financiero = ? WHERE id = ?";
$stmt_update = $db->prepare($query_update);
$stmt_update->bind_param('dds', $porcentaje_completadas, $avance_financiero, $codigo_proyecto);
if ($stmt_update->execute()) {
    //echo "El avance de actividades se actualizó correctamente.";
} else {
    echo "Error al actualizar el avance de actividades: " . $stmt_update->error;
}



$sql="select * from actividades where proyecto='".$row['id']."' order by ano";
if(!$result = $db->query($sql)){
    die('Hay un error [' . $db->error . ']');
}
$row_actividades = $result->fetch_assoc();


$sql="select * from indica where indica_iniciativa='".$row['id']."' order by indica_id";
if(!$indica = $db->query($sql)){
    die('Hay un error [' . $db->error . ']');
}
$row_indica = $indica->fetch_assoc();


$sql="select * from areas order by area";
if(!$areas = $db->query($sql)){
    die('Hay un error [' . $db->error . ']');
}
$row_areas = $areas->fetch_assoc();


$sql="select distinct subarea from subareas order by subarea";
if(!$subarea = $db->query($sql)){
    die('Hay un error [' . $db->error . ']');
}
$row_subarea = $subarea->fetch_assoc();



$sql="select * from origen order by origen";
if(!$origenes = $db->query($sql)){
    die('Hay un error [' . $db->error . ']');
}
$row_origenes = $origenes->fetch_assoc();


$sql="select * from ubicaciones where codigo='".$row["ubicacion"]."'";
if(!$ubicaciones = $db->query($sql)){
    die('Hay un error [' . $db->error . ']');
}
$row_ubicaciones = $ubicaciones->fetch_assoc();


$sql="select * from tipo order by tipo";
if(!$tipo = $db->query($sql)){
    die('Hay un error [' . $db->error . ']');
}
$row_tipo = $tipo->fetch_assoc();


$sql="SELECT *  FROM   `estados`  ORDER BY  id";
if(!$resestado = $db->query($sql)){
    die('Hay un error [' . $db->error . ']');
}
$row_estado = $resestado->fetch_assoc();  





?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1">	
<title><?php echo $row_param["parametros_titulo"];?></title>

<link rel="icon" type="image/png" href="../favicon.png">
<script src="js/jquery-3.5.1.min.js"></script>	
	
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>	

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js" integrity="sha512-GsLlZN/3F2ErC5ifS5QtgpiJtWd43JWSuIgh7mbzZ8zBps+dvLusV+eNQATqgA/HdeKFVgA5v3S/cIrLF7QnIg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<link rel="stylesheet" type="text/css" href="../css/styles.css"/>


<script>
function generatePDF() {
    const element = document.getElementById('pprint');

    // Set up configuration for html2pdf
    const opt = {
        margin: 1,
        filename: '<?php echo $row["nombre"]?>.pdf',
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2 },
        jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' },
    };

    // Generate PDF with html2pdf and add page numbers
    html2pdf().from(element).set(opt).toPdf().get('pdf').then(function (pdf) {
        const totalPages = pdf.internal.getNumberOfPages();

        for (let i = 1; i <= totalPages; i++) {
            pdf.setPage(i);
            pdf.setFontSize(10);
            pdf.text(`Página ${i} de ${totalPages}`, pdf.internal.pageSize.getWidth() / 2, pdf.internal.pageSize.getHeight() - 0.5, { align: 'center' });
        }
    }).save();
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



                <div class="row top-info-div">
                    <div class="col">
                      <?php if($_GET["proyectos_id"]=='0'){ ?>
                        <h5>CREAR NUEVO PROYECTO</h5>
                      <?php } else { ?>
                        <h5>EDITAR PROYECTO</h5>
                      <?php } ?>
                    </div>

                    <div class="col text-end">
                    <!-- <button onclick="window.location='proyectos_editar.php?proyectos_id=<?php echo $row['id'];?>'" type="button" class="btn btn-secondary btn-excel"><i class="fa-regular fa-pen-to-square"></i></i>&nbsp;Editar proyecto</button> -->
                    <?php if($_GET["proyectos_id"]=='0'){ ?>
                    <button onclick="window.location='proyectos.php'" type="button" class="btn btn-primary"><i class="fa-solid fa-chevron-left"></i>&nbsp;Volver</button>
                    <?php } else { ?>
                    <button onclick="window.location='proyectos.php'" type="button" class="btn btn-primary"><i class="fa-solid fa-chevron-left"></i>&nbsp;Volver</button>                                          
                    <?php } ?>  
                    </div>                        

                </div>    




                <div id="pprint">
                <div class="container">

                <form id="form1" name="form1">


<!----------------------> 
<?php if($_GET["proyectos_id"]=='0'){ ?>
  <?php } else { ?>  
<div class="row">
<div class="col">
  <button onclick="generatePDF();" type="button" class="btn btn-secondary"><i class="fa-regular fa-file"></i>&nbsp;Exportar como PDF</button>
</div>  
<div class="col" style="text-align:right">
  <i class="fa-solid fa-list-check"></i>&nbsp;<a href="#actividad">Actividades de este proyecto</a>
</div>  
</div>
<?php } ?>
<!----------------------> 


<!----------------------> 
<div class="row">
<div class="col">

 <div class="form-group">
   <label for="nombre">Nombre del proyecto</label>
   <input type="text" class="form-control" id="nombre" name="nombre"  value="<?php echo $row["nombre"];?>">
 </div>

</div>  
</div>
<!----------------------> 


<!----------------------> 
<div class="row">
<div class="col">

 <div class="form-group">
  <label for="descripcion">Descripción del proyecto</label>
  <textarea class="form-control" id="descripcion" name="descripcion" rows="5"><?php echo $row["descripcion"];?></textarea>
 </div>

</div>  
</div>
<!----------------------> 


<!----------------------> 
<div class="row">
<div class="col">

 <div class="form-group">
   <label for="tipo">TIPO</label>

   <select class="form-select" name="tipo" id="tipo">
     <option value=""></option>
     <?php do { ?>
     <option <?php if( $row_tipo["codigo"] == $row["cod_tipo"]) {echo " selected ";}?> value="<?php echo $row_tipo["codigo"];?>"><?php echo $row_tipo["tipo"];?></option>
     <?php } while ( $row_tipo = $tipo->fetch_assoc() );?>
 </select>

 </div>

</div>  
</div>
<!----------------------> 

<!----------------------> 
<div class="row">
<div class="col">

 <div class="form-group">
   <label for="cod_area">ÁMBITO</label>

   <select class="form-select" name="cod_area" id="cod_area">
     <option value=""></option>
     <?php do { ?>
     <option <?php if( $row_areas["codigo"] == $row["cod_area"]) {echo " selected ";}?> value="<?php echo $row_areas["codigo"];?>"><?php echo $row_areas["area"];?></option>
     <?php } while ( $row_areas = $areas->fetch_assoc() );?>
 </select>

 </div>

</div>  
</div>
<!----------------------> 


<!----------------------> 
<div class="row">
<div class="col">

 <div class="form-group">
   <label for="subarea">SUB ÁMBITO</label>

   <select class="form-select" name="subarea" id="subarea">
     <option value=""></option>
     <?php do { ?>
     <option <?php if( $row_subarea["subarea"] == $row["subarea"]) {echo " selected ";}?> value="<?php echo $row_subarea["subarea"];?>"><?php echo $row_subarea["subarea"];?></option>
     <?php } while ( $row_subarea = $subarea->fetch_assoc() );?>
 </select>

 </div>

</div>  
</div>
<!----------------------> 


<!----------------------> 
<div class="row">
<div class="col">

 <div class="form-group">
   <label for="origen">ORIGEN</label>
   <input type="text" class="form-control" id="origen" name="origen"  value="<?php echo $row["origen"];?>">
 </div>

</div>  
</div>
<!----------------------> 


<!----------------------> 
<div class="row">
<div class="col">

 <div class="form-group">
   <label for="lineamiento">LINEAMIENTO</label>
   <textarea class="form-control" id="lineamiento" name="lineamiento" rows="3"><?php echo $row["lineamiento"];?></textarea>
 </div>

</div>  
</div>
<!----------------------> 

<!----------------------> 
<div class="row">
<div class="col">

 <div class="form-group">
   <label for="objetivos">OBJETIVOS</label>
   <textarea class="form-control" id="objetivos" name="objetivos" rows="3"><?php echo $row["objetivos"];?></textarea>
 </div>

</div>  
</div>
<!----------------------> 


<!----------------------> 
<div class="row">
<div class="col">

 <div class="form-group">
   <label for="etapa">ETAPA</label>
   <input type="text" class="form-control" id="etapa" name="etapa"  value="<?php echo $row["etapa"];?>">
 </div>

</div>  
</div>
<!----------------------> 


<!----------------------> 
<div class="row">
<div class="col">

 <div class="form-group">
   <label for="meta_del_periodo">META DEL PERIODO</label>
   <input type="text" class="form-control" id="meta_del_periodo" name="meta_del_periodo"  value="<?php echo $row["meta_del_periodo"];?>">
 </div>

</div>  
</div>
<!----------------------> 


<!----------------------> 
<div class="row">
<div class="col">

 <div class="form-group">
   <label for="p_diseno">PERFIL DISEÑO (MILES DE PESOS)</label>
   <input type="text" class="form-control" id="p_diseno" name="p_diseno"  value="<?php echo $row["p_diseno"];?>">
 </div>

</div>  
</div>
<!----------------------> 

<!----------------------> 
<div class="row">
<div class="col">

 <div class="form-group">
   <label for="p_prefactibilidad">PREFACTIBILIDAD (MILES DE PESOS)</label>
   <input type="text" class="form-control" id="p_prefactibilidad" name="p_prefactibilidad"  value="<?php echo $row["p_prefactibilidad"];?>">
 </div>

</div>  
</div>
<!----------------------> 

<!---------------------->   
<div class="row">
<div class="col">

 <div class="form-group">
   <label for="p_ejecucion">EJECUCIÓN (MILES DE PESOS)</label>
   <input type="text" class="form-control" id="p_ejecucion" name="p_ejecucion"  value="<?php echo $row["p_ejecucion"];?>">
 </div>

</div>  
</div>
<!----------------------> 


<!----------------------> 
<div class="row">
<div class="col">

 <div class="form-group">
   <label for="total">INVERSIÓN TOTAL (MILES DE PESOS)</label>
   <input type="text" class="form-control" id="total" name="total"  value="<?php echo $row["total"];?>">
 </div>

</div>  
</div>
<!----------------------> 



<!----------------------> 
<div class="row">
<div class="col">

 <div class="form-group">
   <label for="financiamiento">POSIBLE FUENTE DE FINANCIAMIENTO</label>
   <input type="text" class="form-control" id="financiamiento" name="financiamiento"  value="<?php echo $row["financiamiento"];?>">
 </div>

</div>  
</div>
<!----------------------> 

<!----------------------> 
<div class="row">
<div class="col">

 <div class="form-group">
   <label for="unidad">UNIDAD RESPONSABLE</label>
   <input type="text" class="form-control" id="unidad" name="unidad"  value="<?php echo $row["unidad"];?>">
 </div>

</div>  
</div>
<!----------------------> 

<!----------------------> 
<div class="row">
<div class="col">

 <div class="form-group">
   <label for="responsable">COORDINADOR</label>
   <input type="text" class="form-control" id="responsable" name="responsable"  value="<?php echo $row["responsable"];?>">
 </div>

</div>  
</div>
<!----------------------> 

<!----------------------> 
<div class="row">
<div class="col">

 <div class="form-group">
   <label for="entidades_relacionadas">ENTIDADES RELACIONADAS</label>
   <input type="text" class="form-control" id="entidades_relacionadas" name="entidades_relacionadas"  value="<?php echo $row["entidades_relacionadas"];?>">
 </div>

</div>  
</div>
<!----------------------> 


<!----------------------> 
<div class="row">
<div class="col">

 <div class="form-group">
   <label for="prioridad">PRIORIDAD</label>
   <input type="text" class="form-control" id="prioridad" name="prioridad"  value="<?php echo $row["prioridad"];?>">
 </div>

</div>  
</div>
<!----------------------> 



<!----------------------> 
<div class="row">
<div class="col">

 <div class="form-group">
   <label for="estado">ESTADO</label>

   <select class="form-select" name="estado" id="estado">
     <option value=""></option>
     <?php do { ?>
     <option <?php if( $row_estado["estado"] == $row["estado"]) {echo " selected ";}?> value="<?php echo $row_estado["estado"];?>"><?php echo $row_estado["estado"];?></option>
     <?php } while ( $row_estado = $resestado->fetch_assoc() );?>
 </select>

 </div>

</div>  
</div>
<!----------------------> 

<div style="height:50px; clear:both"></div>

<!----------------------> 
<div class="row">
<div class="col">

 <div class="form-group">
   <label for="avance_financiero">% AVANCE FINANCIERO</label>
   <h3><?php echo $avance_financiero;?>%</h3> 
 </div>

</div>  
</div>
<!----------------------> 



<!----------------------> 
<div class="row">
<div class="col">

 <div class="form-group">
   <label for="avance_actividades">% AVANCE ACTIVIDADES</label>
   <h3><?php echo $row["avance_actividades"];?>%</h3> 
 </div>

</div>  
</div>
<!----------------------> 

<div style="height:50px; clear:both"></div>

<!----------------------> 
<div class="row">
  <div class="col-6">
    <div class="form-group">
      <label for="lat">LATITUD</label>
      <input type="text" class="form-control" id="lat" name="lat" value="<?php echo $row['lat'];?>">
    </div>
  </div>
  <div class="col-6">
    <div class="form-group">
      <label for="lng">LONGITUD</label>
      <input type="text" class="form-control" id="lng" name="lng" value="<?php echo $row['lng'];?>">
    </div>
  </div>
</div>
<!---------------------->



<!----------------------> 
<!--
<div class="row">
<div class="col">

 <div class="form-group">
   <label for="pdf">PDF (<a target="_blank" href="../pdfs/<?php echo $row["pdf"];?>"><?php echo $row["pdf"];?></a>)</label>
   <input class="form-control" accept="application/pdf" type="file" id="pdf" name="pdf" value="<?php echo $row["pdf"];?>">
 </div>

</div>  
</div>
-->
<!----------------------> 






<input name="proyectos_id" type="hidden" class="fields30" id="proyectos_id" value="<?php echo $row["id"]?>" />
<input name="posted" type="hidden" class="fields30" id="posted" value="1" />
<?php if($_GET["proyectos_id"]=='0'){ ?>
<input name="task" type="hidden" class="fields30" id="task" value="insert" />
<?php } else { ?>
<input name="task" type="hidden" class="fields30" id="task" value="update" />
<?php } ?>
<input name="token" type="hidden" class="fields30" id="token" value="<?php echo $sid;?>" />







<div class="row">
<div class="col">


     
     

     <tr>
       <td colspan="2" valign="top">&nbsp;</td>
     </tr>
     <tr>
       <td colspan="2" valign="top">
         
       <div class="row top-info-div ">

       <div class="col" style="text-align:center">
  <?php if($row_tabla["usuarios_profile"] == 'ADMINISTRADOR'){ ?>
    <?php if($_GET["proyectos_id"]=='0'){ ?>
    <?php } else { ?>
       <button id="btdelete" onclick="eliminar();" type="button" class="btn btn-danger"><i class="fa-regular fa-trash-can"></i>&nbsp;Eliminar proyecto</button>
       <?php } ?>
      </div>

       <div class="col" style="text-align:center">
       <button id="btsend" onclick="guardar();" type="button" class="btn btn-primary"><i class="fa-regular fa-floppy-disk"></i>&nbsp;Guardar cambios</button>
       </div>

       </div>
  <?php }?>
     

     
     </td>
     </tr>
   </table>

</div>  
</div>



</form>


<form id="frm1" name="frm1">
<input type="hidden" name="task" id="task" value="delete">    
<input type="hidden" class="form-control" id="proyectos_id" name="proyectos_id" value="<?php echo $row["id"];?>">
<input name="token" type="hidden" class="fields30" id="token" value="<?php echo $sid;?>" />
</form>







<div style="height:100px"></div>  

<?php if($_GET["proyectos_id"]=='0'){ ?>
<?php } else { ?>  
<div style="height:10px; clear:both"></div>
<div class="alert alert-warning" role="alert">
Muestra las actividades del proyecto en edición. Permite editar una actividad haciendo clic en la primera columna de cada fila y crear nuevas actividades mediante el enlace en la parte superior derecha.
</div>	 
<?php } ?>
 <!----------------------> 
 <?php if($_GET["proyectos_id"]=='0'){ ?>
 <?php } else { ?> 
 <div class="row top-info-div">
<div class="col">
<a href="#" name="actividad" id="actividad"></a><h5>ACTIVIDAD DEL PROYECTO</h5>
</div>  
<div class="col" style="text-align:right">
<a class="btn btn-primary" href="actividades_editar.php?proyectos_id=<?php echo $row['id'];?>&actividades_id=0"><i class="fa-solid fa-plus"></i>&nbsp;Nueva actividad</a>

</div>  
</div>
<!----------------------> 


<!-- tabla -->
<!----------------------> 
<div class="row">
<div class="col">

<table class="table table-hover">
  <thead>
    <tr class="table-primary">
      <th class="labels" scope="col" style="width:10%">EDITAR</th>
      <th class="labels" scope="col">ACTIVIDAD</th>
      <th class="labels" scope="col">AÑO</th>
      <th class="labels text-end" scope="col">MONTO (en miles de pesos)</th>
      <th class="labels" scope="col">FINACIAMIENTO</th>
      <th class="labels" scope="col">ESTADO</th>
      <th class="labels" scope="col">RESPONSABLE</th>      
    </tr>
  </thead>
  <tbody>

  <?php do { ?>
    <tr>
      <td><a href="actividades_editar.php?actividades_id=<?php echo $row_actividades["id"];?>&proyectos_id=<?php echo $_GET["proyectos_id"];?>"><img src="../img/edit.png" width="32"></a></td>
      <td><?php echo $row_actividades["actividad"];?></td>
      <td><?php echo $row_actividades["ano"];?></td>
      <td class="text-end"><?php echo number_format($row_actividades["monto"], 0, ',', '.'); //echo $row_actividades["monto"];?></td>
      <td><?php echo $row_actividades["finaciamiento"];?></td>
      <td><?php echo $row_actividades["estado"];?></td>
      <td style="text-align:center"><?php echo $row_actividades["responsable"];?></td>
    </tr>
  <?php } while($row_actividades = $result->fetch_assoc()); ?>

  </tbody>
</table>


</div>  
</div>
<!----------------------> 
<?php } ?>















<!-- INDICADORES -->


<div style="height:100px"></div>  

<?php if($_GET["proyectos_id"]=='0'){ ?>
<?php } else { ?>  
<div style="height:10px; clear:both"></div>
<div class="alert alert-info" role="alert">
En la siguiente sección se presentan los indicadores aplicados al proyecto. Desde aquí, podrá agregar nuevos indicadores, editar los existentes o eliminarlos si es necesario.
</div>	 
<?php } ?>
 <!----------------------> 
 <?php if($_GET["proyectos_id"]=='0'){ ?>
 <?php } else { ?> 
 <div class="row top-info-div">
<div class="col">
<a href="#" name="actividad" id="actividad"></a><h5>INDICADORES DEL PROYECTO</h5>
</div>  
<div class="col" style="text-align:right">
<a class="btn btn-primary" href="indicadores_editar.php?proyectos_id=<?php echo $row['id'];?>&indica_id=0"><i class="fa-solid fa-plus"></i>&nbsp;Nuevo indicador</a>

</div>  
</div>
<!----------------------> 


<!-- tabla -->
<!----------------------> 
<div class="row">
<div class="col">

<table class="table table-hover">
  <thead>
    <tr class="table-success">
      <th class="labels" scope="col" style="width:10%">EDITAR</th>
      <th class="labels" scope="col">INDICADOR</th>
      <th class="labels text-center" scope="col">META</th>
    </tr>
  </thead>
  <tbody>

  <?php do { ?>
    <tr>
      <td><a href="indicadores_editar.php?indica_id=<?php echo $row_indica["indica_id"];?>&proyectos_id=<?php echo $_GET["proyectos_id"];?>"><img src="../img/edit.png" width="32"></a></td>
      <td><?php echo $row_indica["indica_indicador"];?></td>
      <td class="text-center"><?php echo $row_indica["indica_meta"];?></td>
      </td>
    </tr>
  <?php } while($row_indica = $indica->fetch_assoc()); ?>

  </tbody>
</table>


</div>  
</div>
<!----------------------> 
<?php } ?>


  
</div>
</div>




<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>









                </div>
            </main>
        </div>
    </div>

<?php include("footer.php");?>

    <!-- Enlace a JavaScript de Bootstrap 5 y dependencias -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js" integrity="sha384-cuYeSxntonz0PPNlHhBs68uyIAVpIIOZZ5JqeqvYYIcEL727kskC66kF92t6Xl2V" crossorigin="anonymous"></script>
    <script src="app.js"></script>





<script>
  
function guardar(){    
    $.confirm({
        title: '¡Atención!',
        content: 'Vas a guardar los cambios.<br>¿Deseas continuar?',
        buttons: {
            confirm: {
                text: 'Guardar cambios',
                action: function(){
                    $("#form1").submit();
                }
            },
            cancel: {
                text: 'Cancelar',
                action: function(){

                }
            }        

        }
    });    
}

$(document).ready(function() {
    $('form#form1').on('submit', function (e) {
        e.preventDefault();
        
        $.ajax({
            type: "POST",
            url: "api/proyectos.php", 
            data: new FormData(document.getElementById("form1")),
            contentType: false,       
            cache: false,             
            processData: false, 
            success: function(response) {
                $.alert({
                    title: '¡Atención!',
                    content: 'Los cambios han sido guardados exitosamente',
                    buttons: {
                      confirm: {
                        text: 'Continuar',
                        action: function(){
                            if (response != 0){
                              window.location='proyectos_editar.php?proyectos_id='+response;
                            } else {
                              
                            }
                      }
                    }
                },
                    
            });
                
            },
            error: function(xhr, status, error) {
                console.error("Error en la petición AJAX:", error);
            },
            beforeSend: function() {
              $('.fa-floppy-disk').addClass('spin-icon');
              $('#btsend').prop('disabled', true);
            },
            complete: function(xhr, status) {    
              $('.fa-floppy-disk').removeClass('spin-icon');
              $('#btsend').prop('disabled', false);
            }
        });
    });                    
});

          



function eliminar(){    
    $.confirm({
        title: '¡Atención!',
        content: 'Vas a eliminar definitivamente este registro.<br>¿Deseas continuar?',
        buttons: {
            confirm: {
                text: 'Eliminar',
                action: function(){
                    $("#frm1").submit();
                }
            },
            cancel: {
                text: 'Cancelar',
                action: function(){

                }
            }        

        }
    });    
}


$(document).ready(function() {

$('form#frm1').on('submit', function (e) {
    
    e.preventDefault();
    
    $.ajax({
        type: "POST",
        url: "api/proyectos.php", 
        data: new FormData(document.getElementById("frm1")),
        contentType: false,       
        cache: false,             
        processData: false,         
        success: function(response) {
            $.confirm({
                title: '¡Atención!',
                content: 'El registro ha sido eliminado',
                buttons: {
                    ok: function () {
                        window.location='proyectos.php';
                    }
                }
            });
        },
          error: function(xhr, status, error) {
                  console.error("Error en la petición AJAX:", error);
          },
          beforeSend: function() {
            $('.fa-trash-can').addClass('spin-icon');
            $('#btdelete').prop('disabled', true);
          },
          complete: function(xhr, status) {    
            $('.fa-trash-can').removeClass('spin-icon');
            $('#btdelete').prop('disabled', false);
          }        
    });

});                    

});    




</script>





</body>
</html>
