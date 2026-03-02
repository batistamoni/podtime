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
}