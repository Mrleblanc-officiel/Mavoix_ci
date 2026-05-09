<?php
// ------------------------------------------------------------
// CandidatController
// ------------------------------------------------------------
// Responsabilités :
// - ajouter un candidat
// - modifier un candidat
// - désactiver un candidat
// - récupérer les candidats
//
// IMPORTANT :
// Seul un ADMIN peut gérer les candidats.
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
// Classe CandidatController
// ------------------------------------------------------------

class CandidatController
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
    // AJOUTER UN CANDIDAT
    // --------------------------------------------------------
    // Ajoute un candidat à une élection
    // --------------------------------------------------------

    public function ajouterCandidat(
        $nom,
        $prenom,
        $photo,
        $description,
        $id_Election,
        $id_Parti = null
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

        $nom = trim(htmlspecialchars($nom));
        $prenom = trim(htmlspecialchars($prenom));
        $description = trim(htmlspecialchars($description));


        // ----------------------------------------------------
        // Vérification champs obligatoires
        // ----------------------------------------------------

        if (
            empty($nom)
            || empty($prenom)
            || empty($id_Election)
        ) {

            return [

                'status' => 'error',
                'message' => 'Champs obligatoires manquants'
            ];
        }


        // ----------------------------------------------------
        // Vérification existence élection
        // ----------------------------------------------------

        $sqlElection = "
            SELECT *
            FROM Election
            WHERE id_Election = :id_Election
            LIMIT 1
        ";

        $stmtElection = $this->pdo->prepare($sqlElection);

        $stmtElection->execute([

            ':id_Election' => $id_Election
        ]);

        $election = $stmtElection->fetch(PDO::FETCH_ASSOC);


        if (!$election) {

            return [

                'status' => 'error',
                'message' => 'Election introuvable'
            ];
        }


        // ----------------------------------------------------
        // Vérification parti politique si fourni
        // ----------------------------------------------------

        if ($id_Parti !== null) {

            $sqlParti = "
                SELECT *
                FROM PartiPolitique
                WHERE id_Parti = :id_Parti
                LIMIT 1
            ";

            $stmtParti = $this->pdo->prepare($sqlParti);

            $stmtParti->execute([

                ':id_Parti' => $id_Parti
            ]);

            $parti = $stmtParti->fetch(PDO::FETCH_ASSOC);


            if (!$parti) {

                return [

                    'status' => 'error',
                    'message' => 'Parti politique introuvable'
                ];
            }
        }


        // ----------------------------------------------------
        // Insertion candidat
        // ----------------------------------------------------

        try {

            $sql = "
                INSERT INTO Candidat (
                    nom,
                    prenom,
                    photo,
                    description,
                    id_Actif,
                    id_Election,
                    id_Parti
                )
                VALUES (
                    :nom,
                    :prenom,
                    :photo,
                    :description,
                    TRUE,
                    :id_Election,
                    :id_Parti
                )
            ";

            $stmt = $this->pdo->prepare($sql);

            $stmt->execute([

                ':nom'         => $nom,
                ':prenom'      => $prenom,
                ':photo'       => $photo,
                ':description' => $description,
                ':id_Election'  => $id_Election,
                ':id_Parti'     => $id_Parti
            ]);


            // ------------------------------------------------
            // Récupération ID candidat
            // ------------------------------------------------

            $id_Candidat = $this->pdo->lastInsertId();


            // ------------------------------------------------
            // Création ligne résultat temporaire
            // ------------------------------------------------

            $sqlResultat = "
                INSERT INTO ResultatTemp (
                    nombre_Voix,
                    pourcentage,
                    id_Candidat
                )
                VALUES (
                    0,
                    0,
                    :id_Candidat
                )
            ";

            $stmtResultat = $this->pdo->prepare($sqlResultat);

            $stmtResultat->execute([

                ':id_Candidat' => $id_Candidat
            ]);


            return [

                'status' => 'success',
                'message' => 'Candidat ajouté avec succès'
            ];

        } catch (PDOException $e) {

            return [

                'status' => 'error',
                'message' => 'Erreur SQL : ' . $e->getMessage()
            ];
        }
    }



    // --------------------------------------------------------
    // MODIFIER CANDIDAT
    // --------------------------------------------------------
    // Permet de modifier :
    // - nom
    // - prénom
    // - photo
    // - description
    // - parti politique
    // --------------------------------------------------------

    public function modifierCandidat(
        $id_Candidat,
        $nom,
        $prenom,
        $photo,
        $description,
        $id_Parti = null
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
        // Vérification candidat
        // ----------------------------------------------------

        $sqlCheck = "
            SELECT *
            FROM Candidat
            WHERE id_Candidat = :id_Candidat
            LIMIT 1
        ";

        $stmtCheck = $this->pdo->prepare($sqlCheck);

        $stmtCheck->execute([

            ':id_Candidat' => $id_Candidat
        ]);

        $candidat = $stmtCheck->fetch(PDO::FETCH_ASSOC);


        if (!$candidat) {

            return [

                'status' => 'error',
                'message' => 'Candidat introuvable'
            ];
        }


        // ----------------------------------------------------
        // Mise à jour candidat
        // ----------------------------------------------------

        try {

            $sql = "
                UPDATE Candidat
                SET
                    nom = :nom,
                    prenom = :prenom,
                    photo = :photo,
                    description = :description,
                    id_Parti = :id_Parti
                WHERE id_Candidat = :id_Candidat
            ";

            $stmt = $this->pdo->prepare($sql);

            $stmt->execute([

                ':nom'         => $nom,
                ':prenom'      => $prenom,
                ':photo'       => $photo,
                ':description' => $description,
                ':id_Parti'     => $id_Parti,
                ':id_Candidat'  => $id_Candidat
            ]);


            return [

                'status' => 'success',
                'message' => 'Candidat modifié avec succès'
            ];

        } catch (PDOException $e) {

            return [

                'status' => 'error',
                'message' => 'Erreur SQL : ' . $e->getMessage()
            ];
        }
    }



    // --------------------------------------------------------
    // DESACTIVER CANDIDAT
    // --------------------------------------------------------
    // Désactive le candidat sans le supprimer
    // --------------------------------------------------------

    public function desactiverCandidat($id_Candidat)
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
        // Vérification existence candidat
        // ----------------------------------------------------

        $sqlCheck = "
            SELECT *
            FROM Candidat
            WHERE id_Candidat = :id_Candidat
            LIMIT 1
        ";

        $stmtCheck = $this->pdo->prepare($sqlCheck);

        $stmtCheck->execute([

            ':id_Candidat' => $id_Candidat
        ]);

        $candidat = $stmtCheck->fetch(PDO::FETCH_ASSOC);


        if (!$candidat) {

            return [

                'status' => 'error',
                'message' => 'Candidat introuvable'
            ];
        }


        // ----------------------------------------------------
        // Vérifie si déjà désactivé
        // ----------------------------------------------------

        if (!$candidat['id_Actif']) {

            return [

                'status' => 'error',
                'message' => 'Candidat déjà désactivé'
            ];
        }


        // ----------------------------------------------------
        // Désactivation SQL
        // ----------------------------------------------------

        try {

            $sql = "
                UPDATE Candidat
                SET id_Actif = FALSE
                WHERE id_Candidat = :id_Candidat
            ";

            $stmt = $this->pdo->prepare($sql);

            $stmt->execute([

                ':id_Candidat' => $id_Candidat
            ]);


            return [

                'status' => 'success',
                'message' => 'Candidat désactivé avec succès'
            ];

        } catch (PDOException $e) {

            return [

                'status' => 'error',
                'message' => 'Erreur SQL : ' . $e->getMessage()
            ];
        }
    }



    // --------------------------------------------------------
    // RECUPERER TOUS LES CANDIDATS
    // --------------------------------------------------------
    // Retourne :
    // - candidat
    // - parti
    // - élection
    // --------------------------------------------------------

    public function recupererCandidats()
    {

        try {

            $sql = "
                SELECT
                    c.id_Candidat,
                    c.nom,
                    c.prenom,
                    c.photo,
                    c.description,
                    c.id_Actif,

                    p.nom AS nomParti,
                    p.sigle,

                    e.titre AS election

                FROM Candidat c

                LEFT JOIN PartiPolitique p
                ON c.id_Parti = p.id_Parti

                INNER JOIN Election e
                ON c.id_Election = e.id_Election

                ORDER BY c.nom ASC
            ";

            $stmt = $this->pdo->query($sql);

            $candidats = $stmt->fetchAll(PDO::FETCH_ASSOC);


            return [

                'status' => 'success',
                'data' => $candidats
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