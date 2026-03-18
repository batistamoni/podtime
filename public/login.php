<?php require_once "partials/header.php"; ?>

<div class="max-w-md mx-auto bg-white p-6 rounded-lg shadow">

    <h2 class="text-2xl font-bold mb-4 text-center">Login</h2>

    <form action="../src/controllers/AuthController.php" method="POST" class="space-y-4">
        
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
            Iniciar sesión
        </button>

    </form>

</div>

<?php require_once "partials/footer.php"; ?>