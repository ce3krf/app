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


include_once("conf/db.php");

$sql = "set names utf8";
$result = $db->query($sql);

/*
$sql = "set global sql_mode = (select replace(@@sql_mode,'ONLY_FULL_GROUP_BY', ''));";
$result = $db->query($sql);
*/

$sql = "
SELECT * from  peredifica where lat IS NOT NULL 
 and ( status1 = 'OK' or ok = 'OK' ) " ;

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
$search = isset($_GET['search']) ? urldecode($_GET['search']) : '';


if ($search !== '') {
	$search = mysqli_real_escape_string($db, $search);
	$sql .= " AND (";
$fields = [
    'nombre', 'direccion', 'localidad', 'rol', 'per_edif', 'rec_def', 'm2', 
    'n', 'm2_avaluo', 'destino1', 'destino2', 'status1', 'tipo', 'expediente', 'ano'
];
	$likeClauses = [];
	foreach ($fields as $field) {
		$likeClauses[] = "`peredifica`.`$field` LIKE '%$search%'";
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