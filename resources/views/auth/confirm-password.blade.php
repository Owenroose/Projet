@extends('admin.layouts.auth')

@section('title', 'Confirmer le mot de passe')

@section('content')
    <h4 class="mb-2">Zone sécurisée</h4>
    <p class="mb-4">Ceci est une zone sécurisée. Veuillez confirmer votre mot de passe pour continuer.</p>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <div class="mb-3 form-password-toggle">
            <label class="form-label" for="password">Mot de passe</label>
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
            <button type="submit" class="btn btn-primary d-grid w-100">Confirmer</button>
        </div>
    </form>
@endsection
