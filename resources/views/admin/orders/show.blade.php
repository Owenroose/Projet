@extends('admin.layouts.app')

@section('title', 'Détails de la commande #' . substr($orderInfo->order_group, 0, 8))

@section('page-css')
<style>
.order-details-card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
}

.status-badge {
    padding: 0.5rem 1rem;
    border-radius: 0.375rem;
    font-weight: 500;
    font-size: 0.875rem;
}

.status-pending { background-color: #fef3c7; color: #d97706; }
.status-processing { background-color: #dbeafe; color: #2563eb; }
.status-shipped { background-color: #dcfce7; color: #16a34a; }
.status-delivered { background-color: #f0fdf4; color: #15803d; }
.status-cancelled { background-color: #fecaca; color: #dc2626; }

.payment-pending { background-color: #fef3c7; color: #d97706; }
.payment-approved { background-color: #dcfce7; color: #16a34a; }
.payment-declined { background-color: #fecaca; color: #dc2626; }
.payment-cancelled { background-color: #f3f4f6; color: #6b7280; }

.timeline {
    position: relative;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 1rem;
    top: 0;
    bottom: 0;
    width: 2px;
    background-color: #e5e7eb;
}

.timeline-item {
    position: relative;
    margin-bottom: 1.5rem;
    padding-left: 3rem;
}

.timeline-item::before {
    content: '';
    position: absolute;
    left: 0.75rem;
    top: 0.25rem;
    width: 0.5rem;
    height: 0.5rem;
    border-radius: 50%;
    background-color: #9ca3af;
}

.timeline-item.completed::before {
    background-color: #10b981;
}

.product-image {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 0.375rem;
}

.order-summary-table th {
    border-top: none;
    font-weight: 600;
    color: #374151;
}

.invoice-section {
    background-color: #f9fafb;
    border-radius: 0.5rem;
    padding: 1.5rem;
}

.customer-info {
    background-color: #f8fafc;
    border-radius: 0.5rem;
    padding: 1.5rem;
}

@media print {
    .no-print {
        display: none !important;
    }
}
</style>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <!-- En-tête avec actions -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Tableau de bord</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.orders.index') }}">Commandes</a></li>
                        <li class="breadcrumb-item active">#{{ substr($orderInfo->order_group, 0, 8) }}</li>
                    </ol>
                </nav>
                <h4 class="mb-0">Commande #{{ substr($orderInfo->order_group, 0, 8) }}</h4>
                <p class="text-muted mb-0">Créée le {{ \Carbon\Carbon::parse($orderInfo->created_at)->format('d/m/Y à H:i') }}</p>
            </div>

            <div class="btn-group no-print">
                <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary">
                    <i class="bx bx-arrow-back"></i> Retour
                </a>
                <button type="button" class="btn btn-outline-primary" onclick="window.print()">
                    <i class="bx bx-printer"></i> Imprimer
                </button>
                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="bx bx-cog"></i> Actions
                    </button>
                    <ul class="dropdown-menu">
                        <li><h6 class="dropdown-header">Changer le statut</h6></li>

                        @if($orderInfo->status !== 'processing')
                        <li><a class="dropdown-item" href="#" onclick="updateOrderStatus('processing')">
                            <i class="bx bx-play text-info"></i> Marquer en cours
                        </a></li>
                        @endif

                        @if($orderInfo->status !== 'shipped')
                        <li><a class="dropdown-item" href="#" onclick="updateOrderStatus('shipped')">
                            <i class="bx bx-package text-warning"></i> Marquer expédiée
                        </a></li>
                        @endif

                        @if($orderInfo->status !== 'delivered')
                        <li><a class="dropdown-item" href="#" onclick="updateOrderStatus('delivered')">
                            <i class="bx bx-check-circle text-success"></i> Marquer livrée
                        </a></li>
                        @endif

                        @if(!in_array($orderInfo->status, ['delivered', 'cancelled']))
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="#" onclick="updateOrderStatus('cancelled')">
                            <i class="bx bx-x"></i> Annuler la commande
                        </a></li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Informations principales -->
            <div class="col-lg-8 mb-4">
                <!-- Statuts de la commande -->
                <div class="card order-details-card mb-4">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h6 class="card-title mb-2">Statut de la commande</h6>
                                <span class="status-badge status-{{ $orderInfo->status }}">
                                    @switch($orderInfo->status)
                                        @case('pending') En attente @break
                                        @case('processing') En cours de traitement @break
                                        @case('shipped') Expédiée @break
                                        @case('delivered') Livrée @break
                                        @case('cancelled') Annulée @break
                                        @default {{ ucfirst($orderInfo->status) }}
                                    @endswitch
                                </span>
                            </div>
                            <div class="col-md-6">
                                <h6 class="card-title mb-2">Statut du paiement</h6>
                                <span class="status-badge payment-{{ $orderInfo->payment_status }}">
                                    @switch($orderInfo->payment_status)
                                        @case('pending') En attente @break
                                        @case('approved') Approuvé @break
                                        @case('declined') Refusé @break
                                        @case('cancelled') Annulé @break
                                        @default {{ ucfirst($orderInfo->payment_status) }}
                                    @endswitch
                                </span>
                                @if($orderInfo->paid_at)
                                    <div class="text-muted small mt-1">
                                        Payé le {{ \Carbon\Carbon::parse($orderInfo->paid_at)->format('d/m/Y à H:i') }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Détails des produits -->
                <div class="card order-details-card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Produits commandés</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Produit</th>
                                        <th width="80">Quantité</th>
                                        <th width="120">Prix unitaire</th>
                                        <th width="120">Sous-total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($orders as $order)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($order->product && $order->product->image)
                                                        <img src="{{ asset('storage/' . $order->product->image) }}"
                                                             alt="{{ $order->product->name }}" class="product-image me-3">
                                                    @else
                                                        <div class="bg-light product-image me-3 d-flex align-items-center justify-content-center">
                                                            <i class="bx bx-image text-muted"></i>
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <div class="fw-semibold">{{ $order->product->name ?? 'Produit supprimé' }}</div>
                                                        @if($order->product && $order->product->description)
                                                            <div class="text-muted small">{{ Str::limit($order->product->description, 50) }}</div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center">{{ $order->quantity }}</td>
                                            <td class="text-end">{{ number_format($order->unit_price, 0, ',', ' ') }} FCFA</td>
                                            <td class="text-end fw-semibold">{{ number_format($order->subtotal, 0, ',', ' ') }} FCFA</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Récapitulatif financier -->
                <div class="card order-details-card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Récapitulatif</h5>
                    </div>
                    <div class="card-body">
                        <div class="invoice-section">
                            <table class="table table-borderless order-summary-table mb-0">
                                <tr>
                                    <th width="70%">Sous-total produits :</th>
                                    <td class="text-end">{{ number_format($productsTotal, 0, ',', ' ') }} FCFA</td>
                                </tr>
                                <tr>
                                    <th>Frais de livraison :</th>
                                    <td class="text-end">
                                        @if($shippingFee > 0)
                                            {{ number_format($shippingFee, 0, ',', ' ') }} FCFA
                                        @else
                                            <span class="text-success">Gratuit</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr class="border-top">
                                    <th class="fs-5">Total :</th>
                                    <td class="text-end fs-5 fw-bold text-primary">{{ number_format($totalAmount, 0, ',', ' ') }} FCFA</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar avec infos client et suivi -->
            <div class="col-lg-4">
                <!-- Informations client -->
                <div class="card order-details-card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Informations client</h5>
                    </div>
                    <div class="card-body">
                        <div class="customer-info">
                            <div class="mb-3">
                                <h6 class="text-muted small mb-1">Nom complet</h6>
                                <div class="fw-semibold">{{ $orderInfo->customer_name }}</div>
                            </div>

                            @if($orderInfo->customer_email)
                            <div class="mb-3">
                                <h6 class="text-muted small mb-1">Email</h6>
                                <div><a href="mailto:{{ $orderInfo->customer_email }}">{{ $orderInfo->customer_email }}</a></div>
                            </div>
                            @endif

                            <div class="mb-3">
                                <h6 class="text-muted small mb-1">Téléphone</h6>
                                <div><a href="tel:{{ $orderInfo->customer_phone }}">{{ $orderInfo->customer_phone }}</a></div>
                            </div>

                            <div class="mb-3">
                                <h6 class="text-muted small mb-1">Ville</h6>
                                <div>{{ $orderInfo->customer_city }}</div>
                            </div>

                            <div>
                                <h6 class="text-muted small mb-1">Adresse de livraison</h6>
                                <div>{{ $orderInfo->customer_address }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informations de paiement -->
                <div class="card order-details-card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Paiement</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <h6 class="text-muted small mb-1">ID Transaction FedaPay</h6>
                            <div class="fw-semibold">{{ $orderInfo->fedapay_transaction_id }}</div>
                        </div>

                        <div class="mb-3">
                            <h6 class="text-muted small mb-1">Statut</h6>
                            <span class="status-badge payment-{{ $orderInfo->payment_status }}">
                                @switch($orderInfo->payment_status)
                                    @case('pending') En attente @break
                                    @case('approved') Approuvé @break
                                    @case('declined') Refusé @break
                                    @case('cancelled') Annulé @break
                                    @default {{ ucfirst($orderInfo->payment_status) }}
                                @endswitch
                            </span>
                        </div>

                        @if($orderInfo->paid_at)
                        <div>
                            <h6 class="text-muted small mb-1">Date de paiement</h6>
                            <div>{{ \Carbon\Carbon::parse($orderInfo->paid_at)->format('d/m/Y à H:i') }}</div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Chronologie de la commande -->
                <div class="card order-details-card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Suivi de la commande</h5>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            <div class="timeline-item completed">
                                <div class="fw-semibold">Commande créée</div>
                                <div class="text-muted small">{{ \Carbon\Carbon::parse($orderInfo->created_at)->format('d/m/Y à H:i') }}</div>
                            </div>

                            @if($orderInfo->paid_at)
                            <div class="timeline-item completed">
                                <div class="fw-semibold">Paiement confirmé</div>
                                <div class="text-muted small">{{ \Carbon\Carbon::parse($orderInfo->paid_at)->format('d/m/Y à H:i') }}</div>
                            </div>
                            @endif

                            @if(in_array($orderInfo->status, ['processing', 'shipped', 'delivered']))
                            <div class="timeline-item completed">
                                <div class="fw-semibold">En cours de traitement</div>
                                <div class="text-muted small">{{ \Carbon\Carbon::parse($orderInfo->updated_at)->format('d/m/Y à H:i') }}</div>
                            </div>
                            @endif

                            @if($orderInfo->shipped_at && in_array($orderInfo->status, ['shipped', 'delivered']))
                            <div class="timeline-item completed">
                                <div class="fw-semibold">Commande expédiée</div>
                                <div class="text-muted small">{{ \Carbon\Carbon::parse($orderInfo->shipped_at)->format('d/m/Y à H:i') }}</div>
                            </div>
                            @endif

                            @if($orderInfo->delivered_at && $orderInfo->status === 'delivered')
                            <div class="timeline-item completed">
                                <div class="fw-semibold">Commande livrée</div>
                                <div class="text-muted small">{{ \Carbon\Carbon::parse($orderInfo->delivered_at)->format('d/m/Y à H:i') }}</div>
                            </div>
                            @endif

                            @if($orderInfo->status === 'cancelled')
                            <div class="timeline-item">
                                <div class="fw-semibold text-danger">Commande annulée</div>
                                <div class="text-muted small">{{ \Carbon\Carbon::parse($orderInfo->updated_at)->format('d/m/Y à H:i') }}</div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page-js')
<script>
function updateOrderStatus(status) {
    const statusLabels = {
        'processing': 'en cours de traitement',
        'shipped': 'expédiée',
        'delivered': 'livrée',
        'cancelled': 'annulée'
    };

    if (!confirm(`Êtes-vous sûr de vouloir marquer cette commande comme ${statusLabels[status]} ?`)) {
        return;
    }

    fetch('{{ route("admin.orders.updateStatus") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            order_groups: ['{{ $orderInfo->order_group }}'],
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
</script>
@endsection
