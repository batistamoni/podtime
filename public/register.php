<?php

// Incluimos el archivo de conexión a la base de datos
// require_once asegura que solo se cargue una vez y detiene el script si falla
require_once "../config/database.php";

// Comprobamos si el formulario se ha enviado mediante método POST
// Esto evita que el código de inserción se ejecute cuando simplemente cargamos la pág
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Recogemos los datos enviados desde el formulario
    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = $_POST["password"];

    // Encriptamos la contraseña antes de guardarla en la bbdd
    // Nunca se deben almacenar contraseñas en texto plano
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Creamos la consulta SQL usando parámetros nombrados para evitar inyección SQL
    $sql = "INSERT INTO users (username, email, password) VALUES (:username, :email, :password)";
    
    // Preparamos la consulta
    // PDO separa la estructura SQL de los datos reales
    $stmt = $pdo->prepare($sql);

    try {
        // Ejecutamos la consulta pasando los valores reales
        // PDO se encarga de sanitizarlos automáticamente
        $stmt->execute([
            ":username" => $username,
            ":email" => $email,
            ":password" => $hashedPassword,
        ]);

        echo "Usuario registrado correctamente 🚀";

    } catch (PDOException $e) {

        // Capturamos cualquier error (ej email duplicado)
        // En producción no se debería mostrar el mensaje real del error
        echo "Error al registrar usuario: " . $e->getMessage();
        }
    }
    ?>

<!DOCTYPE html>
<html>
    <head>
        <title>Registro - PodTime</title>
    </head>
    <body>
        <h2>Registro</h2>

        <form action="register.php" method="POST">
            <input type="text" name="username" placeholder="Nombre de usuario" required><br><br>
            <input type="email" name="email" placeholder="Email" required><br><br>
            <input type="password" name="password" placeholder="Contraseña" required><br><br>
            <button type="submit">Registrarse</button>
        </form>

    </body>
</html>