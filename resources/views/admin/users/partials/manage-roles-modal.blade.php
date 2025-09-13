<!-- Modal pour gérer les rôles d'un utilisateur -->
<div class="modal fade" id="manageRolesModal" tabindex="-1" aria-labelledby="manageRolesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="manageRolesModalLabel">
                    <i class="bx bx-shield-quarter text-primary me-2"></i>
                    Gérer les rôles pour <span id="userNamePlaceholder" class="text-primary"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="manageRolesUserId">

                <!-- Section Assigner un nouveau rôle -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light border-0">
                        <h6 class="card-title mb-0">
                            <i class="bx bx-plus-circle text-success me-2"></i>
                            Assigner un nouveau rôle
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row align-items-end">
                            <div class="col-md-8">
                                <label for="assignRoleSelect" class="form-label">Sélectionner un rôle</label>
                                <select id="assignRoleSelect" class="form-select">
                                    <option value="">-- Choisir un rôle --</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->name }}" data-description="{{ $role->description ?? '' }}">
                                            {{ $role->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-text">
                                    <small id="roleDescription" class="text-muted"></small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <button type="button" class="btn btn-success w-100" id="assignRoleBtn" disabled>
                                    <i class="bx bx-plus me-1"></i>
                                    Assigner
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section Rôles actuels -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light border-0 d-flex justify-content-between align-items-center">
                        <h6 class="card-title mb-0">
                            <i class="bx bx-list-ul text-info me-2"></i>
                            Rôles actuels
                        </h6>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="fetchUserRoles(document.getElementById('manageRolesUserId').value)">
                            <i class="bx bx-refresh"></i>
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <div id="userRolesContainer">
                            <ul id="userRolesList" class="list-group list-group-flush">
                                <!-- Les rôles seront chargés dynamiquement ici -->
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Informations sur les permissions -->
                <div class="mt-3">
                    <div class="alert alert-info" role="alert">
                        <i class="bx bx-info-circle me-2"></i>
                        <strong>À savoir :</strong>
                        <ul class="mb-0 mt-2">
                            <li>Un utilisateur peut avoir plusieurs rôles</li>
                            <li>Les permissions sont cumulatives</li>
                            <li>Seuls les super-administrateurs peuvent modifier les rôles d'autres super-administrateurs</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="bx bx-x me-1"></i>
                    Fermer
                </button>
                <button type="button" class="btn btn-primary" onclick="window.location.reload()">
                    <i class="bx bx-refresh me-1"></i>
                    Actualiser la page
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const assignRoleSelect = document.getElementById('assignRoleSelect');
    const roleDescription = document.getElementById('roleDescription');
    const assignRoleBtn = document.getElementById('assignRoleBtn');

    // Afficher la description du rôle sélectionné
    assignRoleSelect?.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const description = selectedOption.dataset.description;

        if (roleDescription) {
            roleDescription.textContent = description || '';
        }

        // Activer/désactiver le bouton d'assignation
        if (assignRoleBtn) {
            assignRoleBtn.disabled = !this.value;
        }
    });

    // Gérer l'assignation d'un rôle
    assignRoleBtn?.addEventListener('click', function() {
        const userId = document.getElementById('manageRolesUserId').value;
        const roleName = assignRoleSelect.value;

        if (!roleName) {
            showAlert('Veuillez sélectionner un rôle à assigner.', 'warning');
            return;
        }

        // Désactiver le bouton pendant l'opération
        this.disabled = true;
        const originalText = this.innerHTML;
        this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Attribution...';

        // Utiliser fetch directement avec la bonne URL
        fetch(`/admin/users/${userId}/assign-role`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                role: roleName
            })
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => Promise.reject(err));
            }
            return response.json();
        })
        .then((data) => {
            // Réinitialiser le formulaire
            assignRoleSelect.value = '';
            roleDescription.textContent = '';

            // Recharger les rôles
            fetchUserRoles(userId);

            showAlert(data.message || 'Rôle assigné avec succès', 'success');
        })
        .catch((error) => {
            console.error('Erreur:', error);
            showAlert('Erreur lors de l\'assignation du rôle: ' + (error.message || 'Erreur inconnue'), 'error');
        })
        .finally(() => {
            // Rétablir le bouton
            this.disabled = false;
            this.innerHTML = originalText;
        });
    });
});

// Fonction pour afficher les alertes dans le modal
function showAlert(message, type = 'info') {
    const alertContainer = document.createElement('div');
    const alertClass = type === 'success' ? 'alert-success' :
                      type === 'error' ? 'alert-danger' :
                      type === 'warning' ? 'alert-warning' : 'alert-info';

    alertContainer.innerHTML = `
        <div class="alert ${alertClass} alert-dismissible fade show mb-3" role="alert">
            <i class="bx ${type === 'success' ? 'bx-check-circle' :
                          type === 'error' ? 'bx-error-circle' :
                          type === 'warning' ? 'bx-info-circle' : 'bx-info-circle'} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;

    const modalBody = document.querySelector('#manageRolesModal .modal-body');
    modalBody.insertBefore(alertContainer.firstElementChild, modalBody.firstElementChild);

    // Auto-remove après 5 secondes
    setTimeout(() => {
        const alert = modalBody.querySelector('.alert');
        if (alert) {
            alert.remove();
        }
    }, 5000);
}

// Fonction globale pour supprimer un rôle
window.removeRole = function(userId, roleName, roleElement) {
    if (!confirm(`Êtes-vous sûr de vouloir retirer le rôle "${roleName}" de cet utilisateur ?`)) {
        return;
    }

    // Animation de chargement sur l'élément
    const removeBtn = roleElement.querySelector('.btn-remove-role');
    if (removeBtn) {
        removeBtn.disabled = true;
        removeBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
    }

    // Utiliser fetch directement avec la bonne URL
    fetch(`/admin/users/${userId}/remove-role`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            role: roleName
        })
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => Promise.reject(err));
        }
        return response.json();
    })
    .then((data) => {
        // Animation de suppression
        roleElement.style.transition = 'all 0.3s ease';
        roleElement.style.opacity = '0';
        roleElement.style.transform = 'translateX(-100%)';

        setTimeout(() => {
            roleElement.remove();

            // Vérifier s'il reste des rôles
            const rolesList = document.getElementById('userRolesList');
            if (rolesList.children.length === 0) {
                rolesList.innerHTML = `
                    <li class="list-group-item text-center text-muted py-4">
                        <i class="bx bx-info-circle bx-lg mb-2"></i>
                        <div>Aucun rôle assigné à cet utilisateur</div>
                    </li>
                `;
            }
        }, 300);

        showAlert(data.message || 'Rôle retiré avec succès', 'success');
    })
    .catch((error) => {
        console.error('Erreur:', error);
        showAlert('Erreur lors du retrait du rôle: ' + (error.message || 'Erreur inconnue'), 'error');

        // Restaurer le bouton en cas d'erreur
        if (removeBtn) {
            removeBtn.disabled = false;
            removeBtn.innerHTML = '<i class="bx bx-trash"></i>';
        }
    });
};

// Amélioration de la fonction fetchUserRoles
window.fetchUserRoles = function(userId) {
    const rolesList = document.getElementById('userRolesList');

    // Afficher un indicateur de chargement
    rolesList.innerHTML = `
        <li class="list-group-item text-center py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Chargement...</span>
            </div>
            <div class="mt-2">Chargement des rôles...</div>
        </li>
    `;

    fetch(`/admin/users/${userId}/roles`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Erreur réseau: ' + response.status);
            }
            return response.json();
        })
        .then(roles => {
            rolesList.innerHTML = '';

            if (roles.length > 0) {
                roles.forEach(role => {
                    const listItem = document.createElement('li');
                    listItem.className = 'list-group-item';
                    listItem.innerHTML = `
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <span class="badge bg-primary fs-6">${role.name}</span>
                                </div>
                                <div>
                                    <div class="fw-medium">${role.display_name || role.name}</div>
                                    ${role.description ? `<small class="text-muted">${role.description}</small>` : ''}
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-danger btn-remove-role"
                                    onclick="removeRole(${userId}, '${role.name}', this.closest('li'))"
                                    data-bs-toggle="tooltip"
                                    data-bs-placement="top"
                                    title="Retirer ce rôle">
                                <i class="bx bx-trash"></i>
                            </button>
                        </div>
                    `;
                    rolesList.appendChild(listItem);
                });

                // Réinitialiser les tooltips
                const tooltips = rolesList.querySelectorAll('[data-bs-toggle="tooltip"]');
                tooltips.forEach(tooltip => new bootstrap.Tooltip(tooltip));

            } else {
                rolesList.innerHTML = `
                    <li class="list-group-item text-center text-muted py-4">
                        <i class="bx bx-info-circle bx-lg mb-2"></i>
                        <div>Aucun rôle assigné à cet utilisateur</div>
                        <small class="text-muted">Utilisez le formulaire ci-dessus pour assigner un rôle</small>
                    </li>
                `;
            }
        })
        .catch(error => {
            console.error('Erreur lors de la récupération des rôles:', error);
            rolesList.innerHTML = `
                <li class="list-group-item text-center text-danger py-4">
                    <i class="bx bx-error bx-lg mb-2"></i>
                    <div>Impossible de charger les rôles</div>
                    <small class="text-muted">Erreur: ${error.message}</small>
                    <div class="mt-2">
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="fetchUserRoles(${userId})">
                            <i class="bx bx-refresh me-1"></i>Réessayer
                        </button>
                    </div>
                </li>
            `;
        });
};
</script>
