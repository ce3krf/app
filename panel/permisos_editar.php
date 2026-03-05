<?php 
// ************************************************************
//    __       _ _ _                  _   
//   / _|_   _| | | |_ _ __ _   _ ___| |_ 
//  | |_| | | | | | __| '__| | | / __| __|
//  |  _| |_| | | | |_| |  | |_| \__ \ |_ 
//  |_|  \__,_|_|_|\__|_|   \__,_|___/\__|
//                                         
// Autor: Marcelo Jiménez
// Fulltrust Software
// https://www.fulltrust.net
// Fecha última actualización: 2025-09-18 19:49:55
// ************************************************************

//error_reporting(E_ALL);
//ini_set('display_errors', 1);
?>
<?php


@session_start();
include("../conf/db.php");		
include('functions.php');

//echo "here<br>";
//exit;










$sql="select * from peredifica where id=".$_GET['permisos_id'];
if(!$result = $db->query($sql)){
    die('Hay un error [' . $db->error . ']');
}
$row = $result->fetch_assoc();











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

<link rel="stylesheet" type="text/css" href="css/styles.css"/>


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

<div id="wrapper">
    <div id="sidebar-wrapper">
        <?php include("sidebar.php");?>
    </div>
    
    <div id="page-content-wrapper">
        <div class="container-fluid p-4">



                <div class="row top-info-div">
                    <div class="col">
                      <?php if($_GET["proyectos_id"]=='0'){ ?>
                        <h5>CREAR NUEVO PERMISO</h5>
                      <?php } else { ?>
                        <h5>EDITAR PERMISO</h5>
                      <?php } ?>
                    </div>

                    <div class="col text-end">
                    <!-- <button onclick="window.location='proyectos_editar.php?proyectos_id=<?php echo $row['id'];?>'" type="button" class="btn btn-secondary btn-excel"><i class="fa-regular fa-pen-to-square"></i></i>&nbsp;Editar proyecto</button> -->
                    <?php if($_GET["permisos_id"]=='0'){ ?>
                    <button onclick="window.location='permisos.php'" type="button" class="btn btn-primary"><i class="fa-solid fa-chevron-left"></i>&nbsp;Volver</button>
                    <?php } else { ?>
                    <button onclick="window.location='permisos.php'" type="button" class="btn btn-primary"><i class="fa-solid fa-chevron-left"></i>&nbsp;Volver</button>                                          
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

</div>  
</div>
<?php } ?>
<!----------------------> 


<!----------------------> 
<div class="row">
<div class="col">

 <div class="form-group">
   <label for="nombre">Nombre</label>
   <input type="text" class="form-control" id="nombre" name="nombre"  value="<?php echo $row["nombre"];?>">
 </div>

</div>  
</div>
<!----------------------> 


<!----------------------> 
<div class="row">
<div class="col">

 <div class="form-group">
   <label for="direccion">Dirección</label>
   <input type="text" class="form-control" id="direccion" name="direccion"  value="<?php echo $row["direccion"];?>">
 </div>

</div>  
</div>
<!----------------------> 

<!----------------------> 
<div class="row">
<div class="col">

 <div class="form-group">
   <label for="localidad">Localidad</label>
   <input type="text" class="form-control" id="localidad" name="localidad"  value="<?php echo $row["localidad"];?>">
 </div>

</div>  
</div>
<!----------------------> 

<!----------------------> 
<div class="row">
<div class="col">

 <div class="form-group">
   <label for="rol">ROL</label>
   <input type="text" class="form-control" id="rol" name="rol"  value="<?php echo $row["rol"];?>">
 </div>

</div>  
</div>
<!----------------------> 

<!----------------------> 
<div class="row">
<div class="col">

 <div class="form-group">
   <label for="per_edif">PERMISO EDIFICACIÓN</label>
   <input type="text" class="form-control" id="per_edif" name="per_edif"  value="<?php echo $row["per_edif"];?>">
 </div>

</div>  
</div>
<!----------------------> 

<!----------------------> 
<div class="row">
<div class="col">

 <div class="form-group">
   <label for="rec_def">RECEPCIÓN DEFINITIVA</label>
   <input type="text" class="form-control" id="rec_def" name="rec_def"  value="<?php echo $row["rec_def"];?>">
 </div>

</div>  
</div>
<!----------------------> 

<!----------------------> 
<div class="row">
<div class="col">

 <div class="form-group">
   <label for="m2">METROS CUADRADOS</label>
   <input type="text" class="form-control" id="m2" name="m2"  value="<?php echo $row["m2"];?>">
 </div>

</div>  
</div>
<!----------------------> 

<!----------------------> 
<div class="row">
<div class="col">

 <div class="form-group">
   <label for="n">METROS</label>
   <input type="text" class="form-control" id="n" name="n"  value="<?php echo $row["n"];?>">
 </div>

</div>  
</div>
<!----------------------> 

<!----------------------> 
<div class="row">
<div class="col">

 <div class="form-group">
   <label for="m2_avaluo">METROS CUADRADOS AVALUO</label>
   <input type="text" class="form-control" id="m2_avaluo" name="m2_avaluo"  value="<?php echo $row["m2_avaluo"];?>">
 </div>

</div>  
</div>
<!----------------------> 

<!----------------------> 
<div class="row">
<div class="col">

 <div class="form-group">
   <label for="destino2">DESTINO</label>
   <input type="text" class="form-control" id="destino2" name="destino2"  value="<?php echo $row["destino2"];?>">
 </div>

</div>  
</div>
<!----------------------> 

<!----------------------> 
<div class="row">
<div class="col">

 <div class="form-group">
   <label for="tipo">TIPO</label>
   <input type="text" class="form-control" id="tipo" name="tipo"  value="<?php echo $row["tipo"];?>">
 </div>

</div>  
</div>
<!----------------------> 

<!----------------------> 
<div class="row">
<div class="col">

 <div class="form-group">
   <label for="expediente">EXPEDIENTE</label>
   <input type="text" class="form-control" id="expediente" name="expediente"  value="<?php echo $row["expediente"];?>">
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
   <label for="lat">LATITUD</label>
   <input type="text" class="form-control" id="lat" name="lat"  value="<?php echo $row["lat"];?>">
 </div>

</div>  
</div>
<!----------------------> 

<!----------------------> 
<div class="row">
<div class="col">

 <div class="form-group">
   <label for="lon">LONGITUD</label>
   <input type="text" class="form-control" id="lon" name="lon"  value="<?php echo $row["lon"];?>">
 </div>

</div>  
</div>
<!----------------------> 










<input name="permisos_id" type="hidden" class="fields30" id="permisos_id" value="<?php echo $row["id"]?>" />
<input name="posted" type="hidden" class="fields30" id="posted" value="1" />
<?php if($_GET["permisos_id"]=='0'){ ?>
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
       <button id="btdelete" onclick="eliminar();" type="button" class="btn btn-danger"><i class="fa-regular fa-trash-can"></i>&nbsp;Eliminar permiso</button>
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
<input type="hidden" class="form-control" id="permisos_id" name="permisos_id" value="<?php echo $row["id"];?>">
<input name="token" type="hidden" class="fields30" id="token" value="<?php echo $sid;?>" />
</form>







  
</div>
</div>




<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>









                </div>
        </div>
    </div>
</div>

<?php include("footer.php");?>
        </div>
    </div>







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
            url: "api/permisos.php", 
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
                              window.location='permisos_editar.php?permisos_id='+response;
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
        url: "api/permisos.php", 
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
                        window.location='permisos.php';
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






<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js"></script>
<script src="app.js"></script>
</body>
</html>
