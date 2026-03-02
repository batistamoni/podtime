<!DOCTYPE html>
<html>
    <head>
        <title>Login - PodTime</title>
    </head>
    <body>
        <h2>Login</h2>
        
        <form action="../src/controllers/AuthController.php" method="POST">
            <input type="email" name="email" placeholder="Email" required><br><br>
            <input type="password" name="password" placeholder="Contraseña" required><br><br>
            <button type="submit">Iniciar sesión</button>
        </form>
        
    </body>
</html>