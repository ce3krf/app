            <!-- Menú colapsable a la izquierda -->
            <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block sidebar-dark collapse" style="background-color:var(--bg2) !important">
                <div class="position-sticky pt-3">

                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="menu.php" style="color: #000000 !important;">
                                <span data-feather="home"></span>
                                <i class="fa-solid fa-caret-right fa-2xs" style="color: #FFD43B;"></i>&nbsp;Inicio
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="proyectos.php" style="color: #000000 !important;">
                                <span data-feather="file"></span>
                                <i class="fa-solid fa-caret-right fa-2xs" style="color: #FFD43B;"></i>&nbsp;Proyectos
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="avance.php" style="color: #000000 !important;">
                                <span data-feather="file"></span>
                                <i class="fa-solid fa-caret-right fa-2xs" style="color: #FFD43B;"></i>&nbsp;Avance
                            </a>
                        </li>


                        <li class="nav-item">
                            <a class="nav-link" href="avancei.php" style="color: #000000 !important;">
                                <span data-feather="file"></span>
                                <i class="fa-solid fa-caret-right fa-2xs" style="color: #FFD43B;"></i>&nbsp;Avance financiero
                            </a>
                        </li>


                        <li class="nav-item">
                        <a class="nav-link" href="javascript:;" style="color: #000000 !important;">
                                <span data-feather="file"></span>
                                
                            </a>
                        </li>                        

                        <li class="nav-item">
                            <a class="nav-link" href="backup.php" style="color: #000000 !important;">
                                <span data-feather="file"></span>
                                <i class="fa-solid fa-caret-right fa-2xs" style="color: #FFD43B;"></i>&nbsp;Hacer respaldo
                            </a>
                        </li>




                        <li class="nav-item">
                            <a class="nav-link" href="usuarios.php" style="color: #000000 !important;">
                                <span data-feather="layers"></span>
                                <i class="fa-solid fa-caret-right fa-2xs" style="color: #FFD43B;"></i>&nbsp;Usuarios
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>



            <script>
      function avance(area, tipo){
        
        var x = Math.random();
        var w_avance = $.confirm({
                  title: '',
                  type: 'blue',
                  boxWidth: '95%',
                  typeAnimated: true,
                  closeIcon: true,
                  useBootstrap: false,
                  confirmButtonColor: '#d296dd',
                  buttons: {
                     info: {
                      text: 'Continuar', 
                      btnClass: 'btn-blue',
                      action: function(){}
                      }
                 },
    
                  content: 'url:../tabla.php?area='+area+'&tipo='+tipo+"&x="+x
              }); 		
    }	
</script>	            