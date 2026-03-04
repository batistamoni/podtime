<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

require_once "../config/database.php";
require_once "../src/models/Podcast.php";
require_once "../src/models/Episode.php";

$podcastId = $_GET["id"] ?? null;

if (!$podcastId) {
    header("Location: dashboard.php");
    exit;
}

$podcastModel = new Podcast($pdo);
$podcast = $podcastModel->findById($podcastId);

$episodeModel = new Episode($pdo);
$episodes = $episodeModel->getByPodcast($podcastId);

if (!$podcast) {
    header("Location: dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title><?php echo htmlspecialchars($podcast["title"]); ?></title>
</head>
<body>

<a href="dashboard.php">← Volver</a>

<h1><?php echo htmlspecialchars($podcast["title"]); ?></h1>

<?php if ($podcast["image"]): ?>
    <img src="<?php echo htmlspecialchars($podcast["image"]); ?>" width="200">
<?php endif; ?>

<p><?php echo htmlspecialchars($podcast["description"]); ?></p>

<h2>Episodios</h2>

<?php if (empty($episodes)): ?>
    <p>No hay episodios disponibles.</p>
<?php else: ?>
    <?php foreach ($episodes as $episode): ?>

        <div style="border:1px solid #ccc; padding:10px; margin-bottom:10px;">
            
            <h3><?php echo htmlspecialchars($episode["title"]); ?></h3>

            <p>
            📅 <?php echo date("d/m/Y", strtotime($episode["published_at"])); ?>
            </p>

            <?php
            $seconds = (int)$episode["duration"];
            $minutes = floor($seconds / 60);
            $remainingSeconds = $seconds % 60;
            ?>

<p>
⏱ <?php echo $minutes . ":" . str_pad($remainingSeconds, 2, "0", STR_PAD_LEFT); ?>
</p>

            <p><?php echo htmlspecialchars($episode["description"]); ?></p>

        </div>

    <?php endforeach; ?>
<?php endif; ?>

</body>
</html>