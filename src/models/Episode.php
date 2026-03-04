<?php

class Episode {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getByPodcast($podcastId) {

        $sql = "SELECT * FROM episodes
                WHERE podcast_id = :podcast_id
                ORDER BY episode_number DESC";

        $stmt = $this->pdo->prepare($sql);        

        $stmt->execute([
            ":podcast_id" => $podcastId
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}