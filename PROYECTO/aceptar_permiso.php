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

// Obtener el cargo del usuario actual
$sqlCargo = "SELECT id_cargo FROM usuarios WHERE id_usuario = $userId";
$resultCargo = $conexion->query($sqlCargo);

// Comprobar si los datos del cargo se obtuvieron correctamente
if ($resultCargo && $resultCargo->num_rows > 0) {
    $cargoData = $resultCargo->fetch_assoc();
    $idCargo = $cargoData['id_cargo'];
} else {
    // Manejar el caso en el que falla la recuperación de datos del cargo
    $idCargo = 0; // Asigna un valor predeterminado
}

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
        header("Location: solicitar_permiso.php");
        //echo "Solicitud guardada exitosamente.";
    } else {
        echo "Error: " . htmlspecialchars($sql . "<br>" . $conexion->error);
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tabla de Solicitud de Permisos</title>
    <link rel="stylesheet" href="css\apermiso.css">
    <script src="js\boton.js" defer></script>
</head>
<body>

<h2>Solicitudes</h2>

            <div class="mostrar">
                    <label for="num_registros" class="col-form-label">Mostrar: </label>
            </div>

            <div class="paginacion">
                    <select name="num_registros" id="num_registros" class="form-select">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
            </div>
            <div class="text_registros">
                    <label for="num_registros" class="col-form-label">registros </label>
            </div>
<?php

if ($idCargo == 1 || $defaultArea == 'RRHH') {
    // Si el cargo es 1 (Administrador), mostrar todas las solicitudes sin aplicar la condición del área
    $sql = "SELECT solicitud_permisos.*, usuarios.nombre AS nombre_usuario, tipo_ausencia.nombre_tipo_ausencia
            FROM solicitud_permisos
            INNER JOIN usuarios ON solicitud_permisos.usuario = usuarios.id_usuario
            INNER JOIN tipo_ausencia ON solicitud_permisos.tipo_ausencia = tipo_ausencia.id_tipo_ausencia";
} else {
    // Para otros cargos, aplicar la condición del área
    $sql = "SELECT solicitud_permisos.*, usuarios.nombre AS nombre_usuario, tipo_ausencia.nombre_tipo_ausencia
            FROM solicitud_permisos
            INNER JOIN usuarios ON solicitud_permisos.usuario = usuarios.id_usuario
            INNER JOIN tipo_ausencia ON solicitud_permisos.tipo_ausencia = tipo_ausencia.id_tipo_ausencia
            WHERE usuarios.area = '$defaultArea'";
}


$result = $conexion->query($sql);


if ($result->num_rows > 0) {
    // Imprimir la tabla HTML
    echo "<table>
            <thead>
                <tr>
                    <th>Nombre del Trabajador</th>
                    <th>Area</th>
                    <th>Fecha de Salida</th>
                    <th>Hora de Salida</th>
                    <th>Fecha de Retorno</th>
                    <th>Hora de Retorno</th>
                    <th>Tipo de Ausencia</th>
                    <th >Estado</th>
                    <th colspan='2'>Motivo</th>
                </tr>
            </thead>
            <tbody>";

    // Iterar sobre los resultados y mostrar cada fila en la tabla
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['nombre_usuario']}</td>
                <td>{$row['area']}</td>
                <td>{$row['fecha_salida']}</td>
                <td>{$row['hora_salida']}</td>
                <td>{$row['fecha_retorno']}</td>
                <td>{$row['hora_retorno']}</td>
                <td>{$row['nombre_tipo_ausencia']}</td>
                <td>";

        // Agregar botones en la misma celda de la columna "Estado"
        echo "<button onclick='actualizarEstado({$row['id_solicitud']}, \"aprobar\")'>Aprobar</button>";
        echo "<button onclick='actualizarEstado({$row['id_solicitud']}, \"rechazar\")'>Rechazar</button>";

        echo "</td>
                
                <td>{$row['motivo']}</td>
            </tr>";
    }

    echo "</tbody></table>";
} else {
    echo "No se encontraron resultados.";
}

// Cerrar conexión
$conexion->close();
?>
<a href="inicio.php" class="btn bt-1">
    Volver a Inicio
</a>
<a href="pdf.php" class="pdf">
    Generar pdf
</a>
<a href="excel.php" class="excel">
    Generar excel
</a>
</body>

<script>
    getData()

    document.getElementById("num_registros").addEventListener("change", getData)

    function getData() {
        let num_registros = document.getElementById("num_registros").value

        let content = document.getElementById("content")
        let url = "load.php"
        lew formaData = new FormData()
        formaData.append('registros', num_registros)

        fetch(url, {
            method: "POST",
            body: formaData
            }).then(response => response.json())
                .then(data => {
                    content.innerHTML = data.data
            }).catch(err => console.log(err))

    }
</script>
</html>


