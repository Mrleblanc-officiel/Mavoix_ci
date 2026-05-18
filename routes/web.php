<?php
/*
|------------------------------------------------------------
| Routes principales – MaVoix CI
|------------------------------------------------------------
| Point d'entrée unique : index.php?page=XXX
|------------------------------------------------------------
*/

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$_SESSION['user'] = [

    'id_Utilisateur' => 8,
    'nom'            => 'leblanc',
    'email'          => 'daou@gmail.com',
    'id_Role'        => 1
];

function isAuthenticated()
{
    return isset($_SESSION['user']);
}


function isAdmin()
{
    return isset($_SESSION['user'])
        && $_SESSION['user']['id_Role'] == 1;
}

function isObservateur()
{
    return isset($_SESSION['user'])
        && $_SESSION['user']['id_Role'] == 2;
}


require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/VoteController.php';
require_once __DIR__ . '/../controllers/ElectionController.php';
require_once __DIR__ . '/../controllers/CandidatController.php';
require_once __DIR__ . '/../controllers/AdminController.php';
require_once __DIR__ . '/../controllers/ObservateurController.php';



$page = $_GET['page'] ?? 'login';

switch ($page) {

    case 'presentation':

        require_once __DIR__ . '/../views/public/presentation.php'; 

    break;

    case 'admin':
            if (!isAdmin()) {
                header('Location: ?page=login');
                exit;
            }
    break;

    case 'vote-submit':

        if (!isAdmin()) {
            header('Location: ?page=vote-submit');
            exit;
        }
        
        require_once __DIR__ . '/../views/vote/vote-submit.php';
    
    break;

    /*
    |--------------------------------------------------------------------------
    | AUTHENTIFICATION
    |--------------------------------------------------------------------------
    */

    // PAGE LOGIN
    case 'login':

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $controller = new AuthController();
            $controller->login($_POST);

        } else {

            require_once __DIR__ . '/../views/auth/login.php';

        }

        break;


    // PAGE INSCRIPTION
    case 'register':

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $controller = new AuthController();
            $controller->register($_POST);

        } else {

            require_once __DIR__ . '/../views/auth/register.php';

        }

    break;


    // VERIFICATION OTP
    case 'otp':

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $controller = new AuthController();
            $controller->verifyOtp($_POST);

        } else {

            require_once __DIR__ . '/../views/auth/otp.php';

        }

    break;


    // DECONNEXION
    case 'logout':

        $controller = new AuthController();
        $controller->logout();

    break;

    /*
    |--------------------------------------------------------------------------
    | VOTE / ELECTEUR
    |--------------------------------------------------------------------------
    */
        
        
    // ACCUEIL ELECTEUR
    case 'accueil':
    
        require_once __DIR__ . '/../views/vote/accueil.php';
    
    break;
    
    
    
    // LISTE DES ELECTIONS
    case 'elections':
    
        $controller = new VoteController();
        $controller->elections();
    
        break;
    
    
    
    // BULLETIN DE VOTE
    case 'vote':
    
        if (isset($_GET['id'])) {
    
            $controller = new VoteController();
            $controller->bulletin($_GET['id']);

        header('Location: ?page=confirmation');
        exit;
        } 
        else {
    
            echo "Election introuvable";
    
        }
        break;
    
    
    
    // SOUMISSION DU VOTE
    case 'vote-submit':
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
            $controller = new VoteController();
    
            $controller->voter($_POST);
    
        } else {
    
            echo "Méthode non autorisée";
    
        }
    
        break;
    
    
    
    // CONFIRMATION APRES VOTE
    case 'confirmation':
    
        require_once __DIR__ . '/../views/vote/confirmation.php';
    
        break;
    
    
    
    // RESULTATS
    case 'resultats':
    
        $controller = new VoteController();
        $controller->resultats();
    
        break;
    
    
    
    // HISTORIQUE UTILISATEUR
    case 'historique':
    
        $controller = new VoteController();
        $controller->historique();
    
        break;
    
/*
|--------------------------------------------------------------------------
| ADMIN
|--------------------------------------------------------------------------
*/


    // DASHBOARD ADMIN
case 'admin-dashboard':

    if (!isAdmin()) {

        header('Location: ?page=login');
        exit;
    }


    $controller = new AdminController();
    $controller->dashboard();

    break;



// GESTION DES ELECTIONS
case 'admin-elections':

        if (!isAdmin()) {

        header('Location: ?page=login');
        exit;
    }


    $controller = new AdminController();
    $controller->elections();

    break;



// AJOUT ELECTION

case 'admin-election-create':

    if (!isAdmin()) {

        header('Location: ?page=login');
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $controller = new ElectionController();

        $controller->creerElection(
            $_POST['titre'],
            $_POST['description'],
            $_POST['date_Debut'],
            $_POST['date_Fin']
        );
    }

    break;

// MODIFIER ELECTION
case 'admin-election-edit':

        if (!isAdmin()) {

        header('Location: ?page=login');
        exit;
    }


    if (isset($_GET['id'])) {

        $controller = new AdminController();
        $controller->editElection($_GET['id']);

    }

    break;



// SUPPRIMER ELECTION
case 'admin-election-delete':

        if (!isAdmin()) {

        header('Location: ?page=login');
        exit;
    }


    if (isset($_GET['id'])) {

        $controller = new AdminController();
        $controller->deleteElection($_GET['id']);

    }

    break;



// GESTION CANDIDATS
case 'admin-candidats':

        if (!isAdmin()) {

        header('Location: ?page=login');
        exit;
    }


    $controller = new AdminController();
    $controller->candidats();

    break;



// AJOUT CANDIDAT
case 'admin-candidat-create':

    if (!isAdmin()) {

        header('Location: ?page=login');
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $controller = new AdminController();
        $controller->createCandidat($_POST);

    }

    break;



// MODIFIER CANDIDAT
case 'admin-candidat-edit':

    if (!isAdmin()) {

        header('Location: ?page=login');
        exit;
    }

    if (isset($_GET['id'])) {

        $controller = new AdminController();
        $controller->editCandidat($_GET['id']);

    }

    break;



// SUPPRIMER CANDIDAT
case 'admin-candidat-delete':

    if (!isAdmin()) {

        header('Location: ?page=login');
        exit;
    }

    if (isset($_GET['id'])) {

        $controller = new AdminController();
        $controller->deleteCandidat($_GET['id']);

    }

    break;



// GESTION UTILISATEURS
case 'admin-utilisateurs':

    if (!isAdmin()) {

        header('Location: ?page=login');
        exit;
    }

    $controller = new AdminController();
    $controller->utilisateurs();

    break;



// AUDIT / LOGS
case 'admin-logs':

    if (!isAdmin()) {

        header('Location: ?page=login');
        exit;
    }

    $controller = new AdminController();
    $controller->auditLogs();

    break;



// RESULTATS
case 'admin-resultats':

    if (!isAdmin()) {

        header('Location: ?page=login');
        exit;
    }

    $controller = new AdminController();
    $controller->resultats();

    break;


/*
|--------------------------------------------------------------------------
| OBSERVATEUR
|--------------------------------------------------------------------------
*/


// DASHBOARD OBSERVATEUR
case 'observateur-dashboard':

    if (!isObservateur()) {

        header('Location: ?page=login');
    exit;
    }

    $controller = new ObservateurController();
    $controller->dashboard();

    break;



// LISTE DES ELECTIONS
case 'observateur-elections':

    if (!isObservateur()) {

        header('Location: ?page=login');
    exit;
    }

    $controller = new ObservateurController();
    $controller->elections();

    break;



// DETAILS D’UNE ELECTION
case 'observateur-election-view':

    if (!isObservateur()) {

        header('Location: ?page=login');
    exit;
    }


    if (isset($_GET['id'])) {

        $controller = new ObservateurController();
        $controller->viewElection($_GET['id']);

    } else {

        echo "Election introuvable";

    }

    break;



// LISTE DES CANDIDATS
case 'observateur-candidats':

    if (!isObservateur()) {

        header('Location: ?page=login');
    exit;
    }

    if (isset($_GET['id'])) {

        $controller = new ObservateurController();
        $controller->viewCandidat($_GET['id']);

    } else {

        echo "Candidat introuvable";

    }

    break;



// RESULTATS
case 'observateur-resultats':

    if (!isObservateur()) {

        header('Location: ?page=login');
    exit;
    }

    $controller = new ObservateurController();
    $controller->resultats();

    break;



// RAPPORTS
case 'observateur-rapports':

    if (!isObservateur()) {

        header('Location: ?page=login');
    exit;
    }

    if (isset($_GET['id'])) {

        $controller = new ObservateurController();
        $controller->resultats($_GET['id']);
    }

    break;
    
    /*
    -------------------------------------------------------------------
            DEFAULT
    ------------------------------------------------------------------
    */
        default:

        echo "404 - Page introuvable";

        break;
}

?>