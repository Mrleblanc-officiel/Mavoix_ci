<?php
$pageTitle = 'Accueil - MaVoix CI';
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
            <!-- Message de bienvenue -->
            <div class="card bg-primary text-white mb-4">
                <div class="card-body py-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h3><i class="bi bi-person-circle"></i> Bienvenue, <?= htmlspecialchars($_SESSION['user']['nom'] ?? 'Électeur') ?></h3>
                            <p class="mb-0 opacity-75">
                                Participez aux élections en cours et faites entendre votre voix de manière sécurisée.
                            </p>
                        </div>
                        <div class="col-md-4 text-md-end mt-3 mt-md-0">
                            <?php if (!empty($election_en_cours) && !$a_vote): ?>
                                <a href="voter.php" class="btn btn-light btn-lg">
                                    <i class="bi bi-check2-square"></i> Voter maintenant
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <?php if (isset($success)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle"></i> <?= htmlspecialchars($success) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <!-- Statut de vote -->
            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <div class="card h-100 <?= $a_vote ? 'border-success' : 'border-warning' ?>">
                        <div class="card-body text-center py-4">
                            <?php if ($a_vote): ?>
                                <div class="bg-success rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                                     style="width: 80px; height: 80px;">
                                    <i class="bi bi-check-lg text-white fs-1"></i>
                                </div>
                                <h4 class="text-success">Vous avez voté !</h4>
                                <p class="text-muted mb-0">
                                    Votre vote a été enregistré le <?= date('d/m/Y à H:i', strtotime($date_vote)) ?>
                                </p>
                                <a href="mon-vote.php" class="btn btn-outline-success mt-3">
                                    <i class="bi bi-receipt"></i> Voir mon reçu
                                </a>
                            <?php else: ?>
                                <div class="bg-warning rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                                     style="width: 80px; height: 80px;">
                                    <i class="bi bi-hourglass-split text-white fs-1"></i>
                                </div>
                                <h4 class="text-warning">En attente de vote</h4>
                                <p class="text-muted mb-0">
                                    Vous n'avez pas encore participé à l'élection en cours.
                                </p>
                                <?php if (!empty($election_en_cours)): ?>
                                    <a href="voter.php" class="btn btn-warning mt-3">
                                        <i class="bi bi-check2-square"></i> Voter maintenant
                                    </a>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header">
                            <i class="bi bi-calendar-event"></i> Élection en cours
                        </div>
                        <div class="card-body">
                            <?php if (!empty($election_en_cours)): ?>
                                <h5><?= htmlspecialchars($election_en_cours['titre']) ?></h5>
                                <p class="text-muted small">
                                    <?= htmlspecialchars($election_en_cours['description'] ?? 'Aucune description') ?>
                                </p>
                                
                                <div class="bg-light rounded p-3 mb-3">
                                    <div class="row text-center">
                                        <div class="col-6 border-end">
                                            <small class="text-muted">Début</small>
                                            <br><strong><?= date('d/m/Y H:i', strtotime($election_en_cours['date_Debut'])) ?></strong>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted">Fin</small>
                                            <br><strong><?= date('d/m/Y H:i', strtotime($election_en_cours['date_Fin'])) ?></strong>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Compte à rebours -->
                                <div class="text-center">
                                    <small class="text-muted">Temps restant</small>
                                    <h4 id="countdown" class="text-primary mb-0">--:--:--</h4>
                                </div>
                                
                                <script>
                                    const endDate = new Date('<?= $election_en_cours['date_Fin'] ?>').getTime();
                                    
                                    function updateCountdown() {
                                        const now = new Date().getTime();
                                        const distance = endDate - now;
                                        
                                        if (distance < 0) {
                                            document.getElementById('countdown').innerHTML = 'Terminée';
                                            return;
                                        }
                                        
                                        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                                        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                                        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                                        const seconds = Math.floor((distance % (1000 * 60)) / 1000);
                                        
                                        let display = '';
                                        if (days > 0) display += days + 'j ';
                                        display += hours.toString().padStart(2, '0') + ':';
                                        display += minutes.toString().padStart(2, '0') + ':';
                                        display += seconds.toString().padStart(2, '0');
                                        
                                        document.getElementById('countdown').innerHTML = display;
                                    }
                                    
                                    updateCountdown();
                                    setInterval(updateCountdown, 1000);
                                </script>
                            <?php else: ?>
                                <div class="text-center py-4 text-muted">
                                    <i class="bi bi-calendar-x fs-1"></i>
                                    <p class="mt-2 mb-0">Aucune élection en cours actuellement</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Informations importantes -->
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-info-circle"></i> Informations importantes
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3 mb-md-0">
                            <div class="d-flex">
                                <div class="me-3">
                                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" 
                                         style="width: 40px; height: 40px;">
                                        <i class="bi bi-shield-check text-white"></i>
                                    </div>
                                </div>
                                <div>
                                    <h6>Vote sécurisé</h6>
                                    <small class="text-muted">Votre vote est crypté et anonyme</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3 mb-md-0">
                            <div class="d-flex">
                                <div class="me-3">
                                    <div class="bg-success rounded-circle d-flex align-items-center justify-content-center" 
                                         style="width: 40px; height: 40px;">
                                        <i class="bi bi-1-circle text-white"></i>
                                    </div>
                                </div>
                                <div>
                                    <h6>Un seul vote</h6>
                                    <small class="text-muted">Vous ne pouvez voter qu'une seule fois</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex">
                                <div class="me-3">
                                    <div class="bg-warning rounded-circle d-flex align-items-center justify-content-center" 
                                         style="width: 40px; height: 40px;">
                                        <i class="bi bi-receipt text-white"></i>
                                    </div>
                                </div>
                                <div>
                                    <h6>Reçu de vote</h6>
                                    <small class="text-muted">Conservez votre token de vérification</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
