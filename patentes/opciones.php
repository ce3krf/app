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
	
include("db.php");	
	

$sql="set names utf8";
if(!$result = $db->query($sql)){
    die('Hay un error [' . $db->error . ']');
}

$sql="SELECT `proyectos`.`proyectos_area` FROM   `proyectos` GROUP BY   proyectos.proyectos_area ORDER BY   `proyectos`.`proyectos_area`";
  
if(!$restipo = $db->query($sql)){
    die('Hay un error [' . $db->error . ']');
}
$row_tipo = $restipo->fetch_assoc();  



$sql="SELECT 
  proyectos_estado
FROM
  proyectos
GROUP BY
  proyectos_estado
ORDER BY
  proyectos_estado";
  
if(!$resestado = $db->query($sql)){
    die('Hay un error [' . $db->error . ']');
}
$row_estado = $resestado->fetch_assoc();  

?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<link rel="stylesheet" href="css/styles.css">	
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>
</head>

<body>
<div id="mainContainer" style="width:100%">


<b>Área estratégica</b>
<br />

<div class="row">
<select style="width:100%" onchange="document.form1.submit();" name="tipo" id="tipo">
  <option value="">Todas</option>
  <?php do { ?>
  <option <?php if($row_tipo['proyectos_area']==$_REQUEST['tipo']){ echo 'selected="selected"';}?> value="<?php echo $row_tipo['proyectos_area'];?>"><?php echo $row_tipo['proyectos_area'];?></option>
  <?php } while($row_tipo = $restipo->fetch_assoc());?>
</select>
</div>

<b>Estado</b>
<br />

<div class="row">
<select style="width:100%" onchange="document.form1.submit();" name="estado" id="estado">
  <option value="">Todos</option>
  <?php do { ?>
  <option <?php if($row_estado['proyectos_estado']==$_REQUEST['estado']){ echo 'selected="selected"';}?> value="<?php echo $row_estado['proyectos_estado'];?>"><?php echo $row_estado['proyectos_estado'];?></option>
  <?php } while($row_estado = $resestado->fetch_assoc());?>
</select> 
</div>

</div>
</body>
</html>