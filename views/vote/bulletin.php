<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>MaVoix CI – Bulletin de vote</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', sans-serif; background: #f5f5f5; }
        nav { background: #0a5c36; color: #fff; padding: 14px 24px; display: flex; justify-content: space-between; }
        nav h1 { font-size: 20px; }
        .container { max-width: 900px; margin: 32px auto; padding: 0 16px; }
        h2 { font-size: 22px; color: #1a1a1a; margin-bottom: 6px; }
        .sub { color: #666; margin-bottom: 28px; }
        .candidats { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 16px; }
        .candidat-card { background: #fff; border: 2px solid #e5e7eb; border-radius: 12px; padding: 20px; cursor: pointer; transition: border-color .2s, box-shadow .2s; }
        .candidat-card:hover { border-color: #0a5c36; box-shadow: 0 4px 16px rgba(10,92,54,0.12); }
        .candidat-card input[type=radio] { display: none; }
        .candidat-card.selected { border-color: #0a5c36; background: #f0fdf4; }
        .candidat-card img { width: 80px; height: 80px; border-radius: 50%; object-fit: cover; display: block; margin: 0 auto 12px; background: #e5e7eb; }
        .candidat-card .nom { font-weight: 700; font-size: 16px; text-align: center; color: #1a1a1a; }
        .candidat-card .parti { font-size: 13px; color: #666; text-align: center; margin-top: 4px; }
        .btn-submit { display: block; width: 100%; padding: 16px; background: #f5a623; color: #fff; font-size: 17px; font-weight: 700; border: none; border-radius: 10px; cursor: pointer; margin-top: 28px; }
        .btn-submit:disabled { background: #ccc; cursor: not-allowed; }
        .alert { padding: 12px 16px; border-radius: 8px; margin-bottom: 16px; font-size: 14px; }
        .alert-error { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
    </style>
</head>
<body>
<nav><h1>🗳 MaVoix CI – Bulletin de vote</h1></nav>
<div class="container">
    <h2><?= htmlspecialchars($election['titre'] ?? '') ?></h2>
    <p class="sub">Sélectionnez un candidat et confirmez votre vote. Ce vote est définitif.</p>

    <?php if (!empty($error)): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="?page=vote-submit" id="formVote">
        <input type="hidden" name="id_Election" value="<?= $election['id_Election'] ?? '' ?>">
        <div class="candidats">
            <?php foreach ($candidats as $candidat): ?>
                <label class="candidat-card" id="card-<?= $candidat['id_Candidat'] ?>">
                    <input type="radio" name="id_Candidat" value="<?= $candidat['id_Candidat'] ?>" onchange="selectCard(this)">
                    <img src="<?= htmlspecialchars($candidat['photo'] ?? '') ?>" onerror="this.src=''" alt="">
                    <div class="nom"><?= htmlspecialchars($candidat['prenom'] . ' ' . $candidat['nom']) ?></div>
                    <div class="parti"><?= htmlspecialchars($candidat['nomParti'] ?? 'Indépendant') ?></div>
                </label>
            <?php endforeach; ?>
        </div>
        <button type="submit" class="btn-submit" id="btnVote" disabled>Confirmer mon vote</button>
    </form>
</div>
<script>
function selectCard(radio) {
    document.querySelectorAll('.candidat-card').forEach(c => c.classList.remove('selected'));
    radio.closest('.candidat-card').classList.add('selected');
    document.getElementById('btnVote').disabled = false;
}
</script>
</body>
</html>
