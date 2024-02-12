<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <script src="https://kit.fontawesome.com/a2dd6045c4.js" crossorigin="anonymous"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css\login.css">
    <title>Empresa Agroindustrial Pomalca</title>
</head>
<body>
    <section>
        <div class="contenedor">
            <div class="formulario">
                <form action="validar.php" method="POST">
                    <h2>Iniciar Sesión</h2>
                    <hr>
                    <?php
                        if(isset($_GET['error'])) {
                        ?>
                        <p class="error">
                            <?php
                            echo $_GET['error']
                            ?>
                        </p>
                    <?php 
                        }
                    ?>
                    <div class="input-contenedor">
                        <i class="fa-solid fa-envelope"></i>
                        <label for="#">DNI</label>
                        <input type="text" name="dni" autocomplete = "off" maxlength="8" minlength="8" required >
                    </div>

                    <div class="input-contenedor">
                        <i class="fa-solid fa-lock"></i>
                        <label for="#">Contraseña</label>
                        <input type="password" name="clave" autocomplete = "off" required>     
                    </div>

                    <button type="submit">Ingresar</button>
                </form>
            </div>
        </div>
    </section>
    
</body>
</html>