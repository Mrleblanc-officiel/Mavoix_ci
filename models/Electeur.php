<?php
/*
|------------------------------------------------------------
| Modèle Electeur
|------------------------------------------------------------
*/
require_once __DIR__ . '/../config/db.php';

class Electeur
{
    private $pdo;

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function findByUtilisateur($id_Utilisateur)
    {
        $stmt = $this->pdo->prepare("SELECT e.*, u.nom, u.prenom, u.email FROM Electeur e INNER JOIN Utilisateur u ON e.id_Utilisateur = u.id_Utilisateur WHERE e.id_Utilisateur = :id LIMIT 1");
        $stmt->execute([':id' => $id_Utilisateur]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findById($id_Electeur)
    {
        $stmt = $this->pdo->prepare("SELECT e.*, u.nom, u.prenom, u.email FROM Electeur e INNER JOIN Utilisateur u ON e.id_Utilisateur = u.id_Utilisateur WHERE e.id_Electeur = :id LIMIT 1");
        $stmt->execute([':id' => $id_Electeur]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function creer($id_Utilisateur, $numero_Electeur, $numero_CNI, $date_Naissance)
    {
        $stmt = $this->pdo->prepare("INSERT INTO Electeur (id_Utilisateur, numero_Electeur, numero_CNI, date_Naissance) VALUES (:id_Utilisateur, :numero_Electeur, :numero_CNI, :date_Naissance)");
        $stmt->execute([':id_Utilisateur' => $id_Utilisateur, ':numero_Electeur' => $numero_Electeur, ':numero_CNI' => $numero_CNI, ':date_Naissance' => $date_Naissance]);
        return $this->pdo->lastInsertId();
    }

    public function listerTous()
    {
        $stmt = $this->pdo->query("SELECT e.*, u.nom, u.prenom, u.email, u.estActif FROM Electeur e INNER JOIN Utilisateur u ON e.id_Utilisateur = u.id_Utilisateur ORDER BY u.nom ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
