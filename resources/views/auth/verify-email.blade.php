@extends('admin.layouts.auth')

@section('title', 'Vérifier l\'email')

@section('content')
    <h4 class="mb-2">Vérifiez votre adresse email</h4>
    <p class="mb-4">
        Merci pour votre inscription! Avant de commencer, pourriez-vous vérifier votre adresse email en cliquant sur le lien que nous venons de vous envoyer? Si vous n'avez pas reçu l'email, nous vous en enverrons un autre avec plaisir.
    </p>

    @if (session('status') == 'verification-link-sent')
        <div class="alert alert-success mt-3" role="alert">
            Un nouveau lien de vérification a été envoyé à l'adresse email que vous avez fournie lors de l'inscription.
        </div>
    @endif

    <div class="d-flex justify-content-between mt-4">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="btn btn-primary">Renvoyer l'email de vérification</button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-outline-secondary">Se déconnecter</button>
        </form>
    </div>
@endsection
