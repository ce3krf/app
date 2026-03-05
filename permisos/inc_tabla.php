<?php

$sql="delete from tabla where true";

if(!$r = $db->query($sql)){
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
        case "Finalizada etapa perfil / prefactibilidad":
            $campo="e6l";
            $campo1="e6v";
            $valor="Finalizada etapa perfil / prefactibilidad";
            break;
        case "Finalizada etapa diseño":
            $campo="e7l";
            $campo1="e7v";
            $valor="Finalizada etapa diseño";
            break;     
        case "Finalizada":
            $campo="e8l";
            $campo1="e8v";
            $valor="Finalizada";
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
    $p7=(($row_tabla["e7v"] / $t) * 100);
    $p8=(($row_tabla["e8v"] / $t) * 100);

    $pt=$p1+$p2+$p3+$p4+$p5+$p6+$p7+$p8;

    $sql ="update tabla set ";
    $sql.="e1p=".$p1.", ";
    $sql.="e2p=".$p2.", ";
    $sql.="e3p=".$p3.", ";
    $sql.="e4p=".$p4.", ";
    $sql.="e5p=".$p5.", ";
    $sql.="e6p=".$p6.", ";
    $sql.="e7p=".$p7.", ";
    $sql.="e8p=".$p8.", ";
    $sql.="tt=".$t.", ";
    $sql.="tp=".$pt." ";
    $sql.="where area ='".$row_tabla["area"]."' and tipo ='".$row_tabla["tipo"]."'";

    //echo $sql."<br><br>";

    if(!$result = $db->query($sql)){
        die('Hay un error [' . $db->error . ']');
    }   

} while( $row_tabla = $tabla->fetch_assoc() );


   
//********************************* */