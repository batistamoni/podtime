<?php

class UserEpisode {

    private $pdo;

    // Constructor: guardamos la conexión a la bbdd
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Método para marcar episodio como escuchado
    public function markAsListened($userId, $episodeId) {

        // 1. Comprobamos si ya existe
        $checkSql = "SELECT id FROM user_episodes WHERE user_id = :user_id AND episode_id = :episode_id";

        $stmt = $this->pdo->prepare($checkSql);

        $stmt->execute([
            ":user_id" => $userId,
            ":episode_id" => $episodeId
        ]);

        $existing = $stmt->fetch();

        // 2. Si no existe -> insertamos
        if (!$existing) {

            // Consulta SQL para insertar relación usuario-episodio
            $sql = "INSERT INTO user_episodes (user_id, episode_id) VALUES (:user_id, :episode_id)";

            // Preparamos la consulta
            $stmt = $this->pdo->prepare($sql);

            // Ejecutamos con los valores reales
            $stmt->execute([
            ":user_id" => $userId,
            ":episode_id" => $episodeId
            ]);
        }
    }

    // Obtener ids de episodios escuchados por el usuario
    public function getListenedEpisodes($userId) {

        $sql = "SELECT episode_id FROM user_episodes WHERE user_id = :user_id";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
        ":user_id" => $userId
        ]);

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    // Quitar episodio de escuchados
public function unmarkAsListened($userId, $episodeId) {

    $sql = "DELETE FROM user_episodes 
            WHERE user_id = :user_id 
            AND episode_id = :episode_id";

    $stmt = $this->pdo->prepare($sql);

    return $stmt->execute([
        ":user_id" => $userId,
        ":episode_id" => $episodeId
    ]);
}

// Obtener estadísticas del usuario
public function getStats($userId) {

    $sql = "SELECT 
                COUNT(*) as total_episodes,
                COALESCE(SUM(e.duration), 0) as total_duration
            FROM user_episodes ue
            JOIN episodes e ON ue.episode_id = e.id
            WHERE ue.user_id = :user_id";

    $stmt = $this->pdo->prepare($sql);

    $stmt->execute([
        ":user_id" => $userId
    ]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

}