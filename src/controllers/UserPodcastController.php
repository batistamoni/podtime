<?php

session_start();

require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/../models/UserPodcast.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: ../../public/login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $userId = $_SESSION["user_id"];
    $podcastId = $_POST["podcast_id"] ?? null;
    $action = $_POST["action"] ?? null;

    if (!$podcastId || !$action) {
        header("Location: ../../public/dashboard.php");
        exit;
    }

    $userPodcastModel = new UserPodcast($pdo);

    try {
        if ($action === "add") {
            $userPodcastModel->addPodcast($userId, $podcastId);
        }

        if ($action === "remove") {
            $userPodcastModel->removePodcast($userId, $podcastId);
        }

    } catch (PDOException $e) {
        // Ignoramos errores (ej: duplicados)
    }

    header("Location: ../../public/dashboard.php");
    exit;
}