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

.modal-icon {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
}

.modal-icon.success { background-color: #dcfce7; color: #16a34a; }
.modal-icon.warning { background-color: #fef3c7; color: #d97706; }
.modal-icon.danger { background-color: #fecaca; color: #dc2626; }
.modal-icon.info { background-color: #dbeafe; color: #2563eb; }

.order-details {
    background-color: #f8fafc;
    border-radius: 0.5rem;
    padding: 1rem;
    margin: 1rem 0;
}

.loading-spinner {
    display: none;
}

.loading .loading-spinner {
    display: inline-block;
}

.loading .btn-text {
    display: none;
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
                    <button type="button" class="btn btn-sm btn-success" onclick="openBulkStatusModal('processing')">
                        <i class="bx bx-check"></i> Marquer en cours
                    </button>
                    <button type="button" class="btn btn-sm btn-info" onclick="openBulkStatusModal('shipped')">
                        <i class="bx bx-package"></i> Marquer expédiées
                    </button>
                    <button type="button" class="btn btn-sm btn-primary" onclick="openBulkStatusModal('delivered')">
                        <i class="bx bx-check-circle"></i> Marquer livrées
                    </button>
                    <button type="button" class="btn btn-sm btn-danger" onclick="openBulkStatusModal('cancelled')">
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
                                                       onclick="openStatusModal('{{ $orderGroup->order_group }}', 'processing')">
                                                    <i class="bx bx-play text-info"></i> En cours
                                                </a></li>
                                                @endif

                                                @if($orderGroup->status !== 'shipped')
                                                <li><a class="dropdown-item" href="#"
                                                       onclick="openStatusModal('{{ $orderGroup->order_group }}', 'shipped')">
                                                    <i class="bx bx-package text-warning"></i> Expédiée
                                                </a></li>
                                                @endif

                                                @if($orderGroup->status !== 'delivered')
                                                <li><a class="dropdown-item" href="#"
                                                       onclick="openStatusModal('{{ $orderGroup->order_group }}', 'delivered')">
                                                    <i class="bx bx-check-circle text-success"></i> Livrée
                                                </a></li>
                                                @endif

                                                @if(!in_array($orderGroup->status, ['delivered', 'cancelled']))
                                                <li><hr class="dropdown-divider"></li>
                                                <li><a class="dropdown-item text-danger" href="#"
                                                       onclick="openDeleteModal('{{ $orderGroup->order_group }}')">
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

<!-- Modals et JavaScript -->
<!-- Modal de changement de statut individuel -->
<div class="modal fade" id="statusModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Changer le statut de la commande</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <div class="modal-icon" id="statusIcon">
                    <i class="bx bx-info-circle bx-lg"></i>
                </div>
                <h5 id="statusTitle">Confirmer l'action</h5>
                <p id="statusMessage" class="text-muted mb-3"></p>

                <div class="order-details" id="orderDetails">
                    <div class="row">
                        <div class="col-6">
                            <strong>Commande:</strong>
                            <div id="orderNumber"></div>
                        </div>
                        <div class="col-6">
                            <strong>Statut actuel:</strong>
                            <div id="currentStatus"></div>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Commentaire (optionnel)</label>
                    <textarea class="form-control" id="statusComment" rows="3" placeholder="Ajouter une note..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn" id="confirmStatusBtn" onclick="confirmStatusUpdate()">
                    <span class="loading-spinner spinner-border spinner-border-sm me-2" role="status"></span>
                    <span class="btn-text">Confirmer</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de changement de statut en lot -->
<div class="modal fade" id="bulkStatusModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Changer le statut des commandes sélectionnées</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <div class="modal-icon" id="bulkStatusIcon">
                    <i class="bx bx-info-circle bx-lg"></i>
                </div>
                <h5 id="bulkStatusTitle">Confirmer l'action</h5>
                <p id="bulkStatusMessage" class="text-muted mb-3"></p>

                <div class="order-details">
                    <div class="alert alert-info">
                        <i class="bx bx-info-circle me-2"></i>
                        <span id="bulkOrdersCount">0 commande(s) sélectionnée(s)</span>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Commentaire (optionnel)</label>
                    <textarea class="form-control" id="bulkStatusComment" rows="3" placeholder="Ajouter une note pour toutes les commandes..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn" id="confirmBulkStatusBtn" onclick="confirmBulkStatusUpdate()">
                    <span class="loading-spinner spinner-border spinner-border-sm me-2" role="status"></span>
                    <span class="btn-text">Confirmer</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de suppression -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Supprimer la commande</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <div class="modal-icon danger">
                    <i class="bx bx-trash bx-lg"></i>
                </div>
                <h5>Êtes-vous sûr ?</h5>
                <p class="text-muted mb-3">Cette action est irréversible. La commande sera définitivement supprimée.</p>

                <div class="order-details">
                    <div class="row">
                        <div class="col-12">
                            <strong>Commande:</strong>
                            <div id="deleteOrderNumber"></div>
                        </div>
                    </div>
                </div>

                <div class="alert alert-danger mt-3">
                    <i class="bx bx-error-circle me-2"></i>
                    <small>Attention: Cette action ne peut pas être annulée.</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn" onclick="confirmDelete()">
                    <span class="loading-spinner spinner-border spinner-border-sm me-2" role="status"></span>
                    <span class="btn-text">Supprimer définitivement</span>
                </button>
            </div>
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

                    <div class="mb-3">
                        <label class="form-label">Format d'export</label>
                        <select class="form-select" name="format">
                            <option value="excel">Excel (.xlsx)</option>
                            <option value="csv">CSV</option>
                            <option value="pdf">PDF</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Colonnes à inclure</label>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="columns[]" value="order_number" checked>
                                    <label class="form-check-label">Numéro de commande</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="columns[]" value="customer" checked>
                                    <label class="form-check-label">Informations client</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="columns[]" value="products" checked>
                                    <label class="form-check-label">Produits</label>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="columns[]" value="amounts" checked>
                                    <label class="form-check-label">Montants</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="columns[]" value="status" checked>
                                    <label class="form-check-label">Statuts</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="columns[]" value="dates" checked>
                                    <label class="form-check-label">Dates</label>
                                </div>
                            </div>
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

<!-- Modal de notification -->
<div class="modal fade" id="notificationModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-4">
                <div class="modal-icon" id="notificationIcon">
                    <i class="bx bx-check bx-lg"></i>
                </div>
                <h5 id="notificationTitle">Opération réussie</h5>
                <p id="notificationMessage" class="text-muted mb-0"></p>
            </div>
            <div class="modal-footer border-0 justify-content-center">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page-js')
<script>
// Variables globales pour les modals
let currentOrderGroup = null;
let currentStatus = null;
let selectedOrderGroups = [];

// Configuration des statuts
const statusConfig = {
    processing: {
        title: 'Marquer en cours de traitement',
        message: 'Cette commande sera marquée comme étant en cours de traitement.',
        icon: 'bx-play',
        iconClass: 'info',
        btnClass: 'btn-info'
    },
    shipped: {
        title: 'Marquer comme expédiée',
        message: 'Cette commande sera marquée comme expédiée.',
        icon: 'bx-package',
        iconClass: 'warning',
        btnClass: 'btn-warning'
    },
    delivered: {
        title: 'Marquer comme livrée',
        message: 'Cette commande sera marquée comme livrée.',
        icon: 'bx-check-circle',
        iconClass: 'success',
        btnClass: 'btn-success'
    },
    cancelled: {
        title: 'Annuler la commande',
        message: 'Cette commande sera marquée comme annulée.',
        icon: 'bx-x',
        iconClass: 'danger',
        btnClass: 'btn-danger'
    }
};

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

    selectedOrderGroups = Array.from(checkboxes).map(cb => cb.value);

    if (checkboxes.length > 0) {
        bulkActions.classList.add('show');
        selectedCount.textContent = checkboxes.length + ' commande(s) sélectionnée(s)';
    } else {
        bulkActions.classList.remove('show');
        document.getElementById('selectAll').checked = false;
    }
}

// Modal de changement de statut individuel
function openStatusModal(orderGroup, status) {
    currentOrderGroup = orderGroup;
    currentStatus = status;

    const config = statusConfig[status];
    const modal = new bootstrap.Modal(document.getElementById('statusModal'));

    // Configuration de l'icône et du style
    const icon = document.getElementById('statusIcon');
    const iconEl = icon.querySelector('i');

    icon.className = `modal-icon ${config.iconClass}`;
    iconEl.className = `bx ${config.icon} bx-lg`;

    // Configuration du contenu
    document.getElementById('statusTitle').textContent = config.title;
    document.getElementById('statusMessage').textContent = config.message;
    document.getElementById('orderNumber').textContent = '#' + orderGroup.substr(0, 8);

    // Configuration du bouton
    const btn = document.getElementById('confirmStatusBtn');
    btn.className = `btn ${config.btnClass}`;

    // Reset du formulaire
    document.getElementById('statusComment').value = '';

    modal.show();
}

// Modal de changement de statut en lot
function openBulkStatusModal(status) {
    if (selectedOrderGroups.length === 0) {
        showNotification('Veuillez sélectionner au moins une commande', 'warning');
        return;
    }

    currentStatus = status;

    const config = statusConfig[status];
    const modal = new bootstrap.Modal(document.getElementById('bulkStatusModal'));

    // Configuration de l'icône et du style
    const icon = document.getElementById('bulkStatusIcon');
    const iconEl = icon.querySelector('i');

    icon.className = `modal-icon ${config.iconClass}`;
    iconEl.className = `bx ${config.icon} bx-lg`;

    // Configuration du contenu
    document.getElementById('bulkStatusTitle').textContent = config.title;
    document.getElementById('bulkStatusMessage').textContent =
        `${selectedOrderGroups.length} commande(s) seront marquées avec ce nouveau statut.`;
    document.getElementById('bulkOrdersCount').textContent =
        `${selectedOrderGroups.length} commande(s) sélectionnée(s)`;

    // Configuration du bouton
    const btn = document.getElementById('confirmBulkStatusBtn');
    btn.className = `btn ${config.btnClass}`;

    // Reset du formulaire
    document.getElementById('bulkStatusComment').value = '';

    modal.show();
}

// Modal de suppression
function openDeleteModal(orderGroup) {
    currentOrderGroup = orderGroup;

    document.getElementById('deleteOrderNumber').textContent = '#' + orderGroup.substr(0, 8);

    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

// Confirmation de changement de statut individuel
function confirmStatusUpdate() {
    const btn = document.getElementById('confirmStatusBtn');
    const comment = document.getElementById('statusComment').value;

    setLoading(btn, true);

    const data = {
        status: currentStatus,
        comment: comment
    };

    fetch(`{{ route('admin.orders.index') }}/${currentOrderGroup}/update-status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        setLoading(btn, false);

        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('statusModal')).hide();
            showNotification(data.message, 'success');
            setTimeout(() => location.reload(), 2000);
        } else {
            showNotification(data.message || 'Erreur lors de la mise à jour', 'error');
        }
    })
    .catch(error => {
        setLoading(btn, false);
        console.error('Erreur:', error);
        showNotification('Erreur lors de la mise à jour', 'error');
    });
}

// Confirmation de changement de statut en lot
function confirmBulkStatusUpdate() {
    const btn = document.getElementById('confirmBulkStatusBtn');
    const comment = document.getElementById('bulkStatusComment').value;

    setLoading(btn, true);

    const data = {
        order_groups: selectedOrderGroups,
        status: currentStatus,
        comment: comment
    };

    fetch('{{ route("admin.orders.updateStatus") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        setLoading(btn, false);

        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('bulkStatusModal')).hide();
            showNotification(data.message, 'success');
            setTimeout(() => location.reload(), 2000);
        } else {
            showNotification(data.message || 'Erreur lors de la mise à jour', 'error');
        }
    })
    .catch(error => {
        setLoading(btn, false);
        console.error('Erreur:', error);
        showNotification('Erreur lors de la mise à jour', 'error');
    });
}

// Confirmation de suppression
function confirmDelete() {
    const btn = document.getElementById('confirmDeleteBtn');

    setLoading(btn, true);

    fetch(`{{ route('admin.orders.index') }}/${currentOrderGroup}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        setLoading(btn, false);

        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('deleteModal')).hide();
            showNotification(data.message, 'success');
            setTimeout(() => location.reload(), 2000);
        } else {
            showNotification(data.message || 'Erreur lors de la suppression', 'error');
        }
    })
    .catch(error => {
        setLoading(btn, false);
        console.error('Erreur:', error);
        showNotification('Erreur lors de la suppression', 'error');
    });
}

// Fonctions utilitaires
function setLoading(btn, loading) {
    if (loading) {
        btn.classList.add('loading');
        btn.disabled = true;
    } else {
        btn.classList.remove('loading');
        btn.disabled = false;
    }
}

function showNotification(message, type = 'info') {
    const modal = new bootstrap.Modal(document.getElementById('notificationModal'));
    const icon = document.getElementById('notificationIcon');
    const iconEl = icon.querySelector('i');
    const title = document.getElementById('notificationTitle');
    const messageEl = document.getElementById('notificationMessage');

    // Configuration selon le type
    switch(type) {
        case 'success':
            icon.className = 'modal-icon success';
            iconEl.className = 'bx bx-check bx-lg';
            title.textContent = 'Opération réussie';
            break;
        case 'error':
            icon.className = 'modal-icon danger';
            iconEl.className = 'bx bx-error bx-lg';
            title.textContent = 'Erreur';
            break;
        case 'warning':
            icon.className = 'modal-icon warning';
            iconEl.className = 'bx bx-error-circle bx-lg';
            title.textContent = 'Attention';
            break;
        default:
            icon.className = 'modal-icon info';
            iconEl.className = 'bx bx-info-circle bx-lg';
            title.textContent = 'Information';
    }

    messageEl.textContent = message;
    modal.show();
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

    // Gestion des modals
    document.getElementById('statusModal').addEventListener('hidden.bs.modal', function () {
        currentOrderGroup = null;
        currentStatus = null;
    });

    document.getElementById('bulkStatusModal').addEventListener('hidden.bs.modal', function () {
        currentStatus = null;
    });

    document.getElementById('deleteModal').addEventListener('hidden.bs.modal', function () {
        currentOrderGroup = null;
    });

    // Auto-fermeture des notifications
    document.getElementById('notificationModal').addEventListener('shown.bs.modal', function () {
        setTimeout(() => {
            const modal = bootstrap.Modal.getInstance(document.getElementById('notificationModal'));
            if (modal) {
                modal.hide();
            }
        }, 3000);
    });
});

// Gestion des raccourcis clavier
document.addEventListener('keydown', function(e) {
    // Échap pour fermer les modals
    if (e.key === 'Escape') {
        const openModals = document.querySelectorAll('.modal.show');
        openModals.forEach(modal => {
            const modalInstance = bootstrap.Modal.getInstance(modal);
            if (modalInstance) {
                modalInstance.hide();
            }
        });
    }

    // Ctrl+A pour tout sélectionner
    if (e.ctrlKey && e.key === 'a' && !e.target.matches('input, textarea')) {
        e.preventDefault();
        document.getElementById('selectAll').checked = true;
        toggleSelectAll();
    }
});

// Export avec feedback
document.querySelector('#exportModal form').addEventListener('submit', function(e) {
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;

    submitBtn.innerHTML = '<i class="bx bx-loader bx-spin"></i> Génération...';
    submitBtn.disabled = true;

    // Restaurer le bouton après 3 secondes
    setTimeout(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
        bootstrap.Modal.getInstance(document.getElementById('exportModal')).hide();
    }, 3000);
});

// Animation des cartes statistiques au survol
document.querySelectorAll('.card').forEach(card => {
    card.addEventListener('mouseenter', function() {
        this.style.transform = 'translateY(-2px)';
        this.style.boxShadow = '0 4px 8px rgba(0,0,0,0.1)';
        this.style.transition = 'all 0.2s ease';
    });

    card.addEventListener('mouseleave', function() {
        this.style.transform = 'translateY(0)';
        this.style.boxShadow = '';
    });
});
</script>
@endsection
