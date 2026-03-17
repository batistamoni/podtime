<?php

class Podcast {

    private $pdo;

    // Recibimos la conexión cuando se crea el objeto
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // ========================
    // Obtener todos los podcasts
    // ========================
    public function getAll() {

        $sql = "SELECT * FROM podcasts ORDER BY created_at DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ========================
    // Buscar podcast por ID
    // ========================
    public function findById($id) {

        $sql = "SELECT * FROM podcasts WHERE id = :id LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ":id" => $id
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ========================
    // Comprobar si podcast está completado
    // ========================
    public function isCompleted($podcastId, $userId) {

        $sql = "SELECT 
                    COUNT(e.id) as total,
                    COUNT(ue.id) as listened
                FROM episodes e
                LEFT JOIN user_episodes ue 
                    ON e.id = ue.episode_id 
                    AND ue.user_id = :user_id
                WHERE e.podcast_id = :podcast_id";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ":user_id" => $userId,
            ":podcast_id" => $podcastId
        ]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result["total"] > 0 && $result["total"] == $result["listened"];
    }
}