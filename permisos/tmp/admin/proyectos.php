<?php 
// ************************************************************
// Autor: Marcelo Jiménez
// Fulltrust Software
// https://www.fulltrust.net
// Fecha última actualización: 2024-12-31 10:55:01
// ************************************************************

//error_reporting(E_ALL);
//ini_set('display_errors', 1);

	@session_start();
	include("../conf/db.php");		
    include('functions.php');
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1">	
<title>Lista de proyectos</title>

<link rel="icon" type="image/png" href="../favicon.png">


<script src="js/jquery-3.5.1.min.js"></script>	
	
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css">

    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.html5.min.js"></script>
	

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>	

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">

<link rel="stylesheet" type="text/css" href="../css/styles.css"/>

<style>
 .dataTables_filter{
    display: block !important;
    margin: 0px !important;
    background-color: white !important;
} 
label {
  background-color: white !important;
  border: 0px solid #ccc !important;
}



/* Style for the pagination container */
.dataTables_wrapper .dataTables_paginate {
    margin-top: 5px;
}

/* Style for the pagination buttons */
.dataTables_wrapper .dataTables_paginate .paginate_button {
    padding: 5px 10px;
    margin: 0 2px;
    border: 1px solid #ddd;
    background-color: #f9f9f9;
    color: #333;
    cursor: pointer;
}

/* Style for the active pagination button */
.dataTables_wrapper .dataTables_paginate .paginate_button.current {
    background-color: #007bff;
    color: white;
    border: 1px solid #007bff;
}

/* Style for the disabled pagination buttons */
.dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
    background-color: #e9ecef;
    color: #6c757d;
    cursor: not-allowed;
}
</style> 
	
<script>
	
function abre(id){
		document.form1["wid"].value=id;
		document.form1.submit();
}	


$(document).ready(function() {
    var token = $("#sid").val();
    var task = "list";

    var tabla = $('#tabla').DataTable({
        initComplete: function(settings, json) {
            agregarFiltroArea(); // Agregar filtro después de que la tabla esté cargada
        },

        searching: true,
        bFilter: true,
        search: {
            regex: true,
            smart: true
        },

        buttons: [{
            extend: 'excel',
            text: 'Exportar a Excel',
            className: 'btn-excel',
            exportOptions: {
                columns: 'th:not(.no-export)'
            }
        }],

        retrieve: true,
        searchHighlight: true,

        bPaginate: true,
        sPaginationType: "full_numbers",
        iDisplayLength: 50,
        aaSorting: [[0, 'asc']],

        dom: 'B<"top"ifp<"clear">>rt<"bottom"ip<"clear">>',

        responsive: true,
        processing: true,

        columnDefs: [
            { "targets": 0, visible: true, width: "5%" },
            { "targets": 1, visible: true, width: "34%" },
            { "targets": 2, visible: true, width: "17%" },
            { "targets": 3, visible: true, width: "17%" },
            { "targets": 4, visible: true, width: "17%" },
            { "targets": 5, visible: true, width: "17%" },
            { "targets": 6, visible: true, width: "10%" },
            { "targets": 7, visible: true },

            { "targets": 8, visible: false },
            { "targets": 9, visible: false },
            { "targets": 10, visible: false },
            { "targets": 11, visible: false },
            { "targets": 12, visible: false },
            { "targets": 13, visible: false },
            { "targets": 14, visible: false },
            { "targets": 15, visible: false },
            { "targets": 16, visible: false },
            { "targets": 17, visible: false },
            { "targets": 18, visible: false },
            { "targets": 19, visible: false },
            { "targets": 20, visible: false },
        ],

        "sAjaxSource": "api/proyectos.php?token=" + token + "&task=list",
        "aoColumns": [
            { mData: mix },
            { mData: 'nombre' },
            { mData: 'area' },
            { mData: 'unidad' },
            { mData: 'origen' },
            { mData: 'etapa' },
            { mData: 'estado' },
            { mData: 'responsable' },

            { mData: 'lineamiento' },
            { mData: 'objetivos' },
            { mData: 'tipo' },
            { mData: 'p_diseno' },
            { mData: 'p_prefactibilidad' },
            { mData: 'p_ejecucion' },
            { mData: 'total' },
            { mData: 'financiamiento' },
            { mData: 'unidad' },
            { mData: 'entidades_relacionadas' },
            { mData: 'etapa' },
            { mData: 'avance_actividades' },
            { mData: 'avance_financiero' },
        ],

        "language": {
            "sProcessing": "<p align='center'><img src='img/ajax-loader.svg' /></p>",
            "sLengthMenu": "Mostrar _MENU_ registros",
            "sZeroRecords": "No se encontraron resultados",
            "sEmptyTable": "Ningún dato disponible en esta tabla",
            "sInfo": "_START_ al _END_ de _TOTAL_ ",
            "sInfoEmpty": "0 al 0 ",
            "sInfoFiltered": "_MAX_ registros)",
            "sInfoPostFix": "",
            "sSearch": "",
            "searchPlaceholder": "Buscar",
            "sUrl": "",
            "sInfoThousands": ",",
            "sLoadingRecords": "",
            "oPaginate": {
                "sFirst": "<<",
                "sLast": ">>",
                "sNext": ">",
                "sPrevious": "<"
            },
            "oAria": {
                "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                "sSortDescending": ": Activar para ordenar la columna de manera descendente"
            }
        }
    });

    // Función para agregar el filtro dropdown
    function agregarFiltroArea() {
        // Crear el select y agregarlo arriba de la tabla
        if ($("#filtroArea").length === 0) {
            $("#tabla_wrapper .top").prepend('<label>Ámbito: <select id="filtroArea"><option value="">Todos</option></select></label>');
        }

        // Llenar el dropdown después de que la tabla haya sido completamente cargada
        var areas = [];
        tabla.column(2).data().each(function(value) {
            if (areas.indexOf(value) === -1) {
                areas.push(value);
            }
        });

        // Agregar las opciones al select
        areas.forEach(function(area) {
            $("#filtroArea").append('<option value="' + area + '">' + area + '</option>');
        });

        // Evento para filtrar la tabla cuando cambia el select
        $("#filtroArea").on("change", function() {
            var valorSeleccionado = $(this).val();
            tabla.column(2).search(valorSeleccionado).draw();
        });
    }

    $("#bexcel").on("click", function() {
        $(".buttons-excel").trigger("click");
    });

    function mix(data, type, dataToSet) {
        var link = '<a href="proyectos_editar.php?proyectos_id=' + data.id + '"><img src="../img/document.png" width="32"></a>';
        return link;
    }

    $('#tabla').on('error.dt', function(e, settings, techNote, message) {
        console.log('An error has been reported by DataTables: ', message);
    });
});


function abrir(cod){
	$("#cont" ).load( "loading.php");
	$("#cont" ).load( "licitaciones_detalle.php?wid="+cod );

	$("#cont").toggle();
	//document.form1["wid"].value=cod;
	//document.form1.submit();
}

	
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

<?php include("header.php");?>

    <div class="container-fluid">
        <div class="row">

        <?php include("sidebar.php");?>

            <!-- Área de trabajo central -->
            <main role="main" class="col-md-9 ms-sm-auto col-lg-10 px-md-4">

                <div class="row top-info-div">
                    <div class="col">
                        <h5>PROYECTOS</h5>
                    </div>
                    <div class="col text-end">
                    <button id="bexcel" type="button" class="btn btn-primary btn-excel"><i class="fa-solid fa-file-arrow-down"></i>&nbsp;Exportar datos</button>
                    <button onclick="window.location='proyectos_editar.php?proyectos_id=0'" type="button" class="btn btn-primary"><i class="fa-solid fa-plus"></i>&nbsp;Nuevo proyecto</button>
                    </div>                        
                </div>

                <div class="container mt-3">

                    <table id="tabla" class="table display hoverable " style="width: 100% !important">
                        <tbody>
                        <thead>
                            <tr>
                            <th style="width:5% !important;">VER</th>
                            <th style="width:17%">Nombre</th>
                            <th style="width:17%">Ámbito</th>
                            <th style="width:17%">Área Gestión</th>
                            <th style="width:17%">Origen</th>
                            <th style="width:17%">Etapa</th>
                            <th style="width:17%">Estado</th>
                            <th style="width:17%">Coordinador</th>
                            </tr>
                        </thead>

                        </tbody>
                    </table>	



                </div>
            </main>
        </div>
    </div>

<?php include("footer.php");?>

    <!-- Enlace a JavaScript de Bootstrap 5 y dependencias -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js" integrity="sha384-cuYeSxntonz0PPNlHhBs68uyIAVpIIOZZ5JqeqvYYIcEL727kskC66kF92t6Xl2V" crossorigin="anonymous"></script>
    <script src="app.js"></script>
</body>
</html>
