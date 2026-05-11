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

    // --------------------------------------------------------
        // DASHBOARD ADMIN
    // --------------------------------------------------------
    public function dashboard()
    {
    if (!$this->verifierAdmin()) {

        header('Location: ?page=login');
        exit;
    }

    $electionModel = new Election();

    $elections = $electionModel->listerToutes();

    require_once __DIR__ . '/../views/admin/dashboard.php';
    }

    private function verifierAdmin()
    {
        return isset($_SESSION['user']) && $_SESSION['user']['id_Role'] == 1;
    }

    // --------------------------------------------------------
    // LISTE UTILISATEURS
    // --------------------------------------------------------//
     
    public function utilisateurs()
    {
    if (!$this->verifierAdmin()) {

        header('Location: ?page=login');
        exit;
    }

    $model = new Utilisateur();

    $utilisateurs = $model->listerTous();

    require_once __DIR__ . '/../views/admin/utilisateurs.php';
    }

    // --------------------------------------------------------
    // ACTIVER / DÉSACTIVER UTILISATEUR
    // --------------------------------------------------------
    public function setActifUtilisateur($id_Utilisateur, $actif)
    {
        if (!$this->verifierAdmin()) {
            header('Location: ?page=login');
            exit;
        }

        $model = new Utilisateur();
        $ok = $model->setActif($id_Utilisateur, $actif);

        require_once __DIR__ . '/../views/admin/utilisateur.php';
    }

    public function elections()
{
    $electionModel = new Election();

    $elections = $electionModel->listerToutes();

    require_once __DIR__ . '/../views/admin/elections.php';
}

    // --------------------------------------------------------
    // LISTE ELECTEURS
    // --------------------------------------------------------
    public function electeurs()
    {
    if (!$this->verifierAdmin()) {

        header('Location: ?page=login');
        exit;
    }

    $model = new Electeur();

    $electeurs = $model->listerTous();

    require_once __DIR__ . '/../views/admin/electeurs.php';
    }
    //Modification de l'election//

    public function editElection($id_Election)
{
    $sql = "
        SELECT *
        FROM Election
        WHERE id_Election = :id_Election
        LIMIT 1
    ";

    $stmt = $this->pdo->prepare($sql);

    $stmt->execute([

        ':id_Election' => $id_Election
    ]);

    $election = $stmt->fetch(PDO::FETCH_ASSOC);

    require_once __DIR__ . '/../views/admin/election.php';
}

// --------------------------------------------------------
// STATISTIQUES GLOBALES
// --------------------------------------------------------
    public function statistiques()
    {
        if (!$this->verifierAdmin()) {

            header('Location: ?page=login');
            exit;
        }

        $utilisateurModel = new Utilisateur();
        $electionModel    = new Election();
        $candidatModel    = new Candidat();
        $voteModel        = new Vote();

        $elections = $electionModel->listerToutes();

        $stats = [
            'utilisateurs' => count($utilisateurModel->listerTous()),
            'elections'    => count($electionModel->listerToutes()),
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

        require_once __DIR__ . '/../views/admin/statistiques.php';
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

    // --------------------------------------------------------
    // LISTE DES CANDIDATS
    // --------------------------------------------------------
    public function candidats()
    {
        if (!$this->verifierAdmin()) {
    
            header('Location: ?page=login');
            exit;
        }
    
        $model = new Candidat();
    
        $candidats = $model->listerTous();
    
        require_once __DIR__ . '/../views/admin/candidats.php';
    }
    //-----------------------------
    // RESULTATS DES VOTES
    //----------------------------
    public function resultats()
{
    if (!$this->verifierAdmin()) {

        header('Location: ?page=login');
        exit;
    }

    $electionModel = new Election();
    $voteModel     = new Vote();

    $elections = $electionModel->listerToutes();

    $resultats = [];

    foreach ($elections as $election) {

        $resultats[] = [

            'election' => $election,
            'votes'    => $voteModel->resultatsParElection(
                $election['id_Election']
            ),
            'total'    => $voteModel->totalParElection(
                $election['id_Election']
            )
        ];
    }

    require_once __DIR__ . '/../views/admin/resultats.php';
}

}
?>