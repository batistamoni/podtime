<?php

class User {

    private $pdo;

    // Recibimos la conexión en el constructor
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Método para buscar usuario por email
    public function findByEmail($email) {

        $sql = "SELECT * FROM users WHERE email = :email LIMIT 1";
        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ":email" => $email
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}