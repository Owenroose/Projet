@extends('admin.layouts.app')

@section('title', 'Gestion des Commandes')
@section('page-title', 'Commandes')

@section('content')
<div class="container-fluid px-4">
    <!-- En-tête avec breadcrumb -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Commandes</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0 text-gray-800 mt-2">Gestion des Commandes</h1>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-success" onclick="showReportModal()">
                <i class="fas fa-download me-2"></i>Exporter
            </button>
            <a href="{{ route('admin.orders.dashboard') }}" class="btn btn-primary">
                <i class="fas fa-chart-line me-2"></i>Analytics
            </a>
        </div>
    </div>

    <!-- Statistiques Dashboard -->
    <div class="row g-4 mb-4">
        <!-- Total Commandes -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="text-xs fw-bold text-primary text-uppercase mb-1">Total Commandes</div>
                            <div class="h5 mb-0 fw-bold text-gray-800">{{ number_format($stats['total']) }}</div>
                            <div class="text-xs text-muted">Ce mois: {{ number_format($stats['this_month']) }}</div>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-gradient rounded-circle p-3">
                                <i class="fas fa-shopping-cart text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Commandes en Attente -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="text-xs fw-bold text-warning text-uppercase mb-1">En Attente</div>
                            <div class="h5 mb-0 fw-bold text-gray-800">{{ number_format($stats['pending']) }}</div>
                            <div class="text-xs text-muted">À traiter</div>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="bg-warning bg-gradient rounded-circle p-3">
                                <i class="fas fa-clock text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Commandes Livrées -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="text-xs fw-bold text-success text-uppercase mb-1">Livrées</div>
                            <div class="h5 mb-0 fw-bold text-gray-800">{{ number_format($stats['delivered']) }}</div>
                            <div class="text-xs text-muted">Terminées</div>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="bg-success bg-gradient rounded-circle p-3">
                                <i class="fas fa-check-circle text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenus Total -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="text-xs fw-bold text-info text-uppercase mb-1">Revenus</div>
                            <div class="h6 mb-0 fw-bold text-gray-800">{{ number_format($stats['total_revenue']) }} FCFA</div>
                            <div class="text-xs text-muted">Total généré</div>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="bg-info bg-gradient rounded-circle p-3">
                                <i class="fas fa-coins text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres et Recherche -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-bottom">
            <h6 class="card-title mb-0">
                <i class="fas fa-filter me-2"></i>Filtres et Recherche
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.orders.index') }}">
                <div class="row g-3">
                    <!-- Recherche -->
                    <div class="col-md-4">
                        <label class="form-label text-muted small">Rechercher</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                            <input type="text"
                                   class="form-control border-start-0"
                                   name="search"
                                   value="{{ request('search') }}"
                                   placeholder="Nom, email, téléphone ou produit...">
                        </div>
                    </div>

                    <!-- Statut -->
                    <div class="col-md-2">
                        <label class="form-label text-muted small">Statut</label>
                        <select name="status" class="form-select">
                            <option value="">Tous les statuts</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>En attente</option>
                            <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>En cours</option>
                            <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>Expédiée</option>
                            <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Livrée</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Annulée</option>
                        </select>
                    </div>

                    <!-- Date de début -->
                    <div class="col-md-2">
                        <label class="form-label text-muted small">Date début</label>
                        <input type="date"
                               class="form-control"
                               name="date_from"
                               value="{{ request('date_from') }}">
                    </div>

                    <!-- Date de fin -->
                    <div class="col-md-2">
                        <label class="form-label text-muted small">Date fin</label>
                        <input type="date"
                               class="form-control"
                               name="date_to"
                               value="{{ request('date_to') }}">
                    </div>

                    <!-- Actions -->
                    <div class="col-md-2 d-flex align-items-end">
                        <div class="btn-group w-100" role="group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i>
                            </button>
                            <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-undo"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Informations de pagination -->
        <div class="card-footer bg-light d-flex justify-content-between align-items-center">
            <small class="text-muted">
                {{ $orders->firstItem() ?? 0 }} - {{ $orders->lastItem() ?? 0 }} sur {{ $orders->total() }} commandes
            </small>
            <div class="d-flex gap-2">
                <span class="badge bg-primary">{{ $orders->total() }} Total</span>
                <span class="badge bg-warning">{{ $stats['pending'] }} En attente</span>
                <span class="badge bg-success">{{ $stats['delivered'] }} Livrées</span>
            </div>
        </div>
    </div>

    <!-- Liste des Commandes -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
            <h6 class="card-title mb-0">
                <i class="fas fa-list me-2"></i>Liste des Commandes
            </h6>
            <div class="d-flex gap-2">
                <button class="btn btn-sm btn-outline-primary" onclick="selectAll()">
                    <i class="fas fa-check-square me-1"></i>Tout sélectionner
                </button>
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-cog me-1"></i>Actions groupées
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#" onclick="bulkAction('delivered')">
                            <i class="fas fa-check text-success me-2"></i>Marquer comme livrées
                        </a></li>
                        <li><a class="dropdown-item" href="#" onclick="bulkAction('processing')">
                            <i class="fas fa-cogs text-primary me-2"></i>Marquer en cours
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="#" onclick="bulkAction('delete')">
                            <i class="fas fa-trash me-2"></i>Supprimer sélectionnées
                        </a></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="border-0">
                            <input type="checkbox" class="form-check-input" id="selectAllCheckbox">
                        </th>
                        <th class="border-0">Commande</th>
                        <th class="border-0">Client</th>
                        <th class="border-0">Produit</th>
                        <th class="border-0">Montant</th>
                        <th class="border-0">Statut</th>
                        <th class="border-0">Date</th>
                        <th class="border-0 text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                    <tr class="{{ $order->is_priority ? 'table-warning' : '' }}">
                        <td>
                            <input type="checkbox" class="form-check-input order-checkbox" value="{{ $order->id }}">
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                @if($order->is_priority)
                                    <i class="fas fa-star text-warning me-2" title="Commande prioritaire"></i>
                                @endif
                                <div>
                                    <div class="fw-semibold">#{{ $order->id }}</div>
                                    <small class="text-muted">Qté: {{ $order->quantity }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div>
                                <div class="fw-semibold text-dark">{{ $order->name }}</div>
                                <small class="text-muted d-block">{{ $order->email }}</small>
                                <small class="text-muted">{{ $order->phone }}</small>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                @if($order->product->image)
                                    <img src="{{ asset('storage/' . $order->product->image) }}"
                                         alt="{{ $order->product->name }}"
                                         class="rounded me-3"
                                         style="width: 40px; height: 40px; object-fit: cover;">
                                @endif
                                <div>
                                    <div class="fw-medium">{{ Str::limit($order->product->name, 30) }}</div>
                                    @if($order->product->brand)
                                        <small class="text-primary">{{ $order->product->brand }}</small>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="fw-bold text-success">{{ number_format($order->total_price) }} FCFA</span>
                        </td>
                        <td>
                            <select onchange="updateOrderStatus({{ $order->id }}, this.value)"
                                    class="form-select form-select-sm border-0 fw-medium
                                           @if($order->status == 'pending') bg-warning bg-opacity-10 text-warning
                                           @elseif($order->status == 'processing') bg-primary bg-opacity-10 text-primary
                                           @elseif($order->status == 'shipped') bg-info bg-opacity-10 text-info
                                           @elseif($order->status == 'delivered') bg-success bg-opacity-10 text-success
                                           @elseif($order->status == 'cancelled') bg-danger bg-opacity-10 text-danger
                                           @endif">
                                <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>En attente</option>
                                <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>En cours</option>
                                <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Expédiée</option>
                                <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Livrée</option>
                                <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Annulée</option>
                            </select>
                        </td>
                        <td>
                            <div>{{ $order->created_at->format('d/m/Y') }}</div>
                            <small class="text-muted">{{ $order->created_at->format('H:i') }}</small>
                        </td>
                        <td class="text-end">
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.orders.show', $order) }}"
                                   class="btn btn-sm btn-outline-primary"
                                   title="Voir détails">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <button onclick="togglePriority({{ $order->id }})"
                                        class="btn btn-sm btn-outline-warning"
                                        title="Marquer comme prioritaire">
                                    <i class="fas fa-star {{ $order->is_priority ? 'text-warning' : '' }}"></i>
                                </button>
                                <button onclick="deleteOrder({{ $order->id }})"
                                        class="btn btn-sm btn-outline-danger"
                                        title="Supprimer">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <div class="d-flex flex-column align-items-center">
                                <div class="bg-light rounded-circle p-4 mb-3">
                                    <i class="fas fa-shopping-cart text-muted" style="font-size: 2rem;"></i>
                                </div>
                                <h6 class="text-muted">Aucune commande trouvée</h6>
                                <p class="text-muted small mb-0">Les commandes apparaîtront ici une fois effectuées</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($orders->hasPages())
        <div class="card-footer bg-white">
            <div class="d-flex justify-content-center">
                {{ $orders->links() }}
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Modal de Rapport -->
<div class="modal fade" id="reportModal" tabindex="-1" aria-labelledby="reportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="reportModalLabel">
                    <i class="fas fa-chart-bar me-2"></i>Générer un rapport
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.orders.generate-report') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Date de début</label>
                            <input type="date" name="date_from" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Date de fin</label>
                            <input type="date" name="date_to" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Format d'export</label>
                            <div class="row g-2">
                                <div class="col-6">
                                    <input type="radio" class="btn-check" name="format" id="excel" value="excel" checked>
                                    <label class="btn btn-outline-success w-100" for="excel">
                                        <i class="fas fa-file-excel me-2"></i>Excel
                                    </label>
                                </div>
                                <div class="col-6">
                                    <input type="radio" class="btn-check" name="format" id="pdf" value="pdf">
                                    <label class="btn btn-outline-danger w-100" for="pdf">
                                        <i class="fas fa-file-pdf me-2"></i>PDF
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-download me-2"></i>Télécharger
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* Styles personnalisés pour un design moderne */
.card {
    transition: all 0.3s ease;
}

.card:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

.table th {
    font-weight: 600;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.btn-group .btn {
    border-radius: 0.375rem !important;
    margin-right: 0.25rem;
}

.btn-group .btn:last-child {
    margin-right: 0;
}

.form-select:focus,
.form-control:focus {
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
    border-color: #86b7fe;
}

.badge {
    font-weight: 500;
}

.text-gray-800 {
    color: #5a5c69 !important;
}

/* Animation pour les cards de statistiques */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.card {
    animation: fadeInUp 0.5s ease-out;
}

/* Responsive improvements */
@media (max-width: 768px) {
    .btn-group {
        flex-direction: column;
        width: 100%;
    }

    .btn-group .btn {
        margin-right: 0;
        margin-bottom: 0.25rem;
        border-radius: 0.375rem !important;
    }
}
</style>
@endpush

@push('scripts')
<script>
// Configuration Bootstrap
document.addEventListener('DOMContentLoaded', function() {
    // Initialiser les tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

// Gestion du modal de rapport
function showReportModal() {
    const modal = new bootstrap.Modal(document.getElementById('reportModal'));
    modal.show();
}

// Mise à jour du statut d'une commande
async function updateOrderStatus(orderId, newStatus) {
    try {
        const response = await fetch(`/admin/orders/${orderId}/status`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ status: newStatus })
        });

        const data = await response.json();

        if (data.success) {
            showToast('Statut mis à jour avec succès', 'success');
            setTimeout(() => location.reload(), 1500);
        } else {
            showToast('Erreur lors de la mise à jour', 'error');
        }
    } catch (error) {
        console.error('Erreur:', error);
        showToast('Erreur de connexion', 'error');
    }
}

// Marquer/Démarquer comme prioritaire
async function togglePriority(orderId) {
    try {
        const response = await fetch(`/admin/orders/${orderId}/priority`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        const data = await response.json();

        if (data.success) {
            showToast(data.message, 'success');
            location.reload();
        } else {
            showToast('Erreur lors de la mise à jour', 'error');
        }
    } catch (error) {
        console.error('Erreur:', error);
        showToast('Erreur de connexion', 'error');
    }
}

// Supprimer une commande
function deleteOrder(orderId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette commande ? Cette action est irréversible.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/orders/${orderId}`;

        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';

        const csrfField = document.createElement('input');
        csrfField.type = 'hidden';
        csrfField.name = '_token';
        csrfField.value = document.querySelector('meta[name="csrf-token"]').content;

        form.appendChild(methodField);
        form.appendChild(csrfField);
        document.body.appendChild(form);
        form.submit();
    }
}

// Sélection multiple
function selectAll() {
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    const orderCheckboxes = document.querySelectorAll('.order-checkbox');

    orderCheckboxes.forEach(checkbox => {
        checkbox.checked = selectAllCheckbox.checked;
    });
}

// Actions groupées
function bulkAction(action) {
    const selectedOrders = [];
    document.querySelectorAll('.order-checkbox:checked').forEach(checkbox => {
        selectedOrders.push(checkbox.value);
    });

    if (selectedOrders.length === 0) {
        showToast('Veuillez sélectionner au moins une commande', 'warning');
        return;
    }

    if (confirm(`Êtes-vous sûr de vouloir appliquer cette action à ${selectedOrders.length} commande(s) ?`)) {
        // Implémenter l'action groupée
        console.log('Action:', action, 'Orders:', selectedOrders);
        showToast('Action en cours de traitement...', 'info');
    }
}

// Système de toast moderne avec Bootstrap
function showToast(message, type = 'info') {
    const toastContainer = document.getElementById('toast-container') || createToastContainer();

    const toastId = 'toast-' + Date.now();
    const bgClass = type === 'success' ? 'bg-success' :
                   type === 'error' ? 'bg-danger' :
                   type === 'warning' ? 'bg-warning' : 'bg-primary';

    const toastHTML = `
        <div id="${toastId}" class="toast align-items-center text-white ${bgClass} border-0" role="alert">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fas ${type === 'success' ? 'fa-check' :
                                  type === 'error' ? 'fa-times' :
                                  type === 'warning' ? 'fa-exclamation-triangle' : 'fa-info'} me-2"></i>
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    `;

    toastContainer.insertAdjacentHTML('beforeend', toastHTML);

    const toastElement = document.getElementById(toastId);
    const toast = new bootstrap.Toast(toastElement, { delay: 5000 });
    toast.show();

    toastElement.addEventListener('hidden.bs.toast', function() {
        this.remove();
    });
}

function createToastContainer() {
    const container = document.createElement('div');
    container.id = 'toast-container';
    container.className = 'toast-container position-fixed top-0 end-0 p-3';
    container.style.zIndex = '1055';
    document.body.appendChild(container);
    return container;
}

// Gestion du checkbox "Sélectionner tout"
document.addEventListener('change', function(e) {
    if (e.target.id === 'selectAllCheckbox') {
        selectAll();
    } else if (e.target.classList.contains('order-checkbox')) {
        updateSelectAllState();
    }
});

function updateSelectAllState() {
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    const orderCheckboxes = document.querySelectorAll('.order-checkbox');
    const checkedBoxes = document.querySelectorAll('.order-checkbox:checked');

    if (checkedBoxes.length === 0) {
        selectAllCheckbox.indeterminate = false;
        selectAllCheckbox.checked = false;
    } else if (checkedBoxes.length === orderCheckboxes.length) {
        selectAllCheckbox.indeterminate = false;
        selectAllCheckbox.checked = true;
    } else {
        selectAllCheckbox.indeterminate = true;
    }
}
</script>
@endpush
