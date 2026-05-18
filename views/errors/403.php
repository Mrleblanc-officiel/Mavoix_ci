<?php
$pageTitle = 'Accès refusé - MaVoix CI';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-md-6 text-center">
            <div class="mb-4">
                <i class="bi bi-shield-x text-danger" style="font-size: 8rem;"></i>
            </div>
            <h1 class="display-4 text-danger">403</h1>
            <h2 class="mb-4">Accès refusé</h2>
            <p class="text-muted mb-4">
                Vous n'avez pas les permissions nécessaires pour accéder à cette page.
            </p>
            <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                <a href="javascript:history.back()" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Retour
                </a>
                <a href="index.php" class="btn btn-primary">
                    <i class="bi bi-house"></i> Accueil
                </a>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
