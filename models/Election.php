<?php
/*
|------------------------------------------------------------
| Modèle Election
|------------------------------------------------------------
*/
require_once __DIR__ . '/../config/db.php';

class Election
{
    private $pdo;

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function findById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM Election WHERE id_Election = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function listerToutes()
    {
        $stmt = $this->pdo->query("SELECT * FROM Election ORDER BY date_Debut DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listerActives()
    {
        $stmt = $this->pdo->query("SELECT * FROM Election WHERE statut = 'EN_COURS' ORDER BY date_Debut ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function creer($titre, $description, $date_Debut, $date_Fin)
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO Election (titre, description, date_Debut, date_Fin, statut)
            VALUES (:titre, :description, :date_Debut, :date_Fin, 'A_VENIR')
        ");
        $stmt->execute([':titre' => $titre, ':description' => $description, ':date_Debut' => $date_Debut, ':date_Fin' => $date_Fin]);
        return $this->pdo->lastInsertId();
    }

    public function modifierStatut($id, $statut)
    {
        $stmt = $this->pdo->prepare("UPDATE Election SET statut = :statut WHERE id_Election = :id");
        $stmt->execute([':statut' => $statut, ':id' => $id]);
        return $stmt->rowCount() > 0;
    }

    public function modifier($id, $titre, $description, $date_Debut, $date_Fin)
    {
        $stmt = $this->pdo->prepare("UPDATE Election SET titre = :titre, description = :description, date_Debut = :date_Debut, date_Fin = :date_Fin WHERE id_Election = :id");
        $stmt->execute([':titre' => $titre, ':description' => $description, ':date_Debut' => $date_Debut, ':date_Fin' => $date_Fin, ':id' => $id]);
        return $stmt->rowCount() > 0;
    }
}
?>
