<?php
$currentPage = basename($_SERVER['PHP_SELF']);
$userRole = $_SESSION['user']['id_Role'] ?? 0;
?>

<div class="sidebar p-3">
    <div class="text-center mb-4">
        <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
            <i class="bi bi-person-fill text-white fs-3"></i>
        </div>
        <p class="text-white mt-2 mb-0">
            <?php
            switch ($userRole) {
                case 1:
                    echo 'Administrateur';
                    break;
                case 2:
                    echo 'Observateur';
                    break;
                case 3:
                    echo 'Électeur';
                    break;
                default:
                    echo 'Invité';
            }
            ?>
        </p>
    </div>
    
    <nav class="nav flex-column">
        <?php if ($userRole == 1): // Admin ?>
            <a class="nav-link <?= $currentPage == 'admin-dashboard.php' ? 'active' : '' ?>" href="admin-dashboard.php">
                <i class="bi bi-speedometer2"></i> Tableau de bord
            </a>
            <a class="nav-link <?= $currentPage == 'admin-utilisateurs.php' ? 'active' : '' ?>" href="admin-utilisateurs.php">
                <i class="bi bi-people"></i> Utilisateurs
            </a>
            <a class="nav-link <?= $currentPage == 'admin-electeurs.php' ? 'active' : '' ?>" href="admin-electeurs.php">
                <i class="bi bi-person-badge"></i> Électeurs
            </a>
            <a class="nav-link <?= $currentPage == 'admin-elections.php' ? 'active' : '' ?>" href="admin-elections.php">
                <i class="bi bi-calendar-event"></i> Élections
            </a>
            <a class="nav-link <?= $currentPage == 'admin-candidats.php' ? 'active' : '' ?>" href="admin-candidats.php">
                <i class="bi bi-person-lines-fill"></i> Candidats
            </a>
            <a class="nav-link <?= $currentPage == 'admin-audit.php' ? 'active' : '' ?>" href="admin-audit.php">
                <i class="bi bi-journal-text"></i> Logs d'audit
            </a>
            
        <?php elseif ($userRole == 2): // Observateur ?>
            <a class="nav-link <?= $currentPage == 'observateur-dashboard.php' ? 'active' : '' ?>" href="observateur-dashboard.php">
                <i class="bi bi-speedometer2"></i> Tableau de bord
            </a>
            <a class="nav-link <?= $currentPage == 'observateur-elections.php' ? 'active' : '' ?>" href="observateur-elections.php">
                <i class="bi bi-calendar-event"></i> Élections
            </a>
            <a class="nav-link <?= $currentPage == 'observateur-resultats.php' ? 'active' : '' ?>" href="observateur-resultats.php">
                <i class="bi bi-bar-chart"></i> Résultats
            </a>
            
        <?php elseif ($userRole == 3): // Électeur ?>
            <a class="nav-link <?= $currentPage == 'accueil.php' ? 'active' : '' ?>" href="accueil.php">
                <i class="bi bi-house"></i> Accueil
            </a>
            <a class="nav-link <?= $currentPage == 'voter.php' ? 'active' : '' ?>" href="voter.php">
                <i class="bi bi-check2-square"></i> Voter
            </a>
            <a class="nav-link <?= $currentPage == 'mon-vote.php' ? 'active' : '' ?>" href="mon-vote.php">
                <i class="bi bi-receipt"></i> Mon vote
            </a>
        <?php endif; ?>
        
        <hr class="text-secondary my-3">
        
        <a class="nav-link text-danger" href="deconnexion.php">
            <i class="bi bi-box-arrow-right"></i> Déconnexion
        </a>
    </nav>
</div>
