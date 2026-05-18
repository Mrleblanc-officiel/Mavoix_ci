<?php
$pageTitle = 'Vérification OTP - MaVoix CI';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-md-5 col-lg-4">
            <div class="card shadow">
                <div class="card-header text-center py-4">
                    <h4 class="mb-0"><i class="bi bi-shield-lock"></i> Vérification OTP</h4>
                    <small>Entrez le code reçu</small>
                </div>
                <div class="card-body p-4">
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle"></i> <?= htmlspecialchars($error) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Info message -->
                    <div class="alert alert-info mb-4">
                        <i class="bi bi-info-circle"></i>
                        Un code de vérification a été envoyé à votre adresse email.
                        <br><small>Le code expire dans 5 minutes.</small>
                    </div>
                    
                    <!-- DEV: Affichage OTP pour tests -->
                    <?php if (isset($otp_dev)): ?>
                        <div class="alert alert-warning">
                            <strong>DEV MODE</strong><br>
                            Code OTP: <code class="fs-4"><?= htmlspecialchars($otp_dev) ?></code>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="?page=verifier_otp">
                        <div class="mb-4">
                            <label for="otp" class="form-label">
                                <i class="bi bi-key"></i> Code OTP
                            </label>
                            <input 
                                type="text" 
                                class="form-control form-control-lg text-center" 
                                id="otp" 
                                name="otp" 
                                placeholder="000000"
                                maxlength="6"
                                pattern="[0-9]{6}"
                                required 
                                autofocus
                                style="letter-spacing: 0.5em; font-weight: bold;"
                            >
                            <div class="form-text text-center">
                                Entrez le code à 6 chiffres
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-check-lg"></i> Vérifier
                            </button>
                            <a href="login.php" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i> Retour à la connexion
                            </a>
                        </div>
                    </form>
                    
                    <hr class="my-4">
                    
                    <div class="text-center">
                        <form method="POST" action="index.php?action=renvoyer_otp" class="d-inline">
                            <button type="submit" class="btn btn-link text-muted small p-0">
                                <i class="bi bi-arrow-clockwise"></i> Renvoyer le code
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Auto-focus sur les champs OTP
document.getElementById('otp').addEventListener('input', function() {
    this.value = this.value.replace(/[^0-9]/g, '');
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
