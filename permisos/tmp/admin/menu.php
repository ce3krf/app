<?php 
// ************************************************************
// Autor: Marcelo Jiménez
// Fulltrust Software
// https://www.fulltrust.net
// Fecha última actualización: 2024-12-31 10:22:30
// ************************************************************

//error_reporting(E_ALL);
//ini_set('display_errors', 1);

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

<link rel="icon" type="image/png" href="../favicon.png">

<script src="js/jquery-3.5.1.min.js"></script>	

    <!-- Enlace a CSS de Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">

    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>



    <script src="functions.js"></script>

    <link rel="stylesheet" type="text/css" href="../css/styles.css"/>    




    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <style>
#pprint {
    margin: 10px !important;
}
</style>    

<script>
    async function pdf() {
        const element = document.getElementById('pprint');
        
        // Capturar el contenido del elemento con html2canvas
        const canvas = await html2canvas(element, {
            scale: window.devicePixelRatio, // Escala alta para mayor fidelidad
            useCORS: true, // Manejo de recursos externos
            scrollY: 0 // Evitar desplazamientos no deseados
        });

        // Convertir el canvas a imagen
        const imgData = canvas.toDataURL('image/jpeg', 0.98);

        // Crear una instancia de jsPDF
        const { jsPDF } = window.jspdf;
        const pdf = new jsPDF({
            orientation: 'landscape', // Orientación del PDF
            unit: 'in',
            format: 'letter' // Formato de la página
        });

        // Obtener las dimensiones del canvas y calcular la escala para el PDF
        const pdfWidth = pdf.internal.pageSize.getWidth();
        const pdfHeight = pdf.internal.pageSize.getHeight();
        const imgWidth = canvas.width / 96; // Convertir píxeles a pulgadas
        const imgHeight = canvas.height / 96;
        const aspectRatio = imgWidth / imgHeight;

        let finalWidth = pdfWidth;
        let finalHeight = pdfWidth / aspectRatio;

        if (finalHeight > pdfHeight) {
            finalHeight = pdfHeight;
            finalWidth = pdfHeight * aspectRatio;
        }

        // Agregar la imagen al PDF
        pdf.addImage(imgData, 'JPEG', 0, 0, finalWidth, finalHeight);

        // Agregar numeración de páginas (si se multiplica en varias)
        const totalPages = pdf.internal.getNumberOfPages();
        const pageWidth = pdf.internal.pageSize.getWidth();
        const pageHeight = pdf.internal.pageSize.getHeight();

        for (let i = 1; i <= totalPages; i++) {
            pdf.setPage(i);
            pdf.setFontSize(10);
            pdf.text(`Página ${i} de ${totalPages}`, pageWidth / 2, pageHeight - 0.5, { align: 'center' });
        }

        // Descargar el PDF
        pdf.save('graficos.pdf');
    }
</script>


</head>
<body>

<?php include("header.php");?>

    <div class="container-fluid">
        <div class="row">

        <?php include("sidebar.php");?>

            <!-- Área de trabajo central -->
            <main role="main" class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h5>BIENVENIDOS</h5>
                </div>
                <div class="container">
                




<div id="pprint">

<p><?php echo $row_param["parametros_titulo"];?></p>
<div style="height: 25px; clear:both"></div>

<!------------------------------------------------------------------------>
<div class="row">
    <div class="col-md-6 text-center">
        <!-- Gráfico por estado -->
        <h5>PROYECTOS POR ESTADO</h5>
        <canvas id="porEstado" class="chart-canvas"></canvas>
    </div>
    <div class="col-md-6 text-center">
        <!-- Gráfico por área -->
        <h5>PROYECTOS POR ÁMBITO</h5>
        <canvas id="myChart" class="chart-canvas"></canvas>
    </div>
</div>

<style>
    .chart-canvas {
        width: 100% !important;
        height: 400px !important; /* Altura fija para ambos gráficos */
    }
</style>

<script>
    // Colores tradicionales para gráficos
    const traditionalColors = [
        '#FF6384', // Rojo
        '#36A2EB', // Azul
        '#FFCE56', // Amarillo
        '#4BC0C0', // Verde azulado
        '#9966FF', // Morado
        '#FF9F40'  // Naranja
    ];

    // Gráfico por estado
    $(document).ready(function() {
        var token = $("#sid").val();
        $.ajax({
            url: 'api/grafico_por_estado.php?token=' + token,
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                const ctx = document.getElementById('porEstado').getContext('2d');
                new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: data.estados,
                        datasets: [{
                            label: '# de Proyectos',
                            data: data.cuantos,
                            backgroundColor: traditionalColors.slice(0, data.estados.length),
                            borderColor: '#ffffff',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false, // Asegura que se ajuste al alto definido
                    }
                });
            },
            error: function(error) {
                console.error("Error obteniendo los datos por estado", error);
            }
        });
    });

    // Gráfico por área
    $(document).ready(function() {
        var token = $("#sid").val();
        $.ajax({
            url: 'api/grafico_por_area.php?token=' + token,
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                const areas = data.map(item => item.area);
                const counts = data.map(item => item.cuantos);

                const ctx = document.getElementById('myChart').getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: areas,
                        datasets: [{
                            label: 'Cantidad de proyectos por área',
                            data: counts,
                            backgroundColor: 'rgba(54, 162, 235, 0.5)', // Azul claro
                            borderColor: 'rgba(54, 162, 235, 1)', // Azul oscuro
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false, // Asegura que se ajuste al alto definido
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            },
            error: function(error) {
                console.error("Error obteniendo los datos por área", error);
            }
        });
    });
</script>

<!------------------------------------------------------------------------>

</div>


<div style="height: 50px; clear:both"></div>




<div class="row">
    <div class="col text-center">
        <button onclick="pdf();" type="button" class="btn btn-secondary" >Exportar como PDF</button>
    </div>  
</div>

<div style="height: 50px;"></div>









                </div>
            </main>
        </div>
    </div>

<?php include("footer.php");?>

    <!-- Enlace a JavaScript de Bootstrap 5 y dependencias -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js" integrity="sha384-cuYeSxntonz0PPNlHhBs68uyIAVpIIOZZ5JqeqvYYIcEL727kskC66kF92t6Xl2V" crossorigin="anonymous"></script>
    <script src="app.js"></script>
</body>
</html>
