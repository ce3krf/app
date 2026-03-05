<?php 
// ************************************************************
// Autor: Marcelo Jiménez
// Fulltrust Software
// https://www.fulltrust.net
// Fecha última actualización: 2025-10-20
// Versión Mejorada - Tabla de Avance Financiero
// ************************************************************
?>
<?php
@session_start();
include("../conf/db.php");		
include('functions.php');

$sql = "set names utf8";
if(!$db->query($sql)){
    die('Hay un error [' . $db->error . ']');
}   

// Obtener parámetros del sistema
$sql = "SELECT * FROM parametros LIMIT 1";
$result_param = $db->query($sql);
$row_param = $result_param->fetch_assoc();

// Obtener sectores para el filtro
$sql_sectores = "SELECT DISTINCT sector_id, sector_descripcion FROM sectores ORDER BY sector_descripcion";
$sectores = $db->query($sql_sectores);

// Obtener tipos para el filtro
$sql_tipos = "SELECT DISTINCT tipo FROM tipo ORDER BY tipo";
$tipos = $db->query($sql_tipos);

// Construir filtros WHERE
$where_conditions = [];
if(isset($_GET['sector']) && $_GET['sector'] != '') {
    $where_conditions[] = "sectores.sector_id = " . intval($_GET['sector']);
}
if(isset($_GET['tipo']) && $_GET['tipo'] != '') {
    $where_conditions[] = "tipo.tipo = '" . $db->real_escape_string($_GET['tipo']) . "'";
}

$where_clause = '';
if(count($where_conditions) > 0) {
    $where_clause = "WHERE " . implode(" AND ", $where_conditions);
}

// Consulta principal con filtros
$sql = "SELECT 
  sectores.sector_id,
  sectores.sector_descripcion,
  tipo.tipo,
  etapas.etapas_id,
  etapas.etapas_descripcion,
  SUM(COALESCE(proyectos.p_diseno, 0) + COALESCE(proyectos.p_ejecucion, 0)) AS presupuesto,
  SUM(CASE WHEN actividades.estado = 'Completa' THEN COALESCE(actividades.monto, 0) ELSE 0 END) AS inversion,
  COUNT(DISTINCT proyectos.id) AS cantidad_proyectos
FROM
  proyectos
  LEFT OUTER JOIN sectores ON (proyectos.sector = sectores.sector_id)
  LEFT OUTER JOIN tipo ON (proyectos.tipo = tipo.id)
  LEFT OUTER JOIN etapas ON (proyectos.etapa = etapas.etapas_id)
  LEFT OUTER JOIN actividades ON (proyectos.id = actividades.id)
$where_clause
GROUP BY
  sectores.sector_id,
  sectores.sector_descripcion,
  tipo.tipo,
  etapas.etapas_id,
  etapas.etapas_descripcion
ORDER BY
  sectores.sector_descripcion,
  tipo.tipo,
  etapas.etapas_descripcion";

if(!$resultado = $db->query($sql)){
    die('Hay un error en la consulta [' . $db->error . ']');
}

// Organizar datos por sector y tipo
$datos_organizados = [];
$totales_por_etapa = [];
$total_general_presupuesto = 0;
$total_general_inversion = 0;

while($row = $resultado->fetch_assoc()) {
    $sector = $row['sector_descripcion'];
    $tipo = $row['tipo'];
    $etapa_id = $row['etapas_id'];
    $etapa = $row['etapas_descripcion'];
    $presupuesto = floatval($row['presupuesto']);
    $inversion = floatval($row['inversion']);
    $cantidad = intval($row['cantidad_proyectos']);
    
    // Calcular porcentaje
    $porcentaje = ($presupuesto > 0) ? ($inversion / $presupuesto * 100) : 0;
    
    // Organizar por sector -> tipo -> etapa
    if(!isset($datos_organizados[$sector])) {
        $datos_organizados[$sector] = [];
    }
    if(!isset($datos_organizados[$sector][$tipo])) {
        $datos_organizados[$sector][$tipo] = [];
    }
    
    $datos_organizados[$sector][$tipo][$etapa] = [
        'presupuesto' => $presupuesto,
        'inversion' => $inversion,
        'porcentaje' => $porcentaje,
        'cantidad' => $cantidad
    ];
    
    // Acumular totales por etapa
    if(!isset($totales_por_etapa[$etapa])) {
        $totales_por_etapa[$etapa] = [
            'presupuesto' => 0,
            'inversion' => 0
        ];
    }
    $totales_por_etapa[$etapa]['presupuesto'] += $presupuesto;
    $totales_por_etapa[$etapa]['inversion'] += $inversion;
    
    // Totales generales
    $total_general_presupuesto += $presupuesto;
    $total_general_inversion += $inversion;
}

// Obtener todas las etapas únicas (ordenadas)
$sql_etapas = "SELECT etapas_id, etapas_descripcion FROM etapas ORDER BY etapas_id";
$etapas_result = $db->query($sql_etapas);
$todas_etapas = [];
while($row_e = $etapas_result->fetch_assoc()) {
    $todas_etapas[] = $row_e['etapas_descripcion'];
}
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
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<link rel="stylesheet" type="text/css" href="css/styles.css"/>		

<style>
:root {
  --main: #018837;
  --primary: #2d6a4f;
  --secondary: #40a629;
  --accent: #74c69d;
  --light: #f1faee;
  --border: #e0e0e0;
}

body {
    background-color: #f8f9fa;
}

.table-wrapper {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.08);
    overflow-x: auto;
    margin-bottom: 20px;
}

#data-table {
    font-size: 13px;
    margin-bottom: 0;
    width: 100%;
}

#data-table thead th {
    background: var(--primary);
    color: white;
    font-weight: 600;
    padding: 12px 10px;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border: 1px solid rgba(255,255,255,0.1);
    position: sticky;
    top: 0;
    z-index: 10;
    text-align: center;
    vertical-align: middle;
}

#data-table thead th.left-align {
    text-align: left;
}

#data-table tbody td {
    padding: 10px;
    border: 1px solid var(--border);
    vertical-align: middle;
}

#data-table tbody tr:hover {
    background-color: #f8f9fa;
}

.sector-cell, .tipo-cell {
    font-weight: 600;
    color: var(--main);
    background-color: #f8fdf9;
}

.etapa-cell {
    background-color: #fafbfc;
    color: #666;
    font-size: 12px;
}

.data-cell {
    text-align: right;
    font-size: 13px;
}

.percent-cell {
    text-align: center;
    font-weight: 600;
    color: var(--primary);
}

.percent-high {
    background-color: #d4edda;
    color: #155724;
}

.percent-medium {
    background-color: #fff3cd;
    color: #856404;
}

.percent-low {
    background-color: #f8d7da;
    color: #721c24;
}

#data-table tfoot td {
    background: var(--primary);
    color: white;
    font-weight: 700;
    padding: 12px 10px;
    border: none;
    text-align: right;
    font-size: 13px;
}

#data-table tfoot td:first-child {
    text-align: left;
}

.total-general {
    background: var(--main) !important;
    font-size: 14px !important;
}

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
    box-shadow: 0 0 0 3px rgba(45, 106, 79, 0.1);
}

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
    box-shadow: 0 2px 8px rgba(45, 106, 79, 0.3);
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

.summary-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    margin-bottom: 20px;
}

.summary-card {
    background: white;
    border-radius: 8px;
    padding: 20px;
    text-align: center;
    box-shadow: 0 2px 4px rgba(0,0,0,0.08);
    border-left: 4px solid var(--primary);
}

.summary-card.total-card {
    border-left-color: var(--main);
    background: linear-gradient(135deg, rgba(45, 106, 79, 0.1), rgba(1, 136, 55, 0.05));
}

.summary-value {
    font-size: 28px;
    font-weight: 700;
    color: var(--main);
    display: block;
    line-height: 1.2;
}

.summary-label {
    font-size: 11px;
    color: #666;
    text-transform: uppercase;
    margin-top: 8px;
    display: block;
    letter-spacing: 0.5px;
}

.summary-sublabel {
    font-size: 14px;
    color: var(--primary);
    margin-top: 5px;
}

@media (max-width: 768px) {
    #data-table {
        font-size: 11px;
    }
    
    .filter-group {
        flex-direction: column;
    }
    
    .filter-item {
        flex: 1 1 100%;
    }
}

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
                    <h5><i class="fas fa-chart-line"></i> AVANCE FINANCIERO</h5>
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
                                    <?php 
                                    if($sectores && $sectores->num_rows > 0) {
                                        $sectores->data_seek(0);
                                        while($row_s = $sectores->fetch_assoc()) {
                                            $selected = (isset($_GET["sector"]) && $row_s["sector_id"] == $_GET["sector"]) ? "selected" : "";
                                            echo '<option value="'.$row_s["sector_id"].'" '.$selected.'>'.$row_s["sector_descripcion"].'</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="filter-item">
                                <label for="tipo">Tipo</label>
                                <select class="form-select" name="tipo" id="tipo">
                                    <option value="">Todos los tipos</option>
                                    <?php 
                                    if($tipos && $tipos->num_rows > 0) {
                                        $tipos->data_seek(0);
                                        while($row_t = $tipos->fetch_assoc()) {
                                            $selected = (isset($_GET["tipo"]) && $row_t["tipo"] == $_GET["tipo"]) ? "selected" : "";
                                            echo '<option value="'.$row_t["tipo"].'" '.$selected.'>'.$row_t["tipo"].'</option>';
                                        }
                                    }
                                    ?>
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

                <?php if(count($datos_organizados) > 0) { 
                    $porcentaje_general = ($total_general_presupuesto > 0) ? ($total_general_inversion / $total_general_presupuesto * 100) : 0;
                ?>
                
                <!-- Resumen general -->
                <div class="summary-cards">
                    <div class="summary-card total-card">
                        <span class="summary-value">$<?php echo number_format($total_general_presupuesto, 0);?></span>
                        <span class="summary-label">Presupuesto Total</span>
                    </div>
                    <div class="summary-card total-card">
                        <span class="summary-value">$<?php echo number_format($total_general_inversion, 0);?></span>
                        <span class="summary-label">Inversión Total</span>
                    </div>
                    <div class="summary-card total-card">
                        <span class="summary-value"><?php echo number_format($porcentaje_general, 1);?>%</span>
                        <span class="summary-label">Avance General</span>
                    </div>
                </div>

                <div id="pprint">
                <div class="table-wrapper">
                <table id="data-table" class="table table-sm">
                    <thead>
                        <tr>
                            <th class="left-align">Sector</th>
                            <th class="left-align">Tipo</th>
                            <th class="left-align">Etapa</th>
                            <th>Presupuesto</th>
                            <th>Inversión</th>
                            <th>% Avance</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php 
                    foreach($datos_organizados as $sector => $tipos_data) {
                        foreach($tipos_data as $tipo => $etapas_data) {
                            $primera_fila = true;
                            foreach($etapas_data as $etapa => $data) {
                                $porcentaje = $data['porcentaje'];
                                
                                // Clase de color según porcentaje
                                $percent_class = '';
                                if($porcentaje >= 75) {
                                    $percent_class = 'percent-high';
                                } elseif($porcentaje >= 40) {
                                    $percent_class = 'percent-medium';
                                } elseif($porcentaje > 0) {
                                    $percent_class = 'percent-low';
                                }
                    ?>
                        <tr>
                            <?php if($primera_fila) { ?>
                            <td class="sector-cell" rowspan="<?php echo count($etapas_data);?>"><?php echo htmlspecialchars($sector);?></td>
                            <td class="tipo-cell" rowspan="<?php echo count($etapas_data);?>"><?php echo htmlspecialchars($tipo);?></td>
                            <?php 
                                $primera_fila = false;
                            } ?>
                            <td class="etapa-cell"><?php echo htmlspecialchars($etapa);?></td>
                            <td class="data-cell">$<?php echo number_format($data['presupuesto'], 0);?></td>
                            <td class="data-cell">$<?php echo number_format($data['inversion'], 0);?></td>
                            <td class="percent-cell <?php echo $percent_class;?>">
                                <?php echo number_format($porcentaje, 1);?>%
                            </td>
                        </tr>
                    <?php 
                            }
                        }
                    } 
                    ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="total-general">TOTALES</td>
                            <td class="total-general">$<?php echo number_format($total_general_presupuesto, 0);?></td>
                            <td class="total-general">$<?php echo number_format($total_general_inversion, 0);?></td>
                            <td class="total-general"><?php echo number_format($porcentaje_general, 1);?>%</td>
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

<script>
function exportToExcel() {
    try {
        const wb = XLSX.utils.book_new();
        const data = [];
        
        // Encabezados
        data.push(['Sector', 'Tipo', 'Etapa', 'Presupuesto', 'Inversión', '% Avance']);
        
        // Datos
        <?php 
        foreach($datos_organizados as $sector => $tipos_data) {
            foreach($tipos_data as $tipo => $etapas_data) {
                foreach($etapas_data as $etapa => $data) {
                    echo "data.push([\n";
                    echo "  '".addslashes($sector)."',\n";
                    echo "  '".addslashes($tipo)."',\n";
                    echo "  '".addslashes($etapa)."',\n";
                    echo "  ".$data['presupuesto'].",\n";
                    echo "  ".$data['inversion'].",\n";
                    echo "  ".number_format($data['porcentaje'], 2, '.', '')."\n";
                    echo "]);\n";
                }
            }
        }
        ?>
        
        // Fila de totales
        data.push([
            'TOTALES',
            '',
            '',
            <?php echo $total_general_presupuesto;?>,
            <?php echo $total_general_inversion;?>,
            <?php echo number_format($porcentaje_general, 2, '.', '');?>
        ]);
        
        const ws = XLSX.utils.aoa_to_sheet(data);
        
        // Configurar anchos de columna
        ws['!cols'] = [
            { wch: 25 },
            { wch: 20 },
            { wch: 20 },
            { wch: 15 },
            { wch: 15 },
            { wch: 12 }
        ];
        
        XLSX.utils.book_append_sheet(wb, ws, 'Avance Financiero');
        
        const fecha = new Date().toISOString().split('T')[0];
        const filename = 'avance_financiero_' + fecha + '.xlsx';
        
        XLSX.writeFile(wb, filename);
        
    } catch(error) {
        console.error('Error al generar Excel:', error);
        alert('Hubo un error al generar el archivo Excel. Por favor intente nuevamente.');
    }
}
</script>


<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js"></script>
<script src="app.js"></script>
</body>
</html>
