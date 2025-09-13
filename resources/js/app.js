import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();
// Nova Tech Bénin - Scripts personnalisés

// Attend que le DOM soit chargé
document.addEventListener('DOMContentLoaded', function() {
    // Initialisation des composants
    initMobileMenu();
    initCharts();
    initForms();
    initNotifications();
    initModals();
    initSortableTables();
});

// Gestion du menu mobile
function initMobileMenu() {
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const sidebar = document.querySelector('.sidebar');
    const mobileOverlay = document.querySelector('.mobile-overlay');

    if (mobileMenuButton && sidebar) {
        mobileMenuButton.addEventListener('click', function() {
            sidebar.classList.toggle('active');
            if (mobileOverlay) {
                mobileOverlay.classList.toggle('active');
            }
        });
    }

    // Fermer le menu en cliquant à l'extérieur
    if (mobileOverlay) {
        mobileOverlay.addEventListener('click', function() {
            sidebar.classList.remove('active');
            mobileOverlay.classList.remove('active');
        });
    }
}

// Initialisation des graphiques
function initCharts() {
    const activityChart = document.getElementById('activityChart');

    if (activityChart) {
        new Chart(activityChart, {
            type: 'line',
            data: {
                labels: ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'],
                datasets: [{
                    label: 'Activité cette semaine',
                    data: [12, 19, 3, 5, 2, 3, 15],
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37, 99, 235, 0.1)',
                    tension: 0.4,
                    fill: true,
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            drawBorder: false
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }

    // Graphique des statistiques (exemple)
    const statsChart = document.getElementById('statsChart');
    if (statsChart) {
        // Implémentation d'un graphique supplémentaire si nécessaire
    }
}

// Gestion des formulaires
function initForms() {
    // Validation des formulaires
    const forms = document.querySelectorAll('form[data-validate]');

    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!validateForm(this)) {
                e.preventDefault();
                showNotification('Veuillez corriger les erreurs dans le formulaire.', 'error');
            }
        });
    });

    // Inputs avec masques
    const phoneInputs = document.querySelectorAll('input[type="tel"]');
    phoneInputs.forEach(input => {
        input.addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9+]/g, '');
        });
    });
}

// Validation de formulaire
function validateForm(form) {
    let isValid = true;
    const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');

    inputs.forEach(input => {
        if (!input.value.trim()) {
            markAsInvalid(input);
            isValid = false;
        } else {
            markAsValid(input);
        }
    });

    return isValid;
}

function markAsInvalid(element) {
    element.classList.add('border-red-500');
    element.classList.remove('border-green-500');
}

function markAsValid(element) {
    element.classList.remove('border-red-500');
    element.classList.add('border-green-500');
}

// Système de notifications
function initNotifications() {
    // Auto-hide des notifications flash
    const flashNotifications = document.querySelectorAll('.alert-auto-hide');

    flashNotifications.forEach(notification => {
        setTimeout(() => {
            notification.style.opacity = '0';
            setTimeout(() => notification.remove(), 300);
        }, 5000);
    });
}

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 transform transition-all duration-300 ${
        type === 'success' ? 'bg-green-500 text-white' :
        type === 'error' ? 'bg-red-500 text-white' :
        type === 'warning' ? 'bg-yellow-500 text-gray-800' :
        'bg-blue-500 text-white'
    }`;
    notification.innerHTML = `
        <div class="flex items-center">
            <i class="fas ${
                type === 'success' ? 'fa-check-circle' :
                type === 'error' ? 'fa-exclamation-circle' :
                type === 'warning' ? 'fa-exclamation-triangle' :
                'fa-info-circle'
            } mr-2"></i>
            <span>${message}</span>
        </div>
    `;

    document.body.appendChild(notification);

    setTimeout(() => {
        notification.style.opacity = '0';
        setTimeout(() => notification.remove(), 300);
    }, 5000);
}

// Gestion des modales
function initModals() {
    const modalTriggers = document.querySelectorAll('[data-modal-toggle]');
    const modalClosers = document.querySelectorAll('[data-modal-hide]');

    modalTriggers.forEach(trigger => {
        trigger.addEventListener('click', function() {
            const modalId = this.getAttribute('data-modal-toggle');
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }
        });
    });

    modalClosers.forEach(closer => {
        closer.addEventListener('click', function() {
            const modal = this.closest('.modal');
            if (modal) {
                modal.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }
        });
    });

    // Fermer la modale en cliquant à l'extérieur
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('modal')) {
            e.target.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
    });
}

// Tables triables
function initSortableTables() {
    const sortableHeaders = document.querySelectorAll('th[data-sortable]');

    sortableHeaders.forEach(header => {
        header.addEventListener('click', function() {
            const table = this.closest('table');
            const columnIndex = Array.from(this.parentElement.children).indexOf(this);
            const isAscending = this.getAttribute('data-sort-direction') === 'asc';

            // Reset other headers
            sortableHeaders.forEach(h => {
                if (h !== this) {
                    h.removeAttribute('data-sort-direction');
                    h.querySelector('.sort-icon')?.remove();
                }
            });

            // Toggle direction
            const newDirection = isAscending ? 'desc' : 'asc';
            this.setAttribute('data-sort-direction', newDirection);

            // Add sort icon
            const existingIcon = this.querySelector('.sort-icon');
            if (existingIcon) {
                existingIcon.remove();
            }

            const icon = document.createElement('i');
            icon.className = `sort-icon fas fa-arrow-${newDirection === 'asc' ? 'up' : 'down'} ml-2`;
            this.appendChild(icon);

            // Sort table
            sortTable(table, columnIndex, newDirection);
        });
    });
}

function sortTable(table, columnIndex, direction) {
    const tbody = table.querySelector('tbody');
    const rows = Array.from(tbody.querySelectorAll('tr'));

    rows.sort((a, b) => {
        const aValue = a.cells[columnIndex].textContent.trim();
        const bValue = b.cells[columnIndex].textContent.trim();

        if (direction === 'asc') {
            return aValue.localeCompare(bValue);
        } else {
            return bValue.localeCompare(aValue);
        }
    });

    // Remove existing rows
    while (tbody.firstChild) {
        tbody.removeChild(tbody.firstChild);
    }

    // Add sorted rows
    rows.forEach(row => tbody.appendChild(row));
}

// Fonctions utilitaires
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

function throttle(func, limit) {
    let inThrottle;
    return function() {
        const args = arguments;
        const context = this;
        if (!inThrottle) {
            func.apply(context, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    };
}

// Export pour utilisation globale
window.NovaTech = {
    showNotification,
    debounce,
    throttle
};
