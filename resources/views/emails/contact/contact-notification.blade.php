<!-- resources/views/emails/contact-notification.blade.php -->
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouveau message de contact</title>
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
        .info-grid {
            display: grid;
            grid-template-columns: auto 1fr;
            gap: 1rem;
            margin-bottom: 2rem;
            border: 1px solid #e5e5e5;
            border-radius: 6px;
            padding: 1.5rem;
            background-color: #f9f9f9;
        }
        .info-label {
            font-weight: 600;
            color: #0077b6;
            white-space: nowrap;
        }
        .info-value {
            word-break: break-word;
        }
        .message-section {
            background-color: #ffffff;
            border: 1px solid #e5e5e5;
            border-radius: 6px;
            padding: 1.5rem;
            margin-top: 1rem;
        }
        .message-content {
            background-color: #f8f9fa;
            border-left: 4px solid #0077b6;
            padding: 1rem;
            margin-top: 1rem;
            border-radius: 0 4px 4px 0;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 1.5rem;
            text-align: center;
            color: #666;
            font-size: 14px;
            border-top: 1px solid #e5e5e5;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: linear-gradient(135deg, #0077b6 0%, #2d6a4f 100%);
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            margin-top: 1rem;
        }
        .btn:hover {
            opacity: 0.9;
        }
        @media (max-width: 600px) {
            .container {
                margin: 10px;
                border-radius: 0;
            }
            .info-grid {
                grid-template-columns: 1fr;
                gap: 0.5rem;
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
            <h1>📧 Nouveau Message de Contact</h1>
            <p>Reçu le {{ $contact->created_at->format('d/m/Y à H:i') }}</p>
        </div>

        <div class="content">
            <div class="info-grid">
                <div class="info-label">👤 Nom :</div>
                <div class="info-value">{{ $contact->name }}</div>

                <div class="info-label">📧 Email :</div>
                <div class="info-value">
                    <a href="mailto:{{ $contact->email }}" style="color: #0077b6;">{{ $contact->email }}</a>
                </div>

                @if($contact->phone)
                <div class="info-label">📱 Téléphone :</div>
                <div class="info-value">
                    <a href="tel:{{ $contact->phone }}" style="color: #0077b6;">{{ $contact->phone }}</a>
                </div>
                @endif

                @if($contact->company)
                <div class="info-label">🏢 Entreprise :</div>
                <div class="info-value">{{ $contact->company }}</div>
                @endif

                <div class="info-label">📋 Sujet :</div>
                <div class="info-value"><strong>{{ $contact->subject }}</strong></div>

                <div class="info-label">🌐 IP :</div>
                <div class="info-value">{{ $contact->ip_address }}</div>

                <div class="info-label">🆔 ID :</div>
                <div class="info-value">#{{ $contact->id }}</div>
            </div>

            <div class="message-section">
                <h3 style="margin-top: 0; color: #0077b6;">💬 Message :</h3>
                <div class="message-content">{{ $contact->message }}</div>
            </div>

            <div style="text-align: center; margin-top: 2rem;">
                <a href="{{ url('/admin/contacts/' . $contact->id) }}" class="btn">
                    Voir dans l'administration
                </a>
            </div>
        </div>

        <div class="footer">
            <p>
                <strong>Nova Tech Bénin</strong><br>
                Ce message a été envoyé automatiquement depuis le site web.<br>
                Pour répondre, utilisez directement l'adresse email du contact.
            </p>
        </div>
    </div>
</body>
</html>
