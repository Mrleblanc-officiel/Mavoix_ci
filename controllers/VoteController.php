<?php

// ------------------------------------------------------------
// VoteController
// ------------------------------------------------------------
// fonctionalité :
// - vérifier électeur
// - empêcher double vote
// - générer token de vote
// - enregistrer vote
//
// IMPORTANT :
// Seul un ELECTEUR connecté peut voter.
// ------------------------------------------------------------


// ------------------------------------------------------------
// Démarrage session
// ------------------------------------------------------------

if (session_status() === PHP_SESSION_NONE) {
    session_start(); 
    }

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/Vote.php';
require_once __DIR__ . '/../models/Electeur.php';
require_once __DIR__ . '/../models/Election.php';
require_once __DIR__ . '/../models/Candidat.php';
require_once __DIR__ . '/../models/TokenVote.php';
require_once __DIR__ . '/../models/AuditLog.php';

// ------------------------------------------------------------
// Connexion base de données
// ------------------------------------------------------------
require_once __DIR__ . '/../config/db.php';

// ------------------------------------------------------------
// Classe VoteController
// ------------------------------------------------------------

class VoteController
{

    // --------------------------------------------------------
    // Objet PDO
    // --------------------------------------------------------

    private $pdo;


    // --------------------------------------------------------
    // Constructeur
    // --------------------------------------------------------

    public function __construct()
    {
        global $pdo;

        $this->pdo = $pdo;
    }



    // --------------------------------------------------------
    // VERIFICATION ELECTEUR
    // --------------------------------------------------------
    // Vérifie :
    // - utilisateur connecté
    // - rôle électeur
    // - existence dans table Electeur
    // - compte actif
    // --------------------------------------------------------

    private function verifierElecteur()
    {

        // ----------------------------------------------------
        // Vérifie session utilisateur
        // ----------------------------------------------------

        if (!isset($_SESSION['user'])) {

            return [

                'status' => 'error',
                'message' => 'Utilisateur non connecté'
            ];
        }


        // ----------------------------------------------------
        // Vérifie rôle électeur
        // ----------------------------------------------------

        // ROLE 3 = ELECTEUR

        if ($_SESSION['user']['idRole'] != 3) {

            return [

                'status' => 'error',
                'message' => 'Accès réservé aux électeurs'
            ];
        }


        // ----------------------------------------------------
        // Vérification existence électeur
        // ----------------------------------------------------

        $sql = "
            SELECT
                e.*,
                u.est_Actif
            FROM Electeur e

            INNER JOIN Utilisateur u
            ON e.id_Electeur = u.id_Utilisateur

            WHERE e.id_Electeur = :id_Electeur
            LIMIT 1
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([

            ':id_Electeur' => $_SESSION['user']['id_Utilisateur']
        ]);

        $electeur = $stmt->fetch(PDO::FETCH_ASSOC);


        // ----------------------------------------------------
        // Vérification existence
        // ----------------------------------------------------

        if (!$electeur) {

            return [

                'status' => 'error',
                'message' => 'Electeur introuvable'
            ];
        }


        // ----------------------------------------------------
        // Vérification compte actif
        // ----------------------------------------------------

        if (!$electeur['est_Actif']) {

            return [

                'status' => 'error',
                'message' => 'Compte désactivé'
            ];
        }


        // ----------------------------------------------------
        // Retour succès
        // ----------------------------------------------------

        return [

            'status' => 'success',
            'data' => $electeur
        ];
    }



    // --------------------------------------------------------
    // GENERER TOKEN UNIQUE
    // --------------------------------------------------------
    // Génère :
    // - un token sécurisé
    // - unique
    // - associé au vote
    // --------------------------------------------------------

    private function genererTokenVote()
    {

        return bin2hex(random_bytes(32));
    }



    // --------------------------------------------------------
    // VERIFIER DOUBLE VOTE
    // --------------------------------------------------------
    // Vérifie :
    // - si électeur a déjà voté
    // --------------------------------------------------------

    private function verifierDoubleVote($id_Electeur)
    {

        $sql = "
            SELECT *
            FROM Vote
            WHERE id_Electeur = :id_Electeur
            LIMIT 1
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([

            ':id_Electeur' => $id_Electeur
        ]);

        $vote = $stmt->fetch(PDO::FETCH_ASSOC);


        // ----------------------------------------------------
        // Si vote trouvé
        // ----------------------------------------------------

        if ($vote) {

            return true;
        }

        return false;
    }



    // --------------------------------------------------------
    // ENREGISTRER VOTE
    // --------------------------------------------------------
    // Vérifie :
    // - électeur
    // - candidat
    // - élection active
    // - double vote
    //
    // Puis :
    // - génère token
    // - enregistre vote
    // --------------------------------------------------------

    public function voter($id_Candidat)
    {

        // ----------------------------------------------------
        // Vérification électeur
        // ----------------------------------------------------

        $verification = $this->verifierElecteur();

        if ($verification['status'] == 'error') {

            return $verification;
        }


        // ----------------------------------------------------
        // Données électeur
        // ----------------------------------------------------

        $electeur = $verification['data'];

        $id_Electeur = $electeur['id_Electeur'];


        // ----------------------------------------------------
        // Vérification double vote
        // ----------------------------------------------------

        if ($this->verifierDoubleVote($id_Electeur)) {

            return [

                'status' => 'error',
                'message' => 'Vous avez déjà voté'
            ];
        }


        // ----------------------------------------------------
        // Vérification candidat
        // ----------------------------------------------------

        $sqlCandidat = "
            SELECT
                c.*,
                e.statut
            FROM Candidat c

            INNER JOIN Election e
            ON c.id_Election = e.id_Election

            WHERE c.id_Candidat = :id_Candidat
            AND c.est_Actif = TRUE

            LIMIT 1
        ";

        $stmtCandidat = $this->pdo->prepare($sqlCandidat);

        $stmtCandidat->execute([

            ':id_Candidat' => $id_Candidat
        ]);

        $candidat = $stmtCandidat->fetch(PDO::FETCH_ASSOC);


        // ----------------------------------------------------
        // Vérification existence candidat
        // ----------------------------------------------------

        if (!$candidat) {

            return [

                'status' => 'error',
                'message' => 'Candidat introuvable'
            ];
        }


        // ----------------------------------------------------
        // Vérification élection active
        // ----------------------------------------------------

        if ($candidat['statut'] != 'EN_COURS') {

            return [

                'status' => 'error',
                'message' => 'Election non active'
            ];
        }


        // ----------------------------------------------------
        // Génération token vote
        // ----------------------------------------------------

        $tokenVote = $this->genererTokenVote();


        // ----------------------------------------------------
        // Transaction SQL
        // ----------------------------------------------------
        // IMPORTANT :
        // Empêche incohérences si erreur
        // ----------------------------------------------------

        try {

            $this->pdo->beginTransaction();


            // ------------------------------------------------
            // Insertion vote
            // ------------------------------------------------

            $sqlVote = "
                INSERT INTO Vote (
                    dateHeure,
                    token_Verification,
                    id_Electeur,
                    id_Candidat
                )
                VALUES (
                    NOW(),
                    :token_Verification,
                    :id_Electeur,
                    :id_Candidat
                )
            ";

            $stmtVote = $this->pdo->prepare($sqlVote);

            $stmtVote->execute([

                ':token_Verification' => $tokenVote,
                ':id_Electeur'        => $id_Electeur,
                ':id_Candidat'        => $id_Candidat
            ]);


            // ------------------------------------------------
            // Mise à jour électeur
            // ------------------------------------------------

            $sqlUpdateElecteur = "
                UPDATE Electeur
                SET
                    aVote = TRUE,
                    dateVote = NOW()
                WHERE id_Electeur = :id_Electeur
            ";

            $stmtUpdate = $this->pdo->prepare($sqlUpdateElecteur);

            $stmtUpdate->execute([

                ':id_Electeur' => $id_Electeur
            ]);


            // ------------------------------------------------
            // Validation transaction
            // ------------------------------------------------

            $this->pdo->commit();


            // ------------------------------------------------
            // Retour succès
            // ------------------------------------------------

            return [

                'status' => 'success',
                'message' => 'Vote enregistré avec succès',

                // Token utile pour :
                // - audit
                // - vérification
                // - traçabilité

                'tokenVote' => $tokenVote
            ];

        } catch (PDOException $e) {


            // ------------------------------------------------
            // Annulation transaction
            // ------------------------------------------------

            $this->pdo->rollBack();


            return [

                'status' => 'error',
                'message' => 'Erreur SQL : ' . $e->getMessage()
            ];
        }
    }
}
?>
