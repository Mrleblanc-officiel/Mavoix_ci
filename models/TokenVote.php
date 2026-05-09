<?php
/*
|------------------------------------------------------------
| Modèle TokenVote
|------------------------------------------------------------
| Token unique par vote pour garantir l'anonymat et
| l'unicité du suffrage.
|------------------------------------------------------------
*/
require_once __DIR__ . '/../config/db.php';

class TokenVote
{
    private $pdo;

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }

    // Générer et stocker un token
    public function generer($id_Electeur, $id_Election)
    {
        $token = bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $token);

        $stmt = $this->pdo->prepare("
            INSERT INTO TokenVote (id_Electeur, id_Election, token_Hash, utilise, date_Generation)
            VALUES (:id_Electeur, :id_Election, :token_Hash, FALSE, NOW())
        ");
        $stmt->execute([
            ':id_Electeur' => $id_Electeur,
            ':id_Election'  => $id_Election,
            ':token_Hash'   => $tokenHash
        ]);

        return $token; // retourner le token brut (non stocké)
    }

    // Vérifier un token
    public function verifier($token, $id_Election)
    {
        $tokenHash = hash('sha256', $token);
        $stmt = $this->pdo->prepare("SELECT * FROM TokenVote WHERE token_Hash = :hash AND id_Election = :id_Election AND utilise = FALSE LIMIT 1");
        $stmt->execute([':hash' => $tokenHash, ':id_Election' => $id_Election]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Marquer token utilisé
    public function marquerUtilise($id_TokenVote)
    {
        $stmt = $this->pdo->prepare("UPDATE TokenVote SET utilise = TRUE WHERE id_TokenVote = :id");
        $stmt->execute([':id' => $id_TokenVote]);
    }
}
?>
