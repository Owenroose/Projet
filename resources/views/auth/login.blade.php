@extends('admin.layouts.auth')

@section('title', 'Connexion')

@section('content')
    <h4 class="mb-2">Bienvenue chez Novatech! 👋</h4>
    <p class="mb-4">Veuillez vous connecter à votre compte et commencer l'aventure</p>

    @if (session('status'))
        <div class="alert alert-success mt-3" role="alert">
            {{ session('status') }}
        </div>
    @endif

    <form id="formAuthentication" class="mb-3" action="{{ route('login') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="email" class="form-label">Adresse Email</label>
            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                name="email" placeholder="Entrez votre email" value="{{ old('email') }}" required autofocus autocomplete="username" />
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3 form-password-toggle">
            <div class="d-flex justify-content-between">
                <label class="form-label" for="password">Mot de passe</label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}">
                        <small>Mot de passe oublié ?</small>
                    </a>
                @endif
            </div>
            <div class="input-group input-group-merge">
                <input type="password" id="password" class="form-control @error('password') is-invalid @enderror"
                    name="password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                    required autocomplete="current-password" />
                <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
            </div>
            @error('password')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="remember_me" name="remember">
                <label class="form-check-label" for="remember_me"> Se souvenir de moi </label>
            </div>
        </div>

        <div class="mb-3">
            <button class="btn btn-primary d-grid w-100" type="submit">Se connecter</button>
        </div>
    </form>

    <p class="text-center">
        <span>Nouveau sur notre plateforme?</span>
        @if (Route::has('register'))
            <a href="{{ route('register') }}">
                <span>Créer un compte</span>
            </a>
        @endif
    </p>
@endsection
