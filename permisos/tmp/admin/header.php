    <!-- Barra de navegación superior -->
    <div class="topdiv"></div> 
    <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: var(--top); padding-top:0px !important; padding-bottom:0px !important;">
        <div class="container-fluid">
            <a class="navbar-brand" href="menu.php"><img src="../img/logomuni.png" style="margin-left:20px; width:120px; margin-bottom:10px"></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">

                <!-- Menu -->
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Menú
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="menu.php">Inicio</a></li>
                            <li><a class="dropdown-item" href="proyectos.php">Proyectos</a></li>
                            <li><a class="dropdown-item" href="avance.php">Avance</a></li>
                            <li><a class="dropdown-item" href="javascript:void(0);"><hr></a></li>
                            <li><a class="dropdown-item" href="usuarios.php">Usuarios</a></li>
                        </ul>
                    </li>
                </ul>


                <!-- Panel de usuario -->
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="<?php echo $usuarios_foto;?>" alt="" style="
                            width: 35px;
                            height: 35px; 
                            object-fit: cover;
                            float: left;
                            vertical-align: middle !important;
                            margin-right: 5px;
                            border-radius: 50%;
                            margin-top: -5px;
                        ">
                            <?php echo $_SESSION["nombre"];?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="usuarios_editar.php?usuarios_id=<?php echo encrypt('1');?>">Perfil</a></li>
                            <li><a class="dropdown-item" href="logout.php">Cerrar sesión</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
