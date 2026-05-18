<?php
$pageTitle = 'Tableau de bord Observateur - MaVoix CI';
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
                <h2><i class="bi bi-speedometer2"></i> Tableau de bord Observateur</h2>
                <span class="text-muted"><?= date('d/m/Y H:i') ?></span>
            </div>
            
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i>
                <strong>Mode Observateur :</strong> Vous avez accès en lecture seule aux données des élections et aux résultats en temps réel.
            </div>
            
            <!-- Statistiques globales -->
            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <div class="card stat-card border-start border-primary border-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-1">Élections actives</h6>
                                    <h3 class="mb-0"><?= $stats['elections_actives'] ?? 0 ?></h3>
                                </div>
                                <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                                    <i class="bi bi-calendar-check text-primary fs-4"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card stat-card border-start border-success border-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-1">Total candidats</h6>
                                    <h3 class="mb-0"><?= $stats['total_candidats'] ?? 0 ?></h3>
                                </div>
                                <div class="bg-success bg-opacity-10 rounded-circle p-3">
                                    <i class="bi bi-people text-success fs-4"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card stat-card border-start border-info border-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-1">Votes enregistrés</h6>
                                    <h3 class="mb-0"><?= $stats['total_votes'] ?? 0 ?></h3>
                                </div>
                                <div class="bg-info bg-opacity-10 rounded-circle p-3">
                                    <i class="bi bi-check2-square text-info fs-4"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Élections en cours -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-broadcast"></i> Élections en cours</span>
                    <a href="observateur-elections.php" class="btn btn-sm btn-light">Voir toutes</a>
                </div>
                <div class="card-body">
                    <?php if (!empty($elections_en_cours)): ?>
                        <div class="row g-3">
                            <?php foreach ($elections_en_cours as $election): ?>
                                <div class="col-md-6">
                                    <div class="card border-success">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h6 class="mb-0"><?= htmlspecialchars($election['titre']) ?></h6>
                                                <span class="badge bg-success">En cours</span>
                                            </div>
                                            <p class="small text-muted mb-2">
                                                <?= htmlspecialchars(substr($election['description'] ?? '', 0, 80)) ?>...
                                            </p>
                                            <div class="d-flex justify-content-between small text-muted mb-2">
                                                <span>Fin: <?= date('d/m/Y H:i', strtotime($election['date_Fin'])) ?></span>
                                                <span><strong><?= $election['votes'] ?? 0 ?></strong> votes</span>
                                            </div>
                                            <a href="observateur-resultats.php?id=<?= $election['id_Election'] ?>" 
                                               class="btn btn-sm btn-outline-success w-100">
                                                <i class="bi bi-bar-chart"></i> Voir les résultats
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-calendar-x fs-1"></i>
                            <p class="mt-2">Aucune élection en cours actuellement</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Accès rapides -->
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <i class="bi bi-lightning"></i> Accès rapides
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="observateur-elections.php" class="btn btn-outline-primary">
                                    <i class="bi bi-calendar-event"></i> Consulter les élections
                                </a>
                                <a href="observateur-resultats.php" class="btn btn-outline-success">
                                    <i class="bi bi-bar-chart"></i> Résultats en temps réel
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <i class="bi bi-clock-history"></i> Activité récente
                        </div>
                        <div class="card-body">
                            <?php if (!empty($activite_recente)): ?>
                                <ul class="list-unstyled mb-0">
                                    <?php foreach ($activite_recente as $activite): ?>
                                        <li class="border-bottom py-2">
                                            <small class="text-muted">
                                                <?= date('d/m H:i', strtotime($activite['date'])) ?>
                                            </small>
                                            <br>
                                            <?= htmlspecialchars($activite['description']) ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <p class="text-muted mb-0 text-center">Aucune activité récente</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
