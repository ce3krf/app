<?php 
// ************************************************************
// Autor: Marcelo Jiménez
// Fulltrust Software
// https://www.fulltrust.net
// Fecha última actualización: 25 de Agosto de 2023
// ************************************************************
?>
<?php

	@session_start();
	
	include("../db.php");	
	
	
	
	$sql="set names utf8";
	if(!$result = $db->query($sql)){
    	die('Hay un error [' . $db->error . ']');
	}	
	
	$sql="select * from usuarios where usuarios_userid='".$_SESSION["usuario"]."'"; 
	if(!$restabla = $db->query($sql)){
    	die('Hay un error [' . $db->error . ']');
	}
	$row_tabla = $restabla->fetch_assoc();  	
	$count = $restabla->num_rows;
	
	if($count < 1){
        session_destroy();
		?>
        <script>
		window.location='index.php';
		</script>		
        <?php
	}

/*
	if($row_tabla["usuarios_profile"] <> 'ADMINISTRADOR'){
		?>
        <script>
		window.location='menu.php';
		</script>		
        <?php
	}
*/


?>




<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">		
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $row["proyectos_nombre"];?></title>


<script src="js/jquery-3.5.1.min.js"></script>	
	
<link rel="shortcut icon" href="../favicon.ico" type="image/x-icon">
<link rel="icon" href="../favicon.ico" type="image/x-icon">
	


	
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">

<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>	

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">

<link rel="stylesheet" type="text/css" href="../css/styles.css"/>		


<script>
	
function ayuda(){
            $.confirm({
                title: '',
                type: 'orange',
				boxWidth: '90%',
                typeAnimated: true,
                closeIcon: true,
                buttons: {
                   info: {
                    text: 'Cerrar', 
                    //btnClass: 'btn-red',
                    action: function(){}
                    }
               },

                content: 'url:ayuda_proyetos_editar.html'
            });   	
}		
	
</script>


<?php

if ($_POST["posted"]=="1"){
	
	$sql ="update usuarios set ";
	$sql.="usuarios_userid = '".mysqli_real_escape_string($db, $_POST['usuarios_userid'])."', ";	

    if($_POST['usuarios_password'] <> ''){
        $sql.="usuarios_password = '".mysqli_real_escape_string($db, md5($_POST['usuarios_password']))."', ";		
    }
    
	$sql.="usuarios_nombre = '".mysqli_real_escape_string($db, $_POST['usuarios_nombre'])."', ";		
	$sql.="usuarios_email = '".mysqli_real_escape_string($db, $_POST['usuarios_email'])."', ";			
	$sql.="usuarios_profile = '".mysqli_real_escape_string($db, $_POST['usuarios_profile'])."', ";			
	
	$sql.="usuarios_updated = '".date("d-m-Y H:i:s")."' ";													
	$sql.="where usuarios_id='".$_POST['usuarios_id']."';";
	
//echo "<br>".$sql."<br>";	
	
	if(!$result = $db->query($sql)){
    	die('Hay un error [' . $db->error . ']');
	} else {
		?>
    
<script>

$.confirm({
  title: 'Atención',
  content: 'Cambios realizados exitosamente',
  buttons: {
    confirm:{
      text:'CONTINUAR',
      action: function () {
      }
  },
  }
}); 

</script>



        <?php
	}
	
}


if ($_POST["posted"]=="2"){
	
	$sql ="delete from usuarios ";									
	$sql.="where usuarios_id='".$_POST['usuarios_id']."';";
	
//echo "<br>".$sql."<br>";	
	
	if(!$result = $db->query($sql)){
    	die('Hay un error [' . $db->error . ']');
	} else {
		?><script>

    $.confirm({
      title: 'Atención',
      content: 'El usuario fue eliminado exitosamente',
      buttons: {
        confirm:{
          text:'CONTINUAR',
          action: function () {
          window.location='usuarios.php';
        }
      },
      }
    });      


		</script>
        <?php
	}
	
}



$sql="select * from usuarios where usuarios_id=".$_GET['id'];
if(!$result = $db->query($sql)){
    die('Hay un error [' . $db->error . ']');
}
$row = $result->fetch_assoc();


?>

</head>

<body>

<button
        type="button"
        class="btn btn-primary btn-floating btn-lg"
        id="btn-back-to-top"
        >
  <i class="fas fa-arrow-up"></i>
</button>

<?php include("header.php");?>

<div id="mainContainer">

  <div class="menu_arriba">
    <div class="div300r">

    
    <img style="vertical-align:middle" src="img/user.png">&nbsp;<?php echo $_SESSION["nombre"];?>&nbsp;&nbsp;&nbsp;<a href="usuarios.php"><img src="img/if_Back_132413.png" width="24" height="24" align="absmiddle" />&nbsp;Volver</a></div>

    <div class="div300l"><strong>EDITAR USUARIO</strong></div>
  </div>
  </div>

  <div style="height:10px; clear:both"></div>
<div class="tip">
En esta ventana es posible editar los datos de cada usuario. Si no desea cambiar la contraseña, déjela en blanco. Recuerde que el usuario con perfil administrador tiene acceso a todo y el usuario invitado solo puede consultar información.
</div>	    

<div class="container">


<form id="form1" name="form1" method="post" action="">


 <!----------------------> 
<div class="row">
<div class="col">
<h4>DATOS DEL USUARIO</h4>

  <div class="form-group">
    <label for="usuarios_userid">ID del usuario</label>
    <input type="text" class="form-control" id="usuarios_userid" name="usuarios_userid" readonly required value="<?php echo $row["usuarios_userid"];?>">
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



<input name="usuarios_id" type="hidden" class="fields30" id="usuarios_id" value="<?php echo $row["usuarios_id"]?>" />
<input name="posted" type="hidden" class="fields30" id="posted" value="1" />






<div class="row">
<div class="col">

     
      

      <tr>
        <td colspan="2" valign="top">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="2" valign="top">
          
        <div class="row">

        <?php if($row_tabla["usuarios_profile"] == 'ADMINISTRADOR'){ ?>        
        <div class="col" style="text-align:center">

        <button onclick="pregunta1();" type="button" class="btn btn-danger">ELIMINAR USUARIO</button>

        </div>

        <div class="col" style="text-align:center">
        <button type="submit" class="btn btn-primary">GUARDAR CAMBIOS</button>
        </div>

        <?php } ?>

        </div>
      

      
      </td>
      </tr>
    </table>

</div>  
</div>



  </form>
</div>




<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>

<!-- The Modal -->
<div id="myModal" class="modal">

  <!-- Modal content -->
  <div class="modal-content">
    <span class="close">&times;</span>

    </div>
  </div>

</div>

<script>
//Get the button
let mybutton = document.getElementById("btn-back-to-top");

// When the user scrolls down 20px from the top of the document, show the button
window.onscroll = function () {
  scrollFunction();
};

function scrollFunction() {
  if (
    document.body.scrollTop > 20 ||
    document.documentElement.scrollTop > 20
  ) {
    mybutton.style.display = "block";
  } else {
    mybutton.style.display = "none";
  }
}
// When the user clicks on the button, scroll to the top of the document
mybutton.addEventListener("click", backToTop);

function backToTop() {
  document.body.scrollTop = 0;
  document.documentElement.scrollTop = 0;
}  
</script>  



<script>

function pregunta(){  

var id  = '<?php echo $_GET['proyectos_id'];?>';
$.confirm({
    title: 'Atención',
    content: 'Va a crear un nuevo objetivo. ¿Seguro de continuar?',
    boxWidth: '300px',
    useBootstrap: false,

    buttons: {
        confirm:{
          text:'CONFIRMAR',
          action: function () {
            //-----------------
            $.ajax({url:"objetivos_nuevo.php", type:'GET', data:{ id:id }, success:function(result){	
            }});

            $.confirm({
              title: 'Atención',
              content: 'Nuevo objetivo agregado exitosamente',
              buttons: {
                confirm:{
                  text:'CONTINUAR',
                  action: function () {
                    datos();
                  }
                },
              }
            }); 

            //-----------------
          }
        },
        cancel:{
          text:'CANCELAR',
          action: function () {
          }
        },
     }
  });  
}
</script>

<script>
function pregunta1(){  
$.confirm({
    title: 'Atención',
    content: 'Va a eliminar el usuario. ¿Seguro de continuar?',
    boxWidth: '300px',
    useBootstrap: false,

    buttons: {
        confirm:{
          text:'CONFIRMAR',
          action: function () {
            document.getElementById('posted').value='2';
		        document.form1.submit();
          }
        },
        cancel:{
          text:'CANCELAR',
          action: function () {
          }
        },
     }
  });  
}

</script>
<?php include("footer.php");?></body>
</html>