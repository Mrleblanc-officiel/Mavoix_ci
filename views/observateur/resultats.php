<?php
$pageTitle = 'Résultats - MaVoix CI';
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
            <?php if (isset($election)): ?>
                <!-- Résultats d'une élection spécifique -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <a href="observateur-resultats.php" class="btn btn-outline-secondary btn-sm mb-2">
                            <i class="bi bi-arrow-left"></i> Retour
                        </a>
                        <h2><i class="bi bi-bar-chart"></i> <?= htmlspecialchars($election['titre']) ?></h2>
                    </div>
                    <div>
                        <?php
                        $badgeClass = match($election['statut']) {
                            'EN_COURS' => 'bg-success',
                            'A_VENIR' => 'bg-warning text-dark',
                            'TERMINER' => 'bg-secondary',
                            default => 'bg-secondary'
                        };
                        ?>
                        <span class="badge <?= $badgeClass ?> fs-6">
                            <?= htmlspecialchars($election['statut']) ?>
                        </span>
                    </div>
                </div>
                
                <!-- Statistiques de participation -->
                <div class="row g-4 mb-4">
                    <div class="col-md-4">
                        <div class="card bg-primary text-white">
                            <div class="card-body text-center">
                                <h3 class="mb-0"><?= $total_votes ?? 0 ?></h3>
                                <small>Votes exprimés</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <h3 class="mb-0"><?= number_format($taux_participation ?? 0, 1) ?>%</h3>
                                <small>Taux de participation</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-info text-white">
                            <div class="card-body text-center">
                                <h3 class="mb-0"><?= count($resultats ?? []) ?></h3>
                                <small>Candidats</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Graphique des résultats -->
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="bi bi-bar-chart-fill"></i> Résultats en temps réel
                        <?php if ($election['statut'] == 'EN_COURS'): ?>
                            <span class="badge bg-success ms-2">
                                <i class="bi bi-broadcast"></i> Live
                            </span>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($resultats)): ?>
                            <?php foreach ($resultats as $index => $resultat): ?>
                                <?php
                                $pourcentage = $total_votes > 0 
                                    ? round(($resultat['voix'] / $total_votes) * 100, 1) 
                                    : 0;
                                $barColor = $index === 0 ? 'bg-success' : ($index === 1 ? 'bg-primary' : 'bg-secondary');
                                ?>
                                <div class="mb-4">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div class="d-flex align-items-center">
                                            <?php if ($index === 0 && $election['statut'] == 'TERMINER'): ?>
                                                <i class="bi bi-trophy-fill text-warning me-2 fs-5"></i>
                                            <?php endif; ?>
                                            <div>
                                                <strong><?= htmlspecialchars($resultat['prenom'] . ' ' . $resultat['nom']) ?></strong>
                                                <?php if (!empty($resultat['parti'])): ?>
                                                    <br><small class="text-muted"><?= htmlspecialchars($resultat['parti']) ?></small>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <strong class="fs-5"><?= $pourcentage ?>%</strong>
                                            <br><small class="text-muted"><?= number_format($resultat['voix']) ?> voix</small>
                                        </div>
                                    </div>
                                    <div class="progress" style="height: 25px;">
                                        <div class="progress-bar <?= $barColor ?>" role="progressbar" 
                                             style="width: <?= $pourcentage ?>%">
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center py-4 text-muted">
                                <i class="bi bi-inbox fs-1"></i>
                                <p class="mt-2">Aucun vote enregistré pour le moment</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Auto-refresh pour les élections en cours -->
                <?php if ($election['statut'] == 'EN_COURS'): ?>
                    <div class="alert alert-info">
                        <i class="bi bi-arrow-clockwise"></i>
                        Les résultats sont actualisés automatiquement toutes les 30 secondes.
                        <span id="refresh-timer">30</span>s
                    </div>
                    <script>
                        let timer = 30;
                        setInterval(() => {
                            timer--;
                            document.getElementById('refresh-timer').textContent = timer;
                            if (timer <= 0) {
                                location.reload();
                            }
                        }, 1000);
                    </script>
                <?php endif; ?>
                
            <?php else: ?>
                <!-- Liste des élections pour consulter les résultats -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="bi bi-bar-chart"></i> Résultats des élections</h2>
                </div>
                
                <div class="row g-4">
                    <?php if (!empty($elections)): ?>
                        <?php foreach ($elections as $election): ?>
                            <div class="col-md-6 col-lg-4">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <h5 class="card-title"><?= htmlspecialchars($election['titre']) ?></h5>
                                            <?php
                                            $badgeClass = match($election['statut']) {
                                                'EN_COURS' => 'bg-success',
                                                'TERMINER' => 'bg-secondary',
                                                default => 'bg-warning'
                                            };
                                            ?>
                                            <span class="badge <?= $badgeClass ?>">
                                                <?= htmlspecialchars($election['statut']) ?>
                                            </span>
                                        </div>
                                        
                                        <p class="text-muted small">
                                            Du <?= date('d/m/Y', strtotime($election['date_Debut'])) ?>
                                            au <?= date('d/m/Y', strtotime($election['date_Fin'])) ?>
                                        </p>
                                        
                                        <div class="d-flex justify-content-between text-muted small mb-3">
                                            <span><i class="bi bi-people"></i> <?= $election['nb_candidats'] ?? 0 ?> candidats</span>
                                            <span><i class="bi bi-check2-square"></i> <?= $election['nb_votes'] ?? 0 ?> votes</span>
                                        </div>
                                    </div>
                                    <div class="card-footer bg-transparent">
                                        <?php if ($election['statut'] != 'A_VENIR'): ?>
                                            <a href="?id=<?= $election['id_Election'] ?>" class="btn btn-success w-100">
                                                <i class="bi bi-bar-chart"></i> Voir les résultats
                                            </a>
                                        <?php else: ?>
                                            <button class="btn btn-secondary w-100" disabled>
                                                <i class="bi bi-clock"></i> Élection à venir
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body text-center py-5">
                                    <i class="bi bi-bar-chart-line fs-1 text-muted"></i>
                                    <h5 class="mt-3">Aucune élection disponible</h5>
                                    <p class="text-muted">Les résultats seront disponibles lorsqu'une élection sera en cours ou terminée.</p>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
