@extends('admin.layouts.app')

@section('title', 'Mon Profil')

@section('content')
<h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Administration /</span> Mon Profil
</h4>

<div class="row">
    <div class="col-md-12">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card mb-4">
            <h5 class="card-header">Paramètres du Profil</h5>
            <div class="card-body">
                <div class="d-flex align-items-start align-items-sm-center gap-4">
                    <div class="d-flex align-items-center justify-content-center me-3" style="width: 100px; height: 100px; background-color: #e9ecef; border-radius: 50%;">
                        <span class="text-muted" style="font-size: 4rem;">
                            <i class='bx bxs-user-circle'></i>
                        </span>
                    </div>
                    <div class="button-wrapper">
                        <h4 class="fw-bold mb-0">{{ $user->name }}</h4>
                        <p class="text-muted mb-0">{{ $user->email }}</p>
                    </div>
                </div>
            </div>
            <hr class="my-0" />
            <div class="card-body">
                <ul class="nav nav-pills flex-column flex-md-row mb-4">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#profile-info">
                            <i class="bx bx-user me-1"></i> Informations du Profil
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#security">
                            <i class="bx bx-shield-alt-2 me-1"></i> Sécurité
                        </a>
                    </li>
                </ul>

                <div class="tab-content p-0">
                    <div class="tab-pane fade show active" id="profile-info" role="tabpanel">
                        <form id="formAccountSettings" method="POST" action="{{ route('admin.profile.update') }}">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="mb-3 col-md-6">
                                    <label for="name" class="form-label">Nom</label>
                                    <input class="form-control @error('name') is-invalid @enderror" type="text" id="name" name="name" value="{{ old('name', $user->name) }}" autofocus />
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="email" class="form-label">E-mail</label>
                                    <input class="form-control @error('email') is-invalid @enderror" type="text" id="email" name="email" value="{{ old('email', $user->email) }}" placeholder="john.doe@example.com" />
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="mt-2">
                                <button type="submit" class="btn btn-primary me-2">Sauvegarder les modifications</button>
                                <button type="reset" class="btn btn-outline-secondary">Annuler</button>
                            </div>
                        </form>
                    </div>

                    <div class="tab-pane fade" id="security" role="tabpanel">
                        <form method="POST" action="{{ route('admin.profile.update') }}">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="mb-3 col-md-6">
                                    <label for="current_password" class="form-label">Mot de passe actuel</label>
                                    <input class="form-control @error('current_password') is-invalid @enderror" type="password" name="current_password" id="current_password" />
                                    @error('current_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="mb-3 col-md-6">
                                    <label for="new_password" class="form-label">Nouveau mot de passe</label>
                                    <input class="form-control @error('new_password') is-invalid @enderror" type="password" name="new_password" id="new_password" />
                                    @error('new_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="new_password_confirmation" class="form-label">Confirmer le nouveau mot de passe</label>
                                    <input class="form-control" type="password" name="new_password_confirmation" id="new_password_confirmation" />
                                </div>
                            </div>
                            <div class="mt-2">
                                <button type="submit" class="btn btn-primary me-2">Changer le mot de passe</button>
                                <button type="reset" class="btn btn-outline-secondary">Annuler</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
