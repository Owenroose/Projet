@extends('admin.layouts.app')

@section('title', 'Détails du Rôle - ' . $role->name)

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center">
            <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary me-3">
                <i class="bx bx-arrow-back me-1"></i>
                Retour
            </a>
            <div>
                <h4 class="fw-bold mb-1">
                    <i class="bx bx-shield-quarter text-primary me-2"></i>
                    {{ $role->name }}
                </h4>
                <p class="text-muted mb-0">Gérer les utilisateurs et permissions de ce rôle</p>
            </div>
        </div>
        <div class="d-flex gap-2"> {{-- Conteneur Flexbox pour les boutons --}}
            @can('update-role')
            <a href="{{ route('admin.roles.edit', $role->id) }}" class="btn btn-warning">
                <i class="bx bx-edit-alt me-1"></i>
                Modifier
            </a>
            @endcan
            @can('delete-role')
            <form action="{{ route('admin.roles.destroy', $role->id) }}" method="POST" class="d-inline" onsubmit="return confirmDelete();">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="bx bx-trash me-1"></i>
                    Supprimer
                </button>
            </form>
            @endcan
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 col-12 mb-3">
            <div class="card card-stat-users border-0 shadow-sm text-white h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-md rounded me-3">
                            <i class="bx bx-users text-white"></i>
                        </div>
                        <div>
                            <p class="text-white-50 mb-0 small">Utilisateurs</p>
                            <h4 class="mb-0 fw-bold text-white">{{ $roleStats['users_count'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-12 mb-3">
            <div class="card card-stat-permissions border-0 shadow-sm text-white h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-md rounded me-3">
                            <i class="bx bx-key text-white"></i>
                        </div>
                        <div>
                            <p class="text-white-50 mb-0 small">Permissions</p>
                            <h4 class="mb-0 fw-bold text-white">{{ $roleStats['permissions_count'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-12 mb-3">
            <div class="card card-stat-created border-0 shadow-sm text-white h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-md rounded me-3">
                            <i class="bx bx-calendar text-white"></i>
                        </div>
                        <div>
                            <p class="text-white-50 mb-0 small">Créé il y a</p>
                            <h6 class="mb-0 fw-bold text-white">{{ $roleStats['created_days_ago'] }} jour(s)</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-12 mb-3">
            <div class="card card-stat-updated border-0 shadow-sm text-white h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-md rounded me-3">
                            <i class="bx bx-time text-white"></i>
                        </div>
                        <div>
                            <p class="text-white-50 mb-0 small">Mis à jour</p>
                            <h6 class="mb-0 fw-bold text-white">{{ $roleStats['last_updated'] }}</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-semibold">
                        <i class="bx bx-users text-primary me-2"></i>
                        Utilisateurs ({{ $role->users->count() }})
                    </h5>
                    @can('update-role')
                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#assignUserModal">
                        <i class="bx bx-plus me-1"></i>
                        Assigner
                    </button>
                    @endcan
                </div>
                <div class="card-body p-0">
                    @if($role->users->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Utilisateur</th>
                                        <th>Email</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="usersList">
                                    @foreach($role->users as $user)
                                    <tr id="user-row-{{ $user->id }}">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm me-3 bg-primary rounded-circle">
                                                    <span class="text-white fw-bold">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">{{ $user->name }}</h6>
                                                    <small class="text-muted">ID: {{ $user->id }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $user->email }}</td>
                                        <td class="text-center">
                                            @can('update-role')
                                            <button class="btn btn-sm btn-outline-danger" onclick="removeUser({{ $user->id }}, '{{ $user->name }}')">
                                                <i class="bx bx-trash"></i>
                                            </button>
                                            @endcan
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bx bx-user-x display-4 text-muted mb-3"></i>
                            <h6 class="text-muted">Aucun utilisateur assigné</h6>
                            <p class="text-muted mb-3">Commencez par assigner des utilisateurs à ce rôle</p>
                            @can('update-role')
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#assignUserModal">
                                <i class="bx bx-plus me-1"></i>
                                Assigner un utilisateur
                            </button>
                            @endcan
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-semibold">
                        <i class="bx bx-key text-primary me-2"></i>
                        Permissions ({{ $role->permissions->count() }})
                    </h5>
                    @can('update-role')
                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#assignPermissionModal">
                        <i class="bx bx-plus me-1"></i>
                        Ajouter
                    </button>
                    @endcan
                </div>
                <div class="card-body">
                    @if($role->permissions->count() > 0)
                        <div class="d-flex flex-wrap gap-2" id="permissionsList">
                            @foreach($role->permissions as $permission)
                            <span class="badge bg-permission text-dark fw-normal fs-6 p-2 d-flex align-items-center" id="permission-badge-{{ $permission->id }}">
                                <i class="bx bx-check-circle fs-6 me-1"></i>
                                {{ $permission->name }}
                                @can('update-role')
                                <button class="btn-close btn-close-sm ms-2" onclick="removePermission('{{ $permission->name }}', {{ $permission->id }})" aria-label="Retirer"></button>
                                @endcan
                            </span>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bx bx-key display-4 text-muted mb-3"></i>
                            <h6 class="text-muted">Aucune permission assignée</h6>
                            <p class="text-muted mb-3">Ajoutez des permissions pour définir les capacités de ce rôle</p>
                            @can('update-role')
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#assignPermissionModal">
                                <i class="bx bx-plus me-1"></i>
                                Ajouter une permission
                            </button>
                            @endcan
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold">
                        <i class="bx bx-info-circle text-primary me-2"></i>
                        Informations du Rôle
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Nom du rôle :</label>
                                <p class="mb-0">{{ $role->name }}</p>
                            </div>
                            @if($role->description)
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Description :</label>
                                <p class="mb-0">{{ $role->description }}</p>
                            </div>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Créé le :</label>
                                <p class="mb-0">{{ $role->created_at->format('d/m/Y à H:i') }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Dernière modification :</label>
                                <p class="mb-0">{{ $role->updated_at->format('d/m/Y à H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="assignUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bx bx-user-plus me-2"></i>
                    Assigner un utilisateur
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="assignUserForm">
                    <div class="mb-3">
                        <label for="userSelect" class="form-label">Sélectionner un utilisateur</label>
                        <select class="form-select" id="userSelect" required>
                            <option value="">Choisir un utilisateur...</option>
                            @foreach($availableUsers as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" onclick="assignUser()">
                    <i class="bx bx-check me-1"></i>
                    Assigner
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="assignPermissionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bx bx-key me-2"></i>
                    Ajouter une permission
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="assignPermissionForm">
                    <div class="mb-3">
                        <label for="permissionSelect" class="form-label">Sélectionner une permission</label>
                        <select class="form-select" id="permissionSelect" required>
                            <option value="">Choisir une permission...</option>
                            @foreach($availablePermissions as $permission)
                            <option value="{{ $permission->name }}">{{ $permission->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" onclick="assignPermission()">
                    <i class="bx bx-check me-1"></i>
                    Ajouter
                </button>
            </div>
        </div>
    </div>
</div>

<style>
/* Palette de couleurs */
:root {
    --color-primary-dark: #3F51B5; /* Bleu Indigo plus prononcé pour le branding */
    --color-primary-light: #C5CAE9; /* Bleu Indigo très clair pour les fonds subtils */
    --color-secondary: #009688; /* Vert Teal pour un accent frais */
    --color-tertiary: #FFC107; /* Ambre pour un accent chaleureux */
    --color-gray-dark: #607D8B; /* Bleu-gris pour les éléments neutres mais présents */
    --color-gray-light: #ECEFF1; /* Gris très clair pour le fond global */
    --color-permission-bg: #E0F2F7; /* Bleu très clair pour le fond des badges de permission */
    --color-permission-text: #006064; /* Cyan foncé pour le texte des badges de permission */
}

body {
    background-color: var(--color-gray-light);
}

.card {
    border-radius: 12px;
    border: none;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08); /* Ombre plus douce et diffuse */
}

/* Styles pour les cartes de statistiques */
.card-stat-users { background-color: var(--color-primary-dark); }
.card-stat-permissions { background-color: var(--color-secondary); }
.card-stat-created { background-color: var(--color-tertiary); }
.card-stat-updated { background-color: var(--color-gray-dark); }

.card-stat-users .avatar { background-color: rgba(255, 255, 255, 0.25); }
.card-stat-permissions .avatar { background-color: rgba(255, 255, 255, 0.25); }
.card-stat-created .avatar { background-color: rgba(255, 255, 255, 0.25); }
.card-stat-updated .avatar { background-color: rgba(255, 255, 255, 0.25); }

/* Styles généraux */
.text-primary { color: var(--color-primary-dark) !important; }
.btn-primary, .btn-primary:hover {
    background-color: var(--color-primary-dark);
    border-color: var(--color-primary-dark);
}

.bg-primary { background-color: var(--color-primary-dark) !important; }

.avatar {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 0.875rem;
    font-weight: 500;
    color: #fff;
    text-align: center;
    vertical-align: middle;
    user-select: none;
    border: 0;
}

.avatar-sm {
    width: 2rem;
    height: 2rem;
}

.avatar-md {
    width: 2.5rem;
    height: 2.5rem;
}

/* Styles pour les badges de permission */
.badge.bg-permission {
    background-color: var(--color-permission-bg) !important;
    color: var(--color-permission-text) !important;
    border: 1px solid rgba(0, 96, 100, 0.2); /* Bordure subtile pour les permissions */
    font-size: 0.9em;
    padding: 0.6em 1em;
    border-radius: 20px; /* Bords arrondis pour un look moderne */
}

.badge.bg-permission .btn-close-sm {
    --bs-btn-close-color: var(--color-permission-text); /* Couleur de la croix de fermeture */
    opacity: 0.7;
    margin-left: 0.75rem !important;
}
.badge.bg-permission .btn-close-sm:hover {
    opacity: 1;
}

/* Ajustements pour les boutons de suppression de permission */
.btn-close-sm {
    --bs-btn-close-width: 0.7em;
    --bs-btn-close-height: 0.7em;
    --bs-btn-close-bg: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23607d8b'%3e%3cpath d='m.235 1.027 4.496 4.496 4.496-4.496a.75.75 0 1 1 1.06 1.06L6.791 6.583l4.496 4.496a.75.75 0 1 1-1.06 1.06L5.731 7.643l-4.496 4.496a.75.75 0 1 1-1.06-1.06L4.671 6.583.175 2.087a.75.75 0 0 1 1.06-1.06z'/%3e%3c/svg%3e"); /* Couleur légèrement plus foncée pour la croix */
}

</style>

<script>
// Configuration CSRF
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

// Messages d'alerte
function showAlert(message, type = 'success') {
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            <i class="bx ${type === 'success' ? 'bx-check-circle' : 'bx-x-circle'} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;

    // Insérer l'alerte en haut de la page
    const container = document.querySelector('.container-xxl');
    container.insertAdjacentHTML('afterbegin', alertHtml);

    // Auto-hide après 5 secondes
    setTimeout(() => {
        const alert = container.querySelector('.alert');
        if (alert) {
            alert.remove();
        }
    }, 5000);
}

// Assigner un utilisateur
async function assignUser() {
    const userSelect = document.getElementById('userSelect');
    const userId = userSelect.value;
    const userName = userSelect.options[userSelect.selectedIndex].text.split('(')[0].trim(); // Récupérer le nom de l'utilisateur

    if (!userId) {
        showAlert('Veuillez sélectionner un utilisateur.', 'warning');
        return;
    }

    try {
        const response = await fetch(`{{ route('admin.roles.assign-user', $role->id) }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                user_id: userId
            })
        });

        const data = await response.json();

        if (response.ok && data.success) {
            // Fermer le modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('assignUserModal'));
            modal.hide();

            // Ajouter l'utilisateur à la liste
            addUserToList(data.user);

            // Retirer l'utilisateur des options disponibles
            userSelect.querySelector(`option[value="${userId}"]`).remove();
            userSelect.value = '';

            // Mettre à jour le compteur
            updateUsersCounter();

            showAlert(data.message, 'success');
        } else {
            showAlert(data.error || 'Une erreur est survenue.', 'danger');
        }
    } catch (error) {
        console.error('Erreur:', error);
        showAlert('Une erreur réseau est survenue.', 'danger');
    }
}

// Ajouter un utilisateur à la liste
function addUserToList(user) {
    let usersList = document.getElementById('usersList');
    let cardBody = usersList ? usersList.closest('.card-body') : null;
    let emptyState = cardBody ? cardBody.querySelector('.text-center.py-5') : null;

    // Si le tableau est vide, le recréer
    if (!usersList || usersList.children.length === 0) {
        if (cardBody) {
            cardBody.innerHTML = `
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Utilisateur</th>
                                <th>Email</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="usersList"></tbody>
                    </table>
                </div>
            `;
            usersList = document.getElementById('usersList');
        }
    }

    // Supprimer l'état vide s'il existe (si des éléments étaient présents avant)
    if (emptyState) {
        emptyState.remove();
    }


    const newRow = `
        <tr id="user-row-${user.id}">
            <td>
                <div class="d-flex align-items-center">
                    <div class="avatar avatar-sm me-3 bg-primary rounded-circle">
                        <span class="text-white fw-bold">${user.name.substring(0, 2).toUpperCase()}</span>
                    </div>
                    <div>
                        <h6 class="mb-0">${user.name}</h6>
                        <small class="text-muted">ID: ${user.id}</small>
                    </div>
                </div>
            </td>
            <td>${user.email}</td>
            <td class="text-center">
                @can('update-role')
                <button class="btn btn-sm btn-outline-danger" onclick="removeUser(${user.id}, '${user.name}')">
                    <i class="bx bx-trash"></i>
                </button>
                @endcan
            </td>
        </tr>
    `;

    if (usersList) {
        usersList.insertAdjacentHTML('beforeend', newRow);
    }
}

// Retirer un utilisateur
async function removeUser(userId, userName) {
    if (!confirm(`Êtes-vous sûr de vouloir retirer "${userName}" de ce rôle ?`)) {
        return;
    }

    try {
        const response = await fetch(`{{ route('admin.roles.remove-user', $role->id) }}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                user_id: userId
            })
        });

        const data = await response.json();

        if (response.ok && data.success) {
            // Supprimer la ligne de l'utilisateur
            const userRow = document.getElementById(`user-row-${userId}`);
            if (userRow) {
                userRow.remove();
            }

            // Remettre l'utilisateur dans les options disponibles (si l'élément userSelect existe)
            const userSelect = document.getElementById('userSelect');
            if (userSelect) {
                const option = document.createElement('option');
                option.value = userId;
                option.textContent = `${userName}`; // Ne pas oublier de récupérer l'email si nécessaire
                userSelect.appendChild(option);
            }


            // Mettre à jour le compteur
            updateUsersCounter();

            showAlert(data.message, 'success');

            // Vérifier s'il faut afficher l'état vide
            checkEmptyUsersState();
        } else {
            showAlert(data.error || 'Une erreur est survenue.', 'danger');
        }
    } catch (error) {
        console.error('Erreur:', error);
        showAlert('Une erreur réseau est survenue.', 'danger');
    }
}

// Assigner une permission
async function assignPermission() {
    const permissionSelect = document.getElementById('permissionSelect');
    const permissionName = permissionSelect.value;

    if (!permissionName) {
        showAlert('Veuillez sélectionner une permission.', 'warning');
        return;
    }

    try {
        const response = await fetch(`{{ route('admin.roles.assign-permission', $role->id) }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                permission_name: permissionName
            })
        });

        const data = await response.json();

        if (response.ok && data.success) {
            // Fermer le modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('assignPermissionModal'));
            modal.hide();

            // Ajouter la permission à la liste
            addPermissionToList(data.permission);

            // Retirer la permission des options disponibles
            permissionSelect.querySelector(`option[value="${permissionName}"]`).remove();
            permissionSelect.value = '';

            // Mettre à jour le compteur
            updatePermissionsCounter();

            showAlert(data.message, 'success');
        } else {
            showAlert(data.error || 'Une erreur est survenue.', 'danger');
        }
    } catch (error) {
        console.error('Erreur:', error);
        showAlert('Une erreur réseau est survenue.', 'danger');
    }
}

// Ajouter une permission à la liste
function addPermissionToList(permission) {
    let permissionsList = document.getElementById('permissionsList');
    let cardBody = permissionsList ? permissionsList.closest('.card-body') : null;
    let emptyState = cardBody ? cardBody.querySelector('.text-center.py-4') : null;

    // Supprimer l'état vide s'il existe
    if (emptyState) {
        emptyState.remove();
        // S'assurer que le conteneur flexbox est là
        if (!permissionsList) {
             const newDiv = document.createElement('div');
             newDiv.id = 'permissionsList';
             newDiv.className = 'd-flex flex-wrap gap-2';
             if(cardBody) cardBody.appendChild(newDiv);
             permissionsList = newDiv;
        }
    }


    const newBadge = `
        <span class="badge bg-permission text-dark fw-normal fs-6 p-2 d-flex align-items-center" id="permission-badge-${permission.id}">
            <i class="bx bx-check-circle fs-6 me-1"></i>
            ${permission.name}
            @can('update-role')
            <button class="btn-close btn-close-sm ms-2" onclick="removePermission('${permission.name}', ${permission.id})" aria-label="Retirer"></button>
            @endcan
        </span>
    `;

    if (permissionsList) {
        permissionsList.insertAdjacentHTML('beforeend', newBadge);
    }
}

// Retirer une permission
async function removePermission(permissionName, permissionId) {
    if (!confirm(`Êtes-vous sûr de vouloir retirer la permission "${permissionName}" de ce rôle ?`)) {
        return;
    }

    try {
        const response = await fetch(`{{ route('admin.roles.remove-permission', $role->id) }}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                permission_name: permissionName
            })
        });

        const data = await response.json();

        if (response.ok && data.success) {
            // Supprimer le badge de la permission
            const permissionBadge = document.getElementById(`permission-badge-${permissionId}`);
            if (permissionBadge) {
                permissionBadge.remove();
            }

            // Remettre la permission dans les options disponibles (si l'élément permissionSelect existe)
            const permissionSelect = document.getElementById('permissionSelect');
            if (permissionSelect) {
                const option = document.createElement('option');
                option.value = permissionName;
                option.textContent = permissionName;
                permissionSelect.appendChild(option);
            }


            // Mettre à jour le compteur
            updatePermissionsCounter();

            showAlert(data.message, 'success');

            // Vérifier s'il faut afficher l'état vide
            checkEmptyPermissionsState();
        } else {
            showAlert(data.error || 'Une erreur est survenue.', 'danger');
        }
    } catch (error) {
        console.error('Erreur:', error);
        showAlert('Une erreur réseau est survenue.', 'danger');
    }
}

// Mettre à jour le compteur d'utilisateurs
function updateUsersCounter() {
    const usersCount = document.querySelectorAll('#usersList tr').length;
    // Mettre à jour le titre du cadre des utilisateurs
    const usersHeader = document.querySelector('.card-header h5 i.bx-users').closest('h5');
    if (usersHeader) {
        usersHeader.innerHTML = `<i class="bx bx-users text-primary me-2"></i>Utilisateurs (${usersCount})`;
    }

    // Mettre à jour la carte de statistiques "Utilisateurs"
    const userStatsCard = document.querySelector('.card-stat-users .fw-bold.text-white');
    if (userStatsCard) {
        userStatsCard.textContent = usersCount;
    }
}

// Mettre à jour le compteur de permissions
function updatePermissionsCounter() {
    const permissionsCount = document.querySelectorAll('#permissionsList .badge').length;
    // Mettre à jour le titre du cadre des permissions
    const permissionsHeader = document.querySelector('.card-header h5 i.bx-key').closest('h5');
    if (permissionsHeader) {
        permissionsHeader.innerHTML = `<i class="bx bx-key text-primary me-2"></i>Permissions (${permissionsCount})`;
    }

    // Mettre à jour la carte de statistiques "Permissions"
    const permissionStatsCard = document.querySelector('.card-stat-permissions .fw-bold.text-white');
    if (permissionStatsCard) {
        permissionStatsCard.textContent = permissionsCount;
    }
}


// Vérifier l'état vide des utilisateurs
function checkEmptyUsersState() {
    const usersList = document.getElementById('usersList');
    if (!usersList || usersList.children.length === 0) {
        const cardBody = usersList?.closest('.card-body') || document.querySelector('.card .card-body');
        if (cardBody) {
            cardBody.innerHTML = `
                <div class="text-center py-5">
                    <i class="bx bx-user-x display-4 text-muted mb-3"></i>
                    <h6 class="text-muted">Aucun utilisateur assigné</h6>
                    <p class="text-muted mb-3">Commencez par assigner des utilisateurs à ce rôle</p>
                    @can('update-role')
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#assignUserModal">
                        <i class="bx bx-plus me-1"></i>
                        Assigner un utilisateur
                    </button>
                    @endcan
                </div>
            `;
        }
    }
}

// Vérifier l'état vide des permissions
function checkEmptyPermissionsState() {
    const permissionsList = document.getElementById('permissionsList');
    if (!permissionsList || permissionsList.children.length === 0) {
        const cardBody = permissionsList?.closest('.card-body');
        if (cardBody) {
            cardBody.innerHTML = `
                <div class="text-center py-4">
                    <i class="bx bx-key display-4 text-muted mb-3"></i>
                    <h6 class="text-muted">Aucune permission assignée</h6>
                    <p class="text-muted mb-3">Ajoutez des permissions pour définir les capacités de ce rôle</p>
                    @can('update-role')
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#assignPermissionModal">
                        <i class="bx bx-plus me-1"></i>
                        Ajouter une permission
                    </button>
                    @endcan
                </div>
            `;
        }
    }
}

// Confirmation de suppression du rôle
function confirmDelete() {
    const usersCount = {{ $role->users->count() }};
    if (usersCount > 0) {
        return confirm(`Attention ! Ce rôle est assigné à ${usersCount} utilisateur(s).\n\nÊtes-vous sûr de vouloir le supprimer ?\n\nCela aura un impact sur tous les utilisateurs qui ont ce rôle.`);
    }
    return confirm('Êtes-vous sûr de vouloir supprimer ce rôle ?\n\nCette action est irréversible.');
}

// Initialisation
document.addEventListener('DOMContentLoaded', function() {
    // Configuration des modals Bootstrap
    const assignUserModal = document.getElementById('assignUserModal');
    const assignPermissionModal = document.getElementById('assignPermissionModal');

    if (assignUserModal) {
        assignUserModal.addEventListener('hidden.bs.modal', function () {
            document.getElementById('userSelect').value = '';
        });
    }

    if (assignPermissionModal) {
        assignPermissionModal.addEventListener('hidden.bs.modal', function () {
            document.getElementById('permissionSelect').value = '';
        });
    }
});
</script>
@endsection
