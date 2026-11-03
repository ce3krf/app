<?php
// ************************************************************
// Autor: Marcelo Jiménez
// Fulltrust Software
// https://www.fulltrust.net
// Fecha última actualización: 2026-10-24
//
// Consulta del log de auditoría (solo ADMINISTRADOR)
// ************************************************************

@session_start();
include("../conf/db.php");
include('functions.php');

if (($_SESSION['usuarios_profile'] ?? '') !== 'ADMINISTRADOR') {
    ?><script>window.location='index.php';</script><?php
    exit;
}

// Filtros iniciales por URL (ej. auditoria.php?tabla=proyectos&registro_id=45)
$f_tabla       = htmlspecialchars($_GET['tabla'] ?? '');
$f_registro_id = htmlspecialchars($_GET['registro_id'] ?? '');
$f_proyecto_id = htmlspecialchars($_GET['proyecto_id'] ?? '');
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Auditoría</title>

<link rel="icon" type="image/png" href="../favicon.png">
<script src="js/jquery-3.5.1.min.js"></script>

<link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css"/>
<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="css/styles.css"/>

<style>
.filtros { background: #fff; border: 1px solid #e3e8ef; border-radius: 8px; padding: 16px 18px; margin-bottom: 14px; }
.filtros label { font-size: .82rem; color: #5b6b7f; margin-bottom: 4px; }
.filtros .form-control, .filtros .form-select { font-size: .9rem; }
/* La casilla no usa el estilo de etiqueta del panel: se alinea con los campos */
.filtros .check-ver { display: flex; align-items: center; gap: 8px; min-height: 38px; padding-left: 0; margin: 0; }
.filtros .check-ver .form-check-input { float: none; margin: 0; width: 1.1em; height: 1.1em; cursor: pointer; }
.filtros .check-ver .form-check-label { background: none; border: 0; padding: 0; margin: 0; width: auto;
    text-transform: none; font-weight: 500; font-size: .9rem; color: #33475b; cursor: pointer; }
#tabla { font-size: .82rem; }
#tabla td { vertical-align: top; }
.valor { max-width: 260px; white-space: pre-wrap; word-break: break-word; max-height: 7.5em; overflow: auto; display: block; }
.valor-ant { color: #a33; }
.valor-nue { color: #1d6b2f; }
.badge-accion { font-size: .72rem; font-weight: 600; padding: 3px 7px; border-radius: 4px; color: #fff; }
.a-CREAR { background: #2e8b57; } .a-EDITAR { background: #d48a00; } .a-ELIMINAR { background: #c0392b; }
.a-LOGIN { background: #3c78b5; } .a-LOGOUT { background: #6c7a89; } .a-LOGIN_FALLIDO { background: #8e44ad; }
.a-VER { background: #95a5a6; } .a-CAMBIO_PASSWORD { background: #16a085; } .a-RESPALDO { background: #34495e; }
.a-VACIAR_LOG { background: #7b241c; }
.zona-peligro { margin-top: 28px; padding: 16px 18px; border: 1px solid #f1c4c0; background: #fdf3f2; border-radius: 8px;
    display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; }
.zona-peligro p { margin: 0; color: #7b241c; font-size: .88rem; }
#respuesta_vaciar { max-width: 200px; margin-top: 8px; }
.link-hist { cursor: pointer; color: #2c6db0; text-decoration: underline dotted; }
.dataTables_wrapper .dataTables_paginate .paginate_button { padding: 4px 9px; }
</style>
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
                    <h5>AUDITORÍA</h5>
                </div>
                <div class="col text-end">
                    <button id="bexport" type="button" class="btn btn-primary"><i class="fa-solid fa-file-excel"></i>&nbsp;Exportar a Excel</button>
                </div>
            </div>

            <div class="filtros mt-3">
                <!-- Fila 1: quién, qué y dónde -->
                <div class="row g-3">
                    <div class="col-12 col-md-3">
                        <label for="f_usuario">Usuario</label>
                        <select id="f_usuario" class="form-select"><option value="">Todos</option></select>
                    </div>
                    <div class="col-12 col-md-3">
                        <label for="f_accion">Acción</label>
                        <select id="f_accion" class="form-select">
                            <option value="">Todas (sin VER)</option>
                            <option value="CREAR">Creación</option>
                            <option value="EDITAR">Edición</option>
                            <option value="ELIMINAR">Eliminación</option>
                            <option value="VER">Pantallas visitadas</option>
                            <option value="LOGIN">Ingreso</option>
                            <option value="LOGIN_FALLIDO">Ingreso fallido</option>
                            <option value="LOGOUT">Salida</option>
                            <option value="CAMBIO_PASSWORD">Cambio de contraseña</option>
                            <option value="RESPALDO">Respaldo</option>
                            <option value="VACIAR_LOG">Vaciado del log</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-3">
                        <label for="f_opcion">Opción</label>
                        <select id="f_opcion" class="form-select"><option value="">Todas</option></select>
                    </div>
                    <div class="col-12 col-md-3">
                        <label for="f_tabla">Tabla</label>
                        <select id="f_tabla" class="form-select"><option value="">Todas</option></select>
                    </div>
                </div>

                <!-- Fila 2: cuándo y sobre qué registro -->
                <div class="row g-3 mt-1">
                    <div class="col-6 col-md-3">
                        <label for="f_desde">Desde</label>
                        <input type="date" id="f_desde" class="form-control">
                    </div>
                    <div class="col-6 col-md-3">
                        <label for="f_hasta">Hasta</label>
                        <input type="date" id="f_hasta" class="form-control">
                    </div>
                    <div class="col-6 col-md-3">
                        <label for="f_registro_id">ID registro</label>
                        <input type="text" id="f_registro_id" class="form-control" value="<?php echo $f_registro_id;?>">
                    </div>
                    <div class="col-6 col-md-3">
                        <label for="f_proyecto_id">ID iniciativa</label>
                        <input type="number" id="f_proyecto_id" class="form-control" value="<?php echo $f_proyecto_id;?>">
                    </div>
                </div>

                <!-- Fila 3: cómo se muestra -->
                <div class="row g-3 mt-1 align-items-end">
                    <div class="col-12 col-md-4">
                        <label for="f_vista">Vista</label>
                        <select id="f_vista" class="form-select">
                            <option value="detalle">Campo por campo</option>
                            <option value="eventos">Solo eventos</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="form-check check-ver">
                            <input class="form-check-input" type="checkbox" id="f_incluir_ver">
                            <label class="form-check-label" for="f_incluir_ver">Incluir pantallas visitadas</label>
                        </div>
                    </div>
                    <div class="col-12 col-md-4 text-md-end">
                        <button id="blimpiar" type="button" class="btn btn-outline-secondary">Limpiar filtros</button>
                    </div>
                </div>
            </div>

            <table id="tabla" class="table display hoverable" style="width: 100%">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Usuario</th>
                        <th>Acción</th>
                        <th>Opción</th>
                        <th>Tabla</th>
                        <th>Registro</th>
                        <th>Campo</th>
                        <th>Valor anterior</th>
                        <th>Valor nuevo</th>
                        <th>Detalle</th>
                        <th>IP</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>

            <!-- Vaciar log -->
            <div class="zona-peligro">
                <p><strong>Vaciar log:</strong> elimina todos los registros de auditoría. Esta acción no se puede deshacer.
                   Se recomienda exportar a Excel antes.</p>
                <button id="bvaciar" type="button" class="btn btn-danger"><i class="fa-solid fa-trash-can"></i>&nbsp;Vaciar log</button>
            </div>

        </div>
    </div>
</div>

<?php include("footer.php");?>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js"></script>
<script src="app.js"></script>

<script>
function esc(s) {
    if (s === null || s === undefined) return '';
    return String(s).replace(/[&<>"']/g, function (c) {
        return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];
    });
}

function filtros() {
    return {
        token:       $("#sid").val(),
        desde:       $("#f_desde").val(),
        hasta:       $("#f_hasta").val(),
        usuario_id:  $("#f_usuario").val(),
        accion:      $("#f_accion").val(),
        tabla:       $("#f_tabla").val(),
        opcion:      $("#f_opcion").val(),
        registro_id: $("#f_registro_id").val(),
        proyecto_id: $("#f_proyecto_id").val(),
        vista:       $("#f_vista").val(),
        incluir_ver: $("#f_incluir_ver").is(':checked') ? 1 : ''
    };
}

$(document).ready(function () {

    var tablaInicial = <?php echo json_encode($f_tabla);?>;

    // Cargar selectores: usuarios, opciones y tablas que existen en el log en este momento
    $.getJSON('api/auditoria.php', { token: $("#sid").val(), task: 'filtros' }, function (r) {
        r.usuarios.forEach(function (u) {
            $('#f_usuario').append($('<option>').val(u.usuario_id).text((u.usuario_nombre || '') + ' (' + (u.usuario_login || '') + ')'));
        });
        r.opciones.forEach(function (o) {
            $('#f_opcion').append($('<option>').val(o).text(o));
        });
        r.tablas.forEach(function (t) {
            $('#f_tabla').append($('<option>').val(t).text(t));
        });
        if (tablaInicial) { $('#f_tabla').val(tablaInicial); tabla.ajax.reload(); }
    });

    var tabla = $('#tabla').DataTable({
        serverSide: true,
        processing: true,
        searching: true,
        ordering: false,
        pageLength: 50,
        lengthMenu: [25, 50, 100, 250],
        dom: '<"top"lfp<"clear">>rt<"bottom"ip<"clear">>',
        ajax: {
            url: 'api/auditoria.php',
            data: function (d) { return $.extend(d, filtros(), { task: 'list' }); }
        },
        columns: [
            { data: 'fecha_hora', render: function (v) { return '<span style="white-space:nowrap">' + esc(v) + '</span>'; } },
            { data: null, render: function (r) {
                return esc(r.usuario_nombre || r.usuario_login || '-') +
                       (r.perfil ? '<br><small class="text-muted">' + esc(r.perfil) + '</small>' : '');
            } },
            { data: 'accion', render: function (v) { return '<span class="badge-accion a-' + esc(v) + '">' + esc(v) + '</span>'; } },
            { data: 'opcion', render: function (v) { return esc(v); } },
            { data: 'tabla',  render: function (v) { return esc(v); } },
            { data: null, render: function (r) {
                if (!r.registro_id) return r.proyecto_id ? 'Iniciativa ' + esc(r.proyecto_id) : '';
                return '<span class="link-hist" title="Ver historial de este registro" data-tabla="' + esc(r.tabla) +
                       '" data-id="' + esc(r.registro_id) + '">#' + esc(r.registro_id) + '</span>' +
                       (r.registro_desc ? '<br><small>' + esc(r.registro_desc) + '</small>' : '');
            } },
            { data: 'campo', render: function (v) { return esc(v); } },
            { data: 'valor_anterior', render: function (v) { return v === null ? '' : '<span class="valor valor-ant">' + esc(v) + '</span>'; } },
            { data: 'valor_nuevo',    render: function (v) { return v === null ? '' : '<span class="valor valor-nue">' + esc(v) + '</span>'; } },
            { data: 'detalle', render: function (v) { return '<span class="valor">' + esc(v) + '</span>'; } },
            { data: 'ip', render: function (v) { return '<small>' + esc(v) + '</small>'; } }
        ],
        language: {
            sProcessing: "<p align='center'><img src='img/ajax-loader.svg' /></p>",
            sLengthMenu: "Mostrar _MENU_",
            sZeroRecords: "No se encontraron resultados",
            sEmptyTable: "Sin registros de auditoría",
            sInfo: "_START_ al _END_ de _TOTAL_",
            sInfoEmpty: "0 registros",
            sInfoFiltered: "",
            sSearch: "",
            searchPlaceholder: "Buscar",
            oPaginate: { sFirst: "<<", sLast: ">>", sNext: ">", sPrevious: "<" }
        }
    });

    $('.filtros').on('change', 'input, select', function () { tabla.ajax.reload(); });

    // Clic en un registro: ver todo su historial
    $('#tabla').on('click', '.link-hist', function () {
        $('#f_tabla').val($(this).data('tabla'));
        $('#f_registro_id').val($(this).data('id'));
        $('#f_accion').val('');
        tabla.ajax.reload();
    });

    $('#blimpiar').on('click', function () {
        $('.filtros input[type=text], .filtros input[type=date], .filtros input[type=number]').val('');
        $('.filtros select').not('#f_vista').val('');
        $('#f_incluir_ver').prop('checked', false);
        tabla.search('').ajax.reload();
    });

    // Vaciar log: hay que responder mes actual * año actual
    $('#bvaciar').on('click', function () {
        // Mes y año del servidor (hora de Chile), los mismos que valida la API
        var mes  = <?php echo (int) (new DateTime('now', new DateTimeZone('America/Santiago')))->format('n');?>;
        var anio = <?php echo (int) (new DateTime('now', new DateTimeZone('America/Santiago')))->format('Y');?>;

        $.confirm({
            title: 'Vaciar log',
            type: 'red',
            boxWidth: '420px',
            useBootstrap: false,
            content: '<p>Se eliminarán <strong>todos</strong> los registros de auditoría. Esta acción no se puede deshacer.</p>' +
                     '<p>Para confirmar, responda: <strong>¿cuánto es ' + mes + ' × ' + anio + '?</strong></p>' +
                     '<input type="text" inputmode="numeric" id="respuesta_vaciar" class="form-control" autocomplete="off">',
            onContentReady: function () { this.$content.find('#respuesta_vaciar').focus(); },
            buttons: {
                cancelar: { text: 'Cancelar' },
                vaciar: {
                    text: 'Vaciar log',
                    btnClass: 'btn-red',
                    action: function () {
                        var resp = this.$content.find('#respuesta_vaciar').val().trim();
                        if (resp === '' || parseInt(resp, 10) !== mes * anio) {
                            $.alert({ title: 'Respuesta incorrecta', content: 'El log no se vació.', useBootstrap: false, boxWidth: '350px' });
                            return;
                        }
                        $.post('api/auditoria.php', { token: $("#sid").val(), task: 'vaciar', respuesta: resp }, function (r) {
                            $.alert({ title: r.ok ? 'Log vaciado' : 'Atención', content: r.mensaje, useBootstrap: false, boxWidth: '350px' });
                            if (r.ok) { tabla.ajax.reload(); }
                        }, 'json').fail(function () {
                            $.alert({ title: 'Error', content: 'No se pudo vaciar el log.', useBootstrap: false, boxWidth: '350px' });
                        });
                    }
                }
            }
        });
    });

    $('#bexport').on('click', function () {
        var p = $.extend(filtros(), { task: 'export', buscar: tabla.search() });
        window.location = 'api/auditoria.php?' + $.param(p);
    });
});
</script>
</body>
</html>
