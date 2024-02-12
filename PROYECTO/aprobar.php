<?php
// aprobar.php

// Check if the user has the required permissions (id_cargo=3 or id_cargo=4)
session_start(); // Assuming you are using sessions to manage user authentication

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_SESSION['id_cargo'])) {
    $id_solicitud = $_GET['id'];
    $accion = $_GET['accion']; // Nuevo parámetro para identificar la acción (aprobar o rechazar)

    // Validate and sanitize the input
    $id_solicitud = filter_var($id_solicitud, FILTER_VALIDATE_INT);

    if ($id_solicitud === false || $id_solicitud === null || !in_array($accion, ['aprobar', 'rechazar'])) {
        // Invalid input or action, return an error response
        $response = array('status' => 'error', 'message' => 'Invalid request');
        http_response_code(400); // Bad Request
        echo json_encode($response);
        exit();
    }

    // Perform the approval/rejection logic here
    // For example, update the database

    include("Config\conexion.php");  // Include your database connection file

    // Determine the value for the appropriate estado column based on the user's id_cargo
    $estadoColumn = ($_SESSION['id_cargo'] == 3) ? 'estado_jefe' : 'estado_rrhh';
    $estadoValue = ($accion == 'aprobar') ? 'aprobada' : 'rechazada';

    // Update the appropriate estado column for the specified id_solicitud
    $sqlUpdate = "UPDATE solicitud_permisos SET $estadoColumn = ? WHERE id_solicitud = ?";
    $stmt = $conexion->prepare($sqlUpdate);
    $stmt->bind_param("si", $estadoValue, $id_solicitud);

    if ($stmt->execute()) {
        // Check if both estado_jefe and estado_rrhh are 'aprobada', then update the 'estado' column
        $sqlCheckApproval = "SELECT estado_jefe, estado_rrhh FROM solicitud_permisos WHERE id_solicitud = ?";
        $stmtCheckApproval = $conexion->prepare($sqlCheckApproval);
        $stmtCheckApproval->bind_param("i", $id_solicitud);
        $stmtCheckApproval->execute();
        $stmtCheckApproval->bind_result($estadoJefe, $estadoRRHH);
        $stmtCheckApproval->fetch();
        $stmtCheckApproval->close();

        if (($estadoJefe == 'aprobada' && $estadoRRHH == 'aprobada') || ($estadoJefe == 'rechazada' || $estadoRRHH == 'rechazada')) {
            // Update the 'estado' column accordingly
            $sqlUpdateEstado = "UPDATE solicitud_permisos SET estado = ? WHERE id_solicitud = ?";
            $stmtUpdateEstado = $conexion->prepare($sqlUpdateEstado);
        
            if ($stmtUpdateEstado) {
                // Set the estado value based on conditions
                $estado = ($estadoJefe == 'aprobada' && $estadoRRHH == 'aprobada') ? 1 : 3;
        
                $stmtUpdateEstado->bind_param("ii", $estado, $id_solicitud);
                $stmtUpdateEstado->execute();
        
                if ($stmtUpdateEstado->affected_rows > 0) {
                    // Update successful
                    $stmtUpdateEstado->close();
                } else {
                    // No rows were updated
                    echo "No rows were updated.";
                }
            } else {
                // Error in preparing the SQL statement
                echo "Error preparing statement: " . $conexion->error;
            }
        } else {
            // Neither approval nor rejection, update the 'estado' column to 2
            $sqlUpdateEstado = "UPDATE solicitud_permisos SET estado = 2 WHERE id_solicitud = ?";
            $stmtUpdateEstado = $conexion->prepare($sqlUpdateEstado);
        
            if ($stmtUpdateEstado) {
                $stmtUpdateEstado->bind_param("i", $id_solicitud);
                $stmtUpdateEstado->execute();
        
                if ($stmtUpdateEstado->affected_rows > 0) {
                    // Update successful
                    $stmtUpdateEstado->close();
                } else {
                    // No rows were updated
                    echo "No rows were updated.";
                }
            } else {
                // Error in preparing the SQL statement
                echo "Error preparing statement: " . $conexion->error;
            }
        }
        
        

        // Assuming the update was successful, return a success response
        $response = array('status' => 'success', 'message' => "Solicitud $accion");
        echo json_encode($response);
    } else {
        // Handle the case where the update fails
        $response = array('status' => 'error', 'message' => 'Error updating database');
        http_response_code(500); // Internal Server Error
        echo json_encode($response);
    }

    // Close the database connection
    $stmt->close();
    $conexion->close();

    exit();
} else {
    // If the user does not have the required permissions, return a forbidden response
    $response = array('status' => 'error', 'message' => 'Forbidden');
    http_response_code(403); // Forbidden
    echo json_encode($response);
    exit();
}
?>








