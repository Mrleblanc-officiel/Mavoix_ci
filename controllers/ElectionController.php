<?php

// ------------------------------------------------------------
// ElectionController
// ------------------------------------------------------------
// Responsabilités :
// - créer une élection
// - modifier une élection
// - clôturer une élection
// - récupérer les élections
//
// IMPORTANT :
// Seul un ADMIN peut gérer les élections.
// ------------------------------------------------------------


// ------------------------------------------------------------
// Démarrage session
// ------------------------------------------------------------
// ------------------------------------------------------------
// Connexion base de données
// ------------------------------------------------------------

require_once __DIR__ . '/../config/db.php';

// ------------------------------------------------------------
// Classe ElectionController
// ------------------------------------------------------------

class ElectionController
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
    // Vérification ADMIN
    // --------------------------------------------------------
    // Empêche les utilisateurs non autorisés
    // d'accéder aux fonctionnalités admin
    // --------------------------------------------------------

    private function verifierAdmin()
    {

        if (
            !isset($_SESSION['user'])
            || $_SESSION['user']['id_Role'] != 1
        ) {

            return false;
        }

        return true;
    }



    // --------------------------------------------------------
    // CREER UNE ELECTION
    // --------------------------------------------------------
    // Crée une nouvelle élection
    // --------------------------------------------------------

    public function creerElection(
        $titre,
        $description,
        $date_Debut,
        $date_Fin
    )
    {

        // ----------------------------------------------------
        // Vérification ADMIN
        // ----------------------------------------------------

        if (!$this->verifierAdmin()) {
            header('Location: ?page=login');
        }


        // ----------------------------------------------------
        // Nettoyage données
        // ----------------------------------------------------

        $titre = trim(htmlspecialchars($titre));
        $description = trim(htmlspecialchars($description));

        // ----------------------------------------------------
        // Vérification champs vides
        // ----------------------------------------------------

        if (
            empty($titre)
            || empty($date_Debut)
            || empty($date_Fin)
        ) 
        {
            $_SESSION['error'] = "Tous les champs sont  obligatoires";

            header('Location: ?page=admin-election-create');
            exit;
        }


        // ----------------------------------------------------
        // Vérification cohérence des dates
        // ----------------------------------------------------

        if (strtotime($date_Fin) <= strtotime($date_Debut)) {

            $_SESSION['error'] = "Dates invalides";

            header('Location: ?page=admin-election-create');
            exit;
    
        }


        // ----------------------------------------------------
        // Insertion SQL
        // ----------------------------------------------------

        try {

            $sql = "
                INSERT INTO Election (
                    titre,
                    description,
                    date_Debut,
                    date_Fin,
                    statut
                )
                VALUES (
                    :titre,
                    :description,
                    :date_Debut,
                    :date_Fin,
                    'A_VENIR'
                )
            ";

            $stmt = $this->pdo->prepare($sql);

            $stmt->execute([

                ':titre'       => $titre,
                ':description' => $description,
                ':date_Debut'   => $date_Debut,
                ':date_Fin'     => $date_Fin
            ]);
            $_SESSION['success'] = "L'élection a été créée avec succès";

            header('Location: ?page=admin-elections');

        } catch (PDOException $e) {
             $_SESSION['error'] = "Erreur SQL";
             header('Location: ?page=admin-election-create');
             exit;
        }
    }



    // --------------------------------------------------------
    // MODIFIER UNE ELECTION
    // --------------------------------------------------------
    // Permet à l'ADMIN de modifier :
    // - titre
    // - description
    // - dates
    // --------------------------------------------------------

    public function modifierElection(
        $id_Election,
        $titre,
        $description,
        $date_Debut,
        $date_Fin
    )
    {

        // ----------------------------------------------------
        // Vérification ADMIN
        // ----------------------------------------------------

        if (!$this->verifierAdmin()) {
            header('Location: ?page=login');
            exit;
        }


        // ----------------------------------------------------
        // Vérification élection existante
        // ----------------------------------------------------

        $sqlCheck = "
            SELECT *
            FROM Election
            WHERE id_Election = :id_Election
            LIMIT 1
        ";

        $stmtCheck = $this->pdo->prepare($sqlCheck);

        $stmtCheck->execute([

            ':id_Election' => $id_Election
        ]);

        $election = $stmtCheck->fetch(PDO::FETCH_ASSOC);


        if (!$election) {
            $_SESSION['error'] = "Election introuvable";

            header('Location: ?page=admin-elections');
            exit;
        }


        // ----------------------------------------------------
        // Vérification dates
        // ----------------------------------------------------

        if (strtotime($date_Fin) <= strtotime($date_Debut)) {
            $_SESSION['error'] = "Dates invalides";

            header('Location: ?page=admin-election-edit&id=' . $id_Election);
            exit;
        }


        // ----------------------------------------------------
        // Mise à jour SQL
        // ----------------------------------------------------

        try {

            $sql = "
                UPDATE Election
                SET
                    titre = :titre,
                    description = :description,
                    date_Debut = :date_Debut,
                    date_Fin = :date_Fin
                WHERE id_Election = :id_Election
            ";

            $stmt = $this->pdo->prepare($sql);

            $stmt->execute([

                ':titre'       => $titre,
                ':description' => $description,
                ':date_Debut'   => $date_Debut,
                ':date_Fin'     => $date_Fin,
                ':id_Election'  => $id_Election
            ]);
            
            $_SESSION['success'] = "L'élection a été modifiée avec succès";
            header('Location: ?page=admin-elections');
            exit;

        } catch (PDOException $e) {
            $_SESSION['error'] = "Erreur SQL";
            header('Location: ?page=admin-election-edit&id=' . $id_Election);
            exit;
        }
    }



    // --------------------------------------------------------
    // CLOTURER UNE ELECTION
    // --------------------------------------------------------
    // Passe le statut à :
    // TERMINER
    // --------------------------------------------------------

    public function cloturerElection($id_Election)
    {

        // ----------------------------------------------------
        // Vérification ADMIN
        // ----------------------------------------------------

        if (!$this->verifierAdmin()) {
            header('Location: ?page=login');
            exit;
        }


        // ----------------------------------------------------
        // Vérification existence élection
        // ----------------------------------------------------

        $sqlCheck = "
            SELECT *
            FROM Election
            WHERE id_Election = :id_Election
            LIMIT 1
        ";

        $stmtCheck = $this->pdo->prepare($sqlCheck);

        $stmtCheck->execute([

            ':id_Election' => $id_Election
        ]);

        $election = $stmtCheck->fetch(PDO::FETCH_ASSOC);


        if (!$election) {

            $_SESSION['error'] = "Election introuvable";
            header('Location: ?page=admin-elections');
            exit;

        }


        // ----------------------------------------------------
        // Vérifie si déjà terminée
        // ----------------------------------------------------

        if ($election['statut'] == 'TERMINER') {

        $_SESSION['error'] = "L'élection est déjà terminée";
        header('Location: ?page=admin-elections');
        exit;

        }


        // ----------------------------------------------------
        // Mise à jour statut
        // ----------------------------------------------------

        try {

            $sql = "
                UPDATE Election
                SET statut = 'TERMINER'
                WHERE id_Election = :id_Election
            ";

            $stmt = $this->pdo->prepare($sql);

            $stmt->execute([

                ':id_Election' => $id_Election
            ]);
            $_SESSION['success'] = "L'élection a été clôturée avec succès";
            header('Location: ?page=admin-elections');
            exit;
        } catch (PDOException $e) {
                $_SESSION['error'] = "Erreur SQL"; 
            }
            
            header('Location: ?page=admin-elections');
            exit;
    
    }

        public function deleteElection($id_Election)
    {
        if (!$this->verifierAdmin()) {

            header('Location: ?page=login');
            exit;
        }

        try {

            $sql = "
                DELETE FROM Election
                WHERE id_Election = :id_Election
            ";

            $stmt = $this->pdo->prepare($sql);

            $stmt->execute([

                ':id_Election' => $id_Election
            ]);

            $_SESSION['success'] = "Election supprimée";

        } catch (PDOException $e) {

            $_SESSION['error'] = "Erreur SQL";
        }

        header('Location: ?page=admin-elections');
        exit;
    }

    // --------------------------------------------------------
    // RECUPERER TOUTES LES ELECTIONS
    // --------------------------------------------------------
    // Retourne la liste complète
    // --------------------------------------------------------
    public function recupererElections()
    {
        $sql = "
            SELECT *
            FROM Election
            ORDER BY dateCreation DESC
        ";
        
        $stmt = $this->pdo->query($sql);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}