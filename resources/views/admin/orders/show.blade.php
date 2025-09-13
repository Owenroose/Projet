@extends('admin.layouts.app')

@section('title', 'Détails de la Commande #' . $order->id)

@push('styles')
<style>
    :root {
        --primary-color: #2563eb;
        --secondary-color: #64748b;
        --success-color: #059669;
        --warning-color: #d97706;
        --danger-color: #dc2626;
        --info-color: #0891b2;
        --dark-color: #1e293b;
        --light-color: #f8fafc;
        --border-color: #e2e8f0;
        --gradient-1: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --gradient-2: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        --gradient-3: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        --gradient-4: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }

    .dashboard-header {
        background: white;
        border-radius: 20px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: var(--shadow-lg);
        position: relative;
        overflow: hidden;
    }

    .dashboard-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: var(--gradient-1);
    }

    .header-title h1 {
        font-size: 2.5rem;
        font-weight: 800;
        background: var(--gradient-1);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 0.5rem;
    }

    .header-subtitle {
        color: var(--secondary-color);
        font-size: 1.1rem;
        font-weight: 500;
    }

    .btn-modern {
        padding: 0.75rem 1.5rem;
        border-radius: 12px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        position: relative;
        overflow: hidden;
    }

    .btn-modern::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.5s;
    }

    .btn-modern:hover::before {
        left: 100%;
    }

    .btn-primary-modern {
        background: var(--gradient-1);
        color: white;
    }

    .btn-secondary-modern {
        background: rgba(100, 116, 139, 0.1);
        color: var(--secondary-color);
        border: 2px solid var(--border-color);
    }

    .btn-modern:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-lg);
        color: white;
    }

    .info-card {
        background: white;
        border-radius: 20px;
        padding: 2rem;
        box-shadow: var(--shadow);
        border: 1px solid var(--border-color);
        transition: all 0.3s ease;
        height: 100%;
    }

    .info-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
    }

    .card-header-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        color: white;
        margin-right: 1.5rem;
    }

    .card-header-icon.primary { background: var(--gradient-1); }
    .card-header-icon.success { background: var(--gradient-4); }
    .card-header-icon.warning { background: var(--gradient-2); }
    .card-header-icon.info { background: var(--gradient-3); }
    .card-header-icon.dark { background: var(--dark-color); }

    .badge-pending { background: #fef3c7; color: #d97706; }
    .badge-processing { background: #dbeafe; color: #2563eb; }
    .badge-shipped { background: #f3e8ff; color: #7c3aed; }
    .badge-delivered { background: #d1fae5; color: #059669; }
    .badge-cancelled { background: #fee2e2; color: #dc2626; }

    .info-group {
        display: flex;
        flex-direction: column;
        margin-bottom: 1.5rem;
    }

    .info-label {
        color: var(--secondary-color);
        font-size: 0.9rem;
        font-weight: 500;
        margin-bottom: 0.25rem;
    }

    .info-value {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--dark-color);
    }

    .status-select {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.9rem;
        font-weight: 600;
        border: none;
        width: 100%;
    }

    .status-select.badge-pending { background: #fef3c7; color: #d97706; }
    .status-select.badge-processing { background: #dbeafe; color: #2563eb; }
    .status-select.badge-shipped { background: #f3e8ff; color: #7c3aed; }
    .status-select.badge-delivered { background: #d1fae5; color: #059669; }
    .status-select.badge-cancelled { background: #fee2e2; color: #dc2626; }

    .modal-header {
        border-bottom: none;
        padding: 1.5rem 2rem 0;
    }

    .modal-body {
        padding: 1.5rem 2rem;
    }

    .modal-footer {
        border-top: none;
        padding: 0 2rem 1.5rem;
    }

</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="dashboard-header mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div class="d-flex align-items-center mb-3 mb-lg-0">
                <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary-modern me-3">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div class="header-title">
                    <h1 class="mb-1">
                        Commande #{{ $order->id }}
                        @if($order->is_priority)
                            <i class="fas fa-star text-warning ms-2" title="Commande prioritaire"></i>
                        @endif
                    </h1>
                    <p class="header-subtitle">Passée le {{ $order->created_at->format('d/m/Y à H:i') }}</p>
                </div>
            </div>

            <div class="d-flex gap-3 flex-wrap">
                <button onclick="togglePriority({{ $order->id }})" class="btn-modern btn-warning-modern">
                    <i class="fas fa-star me-2"></i>
                    {{ $order->is_priority ? 'Supprimer priorité' : 'Marquer prioritaire' }}
                </button>
                <button onclick="printOrder()" class="btn-modern btn-primary-modern">
                    <i class="fas fa-print me-2"></i>Imprimer
                </button>
                <button onclick="deleteOrder({{ $order->id }})" class="btn-modern btn-danger-modern">
                    <i class="fas fa-trash me-2"></i>Supprimer
                </button>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="d-flex flex-column gap-4">
                <div class="info-card">
                    <div class="d-flex align-items-center mb-4">
                        <div class="card-header-icon primary">
                            <i class="fas fa-user"></i>
                        </div>
                        <h2 class="card-title mb-0">Informations client</h2>
                    </div>

                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="info-group">
                                <label class="info-label">Nom complet</label>
                                <p class="info-value">{{ $order->name }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-group">
                                <label class="info-label">Téléphone</label>
                                <p class="info-value">
                                    <a href="tel:{{ $order->phone }}" class="text-primary-modern text-decoration-none">
                                        {{ $order->phone }}
                                    </a>
                                </p>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="info-group">
                                <label class="info-label">Email</label>
                                <p class="info-value">
                                    <a href="mailto:{{ $order->email }}" class="text-primary-modern text-decoration-none">
                                        {{ $order->email }}
                                    </a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="info-card">
                    <div class="d-flex align-items-center mb-4">
                        <div class="card-header-icon success">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <h2 class="card-title mb-0">Adresse de livraison</h2>
                    </div>

                    <div class="bg-light rounded-3 p-4 border border-1 border-light">
                        <p class="mb-0 text-dark">{{ $order->address }}</p>
                    </div>

                    <div class="mt-4 d-flex gap-3">
                        <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($order->address) }}"
                           target="_blank"
                           class="btn-modern btn-primary-modern btn-sm">
                            <i class="fas fa-map me-2"></i>Voir sur Google Maps
                        </a>
                        <button onclick="copyAddress()" class="btn-modern btn-secondary-modern btn-sm">
                            <i class="fas fa-copy me-2"></i>Copier l'adresse
                        </button>
                    </div>
                </div>

                <div class="info-card">
                    <div class="d-flex align-items-center mb-4">
                        <div class="card-header-icon info">
                            <i class="fas fa-shopping-bag"></i>
                        </div>
                        <h2 class="card-title mb-0">Produit commandé</h2>
                    </div>

                    <div class="d-flex align-items-start gap-4">
                        @if($order->product->image)
                            <img src="{{ asset('storage/' . $order->product->image) }}"
                                 alt="{{ $order->product->name }}"
                                 class="rounded-3 shadow-sm"
                                 style="width: 96px; height: 96px; object-fit: cover;">
                        @endif
                        <div class="flex-grow-1">
                            <h3 class="h5 fw-bold mb-1">{{ $order->product->name }}</h3>
                            @if($order->product->brand)
                                <p class="text-primary-modern fw-bold mb-2">{{ $order->product->brand }}</p>
                            @endif
                            <div class="row g-2 text-sm">
                                <div class="col-6">
                                    <span class="info-label">Prix unitaire:</span>
                                    <span class="fw-bold">{{ number_format($order->product->price) }} FCFA</span>
                                </div>
                                <div class="col-6">
                                    <span class="info-label">Quantité:</span>
                                    <span class="fw-bold">{{ $order->quantity }}</span>
                                </div>
                                <div class="col-12">
                                    <span class="info-label">Total:</span>
                                    <span class="h5 fw-bold text-success">{{ number_format($order->total_price) }} FCFA</span>
                                </div>
                            </div>
                            <div class="mt-3">
                                <a href="{{ route('products.show', $order->product->slug) }}"
                                   target="_blank"
                                   class="btn-modern btn-primary-modern btn-sm">
                                    <i class="fas fa-external-link-alt me-1"></i>Voir le produit
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="d-flex flex-column gap-4">
                <div class="info-card">
                    <h3 class="h5 fw-bold mb-4">Statut de la commande</h3>

                    <div class="mb-3">
                        <label class="info-label mb-2">Statut actuel</label>
                        <select onchange="updateOrderStatus({{ $order->id }}, this.value)"
                                class="form-select status-select
                                       @if($order->status == 'pending') badge-pending
                                       @elseif($order->status == 'processing') badge-processing
                                       @elseif($order->status == 'shipped') badge-shipped
                                       @elseif($order->status == 'delivered') badge-delivered
                                       @elseif($order->status == 'cancelled') badge-cancelled
                                       @endif">
                            <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>En attente</option>
                            <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>En cours</option>
                            <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Expédiée</option>
                            <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Livrée</option>
                            <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Annulée</option>
                        </select>
                    </div>

                    <div class="pt-4 border-top border-1 border-light">
                        <p class="text-secondary fw-bold mb-2">Historique:</p>
                        <ul class="list-unstyled mb-0">
                            <li><span class="fw-bold">Commandé:</span> {{ $order->created_at->format('d/m/Y H:i') }}</li>
                            @if($order->status_updated_at)
                                <li><span class="fw-bold">Dernière maj:</span> {{ \Carbon\Carbon::parse($order->status_updated_at)->format('d/m/Y H:i') }}</li>
                            @endif
                        </ul>
                    </div>
                </div>

                <div class="info-card">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <div class="d-flex align-items-center">
                            <div class="card-header-icon warning me-3">
                                <i class="fas fa-sticky-note"></i>
                            </div>
                            <h2 class="h5 fw-bold mb-0">Notes internes</h2>
                        </div>
                        <button class="btn-modern btn-warning-modern btn-sm" data-bs-toggle="modal" data-bs-target="#addNoteModal">
                            <i class="fas fa-plus me-2"></i>Ajouter
                        </button>
                    </div>

                    <div id="notesList" class="d-flex flex-column gap-3">
                        @foreach(json_decode($order->notes, true) ?? [] as $note)
                            <div class="bg-light rounded-3 p-3 border-start border-4 border-warning">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <p class="fw-bold mb-0">{{ $note['author'] }}</p>
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($note['created_at'])->format('d/m/Y H:i') }}</small>
                                </div>
                                <p class="mb-0 text-dark">{{ $note['content'] }}</p>
                            </div>
                        @endforeach
                        @if(count(json_decode($order->notes, true) ?? []) == 0)
                            <p class="text-secondary text-center py-4">Aucune note pour cette commande</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addNoteModal" tabindex="-1" aria-labelledby="addNoteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content info-card">
            <div class="modal-header">
                <h5 class="modal-title h5 fw-bold" id="addNoteModalLabel">Ajouter une note interne</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addNoteForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="noteContent" class="form-label info-label">Contenu de la note</label>
                        <textarea class="form-control" id="noteContent" name="content" rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-modern btn-secondary-modern" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn-modern btn-primary-modern">
                        <i class="fas fa-plus me-2"></i>Ajouter
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Initialisation des tooltips Bootstrap si nécessaire
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })

    // Fonction pour imprimer la page
    function printOrder() {
        window.print();
    }

    // Fonction pour copier l'adresse
    function copyAddress() {
        const address = "{{ $order->address }}";
        navigator.clipboard.writeText(address).then(() => {
            showToast('Adresse copiée dans le presse-papiers !', 'success');
        }).catch(err => {
            showToast('Échec de la copie de l\'adresse', 'error');
            console.error('Error copying text: ', err);
        });
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
                setTimeout(() => { location.reload(); }, 1500);
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

    // Ajouter une note
    document.getElementById('addNoteForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const content = document.getElementById('noteContent').value;
        const orderId = {{ $order->id }};

        try {
            const response = await fetch(`/admin/orders/${orderId}/notes`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ content: content })
            });

            const data = await response.json();

            if (data.success) {
                showToast('Note ajoutée avec succès', 'success');
                setTimeout(() => { location.reload(); }, 1500);
            } else {
                showToast('Erreur lors de l\'ajout de la note', 'error');
            }
        } catch (error) {
            console.error('Erreur:', error);
            showToast('Erreur de connexion', 'error');
        }
    });

    // Système de notification
    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-white bg-${type} border-0`;
        toast.setAttribute('role', 'alert');
        toast.style.position = 'fixed';
        toast.style.top = '20px';
        toast.style.right = '20px';
        toast.style.zIndex = '9999';
        toast.style.minWidth = '300px';

        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fas fa-${type === 'success' ? 'check' : type === 'error' ? 'times' : 'info'}-circle me-2"></i>
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" onclick="this.parentElement.parentElement.remove()"></button>
            </div>
        `;

        document.body.appendChild(toast);

        setTimeout(() => {
            if (toast.parentNode) {
                toast.remove();
            }
        }, 5000);
    }
</script>
@endpush
