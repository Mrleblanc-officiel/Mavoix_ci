<?php
/*
|------------------------------------------------------------
| Modèle SessionUtilisateur
|------------------------------------------------------------
| Gère les sessions persistantes en base de données.
|------------------------------------------------------------
*/
require_once __DIR__ . '/../config/db.php';

class SessionUtilisateur
{
    private $pdo;

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function creer($id_Utilisateur, $sessionToken, $ip, $userAgent, $expiration)
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO SessionUtilisateur (id_Utilisateur, session_Token, ip, user_Agent, expiration, date_Creation)
            VALUES (:id, :token, :ip, :ua, :exp, NOW())
        ");
        $stmt->execute([':id' => $id_Utilisateur, ':token' => $sessionToken, ':ip' => $ip, ':ua' => $userAgent, ':exp' => $expiration]);
    }

    public function findByToken($token)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM SessionUtilisateur WHERE session_Token = :token AND expiration > NOW() LIMIT 1");
        $stmt->execute([':token' => $token]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function supprimer($token)
    {
        $stmt = $this->pdo->prepare("DELETE FROM SessionUtilisateur WHERE session_Token = :token");
        $stmt->execute([':token' => $token]);
    }

    public function nettoyerExpires()
    {
        $this->pdo->exec("DELETE FROM SessionUtilisateur WHERE expiration < NOW()");
    }
}
?>
