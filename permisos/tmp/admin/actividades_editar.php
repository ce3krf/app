<?php 
// ************************************************************
// Autor: Marcelo Jiménez
// Fulltrust Software
// https://www.fulltrust.net
// Fecha última actualización: 2024-08-22 16:50:10
// ************************************************************
?>
<?php
//error_reporting(E_ALL);
//ini_set('display_errors', '1');


@session_start();
include("../conf/db.php");		
include('functions.php');




$sql="select * from actividades where id='".$_GET['actividades_id']."'";
if(!$result = $db->query($sql)){
    die('Hay un error [' . $db->error . ']');
}
$row = $result->fetch_assoc();


$sql="select * from areas order by area";
if(!$areas = $db->query($sql)){
    die('Hay un error [' . $db->error . ']');
}
$row_areas = $areas->fetch_assoc();


$sql="select * from subareas order by subarea";
if(!$subarea = $db->query($sql)){
    die('Hay un error [' . $db->error . ']');
}
$row_subarea = $subarea->fetch_assoc();



$sql="select * from origen order by origen";
if(!$origenes = $db->query($sql)){
    die('Hay un error [' . $db->error . ']');
}
$row_origenes = $origenes->fetch_assoc();


$sql="select * from ubicaciones order by ubicacion";
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


<script src="js/jquery-3.5.1.min.js"></script>	
	
<link rel="icon" type="image/png" href="../favicon.png">
	


	
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">


<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>	

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">

<link rel="stylesheet" type="text/css" href="../css/styles.css"/>		


</head>
<body>

<?php include("header.php");?>

    <div class="container-fluid">
        <div class="row">

        <?php include("sidebar.php");?>

            <!-- Área de trabajo central -->
            <main role="main" class="col ms-sm-auto col-lg-10">

                <div class="row top-info-div">
                    <div class="col">
                        <h5>EDITAR ACTIVIDAD</h5>
                    </div>
                    <div class="col text-end">
                    <button onclick="window.location='proyectos_editar.php?proyectos_id=<?php echo $_GET['proyectos_id'];?>'" type="button" class="btn btn-primary"><i class="fa-solid fa-chevron-left"></i>&nbsp;Volver</button>                                          
                    </div>                        
                </div>

                <div class="container">



<form id="form1" name="form1">


<!----------------------> 
<div class="row">
<div class="col">

 <div class="form-group">
   <label for="actividad">NOMBRE</label>
   <input type="text" class="form-control" id="actividad" name="actividad"  value="<?php echo $row["actividad"];?>">
 </div>

</div>  
</div>
<!----------------------> 

<!----------------------> 
<div class="row">
<div class="col">

 <div class="form-group">
   <label for="ano">AÑO</label>
   <input type="text" class="form-control" id="ano" name="ano"  value="<?php echo $row["ano"];?>">
 </div>

</div>  
</div>
<!----------------------> 


<!----------------------> 
<div class="row">
<div class="col">

 <div class="form-group">
   <label for="monto">MONTO (en miles de pesos)</label>
   <input type="text" class="form-control" id="monto" name="monto"  value="<?php echo $row["monto"];?>">
 </div>

</div>  
</div>
<!----------------------> 


<!----------------------> 
<div class="row">
<div class="col">
 <div class="form-group">
   <label for="finaciamiento">FINACIAMIENTO</label>
   <input type="text" class="form-control" id="finaciamiento" name="finaciamiento"  value="<?php echo $row["finaciamiento"];?>">
 </div>

</div>  
</div>
<!----------------------> 

<!----------------------> 
<div class="row">
<div class="col">
 <div class="form-group">
   <label for="responsable">RESPONSABLE</label>
   <input type="text" class="form-control" id="responsable" name="responsable"  value="<?php echo $row["responsable"];?>">
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
     <option <?php if( $row["estado"] == "Completa") {echo " selected ";}?> value="Completa">Completa</option>
     <option <?php if( $row["estado"] == "Incompleta") {echo " selected ";}?> value="Incompleta">Incompleta</option>

 </select>


 </div>

</div>  
</div>
<!----------------------> 

<!----------------------> 
<!-- 
<div class="row">
<div class="col">

 <div class="form-group">
   <label for="observaciones">OBSERVACIONES</label>
   <textarea class="form-control" id="observaciones" name="observaciones" rows="3"><?php echo $row["observaciones"];?></textarea>
 </div>

</div>  
</div>
-->
<!----------------------> 

<!----------------------> 
<div class="row">
<div class="col">

 <div class="form-group">
   <label for="pdf">PDF ADJUNTO (<a target="_blank" href="../adjuntos/<?php echo $row["adjunto"];?>"><?php echo $row["adjunto"];?></a>)</label>
   <input class="form-control" accept="application/pdf" type="file" id="pdf" name="pdf" value="<?php echo $row["adjunto"];?>">
 </div>

</div>  
</div>
<!----------------------> 

<!----------------------> 
<div class="row">
<div class="col">

 <div class="form-group">
   <label for="actualizado">ÚLTIMA ACTUALIZACIÓN</label>
   <input type="text" readonly class="form-control" id="actualizado" name="actualizado"  value="<?php echo $row["actualizado"];?>">
 </div>

</div>  
</div>
<!----------------------> 




<input name="actividades_id" type="hidden"  id="actividades_id" value="<?php echo $row["id"]?>" />
<input name="proyectos_id" type="hidden"  id="proyectos_id" value="<?php echo $_GET["proyectos_id"]?>" />
<input name="posted" type="hidden" id="posted" value="1" />

<?php if($_GET["actividades_id"]=='0'){ ?>
<input name="task" type="hidden" id="task" value="insert" />
<?php } else { ?>
<input name="task" type="hidden" id="task" value="update" />
<?php } ?>
<input name="token" type="hidden" id="token" value="<?php echo $sid;?>" />






<div class="row">
<div class="col">


     
     

     <tr>
       <td colspan="2" valign="top">&nbsp;</td>
     </tr>
     <tr>
       <td colspan="2" valign="top">
         
       <div class="row">

       <div class="col" style="text-align:center">
  <?php if($row_tabla["usuarios_profile"] == 'ADMINISTRADOR'){ ?>
    <?php if($_GET["actividades_id"]=='0'){ ?>
      <?php } else { ?>    
    <button id="btdelete" onclick="eliminar();" type="button" class="btn btn-danger"><i class="fa-regular fa-trash-can"></i>&nbsp;Eliminar actividad</button>
    <?php }?>
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
<input name="actividades_id" type="hidden"  id="actividades_id" value="<?php echo $row["id"]?>" />
<input name="proyectos_id" type="hidden"  id="proyectos_id" value="<?php echo $row["proyecto"]?>" />
<input name="token" type="hidden" id="token" value="<?php echo $sid;?>"> 
</form>





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
            url: "api/actividades.php", 
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
        url: "api/actividades.php", 
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
                        window.location='proyectos_editar.php?proyectos_id='+$('#proyectos_id').val();
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








                </div>
            </main>
        </div>
    </div>

<?php include("footer.php");?>

    <!-- Enlace a JavaScript de Bootstrap 5 y dependencias -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js" integrity="sha384-cuYeSxntonz0PPNlHhBs68uyIAVpIIOZZ5JqeqvYYIcEL727kskC66kF92t6Xl2V" crossorigin="anonymous"></script>
    <script src="app.js"></script>
</body>
</html>
