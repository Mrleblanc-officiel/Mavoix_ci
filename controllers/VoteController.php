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
    // DASHBOARD ELECTEUR
    // --------------------------------------------------------
    public function dashboard()
    {
        $verification = $this->verifierElecteur();

        if ($verification['status'] == 'error') {

            header('Location: ?page=login');
            exit;
        }

        $electionModel = new Election();

        $elections = $electionModel->listerToutes();

        require_once __DIR__ . '/../views/vote/dashboard.php';
    }


    // --------------------------------------------------------
    // LISTE DES ELECTIONS
    // --------------------------------------------------------
    public function elections()
    {
        $verification = $this->verifierElecteur();

        if ($verification['status'] == 'error') {

            header('Location: ?page=login');
            exit;
        }

        $electionModel = new Election();

        $elections = $electionModel->listerToutes();

        require_once __DIR__ . '/../views/vote/elections.php';
    }

    // --------------------------------------------------------
    // BULLETIN DE VOTE
    // --------------------------------------------------------
    public function bulletin($id_Election)
    {
        $verification = $this->verifierElecteur();

        if ($verification['status'] == 'error') {

            header('Location: ?page=login');
            exit;
        }

        $electionModel = new Election();
        $candidatModel = new Candidat();

        $election = $electionModel->findById($id_Election);

        if (!$election) {

            echo "Election introuvable";
            exit;
        }

        $candidats = $candidatModel->listerParElection($id_Election);

        require_once __DIR__ . '/../views/vote/bulletin.php';
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

        require_once __DIR__ . './../views/vote/dashboard.php';
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

    // --------------------------------------------------------
    // ENREGISTRER VOTE
    // --------------------------------------------------------
    public function voter($data)
    {

        // ----------------------------------------------------
        // Vérification électeur
        // ----------------------------------------------------
        
        $verification = $this->verifierElecteur();

        if ($verification['status'] == 'error') {

            $_SESSION['error'] = $verification['message'];

            header('Location: ?page=login');
            exit;
        }

        if (!isset($data['id_Candidat'])) {

            $_SESSION['error'] = "Candidat invalide";

            header('Location: ?page=elections');
            exit;
        }

        $id_Candidat = $data['id_Candidat'];

        $electeur = $verification['data'];

        $id_Electeur = $electeur['id_Electeur'];

        // ----------------------------------------------------
        // DOUBLE VOTE
        // ----------------------------------------------------

        if ($this->verifierDoubleVote($id_Electeur)) {

            $_SESSION['error'] = "Vous avez déjà voté";

            header('Location: ?page=elections');
            exit;
        }

        // ----------------------------------------------------
        // VERIFICATION CANDIDAT
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

        if (!$candidat) {

            $_SESSION['error'] = "Candidat introuvable";

            header('Location: ?page=elections');
            exit;
        }
        if ($candidat['statut'] != 'EN_COURS') {

            $_SESSION['error'] = "Election non active";

            header('Location: ?page=elections');
            exit;
        }



        // ----------------------------------------------------
        // TOKEN
        // ----------------------------------------------------

        $tokenVote = $this->genererTokenVote();



        // ----------------------------------------------------
        // TRANSACTION
        // ----------------------------------------------------

        try {

            $this->pdo->beginTransaction();



            // INSERT VOTE

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



            // UPDATE ELECTEUR

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



            // COMMIT

            $this->pdo->commit();



            $_SESSION['success'] = "Vote enregistré avec succès";

            $_SESSION['tokenVote'] = $tokenVote;



            header('Location: ?page=confirmation');
            exit;

        } 
        
        catch (PDOException $e) {

            $this->pdo->rollBack();

            $_SESSION['error'] = "Erreur SQL";

            header('Location: ?page=elections');
            exit;
        }
    }

    // --------------------------------------------------------
    // CONFIRMATION VOTE
    // --------------------------------------------------------
    public function confirmation()
    {
        $verification = $this->verifierElecteur();
        
        if ($verification['status'] == 'error') {
        
            header('Location: ?page=login');
            exit;
        }
        
        require_once __DIR__ . '/../views/vote/confirmation.php';
    }
}
?>
