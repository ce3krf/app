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
// Fecha última actualización: 2025-09-19 13:15:04
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






$sql="SELECT localidad FROM peredifica group by localidad ORDER BY localidad";
if(!$localidad = $db->query($sql)){
    die('Hay un error [' . $db->error . ']');
}
$row_localidad = $localidad->fetch_assoc();  


$sql="SELECT destino2 FROM   `peredifica` where destino2 is not null   group by destino2   ORDER BY destino2 ";
if(!$destino = $db->query($sql)){
    die('Hay un error [' . $db->error . ']');
}
$row_destino = $destino->fetch_assoc();  


?>


<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">	
<title>PLAN MAESTRO PARA LA INVERSIÓN - ILUSTRE MUNICIPALIDAD DE SAN JOSÉ DE MAIPO</title>

<link rel="icon" type="image/png" href="favicon.png">

<script src="https://code.jquery.com/jquery-3.6.1.js" integrity="sha256-3zlB5s2uwoUzrXK3BT7AX3FyvojsraNFxCc2vC/7pNI=" crossorigin="anonymous"></script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>

    <!-- Leaflet CSS y JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

    <!-- Leaflet MarkerCluster CSS y JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css" />
    
    <script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>







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
          <input class="form-check-input" type="checkbox" checked value="<?php echo "'".$row_tipo['codigo']."'";?>" id="<?php echo $row_tipo['codigo'];?>" name="t">
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




  </div>

</div>





<div id="m6">

  <a onclick="filtros();" style="text-decoration:none; color:white" href="javascript:;">
    <i class="fas fa-filter"></i> Filtrar
  </a>

  <div id="m6-container">

  <ul>

  <?php
  // Define the categories and their colors
  $categories = [
    ['label' => 'Comercial', 'color' => '#FF5733'],
    ['label' => 'Energía', 'color' => '#FFD700'],
    ['label' => 'Equipamiento', 'color' => '#8B008B'],
    ['label' => 'Habitacional', 'color' => '#00CED1'],
    ['label' => 'Industria', 'color' => '#696969'],
    ['label' => 'Salud, Educación, Deportes, Cultura', 'color' => '#32CD32'],
    ['label' => 'Servicios', 'color' => '#1E90FF'],
    ['label' => 'Turismo', 'color' => '#FF69B4'],
  ];
  foreach ($categories as $cat):
  ?>
    <li style="text-align:left !important; margin-left:-20px; list-style-type:none; cursor:pointer">
    <label style="cursor:pointer;">
      <input onclick="muestra();" name="tipo" type="checkbox" class="category-filter" value="<?php echo htmlspecialchars($cat['label']); ?>">
      <div class="square" style="background-color:<?php echo $cat['color']; ?>; display:inline-block; vertical-align:middle;"></div>
      <?php echo $cat['label']; ?>
    </label>
    </li>
  <?php endforeach; ?>

  </ul>

  <div style="margin-top:10px; text-align:center;">
    <a href="javascript:;" onclick="$('.category-filter').prop('checked', true);muestra();" style="color:#ffffff; text-decoration:none;">Todos</a>
    &nbsp;-&nbsp;
    <a href="javascript:;" onclick="$('.category-filter').prop('checked', false);muestra();" style="color:#ffffff; text-decoration:none;">Ninguno</a>
  </div>

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


        <!-- ------->
        <div class="bloque-titulo">
        LOCALIDAD
        </div>	  

        <?php do { ?>

        <div class="gtipo form-check form-switch form-check-inline">
          <input style="margin-left:5px !important" type="checkbox" name="localidad[]" value="<?php echo htmlspecialchars($row_localidad["localidad"]);?>" id="<?php echo htmlspecialchars($row_localidad["localidad"]);?>" class="chk form-check-input">
          <label for="<?php echo htmlspecialchars($row_localidad["localidad"]);?>" class="form-check-label" style="border-width:0px !important"><?php echo htmlspecialchars($row_localidad["localidad"]);?></label>
        </div>

        <?php } while ( $row_localidad = $localidad->fetch_assoc() ); ?>
        <!-- ------->

        <!-- ------->
        <div class="bloque-titulo">
        DESTINO
        </div>	  

        <?php do { ?>

        <div class="gtipo form-check form-switch form-check-inline">
          <input style="margin-left:5px !important" type="checkbox" name="destino[]" value="<?php echo htmlspecialchars($row_destino["destino2"]);?>" id="<?php echo htmlspecialchars($row_destino["destino2"]);?>" class="chk form-check-input">
          <label for="<?php echo htmlspecialchars($row_destino["destino2"]);?>" class="form-check-label" style="border-width:0px !important"><?php echo htmlspecialchars($row_destino["destino2"]);?></label>
        </div>

        <?php } while ( $row_destino = $destino->fetch_assoc() ); ?>
        <!-- ------->


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