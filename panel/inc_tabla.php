<?php

// Limpiar tabla temporal
$sql="DELETE FROM tabla WHERE true";
if(!$r = $db->query($sql)){
    die('Hay un error [' . $db->error . ']');
}

// Obtener todas las etapas disponibles desde la tabla etapas
$sql="SELECT etapas_id, etapas_descripcion FROM etapas ORDER BY etapas_id";
if(!$etapas_all = $db->query($sql)){
    die('Hay un error [' . $db->error . ']');
}
$etapas_list = [];
while($row_etapa = $etapas_all->fetch_assoc()) {
    $etapas_list[$row_etapa['etapas_id']] = $row_etapa['etapas_descripcion'];
}

// Consulta principal con JOINs para obtener datos agrupados
$sql="SELECT 
s.sector_descripcion as sector,
t.tipo as tipo,  
e.etapas_id as etapa_id,
e.etapas_descripcion as etapa,
COUNT(p.id) as cuantos
FROM proyectos p
LEFT JOIN sectores s ON p.sector = s.sector_id
LEFT JOIN tipo t ON p.tipo = t.id
LEFT JOIN etapas e ON p.etapa = e.etapas_id
WHERE s.sector_descripcion IS NOT NULL 
AND s.sector_descripcion <> '' 
AND p.proyectos_status = 1 ";

if(isset($_GET["sector"]) && $_GET["sector"]<>""){
    $sql.=" AND s.sector_descripcion = '".$db->real_escape_string($_GET["sector"])."' ";
}

if(isset($_GET["tipo"]) && $_GET["tipo"]<>""){
    $sql.=" AND t.tipo = '".$db->real_escape_string($_GET["tipo"])."' ";
}

$sql.=" GROUP BY
s.sector_descripcion,
t.tipo,
e.etapas_id,
e.etapas_descripcion
ORDER BY
s.sector_descripcion,
t.tipo,
e.etapas_id";

if(!$datos = $db->query($sql)){
    die('Hay un error [' . $db->error . ']');
}

// Obtener lista de sectores únicos
$sql="SELECT DISTINCT s.sector_descripcion as sector 
FROM proyectos p
INNER JOIN sectores s ON p.sector = s.sector_id
WHERE s.sector_descripcion IS NOT NULL 
AND s.sector_descripcion <> ''
AND p.proyectos_status = 1
ORDER BY s.sector_descripcion";

if(!$sectores = $db->query($sql)){
    die('Hay un error [' . $db->error . ']');
}
$row_sectores = $sectores->fetch_assoc();

// Obtener lista de tipos únicos
$sql="SELECT DISTINCT t.tipo 
FROM proyectos p
INNER JOIN tipo t ON p.tipo = t.id
WHERE t.tipo IS NOT NULL 
AND t.tipo <> ''
AND p.proyectos_status = 1
ORDER BY t.tipo";

if(!$tipos = $db->query($sql)){
    die('Hay un error [' . $db->error . ']');
}
$row_tipos = $tipos->fetch_assoc();

// Procesar datos y llenar tabla temporal
$row_datos = $datos->fetch_assoc();
if($row_datos) {
do {
    $sector_escaped = $db->real_escape_string($row_datos["sector"]);
    $tipo_escaped = $db->real_escape_string($row_datos["tipo"]);
    
    // IMPORTANTE: La tabla 'tabla' usa 'area' como nombre de columna, pero guarda el sector
    $sql = "SELECT * FROM tabla 
            WHERE area='".$sector_escaped."' 
            AND tipo='".$tipo_escaped."' ";
    
    if(!$r = $db->query($sql)){
        die('Hay un error [' . $db->error . ']');
    }   
    $row_r = $r->fetch_assoc();

    // Si no hay datos, insertar registro base
    if (!$row_r || !isset($row_r["area"]) || $row_r["area"]==""){
        $sql ="INSERT INTO tabla (area, tipo) VALUES (";
        $sql.="'".$sector_escaped."', ";
        $sql.="'".$tipo_escaped."') ";
        if(!$result = $db->query($sql)){
            die('Hay un error [' . $db->error . ']');
        }   
    }

    // Mapear etapa_id a columnas (e1 a e8)
    $etapa_num = (int)$row_datos["etapa_id"];
    if($etapa_num >= 1 && $etapa_num <= 8) {
        $campo = "e".$etapa_num."l";
        $campo1 = "e".$etapa_num."v";
        $valor = $row_datos["etapa"];
        
        // Actualizar con el valor correspondiente
        $sql ="UPDATE tabla SET ".$campo." ='".$db->real_escape_string($valor)."', ".$campo1."='".(int)$row_datos["cuantos"]."' WHERE ";
        $sql.="area ='".$sector_escaped."' AND ";
        $sql.="tipo ='".$tipo_escaped."' ";
        
        if(!$result = $db->query($sql)){
            die('Hay un error [' . $db->error . ']');
        }
    }

} while( $row_datos = $datos->fetch_assoc() );
}

// Actualizar totales y porcentajes
$sql = "SELECT * FROM tabla ";
if(!$tabla = $db->query($sql)){
    die('Hay un error [' . $db->error . ']');
}   
$row_tabla = $tabla->fetch_assoc();

if($row_tabla) {
do {
    $t=0;
    $t=$t+(int)$row_tabla["e1v"];
    $t=$t+(int)$row_tabla["e2v"];
    $t=$t+(int)$row_tabla["e3v"];
    $t=$t+(int)$row_tabla["e4v"];
    $t=$t+(int)$row_tabla["e5v"];
    $t=$t+(int)$row_tabla["e6v"];
    $t=$t+(int)$row_tabla["e7v"];
    $t=$t+(int)$row_tabla["e8v"];

    // Calcular porcentajes solo si hay total
    if($t > 0) {
        $p1=(($row_tabla["e1v"] / $t) * 100);
        $p2=(($row_tabla["e2v"] / $t) * 100);
        $p3=(($row_tabla["e3v"] / $t) * 100);
        $p4=(($row_tabla["e4v"] / $t) * 100);
        $p5=(($row_tabla["e5v"] / $t) * 100);
        $p6=(($row_tabla["e6v"] / $t) * 100);
        $p7=(($row_tabla["e7v"] / $t) * 100);
        $p8=(($row_tabla["e8v"] / $t) * 100);

        $pt=$p1+$p2+$p3+$p4+$p5+$p6+$p7+$p8;

        // IMPORTANTE: Usar 'area' como nombre de columna en WHERE
        $sql ="UPDATE tabla SET ";
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
        $sql.="WHERE area ='".$db->real_escape_string($row_tabla["area"])."' 
               AND tipo ='".$db->real_escape_string($row_tabla["tipo"])."'";

        if(!$result = $db->query($sql)){
            die('Hay un error [' . $db->error . ']');
        }   
    }

} while( $row_tabla = $tabla->fetch_assoc() );
}

?>