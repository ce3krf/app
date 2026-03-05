<?php 
// ************************************************************
// Autor: Marcelo Jiménez
// Fulltrust Software
// https://www.fulltrust.net
// Fecha última actualización: 2025-05-31 19:06:07
// ************************************************************
?>
<?php
@session_start();

//error_reporting(E_ALL);
//ini_set('display_errors', 1);

include_once("../conf/db.php");

date_default_timezone_set('America/Santiago');


$sql="set names utf8";
if(!$result = $db->query($sql)){
    die('Hay un error [' . $db->error . ']');
}


if ($_POST["posted"]=="1"){
	
	$sql="select * from usuarios where usuarios_userid='".$_POST["userid"]."' and usuarios_password='".md5($_POST["password"])."' limit 1"; 
	if(!$restabla = $db->query($sql)){
    	die('Hay un error [' . $db->error . ']');
	}
	$row_tabla = $restabla->fetch_assoc();  	
	$count = $restabla->num_rows;
	
	
	if($count > 0){

		$_SESSION["usuario"]=$row_tabla["usuarios_userid"];
		$_SESSION["nombre"]= $row_tabla["usuarios_nombre"];
		$_SESSION["idx"]=     $row_tabla["usuarios_id"];	

    $_SESSION["net_fulltrust_fas_users_id"] = $row_tabla["usuarios_userid"];
    $_SESSION["net_fulltrust_fas_users_password"] = $_POST["password"];


    // registra la fecha y hora del login
    $sql="update usuarios set last_login='".date("Y-m-d H:i:s")."' where usuarios_id='".$row_tabla["usuarios_id"]."'"; 
    if(!$restabla = $db->query($sql)){
        die('Hay un error [' . $db->error . ']');
    }    
			
		?>
    <script>
    
		window.location='menu.php';
		</script>
        <?php
		
		
	}
	
	
}



?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" type="image/png" href="../favicon.png">


  <title>Ilustre Municipalidad de San José de Maipo</title>
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">

  <link rel="stylesheet" type="text/css" href="../css/styles.css"/>
  
</head>

  <style>
    body {
      background: url("img/bg/Aduana_Cajon_del_Maipo.jfif") no-repeat center center fixed;
      -webkit-background-size: cover;
      -moz-background-size: cover;
      -o-background-size: cover;
      background-size: cover;
    }
  </style>

<body onLoad="document.getElementById('userid').focus();">
  <div class="container">
    <div class="row justify-content-center mt-5">
      <div class="col-md-6">
        <div class="card">
          <div class="card-header bg-light " style="text-align:center !important; ">
            <img src="./img/logo_website_2021.png" style="width: 220px; margin-bottom: 10px" /> 
            <h5 class="text-center">PLAN MAESTRO PARA LA INVERSIÓN</h5>
          </div>
          <div class="card-body">
            <form method="post" action="">
              <div class="form-group">
                <span class="label label-default">ID de usuario</span>
                <input type="text" class="form-control" id="userid" placeholder="" name="userid">
              </div>
              <div class="form-group">
                <span class="label label-default" >Contraseña</span>
                <input type="password" class="form-control" id="password" placeholder="" name="password">
              </div>
              <button type="submit" class="btn btn-primary btn-block">Iniciar sesión</button>
              <input type="hidden" name="posted" id="posted"  value="1" />
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/js/bootstrap.min.js"></script>

</html>
