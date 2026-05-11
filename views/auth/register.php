<?php
$pageTitle = 'Inscription - MaVoix CI';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow">
                <div class="card-header text-center py-4">
                    <h4 class="mb-0"><i class="bi bi-person-plus"></i> Inscription</h4>
                    <small>Créez votre compte MaVoix CI</small>
                </div>
                <div class="card-body p-4">
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle"></i> <?= htmlspecialchars($error) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="?page=register">
                        <div class="mb-3">
                            <label for="nom" class="form-label">
                                <i class="bi bi-person"></i> Nom complet
                            </label>
                            <input 
                                type="text" 
                                class="form-control" 
                                id="nom" 
                                name="nom" 
                                placeholder="Votre nom complet"
                                required 
                                autofocus
                            >
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">
                                <i class="bi bi-envelope"></i> Adresse email
                            </label>
                            <input 
                                type="email" 
                                class="form-control" 
                                id="email" 
                                name="email" 
                                placeholder="votre@email.com"
                                required
                            >
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">
                                <i class="bi bi-lock"></i> Mot de passe
                            </label>
                            <input 
                                type="password" 
                                class="form-control" 
                                id="password" 
                                name="password" 
                                placeholder="Minimum 8 caractères"
                                minlength="8"
                                required
                            >
                        </div>
                        
                        <div class="mb-4">
                            <label for="role" class="form-label">
                                <i class="bi bi-person-badge"></i> Type de compte
                            </label>
                            <select class="form-select" id="role" name="role" required>
                                <option value="">Sélectionnez un rôle</option>
                                <option value="3">Électeur</option>
                                <option value="2">Observateur</option>
                            </select>
                        </div>
                        
                        <div class="mb-4 form-check">
                            <input type="checkbox" class="form-check-input" id="termes" required>
                            <label class="form-check-label small" for="termes">
                                J'accepte les <a href="#">conditions d'utilisation</a> et la 
                                <a href="#">politique de confidentialité</a>
                            </label>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-person-plus"></i> S'inscrire
                            </button>
                        </div>
                    </form>
                    
                    <hr class="my-4">
                    
                    <div class="text-center">
                        <span class="text-muted">Déjà inscrit ?</span>
                        <a href="login.php" class="text-decoration-none">Connectez-vous</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
