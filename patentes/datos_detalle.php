<?php 
// ************************************************************
// Autor: Marcelo Jiménez
// Fulltrust Software
// https://www.fulltrust.net
// Fecha última actualización: 2025-01-03 15:10:15
// ************************************************************
?>
<?php
include_once("conf/db.php");


//error_reporting(-1);
//ini_set('display_errors', '1');

@session_start();
$session = session_id();


if ( $session != $_GET["token"] ){
        exit();
}



$sql = "set names utf8";
$result = $db->query($sql);

$sql = "SELECT proyectos.*,
`areas`.`area`
FROM
`proyectos`
LEFT OUTER JOIN `areas` ON (`proyectos`.`cod_area` = `areas`.`codigo`)
WHERE proyectos_status=1 ";

if ( $_GET["tipo"] !="" ) {
	$sql .= " and cod_tipo in (".$_GET["tipo"].")";
}

if ( $_GET["area"] !="" ) {
	$sql .= " and cod_area in (".$_GET["area"].")";
}

if ( $_GET["origen"] !="" ) {
	$sql .= " and origen in (".$_GET["origen"].")";
}

if ( $_GET["estado"] !="" ) {
	$sql .= " and estado in (".$_GET["estado"].")";
}


file_put_contents('debug.txt', $sql);


$result = $db->query($sql);
$row_cnt = $result->num_rows;

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