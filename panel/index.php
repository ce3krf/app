<?php
// ************************************************************
// Autor: Marcelo Jiménez
// Fulltrust Software
// https://www.fulltrust.net
// Página de bienvenida (reemplaza el panel de control)
// ************************************************************

@session_start();
include_once("../conf/db.php");
include('functions.php');

date_default_timezone_set('America/Santiago');

function h($v) {
    return htmlspecialchars((string) ($v ?? ''), ENT_QUOTES, 'UTF-8');
}

// Imágenes del sitio del proyecto. Si se copian a img/, cambiar esta ruta.
define('IMG_PLAN', 'https://plansanjosedemaipo.cl/wp-content/uploads/');
define('URL_PLAN', 'https://plansanjosedemaipo.cl/');

$perfil  = $_SESSION['usuarios_profile'] ?? '';
$user_id = (int) ($_SESSION['net_fulltrust_fas_id'] ?? 0);


// -----------------------------------------------
// Saludo
// -----------------------------------------------
$nombre = '';
if ($user_id > 0) {
    $r = $db->query("SELECT usuarios_nombre FROM usuarios WHERE usuarios_id = $user_id LIMIT 1");
    if ($r && $u = $r->fetch_assoc()) {
        $partes = preg_split('/\s+/', trim((string) $u['usuarios_nombre']));
        $nombre = $partes[0] ?? '';
    }
}
$hora = (int) date('G');
$saludo = $hora < 12 ? 'Buenos días' : ($hora < 20 ? 'Buenas tardes' : 'Buenas noches');

$MESES = ['', 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
$DIAS  = ['domingo', 'lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado'];
$hoy   = $DIAS[(int) date('w')] . ' ' . date('j') . ' de ' . $MESES[(int) date('n')];
$hoy   = mb_strtoupper(mb_substr($hoy, 0, 1)) . mb_substr($hoy, 1);


// -----------------------------------------------
// Localidades (columnas 0/1 de proyectos)
// -----------------------------------------------
$LOCALIDADES = [
    'la_obra' => 'La Obra', 'las_vertientes' => 'Las Vertientes', 'el_canelo' => 'El Canelo',
    'el_manzano' => 'El Manzano', 'los_maitenes' => 'Los Maitenes', 'el_alfalfal' => 'El Alfalfal',
    'el_colorado' => 'El Colorado', 'el_guayacan' => 'El Guayacán', 'san_jose_de_maipo' => 'San José de Maipo',
    'el_melocoton' => 'El Melocotón', 'lagunillas' => 'Lagunillas', 'el_toyo' => 'El Toyo',
    'san_alfonso' => 'San Alfonso', 'el_ingenio' => 'El Ingenio', 'bollenar' => 'Bollenar',
    'san_gabriel' => 'San Gabriel', 'el_yeso' => 'El Yeso', 'las_melosas' => 'Las Melosas',
    'los_queltehues' => 'Los Queltehues', 'el_volcan' => 'El Volcán', 'banos_morales' => 'Baños Morales',
    'lo_valdes' => 'Lo Valdés', 'el_romeral' => 'El Romeral', 'los_nogales' => 'Los Nogales',
];


// -----------------------------------------------
// La cartera en tres cifras
// -----------------------------------------------
$total_iniciativas = 0;
$preseleccionadas  = 0;
$loc_con_iniciativa = [];

$sum_loc = [];
foreach ($LOCALIDADES as $col => $label) {
    $sum_loc[] = "SUM(CASE WHEN $col = 1 THEN 1 ELSE 0 END) AS `$col`";
}
$sql = "SELECT COUNT(*) AS total,
               SUM(CASE WHEN seleccionada_pm_sjm = 'Si' THEN 1 ELSE 0 END) AS pm,
               " . implode(",\n               ", $sum_loc) . "
        FROM proyectos
        WHERE proyectos_status = 1";
$r = $db->query($sql);
if ($r && $row = $r->fetch_assoc()) {
    $total_iniciativas = (int) $row['total'];
    $preseleccionadas  = (int) $row['pm'];
    foreach ($LOCALIDADES as $col => $label) {
        if ((int) $row[$col] > 0) $loc_con_iniciativa[$col] = (int) $row[$col];
    }
} elseif (!$r) {
    error_log('Error bienvenida (cifras): ' . $db->error);
}


$puede_crear = in_array($perfil, ['ADMINISTRADOR', 'ÁREA'], true);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo h($row_param["parametros_titulo"] ?? 'Plan Maestro de Inversión'); ?></title>
<link rel="icon" type="image/png" href="../favicon.png">
<script src="js/jquery-3.5.1.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">
<script src="functions.js"></script>
<link rel="stylesheet" href="css/styles.css"/>

<style>
/* ── Bienvenida: solo afecta a esta página ── */
.bv {
    /* Azules de la aplicación (styles.css): --main #023047, --topdiv #4a8ecd, --top #4e7dbb, --bg2 #e3f2fd */
    --celeste:  #eef5fc;
    --borde:    #d6e3f0;
    --tinta:    #2b3a46;
    --gris:     #5f6f7c;
    color: var(--tinta);
    max-width: 1180px;
    margin: 0 auto;
}
.bv h2, .bv h3 { font-weight: 600; color: var(--main); letter-spacing: 0; }

/* Portada */
.bv-hero {
    position: relative;
    border-radius: 22px;
    overflow: hidden;
    min-height: 340px;
    display: flex;
    align-items: flex-end;
    background: #5d7a8a url('img/MunicipioSJose8.jpg') center 40% / cover no-repeat;
    box-shadow: 0 10px 30px rgba(2, 48, 71, .15);
}
.bv-hero::before {
    content: "";
    position: absolute; inset: 0;
    background: linear-gradient(180deg, rgba(2,48,71,0) 25%, rgba(2,48,71,.78) 100%);
}
.bv-hero-texto { position: relative; padding: 34px 40px; color: #fff; max-width: 760px; }
.bv-hero-fecha { font-size: 14px; opacity: .9; }
.bv-hero h1 {
    font-size: clamp(30px, 4vw, 44px);
    font-weight: 600;
    margin: 4px 0 10px;
    line-height: 1.15;
}
.bv-hero h1 em { font-style: normal; color: #bfe0ff; }
.bv-hero p { font-size: 17px; margin: 0; opacity: .95; line-height: 1.5; }

/* Bloques */
.bv-bloque { margin-top: 44px; }
.bv-bloque h2 { font-size: 26px; margin-bottom: 6px; }
.bv-bajada { color: var(--gris); margin-bottom: 20px; font-size: 16px; }

/* Qué es este espacio */
.bv-que {
    background: #fff;
    border: 1px solid var(--borde);
    border-left: 4px solid var(--topdiv);
    border-radius: 18px;
    padding: 26px 30px;
}
.bv-que-nombre { font-size: 13px; font-weight: 700; color: var(--top); text-transform: uppercase; letter-spacing: .6px; margin-bottom: 6px; }
.bv-que p { font-size: 16px; line-height: 1.55; margin: 0 0 16px; color: var(--tinta); }
.bv-que ul { list-style: none; padding: 0; margin: 0; display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 10px 24px; }
.bv-que li { display: flex; gap: 10px; align-items: flex-start; font-size: 15px; color: var(--gris); line-height: 1.45; }
.bv-que li i { color: var(--topdiv); margin-top: 3px; width: 18px; text-align: center; }
.bv-que li b { color: var(--main); font-weight: 600; }

/* Accesos */
.bv-acceso {
    display: flex; gap: 16px; align-items: flex-start;
    height: 100%;
    padding: 22px;
    border-radius: 18px;
    background: #fff;
    border: 1px solid var(--borde);
    color: inherit;
    text-decoration: none;
    transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
}
.bv-acceso:hover { transform: translateY(-3px); box-shadow: 0 10px 24px rgba(2,48,71,.10); border-color: var(--topdiv); color: inherit; }
.bv-acceso-icono {
    flex-shrink: 0;
    width: 52px; height: 52px; border-radius: 16px;
    display: flex; align-items: center; justify-content: center;
    font-size: 21px;
}
.bv-acceso h3 { font-size: 19px; margin: 2px 0 4px; }
.bv-acceso p { margin: 0; font-size: 15px; color: var(--gris); line-height: 1.45; }
.ico-1 { background: var(--main);   color: #fff; }
.ico-2 { background: var(--top);    color: #fff; }
.ico-3 { background: var(--bg2);    color: var(--main); }

/* Cifras */
.bv-cifras {
    background: var(--celeste);
    border: 1px solid var(--borde);
    border-radius: 22px;
    padding: 30px 26px;
}
.bv-cifra { text-align: center; padding: 6px 10px; }
.bv-cifra-num { font-size: 44px; color: var(--main); line-height: 1; }
.bv-cifra-num small { font-size: 22px; color: var(--top); }
.bv-cifra-txt { margin-top: 8px; font-size: 15px; color: var(--gris); }
@media (min-width: 768px) {
    .bv-cifra + .bv-cifra { border-left: 1px solid var(--borde); }
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
        <div class="bv">

            <!-- Portada -->
            <section class="bv-hero">
                <div class="bv-hero-texto">
                    <div class="bv-hero-fecha"><?php echo h($hoy); ?></div>
                    <h1><?php echo h($saludo); ?><?php echo $nombre !== '' ? ', <em>' . h($nombre) . '</em>' : ''; ?></h1>
                    <p>Te damos la bienvenida al espacio de trabajo del Plan Maestro de Inversión de San José de Maipo.
                       </p>
                </div>
            </section>

            <!-- Qué es este espacio -->
            <section class="bv-bloque">
                <div class="bv-que">
                    <div class="bv-que-nombre">Plataforma de gestión, seguimiento y georreferenciación del Plan Maestro de Inversión de San José de Maipo</div>
                    <p> </p>
                    <ul>
                        <li><i class="fa-solid fa-magnifying-glass"></i><span><b>Consultar</b> las iniciativas, con búsqueda y filtros por instrumento, sector y Plan Maestro.</span></li>
                        <li><i class="fa-solid fa-file-lines"></i><span><b>Gestionar</b> la ficha de cada iniciativa: descripción, brechas, costos, financiamiento y cronograma.</span></li>
                        <li><i class="fa-solid fa-location-dot"></i><span><b>Georreferenciar</b> cada iniciativa en el mapa y las localidades que cubre.</span></li>
                        <li><i class="fa-solid fa-chart-line"></i><span><b>Seguir</b> su avance por etapa y descargar la información en Excel o PDF.</span></li>
                    </ul>
                </div>
            </section>

            <!-- Accesos -->
            <section class="bv-bloque">
                <h2>¿Por dónde quieres comenzar?</h2>
                <div class="bv-bajada">Los accesos más usados, a un clic.</div>
                <div class="row g-3">
                    <div class="col-md-<?php echo $puede_crear ? '4' : '6'; ?>">
                        <a class="bv-acceso" href="proyectos.php">
                            <span class="bv-acceso-icono ico-1"><i class="fa-solid fa-seedling"></i></span>
                            <span>
                                <h3>Ver las iniciativas</h3>
                                <p>Revisa la cartera completa, busca por nombre o sector y descarga la planilla.</p>
                            </span>
                        </a>
                    </div>
                    <?php if ($puede_crear): ?>
                    <div class="col-md-4">
                        <a class="bv-acceso" href="proyectos_editar.php?proyectos_id=0">
                            <span class="bv-acceso-icono ico-2"><i class="fa-solid fa-pen-to-square"></i></span>
                            <span>
                                <h3>Registrar una iniciativa</h3>
                                <p>Agrega una nueva idea a la cartera con su descripción, ubicación y costos.</p>
                            </span>
                        </a>
                    </div>
                    <?php endif; ?>
                    <div class="col-md-<?php echo $puede_crear ? '4' : '6'; ?>">
                        <a class="bv-acceso" href="<?php echo URL_PLAN; ?>" target="_blank" rel="noopener">
                            <span class="bv-acceso-icono ico-3"><i class="fa-solid fa-mountain-sun"></i></span>
                            <span>
                                <h3>Conocer el Plan</h3>
                                <p>Noticias, videos y documentos del proceso en el sitio del Plan Maestro.</p>
                            </span>
                        </a>
                    </div>
                </div>
            </section>

            <!-- Cifras -->
            <section class="bv-bloque">
                <div class="bv-cifras">
                    <div class="row g-3 align-items-center">
                        <div class="col-md-4 bv-cifra">
                            <div class="bv-cifra-num"><?php echo $total_iniciativas; ?></div>
                            <div class="bv-cifra-txt">iniciativas forman hoy la cartera</div>
                        </div>
                        <div class="col-md-4 bv-cifra">
                            <div class="bv-cifra-num"><?php echo $preseleccionadas; ?></div>
                            <div class="bv-cifra-txt">fueron preseleccionadas para el Plan Maestro</div>
                        </div>
                        <div class="col-md-4 bv-cifra">
                            <div class="bv-cifra-num"><?php echo count($loc_con_iniciativa); ?> <small>de <?php echo count($LOCALIDADES); ?></small></div>
                            <div class="bv-cifra-txt">localidades ya cuentan con al menos una iniciativa</div>
                        </div>
                    </div>
                </div>
            </section>

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
