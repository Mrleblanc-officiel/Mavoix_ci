<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>MaVoix CI – Voter</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', sans-serif; background: #f5f5f5; }
        nav { background: #0a5c36; color: #fff; padding: 14px 24px; display: flex; justify-content: space-between; align-items: center; }
        nav h1 { font-size: 20px; }
        nav a { color: #fff; text-decoration: none; font-size: 14px; background: rgba(255,255,255,0.15); padding: 6px 14px; border-radius: 6px; }
        .container { max-width: 800px; margin: 32px auto; padding: 0 16px; }
        h2 { font-size: 22px; color: #1a1a1a; margin-bottom: 20px; }
        .election-card { background: #fff; border-radius: 12px; padding: 24px; margin-bottom: 20px; box-shadow: 0 2px 12px rgba(0,0,0,0.08); }
        .election-card h3 { font-size: 18px; color: #0a5c36; margin-bottom: 8px; }
        .badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        .badge-encours { background: #d1fae5; color: #065f46; }
        .badge-avenir { background: #dbeafe; color: #1e40af; }
        .badge-termine { background: #f3f4f6; color: #6b7280; }
        .btn-vote { display: inline-block; margin-top: 14px; padding: 10px 20px; background: #f5a623; color: #fff; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 14px; }
        .btn-vote.disabled { background: #ccc; pointer-events: none; }
        .dates { font-size: 13px; color: #888; margin-top: 6px; }
        .empty { text-align: center; color: #999; padding: 60px 0; }
    </style>
</head>
<body>
<nav>
    <h1>🗳 MaVoix CI</h1>
    <a href="?page=logout">Déconnexion</a>
</nav>
<div class="container">
    <h2>Bienvenue, <?= htmlspecialchars($user['prenom'] ?? '') ?> 👋</h2>

    <?php if (empty($elections)): ?>
        <div class="empty">
            <p>Aucune élection disponible pour le moment.</p>
        </div>
    <?php else: ?>
        <?php foreach ($elections as $election): ?>
            <div class="election-card">
                <div>
                    <?php
                        $badgeClass = match($election['statut']) {
                            'EN_COURS' => 'badge-encours',
                            'A_VENIR'  => 'badge-avenir',
                            default    => 'badge-termine'
                        };
                    ?>
                    <span class="badge <?= $badgeClass ?>">
                        <?= htmlspecialchars($election['statut']) ?>
                    </span>
                </div>
                <h3><?= htmlspecialchars($election['titre']) ?></h3>
                <p><?= htmlspecialchars($election['description'] ?? '') ?></p>
                <p class="dates">
                    Du <?= date('d/m/Y', strtotime($election['date_Debut'])) ?>
                    au <?= date('d/m/Y', strtotime($election['date_Fin'])) ?>
                </p>

                <?php if ($election['statut'] === 'EN_COURS' && !$election['aVote']): ?>
                    <a href="?page=vote&id=<?= $election['id_Election'] ?>" class="btn-vote">Voter</a>
                <?php elseif ($election['aVote']): ?>
                    <span class="btn-vote disabled">✅ Déjà voté</span>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
</body>
</html>
