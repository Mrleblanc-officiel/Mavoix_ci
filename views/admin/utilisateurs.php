<?php
$pageTitle = 'Gestion des Utilisateurs - MaVoix CI';
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
                <h2><i class="bi bi-people"></i> Gestion des Utilisateurs</h2>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                    <i class="bi bi-plus-lg"></i> Ajouter
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
            
            <!-- Filtres -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-4">
                            <input type="text" class="form-control" name="search" 
                                   placeholder="Rechercher par nom ou email..."
                                   value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" name="role">
                                <option value="">Tous les rôles</option>
                                <option value="1" <?= ($_GET['role'] ?? '') == '1' ? 'selected' : '' ?>>Admin</option>
                                <option value="2" <?= ($_GET['role'] ?? '') == '2' ? 'selected' : '' ?>>Observateur</option>
                                <option value="3" <?= ($_GET['role'] ?? '') == '3' ? 'selected' : '' ?>>Électeur</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" name="statut">
                                <option value="">Tous les statuts</option>
                                <option value="1" <?= ($_GET['statut'] ?? '') == '1' ? 'selected' : '' ?>>Actif</option>
                                <option value="0" <?= ($_GET['statut'] ?? '') == '0' ? 'selected' : '' ?>>Inactif</option>
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
            
            <!-- Table des utilisateurs -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nom</th>
                                    <th>Email</th>
                                    <th>Rôle</th>
                                    <th>Statut</th>
                                    <th>Date création</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($utilisateurs)): ?>
                                    <?php foreach ($utilisateurs as $user): ?>
                                        <tr>
                                            <td><?= $user['id_Utilisateur'] ?></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center me-2" 
                                                         style="width: 35px; height: 35px;">
                                                        <i class="bi bi-person text-white"></i>
                                                    </div>
                                                    <strong><?= htmlspecialchars($user['nom'] ?? 'N/A') ?></strong>
                                                </div>
                                            </td>
                                            <td><?= htmlspecialchars($user['email']) ?></td>
                                            <td>
                                                <?php
                                                $roleLabel = match($user['id_Role']) {
                                                    1 => '<span class="badge bg-danger">Admin</span>',
                                                    2 => '<span class="badge bg-info">Observateur</span>',
                                                    3 => '<span class="badge bg-primary">Électeur</span>',
                                                    default => '<span class="badge bg-secondary">Inconnu</span>'
                                                };
                                                echo $roleLabel;
                                                ?>
                                            </td>
                                            <td>
                                                <?php if ($user['estActif']): ?>
                                                    <span class="badge bg-success">Actif</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Inactif</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= date('d/m/Y', strtotime($user['dateCreation'] ?? 'now')) ?></td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="admin-utilisateurs.php?action=voir&id=<?= $user['id_Utilisateur'] ?>" 
                                                       class="btn btn-outline-info" title="Voir">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <?php if ($user['estActif']): ?>
                                                        <form method="POST" action="index.php?action=desactiver_utilisateur" class="d-inline">
                                                            <input type="hidden" name="id_Utilisateur" value="<?= $user['id_Utilisateur'] ?>">
                                                            <button type="submit" class="btn btn-outline-warning" title="Désactiver"
                                                                    onclick="return confirm('Êtes-vous sûr de vouloir désactiver cet utilisateur ?')">
                                                                <i class="bi bi-pause-circle"></i>
                                                            </button>
                                                        </form>
                                                    <?php else: ?>
                                                        <form method="POST" action="index.php?action=activer_utilisateur" class="d-inline">
                                                            <input type="hidden" name="id_Utilisateur" value="<?= $user['id_Utilisateur'] ?>">
                                                            <button type="submit" class="btn btn-outline-success" title="Activer">
                                                                <i class="bi bi-play-circle"></i>
                                                            </button>
                                                        </form>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">
                                            <i class="bi bi-people fs-1"></i>
                                            <p class="mt-2">Aucun utilisateur trouvé</p>
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

<!-- Modal Ajouter Utilisateur -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-person-plus"></i> Ajouter un utilisateur</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="index.php?action=ajouter_utilisateur">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nom complet</label>
                        <input type="text" class="form-control" name="nom" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mot de passe</label>
                        <input type="password" class="form-control" name="password" minlength="8" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Rôle</label>
                        <select class="form-select" name="role" required>
                            <option value="3">Électeur</option>
                            <option value="2">Observateur</option>
                            <option value="1">Administrateur</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-plus-lg"></i> Ajouter
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
