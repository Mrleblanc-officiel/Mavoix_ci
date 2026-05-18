<?php
/*
|------------------------------------------------------------
| Modèle OTP
|------------------------------------------------------------
*/
require_once __DIR__ . '/../config/db.php';

class OTP
{
    private $pdo;

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }

    // Supprimer anciens OTP d'un utilisateur
    public function supprimerParUtilisateur($id_Utilisateur)
    {
        $stmt = $this->pdo->prepare("DELETE FROM OTP WHERE id_Utilisateur = :id");
        $stmt->execute([':id' => $id_Utilisateur]);
    }

    // Insérer un nouvel OTP
    public function inserer($id_Utilisateur, $codeHash, $expiration)
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO OTP (id_Utilisateur, code_Hash, expiration, utilise, date_Creation)
            VALUES (:id, :hash, :exp, FALSE, NOW())
        ");
        $stmt->execute([':id' => $id_Utilisateur, ':hash' => $codeHash, ':exp' => $expiration]);
        return $this->pdo->lastInsertId();
    }

    // Récupérer OTP actif (non utilisé)
    public function findActif($id_Utilisateur)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM OTP WHERE id_Utilisateur = :id AND utilise = FALSE ORDER BY date_Creation DESC LIMIT 1");
        $stmt->execute([':id' => $id_Utilisateur]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Marquer OTP comme utilisé
    public function marquerUtilise($id_otp)
    {
        $stmt = $this->pdo->prepare("UPDATE OTP SET utilise = TRUE WHERE id_otp = :id");
        $stmt->execute([':id' => $id_otp]);
    }
}
?>
