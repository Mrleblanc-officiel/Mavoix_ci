<?php
/*
|------------------------------------------------------------
| Modèle Notification
|------------------------------------------------------------
*/
require_once __DIR__ . '/../config/db.php';

class Notification
{
    private $pdo;

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function creer($id_Utilisateur, $titre, $message)
    {
        $stmt = $this->pdo->prepare("INSERT INTO Notification (id_Utilisateur, titre, message, lu, date_Creation) VALUES (:id, :titre, :message, FALSE, NOW())");
        $stmt->execute([':id' => $id_Utilisateur, ':titre' => $titre, ':message' => $message]);
    }

    public function listerParUtilisateur($id_Utilisateur)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM Notification WHERE id_Utilisateur = :id ORDER BY date_Creation DESC");
        $stmt->execute([':id' => $id_Utilisateur]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function marquerLu($id_Notification)
    {
        $stmt = $this->pdo->prepare("UPDATE Notification SET lu = TRUE WHERE id_Notification = :id");
        $stmt->execute([':id' => $id_Notification]);
    }

    public function compterNonLus($id_Utilisateur)
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM Notification WHERE id_Utilisateur = :id AND lu = FALSE");
        $stmt->execute([':id' => $id_Utilisateur]);
        return $stmt->fetchColumn();
    }
}
?>
