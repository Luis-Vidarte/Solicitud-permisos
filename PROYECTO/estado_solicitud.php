<?php
include("Config\conexion.php");
session_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tabla de Solicitud de Permisos</title>
    <link rel="stylesheet" href="css\epermiso.css">
</head>
<body>

    <h2>Estado del Permiso</h2>

    <?php
    // Verifica si el usuario ha iniciado sesión.
    if (!isset($_SESSION['id_usuario'])) {
        header("Location: login.php");
        exit();
    }

    // Obtenga la identificación del usuario que inició sesión y id_cargo
    $loggedInUserId = $_SESSION['id_usuario'];
    $loggedInUserRole = $_SESSION['id_cargo'];

    // Consulta SQL para obtener datos de la tabla solicitud_permisos
    $sql = "";

    if ($loggedInUserRole == 1) {
        $sql = "SELECT solicitud_permisos.*, usuarios.nombre AS nombre_usuario, tipo_ausencia.nombre_tipo_ausencia, estado.nombre_estado
        FROM solicitud_permisos
        LEFT JOIN usuarios ON solicitud_permisos.usuario = usuarios.id_usuario
        LEFT JOIN tipo_ausencia ON solicitud_permisos.tipo_ausencia = tipo_ausencia.id_tipo_ausencia
        LEFT JOIN estado ON solicitud_permisos.estado = estado.id_estado";
    } else {
        $sql = "SELECT solicitud_permisos.*, usuarios.nombre AS nombre_usuario, tipo_ausencia.nombre_tipo_ausencia, estado.nombre_estado
        FROM solicitud_permisos
        LEFT JOIN usuarios ON solicitud_permisos.usuario = usuarios.id_usuario
        LEFT JOIN tipo_ausencia ON solicitud_permisos.tipo_ausencia = tipo_ausencia.id_tipo_ausencia 
        LEFT JOIN estado ON solicitud_permisos.estado = estado.id_estado
        WHERE solicitud_permisos.usuario = $loggedInUserId";
    }

    $result = $conexion->query($sql);

    if ($result->num_rows > 0) {
        echo "<table>
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Fecha de Salida</th>
                        <th>Hora de Salida</th>
                        <th>Fecha de Retorno</th>
                        <th>Hora de Retorno</th>
                        <th>Tipo de Ausencia</th>
                        <th>Estado</th>
                        <th>Motivo</th>
                    </tr>
                </thead>
                <tbody>";

        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['nombre_usuario']}</td>
                    <td>{$row['fecha_salida']}</td>
                    <td>{$row['hora_salida']}</td>
                    <td>{$row['fecha_retorno']}</td>
                    <td>{$row['hora_retorno']}</td>
                    <td>{$row['nombre_tipo_ausencia']}</td>
                    <td>{$row['nombre_estado']}</td>
                    <td>{$row['motivo']}</td>
                </tr>";
        }

        echo "</tbody></table>";
    } else {
        echo "No se encontraron resultados.";
    }

    $conexion->close();
    ?>

    <a href="inicio.php" class="btn bt-1">
        Volver a Inicio
    </a>
</body>
</html>
