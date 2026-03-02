<?php

class UserPodcast {

    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // ========================
    // Añadir podcast a usuario
    // ========================
    public function addPodcast($userId, $podcastId) {

        $sql = "INSERT INTO user_podcasts (user_id, podcast_id, followed_at)
                VALUES (:user_id, :podcast_id, NOW())";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ":user_id" => $userId,
            ":podcast_id" => $podcastId
        ]);
    }

    // ========================
    // Obtener podcasts de un usuario
    // ========================
    public function getUserPodcasts($userId) {

        $sql = "SELECT p.*
                FROM podcasts p
                INNER JOIN user_podcasts up ON p.id = up.podcast_id
                WHERE up.user_id = :user_id
                ORDER BY up.followed_at DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ":user_id" => $userId
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ========================
    // Eliminar podcast de usuario
    // ========================
    public function removePodcast($userId, $podcastId) {

        $sql = "DELETE FROM user_podcasts
                WHERE user_id = :user_id
                AND podcast_id = :podcast_id";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ":user_id" => $userId,
            ":podcast_id" => $podcastId
        ]);
    }
}