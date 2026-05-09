<?php
/*
|------------------------------------------------------------
| AdminController
|------------------------------------------------------------
| Responsabilités :
| - gérer les utilisateurs
| - gérer les électeurs
| - consulter les logs d'audit
| - consulter les statistiques globales
|------------------------------------------------------------
*/

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/Utilisateur.php';
require_once __DIR__ . '/../models/Electeur.php';
require_once __DIR__ . '/../models/Election.php';
require_once __DIR__ . '/../models/Candidat.php';
require_once __DIR__ . '/../models/Vote.php';
require_once __DIR__ . '/../models/AuditLog.php';

class AdminController
{
    private $pdo;

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }

    private function verifierAdmin()
    {
        return isset($_SESSION['user']) && $_SESSION['user']['id_Role'] == 1;
    }

    // --------------------------------------------------------
    // LISTE UTILISATEURS
    // --------------------------------------------------------
    public function utilisateurs()
    {
        if (!$this->verifierAdmin()) {
            return ['status' => 'error', 'message' => 'Accès refusé'];
        }

        $model = new Utilisateur();
        return ['status' => 'success', 'data' => $model->listerTous()];
    }

    // --------------------------------------------------------
    // ACTIVER / DÉSACTIVER UTILISATEUR
    // --------------------------------------------------------
    public function setActifUtilisateur($id_Utilisateur, $actif)
    {
        if (!$this->verifierAdmin()) {
            return ['status' => 'error', 'message' => 'Accès refusé'];
        }

        $model = new Utilisateur();
        $ok = $model->setActif($id_Utilisateur, $actif);

        return $ok
            ? ['status' => 'success', 'message' => 'Statut mis à jour']
            : ['status' => 'error', 'message' => 'Utilisateur introuvable'];
    }

    // --------------------------------------------------------
    // LISTE ELECTEURS
    // --------------------------------------------------------
    public function electeurs()
    {
        if (!$this->verifierAdmin()) {
            return ['status' => 'error', 'message' => 'Accès refusé'];
        }

        $model = new Electeur();
        return ['status' => 'success', 'data' => $model->listerTous()];
    }

    // --------------------------------------------------------
    // STATISTIQUES GLOBALES
    // --------------------------------------------------------
    public function statistiques()
    {
        if (!$this->verifierAdmin()) {
            return ['status' => 'error', 'message' => 'Accès refusé'];
        }

        $utilisateurModel = new Utilisateur();
        $electionModel    = new Election();
        $candidatModel    = new Candidat();
        $voteModel        = new Vote();

        $elections = $electionModel->listerToutes();

        $stats = [
            'utilisateurs' => count($utilisateurModel->listerTous()),
            'elections'    => count($elections),
            'candidats'    => count($candidatModel->listerTous()),
            'elections_detail' => []
        ];

        foreach ($elections as $election) {
            $stats['elections_detail'][] = [
                'id'          => $election['id_Election'],
                'titre'       => $election['titre'],
                'statut'      => $election['statut'],
                'total_votes' => $voteModel->totalParElection($election['id_Election'])
            ];
        }

        return ['status' => 'success', 'data' => $stats];
    }

    // --------------------------------------------------------
    // LOGS D'AUDIT
    // --------------------------------------------------------
    public function auditLogs($limite = 50)
    {
        if (!$this->verifierAdmin()) {
            return ['status' => 'error', 'message' => 'Accès refusé'];
        }

        $audit = new AuditLog();
        return ['status' => 'success', 'data' => $audit->listerRecents($limite)];
    }
}
?>