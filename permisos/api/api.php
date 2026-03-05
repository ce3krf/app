<?php

require('../conf/db.php');

if ($_GET["apikey"] <> "7061746f7061746f7061746f6c6167617254696a6f"){
    echo "Acceso denegado";
    exit;
}

$q = "set names utf8";
if(!$preg = $db->query($q)){
	die('Ha ocurrido un error ejecutando la consulta [' . $db->error . ']');
}

$splitnumber = $_GET["cat"];
$splittedNumbers = explode(",", $splitnumber);
$cat = "'" . implode("', '", $splittedNumbers) ."'";



$sql = "SELECT 
`proyectos`.`id`,
`proyectos`.`cod_area`,
`proyectos`.`area`,
`proyectos`.`subarea`,
`proyectos`.`origen`,
`proyectos`.`lineamiento`,
`proyectos`.`objetivos`,
`proyectos`.`cod_tipo`,
`proyectos`.`tipo`,
`proyectos`.`nombre`,
`proyectos`.`area_gestion`,
`proyectos`.`plazo`,
`proyectos`.`presupuesto`,
`proyectos`.`financiamiento`,
`proyectos`.`unidad`,
`proyectos`.`e`,
`proyectos`.`entidades_relacionadas`,
`proyectos`.`lat`,
`proyectos`.`lng`,
`proyectos`.`estado`,
`proyectos`.`pin`,
`proyectos`.`proyectos_last_update`,
`proyectos`.`pdf`,
`proyectos`.`ano_inicio`,
`proyectos`.`ubicacion`,
`proyectos`.`status`,
`proyectos`.`descripcion`,
`proyectos`.`politicas`,
`proyectos`.`problema`,
`proyectos`.`area_influencia`,
`proyectos`.`deficit`,
`proyectos`.`solucion`,
`proyectos`.`beneficios`,
`proyectos`.`orientaciones`,
`proyectos`.`etapa`,
`proyectos`.`p_diseno`,
`proyectos`.`p_prefactibilidad`,
`proyectos`.`p_ejecucion`,
`proyectos`.`avance_etapas`,
`proyectos`.`avance_financiero`,
`proyectos`.`cod_iniciativa`,
`actividades`.`id`,
`actividades`.`proyecto`,
`actividades`.`actividad`,
`actividades`.`ano`,
`actividades`.`monto`,
`actividades`.`finaciamiento`,
`actividades`.`estado`,
`actividades`.`creado`,
`actividades`.`actualizado`,
`actividades`.`monto_final`,
`actividades`.`observaciones`,
`actividades`.`adjunto`,
`actividades`.`cod_iniciativa`,
`ubicaciones`.`ubicacion`,
`ubicaciones`.`lat`,
`ubicaciones`.`lng`,
`tipo`.`tipo`
FROM
`actividades`
RIGHT OUTER JOIN `proyectos` ON (`actividades`.`proyecto` = `proyectos`.`id`)
LEFT OUTER JOIN `ubicaciones` ON (`proyectos`.`ubicacion` = `ubicaciones`.`codigo`)
LEFT OUTER JOIN `tipo` ON (`proyectos`.`tipo` = `tipo`.`codigo`)
";

/////////////////////////////////////////////////////////////
//file_put_contents('debug.txt', $sql);

$result = $db->query($sql);
$row_cnt = $result->num_rows;

$data = array();
while($row = $result->fetch_array(MYSQLI_ASSOC)){
	$data[] = $row;
}


$results = ["sEcho" => 1,
        	"iTotalRecords" => count($data),
        	"iTotalDisplayRecords" => count($data),
        	"aaData" => $data ];


echo json_encode($results);

 
?>