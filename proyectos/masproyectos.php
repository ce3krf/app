<?php 
// ************************************************************
// Autor: Marcelo Jiménez
// Fulltrust Software
// https://www.fulltrust.net
// Fecha última actualización: 25 de Agosto de 2023
// ************************************************************
?>
<?php
@session_start();
	
include("db.php");	
	

$sql="set names utf8";
if(!$result = $db->query($sql)){
    die('Hay un error [' . $db->error . ']');
}


$sql="select * from proyectos where proyectos_ubicacion='".$_GET['u']."'";
if(!$result = $db->query($sql)){
    die('Hay un error [' . $db->error . ']');
}
$rowu = $result->fetch_assoc();
$row_cnt = $result->num_rows;


?>


<!doctype html>

<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">	


<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js" integrity="sha512-GsLlZN/3F2ErC5ifS5QtgpiJtWd43JWSuIgh7mbzZ8zBps+dvLusV+eNQATqgA/HdeKFVgA5v3S/cIrLF7QnIg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>




<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

<script src="https://use.fontawesome.com/releases/v5.15.3/js/all.js" crossorigin="anonymous"></script>

<script src="js/Bing.js"></script> 

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js" integrity="sha512-GsLlZN/3F2ErC5ifS5QtgpiJtWd43JWSuIgh7mbzZ8zBps+dvLusV+eNQATqgA/HdeKFVgA5v3S/cIrLF7QnIg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<link rel="stylesheet" href="css/styles.css">

<script>
function url(dire){
  jconfirm.instances[0].close();
  var dlg = $.confirm({
                title: '',
                type: 'blue',
                typeAnimated: true,
                closeIcon: true,
                useBootstrap: false,
                boxWidth:'90%',
                buttons: {
                   info: {
                    text: 'Cerrar', 
                    btnClass: 'btn-blue',
                    action: function(){}
                    }
               },

                content: 'url:'+dire
            }); 
}  
</script>  

</head>

<body>

<div class="container">

<div class="row">
    <div class="col">
    <h4>Proyectos: <?php echo $rowu["proyectos_ubicacion"]?></h4> 
    </div>
  </div>
  <div style="height:20px;"></div>

<?php do { ?>

  <div class="row">
    <div class="col">
    <h5 style="color:var(--primero); font-weight:bold"><?php echo $rowu["proyectos_nombre"]?></h5> 
    </div>
  </div>

<div class="row">
    <div class="col">
      <b>ÁREA ESTRATÉGICA:</b> <?php echo $rowu["proyectos_area"]?>      
    </div>
  </div>  


<div class="row">
    <div class="col">
      <b>OBJETIVO DEL PROYECTO:</b> <?php echo $rowu["proyectos_objetivo"]?>      
    </div>
  </div>  


  <div class="row">
    <div class="col">
    <div style="height:10px;"></div>
    <img style="width:24px" src="img/document.png">&nbsp;<a onclick="url('verproyecto.php?proyectos_id=<?php echo $rowu["proyectos_id"]?>');" style="text-decoration:none; color:#d63441" href="javascript:;">CLICK AQUÍ PARA VER LOS DETALLES DEL PROYECTO</a>
    </div>
  </div>  

<hr>



<p>&nbsp;</p>

<?php } while ( $rowu = $result->fetch_assoc() ); ?>
</div>


<script>
        function gpdf() {

          var element = document.getElementById('mainContainer');

          var opt = {
            margin:       1,
            filename:     'Proyecto.pdf',
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { scale: 2 },
            jsPDF:        { unit: 'in', format: 'letter', orientation: 'portrait' }
          };

          // New Promise-based usage:
          html2pdf().set(opt).from(element).save();
          

        };

</script>


</body>
</html>