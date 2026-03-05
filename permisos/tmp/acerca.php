<?php 
// ************************************************************
// Autor: Marcelo Jiménez
// Fulltrust Software
// https://www.fulltrust.net
// Fecha última actualización: 2024-10-03 15:37:28
// ************************************************************
?>
<?php
include ("conf/db.php");


$q = "set names utf8";
if(!$preg = $db->query($q)){
	die('Ha ocurrido un error ejecutando la consulta [' . $db->error . ']');
}

$q = "select * from stats";
if(!$preg = $db->query($q)){
	die('Ha ocurrido un error ejecutando la consulta [' . $db->error . ']');
}
$row_stats = $preg->fetch_assoc();
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title style="line-height: 50px !important;"></title>
</head>

<body>
<div style="background-color: #ffffff; padding:5px;">	
<img src="img/tres-logos-sn-jose.png" style="width:90%">
</div>
<div style="margin-top: 10px; padding: 10px;">

<p style="font-size:1.3em; color:black">Este mapa interactivo presenta la localización y características de las iniciativas que conforman la cartera de inversión priorizada del Plan Maestro Comunal de Inversión de San José de Maipo. Cada marcador en el mapa representa un proyecto que busca contribuir al desarrollo sostenible, inclusivo y territorialmente equilibrado de la comuna, en coherencia con su vocación turística y su identidad local.</p> 
<p style="font-size:1.3em; color:black">Al explorar el mapa, los usuarios pueden hacer clic sobre cada iniciativa para conocer su ubicación, objetivos, estado de avance y detalles relevantes. Esta herramienta permite visualizar cómo las distintas propuestas se articulan en el territorio y facilita el acceso a información transparente y actualizada sobre el proceso de planificación comunal. Además, desde la ficha de cada proyecto es posible descargar una versión en PDF con información detallada, fomentando así la participación y el seguimiento ciudadano.</p>


</div>

<hr>
<p style="color: gray"><?php echo number_format($row_stats["visitas"], 0, '.', ',');?> visitas desde junio de 2025</p>  	
	</body>
</html>