/* ************************************************************
 * Exportación a Excel del listado de iniciativas (proyectos.php)
 * Formato basado en la Cartera Consolidada SJM y ordenado según
 * las secciones de la ficha (proyectos_editar.php).
 * Requiere ExcelJS 4.x.
 *
 * Uso:
 *   var wb = IniciativasExcel.construir(ExcelJS, filas, detalle, { filtros: [...] });
 *   IniciativasExcel.descargar(wb, 'Iniciativas.xlsx');
 *
 * filas   = filas del listado (JSON de api_proyectos.php?task=list)
 * detalle = JSON de api_proyectos.php?task=export_detalle
 * ************************************************************ */
(function (root) {
'use strict';

var AZUL = 'FF00007B', BLANCO = 'FFFFFFFF', GRIS = 'FF555555', FONDO = 'FFF2F2F2', TOTAL = 'FFEFF1F7';

var FMT = {
    mon:   '_ "$"* #,##0_ ;_ "$"* \\-#,##0_ ;_ "$"* "-"_ ;_ @_ ',
    int:   '0',
    cant:  '#,##0',
    cant2: '#,##0.00',
    pct:   '0.0%',
    coord: '0.000000',
    fecha: 'dd/mm/yyyy hh:mm'
};

var TITULO = 'PLAN MAESTRO DE INVERSIÓN SAN JOSÉ DE MAIPO - CARTERA DE INICIATIVAS';


// ------------------------------------------------------------
// Utilidades
// ------------------------------------------------------------
function txt(v) { return (v === null || v === undefined) ? '' : String(v).trim(); }

function num(v) {
    if (v === null || v === undefined || v === '') return null;
    var n = Number(v);
    return isFinite(n) ? n : null;
}

function siNo(v) {
    var s = txt(v).toLowerCase();
    if (s === 'si' || s === 'sí') return 'Sí';
    if (s === 'no') return 'No';
    return txt(v);
}

// 'AAAA-MM-DD HH:MM:SS' => Date con la hora tal cual (sin desfase de zona horaria)
function fecha(v) {
    var m = /^(\d{4})-(\d{2})-(\d{2})(?:[ T](\d{2}):(\d{2})(?::(\d{2}))?)?/.exec(txt(v));
    if (!m || m[1] === '0000') return null;
    return new Date(Date.UTC(+m[1], +m[2] - 1, +m[3], +(m[4] || 0), +(m[5] || 0), +(m[6] || 0)));
}

function fmtMiles(n) { return Math.round(n || 0).toLocaleString('es-CL'); }

function fmtAhora(d) {
    var z = function (n) { return (n < 10 ? '0' : '') + n; };
    return z(d.getDate()) + '/' + z(d.getMonth() + 1) + '/' + d.getFullYear() + ' ' + z(d.getHours()) + ':' + z(d.getMinutes());
}

function fuente(o) {
    o = o || {};
    return { name: 'Arial', size: o.size || 9, bold: !!o.bold, italic: !!o.italic, color: { argb: o.color || 'FF000000' } };
}

function relleno(argb) { return { type: 'pattern', pattern: 'solid', fgColor: { argb: argb } }; }

function borde(izq, der, sup, inf) {
    var b = function (s) { return { style: s || 'thin', color: { argb: AZUL } }; };
    return { left: b(izq), right: b(der), top: b(sup), bottom: b(inf) };
}

// Alto de fila según el texto más largo (Excel no ajusta solo al abrir)
function altoFila(valores, anchos, minimo) {
    var lineas = 1;
    valores.forEach(function (v, i) {
        if (v === null || v === undefined || v === '') return;
        var s = (v instanceof Date) ? '00/00/0000 00:00' : String(v);
        var porLinea = Math.max(1, Math.floor((anchos[i] || 10) * 1.1));
        var n = 0;
        s.split('\n').forEach(function (p) { n += Math.max(1, Math.ceil(p.length / porLinea)); });
        if (n > lineas) lineas = n;
    });
    return Math.min(409, Math.max(minimo || 15, lineas * 11.5 + 5));
}

function titulo(ws, texto, subtitulo) {
    var c1 = ws.getCell(1, 1);
    c1.value = texto;
    c1.font = fuente({ size: 13, bold: true, color: AZUL });
    ws.getRow(1).height = 16.5;
    if (subtitulo) {
        var c2 = ws.getCell(2, 1);
        c2.value = subtitulo;
        c2.font = fuente({ color: GRIS });
    }
}


// ------------------------------------------------------------
// Escritor genérico de tablas
//   cfg.cols:      [{ key, t, w, tipo, al, bold, sec, v(r), f(n, L, r) }]
//   cfg.filaHead:  fila de encabezados (con secciones, la banda va en filaHead - 1)
//   cfg.total:     { label, labelKey, keys: [...] }
//   cfg.xSplit:    columnas fijas
// ------------------------------------------------------------
function escribirTabla(ws, cfg) {
    var cols = cfg.cols, n = cols.length, fh = cfg.filaHead, f0 = fh + 1;
    var filas = cfg.filas, ultima = fh + filas.length;
    var filaTotal = (cfg.total && filas.length) ? ultima + 1 : null;
    var anchos = cols.map(function (c) { return c.w || 14; });

    var idx = {};
    cols.forEach(function (c, i) { idx[c.key] = i + 1; ws.getColumn(i + 1).width = anchos[i]; });
    var L = function (key) { return ws.getColumn(idx[key]).letter; };

    // Primera columna de cada sección (lleva borde grueso a la izquierda)
    var inicioSec = {};
    if (cfg.secciones) {
        var i = 0;
        while (i < n) {
            var j = i;
            while (j + 1 < n && cols[j + 1].sec === cols[i].sec) j++;
            if (j > i) ws.mergeCells(fh - 1, i + 1, fh - 1, j + 1);
            for (var k = i; k <= j; k++) {
                var cb = ws.getCell(fh - 1, k + 1);
                cb.border = {
                    top:    { style: 'medium', color: { argb: AZUL } },
                    bottom: { style: 'thin',   color: { argb: AZUL } },
                    left:   k === i ? { style: 'medium', color: { argb: AZUL } } : undefined,
                    right:  k === j ? { style: 'medium', color: { argb: AZUL } } : undefined
                };
            }
            var cs = ws.getCell(fh - 1, i + 1);
            cs.value = cols[i].sec;
            cs.font = fuente({ size: 11, bold: true, color: AZUL });
            cs.alignment = { horizontal: 'center', vertical: 'middle' };
            inicioSec[i] = true;
            i = j + 1;
        }
        ws.getRow(fh - 1).height = 27.75;
    }

    var bordeCelda = function (i, sup, inf) {
        return borde(inicioSec[i] || i === 0 ? 'medium' : 'thin', i === n - 1 ? 'medium' : 'thin', sup, inf);
    };

    // Encabezados
    cols.forEach(function (c, i) {
        var cell = ws.getCell(fh, i + 1);
        cell.value = c.t;
        cell.font = fuente({ bold: true, color: BLANCO });
        cell.fill = relleno(AZUL);
        cell.alignment = { horizontal: 'center', vertical: 'middle', wrapText: true };
        cell.border = bordeCelda(i);
    });
    ws.getRow(fh).height = Math.max(31.5, altoFila(cols.map(function (c) { return c.t; }), anchos));

    // Datos
    filas.forEach(function (r, p) {
        var fila = f0 + p;
        var valores = [];
        cols.forEach(function (c, i) {
            var cell = ws.getCell(fila, i + 1);
            var v = c.f ? c.f(fila, L, r, filaTotal) : (c.v ? c.v(r) : null);
            if (v === '' || v === undefined) v = null;
            cell.value = v;
            valores.push(v && v.formula !== undefined ? '' : v);

            var fmt = FMT[c.tipo];
            if (c.tipo === 'cant' && typeof v === 'number' && v % 1 !== 0) fmt = FMT.cant2;
            if (fmt) cell.numFmt = fmt;
            cell.font = fuente({ bold: c.bold });
            cell.fill = relleno(c.fondo || FONDO);
            cell.alignment = { horizontal: c.al || 'left', vertical: 'middle', wrapText: true };
            cell.border = bordeCelda(i, null, p === filas.length - 1 ? 'medium' : 'thin');
        });
        ws.getRow(fila).height = altoFila(valores, anchos, cfg.altoMin || 24);
    });

    if (!filas.length) {
        var cv = ws.getCell(f0, 1);
        cv.value = 'Sin registros para las iniciativas exportadas.';
        cv.font = fuente({ color: GRIS });
    }

    // Totales (SUBTOTAL: si se filtra en Excel, suma solo lo visible)
    if (filaTotal) {
        var ct = ws.getCell(filaTotal, idx[cfg.total.labelKey]);
        ct.value = cfg.total.label;
        ct.font = fuente({ bold: true });
        ct.alignment = { horizontal: 'right', vertical: 'middle' };
        cfg.total.keys.forEach(function (key) {
            var col = cols[idx[key] - 1];
            var suma = 0;
            for (var f = f0; f <= ultima; f++) {
                var x = ws.getCell(f, idx[key]).value;
                suma += (x && typeof x === 'object') ? (Number(x.result) || 0) : (Number(x) || 0);
            }
            var cell = ws.getCell(filaTotal, idx[key]);
            cell.value = { formula: 'SUBTOTAL(109,' + L(key) + f0 + ':' + L(key) + ultima + ')', result: suma };
            cell.numFmt = FMT[col.tipo] || FMT.mon;
            cell.font = fuente({ bold: true });
            cell.fill = relleno(TOTAL);
            cell.alignment = { horizontal: col.al || 'center', vertical: 'middle' };
            cell.border = borde('thin', 'thin', 'medium', 'medium');
        });
        ws.getRow(filaTotal).height = 22.5;
    }

    ws.views = [{ state: 'frozen', xSplit: cfg.xSplit || 0, ySplit: fh, topLeftCell: ws.getColumn((cfg.xSplit || 0) + 1).letter + f0, zoomScale: 90 }];
    ws.autoFilter = { from: { row: fh, column: 1 }, to: { row: Math.max(fh, ultima), column: n } };
    ws.pageSetup = { orientation: 'landscape', paperSize: 9, fitToPage: true, fitToWidth: 1, fitToHeight: 0, printTitlesRow: (cfg.secciones ? fh - 1 : fh) + ':' + fh };

    return { L: L, f0: f0, ultima: ultima, filaTotal: filaTotal, siguiente: (filaTotal || Math.max(ultima, f0)) + 2 };
}


// ------------------------------------------------------------
// Columnas de la hoja principal (mismo orden y nombres que la ficha)
// ------------------------------------------------------------
function columnasIniciativas(ctx) {
    var C = [];
    function sec(nombre, lista) { lista.forEach(function (c) { c.sec = nombre; C.push(c); }); }
    function sumaCols(n, L, keys) { return 'SUM(' + L(keys[0]) + n + ':' + L(keys[keys.length - 1]) + n + ')'; }

    sec('INICIATIVA', [
        { key: 'numero',    t: 'N°',                               w: 6.4,  tipo: 'int', al: 'center', v: function (r) { return num(r.numero); } },
        { key: 'codigo',    t: 'CÓDIGO',                           w: 10,   al: 'center', bold: true, v: function (r) { return txt(r.codigo); } },
        { key: 'nombre',    t: 'NOMBRE DE LA INICIATIVA',          w: 34,   v: function (r) { return txt(r.nombre_iniciativa); } },
        { key: 'idi',       t: 'IDI / BIP',                        w: 12.7, al: 'center', v: function (r) { return txt(r.codigo_idi); } },
        { key: 'pm',        t: 'PRESELECCIONADA PLAN MAESTRO SJM', w: 16,   al: 'center', bold: true, v: function (r) { return siNo(r.seleccionada_pm_sjm); } },
        { key: 'tipo_inv',  t: 'TIPO DE INVERSIÓN',                w: 18,   v: function (r) { return txt(r.tipo_de_inversion); } }
    ]);

    sec('DESCRIPCIÓN', [
        { key: 'instrumento', t: 'INSTRUMENTO DE ORIGEN',            w: 25.7, v: function (r) { return txt(r.instrumento_de_origen); } },
        { key: 'tipologia',   t: 'TIPOLOGÍA DE INICIATIVA',          w: 12.7, al: 'center', v: function (r) { return txt(r.tipo); } },
        { key: 'descripcion', t: 'DESCRIPCIÓN',                      w: 72,   v: function (r) { return txt(r.descripcion); } },
        { key: 'eje',         t: 'EJE ESTRATÉGICO',                  w: 20,   v: function (r) { return txt(r.eje_estrategico); } },
        { key: 'objetivo',    t: 'OBJETIVO ESTRATÉGICO',             w: 25.7, v: function (r) { return txt(r.objetivo_estrategico); } },
        { key: 'utr',         t: 'UNIDAD TÉCNICA RESPONSABLE (UTR)', w: 20,   v: function (r) { return txt(r.utr); } },
        { key: 'sector',      t: 'SECTOR SNI',                       w: 15,   v: function (r) { return txt(r.sector); } },
        { key: 'subsector',   t: 'SUBSECTOR SNI',                    w: 17,   v: function (r) { return txt(r.subsector_de_inversion_sni); } }
    ]);

    sec('BRECHAS', [
        { key: 'brecha',    t: 'BRECHA PRINCIPAL',         w: 25.7, v: function (r) { return txt(r.brecha_principal); } },
        { key: 'atributo',  t: 'ATRIBUTO AL QUE RESPONDE', w: 18,   v: function (r) { return txt(r.atributo_al_que_responde); } },
        { key: 'brecha_es', t: 'BRECHA ESPECÍFICA',        w: 25.7, v: function (r) { return txt(r.brecha_especifica); } }
    ]);

    sec('EMPLAZAMIENTO', [
        { key: 'alcance',     t: 'ALCANCE',                  w: 12.7, al: 'center', bold: true, v: function (r) { return txt(r.localizacion); } },
        { key: 'localizacion',t: 'LOCALIZACIÓN',             w: 22,   v: function (r) { return txt(r.localizacion_2); } },
        { key: 'localidades', t: 'LOCALIDADES CUBIERTAS',    w: 30,   v: function (r) { return ctx.locTexto(r); } },
        { key: 'n_loc',       t: 'N° LOCALIDADES',           w: 9,    tipo: 'int', al: 'center', v: function (r) { return ctx.locCuenta(r) || null; } },
        { key: 'lat',         t: 'LATITUD',                  w: 11.5, tipo: 'coord', al: 'center', v: function (r) { return num(r.point_x); } },
        { key: 'lng',         t: 'LONGITUD',                 w: 11.5, tipo: 'coord', al: 'center', v: function (r) { return num(r.point_y); } },
        { key: 'n_ubic',      t: 'N° UBICACIONES ADICIONALES', w: 11, tipo: 'int', al: 'center', v: function (r) { return num(r.n_ubicaciones) || null; } },
        { key: 'ubic',        t: 'UBICACIONES ADICIONALES',  w: 30,   v: function (r) { return txt(r.ubicaciones_adicionales).split('; ').join('\n'); } }
    ]);

    sec('FINANCIAMIENTO', [
        // Meta física
        { key: 'indicador',  t: 'INDICADOR',           w: 20,   v: function (r) { return txt(r.indicador_2); } },
        { key: 'unidad',     t: 'UNIDAD',              w: 12.7, al: 'center', v: function (r) { return txt(r.unidad_de_medida); } },
        { key: 'cant_proy',  t: 'CANTIDAD PROYECTADA', w: 11,   tipo: 'cant', al: 'center', v: function (r) { return num(r.cantidad_proyectada); } },
        { key: 'cant_real',  t: 'CANTIDAD REALIZADA',  w: 11,   tipo: 'cant', al: 'center', v: function (r) { return num(r.cantidad_realizada); } },
        { key: 'meta_pct',   t: 'AVANCE META FÍSICA',  w: 10,   tipo: 'pct', al: 'center', bold: true,
          f: function (n, L, r) {
              var p = num(r.cantidad_proyectada), q = num(r.cantidad_realizada) || 0;
              return { formula: 'IFERROR(' + L('cant_real') + n + '/' + L('cant_proy') + n + ',"")', result: p ? q / p : '' };
          } },
        // Costos
        { key: 'c_pref',  t: 'M$\nPrefactibilidad', w: 14, tipo: 'mon', al: 'center', v: function (r) { return num(r.costos_perfil_prefactibilidad); } },
        { key: 'c_dis',   t: 'M$\nDiseño',           w: 14, tipo: 'mon', al: 'center', v: function (r) { return num(r.costos_diseno); } },
        { key: 'c_eje',   t: 'M$\nEjecución',        w: 15, tipo: 'mon', al: 'center', v: function (r) { return num(r.costos_ejecucion); } },
        { key: 'c_total', t: 'M$\nTOTAL',            w: 15, tipo: 'mon', al: 'center', bold: true,
          f: function (n, L, r) {
              return { formula: sumaCols(n, L, ['c_pref', 'c_eje']), result: ctx.costoTotal(r) };
          } },
        { key: 'c_pct',   t: '%\nde la cartera',     w: 9,  tipo: 'pct', al: 'center',
          f: function (n, L, r, T) {
              var g = ctx.granTotal;
              return { formula: 'IFERROR(' + L('c_total') + n + '/$' + L('c_total') + '$' + T + ',0)', result: g ? ctx.costoTotal(r) / g : 0 };
          } },
        { key: 'c_unit',  t: 'VALOR UNIDAD EJECUCIÓN M$',   w: 13,   tipo: 'mon', al: 'center', v: function (r) { return num(r.costo_unitario_ejecucion); } },
        { key: 'c_origen',t: 'ORIGEN DEL CÁLCULO DE COSTOS', w: 20,  v: function (r) { return txt(r.origen_del_calculo_de_costos); } },
        // Fuentes de financiamiento por etapa (tabla financiamiento)
        { key: 'fin_txt', t: 'FUENTES DE FINANCIAMIENTO POR ETAPA', w: 30, v: function (r) { return ctx.porEtapaTexto(ctx.fin, r.id); } },
        { key: 'fin_tot', t: 'M$ FINANCIAMIENTO\nTOTAL',            w: 15, tipo: 'mon', al: 'center', v: function (r) { return ctx.porEtapaTotal(ctx.fin, r.id); } },
        // Gasto efectivo
        { key: 'g_pref',  t: 'GASTO\nPrefactibilidad', w: 14, tipo: 'mon', al: 'center', v: function (r) { return num(r.gasto_perfil_prefactibilidad); } },
        { key: 'g_dis',   t: 'GASTO\nDiseño',           w: 14, tipo: 'mon', al: 'center', v: function (r) { return num(r.gasto_diseno); } },
        { key: 'g_eje',   t: 'GASTO\nEjecución',        w: 14, tipo: 'mon', al: 'center', v: function (r) { return num(r.gasto_ejecucion); } },
        { key: 'g_total', t: 'GASTO\nTOTAL',            w: 15, tipo: 'mon', al: 'center', bold: true,
          f: function (n, L, r) {
              var s = (num(r.gasto_perfil_prefactibilidad) || 0) + (num(r.gasto_diseno) || 0) + (num(r.gasto_ejecucion) || 0);
              return { formula: sumaCols(n, L, ['g_pref', 'g_eje']), result: s };
          } },
        { key: 'gas_txt', t: 'GASTO POR ETAPA', w: 30, v: function (r) { return ctx.porEtapaTexto(ctx.gas, r.id); } }
    ]);

    sec('MANTENCIÓN', [
        { key: 'm_resp', t: 'RESPONSABLE',    w: 24, v: function (r) { return txt(r.responsable_del_mantenimiento); } },
        { key: 'm_fin',  t: 'FINANCIAMIENTO', w: 24, v: function (r) { return txt(r.financiamiento_del_mantenimiento); } }
    ]);

    sec('CRONOGRAMA (AÑOS)', [
        { key: 'inicio',  t: 'INICIO',                    w: 9,  tipo: 'int', al: 'center', v: function (r) { return num(r.inicio); } },
        { key: 'termino', t: 'TÉRMINO',                   w: 9,  tipo: 'int', al: 'center', v: function (r) { return num(r.termino); } },
        { key: 'plazo',   t: 'PLAZO DE EJECUCIÓN (AÑOS)', w: 11, tipo: 'int', al: 'center', v: function (r) { return num(r.plazos_ejecucion_anos); } },
        { key: 'anos',    t: 'TOTAL',                     w: 8,  tipo: 'int', al: 'center', bold: true,
          f: function (n, L, r) {
              var a = num(r.inicio), b = num(r.termino);
              var ini = L('inicio') + n, ter = L('termino') + n;
              return { formula: 'IF(AND(ISNUMBER(' + ini + '),ISNUMBER(' + ter + ')),' + ter + '-' + ini + ',"")', result: (a !== null && b !== null) ? b - a : '' };
          } }
    ]);

    sec('ESTADO', [
        { key: 'etapa',    t: 'ETAPA ACTUAL',               w: 18, v: function (r) { return txt(r.etapa_actual); } }
    ]);

    sec('REGISTRO', [
        { key: 'creado',  t: 'FECHA CREACIÓN',       w: 15, tipo: 'fecha', al: 'center', v: function (r) { return fecha(r.proyectos_created); } },
        { key: 'editado', t: 'ÚLTIMA ACTUALIZACIÓN', w: 15, tipo: 'fecha', al: 'center', v: function (r) { return fecha(r.proyectos_updated); } },
        { key: 'usuario', t: 'MODIFICADO POR',       w: 20, v: function (r) { return txt(r.proyectos_user); } }
    ]);

    return C;
}


// ------------------------------------------------------------
// Contexto: localidades y detalle por etapa agrupados por iniciativa
// ------------------------------------------------------------
function crearContexto(filas, detalle) {
    detalle = detalle || {};
    var LOC = detalle.localidades || {};
    var ids = {};
    filas.forEach(function (r, i) { ids[String(r.id)] = i; });

    function agrupar(lista) {
        var g = {};
        (lista || []).forEach(function (x) {
            var k = String(x.proyecto_id);
            if (!(k in ids)) return;
            (g[k] = g[k] || []).push(x);
        });
        return g;
    }

    var ctx = {
        LOC: LOC,
        ids: ids,
        fin: agrupar(detalle.financiamiento),
        gas: agrupar(detalle.gasto),
        locMarcadas: function (r) {
            return Object.keys(LOC).filter(function (c) { return String(r[c]) === '1'; });
        },
        locTexto: function (r) {
            var m = ctx.locMarcadas(r);
            if (m.length) return m.map(function (c) { return LOC[c]; }).join('; ');
            return txt(r.localidades).split(', ').join('; ');
        },
        locCuenta: function (r) {
            var m = ctx.locMarcadas(r).length;
            return m || (txt(r.localidades) ? txt(r.localidades).split(', ').length : 0);
        },
        costoTotal: function (r) {
            return (num(r.costos_perfil_prefactibilidad) || 0) + (num(r.costos_diseno) || 0) + (num(r.costos_ejecucion) || 0);
        },
        porEtapaTexto: function (g, id) {
            return (g[String(id)] || []).map(function (x) {
                return (txt(x.etapa_txt) || 'Etapa ' + x.etapa) + ': ' + txt(x.fuente) + ' (M$ ' + fmtMiles(num(x.monto)) + ')';
            }).join('\n');
        },
        porEtapaTotal: function (g, id) {
            var l = g[String(id)];
            if (!l || !l.length) return null;
            return l.reduce(function (s, x) { return s + (num(x.monto) || 0); }, 0);
        }
    };
    ctx.granTotal = filas.reduce(function (s, r) { return s + ctx.costoTotal(r); }, 0);
    return ctx;
}

// Filas de detalle en el mismo orden que las iniciativas exportadas
// ------------------------------------------------------------
// Hojas
// ------------------------------------------------------------
function hojaIniciativas(wb, filas, ctx, info) {
    var ws = wb.addWorksheet('Iniciativas', { properties: { tabColor: { argb: AZUL } } });
    var sub = 'Exportado el ' + fmtAhora(info.fecha) + ' · ' + filas.length + ' iniciativa' + (filas.length === 1 ? '' : 's') +
              ' · ' + (info.filtros.length ? 'Filtros: ' + info.filtros.join(' · ') : 'Sin filtros') + ' · Montos en miles de pesos (M$).';
    titulo(ws, TITULO, sub);

    var cols = columnasIniciativas(ctx);
    var res = escribirTabla(ws, {
        cols: cols, filas: filas, filaHead: 5, secciones: true, xSplit: 3, altoMin: 30,
        total: {
            label: 'TOTAL CARTERA (' + filas.length + ' iniciativa' + (filas.length === 1 ? '' : 's') + ')',
            labelKey: 'nombre',
            keys: ['c_pref', 'c_dis', 'c_eje', 'c_total', 'c_pct', 'fin_tot', 'g_pref', 'g_dis', 'g_eje', 'g_total']
        }
    });

    return res;
}

// ------------------------------------------------------------
// API pública
// ------------------------------------------------------------
function construir(ExcelJS, filas, detalle, info) {
    info = info || {};
    info.fecha = info.fecha || new Date();
    info.filtros = info.filtros || [];

    var wb = new ExcelJS.Workbook();
    wb.creator = 'Plan Maestro SJM';
    wb.created = info.fecha;
    wb.calcProperties.fullCalcOnLoad = true;

    var ctx = crearContexto(filas, detalle);
    hojaIniciativas(wb, filas, ctx, info);
    return wb;
}

function descargar(wb, nombreArchivo) {
    return wb.xlsx.writeBuffer().then(function (buf) {
        var blob = new Blob([buf], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
        var url = URL.createObjectURL(blob);
        var a = document.createElement('a');
        a.href = url;
        a.download = nombreArchivo;
        document.body.appendChild(a);
        a.click();
        setTimeout(function () { URL.revokeObjectURL(url); a.remove(); }, 1000);
    });
}

var api = { construir: construir, descargar: descargar };
if (typeof module !== 'undefined' && module.exports) module.exports = api;
else root.IniciativasExcel = api;

})(typeof window !== 'undefined' ? window : this);
