<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

require_once "../config/database.php";
require_once "../src/models/Podcast.php";

$podcastModel = new Podcast($pdo);
$podcasts = $podcastModel->getAll();

require_once "../src/models/UserPodcast.php";

$userPodcastModel = new UserPodcast($pdo);
$userPodcasts = $userPodcastModel->getUserPodcasts($_SESSION["user_id"]);

$userPodcastIds = array_column($userPodcasts, "id");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - PodTime</title>
</head>
<body>

<h1>Bienvenido, <?php echo $_SESSION["username"]; ?> 👋</h1>

<h2>Catálogo de Podcasts</h2>

<?php if (empty($podcasts)): ?>
    <p>No hay podcasts disponibles.</p>
<?php else: ?>
    <?php foreach ($podcasts as $podcast): ?>
        <div style="margin-bottom: 20px; border:1px solid #ccc; padding:10px;">
            <h3>
                <a href="podcast.php?id=<?php echo $podcast["id"]; ?>">
                    <?php echo htmlspecialchars($podcast["title"]); ?>
                </a>
            </h3>
            
            <?php if ($podcast["image"]): ?>
                <img src="<?php echo htmlspecialchars($podcast["image"]); ?>" width="150">
            <?php endif; ?>
            
            <p><?php echo htmlspecialchars($podcast["description"]); ?></p>

            <?php if (in_array($podcast["id"], $userPodcastIds)): ?>
                <p><strong>Ya en tu lista</strong></p>
            <?php else: ?>
                <form action="../src/controllers/UserPodcastController.php" method="POST">
                    <input type="hidden" name="podcast_id" value="<?php echo $podcast["id"]; ?>">
                    <input type="hidden" name="action" value="add">
                    <button type="submit">Añadir a mi lista</button>
                </form>
<?php endif; ?>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<h2>Mis Podcasts</h2>

<?php if (empty($userPodcasts)): ?>
    <p>No sigues ningún podcast todavía.</p>
<?php else: ?>
    <?php foreach ($userPodcasts as $podcast): ?>
        <div style="margin-bottom: 20px; border:1px solid green; padding:10px;">
            <h3><?php echo htmlspecialchars($podcast["title"]); ?></h3>
            
            <?php if ($podcast["image"]): ?>
                <img src="<?php echo htmlspecialchars($podcast["image"]); ?>" width="150">
            <?php endif; ?>
            
            <p><?php echo htmlspecialchars($podcast["description"]); ?></p>
        
            <form action="../src/controllers/UserPodcastController.php" method="POST">
                <input type="hidden" name="podcast_id" value="<?php echo $podcast["id"]; ?>">
                <input type="hidden" name="action" value="remove">
                <button type="submit">Eliminar de mi lista</button>
            </form>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<a href="logout.php">Cerrar sesión</a>

</body>
</html>