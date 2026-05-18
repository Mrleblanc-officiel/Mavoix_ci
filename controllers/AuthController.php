<?php

// ------------------------------------------------------------
// AuthController
// ------------------------------------------------------------
// Responsabilités :
// - connexion utilisateur
// - vérification OTP
// - gestion session
// - déconnexion
//
// IMPORTANT :
// Ce controller ne doit PAS gérer :
// - candidats
// - élections
// - votes
//
// Ces fonctionnalités auront leurs propres controllers.
// ------------------------------------------------------------


// ------------------------------------------------------------
// Démarrage session
// ------------------------------------------------------------

// ------------------------------------------------------------
// Connexion base de données
// ------------------------------------------------------------

require_once __DIR__ . '/../config/db.php';

// ------------------------------------------------------------
// Classe AuthController
// ------------------------------------------------------------

class AuthController
{

    // --------------------------------------------------------
    // Propriété PDO
    // --------------------------------------------------------

    private $pdo;


    // --------------------------------------------------------
    // Constructeur
    // --------------------------------------------------------
    // Récupère l'objet PDO global
    // --------------------------------------------------------

    public function __construct()
    {
        global $pdo;

        $this->pdo = $pdo;
    }


    // --------------------------------------------------------
    // LOGIN
    // --------------------------------------------------------
    // Vérifie :
    // - utilisateur
    // - mot de passe
    // - compte actif
    //
    // Puis :
    // - génère OTP
    // - stocke session temporaire
    // --------------------------------------------------------

    // --------------------------------------------------------
    // INSCRIPTION
    // --------------------------------------------------------
    public function register($data)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

            require_once __DIR__ . '/../views/auth/register.php';
            return;
        }

        $nom        = trim($data['nom']);
        $email      = trim($data['email']);
        $motDePasse = password_hash($data['password'], PASSWORD_DEFAULT);
        $role       = $data['role'];



        // VERIFICATION EMAIL

        $sqlCheck = "
            SELECT *
            FROM Utilisateur
            WHERE email = :email
            LIMIT 1
        ";

        $stmtCheck = $this->pdo->prepare($sqlCheck);

        $stmtCheck->execute([

            ':email' => $email
        ]);

        if ($stmtCheck->fetch()) {

            $_SESSION['error'] = "Email déjà utilisé";

            header('Location: ?page=register');
            exit;
        }



        // INSERTION

        $sql = "
            INSERT INTO Utilisateur (
                nom,
                email,
                motDePasseHash,
                id_Role,
                est_Actif
            )
            VALUES (
                :nom,
                :email,
                :motDePasseHash,
                :id_Role,
                TRUE
            )
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([

            ':nom'             => $nom,
            ':email'           => $email,
            ':motDePasseHash'  => $motDePasse,
            ':id_Role'         => $role
        ]);


        $_SESSION['success'] = "Compte créé avec succès";

        header('Location: ?page=login');
        exit;
    }
    // --------------------------------------------------------
    // LOGIN
    // --------------------------------------------------------
    public function login($data)
    {
        $email = trim(htmlspecialchars($data['email']));
        $motDePasse = trim($data['password']);



        $sql = "
            SELECT *
            FROM Utilisateur
            WHERE email = :email
            LIMIT 1
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([

            ':email' => $email
        ]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);



        // UTILISATEUR

        if (!$user) {

            $_SESSION['error'] = "Utilisateur introuvable";

            header('Location: ?page=login');
            exit;
        }



        // ACTIF

        if (!$user['est_Actif']) {

            $_SESSION['error'] = "Compte désactivé";

            header('Location: ?page=login');
            exit;
        }



        // PASSWORD

        if (!password_verify($motDePasse, $user['motDePasseHash'])) {

            $_SESSION['error'] = "Mot de passe incorrect";

            header('Location: ?page=login');
            exit;
        }



        session_regenerate_id(true);



        // OTP

        $otp = random_int(100000, 999999);

        $otpHash = password_hash($otp, PASSWORD_DEFAULT);

        $expiration = date(
            'Y-m-d H:i:s',
            strtotime('+5 minutes')
        );



        // DELETE OLD OTP

        $deleteOTP = "
            DELETE FROM OTP
            WHERE id_Utilisateur = :id_Utilisateur
        ";

        $stmtDelete = $this->pdo->prepare($deleteOTP);

        $stmtDelete->execute([

            ':id_Utilisateur' => $user['id_Utilisateur']
        ]);

        // INSERT OTP

        $insertOTP = "
            INSERT INTO OTP (
                id_Utilisateur,
                code_Hash,
                expiration,
                utilise,
                date_Creation
            )
            VALUES (
                :id_Utilisateur,
                :code_Hash,
                :expiration,
                FALSE,
                NOW()
            )
        ";

        $stmtOTP = $this->pdo->prepare($insertOTP);

        $stmtOTP->execute([

            ':id_Utilisateur' => $user['id_Utilisateur'],
            ':code_Hash'      => $otpHash,
            ':expiration'     => $expiration
        ]);

        $_SESSION['temp_user'] = [

            'id_Utilisateur' => $user['id_Utilisateur'],
            'email'          => $user['email'],
            'id_Role'        => $user['id_Role']
        ];



        // DEV OTP

        $_SESSION['otp_dev'] = $otp;



        header('Location: ?page=otp');
        exit;
    }

    // --------------------------------------------------------
    // VERIFICATION OTP
    // --------------------------------------------------------
    // Vérifie :
    // - existence session temporaire
    // - OTP valide
    // - OTP non expiré
    // - OTP non utilisé
    // --------------------------------------------------------

    public function verifierOTP($otpSaisi)
    {

        // ----------------------------------------------------
        // Vérification session temporaire
        // ----------------------------------------------------

        if (!isset($_SESSION['temp_user'])) {
            
            $_SESSION['error'] = "Session expirée";
            
            header('Location: ?page=login');
            exit;
        }


        // ----------------------------------------------------
        // Récupération utilisateur temporaire
        // ----------------------------------------------------
        
        $otpSaisi = trim($data['otp']);

        $tempUser = $_SESSION['temp_user'];


        // ----------------------------------------------------
        // Recherche OTP
        // ----------------------------------------------------

        $sql = "
            SELECT *
            FROM OTP
            WHERE id_Utilisateur = :id_Utilisateur
            AND utilise = FALSE
            LIMIT 1
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([

            ':id_Utilisateur' => $tempUser['id_Utilisateur']
        ]);

        $otpData = $stmt->fetch(PDO::FETCH_ASSOC);


        // ----------------------------------------------------
        // Vérification existence OTP
        // ----------------------------------------------------

        if (!$otpData) {
        
            $_SESSION['error'] = "OTP introuvable";

             header('Location: ?page=otp');
            exit;
        }


        // ----------------------------------------------------
        // Vérification expiration
        // ----------------------------------------------------

 
        if (strtotime($otpData['expiration']) < time()) {

            $_SESSION['error'] = "OTP expiré";

            header('Location: ?page=otp');
            exit;
        }


        // ----------------------------------------------------
        // Vérification OTP
        // ----------------------------------------------------

        if (!password_verify($otpSaisi, $otpData['code_Hash'])) {

            $_SESSION['error'] = "OTP invalide";
            
            header('Location: ?page=otp');
            exit;
        }


        // ----------------------------------------------------
        // Marquer OTP comme utilisé ou mise a jour
        // ----------------------------------------------------

        $updateOTP = "
            UPDATE OTP
            SET utilise = TRUE
            WHERE idOTP = :idOTP
        ";

        $stmtUpdate = $this->pdo->prepare($updateOTP);

        $stmtUpdate->execute([

            ':idOTP' => $otpData['idOTP']
        ]);


        // ----------------------------------------------------
        // Création session finale utilisateur
        // ----------------------------------------------------

        $_SESSION['user'] = [

            'id_Utilisateur' => $tempUser['id_Utilisateur'],
            'email'         => $tempUser['email'],
            'id_Role'        => $tempUser['id_Role']
        ];


        // ----------------------------------------------------
        // Suppression session temporaire
        // ----------------------------------------------------

        unset($_SESSION['temp_user']);


        // ----------------------------------------------------
        // Redirection selon rôle
        // ----------------------------------------------------

        $redirect = 'login.php';


      switch ($_SESSION['user']['id_Role']) {

            case 1:

                header('Location: ?page=admin-dashboard');
            exit;

            case 2:

                header('Location: ?page=observateur-dashboard');
            exit;

            default:

                header('Location: ?page=accueil');
            exit;
        }

    }



    // --------------------------------------------------------
    // DECONNEXION
    // --------------------------------------------------------
    // Détruit complètement la session utilisateur
    // --------------------------------------------------------

    public function deconnexion()
    {

        // ----------------------------------------------------
        // Suppression variables session
        // ----------------------------------------------------

        session_unset();


        // ----------------------------------------------------
        // Destruction session
        // ----------------------------------------------------

        session_destroy();


        header('Location: ?page=login');
        exit;
    }
}
?>