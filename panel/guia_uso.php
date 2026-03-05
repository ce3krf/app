<?php 
// ************************************************************
// Autor: Marcelo Jiménez
// Fulltrust Software
// https://www.fulltrust.net
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
<title><?php echo $row_param["parametros_titulo"];?></title>

<link rel="icon" type="image/png" href="../favicon.png">

<script src="js/jquery-3.5.1.min.js"></script>	

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">

<script src="functions.js"></script>

<link rel="stylesheet" type="text/css" href="css/styles.css"/>

<style>
.guia-card {
    background: white;
    border-radius: 16px;
    padding: 40px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    max-width: 700px;
    margin: 40px auto;
}

.guia-icon {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #4a8ecd 0%, #1976d2 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 30px;
    box-shadow: 0 4px 15px rgba(74, 142, 205, 0.3);
}

.guia-icon i {
    font-size: 2.5rem;
    color: white;
}

.guia-title {
    font-size: 1.8rem;
    font-weight: 600;
    color: #2c3e50;
    text-align: center;
    margin-bottom: 20px;
}

.guia-description {
    color: #6c757d;
    font-size: 1rem;
    line-height: 1.7;
    text-align: center;
    margin-bottom: 30px;
}

.guia-info {
    background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
    border-left: 4px solid #1976d2;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 30px;
}

.guia-info h6 {
    color: #1565c0;
    font-weight: 600;
    margin-bottom: 15px;
    font-size: 0.95rem;
}

.guia-info ul {
    margin: 0;
    padding-left: 20px;
    color: #424242;
}

.guia-info li {
    margin-bottom: 8px;
    font-size: 0.9rem;
}

.btn-guia {
    background: linear-gradient(135deg, #4a8ecd 0%, #1976d2 100%);
    border: none;
    padding: 14px 40px;
    font-size: 1.1rem;
    font-weight: 600;
    border-radius: 50px;
    color: white;
    box-shadow: 0 4px 15px rgba(74, 142, 205, 0.3);
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    text-decoration: none;
}

.btn-guia:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(74, 142, 205, 0.4);
    background: linear-gradient(135deg, #1976d2 0%, #0d47a1 100%);
    color: white;
    text-decoration: none;
}

.btn-guia i {
    font-size: 1.2rem;
}

/* Panel destacado del PDF */
.pdf-panel {
    background: linear-gradient(135deg, #fce4ec 0%, #f8bbd0 100%);
    border-left: 4px solid #c62828;
    border-radius: 8px;
    padding: 22px 20px;
    margin-bottom: 30px;
    display: flex;
    align-items: center;
    gap: 16px;
}

.pdf-panel .pdf-icon {
    font-size: 2.2rem;
    color: #c62828;
    flex-shrink: 0;
}

.pdf-panel .pdf-body {
    line-height: 1.4;
    flex: 1;
}

.pdf-panel .pdf-label {
    font-size: 0.78rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #b71c1c;
    margin-bottom: 3px;
}

.pdf-panel .pdf-name {
    font-size: 1rem;
    font-weight: 700;
    color: #7f0000;
}

.pdf-panel .pdf-desc {
    font-size: 0.85rem;
    color: #c62828;
    margin-top: 2px;
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
        <div class="container-fluid">

            <div class="top-info-div">
                <div class="row align-items-center">
                    <div class="col">
                        <h5 style="margin: 0; color: var(--main); font-weight: 600;">GUÍA DE USO</h5>
                    </div>
                </div>
            </div>

            <div class="container">

                <div class="guia-card">
                    <div class="guia-icon">
                        <i class="fa-solid fa-book-open"></i>
                    </div>
                    
                    <h2 class="guia-title">Guía de Uso del Sistema</h2>
                    
                    <p class="guia-description">
                        Aquí encontrarás el manual completo de uso del sistema. 
                        Consulta la guía para conocer todas las funcionalidades disponibles, 
                        pasos de operación y recomendaciones de uso.
                    </p>

                    <!-- Panel PDF -->
                    <div class="pdf-panel">
                        <div class="pdf-icon"><i class="fa-solid fa-file-pdf"></i></div>
                        <div class="pdf-body">
                            <div class="pdf-label">📄 Documento disponible</div>
                            <div class="pdf-name">guia_de_uso.pdf</div>
                            <div class="pdf-desc">Manual completo en formato PDF</div>
                        </div>
                    </div>

                    <div class="guia-info">
                        <h6>📋 ¿Qué encontrarás en la guía?</h6>
                        <ul>
                            <li>Descripción de todos los módulos del sistema</li>
                            <li>Instrucciones paso a paso para cada función</li>
                            <li>Preguntas frecuentes y solución de problemas</li>
                            <li>Recomendaciones y buenas prácticas de uso</li>
                        </ul>
                    </div>
                    
                    <div class="text-center">
                        <a href="doc/guia_de_uso.pdf" target="_blank" class="btn-guia">
                            <i class="fa-solid fa-file-pdf"></i>
                            Abrir Guía de Uso
                        </a>
                    </div>

                    <div class="text-center mt-4">
                        <small style="color: #9ca3af;">
                            <i class="fa-solid fa-circle-info"></i> 
                            El documento se abrirá en una nueva pestaña de tu navegador
                        </small>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>

<!-- Botón Back to Top -->
<a href="#" class="cd-top"></a>

<?php include("footer.php");?>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js"></script>
<script src="app.js"></script>

</body>
</html>
