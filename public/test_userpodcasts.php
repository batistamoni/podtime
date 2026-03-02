<?php

session_start();

require_once "../config/database.php";
require_once "../src/models/UserPodcast.php";

$userPodcastModel = new UserPodcast($pdo);

// Simulamos usuario logueado (id 1)
$result = $userPodcastModel->addPodcast(1, 1);

var_dump($result);