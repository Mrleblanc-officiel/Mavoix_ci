<?php
$pageTitle = 'Voter - MaVoix CI';
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
                <h2><i class="bi bi-check2-square"></i> Voter</h2>
            </div>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle"></i> <?= htmlspecialchars($error) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <?php if ($a_deja_vote ?? false): ?>
                <!-- L'électeur a déjà voté -->
                <div class="card">
                    <div class="card-body text-center py-5">
                        <div class="bg-success rounded-circle d-inline-flex align-items-center justify-content-center mb-4" 
                             style="width: 100px; height: 100px;">
                            <i class="bi bi-check-lg text-white fs-1"></i>
                        </div>
                        <h3 class="text-success">Vous avez déjà voté</h3>
                        <p class="text-muted mb-4">
                            Votre vote a été enregistré avec succès. Vous ne pouvez voter qu'une seule fois.
                        </p>
                        <a href="mon-vote.php" class="btn btn-success">
                            <i class="bi bi-receipt"></i> Voir mon reçu de vote
                        </a>
                    </div>
                </div>
                
            <?php elseif (empty($election)): ?>
                <!-- Aucune élection en cours -->
                <div class="card">
                    <div class="card-body text-center py-5">
                        <div class="bg-warning rounded-circle d-inline-flex align-items-center justify-content-center mb-4" 
                             style="width: 100px; height: 100px;">
                            <i class="bi bi-calendar-x text-white fs-1"></i>
                        </div>
                        <h3>Aucune élection en cours</h3>
                        <p class="text-muted mb-4">
                            Il n'y a pas d'élection active pour le moment. Revenez plus tard.
                        </p>
                        <a href="accueil.php" class="btn btn-primary">
                            <i class="bi bi-house"></i> Retour à l'accueil
                        </a>
                    </div>
                </div>
                
            <?php else: ?>
                <!-- Formulaire de vote -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><?= htmlspecialchars($election['titre']) ?></h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted"><?= htmlspecialchars($election['description'] ?? '') ?></p>
                        
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i>
                            <strong>Instructions :</strong>
                            <ul class="mb-0 mt-2">
                                <li>Sélectionnez le candidat de votre choix</li>
                                <li>Vérifiez votre choix avant de confirmer</li>
                                <li>Votre vote est définitif et ne peut pas être modifié</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <form method="POST" action="index.php?action=voter" id="voteForm">
                    <input type="hidden" name="id_Election" value="<?= $election['id_Election'] ?>">
                    
                    <div class="row g-4 mb-4">
                        <?php if (!empty($candidats)): ?>
                            <?php foreach ($candidats as $candidat): ?>
                                <div class="col-md-6 col-lg-4">
                                    <div class="card h-100 candidat-card position-relative" data-id="<?= $candidat['id_Candidat'] ?>">
                                        <div class="card-body text-center">
                                            <?php if (!empty($candidat['photo'])): ?>
                                                <img src="<?= htmlspecialchars($candidat['photo']) ?>" 
                                                     alt="<?= htmlspecialchars($candidat['nom']) ?>"
                                                     class="rounded-circle mb-3"
                                                     style="width: 120px; height: 120px; object-fit: cover;">
                                            <?php else: ?>
                                                <div class="bg-secondary rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                                     style="width: 120px; height: 120px;">
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
                                            <?php else: ?>
                                                <p class="text-muted small mb-2">
                                                    <i class="bi bi-person"></i> Indépendant
                                                </p>
                                            <?php endif; ?>
                                            
                                            <p class="small text-muted">
                                                <?= htmlspecialchars(substr($candidat['description'] ?? '', 0, 100)) ?>
                                                <?php if (strlen($candidat['description'] ?? '') > 100): ?>...<?php endif; ?>
                                            </p>
                                            
                                            <div class="form-check d-flex justify-content-center mt-3">
                                                <input class="form-check-input candidat-radio" 
                                                       type="radio" 
                                                       name="id_Candidat" 
                                                       id="candidat_<?= $candidat['id_Candidat'] ?>"
                                                       value="<?= $candidat['id_Candidat'] ?>"
                                                       required>
                                                <label class="form-check-label ms-2 fw-bold" 
                                                       for="candidat_<?= $candidat['id_Candidat'] ?>">
                                                    Sélectionner
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="col-12">
                                <div class="alert alert-warning text-center">
                                    <i class="bi bi-exclamation-triangle"></i>
                                    Aucun candidat disponible pour cette élection.
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <?php if (!empty($candidats)): ?>
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>Votre choix :</strong>
                                        <span id="selectedCandidat" class="text-muted">Aucun candidat sélectionné</span>
                                    </div>
                                    <button type="button" class="btn btn-success btn-lg" 
                                            data-bs-toggle="modal" data-bs-target="#confirmModal"
                                            id="btnConfirmer" disabled>
                                        <i class="bi bi-check-lg"></i> Confirmer mon vote
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </form>
                
                <!-- Modal de confirmation -->
                <div class="modal fade" id="confirmModal" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header bg-warning">
                                <h5 class="modal-title"><i class="bi bi-exclamation-triangle"></i> Confirmation</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body text-center py-4">
                                <h5>Êtes-vous sûr de votre choix ?</h5>
                                <p class="text-muted mb-3">
                                    Vous allez voter pour :
                                </p>
                                <div class="alert alert-primary">
                                    <strong id="confirmCandidatName">-</strong>
                                </div>
                                <p class="text-danger small">
                                    <i class="bi bi-exclamation-circle"></i>
                                    Cette action est définitive et ne peut pas être annulée.
                                </p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                    <i class="bi bi-x-lg"></i> Annuler
                                </button>
                                <button type="submit" form="voteForm" class="btn btn-success">
                                    <i class="bi bi-check-lg"></i> Confirmer définitivement
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
    .candidat-card {
        cursor: pointer;
        transition: all 0.3s ease;
        border: 2px solid transparent;
    }
    
    .candidat-card:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        transform: translateY(-5px);
    }
    
    .candidat-card.selected {
        border-color: #198754;
        background-color: rgba(25, 135, 84, 0.05);
    }
    
    .candidat-card.selected::after {
        content: '\2713';
        position: absolute;
        top: 10px;
        right: 10px;
        background: #198754;
        color: white;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.candidat-card');
    const btnConfirmer = document.getElementById('btnConfirmer');
    const selectedCandidatSpan = document.getElementById('selectedCandidat');
    const confirmCandidatName = document.getElementById('confirmCandidatName');
    
    if (cards.length > 0) {
        cards.forEach(card => {
            card.addEventListener('click', function() {
                const radio = this.querySelector('.candidat-radio');
                radio.checked = true;
                
                cards.forEach(c => c.classList.remove('selected'));
                this.classList.add('selected');
                
                const candidatName = this.querySelector('.card-title').textContent.trim();
                selectedCandidatSpan.textContent = candidatName;
                selectedCandidatSpan.classList.remove('text-muted');
                selectedCandidatSpan.classList.add('text-success', 'fw-bold');
                
                confirmCandidatName.textContent = candidatName;
                
                btnConfirmer.disabled = false;
            });
        });
    }
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
