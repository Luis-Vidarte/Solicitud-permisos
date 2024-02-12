<?php

        $host = "localhost:3307";
        $user = "root";
        $password = "";
        $db = "proyecto";

        // Crear una conexión a la base de datos
        $conexion = new mysqli($host, $user, $password, $db);

        // Verificar si hay errores en la conexión
        if ($conexion->connect_error) {
            die("Error al conectar a la base de datos: " . $conexion->connect_error);
        }

        return $conexion;
  
