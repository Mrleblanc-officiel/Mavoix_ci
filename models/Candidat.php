<?php
/*
|------------------------------------------------------------
| Modèle Candidat
|------------------------------------------------------------
*/
require_once __DIR__ . '/../config/db.php';

class Candidat
{
    private $pdo;

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function findById($id)
    {
        $stmt = $this->pdo->prepare("SELECT c.*, p.nom AS nomParti, p.sigle, e.titre AS election FROM Candidat c LEFT JOIN PartiPolitique p ON c.id_Parti = p.id_Parti INNER JOIN Election e ON c.id_Election = e.id_Election WHERE c.id_Candidat = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function listerParElection($id_Election)
    {
        $stmt = $this->pdo->prepare("SELECT c.*, p.nom AS nomParti, p.sigle FROM Candidat c LEFT JOIN PartiPolitique p ON c.id_Parti = p.id_Parti WHERE c.id_Election = :id AND c.id_Actif = TRUE ORDER BY c.nom ASC");
        $stmt->execute([':id' => $id_Election]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listerTous()
    {
        $stmt = $this->pdo->query("SELECT c.*, p.nom AS nomParti, p.sigle, e.titre AS election FROM Candidat c LEFT JOIN PartiPolitique p ON c.id_Parti = p.id_Parti INNER JOIN Election e ON c.id_Election = e.id_Election ORDER BY c.nom ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function creer($nom, $prenom, $photo, $description, $id_Election, $id_Parti = null)
    {
        $stmt = $this->pdo->prepare("INSERT INTO Candidat (nom, prenom, photo, description, id_Actif, id_Election, id_Parti) VALUES (:nom, :prenom, :photo, :description, TRUE, :id_Election, :id_Parti)");
        $stmt->execute([':nom' => $nom, ':prenom' => $prenom, ':photo' => $photo, ':description' => $description, ':id_Election' => $id_Election, ':id_Parti' => $id_Parti]);
        return $this->pdo->lastInsertId();
    }

    public function modifier($id, $nom, $prenom, $photo, $description, $id_Parti = null)
    {
        $stmt = $this->pdo->prepare("UPDATE Candidat SET nom = :nom, prenom = :prenom, photo = :photo, description = :description, id_Parti = :id_Parti WHERE id_Candidat = :id");
        $stmt->execute([':nom' => $nom, ':prenom' => $prenom, ':photo' => $photo, ':description' => $description, ':id_Parti' => $id_Parti, ':id' => $id]);
        return $stmt->rowCount() > 0;
    }

    public function setActif($id, $actif)
    {
        $stmt = $this->pdo->prepare("UPDATE Candidat SET id_Actif = :actif WHERE id_Candidat = :id");
        $stmt->execute([':actif' => $actif, ':id' => $id]);
        return $stmt->rowCount() > 0;
    }
}
?>
