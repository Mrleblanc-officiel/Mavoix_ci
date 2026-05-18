<?php
$pageTitle = 'Gestion des Électeurs - MaVoix CI';
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
                <h2><i class="bi bi-person-badge"></i> Gestion des Électeurs</h2>
                <div>
                    <button class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#importModal">
                        <i class="bi bi-upload"></i> Importer
                    </button>
                    <a href="export-electeurs.php" class="btn btn-outline-primary">
                        <i class="bi bi-download"></i> Exporter
                    </a>
                </div>
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
            
            <!-- Statistiques -->
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="mb-0">Total Électeurs</h6>
                                    <h3 class="mb-0"><?= $stats['total'] ?? 0 ?></h3>
                                </div>
                                <i class="bi bi-people fs-1 opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="mb-0">Ont voté</h6>
                                    <h3 class="mb-0"><?= $stats['votes'] ?? 0 ?></h3>
                                </div>
                                <i class="bi bi-check2-square fs-1 opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-warning text-dark">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="mb-0">N'ont pas voté</h6>
                                    <h3 class="mb-0"><?= ($stats['total'] ?? 0) - ($stats['votes'] ?? 0) ?></h3>
                                </div>
                                <i class="bi bi-hourglass-split fs-1 opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Filtres -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-4">
                            <input type="text" class="form-control" name="search" 
                                   placeholder="Rechercher par nom, CNI..."
                                   value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" name="vote">
                                <option value="">Statut de vote</option>
                                <option value="1" <?= ($_GET['vote'] ?? '') == '1' ? 'selected' : '' ?>>A voté</option>
                                <option value="0" <?= ($_GET['vote'] ?? '') == '0' ? 'selected' : '' ?>>N'a pas voté</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" name="region">
                                <option value="">Toutes les régions</option>
                                <!-- Ajouter les régions dynamiquement -->
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-outline-primary w-100">
                                <i class="bi bi-search"></i> Filtrer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Table des électeurs -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nom & Prénom</th>
                                    <th>N° CNI</th>
                                    <th>N° Électeur</th>
                                    <th>Bureau de vote</th>
                                    <th>A voté</th>
                                    <th>Date vote</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($electeurs)): ?>
                                    <?php foreach ($electeurs as $electeur): ?>
                                        <tr>
                                            <td><?= $electeur['id_Electeur'] ?></td>
                                            <td>
                                                <strong><?= htmlspecialchars($electeur['nom'] . ' ' . ($electeur['prenom'] ?? '')) ?></strong>
                                            </td>
                                            <td><code><?= htmlspecialchars($electeur['numeroCNI'] ?? 'N/A') ?></code></td>
                                            <td><code><?= htmlspecialchars($electeur['numeroElecteur'] ?? 'N/A') ?></code></td>
                                            <td><?= htmlspecialchars($electeur['bureauVote'] ?? 'N/A') ?></td>
                                            <td>
                                                <?php if ($electeur['aVote'] ?? false): ?>
                                                    <span class="badge bg-success"><i class="bi bi-check-lg"></i> Oui</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary"><i class="bi bi-x-lg"></i> Non</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($electeur['dateVote'] ?? false): ?>
                                                    <?= date('d/m/Y H:i', strtotime($electeur['dateVote'])) ?>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="admin-electeurs.php?action=voir&id=<?= $electeur['id_Electeur'] ?>" 
                                                   class="btn btn-sm btn-outline-info" title="Voir détails">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center py-4 text-muted">
                                            <i class="bi bi-person-badge fs-1"></i>
                                            <p class="mt-2">Aucun électeur trouvé</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Import -->
<div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="bi bi-upload"></i> Importer des électeurs</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="index.php?action=importer_electeurs" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Fichier CSV</label>
                        <input type="file" class="form-control" name="fichier" accept=".csv" required>
                        <div class="form-text">
                            Format: nom, prénom, email, numeroCNI, numeroElecteur, bureauVote
                        </div>
                    </div>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i>
                        <a href="template-electeurs.csv">Télécharger le modèle CSV</a>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-upload"></i> Importer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
