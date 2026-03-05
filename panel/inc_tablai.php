<?php

// Limpiar tabla temporal
$sql="DELETE FROM tablai WHERE true";
if(!$r = $db->query($sql)){
    die('Hay un error [' . $db->error . ']');
}

// Obtener todas las etapas disponibles
$sql="SELECT etapas_id, etapas_descripcion FROM etapas ORDER BY etapas_id";
if(!$etapas_all = $db->query($sql)){
    die('Hay un error [' . $db->error . ']');
}
$etapas_list = [];
while($row_etapa = $etapas_all->fetch_assoc()) {
    $etapas_list[$row_etapa['etapas_id']] = $row_etapa['etapas_descripcion'];
}

// Consulta principal - CALCULA avance financiero con monto invertido
// NOTA: Esta consulta calcula por cada grupo de proyectos:
// - presupuesto_total: suma de p_diseno + p_ejecucion de todos los proyectos
// - monto_invertido: suma de montos de actividades Completas de todos los proyectos
$sql="SELECT 
s.sector_descripcion as sector,
t.tipo as tipo,  
e.etapas_id as etapa_id,
e.etapas_descripcion as etapa,
COUNT(p.id) as cuantos,
SUM(COALESCE(p.p_diseno, 0) + COALESCE(p.p_ejecucion, 0)) as presupuesto_total,
(
    SELECT COALESCE(SUM(a.monto), 0) 
    FROM actividades a 
    INNER JOIN proyectos p2 ON a.proyecto = p2.id
    INNER JOIN sectores s2 ON p2.sector = s2.sector_id
    INNER JOIN tipo t2 ON p2.tipo = t2.id
    WHERE a.estado = 'Completa'
    AND s2.sector_descripcion = s.sector_descripcion
    AND t2.tipo = t.tipo
    AND p2.etapa = e.etapas_id
    AND p2.proyectos_status = 1
) as monto_invertido
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
    
    // Obtener valores
    $presupuesto = (float)$row_datos["presupuesto_total"];
    $invertido = (float)$row_datos["monto_invertido"];
    $porcentaje_avance = ($presupuesto > 0) ? (($invertido / $presupuesto) * 100) : 0;
    
    // La tabla 'tablai' usa 'area' como nombre de columna
    $sql = "SELECT * FROM tablai 
            WHERE area='".$sector_escaped."' 
            AND tipo='".$tipo_escaped."' ";
    
    if(!$r = $db->query($sql)){
        die('Hay un error [' . $db->error . ']');
    }   
    $row_r = $r->fetch_assoc();

    // Si no hay datos, insertar registro base
    if (!$row_r || !isset($row_r["area"]) || $row_r["area"]==""){
        $sql ="INSERT INTO tablai (area, tipo) VALUES (";
        $sql.="'".$sector_escaped."', ";
        $sql.="'".$tipo_escaped."') ";
        if(!$result = $db->query($sql)){
            die('Hay un error [' . $db->error . ']');
        }   
    }

    // Mapear etapa_id a columnas (e1 a e8)
    $etapa_num = (int)$row_datos["etapa_id"];
    if($etapa_num >= 1 && $etapa_num <= 8) {
        $campo_label = "e".$etapa_num."l";   // Label (nombre de etapa)
        $campo_cant = "e".$etapa_num."v";    // Cantidad de proyectos
        $campo_inv = "e".$etapa_num."s";     // Monto invertido (miles $)
        $campo_pres = "e".$etapa_num."pr";   // Presupuesto total (miles $)
        $campo_porc = "e".$etapa_num."p";    // Porcentaje de avance
        $valor = $row_datos["etapa"];
        
        // Actualizar con todos los valores
        $sql ="UPDATE tablai SET 
               ".$campo_label." ='".$db->real_escape_string($valor)."', 
               ".$campo_cant." ='".(int)$row_datos["cuantos"]."', 
               ".$campo_inv." ='".$invertido."',
               ".$campo_pres." ='".$presupuesto."',
               ".$campo_porc." ='".$porcentaje_avance."' 
               WHERE area ='".$sector_escaped."' AND tipo ='".$tipo_escaped."' ";
        
        if(!$result = $db->query($sql)){
            die('Hay un error [' . $db->error . ']');
        }
    }

} while( $row_datos = $datos->fetch_assoc() );
}

// Actualizar totales
$sql = "SELECT * FROM tablai ";
if(!$tabla = $db->query($sql)){
    die('Hay un error [' . $db->error . ']');
}   
$row_tabla = $tabla->fetch_assoc();

if($row_tabla) {
do {
    // Total de proyectos
    $total_proy = 0;
    $total_proy += (int)$row_tabla["e1v"];
    $total_proy += (int)$row_tabla["e2v"];
    $total_proy += (int)$row_tabla["e3v"];
    $total_proy += (int)$row_tabla["e4v"];
    $total_proy += (int)$row_tabla["e5v"];
    $total_proy += (int)$row_tabla["e6v"];
    $total_proy += (int)$row_tabla["e7v"];
    $total_proy += (int)$row_tabla["e8v"];

    // Total invertido
    $total_inv = 0;
    $total_inv += (float)$row_tabla["e1s"];
    $total_inv += (float)$row_tabla["e2s"];
    $total_inv += (float)$row_tabla["e3s"];
    $total_inv += (float)$row_tabla["e4s"];
    $total_inv += (float)$row_tabla["e5s"];
    $total_inv += (float)$row_tabla["e6s"];
    $total_inv += (float)$row_tabla["e7s"];
    $total_inv += (float)$row_tabla["e8s"];
    
    // Calcular promedio ponderado de porcentaje
    $suma_ponderada = 0;
    $suma_ponderada += (int)$row_tabla["e1v"] * (float)$row_tabla["e1p"];
    $suma_ponderada += (int)$row_tabla["e2v"] * (float)$row_tabla["e2p"];
    $suma_ponderada += (int)$row_tabla["e3v"] * (float)$row_tabla["e3p"];
    $suma_ponderada += (int)$row_tabla["e4v"] * (float)$row_tabla["e4p"];
    $suma_ponderada += (int)$row_tabla["e5v"] * (float)$row_tabla["e5p"];
    $suma_ponderada += (int)$row_tabla["e6v"] * (float)$row_tabla["e6p"];
    $suma_ponderada += (int)$row_tabla["e7v"] * (float)$row_tabla["e7p"];
    $suma_ponderada += (int)$row_tabla["e8v"] * (float)$row_tabla["e8p"];
    
    $promedio_porc = ($total_proy > 0) ? ($suma_ponderada / $total_proy) : 0;

    // Actualizar totales
    $sql ="UPDATE tablai SET ";
    $sql.="tt=".$total_proy.", ";
    $sql.="tp=".$promedio_porc." ";
    $sql.="WHERE area ='".$db->real_escape_string($row_tabla["area"])."' 
           AND tipo ='".$db->real_escape_string($row_tabla["tipo"])."'";

    if(!$result = $db->query($sql)){
        die('Hay un error [' . $db->error . ']');
    }

} while( $row_tabla = $tabla->fetch_assoc() );
}

?>