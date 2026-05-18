<?php
/*
|------------------------------------------------------------
| Modèle AuditLog
|------------------------------------------------------------
| Trace toutes les actions importantes du système.
|------------------------------------------------------------
*/
require_once __DIR__ . '/../config/db.php';

class AuditLog
{
    private $pdo;

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function enregistrer($id_Utilisateur, $action, $details = null, $ip = null)
    {
        $ip = $ip ?? ($_SERVER['REMOTE_ADDR'] ?? 'inconnue');
        $stmt = $this->pdo->prepare("
            INSERT INTO AuditLog (id_Utilisateur, action, details, ip, date_Action)
            VALUES (:id_Utilisateur, :action, :details, :ip, NOW())
        ");
        $stmt->execute([
            ':id_Utilisateur' => $id_Utilisateur,
            ':action'         => $action,
            ':details'        => $details,
            ':ip'             => $ip
        ]);
    }

    public function listerRecents($limite = 50)
    {
        $stmt = $this->pdo->prepare("
            SELECT a.*, u.nom, u.prenom, u.email
            FROM AuditLog a
            LEFT JOIN Utilisateur u ON a.id_Utilisateur = u.id_Utilisateur
            ORDER BY a.date_Action DESC
            LIMIT :limite
        ");
        $stmt->bindValue(':limite', (int)$limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
