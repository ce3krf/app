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
// Fecha última actualización: 2025-09-19 17:51:09
// ************************************************************

//error_reporting(E_ALL);
//ini_set('display_errors', 1);

@session_start();
$session = session_id();

include("../conf/db.php");	
	
$sql="set names utf8";
if(!$result = $db->query($sql)){
    die('Hay un error [' . $db->error . ']');
}


$sql="SELECT *  FROM   `sectores`  ORDER BY  sector_descripcion";
if(!$ressector = $db->query($sql)){
    die('Hay un error [' . $db->error . ']');
}
$row_sector = $ressector->fetch_assoc();  

// Guardamos una copia para usar en los filtros
$sql="SELECT *  FROM   `sectores`  ORDER BY  sector_descripcion";
if(!$ressector_filtro = $db->query($sql)){
    die('Hay un error [' . $db->error . ']');
}
$row_sector_filtro = $ressector_filtro->fetch_assoc();


$sql="SELECT *  FROM   `procesos`  ORDER BY  procesos_descripcion";
if(!$resproceso = $db->query($sql)){
    die('Hay un error [' . $db->error . ']');
}
$row_proceso = $resproceso->fetch_assoc();  

$sql="SELECT *  FROM   `origen`  ORDER BY  origen";
if(!$resorigen = $db->query($sql)){
    die('Hay un error [' . $db->error . ']');
}
$row_origen = $resorigen->fetch_assoc();  


$sql="SELECT *  FROM   `estados`  ORDER BY  id";
if(!$resestado = $db->query($sql)){
    die('Hay un error [' . $db->error . ']');
}
$row_estado = $resestado->fetch_assoc();  


$q = "update stats set visitas = visitas + 1";
if(!$preg = $db->query($q)){
	die('Ha ocurrido un error ejecutando la consulta [' . $db->error . ']');
}


?>


<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">	
<title>PLAN MAESTRO PARA LA INVERSIÓN - ILUSTRE MUNICIPALIDAD DE SAN JOSÉ DE MAIPO</title>

<link rel="icon" type="image/png" href="favicon.png">



    <!-- Leaflet CSS y JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

    <!-- Leaflet MarkerCluster CSS y JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css" />
    
    <script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>






<script src="https://code.jquery.com/jquery-3.6.1.js" integrity="sha256-3zlB5s2uwoUzrXK3BT7AX3FyvojsraNFxCc2vC/7pNI=" crossorigin="anonymous"></script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

<script src="https://kit.fontawesome.com/7c2086f90b.js" crossorigin="anonymous"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js" integrity="sha512-GsLlZN/3F2ErC5ifS5QtgpiJtWd43JWSuIgh7mbzZ8zBps+dvLusV+eNQATqgA/HdeKFVgA5v3S/cIrLF7QnIg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script src="js/Bing.js"></script> 


<link rel="stylesheet" type="text/css" href="js/DataTables/dataTables.searchHighlight.css">

<link rel="stylesheet" type="text/css" href="js/DataTables/datatables.css">
<link rel="stylesheet" type="text/css" href="js/DataTables/buttons.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="js/DataTables/dataTables.searchHighlight.css">
<script type="text/javascript" charset="utf8" src="js/DataTables/datatables.js"></script>
<script type="text/javascript" charset="utf8" src="js/DataTables/jquery.highlight.js"></script>
<script type="text/javascript" charset="utf8" src="js/DataTables/dataTables.searchHighlight.js"></script>







<link rel="stylesheet" href="css/styles-mapa.css">


</head>

<body style="margin:0px !important">


<!-- tools -->
<div id="box">
  <div class="box-inner">


<div id="cerrar_filtros">
  <a onclick="$('#box').toggle(250);" href="javascript:;" style="color: white; text-decoration:none">x</a>
</div>

	  
<form id="form1" name="form1">
<input type="hidden" name="ntem" id="ntem" value="<?php echo $ntem;?>">
	



<!-- selects -->
<div id="selects">

<div class="container" style="margin: 5px !important">
    <div class="row">
        <div class="col text-white form-check-label">


        <!-- SECTOR -->
        <div class="bloque-titulo">
        SECTOR
        </div>	  

        <?php do { ?>

        <div class="gtipo form-check form-switch form-check-inline">
          <input style="margin-left:5px !important" type="checkbox" name="sector[]" value="<?php echo htmlspecialchars($row_sector_filtro["sector_id"]);?>" id="sector_<?php echo htmlspecialchars($row_sector_filtro["sector_id"]);?>" class="chk form-check-input">
          <label for="sector_<?php echo htmlspecialchars($row_sector_filtro["sector_id"]);?>" class="form-check-label" style="border-width:0px !important"><?php echo htmlspecialchars($row_sector_filtro["sector_descripcion"]);?></label>
        </div>

        <?php } while ( $row_sector_filtro = $ressector_filtro->fetch_assoc() ); ?>
        <!-- SECTOR -->

        <!-- PROCESO -->
        <div class="bloque-titulo">
        PROCESO
        </div>	  

        <?php do { ?>

        <div class="gtipo form-check form-switch form-check-inline">
          <input style="margin-left:5px !important" type="checkbox" name="proceso[]" value="<?php echo htmlspecialchars($row_proceso["procesos_id"]);?>" id="proceso_<?php echo htmlspecialchars($row_proceso["procesos_id"]);?>" class="chk form-check-input">
          <label for="proceso_<?php echo htmlspecialchars($row_proceso["procesos_id"]);?>" class="form-check-label" style="border-width:0px !important"><?php echo htmlspecialchars($row_proceso["procesos_descripcion"]);?></label>
        </div>

        <?php } while ( $row_proceso = $resproceso->fetch_assoc() ); ?>
        <!-- PROCESO -->

        <!-- ESTADO -->
         <!--
        <div class="bloque-titulo">
        ESTADO
        </div>	  

        <?php do { ?>

        <div class="gtipo form-check form-switch form-check-inline">
          <input style="margin-left:5px !important" type="checkbox" name="estado[]" value="<?php echo htmlspecialchars($row_estado["estado"]);?>" id="estado_<?php echo htmlspecialchars($row_estado["id"]);?>" class="chk form-check-input">
          <label for="estado_<?php echo htmlspecialchars($row_estado["id"]);?>" class="form-check-label" style="border-width:0px !important"><?php echo htmlspecialchars($row_estado["estado"]);?></label>
        </div>

        <?php } while ( $row_estado = $resestado->fetch_assoc() ); ?>
        -->
        <!-- ESTADO -->


        </div>
    </div>
</div>











</div>	  
<!-- selects -->

<div style="background-color: var(--fondo-filtros); padding: 10px; text-align: right; border-top: 1px solid var(--segundo);">	
  <button type="button" class="btn btn-info" id="selectall">Todos</button>
  <button type="button" class="btn btn-info" id="unselectall">Ninguno</button>


&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input onclick="$('#box').toggle(250);procesar();" style="float:right" name="aplicar1" type="button" class="btn btn-dark" id="aplicar1" value="Aplicar filtros">


</form>	  	

</div>

<div style="height: 15px; clear: both"></div>	  
	  

</div>	
</div>	


<div id="logo_container">
  <img src="img/logo.png" style="width: 120px; filter: drop-shadow(1px 1px 0px #222);">
</div>    




<div id="map_canvas"></div>	



<div id="m1">
  ...
</div>  

<div id="m2">
  <a class="tools" style="text-decoration:none; color:white" href="javascript:;" onclick="road();">Mapa</a> &nbsp; 
  <a class="tools" style="text-decoration:none; color:white" href="javascript:;" onclick="sat();">Satelite</a>&nbsp;
</div>  

<div id="m3">
  <a onclick="avance('', '');" href="javascript:;" style="text-decoration:none; color:white">
    <i class="fas fa-tasks"></i>&nbsp; Estado de avance
  </a>
</div>

<div id="m4">
  <a style="text-decoration:none; color:white" href="javascript:;">
    <i class="fa-regular fa-square-check"></i>&nbsp; Filtrar información
  </a>
</div>


<div id="m5">

  <a onclick="s();" style="text-decoration:none; color:white" href="javascript:;">
    <i class="fas fa-info-circle"></i> Simbología
  </a>

  <div id="m5-container">

  <ul>
  <?php 
  // Resetear el puntero del resultado para la simbología
  $ressector->data_seek(0);
  $row_sector = $ressector->fetch_assoc();
  do { ?>
  <li style="text-align:left !important; margin-left:-20px; list-style-type:none">
  <div class="square" style="background-color:<?php echo trim($row_sector['sector_color']);?>"></div><?php echo trim($row_sector['sector_descripcion']);?>
  </li>

  <?php } while( $row_sector = $ressector->fetch_assoc() );?>
  </ul>

  </div>

</div>




<div id="paneltabla">



  <div id="windowcontrols">  
    <a title="Ver / ocultar la lista de iniciativas" class="tools" href="javascript:;" onclick="restaurar();"><img id="icn" style="height:20px" src="img/list.png"></a>&nbsp;
  </div>  




 <!-- tools tabla --> 

<div  id ="sep30" style="height: 30px; clear:both"></div> 


<!-- tools tabla --> 


<div id="divs">
Mostrando <span id="filas">n</span> iniciativas  
&nbsp;  
  <input type="text" id="buscar" name="buscar" placeholder="Buscar">
  &nbsp;
  <!-- <a onclick="checkarea(); checktipo(); muestra();" style="color:white" href="javascript:;">Ver todos</a> -->
</div>  


<div id="esperar">
  <div class="image-wrapper">
    <img src="img/ajax-loader.svg" alt="Loading..." class="centered-image">
  </div>  
</div> 



  <table id="tabla" class="display" style="width: 100% !important;">
	<tbody>
	<thead>
    	<tr>
          <th class="encabezado" style="width:5% !important;text-align:center">VER</th>        
          <th class="encabezado" style="width:65%;">PROYECTO</th>        
          <th class="encabezado" style="width:10%;">SECTOR</th>                      
          <th class="encabezado" style="width:10%;">ESTADO</th>          
          <th class="encabezado" style="width:10%;">ORIGEN</th>                          
     	</tr>
    </thead>

	</tbody>
</table>



</div> 





<div id="snackbar">Some text some message..</div>











    <div id="zoom-in">
        +
    </div>  
    
    <div id="zoom-out">
        -
    </div> 

    <div id="acerca">
        <img src="img/info.png" alt="Acerca de" style="width: 28px; padding-top: 3px; cursor: pointer !important;">
    </div>




<!-- busqueda -->
<div id="divsearch">

<input style="display:none" type="text" value="<?php echo $session;?>" name="palab" id="palab">
  
<div class="input-group" style="padding:0px">
        <input type="search" class="form-control" id="searchInput" placeholder="Buscar...">

        <button id="gosearch" type="button" class="btn btn-custom" onclick="muestra();">
            <i class="fas fa-search"></i>
        </button>
        
</div>

</div>
<!-- busqueda -->



<div id="wait">
  <img src="img/loader.svg" alt="Loading..." class="centered-image">
</div>



<input type="hidden" value="<?php echo $session;?>" name="token" id="token">




    <!-- Logos flotantes en la parte inferior -->
    <div class="floating-logos">
        <div class="logos">
            <a href="https://www.gobiernosantiago.cl/" target="_blank" rel="noopener">
                <img src="img/lgore.png" alt="Gobierno Regional Metropolitano de Santiago">
            </a>
            <a href="https://www.coresantiago.cl/" target="_blank" rel="noopener">
                <img src="img/lcore.png" alt="Consejo Regional Metropolitano">
            </a>
            <a href="https://www.sanjosedemaipo.cl/" target="_blank" rel="noopener">
                <img src="img/lsjm.png" alt="Ilustre Municipalidad de San José de Maipo">
            </a>
            <img src="img/lpulso-1.png" alt="Pulso Consultores">
        </div>
    </div>







    

<script src="app.js"></script> 

</body>
</html>



