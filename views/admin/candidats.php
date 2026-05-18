<?php
$pageTitle = 'Gestion des Candidats - MaVoix CI';
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
                <h2><i class="bi bi-person-lines-fill"></i> Gestion des Candidats</h2>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCandidatModal">
                    <i class="bi bi-plus-lg"></i> Ajouter un candidat
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
                                   placeholder="Rechercher par nom..."
                                   value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" name="election">
                                <option value="">Toutes les élections</option>
                                <?php if (!empty($elections)): ?>
                                    <?php foreach ($elections as $election): ?>
                                        <option value="<?= $election['id_Election'] ?>" 
                                                <?= ($_GET['election'] ?? '') == $election['id_Election'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($election['titre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
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
            
            <!-- Liste des candidats -->
            <div class="row g-4">
                <?php if (!empty($candidats)): ?>
                    <?php foreach ($candidats as $candidat): ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="card h-100 <?= !$candidat['id_Actif'] ? 'opacity-50' : '' ?>">
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
                                        <p class="text-muted small mb-2">
                                            <i class="bi bi-flag"></i> 
                                            <?= htmlspecialchars($candidat['nomParti']) ?>
                                            <?php if (!empty($candidat['sigle'])): ?>
                                                (<?= htmlspecialchars($candidat['sigle']) ?>)
                                            <?php endif; ?>
                                        </p>
                                    <?php endif; ?>
                                    
                                    <span class="badge bg-info mb-2">
                                        <?= htmlspecialchars($candidat['election']) ?>
                                    </span>
                                    
                                    <p class="small text-muted">
                                        <?= htmlspecialchars(substr($candidat['description'] ?? '', 0, 80)) ?>...
                                    </p>
                                    
                                    <?php if ($candidat['id_Actif']): ?>
                                        <span class="badge bg-success"><i class="bi bi-check-circle"></i> Actif</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger"><i class="bi bi-x-circle"></i> Inactif</span>
                                    <?php endif; ?>
                                </div>
                                <div class="card-footer bg-transparent">
                                    <div class="btn-group w-100">
                                        <button class="btn btn-outline-primary btn-sm"
                                                onclick="editCandidat(<?= htmlspecialchars(json_encode($candidat)) ?>)">
                                            <i class="bi bi-pencil"></i> Modifier
                                        </button>
                                        <?php if ($candidat['id_Actif']): ?>
                                            <form method="POST" action="index.php?action=desactiver_candidat" class="d-inline">
                                                <input type="hidden" name="id_Candidat" value="<?= $candidat['id_Candidat'] ?>">
                                                <button type="submit" class="btn btn-outline-danger btn-sm"
                                                        onclick="return confirm('Désactiver ce candidat ?')">
                                                    <i class="bi bi-pause-circle"></i> Désactiver
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <form method="POST" action="index.php?action=activer_candidat" class="d-inline">
                                                <input type="hidden" name="id_Candidat" value="<?= $candidat['id_Candidat'] ?>">
                                                <button type="submit" class="btn btn-outline-success btn-sm">
                                                    <i class="bi bi-play-circle"></i> Activer
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
                                <i class="bi bi-person-x fs-1 text-muted"></i>
                                <h5 class="mt-3">Aucun candidat</h5>
                                <p class="text-muted">Ajoutez des candidats à vos élections</p>
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCandidatModal">
                                    <i class="bi bi-plus-lg"></i> Ajouter un candidat
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal Ajouter Candidat -->
<div class="modal fade" id="addCandidatModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-person-plus"></i> Ajouter un candidat</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="index.php?action=ajouter_candidat" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nom *</label>
                            <input type="text" class="form-control" name="nom" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Prénom *</label>
                            <input type="text" class="form-control" name="prenom" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Photo</label>
                        <input type="file" class="form-control" name="photo" accept="image/*">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3" 
                                  placeholder="Biographie, programme..."></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Élection *</label>
                            <select class="form-select" name="id_Election" required>
                                <option value="">Sélectionner une élection</option>
                                <?php if (!empty($elections)): ?>
                                    <?php foreach ($elections as $election): ?>
                                        <option value="<?= $election['id_Election'] ?>">
                                            <?= htmlspecialchars($election['titre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Parti politique</label>
                            <select class="form-select" name="id_Parti">
                                <option value="">Indépendant</option>
                                <?php if (!empty($partis)): ?>
                                    <?php foreach ($partis as $parti): ?>
                                        <option value="<?= $parti['id_Parti'] ?>">
                                            <?= htmlspecialchars($parti['nom']) ?> 
                                            (<?= htmlspecialchars($parti['sigle']) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
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

<!-- Modal Modifier Candidat -->
<div class="modal fade" id="editCandidatModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-pencil"></i> Modifier le candidat</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="index.php?action=modifier_candidat" enctype="multipart/form-data">
                <input type="hidden" name="id_Candidat" id="edit_id_Candidat">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nom *</label>
                            <input type="text" class="form-control" name="nom" id="edit_nom" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Prénom *</label>
                            <input type="text" class="form-control" name="prenom" id="edit_prenom" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Photo (laisser vide pour conserver)</label>
                        <input type="file" class="form-control" name="photo" accept="image/*">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" id="edit_description" rows="3"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Parti politique</label>
                        <select class="form-select" name="id_Parti" id="edit_id_Parti">
                            <option value="">Indépendant</option>
                            <?php if (!empty($partis)): ?>
                                <?php foreach ($partis as $parti): ?>
                                    <option value="<?= $parti['id_Parti'] ?>">
                                        <?= htmlspecialchars($parti['nom']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
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
function editCandidat(candidat) {
    document.getElementById('edit_id_Candidat').value = candidat.id_Candidat;
    document.getElementById('edit_nom').value = candidat.nom;
    document.getElementById('edit_prenom').value = candidat.prenom;
    document.getElementById('edit_description').value = candidat.description || '';
    document.getElementById('edit_id_Parti').value = candidat.id_Parti || '';
    
    new bootstrap.Modal(document.getElementById('editCandidatModal')).show();
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
