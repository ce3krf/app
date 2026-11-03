<?php 
// ************************************************************
// Autor: Marcelo Jiménez
// Fulltrust Software
// https://www.fulltrust.net
// Fecha última actualización: 2026-10-05
// ************************************************************

@session_start();
include("../conf/db.php");		
include('functions.php');

// Cargar instrumentos para el filtro
$instrumentos_filtro = [];
$res_inst_f = $db->query("SELECT instrumentos_id, instrumentos_descripcion FROM instrumentos ORDER BY instrumentos_descripcion");
if ($res_inst_f) {
    while ($r = $res_inst_f->fetch_assoc()) {
        $instrumentos_filtro[] = $r;
    }
}

// Columnas ocultas: definen el Excel exportado (en este orden) y sirven para los filtros.
// Siguen las secciones y los nombres de la ficha de la iniciativa (proyectos_editar.php).
// data = campo del JSON de la API; name = nombre interno (solo si data se repite con una columna visible)
$columnas_ocultas = [
    // INICIATIVA
    ['data' => 'id',                                    'titulo' => 'Id'],
    ['data' => 'numero',                                'titulo' => 'N°'],
    ['data' => 'codigo',                                'titulo' => 'Código', 'name' => 'exp_codigo'],
    ['data' => 'codigo_idi',                            'titulo' => 'IDI / BIP'],
    ['data' => 'nombre_iniciativa',                     'titulo' => 'Nombre de la iniciativa'],
    ['data' => 'seleccionada_pm_sjm',                   'titulo' => 'Preseleccionada Plan Maestro SJM'],
    ['data' => 'tipo_de_inversion',                     'titulo' => 'Tipo de inversión'],
    ['data' => 'pm',                                    'titulo' => 'PM'],
    // DESCRIPCIÓN
    ['data' => 'n_inst',                                'titulo' => 'Cód. Instrumento de origen'],
    ['data' => 'instrumento_de_origen',                 'titulo' => 'Instrumento de origen'],
    ['data' => 'cod_tipo',                              'titulo' => 'Cód. Tipología'],
    ['data' => 'tipo',                                  'titulo' => 'Tipología de iniciativa'],
    ['data' => 'descripcion',                           'titulo' => 'Descripción'],
    ['data' => 'eje_estrategico',                       'titulo' => 'Eje estratégico'],
    ['data' => 'objetivo_estrategico',                  'titulo' => 'Objetivo estratégico'],
    ['data' => 'utr',                                   'titulo' => 'Unidad Técnica Responsable (UTR)'],
    ['data' => 'cod_sector',                            'titulo' => 'Cód. Sector SNI'],
    ['data' => 'sector',                                'titulo' => 'Sector SNI', 'name' => 'exp_sector'],
    ['data' => 'cod_subsector',                         'titulo' => 'Cód. Subsector SNI'],
    ['data' => 'subsector_de_inversion_sni',            'titulo' => 'Subsector SNI'],
    // BRECHAS
    ['data' => 'brecha_principal',                      'titulo' => 'Brecha principal'],
    ['data' => 'brecha_especifica',                     'titulo' => 'Brecha específica'],
    ['data' => 'atributo_al_que_responde',              'titulo' => 'Atributo al que responde'],
    // EMPLAZAMIENTO
    ['data' => 'localizacion',                          'titulo' => 'Alcance'],
    ['data' => 'localizacion_2',                        'titulo' => 'Localización'],
    ['data' => 'localidades',                           'titulo' => 'Localidades cubiertas'],
    ['data' => 'point_x',                               'titulo' => 'Latitud'],
    ['data' => 'point_y',                               'titulo' => 'Longitud'],
    ['data' => 'n_ubicaciones',                         'titulo' => 'N° ubicaciones adicionales'],
    ['data' => 'ubicaciones_adicionales',               'titulo' => 'Ubicaciones adicionales'],
    // FINANCIAMIENTO
    ['data' => 'indicador_2',                           'titulo' => 'Indicador'],
    ['data' => 'unidad_de_medida',                      'titulo' => 'Unidad'],
    ['data' => 'cantidad_proyectada',                   'titulo' => 'Cantidad proyectada'],
    ['data' => 'cantidad_realizada',                    'titulo' => 'Cantidad realizada'],
    ['data' => 'costos_perfil_prefactibilidad',         'titulo' => 'M$ Prefactibilidad'],
    ['data' => 'costos_diseno',                         'titulo' => 'M$ Diseño'],
    ['data' => 'costos_ejecucion',                      'titulo' => 'M$ Ejecución'],
    ['data' => 'suma_costo_prefactibilidad_y_diseno',   'titulo' => 'M$ Prefactibilidad + Diseño'],
    ['data' => 'total',                                 'titulo' => 'M$ Costo total'],
    ['data' => 'costo_unitario_ejecucion',              'titulo' => 'Valor unidad ejecución M$'],
    ['data' => 'origen_del_calculo_de_costos',          'titulo' => 'Origen del cálculo de costos'],
    ['data' => 'fuente_financiamiento_prefactibilidad', 'titulo' => 'Fuente financiamiento Prefactibilidad'],
    ['data' => 'fuente_financiamiento_diseno',          'titulo' => 'Fuente financiamiento Diseño'],
    ['data' => 'fuente_financiamiento_ejecucion',       'titulo' => 'Fuente financiamiento Ejecución'],
    ['data' => 'gasto_perfil_prefactibilidad',          'titulo' => 'Gasto Prefactibilidad'],
    ['data' => 'gasto_diseno',                          'titulo' => 'Gasto Diseño'],
    ['data' => 'gasto_ejecucion',                       'titulo' => 'Gasto Ejecución'],
    // MANTENCIÓN
    ['data' => 'responsable_del_mantenimiento',         'titulo' => 'Mantención: Responsable'],
    ['data' => 'financiamiento_del_mantenimiento',      'titulo' => 'Mantención: Financiamiento'],
    // CRONOGRAMA (AÑOS)
    ['data' => 'inicio',                                'titulo' => 'Inicio', 'name' => 'exp_inicio'],
    ['data' => 'termino',                               'titulo' => 'Término', 'name' => 'exp_termino'],
    ['data' => 'plazos_ejecucion_anos',                 'titulo' => 'Plazo de ejecución (años)'],
    // ESTADO
    ['data' => 'cod_etapa_actual',                      'titulo' => 'Cód. Etapa actual'],
    ['data' => 'etapa_actual',                          'titulo' => 'Etapa actual', 'name' => 'exp_etapa'],
    ['data' => 'cod_actividad_actual',                  'titulo' => 'Cód. Actividad actual (proceso)'],
    ['data' => 'actividad_actual',                      'titulo' => 'Actividad actual (proceso)', 'name' => 'exp_proceso'],
    ['data' => 'avance_actividades',                    'titulo' => 'Avance implementación %'],
    ['data' => 'avance_financiero',                     'titulo' => 'Avance financiero %'],
    ['data' => 'total_actividades',                     'titulo' => 'N° actividades'],
    ['data' => 'actividades_completas',                 'titulo' => 'N° actividades completas'],
    // REGISTRO
    ['data' => 'proyectos_user',                        'titulo' => 'Modificado por'],
    ['data' => 'proyectos_created',                     'titulo' => 'Fecha creación'],
    ['data' => 'proyectos_updated',                     'titulo' => 'Última actualización'],
    ['data' => 'es_mio',                                'titulo' => 'Propia'],
];
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
.fila-propia td {
    background-color: #d4edda !important;
}
.fila-propia:hover td {
    background-color: #c3e6cb !important;
}
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

/* ── Barra de filtros ── */
.filtros-bar {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    align-items: flex-end;
    background: #ffffff;
    border: 1px solid #e0e0e0;
    border-radius: 10px;
    padding: 14px 18px;
    margin-bottom: 14px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.06);
}

.filtro-item {
    display: flex;
    flex-direction: column;
    gap: 4px;
    min-width: 160px;
    flex: 1 1 160px;
}

.filtro-item label {
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.6px;
    color: var(--main, #023047);
    margin: 0;
}

.filtro-item select {
    padding: 7px 10px;
    border: 1px solid #ced4da;
    border-radius: 6px;
    font-size: 13px;
    color: #333;
    background-color: #f8f9fa;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23555' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 10px center;
    appearance: none;
    -webkit-appearance: none;
    cursor: pointer;
    transition: border-color 0.2s, box-shadow 0.2s;
}

.filtro-item select:focus {
    outline: none;
    border-color: var(--topdiv, #4a8ecd);
    box-shadow: 0 0 0 3px rgba(74,142,205,0.18);
    background-color: #fff;
}

.filtro-item select:hover {
    border-color: var(--topdiv, #4a8ecd);
}

.filtro-item label i {
    margin-right: 4px;
    opacity: 0.7;
}

/* ── Segunda línea por registro ── */
.nombre-principal {
    font-weight: 600;
    color: #1a1a2e;
    line-height: 1.4;
}

.meta-row {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 5px;
}

.meta-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 13px;
    font-weight: 500;
    padding: 2px 8px;
    border-radius: 20px;
    white-space: nowrap;
    cursor: default;
}

.meta-badge i {
    font-size: 10px;
    opacity: 0.8;
}

.badge-total {
    background: #e8f4fd;
    color: #1565c0;
    border: 1px solid #bbdefb;
}

.badge-impl {
    background: #f3e5f5;
    color: #6a1b9a;
    border: 1px solid #e1bee7;
}

.badge-fin {
    background: #e8f5e9;
    color: #2e7d32;
    border: 1px solid #c8e6c9;
}

.progress-mini {
    display: inline-block;
    width: 42px;
    height: 5px;
    background: #e0e0e0;
    border-radius: 3px;
    overflow: hidden;
    vertical-align: middle;
    margin-left: 3px;
}

.progress-mini-bar {
    height: 100%;
    border-radius: 3px;
}

.bar-impl  { background: #9c27b0; }
.bar-fin   { background: #43a047; }

.dt-buttons { display: none !important; }
</style> 
	
<script>
$(document).ready(function() {

    var token = $("#sid").val();
    var columnasOcultas = <?php echo json_encode($columnas_ocultas, JSON_UNESCAPED_UNICODE); ?>;

    var tabla = $('#tabla').DataTable({

        initComplete: function(settings, json) {
            agregarFiltros();
        },

        searching: true,
        bFilter:   true,
        search: { regex: true, smart: true },

        buttons: [{
            extend: 'excel',
            text: 'Exportar a Excel',
            className: 'btn-excel',
            title: null,   // sin fila de título: la primera fila son los encabezados
            // Nombre: Iniciativas_AAAA-MM-DD_HH-MM.xlsx
            filename: function () {
                var d = new Date(), z = function (n) { return (n < 10 ? '0' : '') + n; };
                return 'Iniciativas_' + d.getFullYear() + '-' + z(d.getMonth() + 1) + '-' + z(d.getDate()) +
                       '_' + z(d.getHours()) + '-' + z(d.getMinutes());
            },
            exportOptions: { columns: ':not(.no-export)' }
        }],

        retrieve:        true,
        searchHighlight: true,
        bPaginate:       true,
        sPaginationType: "full_numbers",
        iDisplayLength:  50,
        aaSorting:       [[2, 'asc']],
        dom: 'B<"top"ifp<"clear">>rt<"bottom"ip<"clear">>',
        responsive:  true,
        processing:  true,

        "sAjaxSource": "api/api_proyectos.php?token=" + token + "&task=list",

        // Visibles: VER, Código, Nombre, Sector, Etapa, Proceso, Inicio, Término
        // Luego las ocultas (definidas en PHP, mismo orden que el <thead>)
        "aoColumns": [
            { mData: mixVer,             name: 'ver',     className: 'no-export', width: '4%',  orderable: false },
            { mData: 'codigo',           name: 'codigo',  className: 'text-nowrap no-export', width: '6%' },
            { mData: mixNombre,          name: 'nombre',  className: 'no-export', width: '34%' },
            { mData: 'sector',           name: 'sector',  width: '14%', className: 'no-export' },
            { mData: 'etapa_actual',     name: 'etapa',   width: '14%', className: 'no-export' },
            { mData: 'actividad_actual', name: 'proceso', width: '14%', className: 'no-export' },
            { mData: 'inicio',           name: 'inicio',  width: '7%', className: 'text-center no-export' },
            { mData: 'termino',          name: 'termino', width: '7%', className: 'text-center no-export' }
        ].concat(columnasOcultas.map(function(c) {
            return { mData: c.data, name: c.name || c.data, visible: false, defaultContent: '' };
        })),

        "createdRow": function(row, data) {
            if (data.es_mio == 1) {
                $(row).addClass('fila-propia');
            }
        },

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

    function agregarFiltros() {
        if ($("#filtroInstrumento").length === 0) {

            var filtroPropias = <?php echo ($_SESSION['usuarios_profile'] === 'ÁREA') ? "'<div class=\"filtro-item\"><label><i class=\"fa-solid fa-user-check\"></i>Propias</label><select id=\"filtroPropias\"><option value=\"\">Todas</option><option value=\"1\">Sí</option><option value=\"0\">No</option></select></div>'" : "''"; ?>;

            var instrumentos = <?php echo json_encode(array_values($instrumentos_filtro)); ?>;
            var optsInstrumento = '<option value="">Todos</option>';
            instrumentos.forEach(function(inst) {
                optsInstrumento += '<option value="' + inst.instrumentos_id + '">' + inst.instrumentos_descripcion + '</option>';
            });

            $("#tabla_wrapper .top").prepend(
                '<div class="filtros-bar">' +
                  '<div class="filtro-item">' +
                    '<label><i class="fa-solid fa-layer-group"></i>Instrumento</label>' +
                    '<select id="filtroInstrumento">' + optsInstrumento + '</select>' +
                  '</div>' +
                  '<div class="filtro-item">' +
                    '<label><i class="fa-solid fa-star"></i>Plan Maestro</label>' +
                    '<select id="filtroPlan">' +
                      '<option value="">Todos</option>' +
                      '<option value="Si">S\u00ed</option>' +
                      '<option value="No">No</option>' +
                    '</select>' +
                  '</div>' +
                  '<div class="filtro-item">' +
                    '<label><i class="fa-solid fa-building"></i>Sector</label>' +
                    '<select id="filtroSector"><option value="">Todos</option></select>' +
                  '</div>' +
                  filtroPropias +
                '</div>'
            );
        }

        // Poblar opciones de Sector
        var sectores = [];
        tabla.column('sector:name').data().each(function(value) {
            if (value && sectores.indexOf(value) === -1) sectores.push(value);
        });
        sectores.sort().forEach(function(s) {
            $("#filtroSector").append($('<option>').val(s).text(s));
        });

        // (Instrumento ya viene poblado desde PHP)

        // Filtro Sector
        $("#filtroSector").on("change", function() {
            var val = $(this).val();
            tabla.column('sector:name').search(
                val === '' ? '' : '^' + $.fn.dataTable.util.escapeRegex(val) + '$',
                true,
                false
            ).draw();
        });

        // Filtro Instrumento (n_inst)
        $("#filtroInstrumento").on("change", function() {
            var val = $(this).val();
            tabla.column('n_inst:name').search(
                val === '' ? '' : '^' + $.fn.dataTable.util.escapeRegex(val) + '$',
                true,
                false
            ).draw();
        });

        // Filtro Plan Maestro (seleccionada_pm_sjm Si/No)
        $("#filtroPlan").on("change", function() {
            var val = $(this).val();
            tabla.column('seleccionada_pm_sjm:name').search(
                val === '' ? '' : '^' + val + '$',
                true,   // regex
                false   // smart search off
            ).draw();
        });

        // Filtro Propias (es_mio, solo perfil ÁREA)
        $("#filtroPropias").on("change", function() {
            var val = $(this).val();
            tabla.column('es_mio:name').search(
                val === '' ? '' : '^' + val + '$',
                true,
                false
            ).draw();
        });
    }

    $("#bexcel").on("click", function() {
        $(".buttons-excel").trigger("click");
    });

    function mixVer(data) {
        return '<a href="proyectos_editar.php?proyectos_id=' + data.id + '">' +
               '<img src="img/document.png" width="32"></a>';
    }

    function mixNombre(data) {
        return '<div class="nombre-principal">' + (data.nombre_iniciativa || '') + '</div>';
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
<input type="hidden" id="perfil_usuario" value="<?php echo htmlspecialchars($_SESSION['usuarios_profile'] ?? ''); ?>">

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

<?php if ($_SESSION['usuarios_profile'] === 'ÁREA'): ?>
            <div class="row mb-2">
                <div class="col">
                    <small class="text-muted">
                        <span style="display:inline-block; width:10px; height:10px; background-color:#d4edda; border:1px solid #b1dfbb; border-radius:2px; margin-right:5px;"></span>
                        Las iniciativas destacadas en verde pertenecen a sus instrumentos asignados.
                    </small>
                </div>
            </div>
<?php endif; ?>

            <div class="container mt-3">
                <table id="tabla" class="table display hoverable" style="width:100% !important">
                    <thead>
                        <tr>
                            <!-- Visibles -->
                            <th>VER</th>
                            <th>Código</th>
                            <th>Nombre</th>
                            <th>Sector</th>
                            <th>Etapa actual</th>
                            <th>Proceso</th>
                            <th>Inicio</th>
                            <th>Término</th>
                            <!-- Ocultas / solo Excel -->
<?php foreach ($columnas_ocultas as $c): ?>
                            <th><?php echo htmlspecialchars($c['titulo']); ?></th>
<?php endforeach; ?>
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
