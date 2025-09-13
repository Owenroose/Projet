
class UserManagement {
    constructor() {
        this.init();
        this.bindEvents();
        this.loadingStates = new Map();
    }

    init() {
        // Initialiser les tooltips Bootstrap
        this.initTooltips();

        // Initialiser les modals
        this.initModals();

        // Initialiser les animations
        this.initAnimations();

        console.log('UserManagement initialized');
    }

    initTooltips() {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl, {
                delay: { show: 500, hide: 100 }
            });
        });
    }

    initModals() {
        // Auto-focus sur les premiers inputs des modals
        document.addEventListener('shown.bs.modal', function (e) {
            const firstInput = e.target.querySelector('input:not([type="hidden"]):not([readonly])');
            if (firstInput) {
                setTimeout(() => firstInput.focus(), 100);
            }
        });
    }

    initAnimations() {
        // Observer d'intersection pour les animations au scroll
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-fade-in-scale');
                }
            });
        }, { threshold: 0.1 });

        // Observer tous les éléments avec la classe .stat-card
        document.querySelectorAll('.stat-card').forEach(card => {
            observer.observe(card);
        });
    }

    bindEvents() {
        // Recherche en temps réel avec debounce
        this.setupSearch();

        // Gestion des sélections multiples
        this.setupBulkSelection();

        // Gestion des modals
        this.setupModals();

        // Gestion du drag & drop pour l'import
        this.setupDragDrop();
    }

    setupSearch() {
        const searchInput = document.querySelector('input[name="search"]');
        if (!searchInput) return;

        let searchTimeout;
        searchInput.addEventListener('input', (e) => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                this.performSearch(e.target.value);
            }, 500);
        });
    }

    performSearch(query) {
        if (query.length < 2) return;

        const form = document.getElementById('filterForm');
        if (form) {
            // Marquer comme recherche automatique pour éviter la soumission complète
            const searchInput = form.querySelector('input[name="search"]');
            searchInput.value = query;

            // Optionnel: recherche AJAX en temps réel
            this.liveSearch(query);
        }
    }

    async liveSearch(query) {
        try {
            const url = new URL(window.location.href);
            url.searchParams.set('search', query);
            url.searchParams.set('ajax', '1');

            const response = await fetch(url);
            if (response.ok) {
                const data = await response.text();
                // Mettre à jour uniquement le contenu du tableau
                this.updateTableContent(data);
            }
        } catch (error) {
            console.error('Erreur lors de la recherche:', error);
        }
    }

    updateTableContent(html) {
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        const newTableBody = doc.querySelector('tbody');
        const currentTableBody = document.querySelector('tbody');

        if (newTableBody && currentTableBody) {
            // Animation de transition
            currentTableBody.style.opacity = '0.5';
            setTimeout(() => {
                currentTableBody.innerHTML = newTableBody.innerHTML;
                currentTableBody.style.opacity = '1';
                this.initTooltips(); // Réinitialiser les tooltips
            }, 200);
        }
    }

    setupBulkSelection() {
        const checkAll = document.getElementById('checkAll');
        const userCheckboxes = document.querySelectorAll('.user-checkbox');

        if (checkAll) {
            checkAll.addEventListener('change', (e) => {
                userCheckboxes.forEach(cb => {
                    cb.checked = e.target.checked;
                    this.animateCheckbox(cb, e.target.checked);
                });
                this.updateBulkActions();
            });
        }

        userCheckboxes.forEach(cb => {
            cb.addEventListener('change', () => {
                this.updateBulkActions();
                this.updateCheckAllState();
                this.animateCheckbox(cb, cb.checked);
            });
        });
    }

    animateCheckbox(checkbox, checked) {
        const row = checkbox.closest('tr');
        if (checked) {
            row.classList.add('table-active');
            row.style.transform = 'scale(1.02)';
            setTimeout(() => row.style.transform = '', 150);
        } else {
            row.classList.remove('table-active');
        }
    }

    updateBulkActions() {
        const selectedCheckboxes = document.querySelectorAll('.user-checkbox:checked');
        const bulkActions = document.getElementById('bulkActions');
        const selectedCount = document.getElementById('selectedCount');

        if (selectedCheckboxes.length > 0) {
            if (bulkActions.classList.contains('d-none')) {
                bulkActions.classList.remove('d-none');
                bulkActions.classList.add('animate-slide-in-up');
            }
            if (selectedCount) {
                selectedCount.textContent = `${selectedCheckboxes.length} utilisateur(s) sélectionné(s)`;
            }
        } else {
            bulkActions.classList.add('d-none');
        }
    }

    updateCheckAllState() {
        const checkAll = document.getElementById('checkAll');
        const userCheckboxes = document.querySelectorAll('.user-checkbox');
        const checkedBoxes = document.querySelectorAll('.user-checkbox:checked');

        if (!checkAll) return;

        if (checkedBoxes.length === 0) {
            checkAll.checked = false;
            checkAll.indeterminate = false;
        } else if (checkedBoxes.length === userCheckboxes.length) {
            checkAll.checked = true;
            checkAll.indeterminate = false;
        } else {
            checkAll.checked = false;
            checkAll.indeterminate = true;
        }
    }

    setupModals() {
        // Gestion du modal de gestion des rôles
        window.manageRoles = (userId, userName) => {
            const modal = new bootstrap.Modal(document.getElementById('manageRolesModal'));
            document.getElementById('manageRolesUserId').value = userId;
            document.getElementById('userNamePlaceholder').textContent = userName;

            this.loadUserRoles(userId);
            modal.show();
        };

        // Gestion de la suppression de rôles
        window.removeRole = async (userId, roleName, element) => {
            if (!confirm(`Êtes-vous sûr de vouloir retirer le rôle "${roleName}" ?`)) return;

            this.setLoadingState(element, true);

            try {
                const success = await this.performBulkAction('remove-role', [userId], roleName);
                if (success) {
                    this.animateRemoval(element);
                    this.showToast('Rôle retiré avec succès', 'success');
                    setTimeout(() => this.loadUserRoles(userId), 300);
                }
            } catch (error) {
                this.showToast('Erreur: ' + error.message, 'error');
            } finally {
                this.setLoadingState(element, false);
            }
        };
    }

    async loadUserRoles(userId) {
        const rolesList = document.getElementById('userRolesList');
        if (!rolesList) return;

        rolesList.innerHTML = `
            <li class="list-group-item text-center py-4">
                <div class="loading-spinner mx-auto mb-2"></div>
                <div>Chargement des rôles...</div>
            </li>
        `;

        try {
            const response = await fetch(`/admin/users/${userId}/roles`);
            if (!response.ok) throw new Error('Erreur réseau: ' + response.status);

            const roles = await response.json();
            this.renderRolesList(roles, userId);
        } catch (error) {
            console.error('Erreur lors du chargement des rôles:', error);
            rolesList.innerHTML = `
                <li class="list-group-item text-center text-danger py-4">
                    <i class="bx bx-error bx-lg mb-2"></i>
                    <div>Impossible de charger les rôles</div>
                    <small class="text-muted">Erreur: ${error.message}</small>
                    <div class="mt-2">
                        <button class="btn btn-sm btn-outline-primary" onclick="userManagement.loadUserRoles(${userId})">
                            <i class="bx bx-refresh me-1"></i>Réessayer
                        </button>
                    </div>
                </li>
            `;
        }
    }

    renderRolesList(roles, userId) {
        const rolesList = document.getElementById('userRolesList');
        if (!rolesList) return;

        if (roles.length === 0) {
            rolesList.innerHTML = `
                <li class="list-group-item text-center text-muted py-4">
                    <i class="bx bx-info-circle bx-lg mb-2"></i>
                    <div>Aucun rôle assigné</div>
                    <small>Utilisez le formulaire ci-dessus pour assigner un rôle</small>
                </li>
            `;
            return;
        }

        rolesList.innerHTML = roles.map(role => `
            <li class="list-group-item animate-fade-in-scale">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <span class="badge bg-primary fs-6 role-badge">${role.name}</span>
                        </div>
                        <div>
                            <div class="fw-medium">${role.display_name || role.name}</div>
                            ${role.description ? `<small class="text-muted">${role.description}</small>` : ''}
                        </div>
                    </div>
                    <button type="button"
                            class="btn btn-sm btn-outline-danger btn-enhanced"
                            onclick="removeRole(${userId}, '${role.name}', this.closest('li'))"
                            data-bs-toggle="tooltip"
                            title="Retirer ce rôle">
                        <i class="bx bx-trash"></i>
                    </button>
                </div>
            </li>
        `).join('');

        // Réinitialiser les tooltips
        this.initTooltips();
    }

    setupDragDrop() {
        const csvFileInput = document.getElementById('csvFile');
        const dropZone = csvFileInput?.closest('.modal-body');

        if (!dropZone) return;

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, this.preventDefaults, false);
        });

        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, () => dropZone.classList.add('drag-over'), false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, () => dropZone.classList.remove('drag-over'), false);
        });

        dropZone.addEventListener('drop', (e) => {
            const files = e.dataTransfer.files;
            if (files.length > 0 && csvFileInput) {
                csvFileInput.files = files;
                this.showToast('Fichier ajouté avec succès', 'success');
            }
        });
    }

    preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    async performBulkAction(action, userIds, roleName = null) {
        const data = { action, user_ids: userIds };
        if (roleName) data.role_name = roleName;

        const response = await fetch('/admin/users/bulk-actions', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(data)
        });

        if (!response.ok) {
            throw new Error(`Erreur HTTP: ${response.status}`);
        }

        const result = await response.json();
        if (!result.success) {
            throw new Error(result.message || 'Erreur inconnue');
        }

        return result;
    }

    setLoadingState(element, loading) {
        if (loading) {
            element.disabled = true;
            element.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
            this.loadingStates.set(element, element.innerHTML);
        } else {
            element.disabled = false;
            const originalContent = this.loadingStates.get(element) || '<i class="bx bx-trash"></i>';
            element.innerHTML = originalContent;
            this.loadingStates.delete(element);
        }
    }

    animateRemoval(element) {
        element.style.transition = 'all 0.3s ease';
        element.style.opacity = '0';
        element.style.transform = 'translateX(-100%)';

        setTimeout(() => element.remove(), 300);
    }

    showToast(message, type = 'success') {
        let toastContainer = document.getElementById('toastContainer');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.id = 'toastContainer';
            toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
            toastContainer.style.zIndex = '9999';
            document.body.appendChild(toastContainer);
        }

        const toastId = 'toast-' + Date.now();
        const toastClass = `toast-${type}`;
        const iconClass = {
            success: 'bx-check-circle',
            error: 'bx-error-circle',
            warning: 'bx-info-circle',
            info: 'bx-info-circle'
        }[type] || 'bx-info-circle';

        const toastHTML = `
            <div id="${toastId}" class="toast align-items-center text-dark bg-white border-0 ${toastClass}" role="alert">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="bx ${iconClass} me-2"></i>
                        ${message}
                    </div>
                    <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `;

        toastContainer.insertAdjacentHTML('beforeend', toastHTML);

        const toastElement = document.getElementById(toastId);
        const toast = new bootstrap.Toast(toastElement, {
            autohide: true,
            delay: type === 'error' ? 5000 : 3000
        });

        toast.show();

        toastElement.addEventListener('hidden.bs.toast', () => toastElement.remove());
    }

    // Méthode utilitaire pour valider les emails
    validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    // Méthode pour formater les nombres
    formatNumber(num) {
        return new Intl.NumberFormat('fr-FR').format(num);
    }

    // Méthode pour débouncer les fonctions
    debounce(func, wait, immediate) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                timeout = null;
                if (!immediate) func(...args);
            };
            const callNow = immediate && !timeout;
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
            if (callNow) func(...args);
        };
    }
}

// Initialiser la classe au chargement du DOM
document.addEventListener('DOMContentLoaded', function() {
    window.userManagement = new UserManagement();
});

// Fonctions globales pour la compatibilité
window.manageRoles = function(userId, userName) {
    if (window.userManagement) {
        window.userManagement.manageRoles(userId, userName);
    }
};

window.removeRole = function(userId, roleName, element) {
    if (window.userManagement) {
        window.userManagement.removeRole(userId, roleName, element);
    }
};

// Gestion des erreurs globales
window.addEventListener('error', function(e) {
    console.error('Erreur JavaScript:', e.error);
    if (window.userManagement) {
        window.userManagement.showToast('Une erreur inattendue s\'est produite', 'error');
    }
});

// Gestion des erreurs de fetch non attrapées
window.addEventListener('unhandledrejection', function(e) {
    console.error('Promesse rejetée:', e.reason);
    if (window.userManagement) {
        window.userManagement.showToast('Erreur de communication avec le serveur', 'error');
    }
});
