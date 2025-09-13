@extends('admin.layouts.auth')

@section('title', 'Inscription')
@section('subtitle', 'Rejoignez-nous pour une gestion de projet facile et amusante !')

@section('content')
    <form id="formAuthentication" action="{{ route('register') }}" method="POST">
        @csrf

        <div class="mb-4">
            <label for="name" class="block text-gray-700 text-sm font-semibold mb-2">Nom Complet</label>
            <input type="text" class="form-input @error('name') border-red-500 @enderror" id="name"
                name="name" placeholder="Entrez votre nom" value="{{ old('name') }}" required autofocus autocomplete="name" />
            @error('name')
                <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="email" class="block text-gray-700 text-sm font-semibold mb-2">Email</label>
            <input type="email" class="form-input @error('email') border-red-500 @enderror" id="email"
                name="email" placeholder="Entrez votre email" value="{{ old('email') }}" required autocomplete="username" />
            @error('email')
                <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-semibold mb-2" for="password">Mot de passe</label>
            <input type="password" id="password" class="form-input @error('password') border-red-500 @enderror"
                name="password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                required autocomplete="new-password" />
            @error('password')
                <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-semibold mb-2" for="password_confirmation">Confirmer le mot de passe</label>
            <input type="password" id="password_confirmation" class="form-input @error('password_confirmation') border-red-500 @enderror"
                name="password_confirmation" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                required autocomplete="new-password" />
            @error('password_confirmation')
                <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <button class="btn-submit" type="submit">S'inscrire</button>
        </div>
    </form>

    <p class="text-center text-sm text-gray-600 mt-4">
        <span>Vous avez déjà un compte ?</span>
        @if (Route::has('login'))
            <a href="{{ route('login') }}" class="link-text ml-1">Se connecter</a>
        @endif
    </p>
@endsection
