<?php 
// ************************************************************
// Autor: Marcelo Jiménez
// Fulltrust Software
// https://www.fulltrust.net
// Fecha última actualización: 2025-05-31 16:43:52
// ************************************************************
?>
<?php

@session_start();
$session = session_id();

//error_reporting(E_ALL);
//ini_set('display_errors', '1');

/*
if ( $session != $_GET["token"] ){
        exit();
}
*/


include_once("../conf/db.php");

$sql = "set names utf8";
$result = $db->query($sql);

/*
$sql = "set global sql_mode = (select replace(@@sql_mode,'ONLY_FULL_GROUP_BY', ''));";
$result = $db->query($sql);
*/

$sql = "
SELECT * from  patentes where estado=1 and latitud IS NOT NULL and longitud IS NOT NULL " ;

/*
$tipo = urldecode($_GET["tipo"]);
$area = urldecode($_GET["area"]); 
$origen = urldecode($_GET["origen"]); 
$estado = urldecode($_GET["estado"]); 

if ( $tipo !="" ) {
	$sql .= " and cod_tipo in (".$tipo.")";
}

if ( $area !="" ) {
	$sql .= " and cod_area in (".$area.")";
}

if ( $origen !="" ) {
	$sql .= " and origen in (".$origen.")";
}

if ( $estado !="" ) {
	$sql .= " and estado in (".$estado.")";
}

*/




$tipo = isset($_GET["tipo"]) ? urldecode($_GET["tipo"]) : "";
if ($tipo != "") {
	// Split comma-separated string, trim spaces, escape values, and build SQL IN clause
	$tipos = array_map('trim', explode(',', $tipo));
	$tipos_escaped = array_map(function($t) use ($db) {
		return "'" . mysqli_real_escape_string($db, $t) . "'";
	}, $tipos);
	$sql .= " and tipo_patente in (" . implode(',', $tipos_escaped) . ") ";
}
$tipo = isset($_GET["tipo"]) ? urldecode($_GET["tipo"]) : "";



$actividad2 = isset($_GET["actividad2"]) ? urldecode($_GET["actividad2"]) : "";
if ($actividad2 != "") {
	// Split comma-separated string, trim spaces, escape values, and build SQL IN clause
	$actividades = array_map('trim', explode(',', $actividad2));
	$actividades_escaped = array_map(function($a) use ($db) {
		return "'" . mysqli_real_escape_string($db, $a) . "'";
	}, $actividades);
	$sql .= " and actividad2 in (" . implode(',', $actividades_escaped) . ") ";
}





$search = isset($_GET['search']) ? urldecode($_GET['search']) : '';


if ($search !== '') {
	$search = mysqli_real_escape_string($db, $search);
	$sql .= " AND (";
$fields = [
    'ROL', 'RUT', 'NOMBRE', 'DIRECCION_COMERCIAL', 'ROL_PROP', 'GIRO', 'TIPO_PATENTE'
];
	$likeClauses = [];
	foreach ($fields as $field) {
		$likeClauses[] = "`patentes`.`$field` LIKE '%$search%'";
	}
	$sql .= implode(' OR ', $likeClauses);
	$sql .= ")";
}




file_put_contents('debug.txt', $sql);
//echo $sql."<br>";


$result = mysqli_query($db, $sql);
if (!$result) {
    echo "Error de consulta: " . mysqli_error($db);
}
$num_rows = $result->num_rows;

$data = array();
while($row = $result->fetch_array(MYSQLI_ASSOC)){
	$data[] = $row;
}

$d = ["sEcho" => 1,
        	"iTotalRecords" => count($data),
        	"iTotalDisplayRecords" => count($data),
        	"aaData" => $data ];


echo json_encode($d);
?>