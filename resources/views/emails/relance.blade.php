<!DOCTYPE html>
<html>
<head>
    <title>Relance pour Activation de Compte</title>
</head>
<body>
    <h1>Bonjour,</h1>
    <p>Nous avons remarqué que vous n'avez pas encore activé votre compte. Voici vos informations de connexion :</p>
    <p><strong>Email :</strong> {{ $login }}</p>
    <p><strong>Mot de passe par défaut :</strong> {{ $motDePasse }}</p>
    <p><a href="{{ url('/auth/activation') }}">Cliquez ici pour activer votre compte</a></p>
    <p>Merci!</p>
</body>
</html>
