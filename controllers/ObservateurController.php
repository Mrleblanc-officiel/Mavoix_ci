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

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
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
            return ['status' => 'error', 'message' => 'Accès refusé'];
        }

        $elections = $this->electionModel->listerToutes();

        return ['status' => 'success', 'data' => $elections];
    }

    // --------------------------------------------------------
    // CANDIDATS D'UNE ELECTION
    // --------------------------------------------------------
    public function candidats($id_Election)
    {
        if (!$this->verifierObservateur()) {
            return ['status' => 'error', 'message' => 'Accès refusé'];
        }

        $election = $this->electionModel->findById($id_Election);
        if (!$election) {
            return ['status' => 'error', 'message' => 'Election introuvable'];
        }

        $candidats = $this->candidatModel->listerParElection($id_Election);

        return ['status' => 'success', 'election' => $election, 'data' => $candidats];
    }

    // --------------------------------------------------------
    // RESULTATS EN TEMPS REEL
    // --------------------------------------------------------
    public function resultats($id_Election)
    {
        if (!$this->verifierObservateur()) {
            return ['status' => 'error', 'message' => 'Accès refusé'];
        }

        $election = $this->electionModel->findById($id_Election);
        if (!$election) {
            return ['status' => 'error', 'message' => 'Election introuvable'];
        }

        $resultats  = $this->voteModel->resultatsParElection($id_Election);
        $totalVotes = $this->voteModel->totalParElection($id_Election);

        return [
            'status'      => 'success',
            'election'    => $election,
            'resultats'   => $resultats,
            'total_votes' => $totalVotes
        ];
    }
}
?>
