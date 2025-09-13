@component('mail::message')
# Nouveau message de contact

Un nouveau message a été reçu via le formulaire de contact.

**Détails du message :**
- Nom : {{ $contact->name }}
- Email : {{ $contact->email }}
- Téléphone : {{ $contact->phone ?? 'Non renseigné' }}
- Société : {{ $contact->company ?? 'Non renseignée' }}
- Sujet : {{ $contact->subject }}
- Message : {{ $contact->message }}

@component('mail::button', ['url' => route('admin.contacts.show', $contact)])
Voir le message dans l'admin
@endcomponent
@endcomponent
