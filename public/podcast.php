<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

require_once "../config/database.php";
require_once "../src/models/Podcast.php";
require_once "../src/models/Episode.php";
require_once "../src/models/UserEpisode.php";

$podcastId = $_GET["id"] ?? null;

if (!$podcastId) {
    header("Location: dashboard.php");
    exit;
}

$podcastModel = new Podcast($pdo);
$podcast = $podcastModel->findById($podcastId);

$episodeModel = new Episode($pdo);
$episodes = $episodeModel->getByPodcast($podcastId);

$filter = $_GET["filter"] ?? "all";

$userEpisodeModel = new UserEpisode($pdo);
$listenedEpisodes = $userEpisodeModel->getListenedEpisodes($_SESSION["user_id"]);

if (!$podcast) {
    header("Location: dashboard.php");
    exit;
}
?>

<?php require_once "partials/header.php"; ?>

<a href="dashboard.php" class="text-purple-600 hover:underline">← Volver</a>

<h1 class="text-2xl font-bold mt-4">
    <?php echo htmlspecialchars($podcast["title"]); ?>
</h1>

<?php if ($podcast["image"]): ?>
    <img src="<?php echo htmlspecialchars($podcast["image"]); ?>" class="w-48 my-4">
<?php endif; ?>

<p class="text-gray-700 mb-4">
    <?php echo htmlspecialchars($podcast["description"]); ?>
</p>

<h2 class="text-xl font-semibold mb-2">Episodios</h2>

<!-- FILTROS -->
<p class="mb-4 space-x-2">
    <a href="?id=<?php echo $podcastId; ?>&filter=all" class="text-purple-600">Todos</a>
    <a href="?id=<?php echo $podcastId; ?>&filter=listened" class="text-purple-600">Escuchados</a>
    <a href="?id=<?php echo $podcastId; ?>&filter=pending" class="text-purple-600">Pendientes</a>
</p>

<?php if (empty($episodes)): ?>
    <p>No hay episodios disponibles.</p>
<?php else: ?>
    <?php foreach ($episodes as $episode): ?>

        <?php
        if ($filter === "listened" && !in_array($episode["id"], $listenedEpisodes)) {
            continue;
        }

        if ($filter === "pending" && in_array($episode["id"], $listenedEpisodes)) {
            continue;
        }
        ?>

        <div class="bg-white p-4 mb-4 border rounded shadow">

            <h3 class="text-lg font-semibold">
                <?php echo htmlspecialchars($episode["title"]); ?>
            </h3>

            <p class="text-sm text-gray-500">
                📅 <?php echo date("d/m/Y", strtotime($episode["published_at"])); ?>
            </p>

            <p class="text-gray-700 my-2">
                <?php echo htmlspecialchars($episode["description"]); ?>
            </p>

            <?php if (in_array($episode["id"], $listenedEpisodes)): ?>

                <p class="text-green-600 font-semibold">✔ Escuchado</p>

                <form action="../src/controllers/UserEpisodeController.php" method="POST" class="mt-2">
                    <input type="hidden" name="episode_id" value="<?php echo $episode["id"]; ?>">
                    <input type="hidden" name="podcast_id" value="<?php echo $podcastId; ?>">
                    <input type="hidden" name="action" value="remove">

                    <button class="bg-red-500 text-white px-3 py-1 rounded">
                        Quitar de escuchados
                    </button>
                </form>

            <?php else: ?>

                <form action="../src/controllers/UserEpisodeController.php" method="POST" class="mt-2">
                    <input type="hidden" name="episode_id" value="<?php echo $episode["id"]; ?>">
                    <input type="hidden" name="podcast_id" value="<?php echo $podcastId; ?>">
                    <input type="hidden" name="action" value="add">

                    <button class="bg-purple-600 text-white px-3 py-1 rounded">
                        Marcar como escuchado
                    </button>
                </form>

            <?php endif; ?>

        </div>

    <?php endforeach; ?>
<?php endif; ?>

<?php require_once "partials/footer.php"; ?>