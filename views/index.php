<?php
/**
 * Point d'entrée principal de l'application MaVoix CI
 * 
 * Ce fichier gère le routage des requêtes vers les contrôleurs appropriés
 * et charge les vues correspondantes.
 */

// Démarrage session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Chargement des contrôleurs
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/AdminController.php';
require_once __DIR__ . '/../controllers/ElectionController.php';
require_once __DIR__ . '/../controllers/CandidatController.php';
require_once __DIR__ . '/../controllers/VoteController.php';
require_once __DIR__ . '/../controllers/ObservateurController.php';

// Récupération de l'action
$action = $_GET['action'] ?? 'home';

// Instanciation des contrôleurs
$authController = new AuthController();
$adminController = new AdminController();
$electionController = new ElectionController();
$candidatController = new CandidatController();
$voteController = new VoteController();
$observateurController = new ObservateurController();

// Routage des actions
switch ($action) {
    
    // ==================== AUTH ====================
    
    case 'login':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = $authController->login($_POST['email'], $_POST['motDePasse']);
            
            if ($result['status'] === 'success') {
                $otp_dev = $result['otp_dev'] ?? null;
                require_once __DIR__ . '/auth/otp.php';
            } else {
                $error = $result['message'];
                require_once __DIR__ . '/auth/login.php';
            }
        } else {
            require_once __DIR__ . '/auth/login.php';
        }
        break;
        
    case 'verifier_otp':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = $authController->verifierOTP($_POST['otp']);
            
            if ($result['status'] === 'success') {
                header('Location: ' . $result['redirect']);
                exit;
            } else {
                $error = $result['message'];
                require_once __DIR__ . '/auth/otp.php';
            }
        } else {
            require_once __DIR__ . '/auth/otp.php';
        }
        break;
        
    case 'register':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $authController->register();
        } else {
            require_once __DIR__ . '/auth/register.php';
        }
        break;
        
    case 'deconnexion':
        $authController->deconnexion();
        header('Location: login.php');
        exit;
        break;
    
    // ==================== ADMIN ====================
    
    case 'admin_dashboard':
        $result = $adminController->statistiques();
        $stats = $result['data'] ?? [];
        require_once __DIR__ . '/admin/dashboard.php';
        break;
        
    case 'admin_utilisateurs':
        $result = $adminController->utilisateurs();
        $utilisateurs = $result['data'] ?? [];
        require_once __DIR__ . '/admin/utilisateurs.php';
        break;
        
    case 'activer_utilisateur':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = $adminController->setActifUtilisateur($_POST['id_Utilisateur'], true);
            $success = $result['status'] === 'success' ? $result['message'] : null;
            $error = $result['status'] === 'error' ? $result['message'] : null;
        }
        $result = $adminController->utilisateurs();
        $utilisateurs = $result['data'] ?? [];
        require_once __DIR__ . '/admin/utilisateurs.php';
        break;
        
    case 'desactiver_utilisateur':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = $adminController->setActifUtilisateur($_POST['id_Utilisateur'], false);
            $success = $result['status'] === 'success' ? $result['message'] : null;
            $error = $result['status'] === 'error' ? $result['message'] : null;
        }
        $result = $adminController->utilisateurs();
        $utilisateurs = $result['data'] ?? [];
        require_once __DIR__ . '/admin/utilisateurs.php';
        break;
        
    case 'admin_electeurs':
        $result = $adminController->electeurs();
        $electeurs = $result['data'] ?? [];
        require_once __DIR__ . '/admin/electeurs.php';
        break;
        
    case 'admin_audit':
        $result = $adminController->auditLogs();
        $logs = $result['data'] ?? [];
        require_once __DIR__ . '/admin/audit.php';
        break;
    
    // ==================== ELECTIONS ====================
    
    case 'admin_elections':
        $result = $electionController->recupererElections();
        $elections = $result['data'] ?? [];
        require_once __DIR__ . '/admin/elections.php';
        break;
        
    case 'creer_election':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = $electionController->creerElection(
                $_POST['titre'],
                $_POST['description'],
                $_POST['date_Debut'],
                $_POST['date_Fin']
            );
            $success = $result['status'] === 'success' ? $result['message'] : null;
            $error = $result['status'] === 'error' ? $result['message'] : null;
        }
        $result = $electionController->recupererElections();
        $elections = $result['data'] ?? [];
        require_once __DIR__ . '/admin/elections.php';
        break;
        
    case 'modifier_election':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = $electionController->modifierElection(
                $_POST['id_Election'],
                $_POST['titre'],
                $_POST['description'],
                $_POST['date_Debut'],
                $_POST['date_Fin']
            );
            $success = $result['status'] === 'success' ? $result['message'] : null;
            $error = $result['status'] === 'error' ? $result['message'] : null;
        }
        $result = $electionController->recupererElections();
        $elections = $result['data'] ?? [];
        require_once __DIR__ . '/admin/elections.php';
        break;
        
    case 'cloturer_election':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = $electionController->cloturerElection($_POST['id_Election']);
            $success = $result['status'] === 'success' ? $result['message'] : null;
            $error = $result['status'] === 'error' ? $result['message'] : null;
        }
        $result = $electionController->recupererElections();
        $elections = $result['data'] ?? [];
        require_once __DIR__ . '/admin/elections.php';
        break;
    
    // ==================== CANDIDATS ====================
    
    case 'admin_candidats':
        $result = $candidatController->recupererCandidats();
        $candidats = $result['data'] ?? [];
        $electionsResult = $electionController->recupererElections();
        $elections = $electionsResult['data'] ?? [];
        require_once __DIR__ . '/admin/candidats.php';
        break;
        
    case 'ajouter_candidat':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $photo = '';
            if (isset($_FILES['photo']) && $_FILES['photo']['error'] === 0) {
                $uploadDir = __DIR__ . '/../uploads/candidats/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                $photo = 'uploads/candidats/' . uniqid() . '_' . basename($_FILES['photo']['name']);
                move_uploaded_file($_FILES['photo']['tmp_name'], __DIR__ . '/../' . $photo);
            }
            
            $result = $candidatController->ajouterCandidat(
                $_POST['nom'],
                $_POST['prenom'],
                $photo,
                $_POST['description'],
                $_POST['id_Election'],
                $_POST['id_Parti'] ?: null
            );
            $success = $result['status'] === 'success' ? $result['message'] : null;
            $error = $result['status'] === 'error' ? $result['message'] : null;
        }
        $result = $candidatController->recupererCandidats();
        $candidats = $result['data'] ?? [];
        $electionsResult = $electionController->recupererElections();
        $elections = $electionsResult['data'] ?? [];
        require_once __DIR__ . '/admin/candidats.php';
        break;
        
    case 'modifier_candidat':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $photo = $_POST['photo_actuelle'] ?? '';
            if (isset($_FILES['photo']) && $_FILES['photo']['error'] === 0) {
                $uploadDir = __DIR__ . '/../uploads/candidats/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                $photo = 'uploads/candidats/' . uniqid() . '_' . basename($_FILES['photo']['name']);
                move_uploaded_file($_FILES['photo']['tmp_name'], __DIR__ . '/../' . $photo);
            }
            
            $result = $candidatController->modifierCandidat(
                $_POST['id_Candidat'],
                $_POST['nom'],
                $_POST['prenom'],
                $photo,
                $_POST['description'],
                $_POST['id_Parti'] ?: null
            );
            $success = $result['status'] === 'success' ? $result['message'] : null;
            $error = $result['status'] === 'error' ? $result['message'] : null;
        }
        $result = $candidatController->recupererCandidats();
        $candidats = $result['data'] ?? [];
        $electionsResult = $electionController->recupererElections();
        $elections = $electionsResult['data'] ?? [];
        require_once __DIR__ . '/admin/candidats.php';
        break;
        
    case 'desactiver_candidat':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = $candidatController->desactiverCandidat($_POST['id_Candidat']);
            $success = $result['status'] === 'success' ? $result['message'] : null;
            $error = $result['status'] === 'error' ? $result['message'] : null;
        }
        $result = $candidatController->recupererCandidats();
        $candidats = $result['data'] ?? [];
        $electionsResult = $electionController->recupererElections();
        $elections = $electionsResult['data'] ?? [];
        require_once __DIR__ . '/admin/candidats.php';
        break;
    
    // ==================== VOTE ====================
    
    case 'voter':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = $voteController->voter($_POST['id_Candidat']);
            
            if ($result['status'] === 'success') {
                $tokenVote = $result['tokenVote'];
                require_once __DIR__ . '/electeur/vote-succes.php';
            } else {
                $error = $result['message'];
                require_once __DIR__ . '/electeur/voter.php';
            }
        } else {
            require_once __DIR__ . '/electeur/voter.php';
        }
        break;
    
    // ==================== OBSERVATEUR ====================
    
    case 'observateur_elections':
        $result = $observateurController->elections();
        $elections = $result['data'] ?? [];
        require_once __DIR__ . '/observateur/elections.php';
        break;
        
    case 'observateur_candidats':
        $id_Election = $_GET['election'] ?? null;
        if ($id_Election) {
            $result = $observateurController->candidats($id_Election);
            $candidats = $result['data'] ?? [];
            $election = $result['election'] ?? null;
        }
        require_once __DIR__ . '/observateur/candidats.php';
        break;
        
    case 'observateur_resultats':
        $id_Election = $_GET['id'] ?? null;
        if ($id_Election) {
            $result = $observateurController->resultats($id_Election);
            $election = $result['election'] ?? null;
            $resultats = $result['resultats'] ?? [];
            $total_votes = $result['total_votes'] ?? 0;
        } else {
            $result = $observateurController->elections();
            $elections = $result['data'] ?? [];
        }
        require_once __DIR__ . '/observateur/resultats.php';
        break;
    
    // ==================== DEFAULT ====================
    
    case 'home':
    default:
        if (isset($_SESSION['user'])) {
            switch ($_SESSION['user']['id_Role']) {
                case 1:
                    header('Location: index.php?action=admin_dashboard');
                    break;
                case 2:
                    header('Location: index.php?action=observateur_elections');
                    break;
                default:
                    require_once __DIR__ . '/electeur/accueil.php';
            }
        } else {
            require_once __DIR__ . '/auth/login.php';
        }
        break;
}
?>
