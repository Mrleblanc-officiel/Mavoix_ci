<?php
$pageTitle = 'Gestion des Élections - MaVoix CI';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 px-0">
            <?php require_once __DIR__ . '/../layouts/sidebar.php'; ?>
        </div>
        
        <!-- Main Content -->
        <div class="col-md-9 col-lg-10 py-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="bi bi-calendar-event"></i> Gestion des Élections</h2>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createElectionModal">
                    <i class="bi bi-plus-lg"></i> Nouvelle élection
                </button>
            </div>
            
            <?php if (isset($success)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle"></i> <?= htmlspecialchars($success) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle"></i> <?= htmlspecialchars($error) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <!-- Liste des élections -->
            <div class="row g-4">
                <?php if (!empty($elections)): ?>
                    <?php foreach ($elections as $election): ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="card h-100">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <span class="fw-bold"><?= htmlspecialchars($election['titre']) ?></span>
                                    <?php
                                    $badgeClass = match($election['statut']) {
                                        'EN_COURS' => 'bg-success',
                                        'A_VENIR' => 'bg-warning text-dark',
                                        'TERMINER' => 'bg-secondary',
                                        default => 'bg-secondary'
                                    };
                                    $statutLabel = match($election['statut']) {
                                        'EN_COURS' => 'En cours',
                                        'A_VENIR' => 'À venir',
                                        'TERMINER' => 'Terminée',
                                        default => $election['statut']
                                    };
                                    ?>
                                    <span class="badge <?= $badgeClass ?>"><?= $statutLabel ?></span>
                                </div>
                                <div class="card-body">
                                    <p class="text-muted small mb-3">
                                        <?= htmlspecialchars(substr($election['description'] ?? '', 0, 100)) ?>...
                                    </p>
                                    
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between small text-muted mb-1">
                                            <span><i class="bi bi-calendar-check"></i> Début:</span>
                                            <span><?= date('d/m/Y H:i', strtotime($election['date_Debut'])) ?></span>
                                        </div>
                                        <div class="d-flex justify-content-between small text-muted">
                                            <span><i class="bi bi-calendar-x"></i> Fin:</span>
                                            <span><?= date('d/m/Y H:i', strtotime($election['date_Fin'])) ?></span>
                                        </div>
                                    </div>
                                    
                                    <?php if ($election['statut'] == 'EN_COURS'): ?>
                                        <div class="progress mb-3" style="height: 8px;">
                                            <?php
                                            $debut = strtotime($election['date_Debut']);
                                            $fin = strtotime($election['date_Fin']);
                                            $now = time();
                                            $progress = min(100, max(0, ($now - $debut) / ($fin - $debut) * 100));
                                            ?>
                                            <div class="progress-bar bg-success" style="width: <?= $progress ?>%"></div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="card-footer bg-transparent">
                                    <div class="btn-group w-100">
                                        <button class="btn btn-outline-primary btn-sm" 
                                                onclick="editElection(<?= htmlspecialchars(json_encode($election)) ?>)">
                                            <i class="bi bi-pencil"></i> Modifier
                                        </button>
                                        <a href="admin-candidats.php?election=<?= $election['id_Election'] ?>" 
                                           class="btn btn-outline-info btn-sm">
                                            <i class="bi bi-people"></i> Candidats
                                        </a>
                                        <?php if ($election['statut'] != 'TERMINER'): ?>
                                            <form method="POST" action="index.php?action=cloturer_election" class="d-inline">
                                                <input type="hidden" name="id_Election" value="<?= $election['id_Election'] ?>">
                                                <button type="submit" class="btn btn-outline-danger btn-sm"
                                                        onclick="return confirm('Êtes-vous sûr de vouloir clôturer cette élection ?')">
                                                    <i class="bi bi-stop-circle"></i> Clôturer
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body text-center py-5">
                                <i class="bi bi-calendar-x fs-1 text-muted"></i>
                                <h5 class="mt-3">Aucune élection</h5>
                                <p class="text-muted">Créez votre première élection pour commencer</p>
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createElectionModal">
                                    <i class="bi bi-plus-lg"></i> Créer une élection
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal Créer Élection -->
<div class="modal fade" id="createElectionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-plus-circle"></i> Nouvelle élection</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="index.php?action=creer_election">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Titre de l'élection *</label>
                        <input type="text" class="form-control" name="titre" required 
                               placeholder="Ex: Élection présidentielle 2024">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3" 
                                  placeholder="Décrivez l'élection..."></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Date de début *</label>
                            <input type="datetime-local" class="form-control" name="date_Debut" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Date de fin *</label>
                            <input type="datetime-local" class="form-control" name="date_Fin" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-plus-lg"></i> Créer l'élection
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Modifier Élection -->
<div class="modal fade" id="editElectionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-pencil"></i> Modifier l'élection</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="index.php?action=modifier_election">
                <input type="hidden" name="id_Election" id="edit_id_Election">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Titre de l'élection *</label>
                        <input type="text" class="form-control" name="titre" id="edit_titre" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" id="edit_description" rows="3"></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Date de début *</label>
                            <input type="datetime-local" class="form-control" name="date_Debut" id="edit_date_Debut" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Date de fin *</label>
                            <input type="datetime-local" class="form-control" name="date_Fin" id="edit_date_Fin" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg"></i> Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editElection(election) {
    document.getElementById('edit_id_Election').value = election.id_Election;
    document.getElementById('edit_titre').value = election.titre;
    document.getElementById('edit_description').value = election.description || '';
    document.getElementById('edit_date_Debut').value = election.date_Debut.replace(' ', 'T');
    document.getElementById('edit_date_Fin').value = election.date_Fin.replace(' ', 'T');
    
    new bootstrap.Modal(document.getElementById('editElectionModal')).show();
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
