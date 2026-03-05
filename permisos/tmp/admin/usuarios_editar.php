<?php 
// ************************************************************
// Autor: Marcelo Jiménez
// Fulltrust Software
// https://www.fulltrust.net
// Fecha última actualización: 2024-09-13 18:17:14
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


$id = decrypt( $_GET['usuarios_id'] );

$sql="select * from usuarios where usuarios_id='".$id."'";
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

<script src="functions.js"></script>	
<link rel="stylesheet" type="text/css" href="../css/styles.css"/>

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
                      <?php if($_GET["usuarios_id"]=='0'){ ?>
                        <h5>CREAR NUEVO USUARIO</h5>
                      <?php } else { ?>
                        <h5>EDITAR USUARIO</h5>
                      <?php } ?>
                    </div>

                    <div class="col text-end">
                        <button onclick="window.location='usuarios.php'" type="button" class="btn btn-primary"><i class="fa-solid fa-chevron-left"></i>&nbsp;Volver</button>
                    </div>                        

                </div>    




                <div class="container">

                <form id="form1" name="form1" method="post" action="">


<!----------------------> 

<?php
if ($row["usuarios_foto"] == '') {
  $foto = "../img/user.png";
} else {
  $foto = $row["usuarios_foto"];
}
?>

<div class="row">
<div class="col">

 <div class="form-group">
   <label for="usuarios_foto">Imagen de perfil</label>
   <img src="<?php echo $foto; ?>" id="imagen" style="max-width: 100%; max-height: 150px;">
 </div>

</div>  
</div>

<div class="row">
<div class="col">
  <input type="file" id="usuarios_foto" name="usuarios_foto" onchange="uploadImage(event)" accept="image/*">
</div>  
</div>

<div class="row">
<div class="col">
<p>&nbsp;</p>  
</div>  
</div>  

<script>
function uploadImage(event) {
  var image = document.getElementById('imagen');
  image.src = URL.createObjectURL(event.target.files[0]);
}
</script>

<!----------------------> 

<!----------------------> 
<div class="row">
<div class="col">

 <div class="form-group">
   <label for="usuarios_userid">ID del usuario</label>
   <?php if($_GET["usuarios_id"]=='0'){ ?>   
   <input type="text" class="form-control" id="usuarios_userid" name="usuarios_userid" required value="<?php echo $row["usuarios_userid"];?>">
   <?php } else { ?>  
   <input type="text" class="form-control" id="usuarios_userid" name="usuarios_userid" readonly required value="<?php echo $row["usuarios_userid"];?>">
   <?php } ?>
 </div>

</div>  
</div>
<!----------------------> 

<!----------------------> 
<div class="row">
<div class="col">

 <div class="form-group">
   <label for="usuarios_nombre">NOMBRE</label>
   <input type="text" class="form-control" id="usuarios_nombre" name="usuarios_nombre" required value="<?php echo $row["usuarios_nombre"];?>">
 </div>

</div>  
</div>
<!----------------------> 

<!----------------------> 
<div class="row">
<div class="col">

 <div class="form-group">
   <label for="usuarios_email">E-MAIL</label>
   <input type="text" class="form-control" id="usuarios_email" name="usuarios_email" required value="<?php echo $row["usuarios_email"];?>">
 </div>

</div>  
</div>
<!----------------------> 

<!----------------------> 
<div class="row">
<div class="col">

 <div class="form-group">
   <label for="usuarios_profile">PERFIL</label>
   <select class="form-control" id="usuarios_profile" name="usuarios_profile">
       <option value=""></option>
       <option <?php if ($row["usuarios_profile"]=="ADMINISTRADOR"){ echo "selected";};?> value="ADMINISTRADOR">ADMINISTRADOR</option>
       <option <?php if ($row["usuarios_profile"]=="INVITADO"){ echo "selected";};?> value="INVITADO">INVITADO</option>
   </select>
 </div>

</div>  
</div>
<!----------------------> 

<!----------------------> 
<div class="row">
<div class="col">

 <div class="form-group">
   <label for="usuarios_password">CONTRASEÑA (Para mantener la contraseña actual, deje el campo en blanco)</label>
   <input type="password" class="form-control" id="usuarios_password" name="usuarios_password" value="">
 </div>

</div>  
</div>
<!----------------------> 



<input name="usuarios_id" type="hidden" id="usuarios_id" value="<?php echo $row["usuarios_id"]?>" />
<?php if($_GET["usuarios_id"]=='0'){ ?>
<input name="task" type="hidden" id="task" value="insert" />
<?php } else { ?>
<input name="task" type="hidden" id="task" value="update" />
<?php } ?>
<input name="token" type="hidden" class="fields30" id="token" value="<?php echo $sid;?>" />






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
    <?php if($_GET["proyectos_id"]=='0'){ ?>
    <?php } else { ?>
       <button id="btdelete" onclick="eliminar();" type="button" class="btn btn-danger"><i class="fa-regular fa-trash-can"></i>&nbsp;Eliminar usuario</button>
       <?php } ?>
      </div>

       <div class="col" style="text-align:center">
       <button id="btsend" onclick="guardar();" type="button" class="btn btn-primary"><i class="fa-regular fa-floppy-disk"></i>&nbsp;Guardar cambios</button>
       </div>

       </div>
  <?php }?>

       </div>
     

     
     </td>
     </tr>
   </table>

</div>  
</div>



 </form>


<form id="frm1" name="frm1">
<input type="hidden" name="task" id="task" value="delete">    
<input type="hidden" class="form-control" id="usuarios_id" name="usuarios_id" value="<?php echo $row["usuarios_id"];?>">
<input name="token" type="hidden" class="fields30" id="token" value="<?php echo $sid;?>" />
</form>









</div>  
</div>
<!----------------------> 



  
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
            url: "api/usuarios.php", 
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
                              window.location='usuarios_editar.php?usuarios_id='+encrypt(response);
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
        url: "api/usuarios.php", 
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
                        window.location='usuarios.php';
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
