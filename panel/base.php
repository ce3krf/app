<?php 
// ************************************************************
// Autor: Marcelo Jiménez
// Fulltrust Software
// https://www.fulltrust.net
// Fecha última actualización: 2025-12-31
// ************************************************************
?>
<?php
//error_reporting(E_ALL);
//ini_set('display_errors', '1');

@session_start();
include("../conf/db.php");		
include('functions.php');

?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1">	
<title><?php echo $row_param["parametros_titulo"];?></title>

<script src="js/jquery-3.5.1.min.js"></script>	
<link rel="icon" type="image/png" href="../img/gobierno-regional-ohiggins.png">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>	

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">

<link rel="stylesheet" type="text/css" href="css/styles.css"/>		

</head>
<body>

<?php include("header.php");?>

<div id="wrapper">
    <!-- Sidebar -->
    <div id="sidebar-wrapper">
        <?php include("sidebar.php");?>
    </div>

    <!-- Contenido Principal -->
    <div id="page-content-wrapper">
        <div class="container-fluid">
            
            <!-- Header de la página -->
            <div class="top-info-div">
                <div class="row align-items-center">
                    <div class="col">
                        <h5 style="margin: 0; color: var(--main); font-weight: 600;">TÍTULO DE LA PÁGINA</h5>
                    </div>
                    <div class="col text-end">
                        <!-- Botones de acción aquí -->
                        <button type="button" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Nuevo</button>
                        <button type="button" class="btn btn-secondary"><i class="fa-solid fa-download"></i> Exportar</button>
                    </div>
                </div>
            </div>

            <!-- Contenido de la página -->
            <div id="pprint" class="container-fluid mt-3">
                
                <!-- Sección 1 -->
                <div class="row mb-4">
                    <div class="col-md-8">
                        <h3>Lorem Ipsum Dolor Sit Amet</h3>
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.</p>
                        <p>Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo.</p>
                    </div>
                    <div class="col-md-4">
                        <img src="https://picsum.photos/400/300?random=1" class="img-fluid rounded shadow-sm" alt="Imagen 1">
                    </div>
                </div>

                <!-- Sección 2 -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <img src="https://picsum.photos/400/300?random=2" class="img-fluid rounded shadow-sm" alt="Imagen 2">
                    </div>
                    <div class="col-md-8">
                        <h3>Nemo Enim Ipsam Voluptatem</h3>
                        <p>Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem.</p>
                        <p>Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?</p>
                    </div>
                </div>

                <!-- Sección 3 -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h3>At Vero Eos et Accusamus</h3>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <img src="https://picsum.photos/400/300?random=3" class="img-fluid rounded shadow-sm" alt="Imagen 3">
                            </div>
                            <div class="col-md-4 mb-3">
                                <img src="https://picsum.photos/400/300?random=4" class="img-fluid rounded shadow-sm" alt="Imagen 4">
                            </div>
                            <div class="col-md-4 mb-3">
                                <img src="https://picsum.photos/400/300?random=5" class="img-fluid rounded shadow-sm" alt="Imagen 5">
                            </div>
                        </div>
                        <p>At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident, similique sunt in culpa qui officia deserunt mollitia animi, id est laborum et dolorum fuga.</p>
                    </div>
                </div>

                <!-- Sección 4 -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h3>Temporibus Autem Quibusdam</h3>
                        <p>Et harum quidem rerum facilis est et expedita distinctio. Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id quod maxime placeat facere possimus, omnis voluptas assumenda est, omnis dolor repellendus.</p>
                        <p>Temporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet ut et voluptates repudiandae sint et molestiae non recusandae. Itaque earum rerum hic tenetur a sapiente delectus, ut aut reiciendis voluptatibus maiores alias consequatur aut perferendis doloribus asperiores repellat.</p>
                        <img src="https://picsum.photos/600/400?random=6" class="img-fluid rounded shadow-sm mt-3" alt="Imagen 6">
                    </div>
                    <div class="col-md-6">
                        <h3>Hanc Ego Cum Teneam</h3>
                        <p>Hanc ego cum teneam sententiam, quid est cur verear ne ad eam non possim accommodare Torquatos nostros? quos tu paulo ante cum memoriter, tum etiam erga nos amice et benivole collegisti, nec me tamen laudandis maioribus meis corrupisti nec segniorem ad respondendum reddidisti.</p>
                        <p>Quorum facta quem ad modum, quaeso, interpretaris? sicine eos censes aut in armatum hostem impetum fecisse aut in liberos atque in sanguinem suum tam crudelis fuisse, nihil ut de utilitatibus, nihil ut de commodis suis cogitarent?</p>
                        <img src="https://picsum.photos/600/400?random=7" class="img-fluid rounded shadow-sm mt-3" alt="Imagen 7">
                    </div>
                </div>

                <!-- Sección 5 - Más contenido para scroll -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h3>Conclusión</h3>
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
                        <div class="row mt-4">
                            <div class="col-md-3">
                                <img src="https://picsum.photos/300/300?random=8" class="img-fluid rounded shadow-sm" alt="Imagen 8">
                            </div>
                            <div class="col-md-3">
                                <img src="https://picsum.photos/300/300?random=9" class="img-fluid rounded shadow-sm" alt="Imagen 9">
                            </div>
                            <div class="col-md-3">
                                <img src="https://picsum.photos/300/300?random=10" class="img-fluid rounded shadow-sm" alt="Imagen 10">
                            </div>
                            <div class="col-md-3">
                                <img src="https://picsum.photos/300/300?random=11" class="img-fluid rounded shadow-sm" alt="Imagen 11">
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            
        </div>
    </div>
</div>

<!-- Botón Back to Top -->
<a href="#" class="cd-top"></a>

<?php include("footer.php");?>

<!-- JavaScript de Bootstrap 5 y dependencias -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js" integrity="sha384-cuYeSxntonz0PPNlHhBs68uyIAVpIIOZZ5JqeqvYYIcEL727kskC66kF92t6Xl2V" crossorigin="anonymous"></script>
<script src="app.js"></script>

<script>
$(document).ready(function() {
    
    // Tu código JavaScript aquí
    
});
</script>

</body>
</html>