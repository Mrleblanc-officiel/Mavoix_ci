<?php

/*
|------------------------------------------------------------
| Modèle Utilisateur
|------------------------------------------------------------
| Gère les requêtes liées aux utilisateurs.
|------------------------------------------------------------
*/

require_once 'config/db.php';

class Utilisateur
{

    private $pdo;

    /*
    |--------------------------------------------------------
    | Constructeur
    |--------------------------------------------------------
    */

    public function __construct()
    {
        global $pdo;

        $this->pdo = $pdo;
    }

    /*
    |--------------------------------------------------------
    | Recherche utilisateur par email
    |--------------------------------------------------------
    */

    public function findByEmail($email)
    {

        $sql = "SELECT * FROM Utilisateur WHERE email = :email LIMIT 1";
       $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ':email' => $email
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}