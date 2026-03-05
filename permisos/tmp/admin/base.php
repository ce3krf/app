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

?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1">	
<title><?php echo $row_param["parametros_titulo"];?></title>


<script src="js/jquery-3.5.1.min.js"></script>	
	
<link rel="icon" type="image/png" href="../img/gobierno-regional-ohiggins.png">
	


	
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
            <main role="main" class="col-md-9 ms-sm-auto col-lg-10 px-md-4">

                <div class="row top-info-div">
                    <div class="col">
                        <h5>PROYECTOS</h5>
                    </div>
                    <div class="col text-end">
                    <button id="bexcel" type="button" class="btn btn-secondary btn-excel"><i class="fa-solid fa-file-arrow-down"></i>&nbsp;Exportar datos</button>
                    <button onclick="window.location='proyectos_editar.php?proyectos_id=0'" type="button" class="btn btn-secondary"><i class="fa-solid fa-file-circle-plus"></i>&nbsp;Nuevo proyecto</button>
                    </div>                        
                </div>

                <div class="container">
                    <p><?php echo $row_param["parametros_titulo"];?></p>

                    <div class="row">
                        here
                    </div>    


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
