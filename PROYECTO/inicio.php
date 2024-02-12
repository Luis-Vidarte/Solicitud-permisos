<?php session_start();
    if (isset($_SESSION['id_usuario']) && isset($_SESSION['dni'])) {

        $nombre = $_SESSION['nombre'];
        $id_cargo = $_SESSION['id_cargo'];
        
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Empresa Agroindustrial Pomalca</title>
    <link rel="stylesheet" href="css/inicio.css">
</head>
<body>

    <header>
    <img src="images\logo2.jpeg" class="img-logo">
        <a href="#" class=logo> 
            EMPRESA AGROINDUSTRIAL POMALCA
            <br>
            <?php echo "Bienvenido  " . $nombre; ?>
        </a>
        
        <input type="checkbox" id="menu-bar">
        <label for="menu-bar">Menu</label>
        <nav class="navbar">
            <ul>
                <li><a href="#">Inicio</a></li>
                <li><a href="#">Permisos</a>
                    <ul>
                        <li><a href="solicitar_permiso.php">Solicitar Permiso</a></li>
                        <?php
                    // Check if id_cargo is not equal to 2
                            if ($id_cargo != 2 ) {
                                echo '<li><a href="aceptar_permiso.php">Aceptar Permiso</a></li>';
                            }
                        ?>
                    </ul>
                </li>
                <li><a href="#">Revisar</a>
                    <ul>
                    <?php
                    // Check if id_cargo is not equal to 3
                        if ($id_cargo != 3 ) {
                            echo '<li><a href="estado_solicitud.php">Estado de la Solicitud</a></li>';
                        }
                     ?>
                    </ul>
                </li>
                <li><a href="#">Contactanos</a></li>
                <li><a><img src="images\cerrar.svg" class="cerrar"></a>
                    <ul>
                        <li><a href="Login\CerrarSesion.php"> Cerrar Sesion</a></li>
                    </ul>
                </li>
            </ul>

            
        </nav>
    </header>
        
            
</body>
</html>
<?php }else {
    header("location: ../login.php");
} ?>

