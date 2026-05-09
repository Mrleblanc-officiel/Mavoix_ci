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

if (session_status() === PHP_SESSION_NONE) {
    session_start(); 
    }
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

            return [

                'status' => 'error',
                'message' => 'Accès refusé'
            ];
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
        ) {

            return [

                'status' => 'error',
                'message' => 'Tous les champs sont obligatoires'
            ];
        }


        // ----------------------------------------------------
        // Vérification cohérence des dates
        // ----------------------------------------------------

        if (strtotime($date_Fin) <= strtotime($date_Debut)) {

            return [

                'status' => 'error',
                'message' => 'La date de fin doit être supérieure à la date de début'
            ];
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


            // ------------------------------------------------
            // Retour succès
            // ------------------------------------------------

            return [

                'status' => 'success',
                'message' => 'Election créée avec succès'
            ];

        } catch (PDOException $e) {

            return [

                'status' => 'error',
                'message' => 'Erreur SQL : ' . $e->getMessage()
            ];
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

            return [

                'status' => 'error',
                'message' => 'Accès refusé'
            ];
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

            return [

                'status' => 'error',
                'message' => 'Election introuvable'
            ];
        }


        // ----------------------------------------------------
        // Vérification dates
        // ----------------------------------------------------

        if (strtotime($date_Fin) <= strtotime($date_Debut)) {

            return [

                'status' => 'error',
                'message' => 'Dates invalides'
            ];
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


            return [

                'status' => 'success',
                'message' => 'Election modifiée avec succès'
            ];

        } catch (PDOException $e) {

            return [

                'status' => 'error',
                'message' => 'Erreur SQL : ' . $e->getMessage()
            ];
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

            return [

                'status' => 'error',
                'message' => 'Accès refusé'
            ];
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

            return [

                'status' => 'error',
                'message' => 'Election introuvable'
            ];
        }


        // ----------------------------------------------------
        // Vérifie si déjà terminée
        // ----------------------------------------------------

        if ($election['statut'] == 'TERMINER') {

            return [

                'status' => 'error',
                'message' => 'Election déjà clôturée'
            ];
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


            return [

                'status' => 'success',
                'message' => 'Election clôturée avec succès'
            ];

        } catch (PDOException $e) {

            return [

                'status' => 'error',
                'message' => 'Erreur SQL : ' . $e->getMessage()
            ];
        }
    }



    // --------------------------------------------------------
    // RECUPERER TOUTES LES ELECTIONS
    // --------------------------------------------------------
    // Retourne la liste complète
    // --------------------------------------------------------

    public function recupererElections()
    {

        try {

            $sql = "
                SELECT *
                FROM Election
                ORDER BY dateCreation DESC
            ";

            $stmt = $this->pdo->query($sql);

            $elections = $stmt->fetchAll(PDO::FETCH_ASSOC);


            return [

                'status' => 'success',
                'data' => $elections
            ];

        } catch (PDOException $e) {

            return [

                'status' => 'error',
                'message' => 'Erreur SQL : ' . $e->getMessage()
            ];
        }
    }
}
?>