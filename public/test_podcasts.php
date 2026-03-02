<?php

require_once "../config/database.php";
require_once "../src/models/Podcast.php";

$podcastModel = new Podcast($pdo);

$podcasts = $podcastModel->getAll();

echo "<pre>";
print_r($podcasts);
echo "</pre>";