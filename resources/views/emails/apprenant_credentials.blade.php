<!DOCTYPE html>
<html>
<head>
    <title>Informations de connexion</title>
</head>
<body>
    <h1>Bonjour {{ $prenom }} {{ $nom }},</h1>
    <p>Voici vos informations de connexion :</p>
    <ul>
        <li>Email : {{ $email }}</li>
        <li>Mot de passe : {{ $password }}</li>
    </ul>
    <p>Vous pouvez vous connecter en cliquant sur ce lien : <a href="{{ $loginLink }}">Se connecter</a></p>
    
    <h2>Votre QR Code :</h2>
    <img src="data:image/png;base64,{{ base64_encode($qrcode) }}" alt="QR Code">

    <p>Merci,</p>
    <p>L'équipe de gestion des apprenants</p>
</body>
</html>
