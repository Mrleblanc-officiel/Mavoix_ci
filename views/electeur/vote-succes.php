<?php
$pageTitle = 'Vote enregistré - MaVoix CI';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow text-center">
                <div class="card-body py-5">
                    <!-- Animation de succès -->
                    <div class="mb-4">
                        <div class="bg-success rounded-circle d-inline-flex align-items-center justify-content-center animate-success" 
                             style="width: 120px; height: 120px;">
                            <i class="bi bi-check-lg text-white" style="font-size: 4rem;"></i>
                        </div>
                    </div>
                    
                    <h2 class="text-success mb-3">Vote enregistré avec succès !</h2>
                    
                    <p class="text-muted mb-4">
                        Merci pour votre participation. Votre vote a été enregistré de manière sécurisée et anonyme.
                    </p>
                    
                    <!-- Token de vérification -->
                    <div class="card bg-light mb-4">
                        <div class="card-body">
                            <h6 class="text-muted mb-2">Votre token de vérification</h6>
                            <div class="bg-white rounded p-3 border">
                                <code class="fs-6 text-break"><?= htmlspecialchars($tokenVote ?? 'TOKEN-XXXX-XXXX-XXXX') ?></code>
                            </div>
                            <button class="btn btn-outline-primary btn-sm mt-2" onclick="copyToken()">
                                <i class="bi bi-clipboard"></i> Copier
                            </button>
                        </div>
                    </div>
                    
                    <div class="alert alert-warning text-start">
                        <i class="bi bi-exclamation-triangle"></i>
                        <strong>Important :</strong> Conservez ce token ! Il vous permettra de vérifier que votre vote a bien été comptabilisé après la clôture de l'élection.
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-center mt-4">
                        <a href="mon-vote.php" class="btn btn-success btn-lg">
                            <i class="bi bi-receipt"></i> Voir mon reçu
                        </a>
                        <a href="accueil.php" class="btn btn-outline-primary btn-lg">
                            <i class="bi bi-house"></i> Accueil
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes scaleIn {
    0% {
        transform: scale(0);
        opacity: 0;
    }
    50% {
        transform: scale(1.1);
    }
    100% {
        transform: scale(1);
        opacity: 1;
    }
}

.animate-success {
    animation: scaleIn 0.5s ease-out;
}
</style>

<script>
function copyToken() {
    const token = '<?= htmlspecialchars($tokenVote ?? '') ?>';
    navigator.clipboard.writeText(token).then(function() {
        alert('Token copié dans le presse-papier !');
    }).catch(function() {
        alert('Erreur lors de la copie. Veuillez copier manuellement.');
    });
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
