<?php
$pageTitle = 'Mon Vote - MaVoix CI';
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
                <h2><i class="bi bi-receipt"></i> Mon Vote</h2>
            </div>
            
            <?php if ($a_vote && !empty($vote)): ?>
                <!-- Reçu de vote -->
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="card shadow">
                            <div class="card-header bg-success text-white text-center py-4">
                                <i class="bi bi-check-circle fs-1"></i>
                                <h4 class="mb-0 mt-2">Vote enregistré avec succès</h4>
                            </div>
                            <div class="card-body p-4">
                                <!-- Informations du vote -->
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <h6 class="text-muted mb-1">Élection</h6>
                                        <p class="fw-bold"><?= htmlspecialchars($vote['election'] ?? 'N/A') ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="text-muted mb-1">Date et heure du vote</h6>
                                        <p class="fw-bold">
                                            <?= date('d/m/Y à H:i:s', strtotime($vote['dateHeure'])) ?>
                                        </p>
                                    </div>
                                </div>
                                
                                <hr>
                                
                                <!-- Token de vérification -->
                                <div class="text-center mb-4">
                                    <h6 class="text-muted mb-2">Token de vérification</h6>
                                    <div class="bg-light rounded p-3">
                                        <code class="fs-6 text-break"><?= htmlspecialchars($vote['token_Verification']) ?></code>
                                    </div>
                                    <button class="btn btn-outline-primary btn-sm mt-2" onclick="copyToken()">
                                        <i class="bi bi-clipboard"></i> Copier le token
                                    </button>
                                </div>
                                
                                <div class="alert alert-warning">
                                    <i class="bi bi-exclamation-triangle"></i>
                                    <strong>Important :</strong> Conservez ce token en lieu sûr. Il vous permet de vérifier que votre vote a bien été comptabilisé.
                                </div>
                                
                                <hr>
                                
                                <!-- QR Code (optionnel) -->
                                <div class="text-center mb-4">
                                    <h6 class="text-muted mb-3">Code de vérification</h6>
                                    <div class="d-inline-block p-3 bg-white border rounded">
                                        <!-- Placeholder pour QR Code -->
                                        <div class="bg-light d-flex align-items-center justify-content-center" 
                                             style="width: 150px; height: 150px;">
                                            <i class="bi bi-qr-code fs-1 text-muted"></i>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Actions -->
                                <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                                    <button class="btn btn-primary" onclick="window.print()">
                                        <i class="bi bi-printer"></i> Imprimer le reçu
                                    </button>
                                    <a href="accueil.php" class="btn btn-outline-secondary">
                                        <i class="bi bi-house"></i> Retour à l'accueil
                                    </a>
                                </div>
                            </div>
                            <div class="card-footer text-center text-muted">
                                <small>
                                    <i class="bi bi-shield-check"></i>
                                    Ce reçu atteste de votre participation à l'élection.
                                    <br>Il ne révèle pas le contenu de votre vote.
                                </small>
                            </div>
                        </div>
                        
                        <!-- Informations supplémentaires -->
                        <div class="card mt-4">
                            <div class="card-header">
                                <i class="bi bi-info-circle"></i> Comment vérifier mon vote ?
                            </div>
                            <div class="card-body">
                                <ol class="mb-0">
                                    <li class="mb-2">
                                        Conservez votre token de vérification en lieu sûr
                                    </li>
                                    <li class="mb-2">
                                        Après la clôture de l'élection, rendez-vous sur la page de vérification
                                    </li>
                                    <li class="mb-2">
                                        Entrez votre token pour confirmer que votre vote a été comptabilisé
                                    </li>
                                    <li>
                                        Votre vote reste anonyme : le token ne permet pas de connaître votre choix
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
                
            <?php else: ?>
                <!-- L'électeur n'a pas encore voté -->
                <div class="card">
                    <div class="card-body text-center py-5">
                        <div class="bg-warning rounded-circle d-inline-flex align-items-center justify-content-center mb-4" 
                             style="width: 100px; height: 100px;">
                            <i class="bi bi-hourglass-split text-white fs-1"></i>
                        </div>
                        <h3>Vous n'avez pas encore voté</h3>
                        <p class="text-muted mb-4">
                            Participez à l'élection en cours pour obtenir votre reçu de vote.
                        </p>
                        <a href="voter.php" class="btn btn-primary btn-lg">
                            <i class="bi bi-check2-square"></i> Voter maintenant
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function copyToken() {
    const token = '<?= htmlspecialchars($vote['token_Verification'] ?? '') ?>';
    navigator.clipboard.writeText(token).then(function() {
        alert('Token copié dans le presse-papier !');
    }).catch(function() {
        alert('Erreur lors de la copie. Veuillez copier manuellement.');
    });
}
</script>

<!-- Style pour l'impression -->
<style>
@media print {
    .sidebar, .navbar, .btn, .card-footer small br + small {
        display: none !important;
    }
    
    .col-md-9, .col-lg-10 {
        width: 100% !important;
        max-width: 100% !important;
    }
    
    .card {
        border: 1px solid #000 !important;
        box-shadow: none !important;
    }
}
</style>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
