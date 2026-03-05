<?php 
// ************************************************************
// Autor: Marcelo Jiménez
// Fulltrust Software
// https://www.fulltrust.net
// Fecha última actualización: 25 de Agosto de 2023
// ************************************************************
?>
<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

	@session_start();
	
	
	include("../db.php");		
	
	
	$sql="set names utf8";
	if(!$result = $db->query($sql)){
    	die('Hay un error [' . $db->error . ']');
	}	
	
	$sql="select * from usuarios where usuarios_userid='".$_SESSION["usuario"]."'"; 
	if(!$restabla = $db->query($sql)){
    	die('Hay un error [' . $db->error . ']');
	}
	$row_tabla = $restabla->fetch_assoc();  	
	$count = $restabla->num_rows;
	
	if($count < 1){
        session_destroy();
		?>
        <script>
		window.location='index.php';
		</script>		
        <?php
	}
	
	if($row_tabla["usuarios_profile"] <> 'ADMINISTRADOR'){
		?>
        <script>
		window.location='menu.php';
		</script>		
        <?php
	}
	
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1">	
<title>Usuarios</title>

<link rel="shortcut icon" href="../favicon.ico" type="image/x-icon">
<link rel="icon" href="../favicon.ico" type="image/x-icon">	
	
<link rel="stylesheet" href="../css//mini-default.css">
	
<script src="js/jquery-3.5.1.min.js"></script>	
	
<link rel="stylesheet" type="text/css" href="js/DataTables/datatables.css">
<link rel="stylesheet" type="text/css" href="js/DataTables/buttons.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="js/DataTables/dataTables.searchHighlight.css">

<script type="text/javascript" charset="utf8" src="js/DataTables/datatables.js"></script>
<script type="text/javascript" charset="utf8" src="js/DataTables/dataTables.buttons.min.js"></script>
<script type="text/javascript" charset="utf8" src="js/DataTables/buttons.flash.min.js"></script>
<script type="text/javascript" charset="utf8" src="js/DataTables/jszip.min.js"></script>
<script type="text/javascript" charset="utf8" src="js/DataTables/pdfmake.min.js"></script>
<script type="text/javascript" charset="utf8" src="js/DataTables/vfs_fonts.js"></script>
<script type="text/javascript" charset="utf8" src="js/DataTables/buttons.print.min.js"></script>
<script type="text/javascript" charset="utf8" src="js/DataTables/jquery.highlight.js"></script>
<script type="text/javascript" charset="utf8" src="js/DataTables/dataTables.searchHighlight.js"></script>
<script type="text/javascript" charset="utf8" src="js/DataTables/buttons.html5.min.js"></script>

<link rel="stylesheet" type="text/css" href="https://code.jquery.com/ui/1.12.1/themes/Redmond/jquery-ui.css"/>
	

	
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js">	</script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.25/js/dataTables.jqueryui.min.js">	</script>
	
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/dataTables.jqueryui.min.css"/>	
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/rowreorder/1.2.7/css/rowReorder.dataTables.min.css">	
	
	

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>	
	

<link rel="stylesheet" type="text/css" href="../css/styles.css"/>
	
	
<script>
	
function abre(id){
		document.form1["wid"].value=id;
		document.form1.submit();
}	


  $(document).ready(function() {
   		$('#tabla').dataTable({
 

  		initComplete: function(settings, json) {
    		//filtrar();
  		},

				search: {
			 		regex: false,
			 		smart: false
			  },

		 retrieve: true,
         searchHighlight: true,

        buttons: [
            'excel', 'pdf'
        ],

        "bPaginate": true,
        "sPaginationType": "full_numbers",
        "iDisplayLength": 100,
		"aaSorting": [[ 0, 'desc' ]],
		"dom": '<"top"ifp<"clear">>rt<"bottom"ip<"clear">>',
    buttons: [
            'excel', 'pdf'
        ],

		responsive: true,
		processing: true,

    "columnDefs": [
      { "targets": 0, visible:true },
      { "targets": 1, visible:true },
      { "targets": 2, visible:true },
      { "targets": 3, visible:true },
      { "targets": 4, visible:true },
      { "targets": 5, visible:true, "width": "300px" },
    ],


        "sAjaxSource": "usuarios_data.php",
        "aoColumns": [
              { mData: mix } ,
              { mData: 'usuarios_userid' },
              { mData: 'usuarios_nombre' },
              { mData: 'usuarios_email'} ,
              { mData: 'usuarios_profile' } ,
              { mData: 'last_login' } ,
            ],


		"language":
{
    "sProcessing":     "<p align='center'><img src='img/ajax-loader.svg' /></p>",
    "sLengthMenu":     "Mostrar _MENU_ registros",
    "sZeroRecords":    "No se encontraron resultados",
    "sEmptyTable":     "Ningún dato disponible en esta tabla",
    "sInfo":           "_START_ al _END_ de _TOTAL_ ",
    "sInfoEmpty":      "0 al 0 ",
    "sInfoFiltered":   "_MAX_ registros)",
    "sInfoPostFix":    "",
    "sSearch":         "",
	searchPlaceholder: "Buscar",
    "sUrl":            "",
    "sInfoThousands":  ",",
    "sLoadingRecords": "",
    "oPaginate": {
        "sFirst":    "<<",
        "sLast":     ">>",
        "sNext":     ">",
        "sPrevious": "<"
    },
    "oAria": {
        "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
        "sSortDescending": ": Activar para ordenar la columna de manera descendente"
    }
}


});



	  

function mix(data, type, dataToSet) {

	var link = '<a href="usuarios_editar.php?id='+data.usuarios_id+'"><img src="../img/document.png" width="32"></a>';
  return link;
}	  



});




	
function ayuda(){
            $.confirm({
                title: '',
                type: 'orange',
				boxWidth: '90%',
                typeAnimated: true,
                closeIcon: true,
                buttons: {
                   info: {
                    text: 'Cerrar', 
                    //btnClass: 'btn-red',
                    action: function(){}
                    }
               },

                content: 'url:ayuda_proyetos.html'
            });   	
}	


</script>	  		

</head>

<body>

<div id="barra_superior">
  <div id="logo_container"> <img src="../img/logo-mapa.png" /></div>
</div>
<div id="mainContainer">
  <div class="menu_arriba">
    <div class="div300r">
		<img style="vertical-align:middle" src="img/user.png">&nbsp;<?php echo $_SESSION["nombre"];?>&nbsp;&nbsp;&nbsp;<a href="usuarios_crear.php"><img src="img/if_plus-sign_173078.png" width="24" height="24" align="absmiddle" />&nbsp;&nbsp;Agregar un nuevo usuario</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="menu.php"><img src="../img/home.png" width="22" height="22" align="absmiddle" />&nbsp;Inicio</a></div>
  <div class="div300l">Lista de usuarios</div>
</div>
</div>

<div style="height:10px; clear:both"></div>
<div class="tip">
Es lista muestra los usuarios del sistema. Existen dos perfiles de usuario: el administrador que tiene control total y el invitado, que solo puede consultar información.  Desde esta ventana es posible buscar en la lista, ordenar los datos haciendo clic en los títulos y crear nuevos registros haciendo clic en el enlace ubicado en la parte superior derecha de la pantalla. Para editar un usuario, haga clic en la primera columna de cada fila en la lista.
</div>	  

<table id="tabla" class="display hoverable" style="width: 100% !important">
	<tbody>
	<thead>
    	<tr>
          <th class="mheaderx" style="width: 40px !important;">VER</th>
          <th class="mheaderx">USER ID</th>
          <th class="mheaderx">NOMBRE</th>
          <th class="mheaderx">E-MAIL</th>
          <th class="mheaderx">PERFIL</th>
          <th class="mheaderx">ÚLTIMO ACCESO</th>
     	</tr>
    </thead>

	</tbody>
</table>	
	
	
</div>

<form action="proyectos_consultar.php" target="_self" method="post" name="form1" id="form1">
  <input type="hidden" name="wid" id="wid">
</form>


<?php include("footer.php");?></body>
</html>