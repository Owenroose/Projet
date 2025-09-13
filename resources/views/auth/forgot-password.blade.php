@extends('admin.layouts.auth')

@section('title', 'Mot de passe oublié')

@section('content')
    <h4 class="mb-2">Mot de passe oublié ? 🔒</h4>
    <p class="mb-4">Entrez votre email et nous vous enverrons un lien pour réinitialiser votre mot de passe.</p>

    @if (session('status'))
        <div class="alert alert-success mt-3" role="alert">
            {{ session('status') }}
        </div>
    @endif

    <form id="formAuthentication" class="mb-3" action="{{ route('password.email') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                name="email" placeholder="Entrez votre email" value="{{ old('email') }}" required autofocus />
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button class="btn btn-primary d-grid w-100">Envoyer le lien de réinitialisation</button>
    </form>

    <div class="text-center">
        <a href="{{ route('login') }}" class="d-flex align-items-center justify-content-center">
            <i class="bx bx-chevron-left scaleX-n1-rtl bx-sm"></i>
            Retour à la connexion
        </a>
    </div>
@endsection
