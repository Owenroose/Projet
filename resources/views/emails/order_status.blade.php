<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Mise à jour de votre commande</title>
</head>
<body>
    <h1>Bonjour {{ $order->customer_name }},</h1>
    <p>Le statut de votre commande #{{ substr($order->order_group, 0, 8) }} a été mis à jour.</p>
    <p>Le nouveau statut est : **{{ $status }}**.</p>
    <p>Vous pouvez suivre l'état de votre commande en cliquant sur le lien ci-dessous :</p>
    <a href="{{ $trackLink }}">Suivre ma commande</a>
    <p>Merci pour votre confiance !</p>
</body>
</html>
