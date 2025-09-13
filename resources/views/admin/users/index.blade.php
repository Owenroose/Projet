@extends('admin.layouts.app')

@section('title', 'Gestion des Utilisateurs')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">
                <i class="bx bx-group text-primary me-2"></i>
                Gestion des Utilisateurs
            </h4>
            <p class="text-muted mb-0">Administrez les comptes utilisateurs, leurs rôles et permissions</p>
        </div>
        <div class="d-flex gap-2">
            @can('create-user')
            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#importModal">
                <i class="bx bx-import me-1"></i>
                Importer
            </button>
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                <i class="bx bx-plus me-1"></i>
                Nouvel Utilisateur
            </a>
            @endcan
            @can('export-user')
            <a href="{{ route('admin.users.export', request()->query()) }}" class="btn btn-outline-success">
                <i class="bx bx-download me-1"></i>
                Exporter
            </a>
            @endcan
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-3 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar flex-shrink-0 me-3">
                            <span class="avatar-initial rounded bg-label-primary"><i class="bx bx-user bx-sm"></i></span>
                        </div>
                        <div class="d-flex flex-column">
                            <h6 class="mb-0 text-muted">Total Utilisateurs</h6>
                            <h5 class="mb-0">{{ $userStats['total_users'] ?? 'N/A' }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar flex-shrink-0 me-3">
                            <span class="avatar-initial rounded bg-label-success"><i class="bx bx-check-double bx-sm"></i></span>
                        </div>
                        <div class="d-flex flex-column">
                            <h6 class="mb-0 text-muted">Utilisateurs Vérifiés</h6>
                            <h5 class="mb-0">{{ $userStats['verified_users'] ?? 'N/A' }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar flex-shrink-0 me-3">
                            <span class="avatar-initial rounded bg-label-info"><i class="bx bx-group bx-sm"></i></span>
                        </div>
                        <div class="d-flex flex-column">
                            <h6 class="mb-0 text-muted">Utilisateurs En Ligne</h6>
                            <h5 class="mb-0">{{ $userStats['online_now'] ?? 'N/A' }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar flex-shrink-0 me-3">
                            <span class="avatar-initial rounded bg-label-warning"><i class="bx bx-plus bx-sm"></i></span>
                        </div>
                        <div class="d-flex flex-column">
                            <h6 class="mb-0 text-muted">Nouveaux cette semaine</h6>
                            <h5 class="mb-0">{{ $userStats['new_this_week'] ?? 'N/A' }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header border-bottom">
            <h5 class="card-title mb-0">Recherche & Filtres</h5>
        </div>
        <div class="card-body">
            <form id="filterForm" class="row gx-3 gy-2 align-items-center" method="GET">
                <div class="col-md-5">
                    <label class="form-label" for="search-users">Rechercher</label>
                    <input type="text" id="search-users" name="search" class="form-control" placeholder="Nom ou email..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label" for="filter-role">Rôle</label>
                    <select id="filter-role" name="role" class="form-select">
                        <option value="">Tous les rôles</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>
                                {{ $role->display_name ?? $role->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label" for="filter-status">Statut</label>
                    <select id="filter-status" name="activity_status" class="form-select">
                        <option value="">Tous</option>
                        <option value="online" {{ request('activity_status') == 'online' ? 'selected' : '' }}>En ligne</option>
                        <option value="recent" {{ request('activity_status') == 'recent' ? 'selected' : '' }}>Actifs récemment</option>
                        <option value="offline" {{ request('activity_status') == 'offline' ? 'selected' : '' }}>Hors ligne</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary mt-4 w-100">
                        <i class="bx bx-filter-alt me-1"></i>
                        Filtrer
                    </button>
                    <button type="button" id="clearFilters" class="btn btn-outline-secondary mt-2 w-100">
                        <i class="bx bx-x me-1"></i>
                        Effacer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <h5 class="card-header d-flex justify-content-between align-items-center">
            Liste des Utilisateurs
            <div class="text-muted">
                <small>{{ $users->total() }} utilisateur(s) trouvé(s)</small>
            </div>
        </h5>
        <div class="card-body">
            <div class="table-responsive text-nowrap">
                <table class="table card-table">
                    <thead>
                        <tr>
                            <th>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="selectAllUsers">
                                </div>
                            </th>
                            <th>Utilisateur</th>
                            <th>Rôles</th>
                            <th>Statut</th>
                            <th>Activité</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0" id="users-table-body">
                        @forelse($users as $user)
                        <tr>
                            <td>
                                <div class="form-check">
                                    <input class="form-check-input user-checkbox" type="checkbox" value="{{ $user->id }}">
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="{{ $user->avatar_url }}" alt="Avatar" class="rounded-circle me-3" style="width: 40px; height: 40px;">
                                    <div class="d-flex flex-column">
                                        <span class="fw-medium">
                                            {{ $user->name }}
                                            <i class="bx bx-info-circle text-muted ms-1 cursor-pointer quick-info-btn" data-user-id="{{ $user->id }}" data-bs-toggle="tooltip" data-bs-html="true" title="Chargement..."></i>
                                        </span>
                                        <small class="text-muted">{{ $user->email }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @forelse($user->roles as $role)
                                <span class="badge bg-label-info me-1">{{ $role->display_name ?? $role->name }}</span>
                                @empty
                                <span class="text-muted">Aucun rôle</span>
                                @endforelse
                            </td>
                            <td>
                                <span class="badge {{ $user->is_active ? 'bg-label-success' : 'bg-label-danger' }}">
                                    {{ $user->is_active ? 'Actif' : 'Inactif' }}
                                </span>
                            </td>
                            <td>
                                @if($user->is_online)
                                <span class="badge bg-success">En ligne</span>
                                @else
                                <span class="text-muted">{{ $user->last_seen_at ? $user->last_seen_at->diffForHumans() : 'Jamais connecté' }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="javascript:void(0);"
                                           data-bs-toggle="modal"
                                           data-bs-target="#manageRolesModal"
                                           data-user-id="{{ $user->id }}"
                                           data-user-name="{{ $user->name }}">
                                            <i class="bx bx-shield-quarter me-1"></i> Gérer les rôles
                                        </a>

                                        <a class="dropdown-item" href="{{ route('admin.users.edit', $user->id) }}">
                                            <i class="bx bx-edit-alt me-1"></i> Modifier
                                        </a>

                                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="bx bx-trash me-1"></i> Supprimer
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="bx bx-search bx-lg text-muted mb-2"></i>
                                    <p class="text-muted mb-0">Aucun utilisateur trouvé avec les critères sélectionnés.</p>
                                    <button type="button" id="resetFilters" class="btn btn-outline-primary btn-sm mt-2">
                                        Réinitialiser les filtres
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($users->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $users->appends(request()->query())->links() }}
            </div>
            @endif
        </div>
    </div>

    <button id="bulkAssignRoleBtn" class="btn btn-primary mt-3 d-none"
            data-bs-toggle="modal"
            data-bs-target="#assignRoleModal">
        <i class="bx bx-shield-plus me-1"></i>
        Assigner un rôle (Sélection)
    </button>
</div>

@include('admin.users.partials.import-modal')
@include('admin.users.partials.manage-roles-modal')
@include('admin.users.partials.bulk-assign-role-modal')

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const manageRolesModal = document.getElementById('manageRolesModal');
        manageRolesModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const userId = button.getAttribute('data-user-id');
            const userName = button.getAttribute('data-user-name');

            const modalTitle = manageRolesModal.querySelector('#userNamePlaceholder');
            modalTitle.textContent = userName;

            const hiddenUserId = manageRolesModal.querySelector('#manageRolesUserId');
            hiddenUserId.value = userId;

            fetchUserRoles(userId);
        });

        // Logique pour la sélection multiple et le bouton de masse
        const selectAllCheckbox = document.getElementById('selectAllUsers');
        const userCheckboxes = document.querySelectorAll('.user-checkbox');
        const bulkAssignRoleBtn = document.getElementById('bulkAssignRoleBtn');

        function updateBulkButtonState() {
            const checkedUsers = document.querySelectorAll('.user-checkbox:checked').length;
            if (checkedUsers > 0) {
                bulkAssignRoleBtn.classList.remove('d-none');
                bulkAssignRoleBtn.innerHTML = `<i class="bx bx-shield-plus me-1"></i>Assigner un rôle (${checkedUsers} sélectionné${checkedUsers > 1 ? 's' : ''})`;
            } else {
                bulkAssignRoleBtn.classList.add('d-none');
            }
        }

        selectAllCheckbox.addEventListener('change', function() {
            userCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateBulkButtonState();
        });

        userCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                updateBulkButtonState();

                // Mettre à jour l'état du checkbox "tout sélectionner"
                const totalCheckboxes = userCheckboxes.length;
                const checkedCheckboxes = document.querySelectorAll('.user-checkbox:checked').length;

                selectAllCheckbox.checked = totalCheckboxes === checkedCheckboxes;
                selectAllCheckbox.indeterminate = checkedCheckboxes > 0 && checkedCheckboxes < totalCheckboxes;
            });
        });

        // Gestion des boutons pour effacer les filtres
        const clearFiltersBtn = document.getElementById('clearFilters');
        const resetFiltersBtn = document.getElementById('resetFilters');

        function clearAllFilters() {
            document.getElementById('search-users').value = '';
            document.getElementById('filter-role').value = '';
            document.getElementById('filter-status').value = '';
            document.getElementById('filterForm').submit();
        }

        if (clearFiltersBtn) {
            clearFiltersBtn.addEventListener('click', clearAllFilters);
        }

        if (resetFiltersBtn) {
            resetFiltersBtn.addEventListener('click', clearAllFilters);
        }

        // Logique pour les tooltips "Quick Info"
        const quickInfoButtons = document.querySelectorAll('.quick-info-btn');
        quickInfoButtons.forEach(button => {
            const userId = button.getAttribute('data-user-id');
            const tooltip = new bootstrap.Tooltip(button);

            button.addEventListener('mouseover', function() {
                if (!button.getAttribute('data-bs-original-title').includes('Chargement')) {
                    return;
                }

                fetch(`/admin/users/quick-info/${userId}`)
                    .then(response => response.json())
                    .then(data => {
                        const tooltipContent = `
                            <strong>Statut:</strong> ${data.active ? '<span class="text-success">Actif</span>' : '<span class="text-danger">Inactif</span>'}<br>
                            <strong>Vérifié:</strong> ${data.verified ? '<span class="text-success">Oui</span>' : '<span class="text-danger">Non</span>'}<br>
                            <strong>Rôles:</strong> ${data.roles.length > 0 ? data.roles.join(', ') : 'Aucun'}<br>
                            <strong>Dernière vue:</strong> ${data.last_seen}<br>
                            <strong>Créé le:</strong> ${data.created_at}
                        `;

                        button.setAttribute('data-bs-original-title', tooltipContent);
                        tooltip.dispose();
                        new bootstrap.Tooltip(button).show();
                    })
                    .catch(error => {
                        console.error('Erreur lors du chargement des infos:', error);
                        button.setAttribute('data-bs-original-title', 'Erreur de chargement.');
                        tooltip.dispose();
                        new bootstrap.Tooltip(button).show();
                    });
            });
        });

        // Debug pour les filtres
        console.log('Paramètres actuels de filtrage:', {
            search: '{{ request("search") }}',
            role: '{{ request("role") }}',
            activity_status: '{{ request("activity_status") }}'
        });
    });

    function showToast(message, type) {
        let toastContainer = document.getElementById('toastContainer');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.id = 'toastContainer';
            toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
            toastContainer.style.zIndex = '9999';
            document.body.appendChild(toastContainer);
        }

        const toastId = 'toast-' + Date.now();
        const toastClass = type === 'success' ? 'bg-success' : type === 'error' ? 'bg-danger' : 'bg-primary';

        const toastHTML = `
            <div id="${toastId}" class="toast align-items-center text-white ${toastClass} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="bx ${type === 'success' ? 'bx-check-circle' : type === 'error' ? 'bx-error-circle' : 'bx-info-circle'} me-2"></i>
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `;

        toastContainer.insertAdjacentHTML('beforeend', toastHTML);

        const toastElement = document.getElementById(toastId);
        const toast = new bootstrap.Toast(toastElement, { autohide: true, delay: 3000 });
        toast.show();

        toastElement.addEventListener('hidden.bs.toast', function() {
            toastElement.remove();
        });
    }
</script>
@endsection
