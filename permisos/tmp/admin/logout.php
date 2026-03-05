<?php 
// ************************************************************
// Autor: Marcelo Jiménez
// Fulltrust Software
// https://www.fulltrust.net
// Fecha última actualización: 2024-09-06 17:03:53
// ************************************************************
?>
<?php
	session_destroy();
	session_start();
	$_SESSION["usuario"] = '';
?>

<script>
	window.location='index.php';
</script>		
<?php

?>