<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>MaVoix CI – Administration</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', sans-serif; background: #f5f5f5; }
        nav { background: #1a1a2e; color: #fff; padding: 14px 24px; display: flex; justify-content: space-between; align-items: center; }
        nav h1 { font-size: 20px; }
        nav a { color: #fff; text-decoration: none; font-size: 13px; background: rgba(255,255,255,0.1); padding: 6px 14px; border-radius: 6px; }
        .container { max-width: 1100px; margin: 32px auto; padding: 0 16px; }
        .stats { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 16px; margin-bottom: 32px; }
        .stat-card { background: #fff; border-radius: 12px; padding: 24px; text-align: center; box-shadow: 0 2px 12px rgba(0,0,0,0.08); }
        .stat-card .value { font-size: 40px; font-weight: 800; color: #0a5c36; }
        .stat-card .label { font-size: 14px; color: #666; margin-top: 4px; }
        h2 { font-size: 20px; color: #1a1a1a; margin-bottom: 16px; margin-top: 8px; }
        table { width: 100%; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,0.08); border-collapse: collapse; margin-bottom: 32px; }
        th { background: #f9fafb; padding: 12px 16px; text-align: left; font-size: 13px; color: #6b7280; font-weight: 600; }
        td { padding: 12px 16px; font-size: 14px; border-top: 1px solid #f3f4f6; }
        .badge { display: inline-block; padding: 2px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        .badge-encours { background: #d1fae5; color: #065f46; }
        .badge-avenir { background: #dbeafe; color: #1e40af; }
        .badge-termine { background: #f3f4f6; color: #6b7280; }
        .nav-links { display: flex; gap: 12px; margin-bottom: 24px; flex-wrap: wrap; }
        .nav-links a { padding: 8px 18px; background: #0a5c36; color: #fff; border-radius: 8px; text-decoration: none; font-size: 14px; font-weight: 600; }
        .nav-links a:hover { background: #084a2c; }
    </style>
</head>
<body>
<nav>
    <h1>🗳 MaVoix CI – Administration</h1>
    <a href="?page=logout">Déconnexion</a>
</nav>
<div class="container">
    <div class="stats">
        <div class="stat-card">
            <div class="value"><?= $stats['utilisateurs'] ?? 0 ?></div>
            <div class="label">Utilisateurs</div>
        </div>
        <div class="stat-card">
            <div class="value"><?= $stats['elections'] ?? 0 ?></div>
            <div class="label">Élections</div>
        </div>
        <div class="stat-card">
            <div class="value"><?= $stats['candidats'] ?? 0 ?></div>
            <div class="label">Candidats</div>
        </div>
        <div class="stat-card">
            <div class="value"><?= array_sum(array_column($stats['elections_detail'] ?? [], 'total_votes')) ?></div>
            <div class="label">Votes enregistrés</div>
        </div>
    </div>

    <div class="nav-links">
        <a href="?page=admin-elections">Gérer les Élections</a>
        <a href="?page=admin-candidats">Gérer les Candidats</a>
        <a href="?page=admin-utilisateurs">Gérer les Utilisateurs</a>
        <a href="?page=admin-logs">Logs d'audit</a>
    </div>

    <h2>📊 Aperçu des Élections</h2>
    <table>
        <thead>
            <tr>
                <th>Titre</th><th>Statut</th><th>Votes</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($stats['elections_detail'] ?? [] as $e): ?>
                <tr>
                    <td><?= htmlspecialchars($e['titre']) ?></td>
                    <td>
                        <?php $bc = match($e['statut']) { 'EN_COURS' => 'badge-encours', 'A_VENIR' => 'badge-avenir', default => 'badge-termine' }; ?>
                        <span class="badge <?= $bc ?>"><?= htmlspecialchars($e['statut']) ?></span>
                    </td>
                    <td><?= $e['total_votes'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
