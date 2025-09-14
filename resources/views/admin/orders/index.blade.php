@extends('admin.layouts.app')

@section('title', 'Gestion des Commandes')

@section('page-css')
<style>
.order-status {
    display: inline-flex;
    align-items: center;
    padding: 0.375rem 0.75rem;
    border-radius: 0.375rem;
    font-size: 0.75rem;
    font-weight: 500;
}

.status-pending { background-color: #fef3c7; color: #d97706; }
.status-processing { background-color: #dbeafe; color: #2563eb; }
.status-shipped { background-color: #dcfce7; color: #16a34a; }
.status-delivered { background-color: #f0fdf4; color: #15803d; }
.status-cancelled { background-color: #fecaca; color: #dc2626; }

.payment-status {
    display: inline-flex;
    align-items: center;
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    font-size: 0.75rem;
    font-weight: 500;
}

.payment-pending { background-color: #fef3c7; color: #d97706; }
.payment-approved { background-color: #dcfce7; color: #16a34a; }
.payment-declined { background-color: #fecaca; color: #dc2626; }
.payment-cancelled { background-color: #f3f4f6; color: #6b7280; }

.table-actions {
    display: flex;
    gap: 0.5rem;
    align-items: center;
}

.order-summary {
    font-size: 0.875rem;
    color: #6b7280;
}

.bulk-actions {
    background-color: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 0.5rem;
    padding: 1rem;
    margin-bottom: 1rem;
    display: none;
}

.bulk-actions.show {
    display: block;
}
</style>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <!-- En-tête avec statistiques -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div class="card-info">
                                <p class="card-text">Total Commandes</p>
                                <div class="d-flex align-items-center">
                                    <h4 class="card-title mb-0 me-2">{{ $stats['totalOrders'] }}</h4>
                                </div>
                            </div>
                            <div class="card-icon">
                                <span class="badge bg-label-primary rounded p-2">
                                    <i class="bx bx-shopping-bag bx-sm"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div class="card-info">
                                <p class="card-text">Chiffre d'affaires</p>
                                <div class="d-flex align-items-center">
                                    <h4 class="card-title mb-0 me-2">{{ number_format($stats['totalSales'], 0, ',', ' ') }} FCFA</h4>
                                </div>
                            </div>
                            <div class="card-icon">
                                <span class="badge bg-label-success rounded p-2">
                                    <i class="bx bx-wallet bx-sm"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div class="card-info">
                                <p class="card-text">En attente</p>
                                <div class="d-flex align-items-center">
                                    <h4 class="card-title mb-0 me-2">{{ $stats['pendingOrders'] }}</h4>
                                </div>
                            </div>
                            <div class="card-icon">
                                <span class="badge bg-label-warning rounded p-2">
                                    <i class="bx bx-time-five bx-sm"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div class="card-info">
                                <p class="card-text">Prioritaires</p>
                                <div class="d-flex align-items-center">
                                    <h4 class="card-title mb-0 me-2">{{ $stats['priorityOrders'] }}</h4>
                                </div>
                            </div>
                            <div class="card-icon">
                                <span class="badge bg-label-info rounded p-2">
                                    <i class="bx bx-alarm bx-sm"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtres et recherche -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Filtres et recherche</h5>
                <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#filtersCollapse">
                    <i class="bx bx-filter"></i> Filtres
                </button>
            </div>
            <div class="collapse" id="filtersCollapse">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.orders.index') }}" id="filtersForm">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Rechercher</label>
                                <input type="text" class="form-control" name="search" value="{{ request('search') }}"
                                       placeholder="Nom, email, téléphone...">
                            </div>

                            <div class="col-md-2 mb-3">
                                <label class="form-label">Statut commande</label>
                                <select name="status" class="form-select">
                                    <option value="">Tous</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>En attente</option>
                                    <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>En cours</option>
                                    <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>Expédiées</option>
                                    <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Livrées</option>
                                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Annulées</option>
                                </select>
                            </div>

                            <div class="col-md-2 mb-3">
                                <label class="form-label">Statut paiement</label>
                                <select name="payment_status" class="form-select">
                                    <option value="">Tous</option>
                                    <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>En attente</option>
                                    <option value="approved" {{ request('payment_status') == 'approved' ? 'selected' : '' }}>Approuvé</option>
                                    <option value="declined" {{ request('payment_status') == 'declined' ? 'selected' : '' }}>Refusé</option>
                                </select>
                            </div>

                            <div class="col-md-2 mb-3">
                                <label class="form-label">Date début</label>
                                <input type="date" class="form-control" name="date_from" value="{{ request('date_from') }}">
                            </div>

                            <div class="col-md-2 mb-3">
                                <label class="form-label">Date fin</label>
                                <input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}">
                            </div>

                            <div class="col-md-1 mb-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="bx bx-search"></i>
                                </button>
                                <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary">
                                    <i class="bx bx-reset"></i>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Actions en lot -->
        <div class="bulk-actions" id="bulkActions">
            <div class="d-flex justify-content-between align-items-center">
                <span id="selectedCount">0 commande(s) sélectionnée(s)</span>
                <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-success" onclick="bulkUpdateStatus('processing')">
                        <i class="bx bx-check"></i> Marquer en cours
                    </button>
                    <button type="button" class="btn btn-sm btn-info" onclick="bulkUpdateStatus('shipped')">
                        <i class="bx bx-package"></i> Marquer expédiées
                    </button>
                    <button type="button" class="btn btn-sm btn-primary" onclick="bulkUpdateStatus('delivered')">
                        <i class="bx bx-check-circle"></i> Marquer livrées
                    </button>
                    <button type="button" class="btn btn-sm btn-danger" onclick="bulkUpdateStatus('cancelled')">
                        <i class="bx bx-x"></i> Annuler
                    </button>
                </div>
            </div>
        </div>

        <!-- Liste des commandes -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    Commandes
                    @if(request('status'))
                        - {{ ucfirst(request('status')) }}
                    @endif
                    ({{ $orders->total() }})
                </h5>

                <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="toggleSelectAll()">
                        <i class="bx bx-check-square"></i> Tout sélectionner
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#exportModal">
                        <i class="bx bx-export"></i> Exporter
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th width="50">
                                <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                            </th>
                            <th>Commande</th>
                            <th>Client</th>
                            <th>Produits</th>
                            <th>Total</th>
                            <th>Statut</th>
                            <th>Paiement</th>
                            <th>Date</th>
                            <th width="120">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $orderGroup)
                            <tr>
                                <td>
                                    <input type="checkbox" class="order-checkbox" value="{{ $orderGroup->order_group }}"
                                           onchange="updateBulkActions()">
                                </td>

                                <td>
                                    <div class="fw-semibold text-primary">#{{ substr($orderGroup->order_group, 0, 8) }}</div>
                                    <div class="order-summary">
                                        {{ $orderGroup->total_items }} article(s)
                                    </div>
                                </td>

                                <td>
                                    <div class="fw-semibold">{{ $orderGroup->customer_name }}</div>
                                    @if($orderGroup->customer_email)
                                        <div class="text-muted small">{{ $orderGroup->customer_email }}</div>
                                    @endif
                                    <div class="text-muted small">{{ $orderGroup->customer_phone }}</div>
                                    <div class="text-muted small">{{ $orderGroup->customer_city }}</div>
                                </td>

                                <td>
                                    <div class="order-summary">
                                        @foreach($orderGroup->items->take(2) as $item)
                                            <div>{{ $item->product->name ?? 'Produit supprimé' }} ({{ $item->quantity }})</div>
                                        @endforeach
                                        @if($orderGroup->items->count() > 2)
                                            <div class="text-muted small">+{{ $orderGroup->items->count() - 2 }} autre(s)</div>
                                        @endif
                                    </div>
                                </td>

                                <td>
                                    <div class="fw-semibold">{{ number_format($orderGroup->total_amount, 0, ',', ' ') }} FCFA</div>
                                    <div class="order-summary">
                                        Produits: {{ number_format($orderGroup->products_total, 0, ',', ' ') }} FCFA
                                        @if($orderGroup->shipping_fee > 0)
                                            <br>Livraison: {{ number_format($orderGroup->shipping_fee, 0, ',', ' ') }} FCFA
                                        @endif
                                    </div>
                                </td>

                                <td>
                                    <span class="order-status status-{{ $orderGroup->status }}">
                                        @switch($orderGroup->status)
                                            @case('pending') En attente @break
                                            @case('processing') En cours @break
                                            @case('shipped') Expédiée @break
                                            @case('delivered') Livrée @break
                                            @case('cancelled') Annulée @break
                                            @default {{ ucfirst($orderGroup->status) }}
                                        @endswitch
                                    </span>
                                </td>

                                <td>
                                    <span class="payment-status payment-{{ $orderGroup->payment_status }}">
                                        @switch($orderGroup->payment_status)
                                            @case('pending') En attente @break
                                            @case('approved') Approuvé @break
                                            @case('declined') Refusé @break
                                            @case('cancelled') Annulé @break
                                            @default {{ ucfirst($orderGroup->payment_status) }}
                                        @endswitch
                                    </span>
                                </td>

                                <td>
                                    <div>{{ \Carbon\Carbon::parse($orderGroup->created_at)->format('d/m/Y') }}</div>
                                    <div class="text-muted small">{{ \Carbon\Carbon::parse($orderGroup->created_at)->format('H:i') }}</div>
                                </td>

                                <td>
                                    <div class="table-actions">
                                        <a href="{{ route('admin.orders.show', $orderGroup->order_group) }}"
                                           class="btn btn-sm btn-outline-primary" title="Voir détails">
                                            <i class="bx bx-show"></i>
                                        </a>

                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button"
                                                    data-bs-toggle="dropdown">
                                                <i class="bx bx-dots-vertical-rounded"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><h6 class="dropdown-header">Changer le statut</h6></li>

                                                @if($orderGroup->status !== 'processing')
                                                <li><a class="dropdown-item" href="#"
                                                       onclick="updateOrderStatus('{{ $orderGroup->order_group }}', 'processing')">
                                                    <i class="bx bx-play text-info"></i> En cours
                                                </a></li>
                                                @endif

                                                @if($orderGroup->status !== 'shipped')
                                                <li><a class="dropdown-item" href="#"
                                                       onclick="updateOrderStatus('{{ $orderGroup->order_group }}', 'shipped')">
                                                    <i class="bx bx-package text-warning"></i> Expédiée
                                                </a></li>
                                                @endif

                                                @if($orderGroup->status !== 'delivered')
                                                <li><a class="dropdown-item" href="#"
                                                       onclick="updateOrderStatus('{{ $orderGroup->order_group }}', 'delivered')">
                                                    <i class="bx bx-check-circle text-success"></i> Livrée
                                                </a></li>
                                                @endif

                                                @if(!in_array($orderGroup->status, ['delivered', 'cancelled']))
                                                <li><hr class="dropdown-divider"></li>
                                                <li><a class="dropdown-item text-danger" href="#"
                                                       onclick="updateOrderStatus('{{ $orderGroup->order_group }}', 'cancelled')">
                                                    <i class="bx bx-x"></i> Annuler
                                                </a></li>
                                                @endif

                                                <li><hr class="dropdown-divider"></li>
                                                <li><a class="dropdown-item text-danger" href="#"
                                                       onclick="deleteOrder('{{ $orderGroup->order_group }}')">
                                                    <i class="bx bx-trash"></i> Supprimer
                                                </a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    <div class="empty-state">
                                        <i class="bx bx-shopping-bag display-4 text-muted mb-3"></i>
                                        <h5 class="text-muted">Aucune commande trouvée</h5>
                                        <p class="text-muted">Aucune commande ne correspond à vos critères de recherche.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($orders->hasPages())
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted small">
                            Affichage de {{ $orders->firstItem() }} à {{ $orders->lastItem() }} sur {{ $orders->total() }} résultats
                        </div>
                        {{ $orders->appends(request()->query())->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal d'export -->
<div class="modal fade" id="exportModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Exporter les commandes</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.orders.export') }}">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Date de début</label>
                            <input type="date" class="form-control" name="date_from" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Date de fin</label>
                            <input type="date" class="form-control" name="date_to" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bx bx-export"></i> Exporter
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('page-js')
<script>
// Gestion des sélections multiples
function toggleSelectAll() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.order-checkbox');

    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });

    updateBulkActions();
}

function updateBulkActions() {
    const checkboxes = document.querySelectorAll('.order-checkbox:checked');
    const bulkActions = document.getElementById('bulkActions');
    const selectedCount = document.getElementById('selectedCount');

    if (checkboxes.length > 0) {
        bulkActions.classList.add('show');
        selectedCount.textContent = checkboxes.length + ' commande(s) sélectionnée(s)';
    } else {
        bulkActions.classList.remove('show');
        document.getElementById('selectAll').checked = false;
    }
}

// Mise à jour en lot
function bulkUpdateStatus(status) {
    const checkboxes = document.querySelectorAll('.order-checkbox:checked');
    const orderGroups = Array.from(checkboxes).map(cb => cb.value);

    if (orderGroups.length === 0) {
        showAlert('Veuillez sélectionner au moins une commande', 'warning');
        return;
    }

    if (!confirm(`Êtes-vous sûr de vouloir changer le statut de ${orderGroups.length} commande(s) ?`)) {
        return;
    }

    fetch('{{ route("admin.orders.updateStatus") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            order_groups: orderGroups,
            status: status
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.message) {
            showAlert(data.message, 'success');
            setTimeout(() => location.reload(), 1500);
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        showAlert('Erreur lors de la mise à jour', 'error');
    });
}

// Mise à jour individuelle
function updateOrderStatus(orderGroup, status) {
    if (!confirm('Êtes-vous sûr de vouloir changer le statut de cette commande ?')) {
        return;
    }

    fetch('{{ route("admin.orders.updateStatus") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            order_groups: [orderGroup],
            status: status
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.message) {
            showAlert(data.message, 'success');
            setTimeout(() => location.reload(), 1500);
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        showAlert('Erreur lors de la mise à jour', 'error');
    });
}

// Suppression
function deleteOrder(orderGroup) {
    if (!confirm('Êtes-vous sûr de vouloir supprimer cette commande ? Cette action est irréversible.')) {
        return;
    }

    fetch(`/admin/orders/${orderGroup}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.message) {
            showAlert(data.message, 'success');
            setTimeout(() => location.reload(), 1500);
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        showAlert('Erreur lors de la suppression', 'error');
    });
}

// Fonction d'alerte
function showAlert(message, type = 'info') {
    const alertContainer = document.querySelector('.container-xxl.flex-grow-1.container-p-y');
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        <i class="bx bx-${type === 'success' ? 'check' : type === 'error' ? 'error' : 'info'}-circle me-2"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    alertContainer.insertBefore(alertDiv, alertContainer.firstChild);

    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}

// Initialisation
document.addEventListener('DOMContentLoaded', function() {
    // Auto-soumission des filtres avec délai
    const searchInput = document.querySelector('input[name="search"]');
    if (searchInput) {
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                document.getElementById('filtersForm').submit();
            }, 500);
        });
    }
});
</script>
@endsection
