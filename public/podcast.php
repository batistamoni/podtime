<?php
session_start();

// Comprobamos si el usuario está logueado
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

// Conexión y modelos
require_once "../config/database.php";
require_once "../src/models/Podcast.php";
require_once "../src/models/Episode.php";
require_once "../src/models/UserEpisode.php";

// Obtenemos el id del podcast desde la URL
$podcastId = $_GET["id"] ?? null;

// Si no hay id → redirigimos
if (!$podcastId) {
    header("Location: dashboard.php");
    exit;
}

// Obtenemos datos del podcast
$podcastModel = new Podcast($pdo);
$podcast = $podcastModel->findById($podcastId);

// Obtenemos episodios del podcast
$episodeModel = new Episode($pdo);
$episodes = $episodeModel->getByPodcast($podcastId);

// Leemos el filtro desde la URL (GET)
// Si no viene nada, usamos "all" por defecto
$filter = $_GET["filter"] ?? "all";

// Obtenemos episodios escuchados por el usuario
$userEpisodeModel = new UserEpisode($pdo);
$listenedEpisodes = $userEpisodeModel->getListenedEpisodes($_SESSION["user_id"]);

// Si el podcast no existe → redirigimos
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

<!-- Volver al dashboard -->
<a href="dashboard.php">← Volver</a>

<!-- TÍTULO DEL PODCAST -->
<h1><?php echo htmlspecialchars($podcast["title"]); ?></h1>

<!-- Imagen del podcast -->
<?php if ($podcast["image"]): ?>
    <img src="<?php echo htmlspecialchars($podcast["image"]); ?>" width="200">
<?php endif; ?>

<!-- Descripción del podcast -->
<p><?php echo htmlspecialchars($podcast["description"]); ?></p>

<!-- LISTADO DE EPISODIOS -->
<h2>Episodios</h2>

<!-- FILTROS -->
<p>
    <a href="?id=<?php echo $podcastId; ?>&filter=all">Todos</a> |
    <a href="?id=<?php echo $podcastId; ?>&filter=listened">Escuchados</a> |
    <a href="?id=<?php echo $podcastId; ?>&filter=pending">Pendientes</a>
</p>

<?php if (empty($episodes)): ?>
    <p>No hay episodios disponibles.</p>
<?php else: ?>
    <?php foreach ($episodes as $episode): ?>

        <?php
        // FILTRO DE EPISODIOS
        // Si queremos solo escuchados y este no lo está → lo saltamos
        if ($filter === "listened" && !in_array($episode["id"], $listenedEpisodes)) {
            continue;
        }

        // Si queremos solo pendientes y este ya está escuchado → lo saltamos
        if ($filter === "pending" && in_array($episode["id"], $listenedEpisodes)) {
            continue;
        }
        ?>

        <div style="border:1px solid #ccc; padding:10px; margin-bottom:10px;">
            
            <!-- TÍTULO DEL EPISODIO -->
            <h3><?php echo htmlspecialchars($episode["title"]); ?></h3>

            <!-- FECHA DE PUBLICACIÓN -->
            <p>
            📅 <?php echo date("d/m/Y", strtotime($episode["published_at"])); ?>
            </p>

            <!-- DESCRIPCIÓN DEL EPISODIO -->
            <p><?php echo htmlspecialchars($episode["description"]); ?></p>

            <!-- ESTADO DEL EPISODIO -->
            <?php if (in_array($episode["id"], $listenedEpisodes)): ?>

                <!-- Ya escuchado -->
                <p><strong>✔ Escuchado</strong></p>

                <!-- Botón quitar -->
                <form action="../src/controllers/UserEpisodeController.php" method="POST">
        
                    <input type="hidden" name="episode_id" value="<?php echo $episode["id"]; ?>">
                    <input type="hidden" name="podcast_id" value="<?php echo $podcastId; ?>">
                    <input type="hidden" name="action" value="remove">

                    <button type="submit">Quitar de escuchados</button>
                </form>

            <?php else: ?>

            <!-- BOTÓN MARCAR COMO ESCUCHADO -->
            <form action="../src/controllers/UserEpisodeController.php" method="POST">
                <!-- Enviamos el id del episodio oculto -->
                <input type="hidden" name="episode_id" value="<?php echo $episode["id"]; ?>">
                <input type="hidden" name="podcast_id" value="<?php echo $podcastId; ?>">
                <input type="hidden" name="action" value="add">

                <!-- Botón -->
                <button type="submit">Marcar como escuchado</button>
            </form>

            <?php endif; ?>

        </div>

    <?php endforeach; ?>

<?php endif; ?>

</body>
</html>