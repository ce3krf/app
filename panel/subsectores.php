<?php 
// ************************************************************
// Autor: Marcelo Jiménez
// Fulltrust Software
// https://www.fulltrust.net
// Fecha última actualización: 2024-12-31
// ************************************************************

@session_start();
include("../../conf/db.php");		
include('functions.php');
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">	
<title>Lista de subsectores</title>

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

.dataTables_wrapper .dataTables_paginate {
    margin-top: 5px;
}

.dataTables_wrapper .dataTables_paginate .paginate_button {
    padding: 5px 10px;
    margin: 0 2px;
    border: 1px solid #ddd;
    background-color: #f9f9f9;
    color: #333;
    cursor: pointer;
}

.dataTables_wrapper .dataTables_paginate .paginate_button.current {
    background-color: #007bff;
    color: white;
    border: 1px solid #007bff;
}

.dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
    background-color: #e9ecef;
    color: #6c757d;
    cursor: not-allowed;
}
</style> 
	
<script>
$(document).ready(function() {
    var token = $("#sid").val();
    var task = "list";

    var tabla = $('#tabla').DataTable({
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
            { "targets": 0, visible: true, width: "10%" },
            { "targets": 1, visible: true, width: "90%" }
        ],

        "sAjaxSource": "api/subsectores.php?token=" + token + "&task=list",
        "aoColumns": [
            { mData: mix },
            { mData: 'subsector_descripcion' }
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

    $("#bexcel").on("click", function() {
        $(".buttons-excel").trigger("click");
    });

    function mix(data, type, dataToSet) {
        var link = '<a href="subsectores_editar.php?subsector_id=' + data.subsector_id + '"><img src="img/document.png" width="32"></a>';
        return link;
    }

    $('#tabla').on('error.dt', function(e, settings, techNote, message) {
        console.log('An error has been reported by DataTables: ', message);
    });
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
                action: function(){}
            }
        },
        content: 'url:ayuda_subsectores.html'
    });   	
}
</script>

</head>
<body>

<?php include("header.php");?>

<div id="wrapper">
    <div id="sidebar-wrapper">
        <?php include("sidebar.php");?>
    </div>
    
    <div id="page-content-wrapper">
        <div class="container-fluid p-4">

            <div class="row top-info-div">
                <div class="col">
                    <h5>SUBSECTORES</h5>
                </div>
                <div class="col text-end">
                    <button id="bexcel" type="button" class="btn btn-primary btn-excel"><i class="fa-solid fa-file-arrow-down"></i>&nbsp;Exportar datos</button>
                    <button onclick="window.location='subsectores_editar.php?subsector_id=0'" type="button" class="btn btn-primary"><i class="fa-solid fa-plus"></i>&nbsp;Nuevo subsector</button>
                </div>                        
            </div>

            <div class="container mt-3">
                <table id="tabla" class="table display hoverable" style="width: 100% !important">
                    <thead>
                        <tr>
                            <th style="width:10% !important;">VER</th>
                            <!-- <th style="width:20%">Código</th> -->
                            <th style="width:90%">Descripción</th>
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