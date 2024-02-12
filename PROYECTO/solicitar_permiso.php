<?php
include("Config\conexion.php");

session_start();

// Comprobar si el usuario ha iniciado sesión
if (!isset($_SESSION['id_usuario'])) {
    // Redirigir a la página de inicio de sesión o gestionar la autenticación según sea necesario
    header("Location: login.php");
    exit();
}

// Obtener datos de usuario de la tabla de usuarios según el usuario que inició sesión
$userId = $_SESSION['id_usuario'];

// Prepare y ejecute la consulta SQL para recuperar datos del usuario
$sqlUser = "SELECT id_usuario, nombre, area FROM usuarios WHERE id_usuario = $userId";
$resultUser = $conexion->query($sqlUser);

// Compruebe si los datos del usuario se obtuvieron correctamente
if ($resultUser && $resultUser->num_rows > 0) {
    $userData = $resultUser->fetch_assoc();
    $defaultUserId = $userData['id_usuario'];
    $defaultNombre = $userData['nombre'];
    $defaultArea = $userData['area'];
} else {
    // Manejar el caso en el que falla la recuperación de datos del usuario
    $defaultUserId = "Unknown";
    $defaultNombre = "Unknown";
    $defaultArea = "Default Area";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recuperar datos del formulario
    $nombre = isset($_POST["nombre"]) ? $_POST["nombre"] : $defaultNombre;
    $fecha_sal = $_POST["fecha_sal"];
    $hora_sal = $_POST["hora_sal"];
    $fecha_ret = $_POST["fecha_ret"];
    $hora_ret = $_POST["hora_ret"];
    $area = isset($_POST["area"]) ? $_POST["area"] : $defaultArea;
    $tipo_ausencia = $_POST["tipo_ausencia"];
    $motivo = isset($_POST["motivo"]) ? $_POST["motivo"] : "";

    // Preparar y ejecutar la consulta SQL.
    $sql = "INSERT INTO solicitud_permisos (usuario, fecha_salida, hora_salida, fecha_retorno, hora_retorno, area, tipo_ausencia, motivo)
            VALUES ('$defaultUserId', '$fecha_sal', '$hora_sal', '$fecha_ret', '$hora_ret', '$area', '$tipo_ausencia', '$motivo')";

    if ($conexion->query($sql) === TRUE) {
        echo "<script>alert('Solicitud registrada');</script>";
        header("Location: inicio.php");
        //echo "Solicitud guardada exitosamente.";
    } else {
        echo "Error: " . htmlspecialchars($sql . "<br>" . $conexion->error);
    }
}

$tipo_ausencia1 = "SELECT * FROM tipo_ausencia";
$guardar_tipo = $conexion->query($tipo_ausencia1);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Empresa Agorindustrial Pomalca</title>
    <link rel="stylesheet" href="css\spermiso.css">
    <script>
        function validateForm() {
            var tipoAusencia = document.forms["solicitudForm"]["tipo_ausencia"].value;
            if (tipoAusencia === "") {
                alert("Por favor, seleccione un tipo de ausencia");
                return false;
            }
            return true;
        }
    </script>
</head>
<body>

    <h2>Solicitud de Permiso</h2>

    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
        <table>
            <tr>
                <td>Nombre:</td>
                <td colspan="3"><input type="text" name="nombre" value="<?php echo htmlspecialchars($defaultNombre); ?>" readonly></td>
            </tr>
            <tr>
                <td>Fecha de Salida:</td>
                <td><input type="date" name="fecha_sal" required></td>
                <td>Hora de Salida:</td>
                <td><input type="time" name="hora_sal" required></td>
            </tr>
            <tr>
                <td>Fecha de Retorno:</td>
                <td><input type="date" name="fecha_ret" ></td>
                <td>Hora de Retorno:</td>
                <td><input type="time" name="hora_ret" ></td>
            </tr>
            <tr>
                <td>Área:</td>
                <td colspan="3"><input type="text" name="area" value="<?php echo htmlspecialchars($defaultArea); ?>" readonly ></td>
            </tr>
            <tr>
                <td>Tipo de Ausencia:</td>
                <td colspan="3">
                    <select name="tipo_ausencia">
                        <option value="">Seleccione un tipo</option>
                        <?php while($row=$guardar_tipo->fetch_assoc()){?>
                        <option value="<?php echo $row['id_tipo_ausencia'];?>"><?php echo $row['nombre_tipo_ausencia'];?></option>
                        <?php } ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Motivo:</td>
                <td colspan="3"><textarea name="motivo" rows="4" cols="50" required></textarea></td>
            </tr>
            <input type="hidden" name="id_usuario" value="<?php echo htmlspecialchars($defaultUserId); ?>">
            <tr>
                <td colspan="4"><input type="submit" name="enviar" value="Enviar Solicitud"></td>
            </tr>
        </table>
    </form>
    <a href="inicio.php" class="btn bt-1">
        Volver a Inicio
    </a>
</body>
</html>

