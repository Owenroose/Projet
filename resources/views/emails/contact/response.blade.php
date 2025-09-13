@component('mail::message')
# Réponse à votre message

Bonjour {{ $contact->name }},

Merci d'avoir contacté Nova Tech Bénin. Voici notre réponse à votre message :

---

{!! nl2br(e($response)) !!}

---

**Votre message original :**
> {{ $contact->message }}

Si vous avez d'autres questions, n'hésitez pas à nous recontacter.

Cordialement,
L'équipe **Nova Tech Bénin**

@component('mail::button', ['url' => config('app.url')])
Visiter notre site
@endcomponent
@endcomponent
