    <meta charset="UTF-8">
    <title>Connexion</title>
</head>
<body>

    <h1>Connexion</h1>

    <form action="?page=login-process" method="POST">

        <input
            type="email"
            name="email"
            placeholder="Email"
            required
        >

        <br><br>

        <input
            type="password"
            name="password"
            placeholder="Mot de passe"
            required
        >

        <br><br>

        <button type="submit">
            Se connecter
        </button>

    </form>

</body>
</html>