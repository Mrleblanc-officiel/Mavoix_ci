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
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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

    public function login($email, $motDePasse)
    {

        // ----------------------------------------------------
        // Nettoyage données utilisateur
        // ----------------------------------------------------

        $email = trim(htmlspecialchars($email));
        $motDePasse = trim($motDePasse);


        // ----------------------------------------------------
        // Recherche utilisateur
        // ----------------------------------------------------

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


        // ----------------------------------------------------
        // Vérification utilisateur
        // ----------------------------------------------------

        if (!$user) {

            return [
                'status' => 'error',
                'message' => 'Utilisateur introuvable'
            ];
        }


        // ----------------------------------------------------
        // Vérification compte actif
        // ----------------------------------------------------

        if (!$user['estActif']) {

            return [
                'status' => 'error',
                'message' => 'Compte désactivé'
            ];
        }


        // ----------------------------------------------------
        // Vérification mot de passe
        // ----------------------------------------------------

        if (!password_verify($motDePasse, $user['motDePasseHash'])) {

            return [
                'status' => 'error',
                'message' => 'Mot de passe incorrect'
            ];
        }


        // ----------------------------------------------------
        // Sécurisation session
        // ----------------------------------------------------

        session_regenerate_id(true);


        // ----------------------------------------------------
        // Génération OTP sécurisé
        // ----------------------------------------------------

        $otp = random_int(100000, 999999);

        // Hash OTP avant stockage
        $otpHash = password_hash($otp, PASSWORD_DEFAULT);

        // Expiration OTP : 5 minutes
        $expiration = date(
            'Y-m-d H:i:s',
            strtotime('+5 minutes')
        );


        // ----------------------------------------------------
        // Suppression anciens OTP
        // ----------------------------------------------------

        $deleteOTP = "
            DELETE FROM OTP
            WHERE id_Utilisateur = :id_Utilisateur
        ";

        $stmtDelete = $this->pdo->prepare($deleteOTP);

        $stmtDelete->execute([
            ':id_Utilisateur' => $user['id_Utilisateur']
        ]);


        // ----------------------------------------------------
        // Insertion OTP sécurisé
        // ----------------------------------------------------

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
            ':expiration'    => $expiration
        ]);


        // ----------------------------------------------------
        // Session temporaire avant validation OTP
        // ----------------------------------------------------

        $_SESSION['temp_user'] = [

            'id_Utilisateur' => $user['id_Utilisateur'],
            'email'         => $user['email'],
            'id_Role'        => $user['id_Role']
        ];


        // ----------------------------------------------------
        // Retour succès
        // ----------------------------------------------------
        // IMPORTANT :
        // En réel :
        // - envoyer OTP par SMS
        // - ou email
        //
        // Ici :
        // - affiché pour développement
        // ----------------------------------------------------

        return [

            'status' => 'success',
            'message' => 'OTP généré',
            'otp_dev' => $otp
        ];
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

            return [

                'status' => 'error',
                'message' => 'Session expirée'
            ];
        }


        // ----------------------------------------------------
        // Récupération utilisateur temporaire
        // ----------------------------------------------------

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

            return [

                'status' => 'error',
                'message' => 'OTP introuvable'
            ];
        }


        // ----------------------------------------------------
        // Vérification expiration
        // ----------------------------------------------------

        if (strtotime($otpData['expiration']) < time()) {

            return [

                'status' => 'error',
                'message' => 'OTP expiré'
            ];
        }


        // ----------------------------------------------------
        // Vérification OTP
        // ----------------------------------------------------

        if (!password_verify($otpSaisi, $otpData['code_Hash'])) {

            return [

                'status' => 'error',
                'message' => 'OTP invalide'
            ];
        }


        // ----------------------------------------------------
        // Marquer OTP comme utilisé
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

                $redirect = 'admin-dashboard.php';
                break;

            case 2:

                $redirect = 'observateur-dashboard.php';
                break;

            default:

                $redirect = 'accueil.php';
        }


        // ----------------------------------------------------
        // Retour succès
        // ----------------------------------------------------

        return [

            'status' => 'success',
            'message' => 'Connexion validée',
            'redirect' => $redirect
        ];
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


        // ----------------------------------------------------
        // Retour succès
        // ----------------------------------------------------

        return [

            'status' => 'success',
            'message' => 'Déconnexion réussie',
            'redirect' => 'login.php'
        ];
    }
}
?>