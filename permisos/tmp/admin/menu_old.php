<?php 
// ************************************************************
// Autor: Marcelo Jiménez
// Fulltrust Software
// https://www.fulltrust.net
// Fecha última actualización: 2024-07-15 10:49:52
// ************************************************************
?>
<?php

//error_reporting(E_ALL);
//ini_set('display_errors', 1);

	@session_start();
	
	
	include("../conf/db.php");		
	
	
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
	
	
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1">	
<title>Estrategia Regional de Residuos Sólidos Domiciliarios y Economía Circular en la Región de O'Higgins 2024 – 2036</title>

<link rel="icon" type="image/png" href="../img/gobierno-regional-ohiggins.png">

	
<link rel="stylesheet" href="../css//mini-default.css">
	
<script src="js/jquery-3.5.1.min.js"></script>	
	
	
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>


<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>	
	
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" integrity="sha512-MV7K8+y+gLIBoVD59lQIYicR65iaqukzvf/nwasF0nqhPay5w/9lJmVM2hMDcnK1OnMGCdVK+iQrJ7lzPJQd1w==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<link rel="stylesheet" type="text/css" href="../css/styles.css"/>
	

<style>
i {
	color: #e63946 !important;
}	
.btn, button {
	width:90% !important;
}
</style>	

</head>

<body>


<?php include("./header.php");?>


<div id="mainContainer">

<!---------------------->
<div class="menu_arriba">
  	<div class="div300l">Menú principal</div>
    <div class="div300r">
		<img src="img/user.png">&nbsp;<?php echo $_SESSION["nombre"];?>&nbsp;&nbsp;&nbsp;
		<a href="logout.php"><img src="img/if_system-log-out_15324.png" width="22" height="22"/>&nbsp;Cerrar sesión</a>
	</div>
</div>
<!---------------------->

<br />

<div class="tip">
El menú principal tiene la función de proporcionar a los usuarios un acceso rápido y conveniente a las diferentes funciones, características y secciones principales de la aplicación. 	
</div>	
	
<div style="width: 450px; margin-left: auto; margin-right: auto; text-align: center; border-width: 1px; border-style: solid; border-color: #0084d4">

	<table style="width:100%" cellpad="10" cellspacing="5">

	<tr>
	<td colspan="3" style="text-align:center">
	<div style="padding:15px; color: black; background-color: #f0f0f0">
	<strong>OPCIONES</strong>	
	</div>
	</td>
	</tr>		

	<tr>
	<td style="width:50%; text-align:center">
	<button onclick="window.location='proyectos.php'" type="button" class="btn btn-light"><i class="fa-solid fa-sheet-plastic"></i>&nbsp;PROYECTOS</button>
	<td>
	<td style="width:50%; text-align:center">
	<button onclick="window.location='ubicaciones.php'" type="button" class="btn btn-light"><i class="fa-solid fa-location-dot"></i>&nbsp;UBICACIONES</button>	
	<td>
	</tr>		

	<tr>
	<td style="width:50%; text-align:center">
	<button onclick="window.location='areas.php'" type="button" class="btn btn-light"><i class="fa-solid fa-tag"></i>&nbsp;ÁREAS</button>
	<td>
	<td style="width:50%; text-align:center">
	<button onclick="window.location='origenes.php'" type="button" class="btn btn-light"><i class="fa-brands fa-sourcetree"></i>&nbsp;ORÍGENES</button>	
	<td>

	<tr>
	<td style="width:50%; text-align:center">
	<button onclick="window.location='tipos.php'" type="button" class="btn btn-light"><i class="fa-solid fa-layer-group"></i>&nbspTIPOS</button>	
	</td>	

	<?php 
	if($row_tabla["usuarios_profile"] <> 'ADMINISTRADOR'){
		$link="usuarios_editar.php?id=".$_SESSION["idx"];
	} else { 
		$link="usuarios.php";
	}
	?>		


	<td colspan="3" style="text-align:center">
	<button onclick="window.location='<?php echo $link;?>'" type="button" class="btn btn-light"><i class="fa-solid fa-users"></i>&nbspUSUARIOS</button>	
	</td>	
	<td>
	</tr>	

	<tr>
	<td colspan="3" style="text-align:center">
<button onclick="avance('', '');" type="button" class="btn btn-light"><i class="fa-solid fa-sheet-plastic"></i>&nbspESTADO DE AVANCE</button>	
	</td>
</tr>

<?php 
	if($row_tabla["usuarios_profile"] == 'ADMINISTRADOR'){
?>		
	<tr>
	<td colspan="3" style="text-align:center">
<button onclick="window.location='respaldo_db.php'" type="button" class="btn btn-light"><i class="fa-solid fa-sheet-plastic"></i>&nbspRESPALDO DE LA BASE DE DATOS</button>	
	</td>
	</tr>			
<?php } ?>	
	

	</table>


<script>
      function avance(area, tipo){
        
        var x = Math.random();
        var w_avance = $.confirm({
                  title: '',
                  type: 'blue',
                  boxWidth: '95%',
                  typeAnimated: true,
                  closeIcon: true,
                  useBootstrap: false,
                  confirmButtonColor: '#d296dd',
                  buttons: {
                     info: {
                      text: 'Continuar', 
                      btnClass: 'btn-blue',
                      action: function(){}
                      }
                 },
    
                  content: 'url:../tabla.php?area='+area+'&tipo='+tipo+"&x="+x
              }); 		
    }	
</script>	


	

</div>	
	
</div>




<hr>	
<?php include("footer.php");?></body>
</html>