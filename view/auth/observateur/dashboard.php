<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>MaVoix CI – Observateur</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', sans-serif; background: #f5f5f5; }
        nav { background: #1e3a5f; color: #fff; padding: 14px 24px; display: flex; justify-content: space-between; }
        nav h1 { font-size: 20px; }
        nav a { color: #fff; text-decoration: none; background: rgba(255,255,255,0.15); padding: 6px 14px; border-radius: 6px; font-size: 13px; }
        .container { max-width: 900px; margin: 32px auto; padding: 0 16px; }
        h2 { font-size: 20px; margin-bottom: 16px; color: #1a1a1a; }
        .election-card { background: #fff; border-radius: 12px; padding: 24px; margin-bottom: 20px; box-shadow: 0 2px 12px rgba(0,0,0,0.08); }
        .election-card h3 { font-size: 17px; color: #1e3a5f; margin-bottom: 8px; }
        .badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; margin-bottom: 10px; }
        .badge-encours { background: #d1fae5; color: #065f46; }
        .badge-avenir { background: #dbeafe; color: #1e40af; }
        .badge-termine { background: #f3f4f6; color: #6b7280; }
        .bar-wrap { display: flex; align-items: center; gap: 10px; margin-bottom: 8px; }
        .bar-label { width: 160px; font-size: 13px; color: #333; flex-shrink: 0; }
        .bar { flex: 1; background: #e5e7eb; border-radius: 6px; height: 22px; overflow: hidden; }
        .bar-fill { height: 100%; background: #0a5c36; border-radius: 6px; transition: width 1s; display: flex; align-items: center; padding-left: 8px; color: #fff; font-size: 12px; font-weight: 600; }
        .total { font-size: 13px; color: #888; margin-top: 10px; }
        .btn-refresh { display: inline-block; margin-top: 10px; padding: 8px 18px; background: #1e3a5f; color: #fff; border-radius: 8px; text-decoration: none; font-size: 13px; }
    </style>
</head>
<body>
<nav>
    <h1>🗳 MaVoix CI – Observateur</h1>
    <a href="?page=logout">Déconnexion</a>
</nav>
<div class="container">
    <h2>Résultats en temps réel</h2>

    <?php foreach ($elections as $election): ?>
        <?php
            $bc = match($election['election']['statut']) { 'EN_COURS' => 'badge-encours', 'A_VENIR' => 'badge-avenir', default => 'badge-termine' };
            $total = $election['total_votes'];
        ?>
        <div class="election-card">
            <span class="badge <?= $bc ?>"><?= $election['election']['statut'] ?></span>
            <h3><?= htmlspecialchars($election['election']['titre']) ?></h3>
            <p class="total">Total votes : <strong><?= $total ?></strong></p>
            <br>
            <?php foreach ($election['resultats'] as $res): ?>
                <div class="bar-wrap">
                    <div class="bar-label"><?= htmlspecialchars($res['prenom'] . ' ' . $res['nom']) ?></div>
                    <div class="bar">
                        <div class="bar-fill" style="width: <?= min($res['pourcentage'] ?? 0, 100) ?>%">
                            <?= $res['pourcentage'] ?? 0 ?>%
                        </div>
                    </div>
                    <span style="font-size:13px;color:#555"><?= $res['nombre_Voix'] ?> voix</span>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>
    <a href="?page=observateur-dashboard" class="btn-refresh">🔄 Actualiser</a>
</div>
</body>
</html>
