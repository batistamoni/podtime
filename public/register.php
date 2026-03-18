<?php

require_once "../config/database.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = $_POST["password"];

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (username, email, password) VALUES (:username, :email, :password)";
    
    $stmt = $pdo->prepare($sql);

    try {
        $stmt->execute([
            ":username" => $username,
            ":email" => $email,
            ":password" => $hashedPassword,
        ]);

        echo "Usuario registrado correctamente 🚀";

    } catch (PDOException $e) {
        echo "Error al registrar usuario";
    }
}
?>

<?php require_once "partials/header.php"; ?>

<div class="max-w-md mx-auto bg-white p-6 rounded-lg shadow">

    <h2 class="text-2xl font-bold mb-4 text-center">Registro</h2>

    <form action="register.php" method="POST" class="space-y-4">

        <input 
            type="text" 
            name="username" 
            placeholder="Nombre de usuario" 
            required
            class="w-full border p-2 rounded"
        >

        <input 
            type="email" 
            name="email" 
            placeholder="Email" 
            required
            class="w-full border p-2 rounded"
        >

        <input 
            type="password" 
            name="password" 
            placeholder="Contraseña" 
            required
            class="w-full border p-2 rounded"
        >

        <button 
            type="submit"
            class="w-full bg-purple-600 text-white p-2 rounded hover:bg-purple-700"
        >
            Registrarse
        </button>

    </form>

</div>

<?php require_once "partials/footer.php"; ?>