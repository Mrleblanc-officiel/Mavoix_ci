<?php
/*
|------------------------------------------------------------
| Routes principales – MaVoix CI
|------------------------------------------------------------
| Point d'entrée unique : index.php?page=XXX
|------------------------------------------------------------
*/

// Démarrage session (une seule fois ici)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
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

    // --------------------------------------------------------
    // AUTH
    // --------------------------------------------------------

    case 'login':
        $error = $success = '';
        require __DIR__ . '/../view/auth/login.php';
        break;

    case 'login-process':
        $controller = new AuthController();
        $result     = $controller->login($_POST['email'] ?? '', $_POST['password'] ?? '');

        if ($result['status'] === 'success') {
            // Rediriger vers OTP, passer OTP dev en session temporaire
            $_SESSION['otp_dev'] = $result['otp_dev'] ?? null;
            header('Location: ?page=otp');
            exit;
        }
        $error = $result['message'];
        require __DIR__ . '/../view/auth/login.php';
        break;

    case 'otp':
        $otp_dev = $_SESSION['otp_dev'] ?? null;
        $error   = '';
        require __DIR__ . '/../view/auth/otp.php';
        break;

    case 'otp-verify':
        $controller = new AuthController();
        $digits     = $_POST['digit'] ?? [];
        $otpSaisi   = implode('', $digits);
        $result     = $controller->verifierOTP($otpSaisi);

        if ($result['status'] === 'success') {
            unset($_SESSION['otp_dev']);
            header('Location: ?page=' . $result['redirect']);
            exit;
        }
        $otp_dev = $_SESSION['otp_dev'] ?? null;
        $error   = $result['message'];
        require __DIR__ . '/../view/auth/otp.php';
        break;

    case 'logout':
        $controller = new AuthController();
        $controller->deconnexion();
        header('Location: ?page=login');
        exit;

    // --------------------------------------------------------
    // VOTE (Electeur)
    // --------------------------------------------------------

    case 'accueil':
    case 'home':
        if (!isset($_SESSION['user'])) { header('Location: ?page=login'); exit; }
        $controller = new VoteController();
        $result     = $controller->electionsDisponibles();
        $elections  = $result['data'] ?? [];
        $user       = $_SESSION['user'];
        require __DIR__ . '/../view/vote/accueil.php';
        break;

    case 'vote':
        if (!isset($_SESSION['user'])) { header('Location: ?page=login'); exit; }
        $controller = new ElectionController();
        $id         = (int)($_GET['id'] ?? 0);
        $res        = $controller->recupererElections();
        // Trouver l'élection
        $election = null;
        foreach ($res['data'] ?? [] as $e) {
            if ($e['id_Election'] == $id) { $election = $e; break; }
        }
        if (!$election) { echo "Election introuvable"; break; }
        $cc       = new CandidatController();
        $cRes     = $cc->recupererCandidats();
        $candidats = array_filter($cRes['data'] ?? [], fn($c) => $c['id_Election'] == $id && $c['id_Actif']);
        $candidats = array_values($candidats);
        $error = '';
        require __DIR__ . '/../view/vote/bulletin.php';
        break;

    case 'vote-submit':
        if (!isset($_SESSION['user'])) { header('Location: ?page=login'); exit; }
        $controller = new VoteController();
        $result     = $controller->voter((int)($_POST['id_Candidat'] ?? 0), (int)($_POST['id_Election'] ?? 0));
        if ($result['status'] === 'success') {
            // Token preuve
            echo "<script>alert('✅ Vote enregistré !\\nToken preuve : " . addslashes($result['token']) . "'); window.location='?page=accueil';</script>";
        } else {
            echo "<script>alert('❌ " . addslashes($result['message']) . "'); history.back();</script>";
        }
        break;

    // --------------------------------------------------------
    // ADMIN
    // --------------------------------------------------------

    case 'admin-dashboard':
        if (!isset($_SESSION['user']) || $_SESSION['user']['id_Role'] != 1) { header('Location: ?page=login'); exit; }
        $admin = new AdminController();
        $res   = $admin->statistiques();
        $stats = $res['data'] ?? [];
        require __DIR__ . '/../view/admin/dashboard.php';
        break;

    // --------------------------------------------------------
    // OBSERVATEUR
    // --------------------------------------------------------

    case 'observateur-dashboard':
        if (!isset($_SESSION['user'])) { header('Location: ?page=login'); exit; }
        $obs       = new ObservateurController();
        $resEl     = $obs->elections();
        $elections = [];
        foreach ($resEl['data'] ?? [] as $election) {
            $r = $obs->resultats($election['id_Election']);
            $elections[] = [
                'election'    => $election,
                'resultats'   => $r['resultats'] ?? [],
                'total_votes' => $r['total_votes'] ?? 0
            ];
        }
        require __DIR__ . '/../view/observateur/dashboard.php';
        break;

    default:
        http_response_code(404);
        echo "<h2>404 – Page introuvable</h2>";
}
?>
