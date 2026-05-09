<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>MaVoix CI – Vérification OTP</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', sans-serif; background: #0a5c36; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .card { background: #fff; border-radius: 12px; padding: 40px; width: 100%; max-width: 400px; box-shadow: 0 8px 32px rgba(0,0,0,0.2); text-align: center; }
        h2 { color: #0a5c36; font-size: 22px; margin-bottom: 10px; }
        p.sub { color: #666; font-size: 14px; margin-bottom: 24px; }
        .otp-input { display: flex; justify-content: center; gap: 10px; margin-bottom: 24px; }
        .otp-input input { width: 48px; height: 56px; text-align: center; font-size: 24px; font-weight: 700; border: 2px solid #ddd; border-radius: 8px; }
        .otp-input input:focus { outline: none; border-color: #0a5c36; }
        .btn { width: 100%; padding: 14px; background: #0a5c36; color: #fff; font-size: 16px; font-weight: 700; border: none; border-radius: 8px; cursor: pointer; }
        .btn:hover { background: #084a2c; }
        .alert { padding: 12px 16px; border-radius: 8px; margin-bottom: 16px; font-size: 14px; }
        .alert-error { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
        /* DEV ONLY */
        .dev-otp { background: #fffbeb; border: 1px dashed #f59e0b; border-radius: 8px; padding: 12px; margin-bottom: 20px; font-size: 13px; color: #92400e; }
        .dev-otp strong { font-size: 20px; letter-spacing: 4px; }
    </style>
</head>
<body>
<div class="card">
    <h2>🔐 Vérification OTP</h2>
    <p class="sub">Un code à 6 chiffres vous a été envoyé.</p>

    <?php if (!empty($otp_dev)): ?>
        <div class="dev-otp">
            <em>[DEV]</em> Code OTP : <strong><?= $otp_dev ?></strong>
        </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="?page=otp-verify">
        <div class="otp-input">
            <?php for ($i = 1; $i <= 6; $i++): ?>
                <input type="text" name="digit[]" maxlength="1" pattern="[0-9]" inputmode="numeric" required>
            <?php endfor; ?>
        </div>
        <button type="submit" class="btn">Valider le code</button>
    </form>
</div>
<script>
// Auto-focus sur chaque chiffre
const inputs = document.querySelectorAll('.otp-input input');
inputs.forEach((input, i) => {
    input.addEventListener('input', () => {
        if (input.value && i < inputs.length - 1) inputs[i + 1].focus();
    });
    input.addEventListener('keydown', e => {
        if (e.key === 'Backspace' && !input.value && i > 0) inputs[i - 1].focus();
    });
});
</script>
</body>
</html>
