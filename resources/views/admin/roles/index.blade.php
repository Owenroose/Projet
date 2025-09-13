@extends('admin.layouts.app')

@section('title', 'Gestion des Rôles')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">
                <i class="bx bx-shield-quarter text-primary me-2"></i>
                Gestion des Rôles
            </h4>
            <p class="text-muted mb-0">Gérez les rôles et leurs permissions système</p>
        </div>
        @can('create-role')
        <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">
            <i class="bx bx-plus me-1"></i>
            Nouveau Rôle
        </a>
        @endcan
    </div>

    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 col-12 mb-3">
            <div class="card card-stat-total border-0 shadow-sm text-white h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-md rounded me-3">
                            <i class="bx bx-shield text-white"></i>
                        </div>
                        <div>
                            <p class="text-white-50 mb-0 small">Total Rôles</p>
                            <h5 class="mb-0 fw-bold">{{ $roles->count() }}</h5>
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
                            <p class="text-white-50 mb-0 small">Permissions Actives</p>
                            <h5 class="mb-0 fw-bold">{{ $roles->sum(function($role) { return $role->permissions->count(); }) }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-12 mb-3">
            <div class="card card-stat-system-roles border-0 shadow-sm text-white h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-md rounded me-3">
                            <i class="bx bx-user-check text-white"></i>
                        </div>
                        <div>
                            <p class="text-white-50 mb-0 small">Rôles Système</p>
                            <h5 class="mb-0 fw-bold">{{ $roles->where('is_system', true)->count() }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-12 mb-3">
            <div class="card card-stat-users border-0 shadow-sm text-white h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-md rounded me-3">
                            <i class="bx bx-users text-white"></i>
                        </div>
                        <div>
                            <p class="text-white-50 mb-0 small">Utilisateurs Assignés</p>
                            <h5 class="mb-0 fw-bold">{{ $roles->sum(function($role) { return $role->users->count(); }) }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body p-3">
            <div class="row g-3 align-items-center">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0">
                            <i class="bx bx-search text-muted"></i>
                        </span>
                        <input type="text" class="form-control border-start-0" placeholder="Rechercher un rôle..." id="searchRole">
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="filterPermissions">
                        <option value="">Toutes les permissions</option>
                        <option value="create">Création</option>
                        <option value="read">Lecture</option>
                        <option value="update">Modification</option>
                        <option value="delete">Suppression</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="sortBy">
                        <option value="name">Trier par nom</option>
                        <option value="created_at">Trier par date</option>
                        <option value="permissions_count">Trier par permissions</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-outline-secondary w-100" onclick="resetFilters()">
                        <i class="bx bx-refresh me-1"></i>
                        Reset
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white border-bottom">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-semibold">
                    <i class="bx bx-list-ul text-primary me-2"></i>
                    Liste des Rôles
                </h5>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-outline-secondary" onclick="exportRoles()">
                        <i class="bx bx-export me-1"></i>
                        Exporter
                    </button>
                    <div class="btn-group" role="group">
                        <input type="radio" class="btn-check" name="viewMode" id="tableView" checked>
                        <label class="btn btn-sm btn-outline-secondary" for="tableView">
                            <i class="bx bx-table"></i>
                        </label>
                        <input type="radio" class="btn-check" name="viewMode" id="cardView">
                        <label class="btn btn-sm btn-outline-secondary" for="cardView">
                            <i class="bx bx-grid-alt"></i>
                        </label>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div id="tableViewContent" class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="selectAll">
                                </div>
                            </th>
                            <th>
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bx bx-id-card text-muted"></i>
                                    ID
                                </div>
                            </th>
                            <th>
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bx bx-shield text-muted"></i>
                                    Rôle
                                </div>
                            </th>
                            <th>
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bx bx-key text-muted"></i>
                                    Permissions
                                </div>
                            </th>
                            <th>
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bx bx-users text-muted"></i>
                                    Utilisateurs
                                </div>
                            </th>
                            <th>
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bx bx-time text-muted"></i>
                                    Créé le
                                </div>
                            </th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($roles as $role)
                        <tr class="role-row" data-role-name="{{ strtolower($role->name) }}" data-permissions="{{ $role->permissions->pluck('name')->implode(',') }}">
                            <td class="ps-4">
                                <div class="form-check">
                                    <input class="form-check-input role-checkbox" type="checkbox" value="{{ $role->id }}">
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark fw-normal">#{{ $role->id }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-3 rounded-circle avatar-role">
                                        <span class="text-white fw-bold">{{ strtoupper(substr($role->name, 0, 2)) }}</span>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-semibold">{{ $role->name }}</h6>
                                        @if($role->description)
                                            <small class="text-muted">{{ Str::limit($role->description, 50) }}</small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-wrap gap-1" style="max-width: 300px;">
                                    @forelse($role->permissions->take(3) as $permission)
                                        <span class="badge badge-permission-index">
                                            <i class="bx bx-key fs-6 me-1"></i>
                                            {{ $permission->name }}
                                        </span>
                                    @empty
                                        <span class="badge bg-light text-muted">Aucune permission</span>
                                    @endforelse
                                    @if($role->permissions->count() > 3)
                                        <span class="badge bg-secondary">+{{ $role->permissions->count() - 3 }} autres</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge badge-users-count">{{ $role->users->count() ?? 0 }}</span>
                                    @if($role->users->count() > 0)
                                        <small class="text-muted">utilisateur(s)</small>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="text-muted small">
                                    <div>{{ $role->created_at->format('d/m/Y') }}</div>
                                    <div class="text-xs opacity-75">{{ $role->created_at->format('H:i') }}</div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex justify-content-center">
                                    <div class="dropdown">
                                        <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                            <i class="bx bx-dots-horizontal-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end shadow">
                                            <h6 class="dropdown-header">Actions pour {{ $role->name }}</h6>
                                            <a class="dropdown-item" href="{{ route('admin.roles.show', $role->id) }}">
                                                <i class="bx bx-show text-info me-2"></i>
                                                Voir les détails
                                            </a>
                                            @can('update-role')
                                            <a class="dropdown-item" href="{{ route('admin.roles.edit', $role->id) }}">
                                                <i class="bx bx-edit-alt text-warning me-2"></i>
                                                Modifier
                                            </a>
                                            @endcan
                                            <div class="dropdown-divider"></div>
                                            @can('delete-role')
                                            <form action="{{ route('admin.roles.destroy', $role->id) }}" method="POST" class="d-inline" onsubmit="return confirmDelete('{{ $role->name }}');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger">
                                                    <i class="bx bx-trash me-2"></i>
                                                    Supprimer
                                                </button>
                                            </form>
                                            @endcan
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="bx bx-shield-x display-4 text-muted mb-3"></i>
                                    <h5 class="text-muted">Aucun rôle trouvé</h5>
                                    <p class="text-muted mb-3">Commencez par créer votre premier rôle</p>
                                    @can('create-role')
                                    <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">
                                        <i class="bx bx-plus me-1"></i>
                                        Créer un rôle
                                    </a>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div id="cardViewContent" class="d-none p-4">
                <div class="row g-4">
                    @foreach($roles as $role)
                    <div class="col-xl-4 col-lg-6 col-md-6">
                        <div class="card h-100 border-0 shadow-sm hover-shadow-lg transition-all">
                            <div class="card-header bg-gradient-primary text-white">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-3 bg-white bg-opacity-20 rounded-circle">
                                            <span class="text-white fw-bold">{{ strtoupper(substr($role->name, 0, 2)) }}</span>
                                        </div>
                                        <h6 class="mb-0 text-white">{{ $role->name }}</h6>
                                    </div>
                                    <span class="badge bg-white text-primary">#{{ $role->id }}</span>
                                </div>
                            </div>
                            <div class="card-body">
                                @if($role->description)
                                    <p class="text-muted small mb-3">{{ $role->description }}</p>
                                @endif

                                <div class="mb-3">
                                    <small class="text-muted fw-semibold d-block mb-2">Permissions :</small>
                                    <div class="d-flex flex-wrap gap-1">
                                        @forelse($role->permissions->take(6) as $permission)
                                            <span class="badge badge-permission-index">{{ $permission->name }}</span>
                                        @empty
                                            <span class="text-muted small">Aucune permission assignée</span>
                                        @endforelse
                                        @if($role->permissions->count() > 6)
                                            <span class="badge bg-secondary">+{{ $role->permissions->count() - 6 }}</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="row text-center mb-3">
                                    <div class="col-6">
                                        <small class="text-muted d-block">Permissions</small>
                                        <strong class="text-primary">{{ $role->permissions->count() }}</strong>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted d-block">Utilisateurs</small>
                                        <strong class="text-info">{{ $role->users->count() ?? 0 }}</strong>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer bg-transparent border-top-0 pt-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        <i class="bx bx-time me-1"></i>
                                        {{ $role->created_at->format('d/m/Y') }}
                                    </small>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.roles.show', $role->id) }}" class="btn btn-outline-info" title="Voir">
                                            <i class="bx bx-show"></i>
                                        </a>
                                        @can('update-role')
                                        <a href="{{ route('admin.roles.edit', $role->id) }}" class="btn btn-outline-warning" title="Modifier">
                                            <i class="bx bx-edit-alt"></i>
                                        </a>
                                        @endcan
                                        @can('delete-role')
                                        <form action="{{ route('admin.roles.destroy', $role->id) }}" method="POST" class="d-inline" onsubmit="return confirmDelete('{{ $role->name }}');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="Supprimer">
                                                <i class="bx bx-trash"></i>
                                            </button>
                                        </form>
                                        @endcan
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mt-4 d-none" id="bulkActions">
        <div class="card-body p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <i class="bx bx-check-circle text-success me-2"></i>
                    <span class="fw-semibold">Actions en lot :</span>
                    <span class="text-muted ms-2" id="selectedCount">0 élément(s) sélectionné(s)</span>
                </div>
                <div class="btn-group">
                    <button class="btn btn-sm btn-outline-warning" onclick="bulkEdit()">
                        <i class="bx bx-edit me-1"></i>
                        Modifier
                    </button>
                    @can('delete-role')
                    <button class="btn btn-sm btn-outline-danger" onclick="bulkDelete()">
                        <i class="bx bx-trash me-1"></i>
                        Supprimer
                    </button>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Palette de couleurs */
:root {
    --color-primary-dark: #3F51B5; /* Bleu Indigo plus prononcé */
    --color-secondary: #009688; /* Vert Teal pour un accent frais */
    --color-tertiary: #FFC107; /* Ambre pour un accent chaleureux */
    --color-gray-dark: #607D8B; /* Bleu-gris pour les éléments neutres */
    --color-gray-light: #ECEFF1; /* Gris très clair pour le fond global */
    --color-permission-bg-index: #E0F7FA; /* Bleu très clair pour le fond des badges de permission */
    --color-permission-text-index: #00838F; /* Cyan plus foncé pour le texte des badges de permission */
}

body {
    background-color: var(--color-gray-light);
}

.card {
    border-radius: 12px;
    border: none;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
}

/* Styles pour les cartes de statistiques */
.card-stat-total { background-color: var(--color-primary-dark); }
.card-stat-permissions { background-color: var(--color-secondary); }
.card-stat-system-roles { background-color: var(--color-tertiary); }
.card-stat-users { background-color: var(--color-gray-dark); }

.card-stat-total .avatar { background-color: rgba(255, 255, 255, 0.25); }
.card-stat-permissions .avatar { background-color: rgba(255, 255, 255, 0.25); }
.card-stat-system-roles .avatar { background-color: rgba(255, 255, 255, 0.25); }
.card-stat-users .avatar { background-color: rgba(255, 255, 255, 0.25); }


/* Styles pour les badges de permission dans le tableau/cartes */
.badge.badge-permission-index {
    background-color: var(--color-permission-bg-index);
    color: var(--color-permission-text-index);
    border: 1px solid rgba(0, 131, 143, 0.2);
    font-size: 0.8em;
    padding: 0.5em 0.75em;
    border-radius: 16px;
    font-weight: normal;
}

.badge.badge-users-count {
    background-color: rgba(0, 188, 212, 0.1); /* Utilisation d'une couleur plus subtile */
    color: #00bcd4;
    font-weight: 500;
    border-radius: 16px;
    padding: 0.4em 0.8em;
}

/* Styles pour l'avatar des rôles dans le tableau */
.avatar.avatar-role {
    background: linear-gradient(135deg, #3F51B5 0%, #673AB7 100%); /* Nouveau dégradé plus doux */
}

.hover-shadow-lg:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

.transition-all {
    transition: all 0.3s ease;
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.role-row:hover {
    background-color: rgba(var(--bs-primary-rgb), 0.05);
}

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
</style>

<script>
// Recherche en temps réel
document.getElementById('searchRole').addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const rows = document.querySelectorAll('.role-row');

    rows.forEach(row => {
        const roleName = row.dataset.roleName;
        if (roleName.includes(searchTerm)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});

// Filtrage par permissions
document.getElementById('filterPermissions').addEventListener('change', function() {
    const filterValue = this.value.toLowerCase();
    const rows = document.querySelectorAll('.role-row');

    rows.forEach(row => {
        const permissions = row.dataset.permissions.toLowerCase();
        if (!filterValue || permissions.includes(filterValue)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});

// Basculer entre vue tableau et cartes
document.querySelectorAll('input[name="viewMode"]').forEach(radio => {
    radio.addEventListener('change', function() {
        const tableView = document.getElementById('tableViewContent');
        const cardView = document.getElementById('cardViewContent');

        if (this.id === 'tableView') {
            tableView.classList.remove('d-none');
            cardView.classList.add('d-none');
        } else {
            tableView.classList.add('d-none');
            cardView.classList.remove('d-none');
        }
    });
});

// Sélection multiple
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.role-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
    updateBulkActions();
});

document.querySelectorAll('.role-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', updateBulkActions);
});

function updateBulkActions() {
    const selectedCheckboxes = document.querySelectorAll('.role-checkbox:checked');
    const bulkActions = document.getElementById('bulkActions');
    const selectedCount = document.getElementById('selectedCount');

    if (selectedCheckboxes.length > 0) {
        bulkActions.classList.remove('d-none');
        selectedCount.textContent = `${selectedCheckboxes.length} élément(s) sélectionné(s)`;
    } else {
        bulkActions.classList.add('d-none');
    }
}

// Confirmation de suppression
function confirmDelete(roleName) {
    return confirm(`Êtes-vous sûr de vouloir supprimer le rôle "${roleName}" ?\n\nCette action est irréversible et peut affecter les utilisateurs qui ont ce rôle.`);
}

// Reset des filtres
function resetFilters() {
    document.getElementById('searchRole').value = '';
    document.getElementById('filterPermissions').value = '';
    document.getElementById('sortBy').value = 'name';

    // Afficher toutes les lignes
    document.querySelectorAll('.role-row').forEach(row => {
        row.style.display = '';
    });
}

// Export des rôles (placeholder)
function exportRoles() {
    alert('Fonctionnalité d\'export en développement...');
}

// Actions en lot (placeholder)
function bulkEdit() {
    const selected = document.querySelectorAll('.role-checkbox:checked');
    alert(`Modification en lot de ${selected.length} rôle(s) - Fonctionnalité en développement`);
}

function bulkDelete() {
    const selected = document.querySelectorAll('.role-checkbox:checked');
    if (confirm(`Êtes-vous sûr de vouloir supprimer ${selected.length} rôle(s) sélectionné(s) ?`)) {
        alert('Suppression en lot - Fonctionnalité en développement');
    }
}
</script>
@endsection
