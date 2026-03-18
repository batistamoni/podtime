<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

require_once "../config/database.php";
require_once "../src/models/Podcast.php";
require_once "../src/models/UserPodcast.php";

$podcastModel = new Podcast($pdo);
$podcasts = $podcastModel->getAll();

$userPodcastModel = new UserPodcast($pdo);
$userPodcasts = $userPodcastModel->getUserPodcasts($_SESSION["user_id"]);

$userPodcastIds = array_column($userPodcasts, "id");
?>

<?php require_once "partials/header.php"; ?>

<h1 class="text-2xl font-bold mb-6">
    Bienvenido, <?php echo $_SESSION["username"]; ?> 👋
</h1>

<!-- ========================= -->
<!-- 🎧 MIS PODCASTS -->
<!-- ========================= -->

<h2 class="text-lg font-semibold mb-4">Tus podcasts</h2>

<?php if (empty($userPodcasts)): ?>
    <p class="text-gray-500 mb-6">No sigues ningún podcast todavía.</p>
<?php else: ?>

<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-10">

<?php foreach ($userPodcasts as $podcast): ?>

    <a href="podcast.php?id=<?php echo $podcast["id"]; ?>" 
       class="bg-white rounded-xl shadow hover:shadow-lg transition overflow-hidden">

        <?php if ($podcast["image"]): ?>
            <img 
                src="<?php echo htmlspecialchars($podcast["image"]); ?>" 
                class="w-full aspect-square object-cover bg-gray-200"
            />
        <?php endif; ?>

        <!-- Barra de progreso (de momento fija) -->
        <div class="h-2 bg-gray-200">
            <div class="h-2 bg-[#FFC107]" style="width: 30%"></div>
        </div>

        <div class="p-3">
            <p class="font-semibold text-sm">
                <?php echo htmlspecialchars($podcast["title"]); ?>
            </p>
        </div>

    </a>

<?php endforeach; ?>

</div>

<?php endif; ?>


<!-- ========================= -->
<!-- 🔍 EXPLORAR PODCASTS -->
<!-- ========================= -->

<h2 class="text-lg font-semibold mb-4">Explorar podcasts</h2>

<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">

<?php foreach ($podcasts as $podcast): ?>

    <?php if (in_array($podcast["id"], $userPodcastIds)) continue; ?>

    <div class="bg-white rounded-xl shadow p-3 flex flex-col justify-between">

        <div>
            <?php if ($podcast["image"]): ?>
                <img 
                    src="<?php echo htmlspecialchars($podcast["image"]); ?>" 
                    class="w-full h-32 object-cover rounded mb-2"
                >
            <?php endif; ?>

            <p class="font-semibold text-sm mb-1">
                <?php echo htmlspecialchars($podcast["title"]); ?>
            </p>

            <p class="text-xs text-gray-500 line-clamp-2">
                <?php echo htmlspecialchars($podcast["description"]); ?>
            </p>
        </div>

        <form action="../src/controllers/UserPodcastController.php" method="POST" class="mt-2">
            <input type="hidden" name="podcast_id" value="<?php echo $podcast["id"]; ?>">
            <input type="hidden" name="action" value="add">

            <button class="w-full bg-[#FFC107] text-black py-1 rounded text-sm hover:opacity-90">
                Seguir
            </button>
        </form>

    </div>

<?php endforeach; ?>

</div>

<?php require_once "partials/footer.php"; ?>