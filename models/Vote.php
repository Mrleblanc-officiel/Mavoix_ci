<?php
/*
|------------------------------------------------------------
| Modèle Vote
|------------------------------------------------------------
*/
require_once __DIR__ . '/../config/db.php';

class Vote
{
    private $pdo;

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }

    // Vérifier si un électeur a déjà voté pour une élection
    public function aDejaVote($id_Electeur, $id_Election)
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM Vote WHERE id_Electeur = :id_Electeur AND id_Election = :id_Election");
        $stmt->execute([':id_Electeur' => $id_Electeur, ':id_Election' => $id_Election]);
        return $stmt->fetchColumn() > 0;
    }

    // Enregistrer un vote
    public function enregistrer($id_Electeur, $id_Candidat, $id_Election, $tokenHash)
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO Vote (id_Electeur, id_Candidat, id_Election, token_Hash, date_Vote)
            VALUES (:id_Electeur, :id_Candidat, :id_Election, :token_Hash, NOW())
        ");
        $stmt->execute([
            ':id_Electeur'  => $id_Electeur,
            ':id_Candidat'  => $id_Candidat,
            ':id_Election'  => $id_Election,
            ':token_Hash'   => $tokenHash
        ]);
        return $this->pdo->lastInsertId();
    }

    // Résultats d'une élection
    public function resultatsParElection($id_Election)
    {
        $stmt = $this->pdo->prepare("
            SELECT
                c.id_Candidat,
                c.nom,
                c.prenom,
                c.photo,
                COUNT(v.id_Vote) AS nombre_Voix,
                ROUND(COUNT(v.id_Vote) * 100.0 / NULLIF((SELECT COUNT(*) FROM Vote WHERE id_Election = :id1), 0), 2) AS pourcentage
            FROM Candidat c
            LEFT JOIN Vote v ON c.id_Candidat = v.id_Candidat AND v.id_Election = :id2
            WHERE c.id_Election = :id3
            GROUP BY c.id_Candidat, c.nom, c.prenom, c.photo
            ORDER BY nombre_Voix DESC
        ");
        $stmt->execute([':id1' => $id_Election, ':id2' => $id_Election, ':id3' => $id_Election]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Total votes pour une élection
    public function totalParElection($id_Election)
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM Vote WHERE id_Election = :id");
        $stmt->execute([':id' => $id_Election]);
        return $stmt->fetchColumn();
    }
}
?>