<?php 
// ************************************************************
//    __       _ _ _                  _   
//   / _|_   _| | | |_ _ __ _   _ ___| |_ 
//  | |_| | | | | | __| '__| | | / __| __|
//  |  _| |_| | | | |_| |  | |_| \__ \ |_ 
//  |_|  \__,_|_|_|\__|_|   \__,_|___/\__|
//                                         
// Autor: Marcelo Jiménez
// Fulltrust Software
// https://www.fulltrust.net
// Fecha última actualización: 2025-09-21 14:02:15
// ************************************************************

//error_reporting(E_ALL);
//ini_set('display_errors', 1);
?>
<?php
include ("../conf/db.php");


$q = "set names utf8";
if(!$preg = $db->query($q)){
	die('Ha ocurrido un error ejecutando la consulta [' . $db->error . ']');
}

$q = "select * from stats";
if(!$preg = $db->query($q)){
	die('Ha ocurrido un error ejecutando la consulta [' . $db->error . ']');
}
$row_stats = $preg->fetch_assoc();
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title style="line-height: 50px !important;"></title>

<style>
        .logos {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 15px;
            flex-wrap: nowrap; /* Evita que se apilen */
            padding: 20px;
            max-width: 100%;
            overflow-x: auto; /* Permite scroll horizontal si es necesario */
        }

        .logos img {
            flex-shrink: 0; /* Evita que las imágenes se compriman demasiado */
            max-width: 150px;
            height: auto;
            transition: all 0.3s ease; /* Suaviza los cambios de tamaño */
        }

        /* Tablets */
        @media (max-width: 768px) {
            .logos {
                gap: 10px;
            }
            .logos img {
                max-width: 120px;
                max-height: 50px; /* Limita la altura en tablets */
                object-fit: contain; /* Mantiene proporciones */
            }
        }

        /* Móviles */
        @media (max-width: 480px) {
            .logos {
                gap: 8px;
                padding: 15px 10px;
            }
            .logos img {
                max-width: 80px;
                max-height: 35px; /* Altura uniforme para móviles */
                object-fit: contain; /* Mantiene proporciones sin distorsión */
            }
        }

        /* Móviles muy pequeños */
        @media (max-width: 320px) {
            .logos {
                gap: 5px;
                padding: 10px 5px;
            }
            .logos img {
                max-width: 60px;
                max-height: 30px; /* Altura uniforme para móviles pequeños */
                object-fit: contain;
            }
        }

        /* Opcional: Estilo para el contenedor principal */
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        /* Hover effect opcional */
        .logos img:hover {
            transform: scale(1.05);
        }

</style>
</head>

<body>

    <div class="logos">
        <a href="https://www.gobiernosantiago.cl/" target="_blank" rel="noopener">
            <img src="img/lgore.png" alt="Gobierno Regional Metropolitano de Santiago">
        </a>
        <a href="https://www.coresantiago.cl/" target="_blank" rel="noopener">
            <img src="img/lcore.png" alt="Consejo Regional Metropolitano">
        </a>
        <a href="https://www.sanjosedemaipo.cl/" target="_blank" rel="noopener">
            <img src="img/lsjm.png" alt="Ilustre Municipalidad de San José de Maipo">
        </a>
        <img src="img/lpulso-1.png" alt="Pulso Consultores">
    </div>





<div style="margin-top: 10px; padding: 10px;">

<p style="font-size:1.3em; color:black">Este mapa interactivo presenta la localización y características de las iniciativas que conforman la cartera de inversión priorizada del Plan Maestro Comunal de Inversión de San José de Maipo. Cada marcador en el mapa representa un proyecto que busca contribuir al desarrollo sostenible, inclusivo y territorialmente equilibrado de la comuna, en coherencia con su vocación turística y su identidad local.</p> 
<p style="font-size:1.3em; color:black">Al explorar el mapa, los usuarios pueden hacer clic sobre cada iniciativa para conocer su ubicación, objetivos, estado de avance y detalles relevantes. Esta herramienta permite visualizar cómo las distintas propuestas se articulan en el territorio y facilita el acceso a información transparente y actualizada sobre el proceso de planificación comunal. Además, desde la ficha de cada proyecto es posible descargar una versión en PDF con información detallada, fomentando así la participación y el seguimiento ciudadano.</p>


</div>

<hr>
<p style="color: gray"><?php echo number_format($row_stats["visitas"], 0, '.', ',');?> visitas desde junio de 2025</p>  	
	</body>
</html>