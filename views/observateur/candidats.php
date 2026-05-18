<?php
$pageTitle = 'Candidats - MaVoix CI';
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
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <a href="observateur-elections.php" class="btn btn-outline-secondary btn-sm mb-2">
                            <i class="bi bi-arrow-left"></i> Retour aux élections
                        </a>
                        <h2><i class="bi bi-people"></i> Candidats - <?= htmlspecialchars($election['titre']) ?></h2>
                    </div>
                </div>
                
                <!-- Informations de l'élection -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <p class="text-muted mb-0">
                                    <?= htmlspecialchars($election['description'] ?? 'Aucune description disponible') ?>
                                </p>
                            </div>
                            <div class="col-md-4 text-md-end">
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
                    </div>
                </div>
                
                <!-- Liste des candidats -->
                <div class="row g-4">
                    <?php if (!empty($candidats)): ?>
                        <?php foreach ($candidats as $candidat): ?>
                            <div class="col-md-6 col-lg-4">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <?php if (!empty($candidat['photo'])): ?>
                                            <img src="<?= htmlspecialchars($candidat['photo']) ?>" 
                                                 alt="<?= htmlspecialchars($candidat['nom']) ?>"
                                                 class="rounded-circle mb-3"
                                                 style="width: 100px; height: 100px; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="bg-secondary rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                                 style="width: 100px; height: 100px;">
                                                <i class="bi bi-person fs-1 text-white"></i>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <h5 class="card-title mb-1">
                                            <?= htmlspecialchars($candidat['prenom'] . ' ' . $candidat['nom']) ?>
                                        </h5>
                                        
                                        <?php if (!empty($candidat['nomParti'])): ?>
                                            <p class="text-primary mb-2">
                                                <i class="bi bi-flag"></i> 
                                                <?= htmlspecialchars($candidat['nomParti']) ?>
                                                <?php if (!empty($candidat['sigle'])): ?>
                                                    (<?= htmlspecialchars($candidat['sigle']) ?>)
                                                <?php endif; ?>
                                            </p>
                                        <?php else: ?>
                                            <p class="text-muted mb-2">
                                                <i class="bi bi-person"></i> Candidat indépendant
                                            </p>
                                        <?php endif; ?>
                                        
                                        <p class="small text-muted">
                                            <?= htmlspecialchars($candidat['description'] ?? 'Aucune description disponible') ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body text-center py-5">
                                    <i class="bi bi-person-x fs-1 text-muted"></i>
                                    <h5 class="mt-3">Aucun candidat</h5>
                                    <p class="text-muted">Aucun candidat n'est inscrit pour cette élection.</p>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                
                <?php if ($election['statut'] != 'A_VENIR'): ?>
                    <div class="mt-4 text-center">
                        <a href="observateur-resultats.php?id=<?= $election['id_Election'] ?>" class="btn btn-success btn-lg">
                            <i class="bi bi-bar-chart"></i> Voir les résultats
                        </a>
                    </div>
                <?php endif; ?>
                
            <?php else: ?>
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="bi bi-people"></i> Candidats</h2>
                </div>
                
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i>
                    Veuillez sélectionner une élection pour voir ses candidats.
                </div>
                
                <a href="observateur-elections.php" class="btn btn-primary">
                    <i class="bi bi-calendar-event"></i> Voir les élections
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
