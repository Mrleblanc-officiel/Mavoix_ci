<?php
// view/public/presentation.php
// Page publique de présentation des candidats.
// Si l'utilisateur est déjà connecté, il peut voter directement.
// Sinon, il sera redirigé vers la page de connexion.

session_start();

// Exemple de données statiques.
// Plus tard, vous pourrez remplacer ce tableau par les candidats récupérés depuis la base de données.
$candidats = [
    [
        'id' => 1,
        'nom' => 'Nom du candidat 1',
        'photo' => 'assets/images/trump.jfif',
        'parti' => 'Parti A',
        'description' => 'Programme axé sur le développement économique et social.'
    ],
    [
        'id' => 2,
        'nom' => 'Nom du candidat 2',
        'photo' => 'assets/images/candidat2.jfif',
        'parti' => 'Parti B',
        'description' => 'Priorité à l’éducation, à la santé et à la jeunesse.'
    ],
    [
        'id' => 3,
        'nom' => 'Nom du candidat 3',
        'photo' => 'assets/images/candidat3.jfif',
        'parti' => 'Parti C',
        'description' => 'Vision centrée sur l’innovation et la transparence.'
    ],
];

// Déterminer la destination du bouton principal
$destinationVote = isset($_SESSION['user'])
    ? '?page=accueil'   // utilisateur connecté → accès aux élections
    : '?page=login';    // utilisateur non connecté → connexion obligatoire
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MaVoix CI - Vote Électronique Sécurisé</title>

    <style>
        :root {
            --primary: #4195c6;
            --primary-dark: #2e78a4;
            --secondary: #32dfdf;
            --success: #10b981;
            --text-dark: #1b2430;
            --text-light: #ffffff;
            --bg-light: #f7fbfd;
            --card-bg: #ffffff;
            --shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            --radius: 18px;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #4195c6, #32dfdf);
            color: var(--text-dark);
            min-height: 100vh;
        }

        .hero {
            text-align: center;
            padding: 60px 20px 40px;
            color: white;
        }

        .hero h1 {
            font-size: clamp(2.5rem, 6vw, 4.5rem);
            margin-bottom: 15px;
            font-weight: bold;
        }

        .hero p {
            font-size: 1.1rem;
            max-width: 750px;
            margin: 0 auto 30px;
            line-height: 1.7;
            opacity: 0.95;
        }

        .actions {
            display: flex;
            justify-content: center;
            gap: 15px;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-block;
            padding: 14px 28px;
            border-radius: 999px;
            text-decoration: none;
            font-weight: bold;
            transition: 0.3s ease;
        }

        .btn-primary {
            background: white;
            color: var(--primary);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.15);
            color: white;
            border: 2px solid rgba(255, 255, 255, 0.4);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.25);
        }

        .section {
            background: var(--bg-light);
            border-radius: 40px 40px 0 0;
            padding: 60px 20px;
            margin-top: 30px;
        }

        .section-title {
            text-align: center;
            font-size: 2rem;
            margin-bottom: 10px;
            color: var(--text-dark);
        }

        .section-subtitle {
            text-align: center;
            max-width: 700px;
            margin: 0 auto 40px;
            color: #556;
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: auto;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
        }

        .card {
            background: var(--card-bg);
            border-radius: var(--radius);
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: 0.3s ease;
        }

        .card:hover {
            transform: translateY(-6px);
        }

        .card img {
            width: 100%;
            height: 280px;
            object-fit: cover;
        }

        .card-content {
            padding: 24px;
        }

        .card h3 {
            margin-bottom: 8px;
            font-size: 1.4rem;
        }

        .badge {
            display: inline-block;
            background: rgba(50, 223, 223, 0.15);
            color: #008b8b;
            padding: 6px 12px;
            border-radius: 999px;
            font-size: 0.85rem;
            font-weight: bold;
            margin-bottom: 12px;
        }

        .card p {
            line-height: 1.6;
            color: #555;
            margin-bottom: 20px;
        }

        .vote-btn {
            display: block;
            width: 100%;
            text-align: center;
            background: var(--primary);
            color: white;
            text-decoration: none;
            padding: 12px;
            border-radius: 10px;
            font-weight: bold;
            transition: 0.3s;
        }

        .vote-btn:hover {
            background: var(--primary-dark);
        }

        .steps {
            margin-top: 70px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
        }

        .step {
            background: white;
            padding: 25px;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            text-align: center;
        }

        .step-number {
            width: 45px;
            height: 45px;
            line-height: 45px;
            margin: 0 auto 15px;
            border-radius: 50%;
            background: var(--secondary);
            color: var(--text-dark);
            font-weight: bold;
        }

        footer {
            text-align: center;
            padding: 25px;
            background: #ffffff;
            color: #666;
            font-size: 0.9rem;
        }

        @media (max-width: 768px) {
            .hero {
                padding: 40px 20px 20px;
            }

            .section {
                padding: 40px 20px;
            }
        }
    </style>
</head>
<body>

<!-- HERO -->
<section class="hero">
    <h1>MaVoix CI</h1>
    <p>
        Plateforme de vote électronique sécurisée avec authentification,
        vérification OTP et preuve numérique de vote.
        Découvrez les candidats et participez aux élections en toute transparence.
    </p>

    <div class="actions">
        <a href="<?= $destinationVote ?>" class="btn btn-primary">
            Commencer à voter
        </a>
        <a href="?page=register" class="btn btn-secondary">
            Créer un compte
        </a>
    </div>
</section>

<!-- CANDIDATS -->
<section class="section">
    <div class="container">
        <h2 class="section-title">Liste des candidats</h2>
        <p class="section-subtitle">
            Consultez les profils des candidats avant de vous connecter
            pour participer au scrutin.
        </p>

        <div class="cards">
            <?php foreach ($candidats as $candidat): ?>
                <div class="card">
                    <img
                        src="<?= htmlspecialchars($candidat['photo']) ?>"
                        alt="<?= htmlspecialchars($candidat['nom']) ?>"
                    >

                    <div class="card-content">
                        <span class="badge">
                            <?= htmlspecialchars($candidat['parti']) ?>
                        </span>

                        <h3><?= htmlspecialchars($candidat['nom']) ?></h3>

                        <p>
                            <?= htmlspecialchars($candidat['description']) ?>
                        </p>

                        <a href="<?= $destinationVote ?>" class="vote-btn">
                            Voter pour ce candidat
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Étapes du processus -->
        <div class="steps">
            <div class="step">
                <div class="step-number">1</div>
                <h3>Créer un compte</h3>
                <p>Inscrivez-vous avec vos informations personnelles.</p>
            </div>

            <div class="step">
                <div class="step-number">2</div>
                <h3>Vérification OTP</h3>
                <p>Recevez et saisissez votre code de sécurité.</p>
            </div>

            <div class="step">
                <div class="step-number">3</div>
                <h3>Exprimer votre vote</h3>
                <p>Sélectionnez votre candidat en toute confidentialité.</p>
            </div>

            <div class="step">
                <div class="step-number">4</div>
                <h3>Confirmation</h3>
                <p>Obtenez une preuve numérique unique de votre vote.</p>
            </div>
        </div>
    </div>
</section>

<footer>
    © <?= date('Y') ?> MaVoix CI — Vote électronique sécurisé et transparent.
</footer>

</body>
</html>