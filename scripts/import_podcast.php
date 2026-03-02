<?php

require_once __DIR__ . "/../config/database.php";

// ========================
// 1. Recibir url por GET
// ========================

$rssUrl = $_GET['rss'] ?? null;

if (!$rssUrl) {
    die("Debes proporcionar una URL RSS usando ?rss=URL");
}

// ========================
// 2. Cargar rss
// ========================

$rss = simplexml_load_file($rssUrl);

if (!$rss) {
    die("No se pudo cargar el RSS");
}

$channel = $rss->channel;

$title = (string) $channel->title;
$description = (string) $channel->description;

// Extraer imagen usando namespace itunes
$namespaces = $rss->getNamespaces(true);
$itunes = $channel->children($namespaces['itunes']);
$image = isset($itunes->image) 
    ? (string) $itunes->image->attributes()->href 
    : null;


// ========================
// 3. Comprobar si el podcast existe
// ========================

$sqlCheck = "SELECT id FROM podcasts WHERE title = :title LIMIT 1";
$stmtCheck = $pdo->prepare($sqlCheck);
$stmtCheck->execute([":title" => $title]);

$existingPodcast = $stmtCheck->fetch(PDO::FETCH_ASSOC);

if ($existingPodcast) {

    $podcastId = $existingPodcast['id'];
    echo "El podcast ya existe. Usando ID existente.<br>";

} else {

    $sqlPodcast = "INSERT INTO podcasts (title, description, image) 
                   VALUES (:title, :description, :image)";

    $stmtPodcast = $pdo->prepare($sqlPodcast);

    $stmtPodcast->execute([
        ":title" => $title,
        ":description" => $description,
        ":image" => $image
    ]);

    $podcastId = $pdo->lastInsertId();

    echo "Podcast insertado correctamente.<br>";
}


// ========================
// 4. IMPORTAR EPISODIOS
// ========================

$episodeNumber = 1;
$insertedCount = 0;
$skippedCount = 0;

foreach ($channel->item as $item) {

    $epTitle = (string) $item->title;
    $epDescription = (string) $item->description;

    $pubDate = date("Y-m-d H:i:s", strtotime((string) $item->pubDate));

    $itunesItem = $item->children($namespaces['itunes']);
    $duration = isset($itunesItem->duration) 
        ? (string) $itunesItem->duration 
        : null;

    $sqlEpisode = "INSERT INTO episodes 
        (podcast_id, title, description, duration, episode_number, published_at)
        VALUES 
        (:podcast_id, :title, :description, :duration, :episode_number, :published_at)";

    $stmtEpisode = $pdo->prepare($sqlEpisode);

    try {

        $stmtEpisode->execute([
            ":podcast_id" => $podcastId,
            ":title" => $epTitle,
            ":description" => $epDescription,
            ":duration" => $duration,
            ":episode_number" => $episodeNumber,
            ":published_at" => $pubDate
        ]);

        $insertedCount++;

    } catch (PDOException $e) {

        // Si es duplicado por UNIQUE, simplemente lo contamos como omitido
        $skippedCount++;
    }

    $episodeNumber++;
}

echo "Episodios insertados: " . $insertedCount . "<br>";
echo "Episodios omitidos (ya existentes): " . $skippedCount . "<br>";

echo "Importación finalizada correctamente.";