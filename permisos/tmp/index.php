<?php 
// ************************************************************
// Autor: Marcelo Jiménez
// Fulltrust Software
// https://www.fulltrust.net
// Fecha última actualización: 2025-01-05 11:13:01
// ************************************************************


//error_reporting(E_ALL);
//ini_set('display_errors', '1');

@session_start();
$session = session_id();

include("conf/db.php");	
	
$sql="set names utf8";
if(!$result = $db->query($sql)){
    die('Hay un error [' . $db->error . ']');
}


$sql="SELECT *  FROM   `areas`  ORDER BY  area";
if(!$resarea = $db->query($sql)){
    die('Hay un error [' . $db->error . ']');
}
$row_area = $resarea->fetch_assoc();  

$sql="select peredifica.destino2 from peredifica 
group by
peredifica.destino2
order by
peredifica.destino2";
if(!$resa = $db->query($sql)){
    die('Hay un error [' . $db->error . ']');
}
$row_a = $resa->fetch_assoc();  


$sql="SELECT *  FROM   `tipo`  ORDER BY  tipo";
if(!$restipo = $db->query($sql)){
    die('Hay un error [' . $db->error . ']');
}
$row_tipo = $restipo->fetch_assoc();  

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

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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


<!-- side menu -->
<div id="side_menu">

<div id="mainContainer" style="padding:5px !important">
<div class="container">

<div style="text-align: right; margin-right: 10px;">
	<a onClick="opciones();" href="javascript:;" style="text-align:right; cursor:pointer; text-decoration: none; color: black"><b>X</b></a>
</div>

<!----------------------> 
<div class="row dark">
<div class="col dark">

  <div style="border-bottom: 1px solid darkblue; margin-bottom: 15px"> 
  <span class="ftitle"><b>ÁMBITO</b></span>
  &nbsp;(<a onclick="checkarea();" style="color:#780116; text-decoration:none" href="javascript:;">Todas</a>
  &nbsp;-&nbsp;<a onclick="uncheckarea();" style="color:#780116; text-decoration:none" href="javascript:;">Ninguna</a>)
  </div>

  <div class="form-group">

      <?php do { ?>

        <div class="form-check form-check-inline">
          <input class="form-check-input" type="checkbox" checked value="<?php echo "'".$row_area['codigo']."'";?>" id="<?php echo $row_area['codigo'];?>" name="area">
          <label class="form-check-label dark" style="width: 80px !important; vertical-align:top; font-size:13px; border-bottom: 0px" for="<?php echo $row_area['codigo'];?>">
          <?php echo trim($row_area['area']);?>
          </label>
        </div>

      <?php } while( $row_area = $resarea->fetch_assoc() );?>

      


  </div>

</div>  
</div>
<!----------------------> 

<div class="sep10"></div>


<!----------------------> 
<div class="row dark">
<div class="col dark">

<div style="border-bottom: 1px solid darkblue; margin-bottom: 15px"> 
<span class="ftitle"><b>TIPO</b></span>
  &nbsp;(<a onclick="checktipo();" style="color:#780116; text-decoration:none" href="javascript:;">Todos</a>
  &nbsp;-&nbsp;<a onclick="unchecktipo();" style="color:#780116; text-decoration:none" href="javascript:;">Ninguno</a>)
</div>

  <div class="form-group">

      <?php do { ?>

        <div class="form-check form-check-inline">
          <input class="form-check-input" type="checkbox" checked value="<?php echo "'".$row_tipo['codigo']."'";?>" id="<?php echo $row_tipo['codigo'];?>" name="tipo">
          <label class="form-check-label dark" style="width: 80px !important; vertical-align:top; font-size:13px; border-bottom: 0px" for="<?php echo $row_tipo['codigo'];?>">
          <?php echo trim($row_tipo['tipo']);?>
          </label>
        </div>

      <?php } while( $row_tipo = $restipo->fetch_assoc() );?>

      


  </div>

</div>  
</div>
<!----------------------> 


<!----------------------> 
<div class="row dark">
<div class="col dark">

<div style="margin-top: 15px; border-bottom: 1px solid darkblue; margin-bottom: 15px"> 
<span class="ftitle"><b>ORIGEN</b></span>
  &nbsp;(<a onclick="checkorigen();" style="color:#780116; text-decoration:none" href="javascript:;">Todos</a>
  &nbsp;-&nbsp;<a onclick="uncheckorigen();" style="color:#780116; text-decoration:none" href="javascript:;">Ninguno</a>)
</div>

  <div class="form-group">

      <?php do { ?>

        <div class="form-check form-check-inline">
          <input class="form-check-input" type="checkbox" checked value="<?php echo "'".$row_origen['origen']."'";?>" id="<?php echo $row_origen['origen'];?>" name="origen">
          <label class="form-check-label dark" style="width: 80px !important; vertical-align:top; font-size:13px; border-bottom: 0px" for="<?php echo $row_origen['origen'];?>">
          <?php echo trim($row_origen['origen']);?>
          </label>
        </div>

      <?php } while( $row_origen = $resorigen->fetch_assoc() );?>

      


  </div>

</div>  
</div>
<div style="height: 15px; clear:both"></div>
<!----------------------> 


<!----------------------> 
<div class="row dark">
<div class="col dark">

<div style="border-bottom: 1px solid darkblue; margin-bottom: 15px"> 
<span class="ftitle"><b>ESTADO</b></span>
  &nbsp;(<a onclick="checkestado();" style="color:#780116; text-decoration:none" href="javascript:;">Todos</a>
  &nbsp;-&nbsp;<a onclick="uncheckestado();" style="color:#780116; text-decoration:none" href="javascript:;">Ninguno</a>)
</div>

  <div class="form-group">

      <?php do { ?>

        <div class="form-check form-check-inline">
          <input class="form-check-input" type="checkbox" checked value="<?php echo "'".$row_estado['estado']."'";?>" id="<?php echo $row_estado['estado'];?>" name="estado">
          <label class="form-check-label dark" style="width: 80px !important; vertical-align:top; font-size:13px; border-bottom: 0px" for="<?php echo $row_estado['estado'];?>">
          <?php echo trim($row_estado['estado']);?>
          </label>
        </div>

      <?php } while( $row_estado = $resestado->fetch_assoc() );?>

      


  </div>


</div>  
</div>
<!----------------------> 

    
 
    

<div style="height:10px; clear:both"></div>
<!----------------------> 
<div class="row">
<div class="col">

  <div class="form-group" style="text-align:center">
    <input type="button" class="btn btn-primary"  style="font-size:14px !important" value="Aplicar" onclick="muestra(); opciones();">    
    <input type="button" class="btn btn-secondary"  style="font-size:14px !important" value="Reestablecer" onclick="checkarea(); checktipo(); checkorigen(); checkestado(); muestra();">    
    <input type="button" class="btn btn-dark"  style="font-size:14px !important" value="Cerrar" onclick="opciones();">    
  </div>

</div>  
</div>
<!----------------------> 

</div>  
</div>

<div style="height:50px;"></div>

</div>
<!-- side menu -->


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
  <a onclick="" href="javascript:;" style="text-decoration:none; color:white">
    <i class="fas fa-tasks"></i>&nbsp; Estado de avance
  </a>
</div>

<div id="m4">
  <a style="text-decoration:none; color:white" href="javascript:;" onclick="">
    <i class="fa-regular fa-square-check"></i>&nbsp; Filtrar información
  </a>
</div>


<div id="m5">

  <a onclick="s();" style="text-decoration:none; color:white" href="javascript:;">
    <i class="fas fa-info-circle"></i> Simbología
  </a>

  <div id="m5-container">

  <ul>

  <li style="text-align:left !important; margin-left:-20px; list-style-type:none; cursor:pointer">
    <div class="square" style="background-color:#FF5733"></div>Comercial
  </li>
  <li style="text-align:left !important; margin-left:-20px; list-style-type:none; cursor:pointer">
    <div class="square" style="background-color:#FFD700"></div>Energía
  </li>
  <li style="text-align:left !important; margin-left:-20px; list-style-type:none; cursor:pointer">
    <div class="square" style="background-color:#8B008B"></div>Equipamiento
  </li>
  <li style="text-align:left !important; margin-left:-20px; list-style-type:none; cursor:pointer">
    <div class="square" style="background-color:#00CED1"></div>Habitacional
  </li>
  <li style="text-align:left !important; margin-left:-20px; list-style-type:none; cursor:pointer">
    <div class="square" style="background-color:#696969"></div>Industria
  </li>
  <li style="text-align:left !important; margin-left:-20px; list-style-type:none; cursor:pointer">
    <div class="square" style="background-color:#32CD32"></div>Salud, Educación, Deportes, Cultura
  </li>
  <li style="text-align:left !important; margin-left:-20px; list-style-type:none; cursor:pointer">
    <div class="square" style="background-color:#1E90FF"></div>Servicios
  </li>
  <li style="text-align:left !important; margin-left:-20px; list-style-type:none; cursor:pointer">
    <div class="square" style="background-color:#FF69B4"></div>Turismo
  </li>

  </ul>


  <div class="text-center">Clic para filtrar</div>


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
          <th class="encabezado" style="width:10%;">ÁMBITO </th>                      
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



<script src="app.js"></script> 

</body>
</html>