<?php 
// ************************************************************
// Autor: Marcelo Jiménez
// Fulltrust Software
// https://www.fulltrust.net
// Fecha última actualización: 2025-01-03 16:27:18
// ************************************************************
?>
<?php
// last release 2023-07-1 12:03
//error_reporting(E_ALL);
//ini_set('display_errors', '1');

@session_start();
	
include("conf/db.php");	
	

$sql="set names utf8";
if(!$result = $db->query($sql)){
    die('Hay un error [' . $db->error . ']');
}


$sql="SELECT 
`proyectos`.`area`,
`proyectos`.`tipo`,  
`proyectos`.`estado`,
count(proyectos.id) as cuantos
FROM
`proyectos`
LEFT OUTER JOIN `estados` ON (`proyectos`.`estado` = `estados`.`estado`)
WHERE
`proyectos`.`area` <> '' and proyectos_status=1 ";

if($_GET["area"]<>""){
    $sql.=" and area='".$_GET["area"]."' ";
}

if($_GET["tipo"]<>""){
    $sql.=" and tipo='".$_GET["tipo"]."' ";
}


$sql.="group by
`proyectos`.`area`,
`proyectos`.`tipo`,
`proyectos`.`estado`
ORDER BY
`proyectos`.`area`,
`proyectos`.`tipo`,
`proyectos`.`estado`";




if(!$datos = $db->query($sql)){
    die('Hay un error [' . $db->error . ']');
}
$row_datos = $datos->fetch_assoc();




//****************************************** */
$sql="update proyectos, areas
set proyectos.area = areas.area
where 
proyectos.cod_area = areas.codigo";

if(!$areas = $db->query($sql)){
    die('Hay un error [' . $db->error . ']');
}

$sql="update proyectos
set tipo='Proyecto'
where cod_tipo='c'";

if(!$areas = $db->query($sql)){
    die('Hay un error [' . $db->error . ']');
}

$sql="update proyectos
set tipo='Estudio'
where cod_tipo='a'";

if(!$areas = $db->query($sql)){
    die('Hay un error [' . $db->error . ']');
}

$sql="update proyectos
set tipo='Programa'
where cod_tipo='b';";

if(!$areas = $db->query($sql)){
    die('Hay un error [' . $db->error . ']');
}
//****************************************** */


$sql="SELECT `proyectos`.`area` from proyectos
WHERE
`proyectos`.`area` <> ''
group by area
order by area";

if(!$areas = $db->query($sql)){
    die('Hay un error [' . $db->error . ']');
}
$row_areas = $areas->fetch_assoc();




$sql="SELECT `proyectos`.`tipo` from proyectos
WHERE
`proyectos`.`tipo` <> ''
group by tipo
order by tipo";

if(!$tipos = $db->query($sql)){
    die('Hay un error [' . $db->error . ']');
}
$row_tipos = $tipos->fetch_assoc();




//********************************** */

$sql="delete from tabla where true";
if(!$result = $db->query($sql)){
    die('Hay un error [' . $db->error . ']');
}


do {

    $sql = "select * from tabla where area='".$row_datos["area"]."' and tipo='".$row_datos["tipo"]."' ";
    //echo $sql."<br>";
    if(!$r = $db->query($sql)){
        die('Hay un error [' . $db->error . ']');
    }   
    $row_r = $r->fetch_assoc();

    // no hay datos 
    if ($row_r["area"]==""){
        $sql ="insert into tabla (area, tipo) values (";
        $sql.="'".$row_datos["area"]."', ";
        $sql.="'".$row_datos["tipo"]."') ";
        if(!$result = $db->query($sql)){
            die('Hay un error [' . $db->error . ']');
        }   
    }

    switch ($row_datos["estado"]) {
        case "En espera":
            $campo="e1l";
            $campo1="e1v";
            $valor="En espera";
            break;
        case "Formulada":
            $campo="e2l";
            $campo1="e2v";
            $valor="Formulada";
            break;
        case "Postulada a financiamiento":
            $campo="e3l";
            $campo1="e3v";
            $valor="Postulada a financiamiento";
            break;
        case "En licitación":
            $campo="e4l";
            $campo1="e4v";
            $valor="En licitación";
            break;
        case "Etapa en ejecución":
            $campo="e5l";
            $campo1="e5v";
            $valor="Etapa en ejecución";
            break;
        case "Detenida por fuerza mayor":
            $campo="e6l";
            $campo1="e6v";
            $valor="Detenida por fuerza mayor";
            break;
        case "Sustituida":
            $campo="e7l";
            $campo1="e7v";
            $valor="Sustituida";
            break;     
        case "Eliminada":
            $campo="e8l";
            $campo1="e8v";
            $valor="Eliminada";
            break;                     
            
    }    

    $sql ="update tabla set ".$campo." ='".$valor."', ".$campo1."='".$row_datos["cuantos"]."' where ";
    $sql.="area ='".$row_datos["area"]."' and ";
    $sql.="tipo ='".$row_datos["tipo"]."' ";
    //echo $sql."<br><br>";
    if(!$result = $db->query($sql)){
        die('Hay un error [' . $db->error . ']');
    }   


} while( $row_datos = $datos->fetch_assoc() );






// actualiza totales
$sql = "select * from tabla ";
if(!$tabla = $db->query($sql)){
    die('Hay un error [' . $db->error . ']');
}   
$row_tabla = $tabla->fetch_assoc();





do {

    $t=0;
    $t=$t+$row_tabla["e1v"];
    $t=$t+$row_tabla["e2v"];
    $t=$t+$row_tabla["e3v"];
    $t=$t+$row_tabla["e4v"];
    $t=$t+$row_tabla["e5v"];
    $t=$t+$row_tabla["e6v"];
    $t=$t+$row_tabla["e7v"];
    $t=$t+$row_tabla["e8v"];

    $p1=(($row_tabla["e1v"] / $t) * 100);
    $p2=(($row_tabla["e2v"] / $t) * 100);
    $p3=(($row_tabla["e3v"] / $t) * 100);
    $p4=(($row_tabla["e4v"] / $t) * 100);
    $p5=(($row_tabla["e5v"] / $t) * 100);
    $p6=(($row_tabla["e6v"] / $t) * 100);
    $p6=(($row_tabla["e7v"] / $t) * 100);
    $p6=(($row_tabla["e8v"] / $t) * 100);

    $pt=$p1+$p2+$p3+$p4+$p5+$p6;

    $sql ="update tabla set ";
    $sql.="e1p=".$p1.", ";
    $sql.="e2p=".$p2.", ";
    $sql.="e3p=".$p3.", ";
    $sql.="e4p=".$p4.", ";
    $sql.="e5p=".$p5.", ";
    $sql.="e6p=".$p6.", ";
    $sql.="e7p=".$p6.", ";
    $sql.="e8p=".$p6.", ";
    $sql.="tt=".$t.", ";
    $sql.="tp=".$pt." ";
    $sql.="where area ='".$row_tabla["area"]."' and tipo ='".$row_tabla["tipo"]."'";

    //echo $sql."<br><br>";

    if(!$result = $db->query($sql)){
        die('Hay un error [' . $db->error . ']');
    }   

} while( $row_tabla = $tabla->fetch_assoc() );


   
//********************************* */

?>


<style>
:root {
  --raya: #457b9d;
}

table {
    font-size:12px !important;
}

.td_estados{
    background-color: #457b9d;
    color:white;
    font-weight: bold;
}


.td_titulo{
    background-color: #8ecae6;
    color:black;
    font-weight: bold;

    border-bottom-style:solid;
    border-bottom-width: 1px;
    border-bottom-color: var(--raya);    
}

.td_totales{
    background-color: #8ecae6;
    color:black;
    font-weight: bold;
}

.tr_data {
    border-bottom-style:solid;
    border-bottom-width: 1px;
    border-bottom-color: var(--raya);
}

.raya_r {
    border-right-style:solid;
    border-right-width: 1px;
    border-right-color: var(--raya);
}
</style>








<div id="mainContainer">
<div class="container">

<div style="height:15px; clear:both"></div>

<form method="post" target="_self">
<div class="row">
<div style="text-align:center; width:400px; margin-left:auto; margin-right:auto">

<select class="form-select" name="area" id="area" style="width:150px; float:left">
        <option value="">Areas (todas)</option>
        <?php do { ?>
        <option <?php if($row_areas["area"]==$_GET["area"]){ echo "selected";};?> value="<?php echo $row_areas["area"];?>"><?php echo $row_areas["area"];?></option>
        <?php } while ( $row_areas = $areas->fetch_assoc() ); ?>
</select>  


<select class="form-select" name="tipo" id="tipo" style="width:150px;float:left">
        <option value="">Tipos (todos)</option>
        <?php do { ?>
        <option <?php if($row_tipos["tipo"]==$_GET["tipo"]){ echo "selected";};?> value="<?php echo $row_tipos["tipo"];?>"><?php echo $row_tipos["tipo"];?></option>
        <?php } while ( $row_tipos = $tipos->fetch_assoc() ); ?>
</select>        

<script>
function lanza(){    
var sarea = document.getElementById("area");
var area_value = sarea.value;

var stipo = document.getElementById("tipo");
var tipo_value = stipo.value;

jconfirm.instances[0].close();
avance(area_value, tipo_value);

}
</script>    


<button onclick="lanza();" type="button" class="btn btn-primary">Aplicar</button>



<div style="height:15px; clear:both"></div>

</form>

</div>    
</div>    



  <div class="row">

    <div class="col">



<?php
$sql = "select * from tabla ";
if(!$tabla = $db->query($sql)){
    die('Hay un error [' . $db->error . ']');
}   
$row_tabla = $tabla->fetch_assoc();
?>    


<table width="100%" border="0" cellspacing="0" cellpadding="5">
  <tbody>
    <tr>


      <td>
     
      </td>

      <td>

      </td>





      <td class="td_estados" colspan="16" align="center">ESTADOS</td>
      <td colspan="2" align="center"></td>
    </tr>
    <tr>
      <td class="td_titulo">ÁREA</td>
      <td class="td_titulo raya_r">TIPO</td>
      <td class="td_titulo raya_r" colspan="2" align="center">EN ESPERA</td>
      <td class="td_titulo raya_r" colspan="2" align="center">FORMULADA</td>
      <td class="td_titulo raya_r" colspan="2" align="center">POSTULADA A FINANCIAMIENTO</td>
      <td class="td_titulo raya_r" colspan="2" align="center">EN LICITACIÓN</td>
      <td class="td_titulo raya_r" colspan="2" align="center">ETAPA EN EJECUCIÓN</td>
      <td class="td_titulo raya_r" colspan="2" align="center">DETENIDA POR FUERZA MAYOR</td>
      <td class="td_titulo raya_r" colspan="2" align="center">SUSTITUIDA</td>
      <td class="td_titulo" colspan="2" align="center">ELIMINADA</td>
      <td class="td_titulo" colspan="2" align="center">TOTAL</td>
    </tr>

<?php 
$num=0;

$tt1=0;
$tt2=0;
$tt3=0;
$tt4=0;
$tt5=0;
$tt6=0;
$tt7=0;
$tt8=0;
?>

<?php do { ?>  
 
<?php
$num++;    
if (($num % 2) == 0) {
    $bg="#f1faee";
} else {
    $bg="#ffffff";
}    
?>

    <tr style="background-color: <?php echo $bg;?>">
      <td class="tr_data"><?php echo $row_tabla["area"];?></td>
      <td class="tr_data raya_r"><?php echo $row_tabla["tipo"];?></td>
      <?php if ($row_tabla["e1v"] > 0){ ?>
      <td class="tr_data " align="center"><?php echo number_format($row_tabla["e1v"], 0);?></td>
      <?php } else { ?>
      <td class="tr_data " align="center"></td>
      <?php } ?>

      <?php if ($row_tabla["e1p"] > 0){ ?>
      <td class="tr_data raya_r" align="center"><?php echo number_format($row_tabla["e1p"], 2)."%";?></td>
      <?php } else { ?>
      <td class="tr_data raya_r" align="center"></td>
      <?php } ?>      

      <?php if ($row_tabla["e2v"] > 0){ ?>
      <td class="tr_data " align="center"><?php echo number_format($row_tabla["e2v"], 0);?></td>
      <?php } else { ?>
      <td class="tr_data " align="center"></td>
      <?php } ?>   

      <?php if ($row_tabla["e2p"] > 0){ ?>
      <td class="tr_data raya_r" align="center"><?php echo number_format($row_tabla["e2p"], 2)."%";?></td>
      <?php } else { ?>
      <td class="tr_data raya_r" align="center"></td>
      <?php } ?>   

      <?php if ($row_tabla["e3v"] > 0){ ?>
      <td class="tr_data " align="center"><?php echo number_format($row_tabla["e3v"], 0);?></td>
      <?php } else { ?>
      <td class="tr_data " align="center"></td>
      <?php } ?>   

      <?php if ($row_tabla["e3p"] > 0){ ?>
      <td class="tr_data raya_r" align="center"><?php echo number_format($row_tabla["e3p"], 2)."%";?></td>
      <?php } else { ?>
      <td class="tr_data raya_r" align="center"></td>
      <?php } ?> 

      <?php if ($row_tabla["e4v"] > 0){ ?>      
      <td class="tr_data " align="center"><?php echo number_format($row_tabla["e4v"], 0);?></td>
      <?php } else { ?>
      <td class="tr_data " align="center"></td>
      <?php } ?> 

      <?php if ($row_tabla["e4p"] > 0){ ?>   
      <td class="tr_data raya_r" align="center"><?php echo number_format($row_tabla["e4p"], 2)."%";?></td>
      <?php } else { ?>
      <td class="tr_data raya_r" align="center"></td>
      <?php } ?> 

      <?php if ($row_tabla["e5v"] > 0){ ?>
      <td class="tr_data " align="center"><?php echo number_format($row_tabla["e5v"], 0);?></td>
      <?php } else { ?>
      <td class="tr_data " align="center"></td>
      <?php } ?> 

      <?php if ($row_tabla["e5p"] > 0){ ?>
      <td class="tr_data raya_r" align="center"><?php echo number_format($row_tabla["e5p"], 2)."%";?></td>
      <?php } else { ?>
      <td class="tr_data raya_r" align="center"></td>
      <?php } ?> 

      <?php if ($row_tabla["e6v"] > 0){ ?>
      <td class="tr_data " align="center"><?php echo number_format($row_tabla["e6v"], 0);?></td>
      <?php } else { ?>
      <td class="tr_data " align="center"></td>
      <?php } ?> 

      <?php if ($row_tabla["e6p"] > 0){ ?>
      <td class="tr_data raya_r" align="center"><?php echo number_format($row_tabla["e6p"], 2)."%";?></td>
      <?php } else { ?>
      <td class="tr_data raya_r" align="center"></td>
      <?php } ?> 



      <?php if ($row_tabla["e7v"] > 0){ ?>
      <td class="tr_data " align="center"><?php echo number_format($row_tabla["e7v"], 0);?></td>
      <?php } else { ?>
      <td class="tr_data " align="center"></td>
      <?php } ?> 

      <?php if ($row_tabla["e7p"] > 0){ ?>
      <td class="tr_data raya_r" align="center"><?php echo number_format($row_tabla["e7p"], 2)."%";?></td>
      <?php } else { ?>
      <td class="tr_data raya_r" align="center"></td>
      <?php } ?>       


      <?php if ($row_tabla["e8v"] > 0){ ?>
      <td class="tr_data " align="center"><?php echo number_format($row_tabla["e8v"], 0);?></td>
      <?php } else { ?>
      <td class="tr_data " align="center"></td>
      <?php } ?> 

      <?php if ($row_tabla["e8p"] > 0){ ?>
      <td class="tr_data raya_r" align="center"><?php echo number_format($row_tabla["e8p"], 2)."%";?></td>
      <?php } else { ?>
      <td class="tr_data raya_r" align="center"></td>
      <?php } ?>       




      <td class="tr_data" align="center"><?php echo number_format($row_tabla["tt"], 0);?></td>
      <td class="tr_data" align="center"><?php echo number_format($row_tabla["tp"], 2)."%";?></td>
    </tr>

<?php
// totales

$tt1 = $tt1 + $row_tabla["e1v"];
$tt2 = $tt2 + $row_tabla["e2v"];
$tt3 = $tt3 + $row_tabla["e3v"];
$tt4 = $tt4 + $row_tabla["e4v"];
$tt5 = $tt5 + $row_tabla["e5v"];
$tt6 = $tt6 + $row_tabla["e6v"];
$tt7 = $tt7 + $row_tabla["e7v"];
$tt8 = $tt8 + $row_tabla["e8v"];

$ttt = $tt1 + $tt2 + $tt3 + $tt4 + $tt5 + $tt6 + $tt7 + $tt8;

$ttp1 = ( $tt1 / $ttt ) * 100;
$ttp2 = ( $tt2 / $ttt ) * 100;
$ttp3 = ( $tt3 / $ttt ) * 100;
$ttp4 = ( $tt4 / $ttt ) * 100;
$ttp5 = ( $tt5 / $ttt ) * 100;
$ttp6 = ( $tt6 / $ttt ) * 100;
$ttp7 = ( $tt7 / $ttt ) * 100;
$ttp8 = ( $tt8 / $ttt ) * 100;

$tttp = $ttp1 + $ttp2 + $ttp3 + $ttp4 + $ttp5 + $ttp6 + $ttp7 + $ttp8;

?>



<?php } while( $row_tabla = $tabla->fetch_assoc() ); ?>    

    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td class="td_totales" align="center"><?php echo number_format($tt1, 0);?></td>
      <td class="td_totales" align="center"><?php echo number_format($ttp1, 2)."%";?></td>      
      <td class="td_totales" align="center"><?php echo number_format($tt2, 0);?></td>
      <td class="td_totales" align="center"><?php echo number_format($ttp2, 2)."%";?></td>            
      <td class="td_totales" align="center"><?php echo number_format($tt3, 0);?></td>
      <td class="td_totales" align="center"><?php echo number_format($ttp3, 2)."%";?></td>            
      <td class="td_totales" align="center"><?php echo number_format($tt4, 0);?></td>
      <td class="td_totales" align="center"><?php echo number_format($ttp4, 2)."%";?></td>            
      <td class="td_totales" align="center"><?php echo number_format($tt5, 0);?></td>
      <td class="td_totales" align="center"><?php echo number_format($ttp5, 2)."%";?></td>            
      <td class="td_totales" align="center"><?php echo number_format($tt6, 0);?></td>
      <td class="td_totales" align="center"><?php echo number_format($ttp6, 2)."%";?></td>            
      <td class="td_totales" align="center"><?php echo number_format($tt7, 0);?></td>
      <td class="td_totales" align="center"><?php echo number_format($ttp7, 2)."%";?></td>                  
      <td class="td_totales" align="center"><?php echo number_format($tt8, 0);?></td>
      <td class="td_totales" align="center"><?php echo number_format($ttp8, 2)."%";?></td>                  

      <td class="td_totales" align="center"><?php echo number_format($ttt, 0);?></td>
      <td class="td_totales" align="center"><?php echo number_format($tttp, 2)."%";?></td>      
    </tr>
  </tbody>
</table>    





    </div>

  </div>

</div>
</div>    


<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
