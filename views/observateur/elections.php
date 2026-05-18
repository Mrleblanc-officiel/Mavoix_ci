<?php
$pageTitle = 'Élections - MaVoix CI';
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
                <h2><i class="bi bi-calendar-event"></i> Élections</h2>
            </div>
            
            <!-- Filtres -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-4">
                            <input type="text" class="form-control" name="search" 
                                   placeholder="Rechercher..."
                                   value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                        </div>
                        <div class="col-md-4">
                            <select class="form-select" name="statut">
                                <option value="">Tous les statuts</option>
                                <option value="EN_COURS" <?= ($_GET['statut'] ?? '') == 'EN_COURS' ? 'selected' : '' ?>>En cours</option>
                                <option value="A_VENIR" <?= ($_GET['statut'] ?? '') == 'A_VENIR' ? 'selected' : '' ?>>À venir</option>
                                <option value="TERMINER" <?= ($_GET['statut'] ?? '') == 'TERMINER' ? 'selected' : '' ?>>Terminées</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-outline-primary">
                                <i class="bi bi-search"></i> Filtrer
                            </button>
                            <a href="observateur-elections.php" class="btn btn-outline-secondary">
                                <i class="bi bi-x-lg"></i> Réinitialiser
                            </a>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Liste des élections -->
            <div class="row g-4">
                <?php if (!empty($elections)): ?>
                    <?php foreach ($elections as $election): ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="card h-100">
                                <div class="card-header">
                                    <div class="d-flex justify-content-between align-items-center">
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
                                </div>
                                <div class="card-body">
                                    <p class="text-muted small mb-3">
                                        <?= htmlspecialchars(substr($election['description'] ?? 'Aucune description', 0, 100)) ?>
                                        <?php if (strlen($election['description'] ?? '') > 100): ?>...<?php endif; ?>
                                    </p>
                                    
                                    <div class="border rounded p-2 mb-3 bg-light">
                                        <div class="row text-center">
                                            <div class="col-6 border-end">
                                                <small class="text-muted d-block">Début</small>
                                                <strong class="small"><?= date('d/m/Y', strtotime($election['date_Debut'])) ?></strong>
                                            </div>
                                            <div class="col-6">
                                                <small class="text-muted d-block">Fin</small>
                                                <strong class="small"><?= date('d/m/Y', strtotime($election['date_Fin'])) ?></strong>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span class="text-muted small">
                                            <i class="bi bi-people"></i> <?= $election['nb_candidats'] ?? 0 ?> candidat(s)
                                        </span>
                                        <span class="text-muted small">
                                            <i class="bi bi-check2-square"></i> <?= $election['nb_votes'] ?? 0 ?> vote(s)
                                        </span>
                                    </div>
                                </div>
                                <div class="card-footer bg-transparent">
                                    <div class="d-grid gap-2">
                                        <a href="observateur-candidats.php?election=<?= $election['id_Election'] ?>" 
                                           class="btn btn-outline-primary btn-sm">
                                            <i class="bi bi-people"></i> Voir les candidats
                                        </a>
                                        <?php if ($election['statut'] != 'A_VENIR'): ?>
                                            <a href="observateur-resultats.php?id=<?= $election['id_Election'] ?>" 
                                               class="btn btn-outline-success btn-sm">
                                                <i class="bi bi-bar-chart"></i> Voir les résultats
                                            </a>
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
                                <h5 class="mt-3">Aucune élection trouvée</h5>
                                <p class="text-muted">Il n'y a pas d'élection correspondant à vos critères.</p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
