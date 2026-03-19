<?php 
// ************************************************************
// Autor: Marcelo Jiménez
// Fulltrust Software
// https://www.fulltrust.net
// Fecha última actualización: 2026-03-19 10:12:32
// ************************************************************

@session_start();
include("../conf/db.php");		
include('functions.php');
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">	
<title>Lista de Iniciativas</title>

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

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet" type="text/css" href="css/styles.css"/>

<style>
.dataTables_filter{
    display: block !important;
    margin: 0px !important;
    background-color: white !important;
} 
.dataTables_wrapper .dataTables_paginate { margin-top: 5px; }
.dataTables_wrapper .dataTables_paginate .paginate_button {
    padding: 5px 10px; margin: 0 2px;
    border: 1px solid #ddd; background-color: #f9f9f9; color: #333; cursor: pointer;
}
.dataTables_wrapper .dataTables_paginate .paginate_button.current {
    background-color: #007bff; color: white; border: 1px solid #007bff;
}
.dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
    background-color: #e9ecef; color: #6c757d; cursor: not-allowed;
}
</style> 
	
<script>
$(document).ready(function() {

    var token = $("#sid").val();

    var tabla = $('#tabla').DataTable({

        initComplete: function(settings, json) {
            agregarFiltroSector();
        },

        searching: true,
        bFilter:   true,
        search: { regex: true, smart: true },

        buttons: [{
            extend: 'excel',
            text: 'Exportar a Excel',
            className: 'btn-excel',
            exportOptions: { columns: ':not(.no-export)' }
        }],

        retrieve:        true,
        searchHighlight: true,
        bPaginate:       true,
        sPaginationType: "full_numbers",
        iDisplayLength:  50,
        aaSorting:       [[1, 'asc']],
        dom: 'B<"top"ifp<"clear">>rt<"bottom"ip<"clear">>',
        responsive:  true,
        processing:  true,

        columnDefs: [
            { targets: 0,  visible: true,  width: "4%"  },   // VER
            { targets: 1,  visible: true,  width: "32%" },   // nombre
            { targets: 2,  visible: true,  width: "14%" },   // sector
            { targets: 3,  visible: true,  width: "14%" },   // etapa
            { targets: 4,  visible: true,  width: "14%" },   // proceso
            { targets: 5,  visible: true,  width: "7%"  },   // finicio
            { targets: 6,  visible: true,  width: "7%"  },   // ftermino
            { targets: 7,  visible: false },   // unidad_responsable
            { targets: 8,  visible: false },   // instrumento
            { targets: 9,  visible: false },   // preseleccionado
            { targets: 10, visible: false },   // lineamiento_id
            { targets: 11, visible: false },   // lineamiento
            { targets: 12, visible: false },   // objetivo_id
            { targets: 13, visible: false },   // objetivo
            { targets: 14, visible: false },   // brecha
            { targets: 15, visible: false },   // descripcion
            { targets: 16, visible: false },   // localizacion
            { targets: 17, visible: false },   // subsector
            { targets: 18, visible: false },   // tipo
            { targets: 19, visible: false },   // impacto_territorial
            { targets: 20, visible: false },   // foco_turismo
            { targets: 21, visible: false },   // codigo_idi
            { targets: 22, visible: false },   // p_diseno
            { targets: 23, visible: false },   // p_ejecucion
            { targets: 24, visible: false },   // total
            { targets: 25, visible: false },   // fuente
            { targets: 26, visible: false },   // instituciones_vinculadas
            { targets: 27, visible: false },   // beneficiarios
            { targets: 28, visible: false },   // informacion
            { targets: 29, visible: false },   // avance_financiero
            { targets: 30, visible: false },   // avance_actividades
            { targets: 31, visible: false },   // lat
            { targets: 32, visible: false },   // lng
            { targets: 33, visible: false },   // proyectos_user
            { targets: 34, visible: false },   // proyectos_created
        ],

        "sAjaxSource": "api/proyectos.php?token=" + token + "&task=list",

        "aoColumns": [
            { mData: mixVer,                   className: 'no-export' },
            { mData: 'nombre' },
            { mData: 'sector' },
            { mData: 'etapas_descripcion' },
            { mData: 'procesos_descripcion' },
            { mData: 'finicio' },
            { mData: 'ftermino' },
            { mData: 'unidad_responsable' },
            { mData: 'instrumento' },
            { mData: 'preseleccionado' },
            { mData: 'lineamiento_id' },
            { mData: 'lineamiento' },
            { mData: 'objetivo_id' },
            { mData: 'objetivo' },
            { mData: 'brecha' },
            { mData: 'descripcion' },
            { mData: 'localizacion' },
            { mData: 'subsector' },
            { mData: 'tipo' },
            { mData: 'impacto_territorial' },
            { mData: 'foco_turismo' },
            { mData: 'codigo_idi' },
            { mData: 'p_diseno' },
            { mData: 'p_ejecucion' },
            { mData: 'total' },
            { mData: 'fuente' },
            { mData: 'instituciones_vinculadas' },
            { mData: 'beneficiarios' },
            { mData: 'informacion' },
            { mData: 'avance_financiero' },
            { mData: 'avance_actividades' },
            { mData: 'lat' },
            { mData: 'lng' },
            { mData: 'proyectos_user' },
            { mData: 'proyectos_created' },
        ],

        "language": {
            "sProcessing":   "<p align='center'><img src='img/ajax-loader.svg' /></p>",
            "sLengthMenu":   "Mostrar _MENU_ registros",
            "sZeroRecords":  "No se encontraron resultados",
            "sEmptyTable":   "Ningún dato disponible en esta tabla",
            "sInfo":         "(_START_ al _END_ de _TOTAL_) registros",
            "sInfoEmpty":    "0 al 0 ",
            "sInfoFiltered": "de _MAX_ totales",
            "sInfoPostFix":  "",
            "sSearch":       "",
            "searchPlaceholder": "Buscar",
            "sUrl":          "",
            "sInfoThousands": ",",
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

    function agregarFiltroSector() {
        if ($("#filtroSector").length === 0) {
            $("#tabla_wrapper .top").prepend(
                '<label style="margin-right:15px">Sector:&nbsp;' +
                '<select id="filtroSector"><option value="">Todos</option></select></label>'
            );
        }
        var sectores = [];
        tabla.column(2).data().each(function(value) {
            if (value && sectores.indexOf(value) === -1) sectores.push(value);
        });
        sectores.sort().forEach(function(s) {
            $("#filtroSector").append('<option value="' + s + '">' + s + '</option>');
        });
        $("#filtroSector").on("change", function() {
            tabla.column(2).search($(this).val()).draw();
        });
    }

    $("#bexcel").on("click", function() {
        $(".buttons-excel").trigger("click");
    });

    function mixVer(data) {
        return '<a href="proyectos_editar.php?proyectos_id=' + data.id + '">' +
               '<img src="img/document.png" width="32"></a>';
    }

    $('#tabla').on('error.dt', function(e, settings, techNote, message) {
        console.log('DataTables error: ', message);
    });

});

function ayuda(){
    $.confirm({
        title: '', type: 'orange', boxWidth: '90%',
        typeAnimated: true, closeIcon: true,
        buttons: { info: { text: 'Cerrar', action: function(){} } },
        content: 'url:ayuda_proyetos.html'
    });   	
}	
</script>

</head>
<body>

<?php include("header.php");?>

<!-- Token de sesión para el Ajax -->
<input type="hidden" id="sid" value="<?php echo session_id(); ?>">

<div id="wrapper">
    <div id="sidebar-wrapper">
        <?php include("sidebar.php");?>
    </div>
    
    <div id="page-content-wrapper">
        <div class="container-fluid p-4">

            <div class="row top-info-div">
                <div class="col">
                    <h5>INICIATIVAS</h5>
                </div>
                <div class="col text-end">
                    <button id="bexcel" type="button" class="btn btn-primary btn-excel">
                        <i class="fa-solid fa-file-arrow-down"></i>&nbsp;Exportar datos
                    </button>
                    <button onclick="window.location='proyectos_editar.php?proyectos_id=0'" type="button" class="btn btn-primary">
                        <i class="fa-solid fa-plus"></i>&nbsp;Nueva iniciativa
                    </button>
                </div>                        
            </div>

            <div class="container mt-3">
                <table id="tabla" class="table display hoverable" style="width:100% !important">
                    <thead>
                        <tr>
                            <!-- Visibles -->
                            <th style="width:4%">VER</th>
                            <th style="width:32%">Nombre</th>
                            <th style="width:14%">Sector</th>
                            <th style="width:14%">Etapa</th>
                            <th style="width:14%">Proceso</th>
                            <th style="width:7%">Inicio</th>
                            <th style="width:7%">Término</th>
                            <!-- Ocultas / solo Excel -->
                            <th>Responsable</th>
                            <th>Instrumento</th>
                            <th>Preseleccionado</th>
                            <th>ID Lineamiento</th>
                            <th>Lineamiento</th>
                            <th>ID Objetivo</th>
                            <th>Objetivo</th>
                            <th>Brecha</th>
                            <th>Descripción</th>
                            <th>Localización</th>
                            <th>Subsector</th>
                            <th>Tipo</th>
                            <th>Impacto Territorial</th>
                            <th>Foco Turismo</th>
                            <th>Código IDI</th>
                            <th>Presup. Diseño</th>
                            <th>Presup. Ejecución</th>
                            <th>Total</th>
                            <th>Fuente</th>
                            <th>Instituciones Vinculadas</th>
                            <th>Beneficiarios</th>
                            <th>Información</th>
                            <th>Avance Financiero</th>
                            <th>Avance Actividades</th>
                            <th>Latitud</th>
                            <th>Longitud</th>
                            <th>Usuario</th>
                            <th>Fecha Creación</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            
        </div>
    </div>
</div>

<?php include("footer.php");?>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js"></script>
<script src="app.js"></script>
</body>
</html>
