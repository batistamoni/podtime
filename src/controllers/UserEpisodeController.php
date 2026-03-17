<?php

session_start();

// Si no hay usuario logueado → fuera
if (!isset($_SESSION["user_id"])) {
    header("Location: ../../public/login.php");
    exit;
}

// Conexión y modelo
require_once "../../config/database.php";
require_once "../models/UserEpisode.php";

// Crear modelo
$userEpisodeModel = new UserEpisode($pdo);

// Comprobar que llega el POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Usuario actual
    $userId = $_SESSION["user_id"];

    // Episodio sobre el que actuamos
    $episodeId = $_POST["episode_id"];

    // Acción (add o remove)
    // Si no viene → por defecto "add"
    $action = $_POST["action"] ?? "add";

    // Decidir qué hacer
    if ($action === "remove") {

        // Quitar de escuchados
        $userEpisodeModel->unmarkAsListened($userId, $episodeId);

    } else {

        // Marcar como escuchado
        $userEpisodeModel->markAsListened($userId, $episodeId);
    }

    // Redirigir de vuelta (importante para no reenviar formulario)
     $podcastId = $_POST["podcast_id"];

    header("Location: " . $_SERVER["HTTP_REFERER"]);
    exit;
}