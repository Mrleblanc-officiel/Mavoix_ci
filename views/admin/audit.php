<?php
$pageTitle = 'Logs d\'audit - MaVoix CI';
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
                <h2><i class="bi bi-journal-text"></i> Logs d'audit</h2>
                <a href="export-audit.php" class="btn btn-outline-primary">
                    <i class="bi bi-download"></i> Exporter
                </a>
            </div>
            
            <!-- Filtres -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Date début</label>
                            <input type="date" class="form-control" name="date_debut"
                                   value="<?= htmlspecialchars($_GET['date_debut'] ?? '') ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Date fin</label>
                            <input type="date" class="form-control" name="date_fin"
                                   value="<?= htmlspecialchars($_GET['date_fin'] ?? '') ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Type d'action</label>
                            <select class="form-select" name="action_type">
                                <option value="">Toutes les actions</option>
                                <option value="LOGIN" <?= ($_GET['action_type'] ?? '') == 'LOGIN' ? 'selected' : '' ?>>Connexion</option>
                                <option value="LOGOUT" <?= ($_GET['action_type'] ?? '') == 'LOGOUT' ? 'selected' : '' ?>>Déconnexion</option>
                                <option value="VOTE" <?= ($_GET['action_type'] ?? '') == 'VOTE' ? 'selected' : '' ?>>Vote</option>
                                <option value="CREATE" <?= ($_GET['action_type'] ?? '') == 'CREATE' ? 'selected' : '' ?>>Création</option>
                                <option value="UPDATE" <?= ($_GET['action_type'] ?? '') == 'UPDATE' ? 'selected' : '' ?>>Modification</option>
                                <option value="DELETE" <?= ($_GET['action_type'] ?? '') == 'DELETE' ? 'selected' : '' ?>>Suppression</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-outline-primary w-100">
                                <i class="bi bi-search"></i> Filtrer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Statistiques rapides -->
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body py-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <small>Total logs</small>
                                    <h4 class="mb-0"><?= count($logs ?? []) ?></h4>
                                </div>
                                <i class="bi bi-journal fs-3 opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body py-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <small>Connexions</small>
                                    <h4 class="mb-0"><?= $stats['connexions'] ?? 0 ?></h4>
                                </div>
                                <i class="bi bi-box-arrow-in-right fs-3 opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body py-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <small>Votes</small>
                                    <h4 class="mb-0"><?= $stats['votes'] ?? 0 ?></h4>
                                </div>
                                <i class="bi bi-check2-square fs-3 opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-dark">
                        <div class="card-body py-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <small>Modifications</small>
                                    <h4 class="mb-0"><?= $stats['modifications'] ?? 0 ?></h4>
                                </div>
                                <i class="bi bi-pencil-square fs-3 opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Table des logs -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-sm">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Date/Heure</th>
                                    <th>Utilisateur</th>
                                    <th>Action</th>
                                    <th>Détails</th>
                                    <th>IP</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($logs)): ?>
                                    <?php foreach ($logs as $log): ?>
                                        <tr>
                                            <td><small class="text-muted"><?= $log['id'] ?></small></td>
                                            <td>
                                                <small>
                                                    <?= date('d/m/Y H:i:s', strtotime($log['dateHeure'])) ?>
                                                </small>
                                            </td>
                                            <td>
                                                <?php if (!empty($log['utilisateur'])): ?>
                                                    <span class="badge bg-secondary">
                                                        <?= htmlspecialchars($log['utilisateur']) ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span class="text-muted">Anonyme</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php
                                                $actionClass = match($log['action'] ?? '') {
                                                    'LOGIN' => 'bg-success',
                                                    'LOGOUT' => 'bg-secondary',
                                                    'VOTE' => 'bg-primary',
                                                    'CREATE' => 'bg-info',
                                                    'UPDATE' => 'bg-warning text-dark',
                                                    'DELETE' => 'bg-danger',
                                                    default => 'bg-secondary'
                                                };
                                                ?>
                                                <span class="badge <?= $actionClass ?>">
                                                    <?= htmlspecialchars($log['action'] ?? 'N/A') ?>
                                                </span>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    <?= htmlspecialchars(substr($log['details'] ?? '', 0, 50)) ?>
                                                    <?php if (strlen($log['details'] ?? '') > 50): ?>
                                                        ...
                                                    <?php endif; ?>
                                                </small>
                                            </td>
                                            <td>
                                                <code class="small"><?= htmlspecialchars($log['adresseIP'] ?? 'N/A') ?></code>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">
                                            <i class="bi bi-journal-x fs-1"></i>
                                            <p class="mt-2">Aucun log d'audit trouvé</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <?php if (!empty($pagination)): ?>
                        <nav aria-label="Navigation des logs">
                            <ul class="pagination justify-content-center mb-0">
                                <li class="page-item <?= $pagination['current'] <= 1 ? 'disabled' : '' ?>">
                                    <a class="page-link" href="?page=<?= $pagination['current'] - 1 ?>">Précédent</a>
                                </li>
                                <?php for ($i = 1; $i <= $pagination['total']; $i++): ?>
                                    <li class="page-item <?= $i == $pagination['current'] ? 'active' : '' ?>">
                                        <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                                    </li>
                                <?php endfor; ?>
                                <li class="page-item <?= $pagination['current'] >= $pagination['total'] ? 'disabled' : '' ?>">
                                    <a class="page-link" href="?page=<?= $pagination['current'] + 1 ?>">Suivant</a>
                                </li>
                            </ul>
                        </nav>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
