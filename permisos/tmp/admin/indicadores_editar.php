<?php 
// ************************************************************
// Autor: Marcelo Jiménez
// Fulltrust Software
// https://www.fulltrust.net
// Fecha última actualización: 2025-02-11 15:56:21
// ************************************************************
?>
<?php
//error_reporting(E_ALL);
//ini_set('display_errors', '1');


@session_start();
include("../conf/db.php");		
include('functions.php');




$sql="select * from indica where indica_id='".$_GET['indica_id']."'";
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
                        <h5>EDITAR INDICADOR</h5>
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
   <label for="indica_indicador">INDICADOR</label>
   <input type="text" class="form-control" id="indica_indicador" name="indica_indicador"  value="<?php echo $row["indica_indicador"];?>">
 </div>

</div>  
</div>
<!----------------------> 

<!----------------------> 
<div class="row">
<div class="col">

 <div class="form-group">
   <label for="indica_meta">META</label>
   <input type="text" class="form-control" id="indica_meta" name="indica_meta"  value="<?php echo $row["indica_meta"];?>">
 </div>

</div>  
</div>
<!----------------------> 






<input name="indica_id" type="hidden"  id="indica_id" value="<?php echo $row["indica_id"]?>" />
<input name="proyectos_id" type="hidden"  id="proyectos_id" value="<?php echo $_GET["proyectos_id"]?>" />
<input name="posted" type="hidden" id="posted" value="1" />

<?php if($_GET["indica_id"]=='0'){ ?>
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
    <?php if($_GET["indica_id"]=='0'){ ?>
      <?php } else { ?>    
    <button id="btdelete" onclick="eliminar();" type="button" class="btn btn-danger"><i class="fa-regular fa-trash-can"></i>&nbsp;Eliminar indicador</button>
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
<input name="indica_id" type="hidden"  id="indica_id" value="<?php echo $row["indica_id"]?>" />
<input name="proyectos_id" type="hidden"  id="proyectos_id" value="<?php echo $row["indica_iniciativa"]?>" />
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
            url: "api/indica.php", 
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
        url: "api/indica.php", 
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
