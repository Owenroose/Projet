@extends('admin.layouts.app')

@section('title', 'Ajouter un utilisateur')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Utilisateurs /</span> Ajouter
        </h4>

        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bx bx-user-plus me-2 text-primary"></i>
                            Ajouter un nouvel utilisateur
                        </h5>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary">
                                <i class="bx bx-arrow-back me-1"></i>Retour
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data" id="createUserForm">
                            @csrf

                            <!-- Section Informations personnelles -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="card-subtitle mb-3 text-primary">
                                        <i class="bx bx-user me-2"></i>Informations personnelles
                                    </h6>
                                </div>

                                <!-- Avatar -->
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Photo de profil</label>
                                    <div class="d-flex align-items-start align-items-sm-center gap-4">
                                        <img src="{{ asset('admin/assets/img/avatars/default.png') }}"
                                             alt="user-avatar" class="d-block rounded" height="100" width="100" id="uploadedAvatar">
                                        <div class="button-wrapper">
                                            <label for="avatar" class="btn btn-primary me-2 mb-4" tabindex="0">
                                                <span class="d-none d-sm-block">Télécharger une photo</span>
                                                <i class="bx bx-upload d-block d-sm-none"></i>
                                                <input type="file" id="avatar" name="avatar"
                                                       class="account-file-input @error('avatar') is-invalid @enderror"
                                                       hidden accept="image/png, image/jpeg, image/jpg, image/gif">
                                            </label>
                                            <button type="button" class="btn btn-outline-secondary account-image-reset mb-4">
                                                <i class="bx bx-reset d-block d-sm-none"></i>
                                                <span class="d-none d-sm-block">Réinitialiser</span>
                                            </button>
                                            <p class="text-muted mb-0">Formats autorisés: JPG, GIF, PNG. Taille max: 2MB</p>
                                            @error('avatar')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Nom -->
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">
                                        Nom complet <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                           id="name" name="name" value="{{ old('name') }}"
                                           placeholder="Nom de l'utilisateur" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Email -->
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">
                                        Adresse e-mail <span class="text-danger">*</span>
                                    </label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                           id="email" name="email" value="{{ old('email') }}"
                                           placeholder="email@example.com" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Téléphone -->
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">Téléphone</label>
                                    <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                                           id="phone" name="phone" value="{{ old('phone') }}"
                                           placeholder="+229 XX XX XX XX">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Statut actif -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Statut du compte</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="is_active"
                                               id="is_active" value="1" {{ old('is_active', 1) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">
                                            Compte actif
                                        </label>
                                    </div>
                                    <small class="text-muted">Si désactivé, l'utilisateur ne pourra pas se connecter</small>
                                </div>
                            </div>

                            <!-- Section Mot de passe -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="card-subtitle mb-3 text-primary">
                                        <i class="bx bx-lock me-2"></i>Sécurité
                                    </h6>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="password">
                                        Mot de passe <span class="text-danger">*</span>
                                    </label>
                                    <div class="form-password-toggle">
                                        <div class="input-group input-group-merge">
                                            <input type="password" id="password"
                                                   class="form-control @error('password') is-invalid @enderror"
                                                   name="password"
                                                   placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                                   required>
                                            <span class="input-group-text cursor-pointer">
                                                <i class="bx bx-hide"></i>
                                            </span>
                                        </div>
                                        @error('password')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <small class="text-muted">Minimum 8 caractères</small>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="password_confirmation">
                                        Confirmer le mot de passe <span class="text-danger">*</span>
                                    </label>
                                    <div class="form-password-toggle">
                                        <div class="input-group input-group-merge">
                                            <input type="password" id="password_confirmation"
                                                   class="form-control" name="password_confirmation"
                                                   placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                                   required>
                                            <span class="input-group-text cursor-pointer">
                                                <i class="bx bx-hide"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Vérification email -->
                                <div class="col-md-12 mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="email_verified"
                                               id="email_verified" value="1" {{ old('email_verified') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="email_verified">
                                            Marquer l'email comme vérifié
                                        </label>
                                    </div>
                                    <small class="text-muted">L'utilisateur n'aura pas besoin de vérifier son email</small>
                                </div>
                            </div>

                            <!-- Section Rôles -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="card-subtitle mb-3 text-primary">
                                        <i class="bx bx-shield me-2"></i>Rôles et permissions
                                    </h6>
                                </div>

                                <div class="col-12">
                                    @if($roles->count() > 0)
                                        <div class="row">
                                            @foreach($roles as $role)
                                                <div class="col-md-6 col-lg-4 mb-3">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox"
                                                               name="roles[]" value="{{ $role->name }}"
                                                               id="role_{{ $role->id }}"
                                                               {{ in_array($role->name, old('roles', [])) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="role_{{ $role->id }}">
                                                            <strong>{{ $role->display_name ?? $role->name }}</strong>
                                                            @if($role->description)
                                                                <br>
                                                                <small class="text-muted">{{ $role->description }}</small>
                                                            @endif
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        @error('roles')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    @else
                                        <div class="alert alert-info">
                                            <i class="bx bx-info-circle me-2"></i>
                                            Aucun rôle disponible. Veuillez d'abord créer des rôles.
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Section Options -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="card-subtitle mb-3 text-primary">
                                        <i class="bx bx-cog me-2"></i>Options
                                    </h6>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="send_welcome_email"
                                               id="send_welcome_email" value="1" {{ old('send_welcome_email') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="send_welcome_email">
                                            Envoyer un email de bienvenue
                                        </label>
                                    </div>
                                    <small class="text-muted">L'utilisateur recevra ses identifiants par email</small>
                                </div>
                            </div>

                            <!-- Boutons d'action -->
                            <div class="row">
                                <div class="col-12">
                                    <hr class="my-4">
                                    <div class="d-flex justify-content-between">
                                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                                            <i class="bx bx-x me-1"></i>Annuler
                                        </a>
                                        <button type="submit" class="btn btn-primary" id="submitBtn">
                                            <i class="bx bx-check me-1"></i>Créer l'utilisateur
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Sidebar avec aide -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="bx bx-help-circle me-2 text-info"></i>Aide
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <h6 class="text-primary">Informations générales</h6>
                            <ul class="list-unstyled small">
                                <li><i class="bx bx-check text-success me-1"></i> Nom et email sont obligatoires</li>
                                <li><i class="bx bx-check text-success me-1"></i> L'email doit être unique</li>
                                <li><i class="bx bx-check text-success me-1"></i> Le mot de passe doit faire au moins 8 caractères</li>
                            </ul>
                        </div>

                        <div class="mb-3">
                            <h6 class="text-primary">Rôles</h6>
                            <ul class="list-unstyled small">
                                <li><i class="bx bx-info-circle text-info me-1"></i> Un utilisateur peut avoir plusieurs rôles</li>
                                <li><i class="bx bx-info-circle text-info me-1"></i> Les permissions sont cumulatives</li>
                                <li><i class="bx bx-shield text-warning me-1"></i> Soyez prudent avec les rôles administrateur</li>
                            </ul>
                        </div>

                        <div class="mb-3">
                            <h6 class="text-primary">Photo de profil</h6>
                            <ul class="list-unstyled small">
                                <li><i class="bx bx-image text-info me-1"></i> Formats: JPG, PNG, GIF</li>
                                <li><i class="bx bx-info-circle text-info me-1"></i> Taille max: 2MB</li>
                                <li><i class="bx bx-crop text-info me-1"></i> Recommandé: 400x400px</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="bx bx-bulb me-2 text-warning"></i>Conseils
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info p-2">
                            <small>
                                <i class="bx bx-info-circle me-1"></i>
                                Si vous activez "Envoyer un email de bienvenue", l'utilisateur recevra ses identifiants par email.
                            </small>
                        </div>
                        <div class="alert alert-warning p-2">
                            <small>
                                <i class="bx bx-shield me-1"></i>
                                Seuls les super-administrateurs peuvent créer d'autres super-administrateurs.
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gestion de l'upload d'avatar
    const avatarInput = document.getElementById('avatar');
    const avatarImg = document.getElementById('uploadedAvatar');
    const resetBtn = document.querySelector('.account-image-reset');

    if (avatarInput && avatarImg) {
        avatarInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    avatarImg.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    }

    if (resetBtn && avatarImg) {
        resetBtn.addEventListener('click', function() {
            avatarImg.src = '{{ asset("admin/assets/img/avatars/default.png") }}';
            if (avatarInput) {
                avatarInput.value = '';
            }
        });
    }

    // Validation du formulaire
    const form = document.getElementById('createUserForm');
    const submitBtn = document.getElementById('submitBtn');

    if (form && submitBtn) {
        form.addEventListener('submit', function(e) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Création...';
        });
    }

    // Validation des mots de passe
    const password = document.getElementById('password');
    const passwordConfirm = document.getElementById('password_confirmation');

    if (password && passwordConfirm) {
        passwordConfirm.addEventListener('input', function() {
            if (password.value !== passwordConfirm.value) {
                passwordConfirm.setCustomValidity('Les mots de passe ne correspondent pas');
            } else {
                passwordConfirm.setCustomValidity('');
            }
        });
    }
});
</script>
@endsection
