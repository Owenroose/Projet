@component('mail::message')
# Confirmation de réception

Bonjour {{ $contact->name }},

Nous avons bien reçu votre message et vous en remercions. Notre équipe va traiter votre demande dans les plus brefs délais.

**Résumé de votre message :**
- Sujet : {{ $contact->subject }}
- Message : {{ Str::limit($contact->message, 200) }}

Nous vous contacterons très prochainement.

Cordialement,
L'équipe **Nova Tech Bénin**

@component('mail::button', ['url' => config('app.url')])
Visiter notre site
@endcomponent
@endcomponent
