<?php
$pageTitle = 'Tableau de bord Admin - MaVoix CI';
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
                <h2><i class="bi bi-speedometer2"></i> Tableau de bord</h2>
                <span class="text-muted"><?= date('d/m/Y H:i') ?></span>
            </div>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle"></i> <?= htmlspecialchars($error) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <!-- Statistiques Cards -->
            <div class="row g-4 mb-4">
                <div class="col-md-6 col-xl-3">
                    <div class="card stat-card border-start border-primary border-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-1">Utilisateurs</h6>
                                    <h3 class="mb-0"><?= $stats['utilisateurs'] ?? 0 ?></h3>
                                </div>
                                <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                                    <i class="bi bi-people text-primary fs-4"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 col-xl-3">
                    <div class="card stat-card border-start border-success border-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-1">Élections</h6>
                                    <h3 class="mb-0"><?= $stats['elections'] ?? 0 ?></h3>
                                </div>
                                <div class="bg-success bg-opacity-10 rounded-circle p-3">
                                    <i class="bi bi-calendar-event text-success fs-4"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 col-xl-3">
                    <div class="card stat-card border-start border-warning border-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-1">Candidats</h6>
                                    <h3 class="mb-0"><?= $stats['candidats'] ?? 0 ?></h3>
                                </div>
                                <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                                    <i class="bi bi-person-lines-fill text-warning fs-4"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 col-xl-3">
                    <div class="card stat-card border-start border-info border-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-1">Total Votes</h6>
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
            
            <!-- Elections en cours -->
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-calendar-event"></i> Élections</span>
                            <a href="admin-elections.php" class="btn btn-sm btn-light">Voir tout</a>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($stats['elections_detail'])): ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Titre</th>
                                                <th>Statut</th>
                                                <th>Votes</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($stats['elections_detail'] as $election): ?>
                                                <tr>
                                                    <td>
                                                        <strong><?= htmlspecialchars($election['titre']) ?></strong>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        $badgeClass = match($election['statut']) {
                                                            'EN_COURS' => 'bg-success',
                                                            'A_VENIR' => 'bg-warning',
                                                            'TERMINER' => 'bg-secondary',
                                                            default => 'bg-secondary'
                                                        };
                                                        ?>
                                                        <span class="badge <?= $badgeClass ?>">
                                                            <?= htmlspecialchars($election['statut']) ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="fw-bold"><?= $election['total_votes'] ?></span>
                                                    </td>
                                                    <td>
                                                        <a href="admin-elections.php?action=voir&id=<?= $election['id'] ?>" 
                                                           class="btn btn-sm btn-outline-primary">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-4 text-muted">
                                    <i class="bi bi-calendar-x fs-1"></i>
                                    <p class="mt-2">Aucune élection pour le moment</p>
                                    <a href="admin-elections.php?action=creer" class="btn btn-primary">
                                        <i class="bi bi-plus-lg"></i> Créer une élection
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <i class="bi bi-lightning"></i> Actions rapides
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="admin-elections.php?action=creer" class="btn btn-outline-primary">
                                    <i class="bi bi-plus-circle"></i> Nouvelle élection
                                </a>
                                <a href="admin-candidats.php?action=ajouter" class="btn btn-outline-success">
                                    <i class="bi bi-person-plus"></i> Ajouter un candidat
                                </a>
                                <a href="admin-utilisateurs.php" class="btn btn-outline-info">
                                    <i class="bi bi-people"></i> Gérer les utilisateurs
                                </a>
                                <a href="admin-audit.php" class="btn btn-outline-secondary">
                                    <i class="bi bi-journal-text"></i> Voir les logs
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
