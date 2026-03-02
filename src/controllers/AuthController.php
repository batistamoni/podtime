<?php

require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/../models/User.php";

session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = $_POST["email"];
    $password = $_POST["password"];

    $userModel = new User($pdo);

    $user = $userModel->findByEmail($email);

    if ($user && password_verify($password, $user["password"])) {

        // Guardamos datos en sesión
        $_SESSION["user_id"] = $user["id"];
        $_SESSION["username"] = $user["username"];

        header("Location: ../../public/dashboard.php");
        exit;

    } else {

        echo "Credenciales incorrectas";
    }
}