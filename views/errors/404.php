<?php
$pageTitle = 'Page introuvable - MaVoix CI';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-md-6 text-center">
            <div class="mb-4">
                <i class="bi bi-question-circle text-warning" style="font-size: 8rem;"></i>
            </div>
            <h1 class="display-4 text-warning">404</h1>
            <h2 class="mb-4">Page introuvable</h2>
            <p class="text-muted mb-4">
                La page que vous recherchez n'existe pas ou a été déplacée.
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
