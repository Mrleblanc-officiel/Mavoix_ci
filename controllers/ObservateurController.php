<?php
/*
|------------------------------------------------------------
| ObservateurController
|------------------------------------------------------------
| Responsabilités :
| - consulter les élections (lecture seule)
| - consulter les résultats en temps réel
| - consulter les candidats
|
| IMPORTANT :
| Rôle 2 = Observateur.
| AUCUNE modification autorisée.
|------------------------------------------------------------
*/


require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/Election.php';
require_once __DIR__ . '/../models/Candidat.php';
require_once __DIR__ . '/../models/Vote.php';

class ObservateurController
{
    private $electionModel;
    private $candidatModel;
    private $voteModel;

    public function __construct()
    {
        $this->electionModel = new Election();
        $this->candidatModel = new Candidat();
        $this->voteModel     = new Vote();
    }

    // --------------------------------------------------------
    // Vérification Observateur connecté
    // --------------------------------------------------------
    private function verifierObservateur()
    {
        if (
            !isset($_SESSION['user'])
            || !in_array($_SESSION['user']['id_Role'], [1, 2]) // Admin ou Observateur
        ) {
            return false;
        }
        return true;
    }

    // --------------------------------------------------------
    // LISTE DES ELECTIONS
    // --------------------------------------------------------
    public function elections()
    {
        if (!$this->verifierObservateur()) {
            header('Location: ?page=login');
        exit;
        }

        $elections = $this->electionModel->listerToutes();

        require_once __DIR__ . '/../views/observateur/election.php';
    }

// --------------------------------------------------------
// CANDIDATS D'UNE ELECTION
// --------------------------------------------------------
public function candidats($id_Election)
{
    if (!$this->verifierObservateur()) {

        header('Location: ?page=login');
        exit;
    }

    $election = $this->electionModel->findById($id_Election);

    if (!$election) {

        echo "Election introuvable";
        exit;
    }

    $candidats = $this->candidatModel->listerParElection($id_Election);

    require_once __DIR__ . '/../views/observateur/candidats.php';
}

// --------------------------------------------------------
// RESULTATS EN TEMPS REEL
// --------------------------------------------------------
public function resultats($id_Election)
{
    if (!$this->verifierObservateur()) {

        header('Location: ?page=login');
        exit;
    }

    $election = $this->electionModel->findById($id_Election);

    if (!$election) {

        echo "Election introuvable";
        exit;
    }

    $resultats  = $this->voteModel->resultatsParElection($id_Election);

    $totalVotes = $this->voteModel->totalParElection($id_Election);

    require_once __DIR__ . '/../views/observateur/resultats.php';
}

// --------------------------------------------------------
// DASHBOARD OBSERVATEUR
// --------------------------------------------------------
public function dashboard()
{
    if (!$this->verifierObservateur()) {
        header('Location: ?page=login');
        exit;
    }

    $elections = $this->electionModel->listerToutes();

    require_once __DIR__ . '/../views/observateur/dashboard.php';
}



// --------------------------------------------------------
// DETAILS D'UNE ELECTION
// --------------------------------------------------------
public function viewElection($id_Election)
{
    if (!$this->verifierObservateur()) {
        return ['status' => 'error', 'message' => 'Accès refusé'];
    }

    $election = $this->electionModel->findById($id_Election);

    if (!$election) {
        return ['status' => 'error', 'message' => 'Election introuvable'];
    }

    require_once __DIR__ . '/../views/observateur/election-details.php';
}



// --------------------------------------------------------
// RAPPORTS / STATISTIQUES
// --------------------------------------------------------
public function rapports()
{
    if (!$this->verifierObservateur()) {
        return ['status' => 'error', 'message' => 'Accès refusé'];
    }

    $elections = $this->electionModel->listerToutes();

    require_once __DIR__ . '/../views/observateur/rapports.php';
}
}
?>
