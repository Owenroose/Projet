@extends('admin.layouts.auth')

@section('title', 'Réinitialiser le mot de passe')

@section('content')
    <h4 class="mb-2">Réinitialiser votre mot de passe 🔑</h4>
    <p class="mb-4">Entrez votre nouveau mot de passe ci-dessous.</p>

    <form id="formAuthentication" class="mb-3" action="{{ route('password.store') }}" method="POST">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email"
                value="{{ old('email', $request->email) }}" required readonly />
        </div>

        <div class="mb-3 form-password-toggle">
            <label class="form-label" for="password">Nouveau mot de passe</label>
            <div class="input-group input-group-merge">
                <input type="password" id="password" class="form-control @error('password') is-invalid @enderror"
                    name="password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                    required autocomplete="new-password" />
                <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
            </div>
            @error('password')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3 form-password-toggle">
            <label class="form-label" for="password_confirmation">Confirmer le nouveau mot de passe</label>
            <div class="input-group input-group-merge">
                <input type="password" id="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror"
                    name="password_confirmation" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                    required autocomplete="new-password" />
                <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
            </div>
            @error('password_confirmation')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <button class="btn btn-primary d-grid w-100">Réinitialiser le mot de passe</button>
    </form>
@endsection
