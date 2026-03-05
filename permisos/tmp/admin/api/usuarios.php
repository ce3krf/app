<?php 
// ************************************************************
// Autor: Marcelo Jiménez
// Fulltrust Software
// https://www.fulltrust.net
// Fecha última actualización: 2024-09-06 10:19:44
// ************************************************************
?>
<?php

@session_start();

include("../../conf/db.php");		
include('functions.php');


if ( 
    (!isset($_GET["token"]) || $_GET["token"] != session_id()) && 
    (!isset($_POST["token"]) || $_POST["token"] != session_id()) 
) {
    exit;
}



$q = "set names utf8";
if(!$preg = $db->query($q)){
	die('Ha ocurrido un error ejecutando la consulta [' . $db->error . ']');
}




// **************************************************************************************
if ( $_POST["task"] == "update" ){

	// upload the file if there is any 
	if ($_FILES["usuarios_foto"]["name"] <> ''){

		if ( 
			pathinfo($_FILES["usuarios_foto"]["name"], PATHINFO_EXTENSION) == 'png' ||
			pathinfo($_FILES["usuarios_foto"]["name"], PATHINFO_EXTENSION) == 'jpg' ||
			pathinfo($_FILES["usuarios_foto"]["name"], PATHINFO_EXTENSION) == 'jfif' ||
			pathinfo($_FILES["usuarios_foto"]["name"], PATHINFO_EXTENSION) == 'webp'
		){

			$path = "../profiles/";
			$filename = $_POST["usuarios_id"] . "-" . date("YmdHis") . "-";
			$filename = $filename . preg_replace("/[^a-z0-9\_\-\.]/i", '-', basename($_FILES["usuarios_foto"]["name"]) );
			
			$target_file =  $path . $filename;

			if(file_exists( $target_file )){
				unlink( $target_file );
			}

			try {

				//file_put_contents('debug.txt', $_FILES['pdf']['tmp_name']."\n".$target_file );
				move_uploaded_file( $_FILES['usuarios_foto']['tmp_name'], $target_file );	
		
			} catch (Exception $e) {
				//file_put_contents('debug.txt', "\n >".$e->getMessage(), FILE_APPEND );
				exit;
			}		 


		}

	}
	// upload the file if there is any 


	$sql ="update usuarios set ";
	$sql.="usuarios_userid = '".mysqli_real_escape_string($db, $_POST['usuarios_userid'])."', ";	
	$sql.="usuarios_nombre = '".mysqli_real_escape_string($db, $_POST['usuarios_nombre'])."', ";		
	$sql.="usuarios_email = '".mysqli_real_escape_string($db, $_POST['usuarios_email'])."', ";			
	$sql.="usuarios_profile = '".mysqli_real_escape_string($db, $_POST['usuarios_profile'])."', ";			
	$sql.="usuarios_foto = '".mysqli_real_escape_string($db, "profiles/" . $filename)."', ";			

	if ($_POST["usuarios_password"]<> ''){
		$sql.="usuarios_password = '".mysqli_real_escape_string($db, md5( $_POST['usuarios_password'] ) )."', ";					
	}	

	$sql.="usuarios_updated = '".date("d-m-Y H:i:s")."' ";													
	$sql.="where usuarios_id='".$_POST['usuarios_id']."';";
	
	//file_put_contents('debug.txt', '\n'.$sql, FILE_APPEND);
	
	if(!$result = $db->query($sql)){
    	die('Hay un error [' . $db->error . ']');
	} else {
	}	


}

// **************************************************************************************








// **************************************************************************************
if ( $_POST["task"] == "delete" ){

	$id = $_POST["usuarios_id"];
	if ( is_numeric($id) ){

		$sql = "delete from usuarios where usuarios_id = '$id'";

		if(!$result = $db->query($sql)){
			die('Hay un error [' . $db->error . ']');
		}

	}
	
}
// **************************************************************************************









// **************************************************************************************
if ( $_POST["task"] == "insert" ){

	


	// crea el nuevo registro vacio
	$sql ="insert into usuarios (usuarios_nombre) values ('Nuevo registro') ";
	if(!$result = $db->query($sql)){
    	die('Hay un error [' . $db->error . ']');
	} else {
	}	

	// recurepa la última id creada
	$sql="select * from usuarios order by usuarios_id desc limit 1";
	if(!$result = $db->query($sql)){
		die('Hay un error [' . $db->error . ']');
	}
	$row = $result->fetch_assoc();




	// upload the file if there is any 
	if ($_FILES["usuarios_foto"]["name"] <> ''){

		if ( 
			pathinfo($_FILES["usuarios_foto"]["name"], PATHINFO_EXTENSION) == 'png' ||
			pathinfo($_FILES["usuarios_foto"]["name"], PATHINFO_EXTENSION) == 'jpg' ||
			pathinfo($_FILES["usuarios_foto"]["name"], PATHINFO_EXTENSION) == 'jfif' ||
			pathinfo($_FILES["usuarios_foto"]["name"], PATHINFO_EXTENSION) == 'webp'
		){

			$path = "../../profiles/";
			$filename = $row["usuarios_id"] . "-" . date("YmdHis") . "-";
			$filename = $filename . preg_replace("/[^a-z0-9\_\-\.]/i", '-', basename($_FILES["usuarios_foto"]["name"]) );
			
			$target_file =  $path . $filename;

			if(file_exists( $target_file )){
				unlink( $target_file );
			}

			try {

				//file_put_contents('debug.txt', $_FILES['pdf']['tmp_name']."\n".$target_file );
				move_uploaded_file( $_FILES['usuarios_foto']['tmp_name'], $target_file );	
		
			} catch (Exception $e) {
				//file_put_contents('debug.txt', "\n >".$e->getMessage(), FILE_APPEND );
				exit;
			}		 


		}

	}
	// upload the file if there is any 	


	
	$sql ="update usuarios set ";
	$sql.="usuarios_userid = '".mysqli_real_escape_string($db, $_POST['usuarios_userid'])."', ";	
	$sql.="usuarios_nombre = '".mysqli_real_escape_string($db, $_POST['usuarios_nombre'])."', ";		
	$sql.="usuarios_email = '".mysqli_real_escape_string($db, $_POST['usuarios_email'])."', ";			
	$sql.="usuarios_profile = '".mysqli_real_escape_string($db, $_POST['usuarios_profile'])."', ";			
	$sql.="usuarios_foto = '".mysqli_real_escape_string($db, $target_file )."', ";			

	if ($_POST["usuarios_password"]<> ''){
		$sql.="usuarios_password = '".mysqli_real_escape_string($db, md5( $_POST['usuarios_password'] ) )."', ";					
	}	

	$sql.="usuarios_updated = '".date("d-m-Y H:i:s")."' ";													
	$sql.="where usuarios_id='".$row['usuarios_id']."';";
	
	//file_put_contents('debug.txt', '\n'.$sql);
	
	if(!$result = $db->query($sql)){
    	die('Hay un error [' . $db->error . ']');
	} else {
		echo $row['usuarios_id'];
	}	


}

// **************************************************************************************





// **************************************************************************************
if ( $_GET["task"] == "list" ){

	$splitnumber = $_GET["cat"];
	$splittedNumbers = explode(",", $splitnumber);
	$cat = "'" . implode("', '", $splittedNumbers) ."'";



	$sql = "SELECT * from usuarios";
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
	
}
// **************************************************************************************

?>


