<!-- Modal pour assigner un rôle à plusieurs utilisateurs -->
<div class="modal fade" id="assignRoleModal" tabindex="-1" aria-labelledby="assignRoleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="assignRoleModalLabel">
                    <i class="bx bx-shield-plus text-primary me-2"></i>
                    Assigner un rôle à plusieurs utilisateurs
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Résumé de la sélection -->
                <div class="alert alert-info" role="alert">
                    <i class="bx bx-info-circle me-2"></i>
                    <span id="bulkSelectionSummary">Aucun utilisateur sélectionné</span>
                </div>

                <!-- Sélection du rôle -->
                <div class="mb-4">
                    <label for="bulkAssignRoleSelect" class="form-label">
                        <i class="bx bx-shield me-1"></i>
                        Sélectionner un rôle
                    </label>
                    <select id="bulkAssignRoleSelect" class="form-select" name="role_name">
                        <option value="">-- Choisir un rôle --</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->name }}"
                                    data-description="{{ $role->description ?? '' }}"
                                    data-permissions="{{ $role->permissions_count ?? 0 }}">
                                {{ $role->name }}
                                @if($role->permissions_count ?? 0)
                                    ({{ $role->permissions_count }} permission(s))
                                @endif
                            </option>
                        @endforeach
                    </select>
                    <div class="form-text">
                        <small id="bulkRoleDescription" class="text-muted"></small>
                    </div>
                </div>

                <!-- Options avancées -->
                <div class="border rounded p-3 mb-3">
                    <h6 class="mb-3">
                        <i class="bx bx-cog me-1"></i>
                        Options
                    </h6>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="replaceExistingRoles" name="replace_existing_roles" value="1">
                        <label class="form-check-label" for="replaceExistingRoles">
                            Remplacer les rôles existants
                        </label>
                        <small class="form-text text-muted d-block">
                            Si coché, les rôles actuels seront supprimés et remplacés par le nouveau rôle
                        </small>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="sendNotification" name="send_notification" value="1">
                        <label class="form-check-label" for="sendNotification">
                            Envoyer une notification par email
                        </label>
                        <small class="form-text text-muted d-block">
                            Informer les utilisateurs de l'attribution du nouveau rôle
                        </small>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="skipSuperAdmins" name="skip_super_admins" value="1" checked>
                        <label class="form-check-label" for="skipSuperAdmins">
                            Ignorer les super-administrateurs
                        </label>
                        <small class="form-text text-muted d-block">
                            Les super-administrateurs ne seront pas affectés par cette action
                        </small>
                    </div>
                </div>

                <!-- Prévisualisation des utilisateurs sélectionnés -->
                <div class="mb-3">
                    <h6 class="mb-2">
                        <i class="bx bx-users me-1"></i>
                        Utilisateurs concernés
                    </h6>
                    <div class="border rounded p-3 bg-light" style="max-height: 200px; overflow-y: auto;">
                        <div id="selectedUsersList">
                            <small class="text-muted">Aucun utilisateur sélectionné</small>
                        </div>
                    </div>
                </div>

                <!-- Avertissement -->
                <div class="alert alert-warning" role="alert">
                    <i class="bx bx-error-circle me-2"></i>
                    <strong>Attention :</strong> Cette action affectera tous les utilisateurs sélectionnés.
                    Assurez-vous d'avoir sélectionné les bons utilisateurs et le bon rôle.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="bx bx-x me-1"></i>
                    Annuler
                </button>
                <button type="button" class="btn btn-primary" id="bulkAssignRoleBtn" disabled>
                    <span class="spinner-border spinner-border-sm d-none me-2" role="status" aria-hidden="true"></span>
                    <i class="bx bx-check me-1"></i>
                    Assigner le rôle
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const bulkAssignRoleSelect = document.getElementById('bulkAssignRoleSelect');
    const bulkRoleDescription = document.getElementById('bulkRoleDescription');
    const bulkAssignRoleBtn = document.getElementById('bulkAssignRoleBtn');
    const selectedUsersList = document.getElementById('selectedUsersList');
    const bulkSelectionSummary = document.getElementById('bulkSelectionSummary');

    // Mise à jour de la description du rôle
    bulkAssignRoleSelect?.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const description = selectedOption.dataset.description;
        const permissions = selectedOption.dataset.permissions;

        if (bulkRoleDescription) {
            let descText = description || 'Aucune description disponible';
            if (permissions && permissions > 0) {
                descText += ` • ${permissions} permission(s)`;
            }
            bulkRoleDescription.textContent = descText;
        }

        updateBulkAssignButton();
    });

    // Mise à jour du bouton d'assignation
    function updateBulkAssignButton() {
        const hasRole = bulkAssignRoleSelect?.value;
        const hasUsers = document.querySelectorAll('.user-checkbox:checked').length > 0;

        if (bulkAssignRoleBtn) {
            bulkAssignRoleBtn.disabled = !hasRole || !hasUsers;
        }
    }

    // Observer les changements de sélection d'utilisateurs
    function updateSelectedUsers() {
        const selectedCheckboxes = document.querySelectorAll('.user-checkbox:checked');
        const count = selectedCheckboxes.length;

        // Mettre à jour le résumé
        if (bulkSelectionSummary) {
            if (count === 0) {
                bulkSelectionSummary.textContent = 'Aucun utilisateur sélectionné';
                bulkSelectionSummary.className = 'text-muted';
            } else {
                bulkSelectionSummary.textContent = `${count} utilisateur(s) sélectionné(s)`;
                bulkSelectionSummary.className = 'text-primary fw-medium';
            }
        }

        // Mettre à jour la liste détaillée
        if (selectedUsersList) {
            if (count === 0) {
                selectedUsersList.innerHTML = '<small class="text-muted">Aucun utilisateur sélectionné</small>';
            } else {
                const userElements = Array.from(selectedCheckboxes).map(checkbox => {
                    const row = checkbox.closest('tr');
                    const nameElement = row.querySelector('.fw-bold');
                    const emailElement = row.querySelector('td:nth-child(3)');
                    const name = nameElement ? nameElement.textContent.trim() : 'Nom inconnu';
                    const email = emailElement ? emailElement.textContent.trim() : 'Email inconnu';

                    return `
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar avatar-xs me-2">
                                <span class="avatar-initial rounded-circle bg-label-primary" style="font-size: 10px;">
                                    ${name.substring(0, 2).toUpperCase()}
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-medium" style="font-size: 0.875rem;">${name}</div>
                                <small class="text-muted">${email}</small>
                            </div>
                        </div>
                    `;
                }).join('');

                selectedUsersList.innerHTML = userElements;
            }
        }

        updateBulkAssignButton();
    }

    // Écouter les changements sur les checkboxes (délégation d'événements)
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('user-checkbox') || e.target.id === 'checkAll') {
            updateSelectedUsers();
        }
    });

    // Gestionnaire du bouton d'assignation
    bulkAssignRoleBtn?.addEventListener('click', function() {
        const roleName = bulkAssignRoleSelect.value;
        const selectedUsers = Array.from(document.querySelectorAll('.user-checkbox:checked')).map(cb => cb.value);
        const replaceExisting = document.getElementById('replaceExistingRoles')?.checked || false;
        const sendNotification = document.getElementById('sendNotification')?.checked || false;
        const skipSuperAdmins = document.getElementById('skipSuperAdmins')?.checked || false;

        if (!roleName) {
            alert('Veuillez sélectionner un rôle à assigner.');
            return;
        }

        if (selectedUsers.length === 0) {
            alert('Veuillez sélectionner au moins un utilisateur.');
            return;
        }

        // Confirmer l'action
        let confirmMessage = `Êtes-vous sûr de vouloir assigner le rôle "${roleName}" à ${selectedUsers.length} utilisateur(s) ?`;
        if (replaceExisting) {
            confirmMessage += '\n\nAttention : Les rôles existants seront remplacés par ce nouveau rôle.';
        }

        if (!confirm(confirmMessage)) {
            return;
        }

        // Désactiver le bouton et afficher le spinner
        const spinner = this.querySelector('.spinner-border');
        const icon = this.querySelector('.bx-check');

        this.disabled = true;
        spinner?.classList.remove('d-none');
        icon?.classList.add('d-none');
        this.innerHTML = this.innerHTML.replace('Assigner le rôle', 'Attribution en cours...');

        // Préparer les données
        const actionData = {
            action: replaceExisting ? 'replace-role' : 'assign-role',
            user_ids: selectedUsers,
            role_name: roleName,
            options: {
                send_notification: sendNotification,
                skip_super_admins: skipSuperAdmins
            }
        };

        // Effectuer la requête
        fetch('{{ route('admin.users.bulk-actions') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(actionData)
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`Erreur HTTP: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Fermer le modal
                bootstrap.Modal.getInstance(document.getElementById('assignRoleModal')).hide();

                // Afficher le message de succès
                showToast(data.message, 'success');

                // Recharger la page après un court délai
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                throw new Error(data.message || 'Erreur inconnue');
            }
        })
        .catch(error => {
            console.error('Erreur lors de l\'assignation du rôle:', error);
            alert('Erreur: ' + error.message);
        })
        .finally(() => {
            // Rétablir le bouton
            this.disabled = false;
            spinner?.classList.add('d-none');
            icon?.classList.remove('d-none');
            this.innerHTML = this.innerHTML.replace('Attribution en cours...', 'Assigner le rôle');
        });
    });

    // Initialiser l'affichage au chargement du modal
    document.getElementById('assignRoleModal')?.addEventListener('shown.bs.modal', function() {
        updateSelectedUsers();
    });
});
</script>
