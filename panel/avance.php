<?php 
// ************************************************************
// Autor: Marcelo Jiménez
// Fulltrust Software
// https://www.fulltrust.net
// Fecha última actualización: 2025-10-20
// Versión Mejorada - Tabla Compacta y Visual - CORREGIDA
// ************************************************************
?>
<?php
@session_start();
include("../conf/db.php");		
include('functions.php');

$sql = "set names utf8";
if(!$tabla = $db->query($sql)){
    die('Hay un error [' . $db->error . ']');
}   

include("inc_tabla.php");

// Obtener las etapas dinámicamente
$sql="SELECT etapas_id, etapas_descripcion FROM etapas ORDER BY etapas_id";
if(!$etapas_result = $db->query($sql)){
    die('Hay un error [' . $db->error . ']');
}
$etapas_headers = [];
while($row_e = $etapas_result->fetch_assoc()) {
    $etapas_headers[$row_e['etapas_id']] = $row_e['etapas_descripcion'];
}
$num_etapas = count($etapas_headers);
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">	
<title><?php echo $row_param["parametros_titulo"];?></title>

<link rel="icon" type="image/png" href="../favicon.png">

<script src="js/jquery-3.5.1.min.js"></script>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<link rel="stylesheet" type="text/css" href="css/styles.css"/>		
<style>
:root {
  --main: #018837;
  --primary: #4e7dbb;
  --secondary: #40a629;
  --accent: #fe5311;
  --light: #f1faee;
  --border: #e0e0e0;
}

body {
    background-color: #f8f9fa;
}

/* Contenedor de la tabla */
.table-wrapper {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.08);
    overflow: hidden;
    margin-bottom: 20px;
}

/* Estilos mejorados de tabla */
#data-table {
    font-size: 13px;
    margin-bottom: 0;
    border-collapse: separate;
    border-spacing: 0;
    width: 100%;
}

#data-table thead th {
    background: var(--primary);
    color: white;
    font-weight: 600;
    padding: 14px 8px;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border: none;
    position: sticky;
    top: 0;
    z-index: 10;
    text-align: center;
    vertical-align: middle;
}

#data-table thead th.sector-col {
    text-align: left;
    min-width: 150px;
}

#data-table thead th.tipo-col {
    text-align: left;
    min-width: 120px;
}

#data-table tbody td {
    padding: 10px 8px;
    border-bottom: 1px solid var(--border);
    vertical-align: middle;
}

#data-table tbody tr {
    transition: background-color 0.15s ease;
}

#data-table tbody tr:hover {
    background-color: #f8f9fa;
}

#data-table tbody tr:last-child td {
    border-bottom: none;
}

/* Columnas de sector y tipo */
.sector-cell {
    font-weight: 600;
    color: var(--main);
    border-right: 2px solid var(--secondary);
}

.tipo-cell {
    color: #666;
    font-size: 12px;
    border-right: 2px solid var(--secondary);
}

/* Celdas de datos con barras visuales */
.data-cell {
    text-align: center;
    padding: 8px 6px !important;
}

.data-cell-value {
    font-weight: 700;
    color: var(--main);
    font-size: 14px;
    display: block;
    margin-bottom: 3px;
}

.data-cell-percent {
    font-size: 11px;
    color: #666;
    display: block;
}

.progress-mini {
    height: 4px;
    background: #e9ecef;
    border-radius: 2px;
    overflow: hidden;
    margin-top: 4px;
}

.progress-mini-bar {
    height: 100%;
    background: linear-gradient(90deg, var(--primary), var(--secondary));
    border-radius: 2px;
    transition: width 0.3s ease;
}

/* Columnas de etapa alternadas */
.etapa-col-1 { background-color: #ffffff; }
.etapa-col-2 { background-color: #fafbfc; }

/* Separadores entre columnas */
.etapa-separator {
    border-right: 2px solid var(--border);
}

/* Fila de totales */
#data-table tfoot td {
    background: var(--primary);
    color: white;
    font-weight: 700;
    padding: 14px 8px;
    border: none;
    text-align: center;
    font-size: 13px;
}

#data-table tfoot td:first-child {
    text-align: left;
}

/* Total general destacado */
.total-general {
    background: var(--main) !important;
    font-size: 15px !important;
}

/* Filtros mejorados */
.filters-bar {
    background: white;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.08);
}

.filter-group {
    display: flex;
    gap: 10px;
    align-items: end;
    flex-wrap: wrap;
}

.filter-item {
    flex: 0 1 200px;
}

.filter-item label {
    all: unset;
    display: block;
    font-size: 12px;
    font-weight: 600;
    color: var(--main);
    margin-bottom: 6px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.filter-item select {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid var(--border);
    border-radius: 6px;
    font-size: 13px;
    transition: border-color 0.2s ease;
}

.filter-item select:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(69, 123, 157, 0.1);
}

/* Botones */
.btn-custom {
    padding: 8px 20px;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 600;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.btn-primary-custom {
    background: var(--primary);
    color: white;
}

.btn-primary-custom:hover {
    background: var(--main);
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(69, 123, 157, 0.3);
}

.btn-secondary-custom {
    background: white;
    color: var(--main);
    border: 2px solid var(--border);
}

.btn-secondary-custom:hover {
    border-color: var(--primary);
    color: var(--primary);
}

/* Estado vacío */
.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #6c757d;
}

.empty-state i {
    font-size: 3rem;
    color: var(--border);
    margin-bottom: 15px;
    display: block;
}

/* Resumen de totales arriba */
.summary-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    gap: 15px;
    margin-bottom: 20px;
}

.summary-card {
    background: white;
    border-radius: 8px;
    padding: 15px;
    text-align: center;
    box-shadow: 0 2px 4px rgba(0,0,0,0.08);
    border-left: 4px solid var(--primary);
}

.summary-card.total-card {
    border-left-color: var(--main);
    background: rgba(78, 125, 187, 0.1);
}

.summary-value {
    font-size: 28px;
    font-weight: 700;
    color: var(--main);
    display: block;
    line-height: 1;
}

.summary-label {
    font-size: 11px;
    color: #666;
    text-transform: uppercase;
    margin-top: 6px;
    display: block;
    letter-spacing: 0.5px;
}

/* Responsive */
@media (max-width: 768px) {
    #data-table {
        font-size: 11px;
    }
    
    #data-table thead th {
        padding: 10px 5px;
        font-size: 10px;
    }
    
    #data-table tbody td {
        padding: 8px 5px;
    }
    
    .filter-group {
        flex-direction: column;
    }
    
    .filter-item {
        flex: 1 1 100%;
    }
    
    .summary-cards {
        grid-template-columns: repeat(2, 1fr);
    }
}

/* Para impresión */
@media print {
    .filters-bar,
    .btn-custom,
    .top-info-div .text-end {
        display: none !important;
    }
    
    .table-wrapper {
        box-shadow: none;
        border: 1px solid var(--border);
    }
    
    #data-table thead th {
        position: static;
    }
}

#pprint {
    margin: 0;
}
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
                    <h5><i class="fas fa-chart-line"></i> ESTADO DE AVANCE DE INICIATIVAS</h5>
                </div>
                <div class="col text-end">
                    <button onclick="exportToExcel();" type="button" class="btn-custom btn-secondary-custom">
                        <i class="fas fa-file-excel"></i> Exportar a Excel
                    </button>
                </div>                        
            </div>

            <div class="container-fluid">

                <!-- Filtros -->
                <div class="filters-bar">
                    <form method="get" target="_self">
                        <div class="filter-group">
                            <div class="filter-item">
                                <label for="sector">Sector</label>
                                <select class="form-select" name="sector" id="sector">
                                    <option value="">Todos los sectores</option>
                                    <?php if(isset($row_sectores) && $row_sectores) { 
                                        $sectores->data_seek(0);
                                        $row_sectores = $sectores->fetch_assoc();
                                    ?>
                                    <?php do { ?>
                                    <option <?php if(isset($_GET["sector"]) && $row_sectores["sector"]==$_GET["sector"]){ echo "selected";};?> value="<?php echo $row_sectores["sector"];?>"><?php echo $row_sectores["sector"];?></option>
                                    <?php } while ( $row_sectores = $sectores->fetch_assoc() ); ?>
                                    <?php } ?>
                                </select>
                            </div>

                            <div class="filter-item">
                                <label for="tipo">Tipo</label>
                                <select class="form-select" name="tipo" id="tipo">
                                    <option value="">Todos los tipos</option>
                                    <?php if(isset($row_tipos) && $row_tipos) { 
                                        $tipos->data_seek(0);
                                        $row_tipos = $tipos->fetch_assoc();
                                    ?>
                                    <?php do { ?>
                                    <option <?php if(isset($_GET["tipo"]) && $row_tipos["tipo"]==$_GET["tipo"]){ echo "selected";};?> value="<?php echo $row_tipos["tipo"];?>"><?php echo $row_tipos["tipo"];?></option>
                                    <?php } while ( $row_tipos = $tipos->fetch_assoc() ); ?>
                                    <?php } ?>
                                </select>
                            </div>

                            <div style="flex: 0 1 auto;">
                                <button type="submit" class="btn-custom btn-primary-custom">
                                    <i class="fas fa-filter"></i> Aplicar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

<?php
// Obtener datos
$sql = "SELECT * FROM tabla ORDER BY area, tipo";
if(!$tabla = $db->query($sql)){
    die('Hay un error [' . $db->error . ']');
}   
$row_tabla = $tabla->fetch_assoc();

// Calcular totales generales - usar el número dinámico de etapas
$totales = [];
foreach($etapas_headers as $etapa_id => $etapa_nombre) {
    $totales[$etapa_id] = 0;
}
$total_proyectos = 0;

if($row_tabla) {
    $tabla->data_seek(0);
    while($row = $tabla->fetch_assoc()) {
        foreach($etapas_headers as $etapa_id => $etapa_nombre) {
            if(isset($row["e".$etapa_id."v"])) {
                $totales[$etapa_id] += $row["e".$etapa_id."v"];
            }
        }
        $total_proyectos += $row["tt"];
    }
    $tabla->data_seek(0);
    $row_tabla = $tabla->fetch_assoc();
}
?>

                <?php if($row_tabla) { ?>
                
                <!-- Resumen rápido -->
                <div class="summary-cards">
                    <div class="summary-card total-card">
                        <span class="summary-value"><?php echo number_format($total_proyectos, 0);?></span>
                        <span class="summary-label">Total Proyectos</span>
                    </div>
                    <?php foreach($etapas_headers as $etapa_id => $etapa_nombre) { 
                        if($totales[$etapa_id] > 0) {
                    ?>
                    <div class="summary-card">
                        <span class="summary-value"><?php echo number_format($totales[$etapa_id], 0);?></span>
                        <span class="summary-label"><?php echo $etapa_nombre;?></span>
                    </div>
                    <?php 
                        }
                    } ?>
                </div>

                <div id="pprint">
                <div class="table-wrapper">
                <table id="data-table">
                    <thead>
                        <tr>
                            <th class="sector-col">Sector</th>
                            <th class="tipo-col">Tipo</th>
                            <?php foreach($etapas_headers as $etapa_id => $etapa_nombre) { ?>
                            <th><?php echo $etapa_nombre;?></th>
                            <?php } ?>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php 
                    $num = 0;
                    do { 
                        $num++;
                    ?>
                        <tr>
                            <td class="sector-cell"><?php echo $row_tabla["area"];?></td>
                            <td class="tipo-cell"><?php echo $row_tabla["tipo"];?></td>
                            
                            <?php 
                            $col_num = 0;
                            foreach($etapas_headers as $etapa_id => $etapa_nombre) {
                                $col_num++;
                                $col_class = ($col_num % 2 == 0) ? 'etapa-col-2' : 'etapa-col-1';
                                $separator_class = ($col_num < count($etapas_headers)) ? 'etapa-separator' : '';
                            ?>
                                <td class="data-cell <?php echo $col_class;?> <?php echo $separator_class;?>">
                                    <?php if (isset($row_tabla["e".$etapa_id."v"]) && $row_tabla["e".$etapa_id."v"] > 0){ ?>
                                        <span class="data-cell-value"><?php echo number_format($row_tabla["e".$etapa_id."v"], 0);?></span>
                                        <span class="data-cell-percent"><?php echo number_format($row_tabla["e".$etapa_id."p"], 1);?>%</span>
                                        <div class="progress-mini">
                                            <div class="progress-mini-bar" style="width: <?php echo $row_tabla["e".$etapa_id."p"];?>%"></div>
                                        </div>
                                    <?php } else { ?>
                                        <span style="color:#ccc;">—</span>
                                    <?php } ?>
                                </td>
                            <?php } ?>

                            <td class="data-cell">
                                <span class="data-cell-value"><?php echo number_format($row_tabla["tt"], 0);?></span>
                                <span class="data-cell-percent"><?php echo number_format($row_tabla["tp"], 2);?>%</span>
                            </td>
                        </tr>
                    <?php } while( $row_tabla = $tabla->fetch_assoc() ); ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2">TOTALES</td>
                            <?php 
                            $total_general = array_sum($totales);
                            foreach($etapas_headers as $etapa_id => $etapa_nombre) {
                                $porcentaje = ($total_general > 0) ? ($totales[$etapa_id] / $total_general) * 100 : 0;
                            ?>
                                <td>
                                    <?php echo number_format($totales[$etapa_id], 0);?>
                                    <br>
                                    <span style="font-size: 11px;"><?php echo number_format($porcentaje, 2);?>%</span>
                                </td>
                            <?php } ?>
                            <td class="total-general">
                                <?php echo number_format($total_general, 0);?>
                                <br>
                                <span style="font-size: 11px;">100%</span>
                            </td>
                        </tr>
                    </tfoot>
                </table>
                </div>
                </div>

                <?php } else { ?>
                    <div class="table-wrapper">
                        <div class="empty-state">
                            <i class="fas fa-inbox"></i>
                            <h5>No hay datos para mostrar</h5>
                            <p>No se encontraron proyectos con los filtros seleccionados</p>
                        </div>
                    </div>
                <?php } ?>

            </div>


        </div>
    </div>
</div>

<?php include("footer.php");?>

    </div>
</div>

<script>
// Función para exportar a Excel
function exportToExcel() {
    try {
        const wb = XLSX.utils.book_new();
        const data = [];
        
        // Encabezados
        const headers = ['Sector', 'Tipo'];
        
        <?php 
        foreach($etapas_headers as $etapa_id => $etapa_nombre) {
            echo "headers.push('".addslashes($etapa_nombre)."');\n";
        }
        ?>
        
        headers.push('Total');
        data.push(headers);
        
        // Datos de las filas
        <?php 
        $sql_export = "SELECT * FROM tabla ORDER BY area, tipo";
        if(!$tabla_export = $db->query($sql_export)){
            die('Hay un error [' . $db->error . ']');
        }
        
        if($tabla_export->num_rows > 0) {
            while($row_export = $tabla_export->fetch_assoc()) {
                echo "data.push([\n";
                echo "  '".addslashes($row_export["area"])."',\n";
                echo "  '".addslashes($row_export["tipo"])."',\n";
                
                foreach($etapas_headers as $etapa_id => $etapa_nombre) {
                    $valor = isset($row_export["e".$etapa_id."v"]) && $row_export["e".$etapa_id."v"] > 0 ? $row_export["e".$etapa_id."v"] : 0;
                    $porcentaje = isset($row_export["e".$etapa_id."p"]) && $row_export["e".$etapa_id."p"] > 0 ? number_format($row_export["e".$etapa_id."p"], 2, '.', '') : 0;
                    echo "  '".$valor." (".$porcentaje."%)',\n";
                }
                
                echo "  '".$row_export["tt"]." (".number_format($row_export["tp"], 2, '.', '')."%)',\n";
                echo "]);\n";
            }
        }
        ?>
        
        // Fila de totales
        const totalsRow = ['TOTALES', ''];
        <?php
        $total_general = array_sum($totales);
        foreach($etapas_headers as $etapa_id => $etapa_nombre) {
            $porcentaje = ($total_general > 0) ? ($totales[$etapa_id] / $total_general) * 100 : 0;
            echo "totalsRow.push('".$totales[$etapa_id]." (".number_format($porcentaje, 2, '.', '')."%)');\n";
        }
        echo "totalsRow.push('".$total_general." (100%)');\n";
        ?>
        data.push(totalsRow);
        
        // Crear hoja de cálculo
        const ws = XLSX.utils.aoa_to_sheet(data);
        
        // Configurar anchos de columna
        const colWidths = [
            { wch: 25 }, // Sector
            { wch: 20 }  // Tipo
        ];
        
        <?php foreach($etapas_headers as $etapa_id => $etapa_nombre) { ?>
        colWidths.push({ wch: 15 });
        <?php } ?>
        
        colWidths.push({ wch: 15 }); // Total
        
        ws['!cols'] = colWidths;
        
        XLSX.utils.book_append_sheet(wb, ws, 'Estado de Avance');
        
        const fecha = new Date().toISOString().split('T')[0];
        const filename = 'estado_avance_' + fecha + '.xlsx';
        
        XLSX.writeFile(wb, filename);
        
        console.log('Excel exportado correctamente con', data.length, 'filas');
        
    } catch(error) {
        console.error('Error al generar Excel:', error);
        alert('Hubo un error al generar el archivo Excel. Por favor intente nuevamente.');
    }
}
</script>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js" integrity="sha384-cuYeSxntonz0PPNlHhBs68uyIAVpIIOZZ5JqeqvYYIcEL727kskC66kF92t6Xl2V" crossorigin="anonymous"></script>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js"></script>
<script src="app.js"></script>
</body>
</html>
