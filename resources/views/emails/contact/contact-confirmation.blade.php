<!-- resources/views/emails/contact-confirmation.blade.php -->
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation de réception</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #0077b6 0%, #2d6a4f 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .content {
            padding: 2rem;
        }
        .message-summary {
            background-color: #f8f9fa;
            border-left: 4px solid #0077b6;
            padding: 1.5rem;
            margin: 1.5rem 0;
            border-radius: 0 6px 6px 0;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 1.5rem;
            text-align: center;
            color: #666;
            font-size: 14px;
            border-top: 1px solid #e5e5e5;
        }
        .contact-info {
            background-color: #e8f4f8;
            border-radius: 6px;
            padding: 1.5rem;
            margin: 1.5rem 0;
            border: 1px solid #b3d9e8;
        }
        .contact-info h3 {
            margin-top: 0;
            color: #0077b6;
        }
        .social-links {
            margin-top: 1rem;
        }
        .social-links a {
            display: inline-block;
            margin: 0 10px;
            color: #0077b6;
            text-decoration: none;
        }
        @media (max-width: 600px) {
            .container {
                margin: 10px;
                border-radius: 0;
            }
            .header, .content {
                padding: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✅ Confirmation de réception</h1>
            <p>Merci pour votre message !</p>
        </div>

        <div class="content">
            <p>Bonjour <strong>{{ $contact->name }}</strong>,</p>

            <p>Nous avons bien reçu votre message et vous remercions de l'intérêt que vous portez à <strong>Nova Tech Bénin</strong>.</p>

            <div class="message-summary">
                <h3 style="margin-top: 0; color: #0077b6;">📝 Récapitulatif de votre message :</h3>
                <p><strong>Sujet :</strong> {{ $contact->subject }}</p>
                <p><strong>Date d'envoi :</strong> {{ $contact->created_at->format('d/m/Y à H:i') }}</p>
                <p><strong>Numéro de référence :</strong> #NT{{ str_pad($contact->id, 6, '0', STR_PAD_LEFT) }}</p>
            </div>

            <p>Notre équipe technique examine actuellement votre demande et vous répondra dans les <strong>24 à 48 heures</strong> qui suivent.</p>

            <p>En attendant notre réponse, n'hésitez pas à :</p>
            <ul>
                <li>Visiter notre site web : <a href="https://novatechbenin.com" style="color: #0077b6;">novatechbenin.com</a></li>
                <li>Consulter notre portfolio de projets</li>
                <li>Découvrir nos services de développement web et mobile</li>
            </ul>

            <div class="contact-info">
                <h3>📞 Nous contacter directement</h3>
                <p>
                    <strong>Téléphone :</strong> +229 XX XX XX XX<br>
                    <strong>Email :</strong> <a href="mailto:contact@novatechbenin.com" style="color: #0077b6;">contact@novatechbenin.com</a><br>
                    <strong>Adresse :</strong> Cotonou, Bénin
                </p>

                <div class="social-links">
                    <p><strong>Suivez-nous :</strong></p>
                    <a href="#" style="color: #0077b6;">LinkedIn</a>
                    <a href="#" style="color: #0077b6;">Facebook</a>
                    <a href="#" style="color: #0077b6;">Twitter</a>
                </div>
            </div>

            <p>Merci encore pour votre confiance !</p>

            <p>
                Cordialement,<br>
                <strong>L'équipe Nova Tech Bénin</strong>
            </p>
        </div>

        <div class="footer">
            <p>
                <strong>Nova Tech Bénin</strong> - Solutions technologiques innovantes<br>
                Spécialisés en développement web, mobile et conseil digital<br>
                <em>Ce message a été envoyé automatiquement, merci de ne pas répondre à cette adresse.</em>
            </p>
        </div>
    </div>
</body>
</html>
