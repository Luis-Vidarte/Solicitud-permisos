<?php
    session_start();
    include('Config\conexion.php');

    if (isset($_POST['dni']) && isset($_POST['clave'])) {
    
        function validar($data)
        {
            $data = trim($data);
            $data = stripslashes($data);
            $data = htmlspecialchars($data);
    
            return $data;
        }
    
        $dni = validar($_POST['dni']);
        $clave = validar($_POST['clave']);

        if(empty($dni)) {
            header("Location: login.php?error=El usuario es requerido");
            exit();
        }elseif(empty($clave)) {
            header("Location: login.php?error=La clave es requerida");
            exit();
        }else {
            $sql = "SELECT * FROM usuarios WHERE dni = '$dni' AND clave='$clave'";
            $resultado = mysqli_query($conexion,$sql);

            if(mysqli_num_rows($resultado) === 1) {
                $row = mysqli_fetch_assoc($resultado);
                if($row['dni'] === $dni && $row['clave'] === $clave) {
                    $_SESSION['dni'] = $row['dni'];
                    $_SESSION['nombre'] = $row['nombre'];
                    $_SESSION['id_cargo'] = $row['id_cargo'];
                    $_SESSION['id_usuario'] = $row['id_usuario'];
                    header("Location: inicio.php");
                    exit();
                }else {
                    header("Location: login.php?error=El usuario o la clvae son incorrectas");
                    exit();
                }
            }else {
                header("Location: login.php?error=El usuario o la clave son incorrectas");
                exit();
            }
        }
    } else {
        header("Location: login.php");
                exit();
    }
